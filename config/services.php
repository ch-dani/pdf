<?php

$is_debug = false;

$debug_ips = ["91.225.196.106"];
$ip = @$_SERVER['REMOTE_ADDR'];
if(in_array($ip, $debug_ips)){
	$is_debug = true;
}


return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => $is_debug?env('STRIPE_DEBUG_KEY'):env('STRIPE_KEY'),
        'secret' => $is_debug?env('STRIPE_DEBUG_SECRET'):env('STRIPE_SECRET'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => 'https://freeconvertpdf.com/google-callback',
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => 'https://freeconvertpdf.com/facebook-callback',
    ],

    'sendgrid' => [
        'api_key' => env('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'),
    ],

];
