<?php

namespace App\Core\Attributes;

use Attribute;

/**
 * PUT方法路由注解
 * 用于在控制器方法上定义PUT请求路由
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Put extends Route
{
    /**
     * 构造函数
     * 
     * @param string $uri 路由URI
     * @param string|null $name 路由名称
     * @param array $middleware 中间件列表
     */
    public function __construct(
        string $uri = '', 
        ?string $name = null, 
        array $middleware = []
    ) {
        parent::__construct('PUT', $uri, $name, $middleware);
    }
} 