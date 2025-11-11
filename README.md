# Goralys

Goralys is a lightweight web app to manage "Grand Oral" topics for students and teachers at a high school.

## Features

- Student/teacher/admin roles with automatic role detection at registration ([PHP/register.php](PHP/register.php)).
- Two-topic student workflow: draft, submit, and read-only once submitted ([JS/core.js](JS/core.js), [PHP/subject/update_student.php](PHP/subject/update_student.php), [PHP/subject/submit_student.php](PHP/subject/submit_student.php)).
- Session-backed user data caching for fast frontend rendering ([PHP/login.php](PHP/login.php) uses [`Goralys\Utility\GoralysUtility::cacheStudentTopicsInfo`](PHP/utility.php) indirectly).
- CSRF protection using a short-lived session token: [`PHP/create_form_token.php`](PHP/create_form_token.php) + [`Goralys\Utility\GoralysUtility::verifyCSRF`](PHP/utility.php).
- Toast notification system used by both PHP and JS ([`Goralys\Config\GoralysUtility::showToast`](PHP/config.php) and [`toast.show_toast`](JS/toast.js)).

## Quick start (development)

Prerequisites:
- PHP 8.1+ with mysqli
- Composer (for PHPMailer)
- Apache (recommended) or PHP built-in server for testing

To simulate a local PHP server with apache and mysql on windows, you can use [xampp](https://www.apachefriends.org) (also available on linux and macOS)

(Optional):
- PHP_CodeSniffer
- PHP ruleset for PSR-12 convention

Steps:
1. Run setup script:
   ```bash
   .\setup.bat
   ```
2. Configure environment:
   - For development, modify the values inside .env (created using setup.bat)
3. Database:
   - Create the database and tables using the schema at [PHP/data_structure.txt](PHP/data_structure.txt).
4. Web server:
   - Place the project under your document root (example path `/goralys/`) and enable `.htaccess` rules in the bundled [.htaccess](.htaccess).
   - Alternatively run PHP's built-in server (note: .htaccess rewrites won't apply):
     ```bash
     php -S localhost:8000
     ```
5. Access the app:
   - Visit `http://localhost/goralys/` (or `http://localhost:8000` if using built-in server).

## Security notes

- CSRF:
  - Token created by [PHP/create_form_token.php](PHP/create_form_token.php) and validated by [`Goralys\Utility\GoralysUtility::verifyCSRF`](PHP/utility.php).
- Passwords:
  - Passwords are hashed using PHP's `password_hash` ([PHP/register.php](PHP/register.php)) and verified with `password_verify` ([PHP/login.php](PHP/login.php)).
- Sensitive config:
  - Move production secrets out of [PHP/config.php](PHP/config.php) into a protected file (e.g. `PHP/config_secret.php`) and ensure it is excluded by `.gitignore`.

## Currently working on
(07/11/2025) I am currently implementing the backend and frontend for admins accounts

*Note: the `dev` branch serves as a pre-production playground, so some commits may include experimental or buggy code — I try to minimize this as much as possible.*

## Key code pointers

- Server-side toast utility: [`Goralys\Utility\GoralysUtility::showToast`](PHP/config.php) — [PHP/config.php](PHP/config.php)
- Form token creation & policy: [PHP/create_form_token.php](PHP/create_form_token.php) and validator [`Goralys\Utility\GoralysUtility::verifyCSRF`](PHP/utility.php) — [PHP/utility.php](PHP/utility.php)
- Student subject UI & flows: [`core.js`, section: `Student functions`](JS/core.js) — [JS/core.js](JS/core.js)
- Core client-side logic for subject management/handling: [core.js](JS/core.js)
- Client-side toast handling: [`toast.show_toast`](JS/toast.js) — [JS/toast.js](JS/toast.js)
- Automatic header update: [`update_header`](JS/header.js) — [JS/header.js](JS/header.js)

## Contents & quick links

- Pages
  - [index.html](index.html)
  - [account.html](account.html)
  - [login_page.php](login_page.php)
  - [register_page.php](register_page.php)
  - [subject-student_page.php](subject-student_page.php)
- PHP endpoints / routers
  - [PHP/subject_router.php](PHP/subject_router.php) — contains the routing logic for subject pages
  - [PHP/login.php](PHP/login.php)
  - [PHP/register.php](PHP/register.php)
  - [PHP/logout.php](PHP/logout.php)
  - [PHP/confirm_email.php](PHP/confirm_email.php)
  - [PHP/create_form_token.php](PHP/create_form_token.php)
- Student subject APIs
  - [PHP/subject/fetch_student_subjects.php](PHP/subject/fetch_student_subjects.php)
  - [PHP/subject/update_student.php](PHP/subject/update_student.php)
  - [PHP/subject/submit_student.php](PHP/subject/submit_student.php)
- Utilities & config
  - [PHP/config.php](PHP/config.php) — contains DB + mail config
  - [PHP/utility.php](PHP/utility.php) — helper functions including [`Goralys\Utility\GoralysUtility::formatUserId`](PHP/utility.php) and [`Goralys\Utility\GoralysUtility::verifyCSRF`](PHP/utility.php)
  - [PHP/data_structure.txt](PHP/data_structure.txt) — DB schema
  - [PHP/composer.json](PHP/composer.json) — 3rd-party deps (PHPMailer)
- Frontend JS
  - [JS/core.js](JS/core.js) — student flows; functions [`core.student_save_draft`](JS/core.js) and [`core.student_submit`](JS/core.js)
  - [JS/user.js](JS/user.js)
  - [JS/header.js](JS/header.js) — [`update_header`](JS/header.js)
  - [JS/toast.js](JS/toast.js) — [`toast.show_toast`](JS/toast.js)
  - [JS/input.js](JS/input.js)
- Styles
  - [CSS/config.css](CSS/config.css)
  - [CSS/style.css](CSS/style.css)
  - [CSS/input.css](CSS/input.css)
  - [CSS/login-register.css](CSS/login-register.css)
  - [CSS/subject.css](CSS/subject.css)
  - [CSS/account.css](CSS/account.css)
  - [CSS/toast.css](CSS/toast.css)

## License and contributing information

This project is under an MIT license (see: [`LICENSE`](LICENSE)).
All contributions are welcome as long as they respect the terms inside [`Contributing`](CONTRIBUTING.md).

## Notes

All the files ending in `_page.php` are old html files renamed to avoid duplicates when implementing CSRF validation.
Any pull request containing sensitive information inside ``PHP/config.php` will have no chance to be merged.