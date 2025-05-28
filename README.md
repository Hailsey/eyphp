# 轻量级PHP MVC框架

这是一个简洁、轻量级、优雅的PHP MVC框架（EyPHP），适合小型项目快速开发。该框架要求PHP 8.0或更高版本。

## 特性

- 遵循MVC架构设计模式
- 简单直观的路由系统
- 支持PHP 8属性(Attributes)路由定义
- 视图渲染系统
- 轻量级的模型系统，支持基本的CRUD操作
- 完善的请求处理机制
- 清晰的目录结构
- 配置管理
- 基于PSR-4的自动加载

## Composer 安装

### 创建新项目

使用 Composer 快速创建基于 EyPHP 框架的新项目：

```bash
composer create-project eyphp/framework your-project-name
```

## 目录结构

```
eyphp/                           # 项目根目录
├── composer.json                # Composer配置文件
├── composer.lock                # Composer依赖锁定文件
├── index.php                    # 应用入口文件
├── README.md                    # 项目文档
├── public/                      # 公共资源目录
│   └── assets/                  # 静态资源目录
│       └── css/                 # CSS样式文件
└── src/                         # 应用源代码目录
    ├── Config/                  # 配置文件目录
    │   ├── app.php              # 应用主配置
    │   └── routes.php           # 路由配置
    ├── Controllers/             # 控制器目录
    │   └── HomeController.php   # 首页控制器
    ├── Core/                    # 框架核心类
    │   ├── App.php              # 应用主类
    │   ├── AttributeRouteCollector.php # 属性路由收集器
    │   ├── Attributes/          # PHP 8属性定义目录
    │   │   ├── Any.php          # 任意方法属性
    │   │   ├── Delete.php       # DELETE请求属性
    │   │   ├── Get.php          # GET请求属性
    │   │   ├── Post.php         # POST请求属性
    │   │   ├── Put.php          # PUT请求属性
    │   │   └── Route.php        # 路由基本属性
    │   ├── Controller.php       # 控制器基类
    │   ├── Model.php            # 模型基类
    │   ├── Request.php          # 请求处理类
    │   ├── Router.php           # 路由处理类
    │   └── View.php             # 视图渲染类
    ├── Models/                  # 模型目录
    │   └── User.php             # 用户模型
    └── Views/                   # 视图目录
        └── home/                # 首页视图目录
            └── index.php        # 首页视图模板
```

## 系统需求

- PHP 8.0 或更高版本
- PDO扩展 (如需使用数据库功能)
- 支持URL重写的Web服务器（Apache或Nginx）

## 安装

1. 创建新项目

```bash
composer create-project eyphp/framework your-project-name
```

2. 或者克隆项目到您的服务器

```bash
git clone https://github.com/Hailsey/eyphp.git your-project-name
```

3. 进入项目目录，运行Composer安装依赖：

```bash
cd your-project-name
composer install
```

4. 配置Web服务器，将文档根目录指向项目根目录

5. 修改`src/Config/app.php`中的配置，设置您的应用参数和数据库连接信息

6. 访问您的域名或localhost

## 本地开发环境启动

使用PHP内置Web服务器快速启动开发环境：

```bash
cd your-project-name
php -S localhost:8000
```

现在您可以访问 http://localhost:8000 查看应用。

### Web服务器配置

#### Apache配置

创建或修改`.htaccess`文件：

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx配置

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/eyphp;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        index index.php;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 配置文件

### 应用配置

在`src/Config/app.php`中配置应用参数：

```php
<?php

return [
    'app_name' => '轻量级MVC框架',
    'debug' => true,        // 开发环境设为true，生产环境设为false
    'timezone' => 'Asia/Shanghai',
    'charset' => 'UTF-8',
    'base_url' => 'http://localhost:8000',
    
    // 启用属性路由
    'use_attributes_route' => true,
    
    'database' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'mvc_app',  // 数据库名
        'username' => 'root',     // 数据库用户名
        'password' => '',         // 数据库密码
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ]
];
```

## 路由配置

### 传统配置式路由

在`src/Config/routes.php`中配置路由：

```php
// 获取路由实例
$router = App\Core\App::getInstance()->getRouter();

// 添加GET路由
$router->get('home', 'HomeController@index');
$router->get('users', 'UserController@index');

// 添加POST路由
$router->post('user/save', 'UserController@save');

// 添加PUT路由
$router->put('users/{id}', 'UserController@update');

// 添加DELETE路由
$router->delete('users/{id}', 'UserController@delete');

// 多方法路由
$router->map(['GET', 'POST'], 'login', 'AuthController@login');

// 任意方法路由
$router->any('api/endpoint', 'ApiController@handle');
```

### PHP 8属性路由

PHP 8引入了Attributes特性，本框架支持使用属性定义路由：

```php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Attributes\Get;
use App\Core\Attributes\Post;
use App\Core\Attributes\Put;
use App\Core\Attributes\Delete;
use App\Core\Attributes\Any;

class ProductController extends Controller
{
    // 默认匹配 /product
    #[Get]  
    public function index()
    {
        return $this->view('product.index');
    }
    
    // 匹配 GET 请求到 /product/show/{id}
    #[Get('product/show/{id}', 'product.show')]
    public function show(Request $request)
    {
        $id = $request->get('id');
        return $this->view('product.show', ['id' => $id]);
    }
    
    // 匹配 POST 请求到 /product/store
    #[Post('product/store')]
    public function store(Request $request)
    {
        $data = $request->all();
        // 处理数据...
        return $this->redirect('product');
    }
    
    // 匹配 PUT 请求到 /product/update/{id}
    #[Put('product/update/{id}')]
    public function update(Request $request)
    {
        $id = $request->get('id');
        // 更新数据...
        return $this->json(['success' => true]);
    }
    
    // 匹配 DELETE 请求到 /product/delete/{id}
    #[Delete('product/delete/{id}')]
    public function delete(Request $request)
    {
        $id = $request->get('id');
        // 删除数据...
        return $this->json(['success' => true]);
    }
    
    // 匹配任何HTTP方法到 /product/any
    #[Any('product/any')]
    public function any(Request $request)
    {
        $method = $request->getMethod();
        return "当前请求方法: {$method}";
    }
}
```

属性路由使用说明：
- 不指定路径时，默认使用"控制器名(不含Controller)/方法名"生成路由
- 控制器名会自动转为小写，如`ProductController`生成`product`
- 如果方法是`index`，则省略方法名部分
- 可以使用`{参数名}`语法在路由中定义参数
- 属性路由需要在`app.php`中设置`use_attributes_route`为`true`

## 控制器

### 创建控制器

所有控制器应继承`App\Core\Controller`基类：

```php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserController extends Controller
{
    public function index()
    {
        return $this->view('user.index');
    }
    
    public function show(Request $request)
    {
        $id = $request->get('id');
        $user = new \App\Models\User();
        $data = ['user' => $user->find($id)];
        return $this->view('user.show', $data);
    }
    
    public function save(Request $request)
    {
        $userData = $request->only(['name', 'email', 'password']);
        // 保存用户数据...
        
        return $this->redirect('users');
    }
}
```

### 控制器方法

控制器基类提供以下方法：

- `view($viewName, $data)` - 渲染视图
- `json($data, $statusCode)` - 返回JSON响应
- `redirect($url)` - 重定向到指定URL

## Request对象

Request对象是框架的核心组件之一，提供了全面的HTTP请求处理能力。

### 获取Request对象

在控制器方法中使用类型声明来接收Request对象：

```php
public function show(Request $request)
{
    // 框架会自动注入Request对象
}
```

### 获取请求参数

```php
// 获取GET参数
$id = $request->get('id');
$id = $request->get('id', 0); // 带默认值

// 获取POST参数
$name = $request->post('name');

// 获取任意输入(按POST、JSON、GET优先级)
$email = $request->input('email');
$email = $request->input('email', 'default@example.com'); // 带默认值

// 获取所有输入
$allParams = $request->all();

// 只获取特定字段
$credentials = $request->only(['username', 'password']);

// 排除特定字段
$userData = $request->except(['_token', 'submit']);

// 检查参数是否存在
if ($request->has('email')) {
    // 处理email
}
```

### 获取上传文件

```php
// 获取单个上传文件
$file = $request->file('avatar');
if (!empty($file)) {
    $fileName = $file['name'];
    $tmpPath = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    
    // 处理文件上传...
}

// 获取所有上传文件
$allFiles = $request->file();
```

### 获取请求信息

```php
// 获取请求URI
$uri = $request->getUri();

// 获取HTTP方法
$method = $request->getMethod();

// 判断请求方法
if ($request->isGet()) {
    // 处理GET请求
}

if ($request->isPost()) {
    // 处理POST请求
}

if ($request->isPut()) {
    // 处理PUT请求
}

if ($request->isDelete()) {
    // 处理DELETE请求
}

// 判断是否为AJAX请求
if ($request->isAjax()) {
    // 处理AJAX请求
}

// 判断是否期望JSON响应
if ($request->wantsJson()) {
    // 返回JSON响应
}
```

### 获取请求头和客户端信息

```php
// 获取请求头
$userAgent = $request->header('User-Agent');
$contentType = $request->getHeader('Content-Type');

// 获取所有请求头
$headers = $request->header();

// 获取客户端IP
$ip = $request->ip();

// 获取用户代理
$userAgent = $request->userAgent();
```

### JSON请求处理

```php
// 获取整个JSON正文
$jsonData = $request->json();

// 获取JSON中的特定字段
$name = $request->json('name');
$name = $request->json('name', 'default'); // 带默认值
```

### 获取请求体和URI段

```php
// 获取原始请求体
$rawBody = $request->getBody();

// 获取所有URI段
$segments = $request->segments();

// 获取特定位置的URI段
$firstSegment = $request->segment(0);
$secondSegment = $request->segment(1, 'default'); // 带默认值
```

## 模型

所有模型应继承`App\Core\Model`基类，该基类提供了基本的CRUD操作：

```php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected $table = 'products';  // 表名
    protected $primaryKey = 'id';   // 主键名，默认为'id'
    
    // 自定义方法
    public function findActive()
    {
        return $this->where('status', 'active');
    }
    
    // 获取产品并附加折扣信息
    public function getWithDiscount($productId, $discountRate)
    {
        $product = $this->find($productId);
        if ($product) {
            $product['discounted_price'] = $product['price'] * (1 - $discountRate);
        }
        return $product;
    }
}
```

### 模型基类提供的方法

- `all()` - 获取所有记录
- `find($id)` - 根据主键查找记录
- `create($data)` - 创建记录
- `update($id, $data)` - 更新记录
- `delete($id)` - 删除记录
- `where($column, $operator, $value)` - 按条件查询

## 视图

### 基本用法

在控制器中渲染视图：

```php
public function show(Request $request)
{
    $id = $request->get('id');
    $product = new \App\Models\Product();
    $data = [
        'product' => $product->find($id),
        'relatedProducts' => $product->getRelated($id)
    ];
    
    return $this->view('products.show', $data);
}
```

### 视图文件

视图文件放在`src/Views/`目录下，使用点表示法指定嵌套目录：

- `products.show` 对应 `src/Views/products/show.php`
- `admin.products.edit` 对应 `src/Views/admin/products/edit.php`

### 视图示例

```php
<!-- src/Views/user/show.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>用户详情</h1>
        <p>用户名: <?= htmlspecialchars($user['username']) ?></p>
        <p>邮箱: <?= htmlspecialchars($user['email']) ?></p>
        <p>注册时间: <?= date('Y-m-d H:i:s', strtotime($user['created_at'])) ?></p>
    </div>
</body>
</html>
```

## 实际应用示例

### 登录系统实现

1. 添加路由：

```php
// src/Config/routes.php
$router->get('login', 'AuthController@showLogin');
$router->post('login', 'AuthController@login');
$router->get('logout', 'AuthController@logout');
```

2. 创建控制器：

```php
// src/Controllers/AuthController.php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return $this->view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);
        
        // 验证用户
        $user = new User();
        $authenticated = $user->authenticate($credentials);
        
        if ($authenticated) {
            // 设置会话
            $_SESSION['user_id'] = $authenticated['id'];
            return $this->redirect('dashboard');
        }
        
        return $this->view('auth.login', ['error' => '用户名或密码错误']);
    }
    
    public function logout()
    {
        // 清除会话
        session_destroy();
        return $this->redirect('login');
    }
}
```

3. 创建视图：

```php
<!-- src/Views/auth/login.php -->
<!DOCTYPE html>
<html>
<head>
    <title>用户登录</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>用户登录</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="/login" method="post">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">登录</button>
        </form>
    </div>
</body>
</html>
```

4. 实现用户模型：

```php
// src/Models/User.php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    
    /**
     * 验证用户凭证
     * 
     * @param array $credentials 用户凭证
     * @return array|false 用户数据或失败时返回false
     */
    public function authenticate(array $credentials)
    {
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            return false;
        }
        
        $user = $this->where('username', '=', $username)[0] ?? null;
        if (!$user) {
            return false;
        }
        
        // 验证密码 (假设密码已哈希存储)
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        return $user;
    }
}
```

## 最佳实践

1. **路由组织**：按功能领域组织路由，保持路由文件清晰

2. **控制器原则**：
   - 保持控制器轻量
   - 将业务逻辑放在模型或服务类中
   - 每个控制器方法专注于单一职责

3. **Request对象使用**：
   - 总是使用类型提示注入Request对象
   - 使用`only()`和`except()`方法过滤输入
   - 在处理前验证用户输入

4. **数据库操作**：
   - 使用模型的CRUD方法进行数据操作
   - 使用事务处理复杂操作
   - 针对复杂查询，使用自定义方法封装SQL

5. **安全考虑**：
   - 始终对输出进行HTML转义以防XSS攻击
   - 使用参数化查询防止SQL注入
   - 验证所有用户输入
   - 在生产环境关闭调试模式

6. **配置管理**：
   - 敏感信息（如数据库密码）应使用环境变量
   - 不同环境使用不同配置

7. **错误处理**：
   - 利用框架的异常处理机制
   - 生产环境显示友好错误，隐藏技术细节
   - 记录错误日志便于排查问题

## 许可证

MIT 