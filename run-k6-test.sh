#!/bin/bash

echo "تشغيل اختبار k6..."

# تثبيت k6 إذا لم يكن مثبت
if ! command -v k6 &> /dev/null; then
    echo "تثبيت k6..."
    sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69
    echo "deb https://dl.k6.io/deb stable main" | sudo tee /etc/apt/sources.list.d/k6.list
    sudo apt-get update
    sudo apt-get install k6
fi

# تشغيل الاختبار
k6 run k6-test.js

echo "انتهى اختبار الأداء"