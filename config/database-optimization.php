<?php

return [
    'mysql' => [
        'options' => [
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "
                SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';
                SET SESSION query_cache_type = ON;
                SET SESSION tmp_table_size = 268435456;
                SET SESSION max_heap_table_size = 268435456;
                SET SESSION join_buffer_size = 4194304;
                SET SESSION sort_buffer_size = 4194304;
            "
        ]
    ]
];