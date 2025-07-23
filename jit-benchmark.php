<?php

echo "=== مقارنة الأداء: بدون JIT vs مع JIT ===" . PHP_EOL;

function intensive_calculation($iterations = 50000) {
    $result = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $result += sqrt($i) * sin($i) + cos($i) * tan($i/100);
        $result += pow($i % 10, 2);
    }
    return $result;
}

function benchmark($name, $func, $iterations = 5) {
    echo "اختبار: $name" . PHP_EOL;
    $times = [];
    
    for ($i = 0; $i < $iterations; $i++) {
        $start = microtime(true);
        $result = $func();
        $end = microtime(true);
        $times[] = ($end - $start) * 1000;
    }
    
    $avg_time = array_sum($times) / count($times);
    $min_time = min($times);
    $max_time = max($times);
    
    echo "متوسط الوقت: " . round($avg_time, 2) . "ms" . PHP_EOL;
    echo "أسرع وقت: " . round($min_time, 2) . "ms" . PHP_EOL;
    echo "أبطأ وقت: " . round($max_time, 2) . "ms" . PHP_EOL;
    echo "---" . PHP_EOL;
    
    return $avg_time;
}

// اختبار الأداء الحالي
$current_performance = benchmark("الأداء الحالي (بدون JIT)", function() {
    return intensive_calculation();
});

// معلومات النظام
echo "معلومات النظام:" . PHP_EOL;
echo "PHP Version: " . PHP_VERSION . PHP_EOL;
echo "OPcache: " . (opcache_get_status() ? 'مفعل' : 'غير مفعل') . PHP_EOL;
echo "JIT: " . (ini_get('opcache.jit') ?: 'غير مفعل') . PHP_EOL;
echo "Memory Limit: " . ini_get('memory_limit') . PHP_EOL;

echo PHP_EOL . "=== التوقعات مع JIT ===" . PHP_EOL;
echo "تحسين متوقع: 20-50% أسرع" . PHP_EOL;
echo "الوقت المتوقع مع JIT: " . round($current_performance * 0.7, 2) . "ms" . PHP_EOL;

echo "=== انتهى المقارنة ===" . PHP_EOL;