<?php

require_once __DIR__ . '/mailer/contactMailer.php';
require_once __DIR__ . '/recaptcha-master/src/autoload.php';

header('Content-Type: text/html; charset=UTF-8');

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderAnswer('Метод запроса не поддерживается.', 'h4', 405);
}

$recaptchaSecret = getenv('RECAPTCHA_SECRET_KEY');
if (!$recaptchaSecret) {
    $recaptchaSecret = '6LeOD3IUAAAAAPgrw7CJDuHBZhS3wNdH2j-wbjaq';
}

$name = trim(strip_tags((string)($_POST['name'] ?? '')));
$company = trim(strip_tags((string)($_POST['company'] ?? '')));
$phone = trim(strip_tags((string)($_POST['phone'] ?? '')));
$email = trim(strip_tags((string)($_POST['email'] ?? '')));
$message = trim(strip_tags((string)($_POST['message'] ?? '')));
$website = trim((string)($_POST['website'] ?? ''));

if ($website !== '') {
    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h4', 400);
}

$truncate = function ($value, $length) {
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length);
    }
    return substr($value, 0, $length);
};

$name = $truncate($name, 120);
$company = $truncate($company, 160);
$phone = $truncate($phone, 60);
$email = $truncate($email, 160);
$message = $truncate($message, 3000);

$email = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';

if ($name === '' || $email === '' || $phone === '') {
    renderAnswer('Заполните обязательные поля.');
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

    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h2', 500);
} catch (\Throwable $e) {
    renderAnswer('Произошла ошибка! Не удалось отправить сообщение.', 'h2', 500);
}
