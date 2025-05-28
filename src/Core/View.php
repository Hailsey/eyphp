<?php

namespace App\Core;

class View
{
    protected $viewPath;
    
    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../Views/';
    }
    
    /**
     * 渲染视图
     * 
     * @param string $view 视图名称
     * @param array $data 传递给视图的数据
     * @return string
     */
    public function render($view, $data = [])
    {
        $viewFile = $this->viewPath . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View {$view} not found");
        }
        
        // 提取数据到变量
        extract($data);
        
        // 启动输出缓冲
        ob_start();
        
        try {
            // 引入视图文件
            include $viewFile;
            
            // 获取缓冲内容并清除缓冲
            return ob_get_clean();
        } catch (\Throwable $e) {
            // PHP 7+ 使用\Throwable，确保在出错时清除缓冲区
            ob_end_clean();
            throw $e;
        }
    }
    
    /**
     * 设置视图路径
     * 
     * @param string $path 视图文件路径
     * @return $this
     */
    public function setViewPath($path)
    {
        $this->viewPath = rtrim($path, '/') . '/';
        return $this;
    }
}