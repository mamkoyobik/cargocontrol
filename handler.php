<?php

require_once __DIR__ . '/mailer/contactMailer.php';
require_once __DIR__ . '/recaptcha-master/src/autoload.php';

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

/**
 * Unified HTML response for ajax form requests.
 */
function renderAnswer($message, $tag = 'h4', $statusCode = 200)
{
    http_response_code((int)$statusCode);
    $safeTag = in_array($tag, ['h2', 'h4', 'p'], true) ? $tag : 'p';
    echo '<div class="answer"><' . $safeTag . '>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</' . $safeTag . '></div>';
    exit;
}

/**
 * Trims text, strips tags and normalizes whitespace.
 */
function normalizeText($value)
{
    $value = trim(strip_tags((string)$value));
    $value = preg_replace('/\s+/u', ' ', $value);
    return trim((string)$value);
}

/**
 * Truncates UTF-8 text safely.
 */
function truncateText($value, $length)
{
    if (function_exists('mb_substr')) {
        return mb_substr((string)$value, 0, (int)$length);
    }
    return substr((string)$value, 0, (int)$length);
}

/**
 * Ensures requests come from this host.
 */
function isAllowedOrigin()
{
    $host = strtolower(trim((string)($_SERVER['HTTP_HOST'] ?? '')));
    $host = explode(':', $host)[0];
    if ($host === '') {
        return false;
    }

    $matchesHost = static function ($url) use ($host) {
        $urlHost = strtolower((string)parse_url((string)$url, PHP_URL_HOST));
        return $urlHost !== '' && $urlHost === $host;
    };

    $origin = trim((string)($_SERVER['HTTP_ORIGIN'] ?? ''));
    if ($origin !== '') {
        return $matchesHost($origin);
    }

    $referer = trim((string)($_SERVER['HTTP_REFERER'] ?? ''));
    if ($referer !== '') {
        return $matchesHost($referer);
    }

    return true;
}

/**
 * Simple IP-based rate limit: 5 requests per 10 minutes.
 */
function isRateLimited($ip, $limit = 5, $windowSeconds = 600)
{
    $ip = trim((string)$ip);
    if ($ip === '') {
        return false;
    }

    $directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cargocontrol-rate-limit';
    if (!is_dir($directory)) {
        @mkdir($directory, 0700, true);
    }

    $file = $directory . DIRECTORY_SEPARATOR . hash('sha256', $ip) . '.json';
    $now = time();
    $threshold = $now - (int)$windowSeconds;
    $hits = [];

    if (is_file($file)) {
        $payload = @file_get_contents($file);
        $decoded = json_decode((string)$payload, true);
        if (is_array($decoded) && isset($decoded['hits']) && is_array($decoded['hits'])) {
            foreach ($decoded['hits'] as $hit) {
                $hit = (int)$hit;
                if ($hit >= $threshold) {
                    $hits[] = $hit;
                }
            }
        }
    }

    if (count($hits) >= (int)$limit) {
        return true;
    }

    $hits[] = $now;
    @file_put_contents($file, json_encode(['hits' => $hits]), LOCK_EX);

    return false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderAnswer('Метод запроса не поддерживается.', 'h4', 405);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isAllowedOrigin()) {
    renderAnswer('Недопустимый источник запроса.', 'h4', 403);
}

$clientIp = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
if (isRateLimited($clientIp)) {
    renderAnswer('Слишком много запросов. Повторите попытку через несколько минут.', 'h4', 429);
}

$sessionToken = trim((string)($_SESSION['cc_form_token'] ?? ''));
$requestToken = trim((string)($_POST['_token'] ?? ''));
if ($sessionToken === '' || $requestToken === '' || !hash_equals($sessionToken, $requestToken)) {
    renderAnswer('Сессия формы устарела. Обновите страницу и попробуйте снова.', 'h4', 400);
}

$recaptchaSecret = trim((string)getenv('RECAPTCHA_SECRET_KEY'));
if ($recaptchaSecret === '') {
    error_log('Contact form misconfigured: RECAPTCHA_SECRET_KEY is missing.');
    renderAnswer('Сервис временно недоступен. Попробуйте позже.', 'h4', 503);
}

$name = normalizeText($_POST['name'] ?? '');
$company = normalizeText($_POST['company'] ?? '');
$phone = normalizeText($_POST['phone'] ?? '');
$email = normalizeText($_POST['email'] ?? '');
$message = normalizeText($_POST['message'] ?? '');
$website = trim((string)($_POST['website'] ?? ''));

if ($website !== '') {
    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h4', 400);
}

$name = truncateText($name, 120);
$company = truncateText($company, 160);
$phone = truncateText($phone, 60);
$email = truncateText($email, 160);
$message = truncateText($message, 3000);

$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
$phoneDigits = preg_replace('/\D+/', '', $phone);

if ($name === '' || $email === '' || $phone === '') {
    renderAnswer('Заполните обязательные поля.');
}

if ($phoneDigits === null || strlen($phoneDigits) < 10 || strlen($phoneDigits) > 15) {
    renderAnswer('Проверьте корректность номера телефона.');
}

$recaptchaToken = trim((string)($_POST['g-recaptcha-response'] ?? ''));
if ($recaptchaToken === '') {
    renderAnswer('Не пройдена проверка ReCaptcha! Попробуйте еще раз.');
}

try {
    $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());
    $response = $recaptcha->verify($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? null);

    if (!$response->isSuccess()) {
        renderAnswer('Не пройдена проверка ReCaptcha! Попробуйте еще раз.');
    }

    if (ContactMailer::send($name, $company, $email, $phone, $message)) {
        echo '<div class="answer">';
        echo '<h2>Ваше сообщение отправлено!</h2>';
        echo '<p>В ближайшее время наш менеджер свяжется с Вами.</p>';
        echo '</div>';
        exit;
    }

    error_log('Contact form: mail delivery failed.');
    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h2', 500);
} catch (\Throwable $e) {
    error_log('Contact form exception: ' . $e->getMessage());
    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h2', 500);
}
