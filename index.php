<?php
require 'vendor/autoload.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline', [
    'app_info' => [
        'api_id' => getenv('API_ID'),
        'api_hash' => getenv('API_HASH')
    ]
]);

echo "در حال راه‌اندازی...\n";
$MadelineProto->start();
echo "احراز هویت موفق!\n";

while(true) { sleep(1); }
