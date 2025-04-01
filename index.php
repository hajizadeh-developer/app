<?php
require __DIR__.'/vendor/autoload.php';

// تنظیمات خطاها
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__.'/error.log');

// دریافت تنظیمات از محیط
$apiId = getenv('API_ID');
$apiHash = getenv('API_HASH');
$botToken = getenv('BOT_TOKEN');

// بررسی وجود MadelineProto
if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

$settings = [
    'app_info' => [
        'api_id' => $apiId,
        'api_hash' => $apiHash,
    ],
    'logger' => [
        'logger_level' => 4,
        'logger' => __DIR__.'/madeline.log',
    ]
];

// ایجاد نمونه MadelineProto
$MadelineProto = new \danog\MadelineProto\API('session.madeline', $settings);
$MadelineProto->async(true);

// تابع برای دریافت نام فایل
function getFilename($media): string {
    if (isset($media['document']['attributes'])) {
        foreach ($media['document']['attributes'] as $attr) {
            if (isset($attr['file_name'])) {
                return $attr['file_name'];
            }
        }
    }
    return 'file_'.time().'.dat';
}

// Health Check Endpoint
if (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/health') {
    header('Content-Type: text/plain');
    echo 'OK';
    exit;
}

// پردازش پیام‌های تلگرام
$updateHandler = function ($update) use ($MadelineProto) {
    if (isset($update['message']) && isset($update['message']['media'])) {
        $message = $update['message'];
        $msgId = $message['id'];
        $peer = $message['peer_id'];
        
        $filename = getFilename($message['media']);
        $size = $message['media']['document']['size'] ?? 0;
        
        $downloadUrl = "https://{$_SERVER['HTTP_HOST']}/download/{$msgId}";
        
        $MadelineProto->loop(
            fn() => $MadelineProto->messages->sendMessage([
                'peer' => $peer,
                'message' => "📥 لینک دانلود فایل:\n{$downloadUrl}\n\n" .
                             "📁 نام فایل: {$filename}\n" .
                             "📦 حجم فایل: ".round($size/1024/1024, 2)."MB",
                'reply_to_msg_id' => $msgId
            ])
        );
    }
};

// اجرای اصلی
$MadelineProto->loop(function () use ($MadelineProto, $updateHandler) {
    // تنظیم هندلر آپدیت‌ها
    $MadelineProto->setCallback($updateHandler);
    
    // شروع اتصال
    yield $MadelineProto->start();
    
    echo "ربات آماده دریافت فایل است...\n";
    
    // حلقه اصلی
    while (true) {
        try {
            $updates = yield $MadelineProto->getUpdates();
            foreach ($updates as $update) {
                // پردازش خودکار توسط هندلر
            }
            yield new \Amp\Delay(1000);
        } catch (Throwable $e) {
            file_put_contents('error.log', date('[Y-m-d H:i:s]').' '.$e->getMessage().PHP_EOL, FILE_APPEND);
            yield new \Amp\Delay(5000);
        }
    }
});
