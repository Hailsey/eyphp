<?php

namespace App\Core;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
        'OPTIONS' => [],
        'HEAD' => [],
        'ANY' => []
    ];

    /**
     * 添加GET路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function get($uri, $handler)
    {
        $this->addRoute('GET', $uri, $handler);
    }

    /**
     * 添加POST路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function post($uri, $handler)
    {
        $this->addRoute('POST', $uri, $handler);
    }
    
    /**
     * 添加PUT路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function put($uri, $handler)
    {
        $this->addRoute('PUT', $uri, $handler);
    }
    
    /**
     * 添加DELETE路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function delete($uri, $handler)
    {
        $this->addRoute('DELETE', $uri, $handler);
    }
    
    /**
     * 添加PATCH路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function patch($uri, $handler)
    {
        $this->addRoute('PATCH', $uri, $handler);
    }
    
    /**
     * 添加OPTIONS路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function options($uri, $handler)
    {
        $this->addRoute('OPTIONS', $uri, $handler);
    }
    
    /**
     * 添加HEAD路由
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function head($uri, $handler)
    {
        $this->addRoute('HEAD', $uri, $handler);
    }
    
    /**
     * 添加任意方法路由(匹配任何HTTP方法)
     * 
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function any($uri, $handler)
    {
        $this->addRoute('ANY', $uri, $handler);
    }
    
    /**
     * 为多个HTTP方法注册相同的路由
     * 
     * @param array $methods HTTP方法数组
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function map(array $methods, $uri, $handler)
    {
        foreach ($methods as $method) {
            $method = strtoupper($method);
            if (isset($this->routes[$method])) {
                $this->addRoute($method, $uri, $handler);
            }
        }
    }

    /**
     * 添加路由
     * 
     * @param string $method 请求方法
     * @param string $uri 路由URI
     * @param string|callable $handler 控制器@方法或回调函数
     * @return void
     */
    public function addRoute($method, $uri, $handler)
    {
        $uri = trim($uri, '/');
        $this->routes[$method][$uri] = $handler;
    }

    /**
     * 分发请求到对应的控制器或回调函数
     * 
     * @param Request $request 请求对象
     * @return mixed
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();
        
        // 检查特定HTTP方法的路由
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            $response = $this->executeHandler($handler);
            
            // 输出控制器返回的内容（如果是字符串）
            if (is_string($response)) {
                echo $response;
            }
            
            return $response;
        }
        
        // 检查ANY方法的路由（匹配所有HTTP方法）
        if (isset($this->routes['ANY'][$uri])) {
            $handler = $this->routes['ANY'][$uri];
            $response = $this->executeHandler($handler);
            
            // 输出控制器返回的内容（如果是字符串）
            if (is_string($response)) {
                echo $response;
            }
            
            return $response;
        }
        
        // 路由未找到
        $this->notFound();
    }

    /**
     * 执行控制器方法或回调函数
     * 
     * @param string|callable $handler 控制器@方法或回调函数
     * @return mixed
     */
    protected function executeHandler($handler)
    {
        $request = App::getInstance()->getRequest();
        
        // 如果是回调函数
        if (is_callable($handler)) {
            // 尝试传递 Request 对象到回调函数
            $reflection = new \ReflectionFunction($handler);
            if ($reflection->getNumberOfParameters() > 0 && 
                ($param = $reflection->getParameters()[0]) && 
                ($paramClass = $param->getType()) && 
                $paramClass->getName() === Request::class) {
                return call_user_func($handler, $request);
            }
            return call_user_func($handler);
        }
        
        // 如果是控制器@方法
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = 'App\\Controllers\\' . $controller;
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }
            
            $controllerInstance = new $controllerClass();
            
            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in controller {$controllerClass}");
            }
            
            // 检查方法参数，如果第一个参数是 Request 类型，则传入请求对象
            $reflection = new \ReflectionMethod($controllerInstance, $method);
            if ($reflection->getNumberOfParameters() > 0 && 
                ($param = $reflection->getParameters()[0]) && 
                ($paramClass = $param->getType()) && 
                $paramClass->getName() === Request::class) {
                return call_user_func([$controllerInstance, $method], $request);
            }
            
            return call_user_func([$controllerInstance, $method]);
        }
        
        throw new \Exception('Invalid route handler');
    }

    /**
     * 处理404 Not Found
     * 
     * @return void
     */
    protected function notFound()
    {
        header('HTTP/1.0 404 Not Found');
        echo '404 Not Found';
        exit;
    }
} 