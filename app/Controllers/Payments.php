<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\ProjectModel;

class Payments extends BaseController
{
    protected $paymentModel;
    protected $projectModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Ödemeler',
            'payments' => $this->paymentModel->getRecentPayments()
        ];

        return view('payments/index', $data);
    }

    public function create()
    {
        $projectId = $this->request->getGet('project_id');
        $selectedProject = null;
        
        if ($projectId) {
            $selectedProject = $this->projectModel->find($projectId);
            if (!$selectedProject) {
                return redirect()->to('payments/create')->with('error', 'Belirtilen proje bulunamadı.');
            }
        }
        
        $data = [
            'title' => 'Yeni Ödeme',
            'projects' => $this->projectModel->where('status !=', 'Tamamlandı')->findAll(),
            'selectedProject' => $selectedProject
        ];

        return view('payments/create', $data);
    }

    public function store()
    {
        if (!$this->validate($this->paymentModel->validationRules, $this->paymentModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'amount' => $this->request->getPost('amount'),
            'payment_date' => $this->request->getPost('payment_date'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->paymentModel->insert($data)) {
            // Projenin ödenen tutarını güncelle
            $project = $this->projectModel->find($data['project_id']);
            $newPaidAmount = $project['paid_amount'] + $data['amount'];
            $this->projectModel->update($data['project_id'], ['paid_amount' => $newPaidAmount]);

            return redirect()->to('payments')->with('message', 'Ödeme başarıyla kaydedildi.');
        }

        return redirect()->back()->withInput()->with('error', 'Ödeme kaydedilirken bir hata oluştu.');
    }

    public function edit($id)
    {
        $payment = $this->paymentModel->getPaymentDetails($id);
        
        if (!$payment) {
            return redirect()->to('payments')->with('error', 'Ödeme bulunamadı.');
        }

        $data = [
            'title' => 'Ödeme Düzenle',
            'payment' => $payment,
            'projects' => $this->projectModel->where('status !=', 'Tamamlandı')->findAll()
        ];

        return view('payments/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate($this->paymentModel->validationRules, $this->paymentModel->validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Mevcut ödemeyi al
        $oldPayment = $this->paymentModel->find($id);
        if (!$oldPayment) {
            return redirect()->to('payments')->with('error', 'Ödeme bulunamadı.');
        }

        $data = [
            'project_id' => $this->request->getPost('project_id'),
            'amount' => $this->request->getPost('amount'),
            'payment_date' => $this->request->getPost('payment_date'),
            'description' => $this->request->getPost('description')
        ];

        // Eğer proje değiştiyse, eski projenin ödenen tutarını güncelle
        if ($oldPayment['project_id'] != $data['project_id']) {
            $oldProject = $this->projectModel->find($oldPayment['project_id']);
            $newPaidAmount = $oldProject['paid_amount'] - $oldPayment['amount'];
            $this->projectModel->update($oldPayment['project_id'], ['paid_amount' => $newPaidAmount]);

            // Yeni projenin ödenen tutarını güncelle
            $newProject = $this->projectModel->find($data['project_id']);
            $newPaidAmount = $newProject['paid_amount'] + $data['amount'];
            $this->projectModel->update($data['project_id'], ['paid_amount' => $newPaidAmount]);
        } else {
            // Aynı proje için tutar farkını hesapla ve güncelle
            $project = $this->projectModel->find($data['project_id']);
            $amountDiff = $data['amount'] - $oldPayment['amount'];
            $newPaidAmount = $project['paid_amount'] + $amountDiff;
            $this->projectModel->update($data['project_id'], ['paid_amount' => $newPaidAmount]);
        }

        // Ödemeyi güncelle
        if ($this->paymentModel->update($id, $data)) {
            return redirect()->to('payments')->with('message', 'Ödeme başarıyla güncellendi.');
        }

        return redirect()->back()->withInput()->with('error', 'Ödeme güncellenirken bir hata oluştu.');
    }

    public function delete($id)
    {
        $payment = $this->paymentModel->find($id);
        
        if (!$payment) {
            return redirect()->to('payments')->with('error', 'Ödeme bulunamadı.');
        }

        // Projenin ödenen tutarını güncelle
        $project = $this->projectModel->find($payment['project_id']);
        $newPaidAmount = $project['paid_amount'] - $payment['amount'];
        $this->projectModel->update($payment['project_id'], ['paid_amount' => $newPaidAmount]);

        // Eğer ödeme silindiyse ve proje tamamlandı olarak işaretlenmişse, durumu güncelle
        if ($project['status'] === 'Tamamlandı' && $newPaidAmount < $project['total_amount']) {
            $this->projectModel->update($payment['project_id'], ['status' => 'Ödeme Bekliyor']);
        }

        if ($this->paymentModel->delete($id)) {
            return redirect()->to('payments')->with('message', 'Ödeme başarıyla silindi.');
        }

        return redirect()->back()->with('error', 'Ödeme silinirken bir hata oluştu.');
    }

    public function projectPayments($projectId)
    {
        $data = [
            'title' => 'Proje Ödemeleri',
            'payments' => $this->paymentModel->getProjectPayments($projectId),
            'project' => $this->projectModel->find($projectId)
        ];

        return view('payments/project_payments', $data);
    }
}


