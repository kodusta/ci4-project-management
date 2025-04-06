<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'description'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name,id,{id}]',
        'description' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Kategori adı zorunludur.',
            'min_length' => 'Kategori adı en az 3 karakter olmalıdır.',
            'max_length' => 'Kategori adı en fazla 100 karakter olmalıdır.',
            'is_unique' => 'Bu kategori adı zaten kullanılıyor.'
        ],
        'description' => [
            'max_length' => 'Açıklama en fazla 500 karakter olmalıdır.'
        ]
    ];

    protected $skipValidation = false;

    public function getProjectCount($categoryId)
    {
        return $this->db->table('projects')
            ->where('category_id', $categoryId)
            ->countAllResults();
    }
} 