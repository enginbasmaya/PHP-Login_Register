<?php

declare(strict_types=1);

// ─── English Translation File ────────────────────────────────────────────────
// Mirror of tr.php — keep keys identical, only change values.

return [

    // General
    'app_name'         => 'Auth System',
    'lang_toggle'      => 'Türkçe',
    'lang_toggle_icon' => '🇹🇷',

    // Login page
    'login_title'      => 'Sign In',
    'login_heading'    => 'Sign In',
    'login_submit'     => 'Sign In',
    'login_link'       => 'No account yet? <a href="register.php">Register</a>',

    // Register page
    'register_title'   => 'Register',
    'register_heading' => 'Create Account',
    'register_submit'  => 'Create Account',
    'register_link'    => 'Already have an account? <a href="login.php">Sign In</a>',

    // Form fields
    'field_username'   => 'Username',
    'field_password'   => 'Password',
    'field_password2'  => 'Confirm Password',
    'password_hint'    => 'At least 8 characters, 1 uppercase letter and 1 number',

    // Welcome page
    'welcome_heading'  => 'Welcome',
    'welcome_message'  => 'You have successfully signed in.',
    'logout'           => 'Sign Out',

    // Error messages
    'err_csrf'         => 'Invalid request. Please try again.',
    'err_invalid_creds'=> 'Invalid username or password.',
    'err_username_fmt' => 'Username must be 3-30 characters, letters/numbers/underscore only.',
    'err_password_fmt' => 'Password must be at least 8 characters, include 1 uppercase and 1 number.',
    'err_password_match'=> 'Passwords do not match.',
    'err_username_taken'=> 'This username is already taken.',
    'err_server'       => 'Server error. Please try again later.',
    'err_db'           => 'Could not connect to the database. Please try again later.',
    'err_required'     => 'Username and password are required.',
    'err_lockout'      => 'Too many failed attempts. Please wait %d seconds.',

    // Success messages
    'success_register' => 'Registration successful! You can now sign in.',

];
