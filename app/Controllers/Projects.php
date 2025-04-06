<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CustomerModel;
use App\Models\ProjectNoteModel;
use App\Models\CategoryModel;

class Projects extends BaseController
{
    protected $projectModel;
    protected $customerModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->customerModel = new CustomerModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Projeler',
            'activeProjects' => $this->projectModel->select('projects.*, customers.name as customer_name, categories.name as category_name')
                                                 ->join('customers', 'customers.id = projects.customer_id')
                                                 ->join('categories', 'categories.id = projects.category_id', 'left')
                                                 ->where('projects.status !=', 'Tamamlandı')
                                                 ->orderBy('(projects.total_amount <= projects.paid_amount)', 'DESC')
                                                 ->orderBy('FIELD(projects.priority, "Acil", "Yüksek", "Orta", "Düşük")', '')
                                                 ->orderBy('projects.created_at', 'DESC')
                                                 ->findAll(),
            'completedProjects' => $this->projectModel->select('projects.*, customers.name as customer_name, categories.name as category_name')
                                                    ->join('customers', 'customers.id = projects.customer_id')
                                                    ->join('categories', 'categories.id = projects.category_id', 'left')
                                                    ->where('projects.status', 'Tamamlandı')
                                                    ->orderBy('projects.end_date', 'DESC')
                                                    ->findAll()
        ];

        return view('projects/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Yeni Proje',
            'customers' => $this->customerModel->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'statusList' => $this->projectModel->getStatusList(),
            'priorityList' => $this->projectModel->getPriorityList()
        ];

        return view('projects/create', $data);
    }

    public function store()
    {
        if (!$this->validate($this->projectModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'task' => $this->request->getPost('task'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'total_amount' => $this->request->getPost('total_amount'),
            'paid_amount' => $this->request->getPost('paid_amount') ?? 0
        ];

        if ($this->projectModel->insert($data)) {
            return redirect()->to('/projects')->with('message', 'Proje başarıyla oluşturuldu.');
        }

        return redirect()->back()->withInput()->with('error', 'Proje oluşturulurken bir hata oluştu.');
    }

    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('/projects');
        }

        $project = $this->projectModel->find($id);
        if ($project === null) {
            return redirect()->to('/projects')->with('error', 'Proje bulunamadı.');
        }

        $data = [
            'title' => 'Proje Düzenle',
            'project' => $project,
            'customers' => $this->customerModel->findAll(),
            'categories' => $this->categoryModel->findAll(),
            'statusList' => $this->projectModel->getStatusList(),
            'priorityList' => $this->projectModel->getPriorityList()
        ];

        return view('projects/edit', $data);
    }

    public function update($id = null)
    {
        if ($id === null) {
            return redirect()->to('/projects');
        }

        if (!$this->validate($this->projectModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'task' => $this->request->getPost('task'),
            'priority' => $this->request->getPost('priority'),
            'status' => $this->request->getPost('status'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'total_amount' => $this->request->getPost('total_amount'),
            'paid_amount' => $this->request->getPost('paid_amount') ?? 0
        ];

        if ($this->projectModel->update($id, $data)) {
            return redirect()->to('/projects')->with('message', 'Proje başarıyla güncellendi.');
        }

        return redirect()->back()->withInput()->with('error', 'Proje güncellenirken bir hata oluştu.');
    }

    public function delete($id = null)
    {
        if ($id === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Geçersiz proje ID.'
                ]);
            }
            return redirect()->to('/projects');
        }

        if ($this->projectModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Proje başarıyla silindi.'
                ]);
            }
            return redirect()->to('/projects')->with('message', 'Proje başarıyla silindi.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Proje silinirken bir hata oluştu.'
            ]);
        }
        return redirect()->to('/projects')->with('error', 'Proje silinirken bir hata oluştu.');
    }

    /**
     * AJAX ile proje alanlarını güncelleme
     */
    public function updateField()
    {
        // AJAX isteği kontrolü
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Geçersiz istek türü.'
            ]);
        }

        // JSON verilerini al
        $json = $this->request->getJSON();
        
        // Gerekli alanları kontrol et
        if (!isset($json->project_id) || !isset($json->field) || !isset($json->value)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Eksik parametreler.'
            ]);
        }

        $projectId = $json->project_id;
        $field = $json->field;
        $value = $json->value;

        // İzin verilen alanları kontrol et
        $allowedFields = ['priority', 'status'];
        if (!in_array($field, $allowedFields)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'İzin verilmeyen alan.'
            ]);
        }

        // Projeyi bul
        $projectModel = new ProjectModel();
        $project = $projectModel->find($projectId);

        if (!$project) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Proje bulunamadı.'
            ]);
        }

        // Değerleri doğrula
        if ($field === 'priority') {
            $allowedPriorities = ['Düşük', 'Orta', 'Yüksek', 'Acil'];
            if (!in_array($value, $allowedPriorities)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Geçersiz öncelik değeri.'
                ]);
            }
        } elseif ($field === 'status') {
            $allowedStatuses = ['Başlamadı', 'Devam Ediyor', 'Beklemede', 'Ödeme Bekliyor', 'Tamamlandı'];
            if (!in_array($value, $allowedStatuses)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Geçersiz durum değeri.'
                ]);
            }
        }

        // Güncelleme işlemi
        $data = [$field => $value];
        $updated = $projectModel->update($projectId, $data);

        if ($updated) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Proje başarıyla güncellendi.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Güncelleme sırasında bir hata oluştu.'
            ]);
        }
    }

    public function view($id = null)
    {
        if ($id === null) {
            return redirect()->to('/projects');
        }

        $project = $this->projectModel->getProjectWithDetails($id);
        if ($project === null) {
            return redirect()->to('/projects')->with('error', 'Proje bulunamadı.');
        }

        // Son 3 proje notunu getir
        $noteModel = new ProjectNoteModel();
        $recentNotes = $noteModel->where('project_id', $id)
                                ->orderBy('created_at', 'DESC')
                                ->limit(3)
                                ->find();

        $data = [
            'title' => 'Proje Detayı',
            'project' => $project,
            'recentNotes' => $recentNotes
        ];

        return view('projects/view', $data);
    }

    public function notes($projectId)
    {
        $projectModel = new ProjectModel();
        $noteModel = new ProjectNoteModel();
        
        $project = $projectModel->find($projectId);
        if (!$project) {
            return redirect()->to('/projects')->with('error', 'Proje bulunamadı.');
        }

        $notes = $noteModel->getProjectNotes($projectId);
        
        return view('projects/notes', [
            'project' => $project,
            'notes' => $notes
        ]);
    }

    public function addNote()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Geçersiz istek.']);
        }

        $noteModel = new ProjectNoteModel();
        
        $projectId = $this->request->getPost('project_id');
        $note = $this->request->getPost('note');

        if ($noteModel->addNote($projectId, $note)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Not başarıyla eklendi.'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Not eklenirken bir hata oluştu.'
        ]);
    }

    public function deleteNote($noteId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Geçersiz istek.']);
        }

        $noteModel = new ProjectNoteModel();
        
        if ($noteModel->deleteNote($noteId)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Not başarıyla silindi.'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Not silinirken bir hata oluştu.'
        ]);
    }

    public function updatePriority()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $json = $this->request->getJSON();
        $projectId = $json->project_id;
        $newPriority = $json->priority;

        if (!$projectId || !$newPriority) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing required fields']);
        }

        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Project not found']);
        }

        if ($this->projectModel->update($projectId, ['priority' => $newPriority])) {
            $colorClass = getPrioritySelectClass($newPriority);
            $rowClass = getPriorityRowClass($newPriority);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Öncelik başarıyla güncellendi',
                'colorClass' => $colorClass,
                'rowClass' => $rowClass
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Öncelik güncellenirken bir hata oluştu']);
    }

    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $json = $this->request->getJSON();
        $projectId = $json->project_id;
        $newStatus = $json->status;

        if (!$projectId || !$newStatus) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing required fields']);
        }

        $project = $this->projectModel->find($projectId);
        if (!$project) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Project not found']);
        }

        if ($this->projectModel->update($projectId, ['status' => $newStatus])) {
            $colorClass = getStatusSelectClass($newStatus);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Durum başarıyla güncellendi',
                'colorClass' => $colorClass
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Durum güncellenirken bir hata oluştu']);
    }
}
