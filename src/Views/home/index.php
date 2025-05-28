<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        ul {
            margin-top: 15px;
        }
        li {
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        
        <p><?= $content ?></p>
        
        <h3>框架特性：</h3>
        <ul>
            <?php foreach ($features as $feature): ?>
                <li><?= $feature ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <div class="footer">
        <p>Copyright &copy; <?= date('Y') ?> 轻量级MVC框架</p>
    </div>
</body>
</html> 