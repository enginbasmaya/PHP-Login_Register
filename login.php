<?php

declare(strict_types=1);

require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/lang.php';

startSecureSession();

if (($_GET['action'] ?? '') === 'lang_toggle') {
    Lang::toggle();
}

Lang::init();

if (isLoggedIn()) {
    header('Location: welcome.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = Lang::t('err_csrf');
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $ipKey       = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR']);
        $maxAttempts = 5;
        $lockoutTime = 300;

        $attempts = $_SESSION[$ipKey]['count'] ?? 0;
        $lastTry  = $_SESSION[$ipKey]['last']  ?? 0;

        if ((time() - $lastTry) > $lockoutTime) {
            $attempts = 0;
        }

        if ($attempts >= $maxAttempts) {
            $remaining = $lockoutTime - (time() - $lastTry);
            $error = Lang::t('err_lockout', max(0, $remaining));
        } elseif (empty($username) || empty($password)) {
            $error = Lang::t('err_required');
        } else {
            try {
                $db   = Database::getInstance();
                $stmt = $db->prepare(
                    "SELECT Kod, username, password FROM users WHERE username = ? AND status = 'A'"
                );
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    regenerateSession();
                    unset($_SESSION[$ipKey]);

                    $_SESSION['user_id']  = $user['Kod'];
                    $_SESSION['username'] = $user['username'];

                    if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
                        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                        $db->prepare('UPDATE users SET password = ? WHERE Kod = ?')
                           ->execute([$newHash, $user['Kod']]);
                    }

                    header('Location: welcome.php');
                    exit;
                }

                $_SESSION[$ipKey] = ['count' => $attempts + 1, 'last' => time()];
                $error = Lang::t('err_invalid_creds');

            } catch (DatabaseException $e) {
                $error = Lang::t('err_db');
                error_log($e->getMessage());
            } catch (RuntimeException $e) {
                $error = Lang::t('err_server');
                error_log($e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::t('login_title') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">

        <div class="lang-bar">
            <?= Lang::toggleButton() ?>
        </div>

        <h2><?= Lang::t('login_heading') ?></h2>

        <?= renderFlash() ?>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= sanitizeString($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username"><?= Lang::t('field_username') ?></label>
                <input type="text" id="username" name="username"
                       autocomplete="username" required>
            </div>

            <div class="form-group">
                <label for="password"><?= Lang::t('field_password') ?></label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn--primary">
                <?= Lang::t('login_submit') ?>
            </button>
        </form>

        <p class="auth-link"><?= Lang::t('login_link') ?></p>
    </div>
</body>
</html>
