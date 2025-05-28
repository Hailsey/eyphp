<?php

namespace App\Core;

class App
{
    protected static $instance;
    protected $router;
    protected $request;
    protected $config = [];

    private function __construct()
    {
        $this->request = new Request();
        $this->router = new Router();
        $this->loadConfig();
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig()
    {
        // 加载配置文件
        if (file_exists(__DIR__ . '/../Config/app.php')) {
            $this->config = require __DIR__ . '/../Config/app.php';
        }
    }

    public function run()
    {
        // 加载路由
        $this->loadRoutes();
        
        // 如果启用了注解路由，则扫描控制器目录
        if ($this->config['use_attributes_route'] ?? false) {
            $this->loadAttributeRoutes();
        }
        
        // 处理请求
        $this->router->dispatch($this->request);
    }

    private function loadRoutes()
    {
        if (file_exists(__DIR__ . '/../Config/routes.php')) {
            require __DIR__ . '/../Config/routes.php';
        }
    }
    
    /**
     * 加载注解路由
     * 
     * @return void
     */
    private function loadAttributeRoutes()
    {
        $controllerDir = __DIR__ . '/../Controllers';
        
        if (is_dir($controllerDir)) {
            $routeCollector = new AttributeRouteCollector($this->router);
            $routeCollector->collectFromDirectory($controllerDir);
        }
    }

    public function getRouter()
    {
        return $this->router;
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? null;
    }
} 