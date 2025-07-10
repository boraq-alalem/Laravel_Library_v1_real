#!/bin/bash

# تطبيق التحسينات النهائية للحمولة العالية
echo "تطبيق التحسينات النهائية للحمولة العالية..."

# تطبيق تحسينات MySQL
mysql -u alalem -p123456789@Rc AlalemLibrary -e "
SET GLOBAL innodb_buffer_pool_size = 1073741824;
SET GLOBAL query_cache_size = 268435456;
SET GLOBAL query_cache_type = ON;
SET GLOBAL max_connections = 200;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL innodb_flush_method = O_DIRECT;
"

# تحسين Redis
redis-cli CONFIG SET maxmemory 512mb
redis-cli CONFIG SET maxmemory-policy allkeys-lru
redis-cli CONFIG SET tcp-keepalive 300

# تنظيف وإعادة بناء الكاش
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan optimize

# تطبيق الفهارس
php artisan migrate --path=database/migrations/2023_06_01_000000_add_indexes_to_tables.php

echo "تم تطبيق التحسينات النهائية بنجاح!"