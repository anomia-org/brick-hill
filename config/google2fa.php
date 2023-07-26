<?php

return [

    /*
     * Enable / disable Google2FA.
     */
    'enabled' => true,

    /*
     * Lifetime in minutes.
     *
     * In case you need your users to be asked for a new one time passwords from time to time.
     */
    'lifetime' => 0, // 0 = eternal

    /*
     * Renew lifetime at every new request.
     */
    'keep_alive' => true,

    /*
     * Auth container binding.
     */
    'auth' => 'auth',

    /*
     * 2FA verified session var.
     */

    'session_var' => '2fa',

    /*
     * One Time Password request input name.
     */
    'otp_input' => 'tfa_token',

    /*
     * One Time Password Window.
     */
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    // this literally doesnt work ???
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret.
     */
    'otp_secret_column' => 'secret_2fa',

    /*
     * One Time Password View.
     */
    'view' => 'pages.auth.2fa',

    /*
     * One Time Password error message.
     */
    'error_messages' => [
        'wrong_otp'       => "Incorrect 2FA token.",
        'cannot_be_empty' => 'You have entered too many wrong tokens. Try again soon.',
        'unknown'         => 'An unknown error has occurred. Please try again.',
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => false,

    /*
     * Which image backend to use for generating QR codes?
     *
     * Supports imagemagick, svg and eps
     */
    'qrcode_image_backend' => \PragmaRX\Google2FALaravel\Support\Constants::QRCODE_IMAGE_BACKEND_SVG,

];
