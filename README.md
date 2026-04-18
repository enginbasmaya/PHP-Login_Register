# PHP Login / Register System

Secure, multilingual login and registration system built with PHP 8+ and PDO.

---

## File Structure

```
├── config.php          # Database credentials (never commit this)
├── conn.php            # Database singleton (PDO)
├── helpers.php         # Session, CSRF, validation, flash messages
├── lang.php            # Language manager class
├── lang/
│   ├── en.php          # English translations
│   └── tr.php          # Turkish translations
├── login.php
├── register.php
├── welcome.php
├── logout.php
├── style.css
└── loginregister.sql   # Database schema + test user
```

---

## Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- PDO + PDO_MySQL extension

---

## Setup

**1. Import the database**
```bash
mysql -u root -p < loginregister.sql
```

**2. Configure credentials**

Open `config.php` and set your values, or use environment variables:

```php
return [
    'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
    'db_name' => $_ENV['DB_NAME'] ?? 'loginregister',
    'db_user' => $_ENV['DB_USER'] ?? 'root',
    'db_pass' => $_ENV['DB_PASS'] ?? '',
];
```

> Never commit `config.php` — add it to `.gitignore`.

**3. HTTPS**

Session cookies are set with `secure => true`. The app must be served over HTTPS. For local development, use a tool like [Laragon](https://laragon.org) or [Valet](https://laravel.com/docs/valet) that supports local SSL, or temporarily set `secure => false` in `helpers.php`.

---

## Test Account

| Field    | Value          |
|----------|----------------|
| Username | `administrator` |
| Password | `Password123`   |

Delete this account before going to production.

---

## Security Features

| Feature | Detail |
|---|---|
| Password hashing | `PASSWORD_BCRYPT` with cost 12, auto-rehash on login |
| CSRF protection | Per-session token validated on every POST |
| Session fixation | `session_regenerate_id(true)` on successful login |
| Secure cookies | `httponly`, `secure`, `SameSite=Strict` |
| Brute-force protection | 5 failed attempts → 5 minute IP lockout |
| Input sanitization | `htmlspecialchars` + regex validation on all inputs |
| Error handling | DB/server errors are logged, never shown to the user |
| Unique usernames | Enforced at DB level (`UNIQUE KEY`) and checked before insert |

---

## Multilanguage

The app defaults to **English** on first visit. A toggle button in the top-right corner of every page switches between English and Turkish. The selected language is stored in the session and persists across pages.

### How it works

`lang.php` contains the `Lang` class. Pages call:

```php
Lang::init();        // load language from session
Lang::t('key');      // get translated string
Lang::toggleButton() // render the switch button HTML
```

### Adding a new language

1. Copy `lang/tr.php` → `lang/de.php` (or any ISO code)
2. Translate all the values — keep the keys identical
3. Add the code to `SUPPORTED_LANGS` in `lang.php`:

```php
private const SUPPORTED_LANGS = ['en', 'tr', 'de'];
```

Done. No other changes needed.

### External translation API (future)

The `Lang` class has a ready-made hook for external APIs (DeepL, Google Translate, LibreTranslate, etc.):

1. Implement `fetchFromApi()` in `lang.php`
2. Set `TRANSLATION_API_ENABLED=true` in your environment
3. Set `TRANSLATION_API_KEY` and `TRANSLATION_API_URL`

---

## Exception Handling

Two exception types are used so callers can show the right translated message:

| Exception | Trigger | Displayed via |
|---|---|---|
| `DatabaseException` | PDO connection failure | `Lang::t('err_db')` |
| `RuntimeException` | Any other server error | `Lang::t('err_server')` |

---

## Extending

**Add a "Remember Me" cookie**
Generate a secure token, store it in a `remember_tokens` table, set a long-lived cookie, and validate it on page load before the session check.

**Add email verification**
Add an `email` column and a `verified_at` timestamp to `users`. On register, send a signed token link and only set `status = 'A'` after the user clicks it.

**Add password reset**
Create a `password_resets` table with a hashed token and expiry. On request, email the user a signed link. On submit, verify the token and update the password hash.
