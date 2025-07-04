<?php

echo "=== حالة الخدمات ===\n\n";

// Redis
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    echo "✅ Redis: متصل\n";
} catch (Exception $e) {
    echo "❌ Redis: غير متصل\n";
}

// OPcache
if (function_exists('opcache_get_status') && opcache_get_status()) {
    echo "✅ OPcache: مفعل\n";
} else {
    echo "❌ OPcache: غير مفعل\n";
}

// Swoole
if (extension_loaded('swoole')) {
    echo "✅ Swoole: مثبت\n";
} else {
    echo "⚠️ Swoole: غير مثبت (استخدم RoadRunner بدلاً منه)\n";
}

// Queue Workers
$output = shell_exec('ps aux | grep "queue:work" | grep -v grep');
if ($output) {
    echo "✅ Queue Workers: يعمل\n";
} else {
    echo "❌ Queue Workers: متوقف\n";
}

echo "\n=== انتهى الفحص ===\n";