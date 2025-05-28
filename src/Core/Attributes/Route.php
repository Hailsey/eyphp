<?php

namespace App\Core\Attributes;

use Attribute;

/**
 * 路由注解
 * 用于在控制器方法上定义路由
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Route
{
    /**
     * @var string 请求方法 (GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD, ANY)
     */
    private string $method;
    
    /**
     * @var string 路由URI
     */
    private string $uri;
    
    /**
     * @var string 路由名称（可选）
     */
    private ?string $name;
    
    /**
     * @var array 中间件列表（可选）
     */
    private array $middleware;
    
    /**
     * 构造函数
     * 
     * @param string $method 请求方法
     * @param string $uri 路由URI
     * @param string|null $name 路由名称
     * @param array $middleware 中间件列表
     */
    public function __construct(
        string $method = 'GET', 
        string $uri = '', 
        ?string $name = null, 
        array $middleware = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->name = $name;
        $this->middleware = $middleware;
    }
    
    /**
     * 获取请求方法
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * 获取路由URI
     * 
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }
    
    /**
     * 获取路由名称
     * 
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    
    /**
     * 获取中间件列表
     * 
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }
} 