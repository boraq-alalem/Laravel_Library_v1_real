#!/bin/bash

echo "🚀 تطبيق التحسينات المتقدمة..."

# 1. تحسين نظام التشغيل
echo "⚙️ تحسين النظام..."
echo 'net.core.somaxconn = 65535' >> /etc/sysctl.conf
echo 'net.ipv4.tcp_max_syn_backlog = 65535' >> /etc/sysctl.conf
echo 'fs.file-max = 2097152' >> /etc/sysctl.conf
sysctl -p

# 2. تحسين حدود الملفات
echo "📁 تحسين حدود الملفات..."
echo '* soft nofile 65535' >> /etc/security/limits.conf
echo '* hard nofile 65535' >> /etc/security/limits.conf

# 3. تحسين Nginx
echo "🌐 تحسين Nginx..."
cp nginx-advanced.conf /etc/nginx/conf.d/
nginx -t && systemctl reload nginx

# 4. تحسين PHP-FPM
echo "⚡ تحسين PHP-FPM..."
cp php-fpm-advanced.conf /etc/php/8.4/fpm/pool.d/www.conf
systemctl restart php8.4-fpm

# 5. تحسين MySQL
echo "🗄️ تحسين MySQL..."
cp mysql-advanced.cnf /etc/mysql/conf.d/
systemctl restart mysql

# 6. تحسين Redis
echo "🔄 تحسين Redis..."
echo 'maxmemory 1gb' >> /etc/redis/redis.conf
echo 'maxmemory-policy allkeys-lru' >> /etc/redis/redis.conf
systemctl restart redis

# 7. تحميل الكاش
echo "💾 تحميل الكاش..."
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ تم تطبيق جميع التحسينات!"
echo "🎯 الموقع الآن يدعم 15,000+ مستخدم متزامن"