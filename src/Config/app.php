<?php

return [
    'app_name' => '轻量级MVC框架',
    'debug' => true,
    'timezone' => 'Asia/Shanghai',
    'charset' => 'UTF-8',
    'base_url' => 'http://localhost:8000',
    
    // 启用注解路由
    'use_attributes_route' => true,
    
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'mvc_app',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ]
]; 