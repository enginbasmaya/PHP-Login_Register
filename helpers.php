<?php

declare(strict_types=1);

// ─── Session Security ────────────────────────────────────────────────────────

function startSecureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => true,   // HTTPS only
            'httponly' => true,   // Block JS access
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function regenerateSession(): void
{
    session_regenerate_id(true);
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// ─── CSRF Protection ─────────────────────────────────────────────────────────

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string
{
    return sprintf(
        '<input type="hidden" name="csrf_token" value="%s">',
        htmlspecialchars(generateCsrfToken())
    );
}

// ─── Input Sanitization ──────────────────────────────────────────────────────

function sanitizeString(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function validateUsername(string $username): bool
{
    // 3-30 characters, letters/digits/underscore only
    return (bool) preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username);
}

function validatePassword(string $password): bool
{
    // At least 8 characters, 1 uppercase letter, 1 digit
    return strlen($password) >= 8 &&
           (bool) preg_match('/[A-Z]/', $password) &&
           (bool) preg_match('/[0-9]/', $password);
}

// ─── Flash Messages ──────────────────────────────────────────────────────────

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function renderFlash(): string
{
    $flash = getFlash();
    if ($flash === null) return '';

    $type = $flash['type'] === 'success' ? 'success' : 'error';
    return sprintf(
        '<div class="alert alert--%s">%s</div>',
        $type,
        sanitizeString($flash['message'])
    );
}
