<?php

return [
    'server' => env('OCTANE_SERVER', 'swoole'),
    
    'https' => [
        'host' => env('OCTANE_HTTPS_HOST', '127.0.0.1'),
        'port' => env('OCTANE_HTTPS_PORT', 8443),
        'cert' => env('OCTANE_HTTPS_CERT'),
        'key' => env('OCTANE_HTTPS_KEY'),
    ],
    
    'swoole' => [
        'host' => env('OCTANE_HOST', '0.0.0.0'),
        'port' => env('OCTANE_PORT', 8000),
        'workers' => env('OCTANE_WORKERS', 'auto'),
        'task_workers' => env('OCTANE_TASK_WORKERS', 'auto'),
        'max_execution_time' => env('OCTANE_MAX_EXECUTION_TIME', 30),
        'max_request_size' => env('OCTANE_MAX_REQUEST_SIZE', 10485760),
        'options' => [
            'log_file' => storage_path('logs/swoole_http.log'),
            'package_max_length' => 10 * 1024 * 1024,
        ],
    ],
    
    'warm' => [
        Illuminate\Http\Middleware\TrustHosts::class,
        Illuminate\Http\Middleware\TrustProxies::class,
        Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
        Illuminate\Http\Middleware\ValidatePostSize::class,
        Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ],
    
    'cache' => [
        'rows' => 1000,
        'bytes' => 10000,
    ],
];