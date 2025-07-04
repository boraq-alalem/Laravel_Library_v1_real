<?php

echo "=== اختبار أداء JIT ===" . PHP_EOL;

// اختبار بدون JIT
function fibonacci($n) {
    if ($n <= 1) return $n;
    return fibonacci($n - 1) + fibonacci($n - 2);
}

function test_performance($name, $iterations = 100000) {
    echo "اختبار: $name" . PHP_EOL;
    
    $start = microtime(true);
    
    // اختبار حسابي مكثف
    $sum = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $sum += sqrt($i) * sin($i) + cos($i);
    }
    
    $end = microtime(true);
    $time = ($end - $start) * 1000;
    
    echo "الوقت: " . round($time, 2) . "ms" . PHP_EOL;
    echo "النتيجة: " . round($sum, 2) . PHP_EOL;
    
    return $time;
}

// اختبار الأداء الحالي
$current_time = test_performance("الأداء الحالي");

// فحص حالة JIT
$jit_status = ini_get('opcache.jit') ?: 'غير مفعل';
echo "حالة JIT: $jit_status" . PHP_EOL;

if ($jit_status === 'غير مفعل') {
    echo "⚠️ JIT غير مفعل - يمكن تحسين الأداء بتفعيله" . PHP_EOL;
} else {
    echo "✅ JIT مفعل" . PHP_EOL;
}

echo "=== انتهى اختبار الأداء ===" . PHP_EOL;