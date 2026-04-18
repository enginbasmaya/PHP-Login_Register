<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/lang.php';

startSecureSession();

if (($_GET['action'] ?? '') === 'lang_toggle') {
    Lang::toggle();
}

Lang::init();
requireLogin();

$username = sanitizeString($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::t('app_name') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">

        <div class="lang-bar">
            <?= Lang::toggleButton() ?>
        </div>

        <h2><?= Lang::t('welcome_heading') ?>, <?= $username ?>! 👋</h2>
        <p><?= Lang::t('welcome_message') ?></p>
        <a href="logout.php" class="btn btn--secondary"><?= Lang::t('logout') ?></a>
    </div>
</body>
</html>
