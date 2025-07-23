<?php

echo "=== تفعيل PHP JIT ===" . PHP_EOL;

// فحص الإصدار
echo "إصدار PHP: " . PHP_VERSION . PHP_EOL;

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "❌ JIT يتطلب PHP 8.0 أو أحدث" . PHP_EOL;
    exit(1);
}

// فحص OPcache
if (!extension_loaded('Zend OPcache')) {
    echo "❌ OPcache غير مفعل" . PHP_EOL;
    exit(1);
}

echo "✅ OPcache مفعل" . PHP_EOL;

// فحص JIT
$jit = ini_get('opcache.jit');
echo "JIT الحالي: " . ($jit ?: 'غير مفعل') . PHP_EOL;

// فحص JIT Buffer
$jit_buffer = ini_get('opcache.jit_buffer_size');
echo "JIT Buffer: " . ($jit_buffer ? ($jit_buffer / 1024 / 1024) . 'MB' : 'غير محدد') . PHP_EOL;

// إنشاء ملف تكوين JIT
$jit_config = "
; تفعيل JIT للأداء العالي
opcache.jit=tracing
opcache.jit_buffer_size=256M
opcache.jit_hot_func=127
opcache.jit_hot_loop=64
opcache.jit_hot_return=8
opcache.jit_hot_side_exit=8
opcache.jit_max_root_traces=1024
opcache.jit_max_side_traces=128
opcache.jit_max_trace_length=1024
";

file_put_contents('jit-config.ini', $jit_config);
echo "✅ تم إنشاء ملف تكوين JIT: jit-config.ini" . PHP_EOL;

echo "=== انتهى تفعيل JIT ===" . PHP_EOL;