<?php
require __DIR__.'/vendor/autoload.php';

// Health Check برای Railway
if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI'])) {
    if ($_SERVER['REQUEST_URI'] === '/health') {
        header('Content-Type: text/plain');
        echo 'OK';
        exit;
    }
    
    header('Content-Type: text/plain');
    echo "ربات تلگرام در حال اجراست!\n";
    echo "برای استفاده، با ربات در تلگرام ارتباط برقرار کنید.";
    exit;
}

// تنظیمات MadelineProto
$MadelineProto = new \danog\MadelineProto\API(
    'session.madeline',
    [
        'app_info' => [
            'api_id' => getenv('API_ID'),
            'api_hash' => getenv('API_HASH')
        ]
    ]
);

// کد اصلی ربات شما اینجا ادامه می‌یابد...
// [کدهای قبلی شما که پیام‌ها را پردازش می‌کند]
