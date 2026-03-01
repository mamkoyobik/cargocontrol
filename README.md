# CARGO CONTROL (PHP Landing)

## Local run

```bash
php -S 127.0.0.1:8080 -t /Users/admin/Desktop/cargocontrol.ru
```

Open: `http://127.0.0.1:8080/`

## Required environment variables

Set these variables on the server (Apache/Nginx/PHP-FPM environment):

- `RECAPTCHA_SITE_KEY` - public reCAPTCHA key used in the form widget.
- `RECAPTCHA_SECRET_KEY` - secret reCAPTCHA key used by `handler.php` validation.
- `MAIL_HOST` - SMTP host.
- `MAIL_PORT` - SMTP port (for example `587`).
- `MAIL_ENCRYPTION` - `STARTTLS` (default) or `SMTPS`.
- `MAIL_USERNAME` - SMTP username.
- `MAIL_PASSWORD` - SMTP password (required).
- `MAIL_FROM` - sender email address.
- `MAIL_FROM_NAME` - sender name (optional, default: `CARGO CONTROL`).
- `MAIL_TO` - recipient email for incoming leads.

## Contact form protections

- Honeypot field (`website`).
- CSRF token (`_token`) validated in session.
- Origin/Referer host check (same-host only).
- Rate limit: 5 requests per 10 minutes per IP.
- Server-side validation for required fields, email, phone, and reCAPTCHA.
- If `RECAPTCHA_SITE_KEY` is missing, submit is disabled in UI with a clear status message.

## Quick checks

```bash
php -l *.php
php -l mailer/contactMailer.php
```
