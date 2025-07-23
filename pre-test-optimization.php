<?php

echo "تحسين ما قبل الاختبار...\n";

// تشغيل تحسينات Laravel
exec('php artisan config:cache');
exec('php artisan route:cache');
exec('php artisan view:cache');

// تشغيل Migration للفهارس
exec('php artisan migrate --force');

// تنظيف الكاش القديم
exec('php artisan cache:clear');

// إحماء الكاش
$urls = [
    'https://alalem.c-library.org/api/theses/latest',
    'https://alalem.c-library.org/api/stats',
];

foreach ($urls as $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_exec($ch);
    curl_close($ch);
    echo "إحماء: $url\n";
}

echo "تم التحسين بنجاح!\n";