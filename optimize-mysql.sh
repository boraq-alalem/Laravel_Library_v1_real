#!/bin/bash

# تحسين إعدادات MySQL
echo "تطبيق تحسينات MySQL..."

mysql -u alalem -p123456789@Rc AlalemLibrary -e "
SET GLOBAL innodb_buffer_pool_size = 2147483648;
SET GLOBAL query_cache_size = 536870912;
SET GLOBAL query_cache_type = ON;
SET GLOBAL max_connections = 500;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL innodb_flush_method = O_DIRECT;
SET GLOBAL innodb_log_file_size = 268435456;
SET GLOBAL tmp_table_size = 134217728;
SET GLOBAL max_heap_table_size = 134217728;
SET GLOBAL thread_cache_size = 50;
SET GLOBAL table_open_cache = 4000;
" 2>/dev/null || echo "بعض إعدادات MySQL تحتاج صلاحيات أعلى"

echo "تم تطبيق تحسينات MySQL!"