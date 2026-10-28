# AGENTS.md

## Project overview

This repository contains the **奶龍選美大賽** HW2 project: a responsive event registration website with a PHP/MySQL backend, PDF/QR generation, confirmation email, PWA support, Discord Bot integration, custom error pages, and Cloudflare Tunnel deployment.

Primary production URLs:

- Custom domain: `https://hw2.chuen666666.com/`
- School Apache URL: `http://140.138.155.243/~s1151538/HW2/`

Production project directory:

```text
/home/s1151538/public_html/HW2/
```

TCPDF is installed as a sibling directory:

```text
/home/s1151538/public_html/TCPDF/
```

## Repository structure

```text
HW2/
├── index.html
├── submit.php
├── success.php
├── 403.html
├── 404.html
├── .htaccess
├── .env
├── example.env
├── .gitignore
├── composer.json
├── composer.lock
├── deploy.sh
├── manifest.json
├── service-worker.js
├── css/
│   ├── bootstrap.min.css
│   ├── animate.css
│   ├── font-awesome.min.css
│   ├── owl.carousel.css
│   ├── owl.theme.css
│   └── style.css
├── js/
│   ├── jquery.js
│   ├── bootstrap.min.js
│   ├── jquery.parallax.js
│   ├── owl.carousel.min.js
│   ├── smoothscroll.js
│   ├── wow.min.js
│   ├── custom.js
│   ├── register.js
│   └── pwa.js
├── images/
├── pdf/
│   └── AGENTS.md
├── bot/
│   ├── AGENTS.md
│   ├── bot.py
│   ├── requirements.txt
│   └── .venv/
└── vendor/
```

## Core application flow

```text
index.html
   │ POST
   ▼
submit.php
   ├── validate/process submitted data
   ├── write/update MySQL
   ├── generate random PDF filename
   ├── create QR Code
   ├── generate PDF using TCPDF
   ├── save PDF into pdf/
   ├── send confirmation email using PHPMailer
   ├── attach QR Code
   └── create Session
   ▼
success.php
   ├── validate Session
   ├── query registration from MySQL
   └── show result and PDF link
```

Do not reintroduce older URL-based identification such as `success.php?student_id=...`. The current success flow uses Session state.

## Frontend

The frontend is based on **TemplateMo New Event (TM-486)** and has been customized.

Main technologies:

- Bootstrap
- jQuery
- Owl Carousel
- WOW.js
- Animate.css
- Font Awesome
- jQuery Parallax
- Smooth Scroll
- Google Fonts
- Custom CSS/JS

### Important frontend files

`index.html`

- Main page structure and registration form.
- Uses POST to `submit.php`.
- Form `name` attributes must continue matching the PHP `$_POST` keys.

`css/style.css`

- Main project-specific styling.
- Responsive rules are defined with media queries.
- Prefer modifying this file rather than third-party CSS.
- Keep selectors scoped where practical, e.g. `#register .form-control`.

`js/custom.js`

- Main UI behavior.
- Contains Owl Carousel configuration.

`js/register.js`

- Client-side form validation.
- Client-side validation is for UX only and is not a security boundary.

### Owl Carousel

Current responsive configuration is conceptually:

```javascript
$('#owl-speakers').owlCarousel({
    autoPlay: 6000,
    items: 4,
    itemsDesktop: [1199, 2],
    itemsDesktopSmall: [979, 2],
    itemsTablet: [768, 1],
    itemsMobile: [479, 1]
})
```

Do not add overlapping or contradictory breakpoints without testing narrow desktop/tablet layouts.

## HTML and CSS conventions

Prefer semantic HTML and valid CSS.

For form controls:

- `name` controls the key sent to PHP.
- `id` is for DOM/label/CSS references.
- `required`, `pattern`, `minlength`, etc. are client-side constraints only.
- Sensitive or authoritative validation must also happen server-side.

For responsive work:

- `max-width` media queries mean “this width and below”.
- `min-width` media queries mean “this width and above”.
- Prefer `max-width: 100%; height: auto;` for responsive images.
- Prefer Flexbox or Grid over manual offset hacks for new layout work.

For registration submit-button positioning:

- Avoid Bootstrap offset classes purely for centering.
- Prefer a dedicated wrapper such as `.submit-wrap` plus `text-align: center`, or a block button with automatic horizontal margins.

For CSS specificity:

- Prefer scoped selectors over `!important`.
- Do not add `!important` unless there is a clear reason.
- If two selectors have equal specificity, the later rule wins.

For dynamic HTML output:

- Keep database and application values unescaped; encode them for the output context at the final output point.
- Escape user-controlled values inserted into HTML, TCPDF `writeHTML()`, or PHPMailer HTML bodies with `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- Do not reuse HTML-escaped values for database writes, SMTP recipient addresses, or other non-HTML contexts.

## Third-party frontend code

Do not casually edit minified or vendor library files:

```text
css/bootstrap.min.css
js/jquery.js
js/bootstrap.min.js
js/owl.carousel.min.js
js/jquery.parallax.js
js/wow.min.js
```

If a third-party library produces legacy validator warnings, do not “fix” the library by hand unless explicitly requested.

Project-specific fixes belong in:

```text
css/style.css
js/custom.js
js/register.js
```

## PHP backend

Production PHP is in the PHP 8.1 series.

Primary backend files:

- `submit.php`
- `success.php`

Use PDO / PDO MySQL and prepared statements. Never construct SQL using raw concatenation of user input.

Good:

```php
$stmt = $pdo->prepare(
    'SELECT * FROM registrations WHERE student_id = ?'
);
$stmt->execute([$student_id]);
```

Avoid:

```php
$sql = "SELECT * FROM registrations WHERE student_id = '$student_id'";
```

## Database

Database engine: **MySQL**

Main registration fields:

```text
student_id
name
email
phone
grade
category
pdf_filename
```

The student ID must remain protected by a `PRIMARY KEY` or `UNIQUE` constraint because registration logic may use:

```sql
ON DUPLICATE KEY UPDATE
```

Do not rename database fields without updating all dependent code in `submit.php`, `success.php`, Discord Bot queries, schema, and documentation.

## PDF and QR Code

TCPDF is not installed inside the repository. It is loaded from `../TCPDF/`.

Typical includes:

```php
require_once __DIR__ . '/../TCPDF/tcpdf_import.php';
require_once __DIR__ . '/../TCPDF/tcpdf_barcodes_2d.php';
```

TCPDF is used for:

- registration PDF generation
- QR Code generation
- embedding the QR Code in the PDF

The QR Code contains a **URL to the generated PDF**, not the PDF data itself.

Generated PDFs are stored in `pdf/`.

PDF filenames must remain unpredictable/random rather than being derived directly from a student ID.

## Session handling

The registration success page uses PHP Session state.

Expected flow:

```text
submit.php
→ create/update registration
→ store registration identity in Session
→ redirect to success.php
```

`success.php` should not trust a student ID supplied directly through the URL.

After:

```php
header('Location: success.php');
```

use:

```php
exit;
```

unless there is an explicit reason not to.

## Email

Email is sent using **PHPMailer** via SMTP.

Current SMTP service:

```text
smtp.office365.com
Port 587
STARTTLS
```

Credentials come from `.env`. Never hard-code SMTP credentials into PHP files.

## Composer

PHP dependencies are managed with Composer.

Relevant files:

```text
composer.json
composer.lock
vendor/
```

Production install:

```bash
composer install --no-dev --no-interaction
```

Do not commit `vendor/` unless project policy changes.

## Environment variables and secrets

`.env` stores runtime secrets/configuration such as:

```env
DB_HOST=
DB_NAME=
DB_USER=
DB_PASSWORD=
DB_REGISTRATION_TABLE=

SMTP_USERNAME=
SMTP_PASSWORD=

DISCORD_TOKEN=
```

Rules:

- Never commit `.env`.
- Never paste real passwords/tokens into source code.
- Never log secrets.
- Never expose a Cloudflare Tunnel token.
- Keep `example.env` secret-free.
- If adding a new required environment variable, add its empty placeholder to `example.env`.

## Apache and error pages

`.htaccess` is used to protect `.env` and define custom errors.

Expected behavior includes:

```apache
<Files ".env">
    Require all denied
</Files>

ErrorDocument 403 /~s1151538/HW2/403.html
ErrorDocument 404 /~s1151538/HW2/404.html
```

The custom 403/404 pages must work from both:

```text
https://hw2.chuen666666.com/
```

and:

```text
http://140.138.155.243/~s1151538/HW2/
```

When changing asset URLs in these pages, verify both deployment paths.

Do not replace true HTTP 403/404 responses with pages that merely display “403” or “404” while returning HTTP 200.

## PWA

This project uses a small custom/native PWA implementation rather than a PWA framework such as Workbox.

Main files:

```text
manifest.json
service-worker.js
js/pwa.js
images/icon-192.png
images/icon-512.png
```

Responsibilities:

`manifest.json`

- app metadata
- name/short name
- icons
- start URL
- display behavior

`js/pwa.js`

- registers the Service Worker

`service-worker.js`

- intercepts supported fetch requests
- uses browser Cache API / network logic

Important constraints:

- Service Worker should only handle intended same-origin requests.
- Do not treat POST submissions like static GET resources.
- Avoid caching sensitive registration POST traffic.
- PWA/Service Worker production usage requires a secure context (HTTPS, with localhost exceptions for development).

## Cloudflare Tunnel

Custom domain traffic is routed approximately as:

```text
Browser
   │ HTTPS
   ▼
Cloudflare
   ▼
Cloudflare Tunnel
   ▼
cloudflared
   ▼
http://127.0.0.1:80
   ▼
Apache
   ▼
/~s1151538/HW2/
```

The tunnel connector binary for this account is under:

```text
/home/s1151538/bin/cloudflared
```

Keep `cloudflared` updated.

Do not commit or expose the Tunnel token.

If the custom domain returns Cloudflare error 1033, first verify whether the user’s own `cloudflared` connector is running.

If the tunnel connector is healthy but the origin cannot be reached, investigate the local Apache origin separately.

## Discord Bot

Bot files live in `bot/`.

Python dependencies:

```text
discord.py
python-dotenv
mysql-connector-python
```

The bot reads configuration from the project environment and queries the same MySQL registration data.

The PHP website and Discord Bot are separate applications sharing the database; they do not directly call each other.

Do not commit `bot/.venv/`.

## Git / generated files

Avoid committing:

```text
.env
.env.*
auth.json
vendor/
bot/.venv/
__pycache__/
pdf/*.pdf
.vscode/
.idea/
```

Generated registration PDFs should normally stay out of version control.

## Deployment and server assumptions

Production environment includes:

- Linux
- Apache
- Apache UserDir
- PHP 8.1+
- PDO MySQL
- Composer 2
- MySQL
- Python 3
- virtualenv/pip
- TCPDF
- Cloudflare Tunnel for the custom HTTPS domain

The project should continue to work through both the custom domain and the school Apache/UserDir URL unless a task explicitly changes that requirement.

## Testing checklist

Before considering a meaningful change complete, test affected areas as appropriate:

```text
Desktop layout
Mobile layout
Navbar
Hero section
Registration form
Client-side validation
Server-side validation
Database insert/update
PDF generation
QR Code
Email sending
Session success flow
PWA registration/cache behavior
403 / 404
Custom domain
School Apache/UserDir URL
Discord Bot
```

For frontend changes, also check:

- narrow desktop widths
- tablet widths
- mobile widths
- DevTools Console
- DevTools Network tab
- missing CSS/images (404)
- unexpected horizontal overflow

HTML/CSS maintained by this project should remain validator-friendly.

## Security rules

Do not weaken existing protections.

In particular:

- Do not expose `.env`.
- Do not expose database or SMTP passwords.
- Do not expose Discord or Cloudflare Tunnel tokens.
- Prevent HTML injection in the success page, generated PDF HTML, and confirmation email HTML by encoding dynamic values at each HTML output point.
- Do not reintroduce predictable PDF filenames.
- Do not reintroduce success-page IDOR via URL parameters.
- Do not rely only on HTML/JavaScript validation.
- Use prepared SQL statements.
- Treat generated PDF URLs as sensitive.
- Avoid caching sensitive form submissions.
- Preserve HTTPS for the custom PWA domain.

### Known deferred security hardening

The following items are known but are outside the current coursework requirements. Do not implement them unless the user explicitly expands the task scope:

- Complete server-side validation for every registration field, including scalar type, format, length, allowed values, and consistency between student ID and school email.
- Replace detailed PDO and PHPMailer errors shown to visitors with generic responses while recording diagnostic details only in protected server logs.
- Add abuse controls for the public registration endpoint, such as rate limiting or another suitable anti-automation mechanism. A CSRF token alone is not sufficient protection against automated submissions.

When production hardening is requested, reassess these items against the current deployment environment before implementation.

## Change discipline

When editing this project:

1. Make the smallest change that solves the requested problem.
2. Do not rewrite unrelated sections.
3. Do not replace working third-party libraries without a clear reason.
4. Preserve compatibility with the school server.
5. Preserve compatibility with PHP 8.1 unless explicitly changing the deployment target.
6. Keep secrets outside source control.
7. Test both custom-domain and Apache/UserDir paths for routing-sensitive changes.
8. Update `README.md`, `example.env`, or other documentation only when behavior/configuration actually changes.
9. Prefer readable code over clever abstractions for this coursework project.
10. Do not add a framework or build system unless the task explicitly requires it.

## Useful commands

Check PHP version:

```bash
php -v
```

Install production PHP dependencies:

```bash
composer install --no-dev --no-interaction
```

Set up Discord Bot environment:

```bash
python3 -m venv bot/.venv
bot/.venv/bin/pip install -r bot/requirements.txt
```

Check current user’s cloudflared process:

```bash
pgrep -a -u "$USER" cloudflared
```

Check cloudflared version:

```bash
~/bin/cloudflared --version
```

Test the custom domain:

```bash
curl -I https://hw2.chuen666666.com/
```

Test the school URL:

```bash
curl -I http://140.138.155.243/~s1151538/HW2/
```

## Guidance for coding agents

When working in this repository:

- Read this file first.
- Inspect the relevant source before editing.
- Prefer the current working tree as the source of truth.
- Do not assume older copies of `submit.php` or `success.php` reflect the current production logic.
- Do not invent configuration values.
- Do not output or commit secrets.
- Explain any change that affects deployment, database schema, security, routing, PWA cache behavior, or external services.
- If a change could break the school Apache deployment while working only on the custom domain, treat that as a regression unless explicitly requested.
