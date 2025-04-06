<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProjectModel;

class Customers extends BaseController
{
    protected $customerModel;
    protected $projectModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Müşteriler',
            'customers' => $this->customerModel->findAll()
        ];

        return view('customers/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Yeni Müşteri Ekle'
        ];

        return view('customers/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'permit_empty|valid_email|is_unique[customers.email]',
            'phone' => 'permit_empty|min_length[10]',
            'address' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];

        $this->customerModel->insert($data);
        return redirect()->to('/customers')->with('success', 'Müşteri başarıyla eklendi.');
    }

    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('/customers');
        }

        $customer = $this->customerModel->find($id);
        
        if ($customer === null) {
            return redirect()->to('/customers')->with('error', 'Müşteri bulunamadı.');
        }

        $data = [
            'title' => 'Müşteri Düzenle',
            'customer' => $customer
        ];

        return view('customers/edit', $data);
    }

    public function update($id = null)
    {
        if ($id === null) {
            return redirect()->to('/customers');
        }

        $customer = $this->customerModel->find($id);
        
        if ($customer === null) {
            return redirect()->to('/customers')->with('error', 'Müşteri bulunamadı.');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'permit_empty|valid_email|is_unique[customers.email,id,' . $id . ']',
            'phone' => 'permit_empty|min_length[10]',
            'address' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];

        $this->customerModel->update($id, $data);
        return redirect()->to('/customers')->with('success', 'Müşteri başarıyla güncellendi.');
    }

    public function delete($id = null)
    {
        if ($id === null) {
            return redirect()->to('/customers');
        }

        $customer = $this->customerModel->find($id);
        
        if ($customer === null) {
            return redirect()->to('/customers')->with('error', 'Müşteri bulunamadı.');
        }

        $this->customerModel->delete($id);
        return redirect()->to('/customers')->with('success', 'Müşteri başarıyla silindi.');
    }
    
    /**
     * Müşteriye ait projeleri listeler
     */
    public function projects($id = null)
    {
        if ($id === null) {
            return redirect()->to('/customers');
        }

        $customer = $this->customerModel->find($id);
        
        if ($customer === null) {
            return redirect()->to('/customers')->with('error', 'Müşteri bulunamadı.');
        }

        $projects = $this->projectModel->getCustomerProjects($id);
        
        // Son yapılan projeyi bul
        $lastProject = null;
        if (!empty($projects)) {
            // Projeleri oluşturulma tarihine göre sırala (en yeni en üstte)
            usort($projects, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            $lastProject = $projects[0];
        }
        
        $data = [
            'title' => $customer['name'] . ' - Projeler',
            'customer' => $customer,
            'projects' => $projects,
            'lastProject' => $lastProject
        ];

        return view('customers/projects', $data);
    }
}
