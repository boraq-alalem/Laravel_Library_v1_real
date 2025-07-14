<?php

return [
    // إعدادات الكاش
    'cache' => [
        'default_ttl' => 3600, // ساعة واحدة
        'api_ttl' => 1800,     // 30 دقيقة
        'static_ttl' => 86400, // يوم كامل
    ],
    
    // إعدادات قاعدة البيانات
    'database' => [
        'chunk_size' => 1000,
        'max_connections' => 100,
        'timeout' => 30,
    ],
    
    // إعدادات الضغط
    'compression' => [
        'enabled' => true,
        'min_size' => 1024, // 1KB
        'level' => 6,       // مستوى متوسط
    ],
    
    // إعدادات الاستجابة
    'response' => [
        'max_execution_time' => 30,
        'memory_limit' => '256M',
    ],
];