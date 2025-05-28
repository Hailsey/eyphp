<?php

namespace App\Core;

use App\Core\Attributes\Route;
use ReflectionClass;
use ReflectionMethod;
use ReflectionAttribute;

/**
 * 路由注解收集器
 * 用于收集控制器中的路由注解并注册到路由系统
 */
class AttributeRouteCollector
{
    /**
     * @var Router 路由实例
     */
    private Router $router;
    
    /**
     * 构造函数
     * 
     * @param Router $router 路由实例
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * 从指定控制器中收集路由注解
     * 
     * @param string|array $controllers 控制器类名或类名数组
     * @return void
     */
    public function collectFromControllers($controllers)
    {
        if (!is_array($controllers)) {
            $controllers = [$controllers];
        }
        
        foreach ($controllers as $controller) {
            $this->collectFromController($controller);
        }
    }
    
    /**
     * 从指定目录下的所有控制器收集路由注解
     * 
     * @param string $directory 控制器目录
     * @param string $namespace 控制器命名空间
     * @return void
     */
    public function collectFromDirectory(string $directory, string $namespace = 'App\\Controllers')
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException("目录 {$directory} 不存在");
        }
        
        $files = scandir($directory);
        foreach ($files as $file) {
            // 跳过目录和非PHP文件
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) {
                continue;
            }
            
            // 获取控制器类名
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fullyQualifiedClassName = $namespace . '\\' . $className;
            
            // 如果类存在且是Controller的子类，则收集路由
            if (class_exists($fullyQualifiedClassName) && is_subclass_of($fullyQualifiedClassName, Controller::class)) {
                $this->collectFromController($fullyQualifiedClassName);
            }
        }
    }
    
    /**
     * 从单个控制器收集路由注解
     * 
     * @param string $controller 控制器类名
     * @return void
     */
    private function collectFromController(string $controller)
    {
        // 使用反射获取控制器类信息
        $reflection = new ReflectionClass($controller);
        
        // 获取控制器短名称（不带命名空间）
        $controllerName = $reflection->getShortName();
        
        // 遍历控制器的所有公共方法
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // 跳过继承的方法
            if ($method->getDeclaringClass()->getName() !== $controller) {
                continue;
            }
            
            // 获取方法上的所有Route注解
            $attributes = $method->getAttributes(
                Route::class,
                ReflectionAttribute::IS_INSTANCEOF
            );
            
            if (empty($attributes)) {
                continue;
            }
            
            // 处理方法上的每个Route注解
            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                
                // 获取路由URI
                $uri = $route->getUri();
                
                // 如果URI为空，则使用控制器名和方法名生成默认URI
                if (empty($uri)) {
                    // 如果控制器名称以"Controller"结尾，则移除
                    $prefix = str_ends_with($controllerName, 'Controller')
                        ? substr($controllerName, 0, -10)
                        : $controllerName;
                    
                    // 转换为小写，生成默认路由
                    $prefix = strtolower($prefix);
                    $methodName = strtolower($method->getName());
                    
                    // 如果方法是index，则不添加到URI中
                    $uri = $methodName === 'index' ? $prefix : $prefix . '/' . $methodName;
                }
                
                // 注册路由到路由系统
                $handler = $controllerName . '@' . $method->getName();
                $this->router->addRoute($route->getMethod(), $uri, $handler);
            }
        }
    }
} 