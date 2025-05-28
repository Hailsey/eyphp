<?php

namespace App\Core;

abstract class Controller
{
    protected $request;
    protected $app;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->request = $this->app->getRequest();
    }

    /**
     * 渲染视图
     * 
     * @param string $view 视图名称
     * @param array $data 传递给视图的数据
     * @return string
     */
    protected function view($view, $data = [])
    {
        $viewRenderer = new View();
        return $viewRenderer->render($view, $data);
    }

    /**
     * 返回JSON响应
     * 
     * @param mixed $data 要转换为JSON的数据
     * @param int $statusCode HTTP状态码
     * @return void
     */
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * 重定向到指定URL
     * 
     * @param string $url 重定向目标URL
     * @return void
     */
    protected function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
} 