<?php

namespace App\Core;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $connection;
    protected $app;
    
    public function __construct()
    {
        $this->app = App::getInstance();
        $this->connect();
    }
    
    /**
     * 连接数据库
     * 
     * @return void
     */
    protected function connect()
    {
        $config = $this->app->getConfig('database');
        
        if (!$config) {
            throw new \Exception('数据库配置不存在');
        }
        
        try {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );
            
            $this->connection = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception('数据库连接失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 执行查询
     * 
     * @param string $sql SQL查询语句
     * @param array $params 绑定参数
     * @return \PDOStatement
     */
    protected function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * 查询所有记录
     * 
     * @return array
     */
    public function all()
    {
        $stmt = $this->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }
    
    /**
     * 根据ID查询记录
     * 
     * @param int $id 记录ID
     * @return array|null
     */
    public function find($id)
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1",
            [$id]
        );
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * 插入记录
     * 
     * @param array $data 要插入的数据
     * @return int 最后插入ID
     */
    public function create($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }
    
    /**
     * 更新记录
     * 
     * @param int $id 记录ID
     * @param array $data 要更新的数据
     * @return int 受影响行数
     */
    public function update($id, $data)
    {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s = ?",
            $this->table,
            implode(', ', $set),
            $this->primaryKey
        );
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->query($sql, $values);
        return $stmt->rowCount();
    }
    
    /**
     * 删除记录
     * 
     * @param int $id 记录ID
     * @return int 受影响行数
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->query($sql, [$id]);
        return $stmt->rowCount();
    }
    
    /**
     * 根据条件查询
     * 
     * @param string $column 列名
     * @param string $operator 操作符
     * @param mixed $value 值
     * @return array
     */
    public function where($column, $operator, $value = null)
    {
        // 如果只传递了两个参数，则假设操作符为等号
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?";
        $stmt = $this->query($sql, [$value]);
        return $stmt->fetchAll();
    }
} 