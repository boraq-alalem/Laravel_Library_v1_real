#!/bin/bash

echo "=== تطبيق تحسينات JIT ==="

# إنشاء ملف تكوين JIT محسن
cat > jit-optimized.ini << 'EOF'
; تحسينات OPcache + JIT للأداء العالي
opcache.enable=1
opcache.memory_consumption=512M
opcache.interned_strings_buffer=64M
opcache.max_accelerated_files=32531
opcache.validate_timestamps=0
opcache.save_comments=0
opcache.enable_file_override=1

; تفعيل JIT
opcache.jit=tracing
opcache.jit_buffer_size=256M
opcache.jit_hot_func=127
opcache.jit_hot_loop=64
opcache.jit_hot_return=8
opcache.jit_hot_side_exit=8
opcache.jit_max_root_traces=1024
opcache.jit_max_side_traces=128
opcache.jit_max_trace_length=1024

; تحسينات إضافية
realpath_cache_size=4096K
realpath_cache_ttl=600
EOF

echo "✅ تم إنشاء ملف التكوين المحسن: jit-optimized.ini"

# اختبار التكوين الجديد
echo "اختبار الأداء مع JIT..."
php -c jit-optimized.ini test-jit.php

echo ""
echo "=== تعليمات التطبيق ==="
echo "لتطبيق JIT على الخادم، أضف هذه الإعدادات إلى php.ini:"
echo ""
cat jit-optimized.ini
echo ""
echo "ثم أعد تشغيل الخادم: sudo systemctl restart apache2"
echo "=== انتهى تطبيق JIT ==="