<?php
require __DIR__.'/vendor/autoload.php';

// تنظیمات Railway
$port = getenv('PORT') ?: 8080;
$webhookUrl = getenv('RAILWAY_STATIC_URL') ?: 'https://' . getenv('RAILWAY_PUBLIC_DOMAIN');

// Health Check Endpoint
if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
    if ($_SERVER['REQUEST_URI'] === '/health') {
        header('Content-Type: text/plain');
        echo 'OK';
        exit;
    }
    
    if ($_SERVER['REQUEST_URI'] === '/') {
        header('Content-Type: text/plain');
        echo "ربات تلگرام در حال اجراست!";
        exit;
    }
}

// بقیه کدهای MadelineProto (همانند قبل)
$MadelineProto = new \danog\MadelineProto\API('session.madeline', [
    'app_info' => [
        'api_id' => getenv('API_ID'),
        'api_hash' => getenv('API_HASH')
    ]
]);
// ...
