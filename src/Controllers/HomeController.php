<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Attributes\Get;
use App\Core\Attributes\Post;
use App\Core\Attributes\Route;

class HomeController extends Controller
{
    /**
     * 首页方法
     * 
     * @return string
     */
    #[Get('/', 'home.index')]
    public function index()
    {
        $data = [
            'title' => '欢迎使用轻量级MVC框架',
            'content' => '这是一个简洁、轻量级、优雅的MVC框架',
            'features' => [
                '简单路由系统',
                'MVC架构',
                '视图渲染',
                '配置管理',
                'PHP 8注解路由'
            ]
        ];
        return $this->view('home.index', $data);
    }
}
