<?php

declare(strict_types=1);

// ─── Türkçe Çeviri Dosyası ───────────────────────────────────────────────────
// Her anahtar bir çeviri anahtarıdır. Yeni dil eklemek için bu dosyayı
// kopyalayıp aynı anahtarlarla çevirin.

return [

    // Genel
    'app_name'         => 'Auth Sistemi',
    'lang_toggle'      => 'English',
    'lang_toggle_icon' => '🇺🇸',

    // Login sayfası
    'login_title'      => 'Giriş Yap',
    'login_heading'    => 'Giriş Yap',
    'login_submit'     => 'Giriş Yap',
    'login_link'       => 'Hesabın yok mu? <a href="register.php">Kayıt Ol</a>',

    // Register sayfası
    'register_title'   => 'Kayıt Ol',
    'register_heading' => 'Kayıt Ol',
    'register_submit'  => 'Kayıt Ol',
    'register_link'    => 'Zaten hesabın var mı? <a href="login.php">Giriş Yap</a>',

    // Form alanları
    'field_username'   => 'Kullanıcı Adı',
    'field_password'   => 'Şifre',
    'field_password2'  => 'Şifre Tekrar',
    'password_hint'    => 'En az 8 karakter, 1 büyük harf ve 1 rakam',

    // Welcome sayfası
    'welcome_heading'  => 'Hoş Geldin',
    'welcome_message'  => 'Başarıyla giriş yaptın.',
    'logout'           => 'Çıkış Yap',

    // Hata mesajları
    'err_csrf'         => 'Geçersiz istek. Lütfen tekrar deneyin.',
    'err_invalid_creds'=> 'Geçersiz kullanıcı adı veya şifre.',
    'err_username_fmt' => 'Kullanıcı adı 3-30 karakter olmalı, sadece harf/rakam/alt çizgi içerebilir.',
    'err_password_fmt' => 'Şifre en az 8 karakter, 1 büyük harf ve 1 rakam içermelidir.',
    'err_password_match'=> 'Şifreler eşleşmiyor.',
    'err_username_taken'=> 'Bu kullanıcı adı zaten kullanılıyor.',
    'err_server'       => 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.',
    'err_db'           => 'Veritabanına bağlanılamadı. Lütfen daha sonra tekrar deneyin.',
    'err_required'     => 'Kullanıcı adı ve şifre gereklidir.',
    'err_lockout'      => '%d saniye bekleyin (çok fazla başarısız deneme).',

    // Başarı mesajları
    'success_register' => 'Kayıt başarılı! Giriş yapabilirsiniz.',

];
