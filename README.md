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
- Session cookies are configured as `HttpOnly`, `SameSite=Lax` and `Secure` on HTTPS via `session-bootstrap.php`.
- If `RECAPTCHA_SITE_KEY` is missing, submit is disabled in UI with a clear status message.

## Quick checks

```bash
php -l *.php
php -l mailer/contactMailer.php
node --check js/main.js
PORT=18100 ./scripts/regression.sh
```

## UI structure

- Base styles: `style.css`.
- Redesign overrides: `style-redesign.css`.
- `head.php` and `footer-libraries.php` append filemtime-based version query params for CSS/JS cache busting.
- Current conversion layer is consolidated in `style-redesign.css` as `v10 unified conversion layer`.
- Main section templates:
  - `index.php` (hero)
  - `section-service-detail.php` (services + CTA)
  - `section-gallery.php` (gallery + CTA)
  - `section-contact.php` (contacts + CTA)
  - `section-about.php` (about + trust metrics)
  - `footer.php` (footer CTA + trust)

## CTA analytics

- CTA click tracking is centralized in `js/main.js` (`setupCtaTracking` and `trackAnalyticsEvent`).
- Use `data-cta="event_name"` on CTA buttons/links to send:
  - GA event: category `cta`, action `event_name`
  - Yandex goal: `cta_event_name`
- Contact form events keep category `contact_form` and existing Yandex goals.
