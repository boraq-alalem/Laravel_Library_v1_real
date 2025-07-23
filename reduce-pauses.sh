#!/bin/bash

# تحسين إضافي لتقليل التوقف المؤقت
echo "تطبيق تحسينات إضافية لتقليل التوقف المؤقت..."

# تحسين PHP Garbage Collection
echo "opcache.jit_buffer_size=512M" >> /etc/php/8.1/fpm/conf.d/99-performance.ini
echo "zend.enable_gc=1" >> /etc/php/8.1/fpm/conf.d/99-performance.ini
echo "opcache.fast_shutdown=1" >> /etc/php/8.1/fpm/conf.d/99-performance.ini

# تحسين MySQL connection timeout
mysql -u alalem -p123456789@Rc AlalemLibrary -e "
SET GLOBAL wait_timeout = 600;
SET GLOBAL interactive_timeout = 600;
SET GLOBAL connect_timeout = 10;
"

# تحسين Redis persistence
redis-cli CONFIG SET save "900 1 300 10 60 10000"
redis-cli CONFIG SET stop-writes-on-bgsave-error no

# إعادة تشغيل الخدمات
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx

echo "تم تطبيق التحسينات الإضافية!"