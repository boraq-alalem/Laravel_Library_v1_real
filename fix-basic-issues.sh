#!/bin/bash

# إصلاح المشاكل الأساسية
echo "إصلاح المشاكل الأساسية..."

# تثبيت Redis PHP extension إذا لم يكن مثبتاً
if ! php -m | grep -q redis; then
    echo "تثبيت Redis PHP extension..."
    sudo apt update
    sudo apt install -y php-redis
    
    # إعادة تشغيل PHP-FPM
    if systemctl is-active php8.1-fpm &>/dev/null; then
        sudo systemctl restart php8.1-fpm
    elif systemctl is-active php8.0-fpm &>/dev/null; then
        sudo systemctl restart php8.0-fpm
    elif systemctl is-active php7.4-fpm &>/dev/null; then
        sudo systemctl restart php7.4-fpm
    fi
fi

# تنظيف وإعادة بناء الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# إعادة بناء الكاش
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "تم إصلاح المشاكل الأساسية!"