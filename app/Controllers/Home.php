<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\CustomerModel;
use App\Models\PaymentModel;

class Home extends BaseController
{
    protected $projectModel;
    protected $customerModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->customerModel = new CustomerModel();
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        // Yıl ve ay parametrelerini al
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');

        $data = [
            'title' => 'Dashboard',
            'years' => $this->getYears(),
            'months' => $this->getMonths(),
            'year' => $year,
            'month' => $month,
            'metrics' => $this->getMetrics(),
            'recentProjects' => $this->getRecentProjects(),
            'topCustomers' => $this->getTopCustomers(),
            'monthlyStats' => $this->getMonthlyStats($year, $month),
            'yearlyStats' => $this->getYearlyStats($year),
            'activeProjects' => $this->getActiveProjects(),
            'budgetOverrunAnalysis' => $this->getBudgetOverrunAnalysis(),
            'profitMarginAnalysis' => $this->getProfitMarginAnalysis(),
            'kpiAnalysis' => $this->getKPIAnalysis(),
            'customerAnalysis' => $this->getCustomerAnalysis(),
            'predictive_analysis' => $this->getPredictiveAnalysis()
        ];

        return view('home/index', $data);
    }

    private function getYears()
    {
        $currentYear = date('Y');
        $years = [];
        for ($i = 0; $i < 5; $i++) {
            $years[] = $currentYear - $i;
        }
        return $years;
    }

    private function getMonths()
    {
        return [
            '00' => 'Tüm Yıl',
            '01' => 'Ocak',
            '02' => 'Şubat',
            '03' => 'Mart',
            '04' => 'Nisan',
            '05' => 'Mayıs',
            '06' => 'Haziran',
            '07' => 'Temmuz',
            '08' => 'Ağustos',
            '09' => 'Eylül',
            '10' => 'Ekim',
            '11' => 'Kasım',
            '12' => 'Aralık'
        ];
    }

    private function getMetrics()
    {
        $totalAmount = $this->projectModel->selectSum('total_amount')->first()['total_amount'] ?? 0;
        $paidAmount = $this->projectModel->selectSum('paid_amount')->first()['paid_amount'] ?? 0;
        $pendingAmount = $totalAmount - $paidAmount;

        return [
            'totalProjects' => $this->projectModel->countAll(),
            'completedProjects' => $this->projectModel->where('status', 'Tamamlandı')->countAllResults(),
            'ongoingProjects' => $this->projectModel->where('status', 'Devam Ediyor')->countAllResults(),
            'pendingProjects' => $this->projectModel->where('status', 'Beklemede')->countAllResults(),
            'paymentPendingProjects' => $this->projectModel->where('status', 'Ödeme Bekliyor')->countAllResults(),
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'pendingAmount' => $pendingAmount
        ];
    }

    private function getRecentProjects($limit = 5)
    {
        return $this->projectModel
            ->select('projects.*, customers.name as customer_name')
            ->join('customers', 'customers.id = projects.customer_id')
            ->orderBy('projects.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    private function getTopCustomers($limit = 5)
    {
        return $this->customerModel->getTopCustomers($limit);
    }

    private function getMonthlyStats($year, $month)
    {
        // Eğer "Tüm Yıl" seçilmişse, yıllık istatistikleri döndür
        if ($month == '00') {
            return $this->getYearlyStats($year);
        }
        
        return [
            'totalProjects' => $this->projectModel->getMonthlyProjectCount($year, $month),
            'completedProjects' => $this->projectModel->getMonthlyCompletedProjectCount($year, $month),
            'totalAmount' => $this->projectModel->getMonthlyProjectAmount($year, $month),
            'paidAmount' => $this->paymentModel->getMonthlyPayments($year, $month),
            'pendingAmount' => $this->projectModel->getMonthlyProjectAmount($year, $month) - $this->paymentModel->getMonthlyPayments($year, $month)
        ];
    }

    private function getYearlyStats($year)
    {
        return [
            'totalProjects' => $this->projectModel->getYearlyProjectCount($year),
            'completedProjects' => $this->projectModel->getYearlyCompletedProjectCount($year),
            'totalAmount' => $this->projectModel->getYearlyProjectAmount($year),
            'paidAmount' => $this->paymentModel->getYearlyPayments($year),
            'pendingAmount' => $this->projectModel->getYearlyProjectAmount($year) - $this->paymentModel->getYearlyPayments($year)
        ];
    }

    private function getActiveProjects()
    {
        return $this->projectModel
            ->select('projects.*, customers.name as customer_name')
            ->join('customers', 'customers.id = projects.customer_id')
            ->where('projects.status !=', 'Tamamlandı')
            ->findAll();
    }

    private function getBudgetOverrunAnalysis()
    {
        $projects = $this->projectModel->getAllProjects();
        $overrunProjects = [];
        $normalProjects = [];

        foreach ($projects as $project) {
            // Veritabanında estimated_cost alanı olmadığı için total_amount'ın %70'ini tahmini maliyet olarak kullanıyoruz
            $estimatedCost = $project['total_amount'] * 0.7;
            
            if ($estimatedCost > 0) {
                $overrunPercentage = (($project['total_amount'] - $estimatedCost) / $estimatedCost) * 100;
                if ($overrunPercentage > 20) {
                    $overrunProjects[] = [
                        'name' => $project['name'],
                        'customer_name' => $project['customer_name'],
                        'total_amount' => $project['total_amount'],
                        'estimated_cost' => $estimatedCost,
                        'overrun_percentage' => $overrunPercentage
                    ];
                } else {
                    $normalProjects[] = $project;
                }
            }
        }

        return [
            'overrun_projects' => $overrunProjects,
            'normal_projects' => $normalProjects
        ];
    }

    private function getProfitMarginAnalysis()
    {
        $projects = $this->projectModel->getAllProjects();
        $totalProfit = 0;
        $totalAmount = 0;
        $byCategory = [];

        foreach ($projects as $project) {
            // Veritabanında estimated_cost alanı olmadığı için total_amount'ın %70'ini tahmini maliyet olarak kullanıyoruz
            $estimatedCost = $project['total_amount'] * 0.7;
            
            if ($estimatedCost > 0) {
                $profit = $project['total_amount'] - $estimatedCost;
                $profitPercentage = ($profit / $project['total_amount']) * 100;
                
                $totalProfit += $profit;
                $totalAmount += $project['total_amount'];

                if (!isset($byCategory[$project['category_name']])) {
                    $byCategory[$project['category_name']] = [
                        'name' => $project['category_name'],
                        'project_count' => 0,
                        'total_amount' => 0,
                        'total_profit' => 0,
                        'profit_percentage' => 0
                    ];
                }

                $byCategory[$project['category_name']]['project_count']++;
                $byCategory[$project['category_name']]['total_amount'] += $project['total_amount'];
                $byCategory[$project['category_name']]['total_profit'] += $profit;
                $byCategory[$project['category_name']]['profit_percentage'] = 
                    ($byCategory[$project['category_name']]['total_profit'] / $byCategory[$project['category_name']]['total_amount']) * 100;
            }
        }

        return [
            'total_profit' => $totalProfit,
            'average_profit_percentage' => $totalAmount > 0 ? ($totalProfit / $totalAmount) * 100 : 0,
            'by_category' => array_values($byCategory)
        ];
    }

    private function getKPIAnalysis()
    {
        $projects = $this->projectModel->getAllProjects();
        $totalProjects = count($projects);
        $completedProjects = 0;
        $onTimeProjects = 0;
        $withinBudgetProjects = 0;
        $convertedProjects = 0;

        foreach ($projects as $project) {
            if ($project['status'] === 'Tamamlandı') {
                $completedProjects++;
                
                // Zamanında tamamlanma kontrolü - end_date alanını planlanan bitiş tarihi olarak kullanıyoruz
                if (strtotime($project['updated_at']) <= strtotime($project['end_date'])) {
                    $onTimeProjects++;
                }
                
                // Bütçe uyumu kontrolü - total_amount'ın %70'ini tahmini maliyet olarak kullanıyoruz
                $estimatedCost = $project['total_amount'] * 0.7;
                if ($estimatedCost > 0 && $project['total_amount'] <= $estimatedCost) {
                    $withinBudgetProjects++;
                }
            }
            
            // Dönüşüm oranı kontrolü (örnek: tekliften projeye dönüşüm)
            if ($project['status'] !== 'Beklemede' && $project['status'] !== 'Başlamadı') {
                $convertedProjects++;
            }
        }

        return [
            'success_rate' => [
                'overall_success_rate' => $totalProjects > 0 ? ($completedProjects / $totalProjects) * 100 : 0,
                'on_time_percentage' => $completedProjects > 0 ? ($onTimeProjects / $completedProjects) * 100 : 0,
                'within_budget_percentage' => $completedProjects > 0 ? ($withinBudgetProjects / $completedProjects) * 100 : 0
            ],
            'project_conversion' => [
                'conversion_rate' => $totalProjects > 0 ? ($convertedProjects / $totalProjects) * 100 : 0
            ]
        ];
    }

    private function getCustomerAnalysis()
    {
        $projects = $this->projectModel->getAllProjects();
        $customerStats = [];
        $segmentStats = ['Yüksek Değerli' => 0, 'Orta Değerli' => 0, 'Düşük Değerli' => 0];
        $loyaltyStats = ['Sadık' => 0, 'Tekrarlayan' => 0, 'Yeni' => 0];
        $profitabilityStats = ['Yüksek' => 0, 'Orta' => 0, 'Düşük' => 0];

        foreach ($projects as $project) {
            if (!isset($customerStats[$project['customer_name']])) {
                $customerStats[$project['customer_name']] = [
                    'total_amount' => 0,
                    'project_count' => 0,
                    'profit' => 0
                ];
            }

            // Veritabanında estimated_cost alanı olmadığı için total_amount'ın %70'ini tahmini maliyet olarak kullanıyoruz
            $estimatedCost = $project['total_amount'] * 0.7;
            
            $customerStats[$project['customer_name']]['total_amount'] += $project['total_amount'];
            $customerStats[$project['customer_name']]['project_count']++;
            $customerStats[$project['customer_name']]['profit'] += ($project['total_amount'] - $estimatedCost);

            // Müşteri segmentasyonu
            if ($project['total_amount'] > 100000) {
                $segmentStats['Yüksek Değerli']++;
            } elseif ($project['total_amount'] > 50000) {
                $segmentStats['Orta Değerli']++;
            } else {
                $segmentStats['Düşük Değerli']++;
            }

            // Müşteri sadakati
            if ($customerStats[$project['customer_name']]['project_count'] > 5) {
                $loyaltyStats['Sadık']++;
            } elseif ($customerStats[$project['customer_name']]['project_count'] > 2) {
                $loyaltyStats['Tekrarlayan']++;
            } else {
                $loyaltyStats['Yeni']++;
            }

            // Müşteri karlılığı
            $profitMargin = ($project['total_amount'] - $estimatedCost) / $project['total_amount'] * 100;
            if ($profitMargin > 30) {
                $profitabilityStats['Yüksek']++;
            } elseif ($profitMargin > 15) {
                $profitabilityStats['Orta']++;
            } else {
                $profitabilityStats['Düşük']++;
            }
        }

        return [
            'segmentStats' => $segmentStats,
            'loyaltyStats' => $loyaltyStats,
            'profitabilityStats' => $profitabilityStats,
            'customerStats' => $customerStats
        ];
    }

    private function getPredictiveAnalysis()
    {
        $projects = $this->projectModel->getAllProjects();
        $categoryStats = [];

        foreach ($projects as $project) {
            if (!isset($categoryStats[$project['category_name']])) {
                $categoryStats[$project['category_name']] = [
                    'project_count' => 0,
                    'completion_time' => 0,
                    'budget_overrun' => ['overrun_percentage' => 0],
                    'success_rate' => ['success_rate' => 0],
                    'resource_need' => [
                        'daily_cost' => 0,
                        'total_cost' => 0
                    ]
                ];
            }
            
            $categoryStats[$project['category_name']]['project_count']++;

            // Veritabanında estimated_cost alanı olmadığı için total_amount'ın %70'ini tahmini maliyet olarak kullanıyoruz
            $estimatedCost = $project['total_amount'] * 0.7;

            // Tamamlanma süresi hesaplama
            if ($project['status'] === 'Tamamlandı') {
                $completionTime = (strtotime($project['updated_at']) - strtotime($project['start_date'])) / (60 * 60 * 24);
                $categoryStats[$project['category_name']]['completion_time'] = 
                    ($categoryStats[$project['category_name']]['completion_time'] * ($categoryStats[$project['category_name']]['project_count'] - 1) + $completionTime) / 
                    $categoryStats[$project['category_name']]['project_count'];
            }

            // Bütçe aşımı hesaplama
            if ($estimatedCost > 0) {
                $overrunPercentage = (($project['total_amount'] - $estimatedCost) / $estimatedCost) * 100;
                $categoryStats[$project['category_name']]['budget_overrun']['overrun_percentage'] = 
                    ($categoryStats[$project['category_name']]['budget_overrun']['overrun_percentage'] * ($categoryStats[$project['category_name']]['project_count'] - 1) + $overrunPercentage) / 
                    $categoryStats[$project['category_name']]['project_count'];
            }

            // Başarı oranı hesaplama
            $successFactors = 0;
            $totalFactors = 0;

            if ($project['status'] === 'Tamamlandı') {
                $successFactors++;
                $totalFactors++;
            }

            // Zamanında tamamlanma kontrolü - end_date alanını planlanan bitiş tarihi olarak kullanıyoruz
            if (strtotime($project['updated_at']) <= strtotime($project['end_date'])) {
                $successFactors++;
                $totalFactors++;
            }

            // Bütçe uyumu kontrolü
            if ($estimatedCost > 0 && $project['total_amount'] <= $estimatedCost) {
                $successFactors++;
                $totalFactors++;
            }

            $successRate = $totalFactors > 0 ? ($successFactors / $totalFactors) * 100 : 0;
            $categoryStats[$project['category_name']]['success_rate']['success_rate'] = 
                ($categoryStats[$project['category_name']]['success_rate']['success_rate'] * ($categoryStats[$project['category_name']]['project_count'] - 1) + $successRate) / 
                $categoryStats[$project['category_name']]['project_count'];

            // Kaynak ihtiyacı hesaplama
            if ($estimatedCost > 0) {
                $dailyCost = $estimatedCost / 30; // Aylık ortalama maliyet
                $categoryStats[$project['category_name']]['resource_need']['daily_cost'] = 
                    ($categoryStats[$project['category_name']]['resource_need']['daily_cost'] * ($categoryStats[$project['category_name']]['project_count'] - 1) + $dailyCost) / 
                    $categoryStats[$project['category_name']]['project_count'];
                $categoryStats[$project['category_name']]['resource_need']['total_cost'] += $estimatedCost;
            }
        }

        return [
            'categoryStats' => $categoryStats
        ];
    }
}
