<?php

if (session_status() === PHP_SESSION_ACTIVE) {
    return;
}

$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
    || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443)
);

if (function_exists('ini_set')) {
    @ini_set('session.use_strict_mode', '1');
    @ini_set('session.use_only_cookies', '1');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.cookie_samesite', 'Lax');
}

if (!headers_sent()) {
    $cookieParams = session_get_cookie_params();
    $cookieLifetime = (int)($cookieParams['lifetime'] ?? 0);
    $cookiePath = (string)($cookieParams['path'] ?? '/');
    $cookieDomain = (string)($cookieParams['domain'] ?? '');

    if ($cookiePath === '') {
        $cookiePath = '/';
    }

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => $cookieLifetime,
            'path' => $cookiePath,
            'domain' => $cookieDomain,
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(
            $cookieLifetime,
            $cookiePath . '; samesite=Lax',
            $cookieDomain,
            $isHttps,
            true
        );
    }
}

session_start();
