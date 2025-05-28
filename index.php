<?php

/**
 * 轻量级MVC框架入口文件
 */

// 设置错误显示
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 引入Composer自动加载
require 'vendor/autoload.php';

// 设置默认时区
date_default_timezone_set('Asia/Shanghai');

// 启动应用程序
try {
    // 获取应用实例并运行
    $app = App\Core\App::getInstance();
    $app->run();
} catch (Exception $e) {
    // 如果开启了调试模式，显示错误信息
    if (isset($app) && $app->getConfig('debug')) {
        echo '<h1>应用错误</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        // 生产环境仅显示友好错误
        echo '<h1>系统错误</h1>';
        echo '<p>抱歉，系统遇到了错误，请稍后再试。</p>';
    }
}
