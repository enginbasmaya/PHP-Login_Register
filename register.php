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

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = Lang::t('err_csrf');
    } else {
        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if (!validateUsername($username)) {
            $errors[] = Lang::t('err_username_fmt');
        }

        if (!validatePassword($password)) {
            $errors[] = Lang::t('err_password_fmt');
        }

        if ($password !== $password2) {
            $errors[] = Lang::t('err_password_match');
        }

        if (empty($errors)) {
            try {
                $db   = Database::getInstance();
                $stmt = $db->prepare('SELECT Kod FROM users WHERE username = ?');
                $stmt->execute([$username]);

                if ($stmt->fetch()) {
                    $errors[] = Lang::t('err_username_taken');
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)')
                       ->execute([$username, $hashedPassword]);

                    setFlash('success', Lang::t('success_register'));
                    header('Location: login.php');
                    exit;
                }

            } catch (DatabaseException $e) {
                $errors[] = Lang::t('err_db');
                error_log($e->getMessage());
            } catch (RuntimeException $e) {
                $errors[] = Lang::t('err_server');
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
    <title><?= Lang::t('register_title') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">

        <div class="lang-bar">
            <?= Lang::toggleButton() ?>
        </div>

        <h2><?= Lang::t('register_heading') ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= sanitizeString($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="username"><?= Lang::t('field_username') ?></label>
                <input type="text" id="username" name="username"
                       value="<?= sanitizeString($_POST['username'] ?? '') ?>"
                       autocomplete="username" required minlength="3" maxlength="30">
            </div>

            <div class="form-group">
                <label for="password"><?= Lang::t('field_password') ?></label>
                <input type="password" id="password" name="password"
                       autocomplete="new-password" required minlength="8">
                <small><?= Lang::t('password_hint') ?></small>
            </div>

            <div class="form-group">
                <label for="password2"><?= Lang::t('field_password2') ?></label>
                <input type="password" id="password2" name="password2"
                       autocomplete="new-password" required>
            </div>

            <button type="submit" class="btn btn--primary">
                <?= Lang::t('register_submit') ?>
            </button>
        </form>

        <p class="auth-link"><?= Lang::t('register_link') ?></p>
    </div>
</body>
</html>
