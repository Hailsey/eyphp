<?php

namespace App\Core;

class Request
{
    protected $get;
    protected $post;
    protected $server;
    protected $cookie;
    protected $files;
    protected $uri;
    protected $method;
    protected $headers;
    protected $body;
    protected $json;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->cookie = $_COOKIE;
        $this->files = $_FILES;
        $this->uri = $this->parseUri();
        $this->method = $this->server['REQUEST_METHOD'] ?? 'GET';
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
        $this->json = $this->parseJson();
    }

    protected function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        
        return trim($uri, '/');
    }

    protected function parseHeaders()
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerKey = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$headerKey] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerKey = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                $headers[$headerKey] = $value;
            }
        }
        
        return $headers;
    }

    protected function parseBody()
    {
        $rawBody = file_get_contents('php://input');
        return $rawBody;
    }

    protected function parseJson()
    {
        if ($this->getHeader('Content-Type') === 'application/json') {
            return json_decode($this->body, true) ?? [];
        }
        return [];
    }

    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }
        
        return $this->get[$key] ?? $default;
    }

    public function post($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        
        return $this->post[$key] ?? $default;
    }

    public function cookie($key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookie;
        }
        
        return $this->cookie[$key] ?? $default;
    }

    public function file($key = null)
    {
        if ($key === null) {
            return $this->files;
        }
        
        return $this->files[$key] ?? null;
    }

    public function json($key = null, $default = null)
    {
        if ($key === null) {
            return $this->json;
        }
        
        return $this->json[$key] ?? $default;
    }

    public function input($key = null, $default = null)
    {
        // 按优先级搜索：POST, JSON, GET
        if ($key === null) {
            return array_merge($this->get, $this->json, $this->post);
        }
        
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        
        if (isset($this->json[$key])) {
            return $this->json[$key];
        }
        
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
        
        return $default;
    }

    public function all()
    {
        return array_merge($this->get, $this->post, $this->json);
    }

    public function only(array $keys)
    {
        $results = [];
        
        foreach ($keys as $key) {
            $results[$key] = $this->input($key);
        }
        
        return $results;
    }

    public function except(array $keys)
    {
        $results = $this->all();
        
        foreach ($keys as $key) {
            unset($results[$key]);
        }
        
        return $results;
    }

    public function has($key)
    {
        return $this->input($key) !== null;
    }

    public function header($key = null, $default = null)
    {
        if ($key === null) {
            return $this->headers;
        }
        
        // 处理大小写不敏感
        foreach ($this->headers as $headerKey => $value) {
            if (strtolower($headerKey) === strtolower($key)) {
                return $value;
            }
        }
        
        return $default;
    }

    public function getHeader($key = null, $default = null)
    {
        return $this->header($key, $default);
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function isMethod($method)
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function isGet()
    {
        return $this->isMethod('GET');
    }

    public function isPost()
    {
        return $this->isMethod('POST');
    }

    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function wantsJson()
    {
        return strpos($this->header('Accept', ''), 'application/json') !== false;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getServerParam($key, $default = null)
    {
        return $this->server[$key] ?? $default;
    }

    public function ip()
    {
        return $this->getServerParam('REMOTE_ADDR');
    }

    public function userAgent()
    {
        return $this->getServerParam('HTTP_USER_AGENT');
    }

    public function segments()
    {
        return explode('/', $this->uri);
    }
    
    public function segment($index, $default = null)
    {
        $segments = $this->segments();
        return $segments[$index] ?? $default;
    }
} 