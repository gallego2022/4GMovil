<?php

return [
    // General form messages
    'required_field' => 'This field is required',
    'invalid_format' => 'The format is invalid',
    'fix_errors' => 'Please correct the errors before continuing',
    'validation_error' => 'Validation error',
    'success' => 'Operation successful',
    'error' => 'An error occurred',
    'loading' => 'Loading...',
    'processing' => 'Processing request...',
    'confirm_action' => 'Are you sure you want to perform this action?',
    'yes' => 'Yes',
    'no' => 'No',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'close' => 'Close',
    'back' => 'Back',
    'continue' => 'Continue',

    // Status messages   
    'status' => [
        'success' => 'Operation completed successfully',
        'error' => 'An error occurred while processing the request',
        'warning' => 'There are some issues that require your attention',
        'info' => 'Important information',
        'loading' => 'Please wait...',
        'saving' => 'Saving changes...',
        'updating' => 'Updating information...',
        'deleting' => 'Deleting...',
        'session_expired' => 'Your session has expired, please log in again',
    ],

    // Field-specific messages
    'name' => [
        'required' => 'Name is required',
        'format' => 'Name must be between 2 and 25 characters and contain only letters',
        'invalid_chars' => 'Name contains invalid characters',
        'too_short' => 'Name is too short (minimum 2 characters)',
        'too_long' => 'Name is too long (maximum 25 characters)',
    ],
    'email' => [
        'required' => 'Email is required',
        'format' => 'Email format is invalid',
        'unique' => 'This email is already registered',
        'not_found' => 'No account found with this email',
        'verified' => 'Email verified successfully',
        'verification_sent' => 'A verification email has been sent',
    ],
    'phone' => [
        'required' => 'Phone is required',
        'format' => 'Phone must have exactly 10 digits',
        'invalid' => 'Phone number is invalid',
        'unique' => 'This phone number is already registered',
    ],
    'password' => [
        'required' => 'Password is required',
        'min_length' => 'Password must be at least 8 characters',
        'uppercase' => 'Password must contain at least one uppercase letter',
        'lowercase' => 'Password must contain at least one lowercase letter',
        'number' => 'Password must contain at least one number',
        'symbol' => 'Password must contain at least one symbol',
        'mismatch' => 'Passwords do not match',
        'current_wrong' => 'Current password is incorrect',
        'recently_used' => 'You cannot use a recently used password',
        'requirements' => 'Password must meet the following requirements:',
        'strength' => [
            'weak' => 'Weak',
            'medium' => 'Medium',
            'strong' => 'Strong',
            'very_strong' => 'Very strong',
        ],
    ],
    'terms' => [
        'required' => 'You must accept the terms and conditions',
        'updated' => 'Terms and conditions have been updated',
        'read_more' => 'Read more',
    ],

    // Authentication messages
    'auth' => [
        'welcome' => 'Welcome back!',
        'goodbye' => 'See you soon!',
        'login_success' => 'You have logged in successfully',
        'login_error' => 'Login error',
        'logout_success' => 'You have logged out successfully',
        'invalid_credentials' => 'The provided credentials are incorrect',
        'account_locked' => 'Your account has been temporarily locked',
        'too_many_attempts' => 'Too many failed attempts. Please try again in :minutes minutes',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot your password?',
        'reset_password' => 'Reset password',
        'reset_password_success' => 'Your password has been reset successfully',
    ],

    // Registration messages
    'register' => [
        'success' => 'Registration completed successfully',
        'error' => 'Error completing registration',
        'verification_required' => 'Please verify your email',
        'already_registered' => 'You already have a registered account',
        'complete_profile' => 'Complete your profile',
    ],

    // Profile messages
    'profile' => [
        'updated' => 'Profile updated successfully',
        'update_error' => 'Error updating profile',
        'photo_updated' => 'Profile photo updated',
        'photo_error' => 'Error updating profile photo',
        'delete_account' => 'Delete account',
        'delete_confirm' => 'Are you sure you want to delete your account? This action cannot be undone',
    ],

    // Common error messages
    'errors' => [
        'default' => 'An unexpected error occurred',
        'connection' => 'Connection error',
        'timeout' => 'Request has exceeded the timeout',
        'validation' => 'Please verify the entered data',
        'server' => 'Server error',
        'not_found' => 'Not found',
        'forbidden' => 'Access denied',
        'unauthorized' => 'Unauthorized',
    ],
]; 