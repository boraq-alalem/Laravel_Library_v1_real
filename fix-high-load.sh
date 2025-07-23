#!/bin/bash

# تحسينات فورية للحمولة العالية (150 مستخدم)
echo "تطبيق تحسينات فورية للحمولة العالية..."

# تحسين PHP-FPM للحمولة العالية
echo "[www]
pm = dynamic
pm.max_children = 150
pm.start_servers = 30
pm.min_spare_servers = 15
pm.max_spare_servers = 50
pm.max_requests = 500
request_terminate_timeout = 30
" > php-fpm-high-load.conf

# تحسين timeouts
echo "fastcgi_connect_timeout 30;
fastcgi_send_timeout 30;
fastcgi_read_timeout 30;
keepalive_timeout 30;
client_body_timeout 30;
client_header_timeout 30;
" > nginx-timeouts.conf

# تحسين Redis للحمولة العالية
redis-cli CONFIG SET timeout 0
redis-cli CONFIG SET tcp-keepalive 60
redis-cli CONFIG SET maxmemory 2gb

# تحسين MySQL connections
mysql -u alalem -p123456789@Rc AlalemLibrary -e "
SET GLOBAL max_connections = 300;
SET GLOBAL connect_timeout = 30;
SET GLOBAL wait_timeout = 300;
" 2>/dev/null || echo "MySQL settings skipped"

echo "تم تطبيق التحسينات الفورية!"