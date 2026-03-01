<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

class ContactMailer
{
    private static $emailFrom = 'cargo@cargocontrol.ru';
    private static $emailTo = 'Cargozayavka@cargocontrol.ru';

    private static function getEnv($name, $default = '')
    {
        $value = getenv($name);
        return ($value === false || $value === '') ? $default : $value;
    }

    private static function getEnvInt($name, $default)
    {
        $value = self::getEnv($name, '');
        if ($value === '' || !is_numeric($value)) {
            return (int)$default;
        }
        return (int)$value;
    }

    private static function isValidEmail($value)
    {
        return filter_var((string)$value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function send($name, $company, $email, $phone, $message)
    {
        $name = str_replace(["\r", "\n"], ' ', trim((string)$name));
        $company = str_replace(["\r", "\n"], ' ', trim((string)$company));
        $email = trim((string)$email);
        $phone = str_replace(["\r", "\n"], ' ', trim((string)$phone));
        $message = trim((string)$message);

        $fromEmail = self::getEnv('MAIL_FROM', self::$emailFrom);
        $toEmail = self::getEnv('MAIL_TO', self::$emailTo);
        $fromName = self::getEnv('MAIL_FROM_NAME', 'CARGO CONTROL');
        $smtpHost = self::getEnv('MAIL_HOST', 'smtp.jino.ru');
        $smtpUser = self::getEnv('MAIL_USERNAME', $fromEmail);
        $smtpPass = self::getEnv('MAIL_PASSWORD', '');
        $smtpPort = self::getEnvInt('MAIL_PORT', 587);
        $smtpEncryptionEnv = strtoupper(self::getEnv('MAIL_ENCRYPTION', 'STARTTLS'));
        $smtpEncryption = ($smtpEncryptionEnv === 'SSL' || $smtpEncryptionEnv === 'SMTPS')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;

        if (
            $smtpHost === '' ||
            $smtpUser === '' ||
            $smtpPass === '' ||
            !self::isValidEmail($fromEmail) ||
            !self::isValidEmail($toEmail) ||
            !self::isValidEmail($email)
        ) {
            error_log('ContactMailer misconfigured or invalid email payload.');
            return false;
        }

        if ($smtpPort <= 0 || $smtpPort > 65535) {
            $smtpPort = 587;
        }

        $body = implode("\n", [
            'Имя: ' . $name,
            'Компания: ' . $company,
            'Телефон: ' . $phone,
            'E-mail: ' . $email,
            '',
            'Описание работ:',
            $message,
        ]);

        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $smtpHost;
            $mailer->SMTPAuth = true;
            $mailer->Username = $smtpUser;
            $mailer->Password = $smtpPass;
            $mailer->SMTPSecure = $smtpEncryption;
            $mailer->Port = $smtpPort;
            $mailer->Timeout = 15;
            $mailer->CharSet = 'UTF-8';
            $mailer->setFrom($fromEmail, $fromName);
            $mailer->addAddress($toEmail);
            $mailer->addReplyTo($email, $name !== '' ? $name : $email);
            $mailer->isHTML(false);
            $mailer->Subject = 'Заполнена форма обратной связи';
            $mailer->Body = $body;

            return $mailer->send();
        } catch (Exception $e) {
            error_log('ContactMailer send failed: ' . $e->getMessage());
            return false;
        }
    }
}
