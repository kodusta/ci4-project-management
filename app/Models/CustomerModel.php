<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'address',
        'note'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $deletedField = '';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'email' => 'permit_empty|valid_email|is_unique[customers.email,id,{id}]',
        'phone' => 'permit_empty|min_length[10]|max_length[20]',
        'address' => 'permit_empty',
        'note' => 'permit_empty'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Müşteri adı zorunludur',
            'min_length' => 'Müşteri adı en az 3 karakter olmalıdır',
            'max_length' => 'Müşteri adı en fazla 100 karakter olabilir'
        ],
        'email' => [
            'valid_email' => 'Geçerli bir e-posta adresi giriniz',
            'is_unique' => 'Bu e-posta adresi zaten kullanılıyor'
        ],
        'phone' => [
            'min_length' => 'Telefon numarası en az 10 karakter olmalıdır',
            'max_length' => 'Telefon numarası en fazla 20 karakter olabilir'
        ]
    ];

    protected $skipValidation = false;

    public function getTopCustomers($limit = 5)
    {
        return $this->select('customers.*, COUNT(projects.id) as project_count, SUM(projects.total_amount) as total_amount')
                    ->join('projects', 'projects.customer_id = customers.id', 'left')
                    ->groupBy('customers.id')
                    ->orderBy('project_count', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getCustomerProjectStats($customerId)
    {
        return $this->select('customers.*, 
                            COUNT(projects.id) as total_projects,
                            SUM(CASE WHEN projects.status = "Tamamlandı" THEN 1 ELSE 0 END) as completed_projects,
                            SUM(projects.total_amount) as total_amount,
                            SUM(projects.paid_amount) as paid_amount')
                    ->join('projects', 'projects.customer_id = customers.id', 'left')
                    ->where('customers.id', $customerId)
                    ->groupBy('customers.id')
                    ->first();
    }
}
