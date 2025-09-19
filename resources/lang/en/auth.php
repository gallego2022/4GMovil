<?php

return [
    // Authentication messages
    'login' => 'Login',
    'register' => 'Create Account',
    'logout' => 'Logout',
    'remember_me' => 'Keep me logged in',
    'forgot_password' => 'Forgot your password?',
    'reset_password' => 'Reset Password',
    'send_reset_link' => 'Send Reset Link',
    'click_reset_link' => 'Click here to reset your password',
    'verify_email' => 'Verify Email Address',
    'verify_email_sent' => 'A new verification link has been sent to your email address.',
    'verify_email_notice' => 'Before proceeding, please check your email for a verification link.',
    'verify_email_success' => 'Your email has been verified successfully.',

    // Password messages
    'change_password' => 'Change Password',
    'change_password_description' => 'Please enter your new password to continue.',
    'current_password' => 'Current Password',
    'new_password' => 'New Password',
    'confirm_password' => 'Confirm Password',
    'password_requirements' => 'Password must be at least 8 characters with uppercase, lowercase, number and symbol.',
    'password_current_wrong' => 'Current password is incorrect.',
    'password_change_success' => 'Your password has been changed successfully.',
    'password_change_error' => 'An error occurred while changing the password.',

    // Success/error messages
    'login_success' => 'Welcome back! You have logged in successfully.',
    'login_error' => 'Email or password is incorrect. Please check your data.',
    'login_error_google_account' => 'This account was created with Google. Please log in using the "Continue with Google" button or set a password from your profile.',
    'account_inactive' => 'Your account is inactive. Please contact the administrator to activate it.',
    'register_success' => 'Account created successfully! Please verify your email to activate your account.',
    'register_success_title' => 'Registration Successful!',
    'logout_success' => 'You have logged out successfully. See you soon!',
    'welcome' => 'Welcome to 4GMovil!',
    'goodbye' => 'Thank you for using 4GMovil!',

    // Validation messages
    'validation' => [
        'nombre_usuario' => [
            'required' => 'Username is required.',
            'max' => 'Username cannot have more than :max characters.',
            'regex' => 'Username can only contain letters and spaces.',
        ],
        'correo_electronico' => [
            'required' => 'Email is required.',
            'email' => 'Please enter a valid email address.',
            'unique' => 'This email is already registered in our system.',
        ],
        'telefono' => [
            'required' => 'Phone number is required.',
            'regex' => 'Phone must have 10 digits and start with 3.',
        ],
        'contrasena' => [
            'required' => 'Password is required.',
            'min' => 'Password must be at least :min characters.',
            'confirmed' => 'Passwords do not match.',
            'regex' => 'Password must meet security requirements.',
        ],
        'acepta_terminos' => [
            'accepted' => 'You must accept the terms and conditions to continue.',
        ],
    ],

    // Verification messages
    'verification' => [
        'required' => 'Please verify your email address before continuing.',
        'sent' => 'A new verification link has been sent to your email address.',
        'verified' => 'Your email has been verified successfully.',
        'already_verified' => 'Your email has already been verified.',
    ],

    // Security messages
    'security' => [
        'password_requirements' => 'Your password must meet security requirements to protect your account.',
        'account_locked' => 'Your account has been temporarily locked due to multiple failed attempts.',
        'too_many_attempts' => 'Too many failed attempts. Please try again in :minutes minutes.',
        'session_expired' => 'Your session has expired. Please log in again.',
    ],
]; 
