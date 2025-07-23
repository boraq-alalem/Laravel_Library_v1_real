#!/bin/bash

echo "=== تطبيق تحسينات Nginx ==="

# نسخ احتياطي من التكوين الحالي
echo "إنشاء نسخة احتياطية..."
sudo cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf.backup.$(date +%Y%m%d_%H%M%S)

# فحص وجود مجلد sites-available
sudo mkdir -p /etc/nginx/sites-available
sudo mkdir -p /etc/nginx/sites-enabled

# نسخ التكوين المحسن
echo "تطبيق التكوين المحسن..."
sudo cp nginx-global.conf /etc/nginx/nginx.conf
sudo cp nginx-optimization.conf /etc/nginx/sites-available/alalem.c-library.org

# تفعيل الموقع
sudo ln -sf /etc/nginx/sites-available/alalem.c-library.org /etc/nginx/sites-enabled/

# فحص التكوين
echo "فحص التكوين..."
if sudo nginx -t; then
    echo "✅ التكوين صحيح"
    
    # إعادة تحميل Nginx
    echo "إعادة تحميل Nginx..."
    sudo systemctl reload nginx
    
    if [ $? -eq 0 ]; then
        echo "✅ تم تطبيق التحسينات بنجاح"
    else
        echo "❌ فشل في إعادة تحميل Nginx"
        sudo systemctl status nginx
    fi
else
    echo "❌ خطأ في التكوين - استعادة النسخة الاحتياطية"
    sudo cp /etc/nginx/nginx.conf.backup.* /etc/nginx/nginx.conf
fi

echo "=== انتهى تطبيق تحسينات Nginx ==="