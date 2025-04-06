<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class Categories extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $categories = $this->categoryModel->findAll();
        
        // Her kategori için proje sayısını hesapla
        foreach ($categories as &$category) {
            $category['project_count'] = $this->categoryModel->getProjectCount($category['id']);
        }

        $data = [
            'title' => 'Kategoriler',
            'categories' => $categories
        ];

        return view('categories/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Yeni Kategori'
        ];

        return view('categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]|is_unique[categories.name]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('categories')->with('message', 'Kategori başarıyla eklendi.');
        }

        return redirect()->back()->withInput()->with('error', 'Kategori eklenirken bir hata oluştu.');
    }

    public function edit($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('categories')->with('error', 'Kategori bulunamadı.');
        }

        $data = [
            'title' => 'Kategori Düzenle',
            'category' => $category
        ];

        return view('categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('categories')->with('error', 'Kategori bulunamadı.');
        }

        $rules = [
            'name' => "required|min_length[3]|max_length[100]|is_unique[categories.name,id,$id]",
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return redirect()->to('categories')->with('message', 'Kategori başarıyla güncellendi.');
        }

        return redirect()->back()->withInput()->with('error', 'Kategori güncellenirken bir hata oluştu.');
    }

    public function delete($id)
    {
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return redirect()->to('categories')->with('error', 'Kategori bulunamadı.');
        }

        // Kategoriye ait proje var mı kontrol et
        $projectCount = $this->categoryModel->getProjectCount($id);
        if ($projectCount > 0) {
            return redirect()->to('categories')->with('error', 'Bu kategoriye ait projeler bulunduğu için silinemez.');
        }

        if ($this->categoryModel->delete($id)) {
            return redirect()->to('categories')->with('message', 'Kategori başarıyla silindi.');
        }

        return redirect()->to('categories')->with('error', 'Kategori silinirken bir hata oluştu.');
    }
} 