<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    /**
     * 根据用户名查找用户
     * 
     * @param string $username 用户名
     * @return array|null
     */
    public function findByUsername($username)
    {
        $users = $this->where('username', $username);
        return !empty($users) ? $users[0] : null;
    }
    
    /**
     * 验证用户密码
     * 
     * @param string $password 明文密码
     * @param string $hash 哈希密码
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    /**
     * 创建新用户
     * 
     * @param array $data 用户数据
     * @return int
     */
    public function register($data)
    {
        // 加密密码
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // 添加创建时间
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
} 