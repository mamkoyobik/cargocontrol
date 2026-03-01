#!/usr/bin/env bash

set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PORT="${PORT:-18080}"
SECOND_PORT="$((PORT + 1))"
TMP_DIR="$(mktemp -d /tmp/cargocontrol-regression.XXXXXX)"
COOKIE_JAR="$TMP_DIR/cookies.txt"
SERVER_LOG="$TMP_DIR/php-server.log"
PHP_PID=""

cleanup() {
  if [[ -n "$PHP_PID" ]] && kill -0 "$PHP_PID" 2>/dev/null; then
    kill "$PHP_PID" 2>/dev/null || true
    wait "$PHP_PID" 2>/dev/null || true
  fi
  rm -rf "$TMP_DIR"
}
trap cleanup EXIT

need_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "ERROR: required command is missing: $1" >&2
    exit 1
  fi
}

wait_for_server() {
  local port="$1"
  local i
  for i in {1..60}; do
    if curl -fsS "http://127.0.0.1:${port}/" >/dev/null 2>&1; then
      return 0
    fi
    sleep 0.1
  done

  echo "ERROR: PHP built-in server did not start on port ${port}" >&2
  if [[ -f "$SERVER_LOG" ]]; then
    tail -n 50 "$SERVER_LOG" >&2 || true
  fi
  exit 1
}

start_server() {
  local port="$1"
  shift
  env "$@" php -S "127.0.0.1:${port}" -t "$ROOT_DIR" >"$SERVER_LOG" 2>&1 &
  PHP_PID="$!"
  wait_for_server "$port"
}

stop_server() {
  if [[ -n "$PHP_PID" ]] && kill -0 "$PHP_PID" 2>/dev/null; then
    kill "$PHP_PID" 2>/dev/null || true
    wait "$PHP_PID" 2>/dev/null || true
  fi
  PHP_PID=""
}

assert_eq() {
  local expected="$1"
  local actual="$2"
  local label="$3"
  if [[ "$actual" != "$expected" ]]; then
    echo "ERROR: ${label}: expected '${expected}', got '${actual}'" >&2
    exit 1
  fi
}

assert_contains() {
  local file="$1"
  local needle="$2"
  local label="$3"
  if ! rg -Fq "$needle" "$file"; then
    echo "ERROR: ${label}: did not find '${needle}' in ${file}" >&2
    exit 1
  fi
}

echo "[1/8] Command availability"
need_cmd php
need_cmd curl
need_cmd node
need_cmd rg

echo "[2/8] PHP syntax checks"
while IFS= read -r -d '' php_file; do
  php -l "$php_file" >/dev/null
done < <(find "$ROOT_DIR" -type f -name "*.php" -print0)

echo "[3/8] JavaScript syntax checks"
node --check "$ROOT_DIR/js/main.js" >/dev/null

echo "[4/8] Static security/config checks"
assert_contains "$ROOT_DIR/.htaccess" "RewriteRule ^\\.backups/" ".htaccess blocks backups"
assert_contains "$ROOT_DIR/.htaccess" "RewriteRule ^mailer/PHPMailer/get_oauth_token\\.php$ - [F,L,NC]" ".htaccess blocks PHPMailer helper"
assert_contains "$ROOT_DIR/.htaccess" "RewriteRule ^recaptcha-master/(examples|tests)/ - [F,L,NC]" ".htaccess blocks recaptcha examples/tests"
assert_contains "$ROOT_DIR/.htaccess" "Content-Security-Policy" ".htaccess sets CSP"
assert_contains "$ROOT_DIR/mailer/PHPMailer/VERSION" "7.0.2" "PHPMailer version"

echo "[5/8] Start local server with full env and collect CSRF token"
rm -rf "$(php -r 'echo sys_get_temp_dir().DIRECTORY_SEPARATOR."cargocontrol-rate-limit";')"
start_server "$PORT" \
  RECAPTCHA_SITE_KEY=test_site_key \
  RECAPTCHA_SECRET_KEY=test_secret_key \
  MAIL_HOST=smtp.example.com \
  MAIL_PORT=587 \
  MAIL_ENCRYPTION=STARTTLS \
  MAIL_USERNAME=mailer@example.com \
  MAIL_PASSWORD=secret \
  MAIL_FROM=mailer@example.com \
  MAIL_TO=inbox@example.com

curl -fsS -c "$COOKIE_JAR" "http://127.0.0.1:${PORT}/" >"$TMP_DIR/index.html"
token="$(rg -o 'name=\"_token\" value=\"[^\"]+\"' "$TMP_DIR/index.html" | head -n 1 | sed 's/.*value=\"//; s/\"$//')"
if [[ -z "$token" || "${#token}" -lt 32 ]]; then
  echo "ERROR: CSRF token was not found or too short" >&2
  exit 1
fi

echo "[6/8] Backend behavior checks (status codes and form errors)"
code_get="$(curl -sS -o /dev/null -w '%{http_code}' "http://127.0.0.1:${PORT}/handler.php")"
assert_eq "405" "$code_get" "GET /handler.php returns 405"

code_missing_token="$(curl -sS -o /dev/null -w '%{http_code}' \
  --data-urlencode "name=Test User" \
  --data-urlencode "phone=+7 (999) 123-45-67" \
  --data-urlencode "email=test@example.com" \
  --data-urlencode "message=Smoke test" \
  "http://127.0.0.1:${PORT}/handler.php")"
assert_eq "400" "$code_missing_token" "POST without token returns 400"

code_bad_origin="$(curl -sS -o /dev/null -w '%{http_code}' \
  -b "$COOKIE_JAR" \
  -H "Origin: http://evil.example" \
  --data-urlencode "_token=${token}" \
  --data-urlencode "name=Test User" \
  --data-urlencode "phone=+7 (999) 123-45-67" \
  --data-urlencode "email=test@example.com" \
  --data-urlencode "message=Smoke test" \
  "http://127.0.0.1:${PORT}/handler.php")"
assert_eq "403" "$code_bad_origin" "POST with foreign origin returns 403"

captcha_response_file="$TMP_DIR/captcha-missing.html"
code_no_captcha="$(curl -sS -o "$captcha_response_file" -w '%{http_code}' \
  -b "$COOKIE_JAR" \
  -H "Origin: http://127.0.0.1:${PORT}" \
  --data-urlencode "_token=${token}" \
  --data-urlencode "name=Test User" \
  --data-urlencode "phone=+7 (999) 123-45-67" \
  --data-urlencode "email=test@example.com" \
  --data-urlencode "message=Smoke test" \
  "http://127.0.0.1:${PORT}/handler.php")"
assert_eq "200" "$code_no_captcha" "POST without captcha returns handled error"
assert_contains "$captcha_response_file" "Не пройдена проверка ReCaptcha" "captcha validation message"

rate_limited="false"
for attempt in {1..6}; do
  code_attempt="$(curl -sS -o /dev/null -w '%{http_code}' \
    -b "$COOKIE_JAR" \
    -H "Origin: http://127.0.0.1:${PORT}" \
    --data-urlencode "_token=${token}" \
    --data-urlencode "name=Rate Limit" \
    --data-urlencode "phone=+7 (999) 123-45-67" \
    --data-urlencode "email=ratelimit@example.com" \
    --data-urlencode "message=Attempt ${attempt}" \
    "http://127.0.0.1:${PORT}/handler.php")"

  if [[ "$code_attempt" == "429" ]]; then
    rate_limited="true"
    break
  fi
done

if [[ "$rate_limited" != "true" ]]; then
  echo "ERROR: rate limiter did not return HTTP 429 after repeated requests" >&2
  exit 1
fi

echo "[7/8] ReCaptcha UI fallback check when site key is missing"
stop_server
start_server "$SECOND_PORT" \
  RECAPTCHA_SECRET_KEY=test_secret_key \
  MAIL_HOST=smtp.example.com \
  MAIL_PORT=587 \
  MAIL_ENCRYPTION=STARTTLS \
  MAIL_USERNAME=mailer@example.com \
  MAIL_PASSWORD=secret \
  MAIL_FROM=mailer@example.com \
  MAIL_TO=inbox@example.com

curl -fsS "http://127.0.0.1:${SECOND_PORT}/" >"$TMP_DIR/no-site-key.html"
assert_contains "$TMP_DIR/no-site-key.html" "Форма временно недоступна. Проверка ReCaptcha не настроена." "missing site key warning"
if ! rg -U -Pq '(?s)id="button".*disabled aria-disabled="true"' "$TMP_DIR/no-site-key.html"; then
  echo "ERROR: submit button should be disabled when RECAPTCHA_SITE_KEY is missing" >&2
  exit 1
fi

echo "[8/8] Completed"
echo "Regression checks passed."
