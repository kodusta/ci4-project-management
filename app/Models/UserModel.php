<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['username', 'password', 'email'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    protected $validationRules = [
        'username' => 'required|min_length[3]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Kullanıcı adı zorunludur',
            'min_length' => 'Kullanıcı adı en az 3 karakter olmalıdır',
            'is_unique' => 'Bu kullanıcı adı zaten kullanılıyor'
        ],
        'email' => [
            'required' => 'E-posta adresi zorunludur',
            'valid_email' => 'Geçerli bir e-posta adresi giriniz',
            'is_unique' => 'Bu e-posta adresi zaten kullanılıyor'
        ],
        'password' => [
            'required' => 'Şifre zorunludur',
            'min_length' => 'Şifre en az 6 karakter olmalıdır'
        ]
    ];

    protected $skipValidation = false;

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }
}
