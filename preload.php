<?php

// PHP 7.4+ Preloading
opcache_compile_file(__DIR__ . '/vendor/autoload.php');
opcache_compile_file(__DIR__ . '/app/Models/User.php');
opcache_compile_file(__DIR__ . '/app/Models/Thesis.php');
opcache_compile_file(__DIR__ . '/app/Models/Author.php');
opcache_compile_file(__DIR__ . '/app/Models/University.php');
opcache_compile_file(__DIR__ . '/app/Models/Specialization.php');
opcache_compile_file(__DIR__ . '/app/Services/CacheService.php');

// Laravel core files
$laravelFiles = [
    '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php',
    '/vendor/laravel/framework/src/Illuminate/Http/Request.php',
    '/vendor/laravel/framework/src/Illuminate/Http/Response.php',
    '/vendor/laravel/framework/src/Illuminate/Routing/Router.php',
    '/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php',
];

foreach ($laravelFiles as $file) {
    if (file_exists(__DIR__ . $file)) {
        opcache_compile_file(__DIR__ . $file);
    }
}