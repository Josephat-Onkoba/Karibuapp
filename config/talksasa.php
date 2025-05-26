<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TalkSasa SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for TalkSasa SMS integration
    |
    */

    'api_token' => env('TALKSASA_API_TOKEN'),
    'sender_id' => env('TALKSASA_SENDER_ID', 'BookPrestig'),
    'api_url' => env('TALKSASA_API_URL', 'https://bulksms.talksasa.com/api/v3/sms'),
]; 