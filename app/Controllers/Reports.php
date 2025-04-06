<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\PaymentModel;
use App\Models\CustomerModel;
use App\Models\CategoryModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Reports extends BaseController
{
    protected $projectModel;
    protected $paymentModel;
    protected $customerModel;
    protected $categoryModel;

    public function __construct()
    {
        helper(['form', 'url', 'report']);
        $this->projectModel = new ProjectModel();
        $this->paymentModel = new PaymentModel();
        $this->customerModel = new CustomerModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');

        $data = [
            'title' => 'Raporlar',
            'year' => $year,
            'month' => $month,
            'years' => $this->getYears(),
            'months' => $this->getMonths(),
            'categories' => $this->categoryModel->findAll(),
            'customers' => $this->customerModel->findAll(),
            'monthlyStats' => $this->getMonthlyStats($year, $month),
            'customerStats' => $this->getCustomerStats($year, $month),
            'categoryStats' => $this->getCategoryStats($year, $month),
            'customerAnalysis' => $this->getCustomerAnalysis($year, $month),
            'projectDurationAnalysis' => $this->getProjectDurationAnalysis($year, $month),
            'delayAnalysis' => $this->getDelayAnalysis($year, $month),
            'seasonalTrends' => $this->getSeasonalTrends($year),
            'cashFlowForecast' => $this->getCashFlowForecast($year),
            'budgetOverrunAnalysis' => $this->getBudgetOverrunAnalysis($year, $month),
            'profitMarginAnalysis' => $this->getProfitMarginAnalysis($year, $month),
            'paymentPerformanceAnalysis' => $this->getPaymentPerformanceAnalysis($year, $month),
            'kpiAnalysis' => $this->getKPIAnalysis($year, $month),
            'duration_analysis' => $this->projectModel->getProjectDurationAnalysis($year, $month),
            'delay_analysis' => $this->projectModel->getDelayAnalysis($year, $month),
            'seasonal_trends' => $this->projectModel->getSeasonalTrends($year),
            'financial_analysis' => $this->paymentModel->getFinancialAnalysis($year),
            'comparative_analysis' => $this->projectModel->getComparativeAnalysis($year, $month),
            'predictive_analysis' => $this->getPredictiveAnalysis($year, $month),
            'heatMapData' => $this->getProjectHeatMapData($year, $month),
            'ganttChartData' => $this->getGanttChartData($year, $month),
            'bubbleChartData' => $this->getBubbleChartData($year, $month)
        ];

        return view('reports/index', $data);
    }

    private function getMonthlyStats($year, $month)
    {
        $totalProjects = $this->projectModel->getMonthlyProjectCount($year, $month);
        $completedProjects = $this->projectModel->getMonthlyCompletedProjectCount($year, $month);
        $totalAmount = $this->projectModel->getMonthlyProjectAmount($year, $month);
        $paidAmount = $this->paymentModel->getMonthlyPaymentsTotal($year, $month);
        $pendingAmount = $totalAmount - $paidAmount;

        return [
            'totalProjects' => $totalProjects,
            'completedProjects' => $completedProjects,
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'pendingAmount' => $pendingAmount
        ];
    }

    private function getCustomerStats($year, $month)
    {
        return [
            'customerProjects' => $this->customerModel->getTopCustomers(5)
        ];
    }

    /**
     * Kategori bazlı istatistikleri getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Kategori istatistikleri
     */
    private function getCategoryStats($year, $month)
    {
        // Tüm kategorileri al
        $categories = $this->categoryModel->findAll();
        $categoryStats = [];

        foreach ($categories as $category) {
            // Kategoriye ait projeleri al
            $projects = $this->projectModel->getProjectsByCategory($category['id']);
            
            // Kategori istatistiklerini hesapla
            $totalProjects = count($projects);
            $completedProjects = count(array_filter($projects, function($p) { return $p['status'] == 'Tamamlandı'; }));
            $totalAmount = array_sum(array_column($projects, 'total_amount'));
            $paidAmount = array_sum(array_column($projects, 'paid_amount'));
            $pendingAmount = $totalAmount - $paidAmount;
            
            // Ortalama tamamlanma süresi (gün cinsinden)
            $avgCompletionTime = 0;
            $completedCount = 0;
            
            foreach ($projects as $project) {
                if ($project['status'] == 'Tamamlandı' && $project['start_date'] && $project['end_date']) {
                    $startDate = new \DateTime($project['start_date']);
                    $endDate = new \DateTime($project['end_date']);
                    $interval = $startDate->diff($endDate);
                    $avgCompletionTime += $interval->days;
                    $completedCount++;
                }
            }
            
            $avgCompletionTime = $completedCount > 0 ? round($avgCompletionTime / $completedCount) : 0;
            
            // Kâr marjı (varsayımsal olarak %20 kabul edelim)
            $profitMargin = $totalAmount > 0 ? ($totalAmount * 0.2) : 0;
            
            $categoryStats[] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'totalProjects' => $totalProjects,
                'completedProjects' => $completedProjects,
                'totalAmount' => $totalAmount,
                'paidAmount' => $paidAmount,
                'pendingAmount' => $pendingAmount,
                'avgCompletionTime' => $avgCompletionTime,
                'profitMargin' => $profitMargin,
                'completionRate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0
            ];
        }

        return $categoryStats;
    }

    /**
     * Müşteri bazlı analizleri getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Müşteri analizleri
     */
    private function getCustomerAnalysis($year, $month)
    {
        // Tüm müşterileri al
        $customers = $this->customerModel->findAll();
        $customerAnalysis = [];
        
        // Müşteri segmentasyonu için eşik değerler
        $highValueThreshold = 50000; // Yüksek değerli müşteri eşiği
        $loyaltyThreshold = 3; // Sadık müşteri eşiği (proje sayısı)
        
        foreach ($customers as $customer) {
            // Müşteriye ait projeleri al
            $projects = $this->projectModel->getProjectsByCustomer($customer['id']);
            
            // Müşteri istatistiklerini hesapla
            $totalProjects = count($projects);
            $completedProjects = count(array_filter($projects, function($p) { return $p['status'] == 'Tamamlandı'; }));
            $totalAmount = array_sum(array_column($projects, 'total_amount'));
            $paidAmount = array_sum(array_column($projects, 'paid_amount'));
            $pendingAmount = $totalAmount - $paidAmount;
            
            // Ortalama proje değeri
            $avgProjectValue = $totalProjects > 0 ? $totalAmount / $totalProjects : 0;
            
            // Ödeme performansı (ödenen / toplam)
            $paymentPerformance = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100, 2) : 0;
            
            // Müşteri segmentasyonu
            $segment = 'Düşük Değerli';
            if ($totalAmount >= $highValueThreshold) {
                $segment = 'Yüksek Değerli';
            } elseif ($totalAmount >= $highValueThreshold / 2) {
                $segment = 'Orta Değerli';
            }
            
            // Müşteri sadakati
            $loyalty = 'Yeni';
            if ($totalProjects >= $loyaltyThreshold) {
                $loyalty = 'Sadık';
            } elseif ($totalProjects > 1) {
                $loyalty = 'Tekrarlayan';
            }
            
            // Kârlılık analizi (varsayımsal olarak %20 kâr marjı)
            $profitMargin = $totalAmount > 0 ? ($totalAmount * 0.2) : 0;
            $profitability = 'Düşük';
            if ($profitMargin >= $highValueThreshold * 0.2) {
                $profitability = 'Yüksek';
            } elseif ($profitMargin >= ($highValueThreshold / 2) * 0.2) {
                $profitability = 'Orta';
            }
            
            // Son proje tarihi
            $lastProjectDate = null;
            if ($totalProjects > 0) {
                $dates = array_column($projects, 'created_at');
                $lastProjectDate = max($dates);
            }
            
            // Son ödeme tarihi
            $lastPaymentDate = null;
            if ($paidAmount > 0) {
                $payments = $this->paymentModel->getPaymentsByCustomer($customer['id']);
                if (!empty($payments)) {
                    $paymentDates = array_column($payments, 'payment_date');
                    $lastPaymentDate = max($paymentDates);
                }
            }
            
            $customerAnalysis[] = [
                'id' => $customer['id'],
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'totalProjects' => $totalProjects,
                'completedProjects' => $completedProjects,
                'totalAmount' => $totalAmount,
                'paidAmount' => $paidAmount,
                'pendingAmount' => $pendingAmount,
                'avgProjectValue' => $avgProjectValue,
                'paymentPerformance' => $paymentPerformance,
                'segment' => $segment,
                'loyalty' => $loyalty,
                'profitMargin' => $profitMargin,
                'profitability' => $profitability,
                'lastProjectDate' => $lastProjectDate,
                'lastPaymentDate' => $lastPaymentDate
            ];
        }
        
        // Müşteri segmentasyonu istatistikleri
        $segmentStats = [
            'Yüksek Değerli' => 0,
            'Orta Değerli' => 0,
            'Düşük Değerli' => 0
        ];
        
        // Müşteri sadakati istatistikleri
        $loyaltyStats = [
            'Sadık' => 0,
            'Tekrarlayan' => 0,
            'Yeni' => 0
        ];
        
        // Kârlılık istatistikleri
        $profitabilityStats = [
            'Yüksek' => 0,
            'Orta' => 0,
            'Düşük' => 0
        ];
        
        foreach ($customerAnalysis as $customer) {
            $segmentStats[$customer['segment']]++;
            $loyaltyStats[$customer['loyalty']]++;
            $profitabilityStats[$customer['profitability']]++;
        }
        
        return [
            'customers' => $customerAnalysis,
            'segmentStats' => $segmentStats,
            'loyaltyStats' => $loyaltyStats,
            'profitabilityStats' => $profitabilityStats
        ];
    }

    /**
     * Proje süre analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Proje süre analizi
     */
    private function getProjectDurationAnalysis($year, $month)
    {
        return $this->projectModel->getProjectDurationAnalysis($year, $month);
    }

    /**
     * Gecikme analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Gecikme analizi
     */
    private function getDelayAnalysis($year, $month)
    {
        return $this->projectModel->getDelayAnalysis($year, $month);
    }

    /**
     * Sezonsal trendleri getirir
     * 
     * @param int $year Yıl
     * @return array Sezonsal trendler
     */
    private function getSeasonalTrends($year)
    {
        return $this->projectModel->getSeasonalTrends($year);
    }

    /**
     * Nakit akışı tahminini getirir
     * 
     * @param int $year Yıl
     * @return array Nakit akışı tahmini
     */
    private function getCashFlowForecast($year)
    {
        return $this->paymentModel->getCashFlowForecast($year);
    }
    
    /**
     * Bütçe aşımı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Bütçe aşımı analizi
     */
    private function getBudgetOverrunAnalysis($year, $month)
    {
        return $this->projectModel->getBudgetOverrunAnalysis($year, $month);
    }
    
    /**
     * Kâr marjı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Kâr marjı analizi
     */
    private function getProfitMarginAnalysis($year, $month)
    {
        return $this->projectModel->getProfitMarginAnalysis($year, $month);
    }
    
    /**
     * Ödeme performansı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Ödeme performansı analizi
     */
    private function getPaymentPerformanceAnalysis($year, $month)
    {
        return $this->paymentModel->getPaymentPerformanceAnalysis($year, $month);
    }

    /**
     * KPI analizini getirir
     */
    private function getKPIAnalysis($year, $month)
    {
        return $this->projectModel->getKPIAnalysis($year, $month);
    }

    /**
     * Filtrelenmiş rapor verilerini getirir
     * 
     * @param array $filters Filtre parametreleri
     * @return array Filtrelenmiş rapor verileri
     */
    private function getFilteredReports($filters)
    {
        try {
            $data = [];
            
            // Tarih aralığı kontrolü
            $startDate = $filters['start_date'] ?? date('Y-m-01');
            $endDate = $filters['end_date'] ?? date('Y-m-t');
            
            // Kategori filtresi
            if (!empty($filters['category_id'])) {
                $data['category_id'] = $filters['category_id'];
            }
            
            // Müşteri filtresi
            if (!empty($filters['customer_id'])) {
                $data['customer_id'] = $filters['customer_id'];
            }
            
            // Durum filtresi
            if (!empty($filters['status'])) {
                $data['status'] = $filters['status'];
            }
            
            // Öncelik filtresi
            if (!empty($filters['priority'])) {
                $data['priority'] = $filters['priority'];
            }
            
            // Bütçe aralığı filtresi
            if (!empty($filters['min_amount'])) {
                $data['min_amount'] = $filters['min_amount'];
            }
            if (!empty($filters['max_amount'])) {
                $data['max_amount'] = $filters['max_amount'];
            }
            
            // Proje süresi filtresi
            if (!empty($filters['min_duration'])) {
                $data['min_duration'] = $filters['min_duration'];
            }
            if (!empty($filters['max_duration'])) {
                $data['max_duration'] = $filters['max_duration'];
            }
            
            // Ödeme durumu filtresi
            if (!empty($filters['payment_status'])) {
                $data['payment_status'] = $filters['payment_status'];
            }
            
            // Filtrelenmiş projeleri getir
            $filteredProjects = $this->projectModel->getFilteredProjects($data, $startDate, $endDate);
            if (!is_array($filteredProjects)) {
                $filteredProjects = [];
            }
            
            // Filtrelenmiş ödemeleri getir
            $filteredPayments = $this->paymentModel->getFilteredPayments($data, $startDate, $endDate);
            if (!is_array($filteredPayments)) {
                $filteredPayments = [];
            }
            
            return [
                'projects' => $filteredProjects,
                'payments' => $filteredPayments,
                'filters' => $filters
            ];
        } catch (\Exception $e) {
            log_message('error', 'getFilteredReports hatası: ' . $e->getMessage());
            return [
                'projects' => [],
                'payments' => [],
                'filters' => $filters,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Filtreleme işlemini gerçekleştirir
     * 
     * @return void
     */
    public function filter()
    {
        try {
            // AJAX isteği kontrolü
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Geçersiz istek türü'
                ]);
            }

            // Filtre parametrelerini al
            $filters = [
                'category_id' => $this->request->getPost('category_id'),
                'customer_id' => $this->request->getPost('customer_id'),
                'status' => $this->request->getPost('status'),
                'priority' => $this->request->getPost('priority'),
                'min_amount' => $this->request->getPost('min_amount'),
                'max_amount' => $this->request->getPost('max_amount'),
                'min_duration' => $this->request->getPost('min_duration'),
                'max_duration' => $this->request->getPost('max_duration'),
                'payment_status' => $this->request->getPost('payment_status')
            ];

            // Tarih aralığını al ve doğrula
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');

            if (empty($startDate) || empty($endDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Başlangıç ve bitiş tarihleri gereklidir'
                ]);
            }

            // Tarihleri doğrula
            if (!strtotime($startDate) || !strtotime($endDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Geçersiz tarih formatı'
                ]);
            }

            // Tarih aralığını kontrol et
            if (strtotime($endDate) < strtotime($startDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Bitiş tarihi başlangıç tarihinden önce olamaz'
                ]);
            }

            // Filtrelenmiş verileri al
            $filteredProjects = $this->projectModel->getFilteredProjects($filters, $startDate, $endDate);
            $filteredPayments = $this->paymentModel->getFilteredPayments($filters, $startDate, $endDate);

            // Sonuçları hazırla
            $result = [
                'success' => true,
                'data' => [
                    'projects' => $filteredProjects,
                    'payments' => $filteredPayments,
                    'total_projects' => count($filteredProjects),
                    'total_payments' => count($filteredPayments),
                    'total_amount' => array_sum(array_column($filteredPayments, 'amount'))
                ]
            ];

            return $this->response->setJSON($result);

        } catch (\Exception $e) {
            log_message('error', 'Filtreleme hatası: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Filtreleme işlemi sırasında bir hata oluştu'
            ]);
        }
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

    public function exportPDF()
    {
        // Rapor verilerini hazırla
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');
        
        // Rapor verilerini al
        $data = [
            'title' => 'Proje Yönetim Sistemi - Rapor',
            'year' => $year,
            'month' => $month,
            'monthlyStats' => $this->getMonthlyStats($year, $month),
            'customerStats' => $this->getCustomerStats($year, $month),
            'categoryStats' => $this->getCategoryStats($year, $month),
            'customerAnalysis' => $this->getCustomerAnalysis($year, $month),
            'projectDurationAnalysis' => $this->getProjectDurationAnalysis($year, $month),
            'delayAnalysis' => $this->getDelayAnalysis($year, $month),
            'seasonalTrends' => $this->getSeasonalTrends($year),
            'cashFlowForecast' => $this->getCashFlowForecast($year),
            'budgetOverrunAnalysis' => $this->getBudgetOverrunAnalysis($year, $month),
            'profitMarginAnalysis' => $this->getProfitMarginAnalysis($year, $month),
            'paymentPerformanceAnalysis' => $this->getPaymentPerformanceAnalysis($year, $month),
            'kpiAnalysis' => $this->getKPIAnalysis($year, $month),
            'predictive_analysis' => $this->getPredictiveAnalysis($year, $month)
        ];
        
        $html = view('reports/pdf_template', $data);
        
        // PDF başlık bilgilerini ayarla
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="rapor.html"');
        
        // HTML'i gönder
        echo $html;
    }

    public function exportExcel()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $month = $this->request->getGet('month') ?? date('m');
        
        // Rapor verilerini al
        $data = [
            'year' => $year,
            'month' => $month,
            'monthlyStats' => $this->getMonthlyStats($year, $month),
            'customerStats' => $this->getCustomerStats($year, $month),
            'categoryStats' => $this->getCategoryStats($year, $month),
            'customerAnalysis' => $this->getCustomerAnalysis($year, $month),
            'projectDurationAnalysis' => $this->getProjectDurationAnalysis($year, $month),
            'delayAnalysis' => $this->getDelayAnalysis($year, $month),
            'seasonalTrends' => $this->getSeasonalTrends($year),
            'cashFlowForecast' => $this->getCashFlowForecast($year),
            'budgetOverrunAnalysis' => $this->getBudgetOverrunAnalysis($year, $month),
            'profitMarginAnalysis' => $this->getProfitMarginAnalysis($year, $month),
            'paymentPerformanceAnalysis' => $this->getPaymentPerformanceAnalysis($year, $month),
            'kpiAnalysis' => $this->getKPIAnalysis($year, $month),
            'predictive_analysis' => $this->getPredictiveAnalysis($year, $month)
        ];
        
        // CSV başlık bilgilerini ayarla
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rapor_' . $year . '_' . $month . '.csv"');
        
        // CSV çıktısını oluştur
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Aylık İstatistikler
        fputcsv($output, ['Aylık İstatistikler']);
        fputcsv($output, ['Metrik', 'Değer']);
        fputcsv($output, ['Toplam Proje', $data['monthlyStats']['totalProjects']]);
        fputcsv($output, ['Tamamlanan Proje', $data['monthlyStats']['completedProjects']]);
        fputcsv($output, ['Toplam Tutar', number_format($data['monthlyStats']['totalAmount'], 2, ',', '.') . ' ₺']);
        fputcsv($output, ['Ödenen Tutar', number_format($data['monthlyStats']['paidAmount'], 2, ',', '.') . ' ₺']);
        fputcsv($output, ['Bekleyen Tutar', number_format($data['monthlyStats']['pendingAmount'], 2, ',', '.') . ' ₺']);
        fputcsv($output, []); // Boş satır
        
        // Kategori İstatistikleri
        fputcsv($output, ['Kategori İstatistikleri']);
        fputcsv($output, ['Kategori', 'Toplam Proje', 'Tamamlanan Proje', 'Toplam Tutar', 'Ödenen Tutar', 'Bekleyen Tutar']);
        foreach ($data['categoryStats'] as $category) {
            fputcsv($output, [
                $category['name'],
                $category['totalProjects'],
                $category['completedProjects'],
                number_format($category['totalAmount'], 2, ',', '.') . ' ₺',
                number_format($category['paidAmount'], 2, ',', '.') . ' ₺',
                number_format($category['pendingAmount'], 2, ',', '.') . ' ₺'
            ]);
        }
        fputcsv($output, []); // Boş satır
        
        // Tahminsel Analizler
        fputcsv($output, ['Tahminsel Analizler']);
        fputcsv($output, ['Kategori', 'Tamamlanma Süresi (Gün)', 'Bütçe Aşımı (%)', 'Başarı Oranı (%)', 'Günlük Kaynak İhtiyacı (₺)', 'Toplam Kaynak İhtiyacı (₺)']);
        foreach ($data['predictive_analysis'] as $category => $predictions) {
            fputcsv($output, [
                $category,
                $predictions['completion_time'],
                number_format($predictions['budget_overrun']['overrun_percentage'], 2, ',', '.'),
                number_format($predictions['success_rate']['success_rate'], 2, ',', '.'),
                number_format($predictions['resource_need']['daily_cost'], 2, ',', '.') . ' ₺',
                number_format($predictions['resource_need']['total_cost'], 2, ',', '.') . ' ₺'
            ]);
        }
        fputcsv($output, []); // Boş satır
        
        // Müşteri Analizi
        fputcsv($output, ['Müşteri Analizi']);
        fputcsv($output, ['Müşteri', 'Toplam Proje', 'Tamamlanan Proje', 'Toplam Tutar', 'Ödenen Tutar', 'Bekleyen Tutar']);
        foreach ($data['customerAnalysis']['customers'] as $customer) {
            fputcsv($output, [
                $customer['name'],
                $customer['totalProjects'],
                $customer['completedProjects'],
                number_format($customer['totalAmount'], 2, ',', '.') . ' ₺',
                number_format($customer['paidAmount'], 2, ',', '.') . ' ₺',
                number_format($customer['pendingAmount'], 2, ',', '.') . ' ₺'
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Tahminsel analizleri getir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array
     */
    private function getPredictiveAnalysis($year, $month)
    {
        // Kategori bilgilerini al
        $categories = $this->categoryModel->findAll();
        $categoryNames = [];
        foreach ($categories as $category) {
            $categoryNames[$category['id']] = $category['name'];
        }

        // Tahminsel analizleri al
        $completionTimePredictions = $this->projectModel->getCompletionTimePrediction($year, $month);
        $budgetOverrunPredictions = $this->projectModel->getBudgetOverrunPrediction($year, $month);
        $projectSuccessPredictions = $this->projectModel->getProjectSuccessPrediction($year, $month);
        $resourceNeedPredictions = $this->projectModel->getResourceNeedPrediction($year, $month);

        // Sonuçları kategorilere göre düzenle
        $predictions = [];
        foreach ($categories as $category) {
            $categoryId = $category['id'];
            $predictions[$category['name']] = [
                'completion_time' => $completionTimePredictions[$categoryId] ?? 0,
                'budget_overrun' => $budgetOverrunPredictions[$categoryId] ?? [
                    'overrun_percentage' => 0,
                    'estimated_amount' => 0,
                    'estimated_paid' => 0
                ],
                'success_rate' => $projectSuccessPredictions[$categoryId] ?? [
                    'success_rate' => 0,
                    'total_projects' => 0,
                    'successful_projects' => 0
                ],
                'resource_need' => $resourceNeedPredictions[$categoryId] ?? [
                    'daily_cost' => 0,
                    'duration' => 0,
                    'total_cost' => 0
                ]
            ];
        }

        return $predictions;
    }

    /**
     * Otomatik rapor gönderimi için zamanlanmış görev
     * 
     * @return void
     */
    public function sendScheduledReports()
    {
        // Zamanlanmış görev kontrolü
        if (!$this->request->isCLI()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Bu işlem sadece CLI üzerinden çalıştırılabilir']);
        }
        
        // Son ayın raporunu hazırla
        $lastMonth = date('m', strtotime('-1 month'));
        $lastYear = date('Y', strtotime('-1 month'));
        
        // Rapor verilerini al
        $data = [
            'year' => $lastYear,
            'month' => $lastMonth,
            'monthlyStats' => $this->getMonthlyStats($lastYear, $lastMonth),
            'customerStats' => $this->getCustomerStats($lastYear, $lastMonth),
            'categoryStats' => $this->getCategoryStats($lastYear, $lastMonth),
            'predictive_analysis' => $this->getPredictiveAnalysis($lastYear, $lastMonth)
        ];
        
        // PDF oluştur
        $dompdf = new \Dompdf\Dompdf();
        $html = view('reports/pdf_template', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // PDF'i kaydet
        $filename = "rapor_{$lastYear}_{$lastMonth}.pdf";
        $filepath = WRITEPATH . 'reports/' . $filename;
        
        // Dizin yoksa oluştur
        if (!is_dir(WRITEPATH . 'reports')) {
            mkdir(WRITEPATH . 'reports', 0777, true);
        }
        
        file_put_contents($filepath, $dompdf->output());
        
        // E-posta gönder
        $email = \Config\Services::email();
        
        $email->setFrom('raporlar@example.com', 'Proje Yönetim Sistemi');
        $email->setTo('yonetici@example.com');
        $email->setSubject("Aylık Rapor - {$lastYear}/{$lastMonth}");
        $email->setMessage("Sayın Yönetici,\n\n{$lastYear} yılı {$lastMonth}. ay raporu ekte sunulmuştur.\n\nSaygılarımızla,\nProje Yönetim Sistemi");
        $email->attach($filepath);
        
        $email->send();
        
        // Rapor arşivine kaydet
        $this->archiveReport($filename, $lastYear, $lastMonth);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Rapor başarıyla gönderildi']);
    }
    
    /**
     * Raporu arşive kaydeder
     * 
     * @param string $filename Dosya adı
     * @param int $year Yıl
     * @param int $month Ay
     * @return bool
     */
    private function archiveReport($filename, $year, $month)
    {
        // Arşiv dizini yoksa oluştur
        $archiveDir = WRITEPATH . 'reports/archive';
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0777, true);
        }
        
        // Dosyayı arşive kopyala
        $sourceFile = WRITEPATH . 'reports/' . $filename;
        $targetFile = $archiveDir . '/' . $filename;
        
        if (file_exists($sourceFile)) {
            return copy($sourceFile, $targetFile);
        }
        
        return false;
    }
    
    /**
     * Rapor arşivini görüntüler
     * 
     * @return string
     */
    public function archive()
    {
        // Arşiv dizini yoksa oluştur
        $archiveDir = WRITEPATH . 'reports/archive';
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0777, true);
        }
        
        // Arşivdeki dosyaları listele
        $files = [];
        if (is_dir($archiveDir)) {
            $items = scandir($archiveDir);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..' && pathinfo($item, PATHINFO_EXTENSION) == 'pdf') {
                    $filePath = $archiveDir . '/' . $item;
                    $fileInfo = pathinfo($item);
                    $fileName = $fileInfo['filename'];
                    
                    // Dosya adından yıl ve ay bilgisini çıkar
                    $parts = explode('_', $fileName);
                    if (count($parts) >= 3) {
                        $year = $parts[1];
                        $month = $parts[2];
                        
                        $files[] = [
                            'name' => $item,
                            'year' => $year,
                            'month' => $month,
                            'size' => filesize($filePath),
                            'date' => date('d.m.Y H:i:s', filemtime($filePath))
                        ];
                    }
                }
            }
            
            // Dosyaları tarihe göre sırala (en yeniden en eskiye)
            usort($files, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        $data = [
            'title' => 'Rapor Arşivi',
            'files' => $files
        ];
        
        return view('reports/archive', $data);
    }
    
    /**
     * Arşivlenmiş raporu indirir
     * 
     * @param string $filename Dosya adı
     * @return \CodeIgniter\HTTP\Response
     */
    public function downloadArchive($filename)
    {
        $archiveDir = WRITEPATH . 'reports/archive';
        $filePath = $archiveDir . '/' . $filename;
        
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($filename);
        }
        
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Dosya bulunamadı']);
    }

    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Proje yoğunluğu ısı haritası için veri getirir
     */
    private function getProjectHeatMapData($year, $month)
    {
        $data = [];
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        // Haftanın günleri
        $weekDays = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        
        // Her gün için proje sayısını hesapla
        for ($day = 1; $day <= $days; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $weekDay = date('N', strtotime($date)) - 1; // 0-6 arası (Pazartesi-Pazar)
            $weekNumber = ceil($day / 7);
            
            // O gün aktif olan projeleri say
            $activeProjects = $this->projectModel->where("start_date <=", $date)
                                               ->groupStart()
                                               ->where("end_date >= ", $date)
                                               ->orWhere("end_date IS NULL")
                                               ->groupEnd()
                                               ->countAllResults();
            
            $data[] = [
                'x' => $weekDay,
                'y' => $weekNumber - 1,
                'value' => $activeProjects
            ];
        }
        
        return [
            'data' => $data,
            'weekDays' => $weekDays
        ];
    }

    /**
     * Gantt şeması için proje verilerini getirir
     */
    private function getGanttChartData($year, $month)
    {
        $projects = $this->projectModel->where('YEAR(start_date)', $year)
                                     ->where('MONTH(start_date) <=', $month)
                                     ->groupStart()
                                     ->where('YEAR(end_date) >=', $year)
                                     ->orWhere('end_date IS NULL')
                                     ->groupEnd()
                                     ->findAll();
        
        $data = [];
        foreach ($projects as $project) {
            // Başlangıç tarihi kontrolü
            if (empty($project['start_date'])) {
                continue;
            }
            
            $startDate = new \DateTime($project['start_date']);
            
            // Bitiş tarihi kontrolü
            if (empty($project['end_date'])) {
                $endDate = (new \DateTime())->modify('+30 days');
            } else {
                $endDate = new \DateTime($project['end_date']);
            }
            
            // Tarihlerin geçerli olduğundan emin ol
            if ($startDate && $endDate) {
                $data[] = [
                    'id' => 'task-' . $project['id'],
                    'name' => $project['name'],
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'progress' => $project['status'] == 'Tamamlandı' ? 100 : 
                                ($project['status'] == 'Devam Ediyor' ? 50 : 
                                ($project['status'] == 'Başlamadı' ? 0 : 25)),
                    'dependencies' => '',
                    'custom_class' => 'bar-' . strtolower(str_replace(' ', '-', $project['status']))
                ];
            }
        }
        
        return $data;
    }

    /**
     * Bubble chart için proje verilerini getirir
     */
    private function getBubbleChartData($year, $month)
    {
        $projects = $this->projectModel->where('YEAR(start_date)', $year)
                                     ->where('MONTH(start_date)', $month)
                                     ->findAll();
        
        $data = [];
        foreach ($projects as $project) {
            if ($project['start_date'] && $project['end_date']) {
                $startDate = new \DateTime($project['start_date']);
                $endDate = new \DateTime($project['end_date']);
                $duration = $endDate->diff($startDate)->days;
                
                $data[] = [
                    'x' => $duration, // Süre (gün)
                    'y' => $project['total_amount'], // Toplam tutar
                    'r' => $project['paid_amount'] / $project['total_amount'] * 20, // Ödeme oranına göre büyüklük
                    'name' => $project['name']
                ];
            }
        }
        
        return $data;
    }
}
