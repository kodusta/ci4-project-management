<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['project_id', 'amount', 'payment_date', 'description'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'project_id' => 'required|numeric',
        'amount' => 'required|numeric|greater_than[0]',
        'payment_date' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'project_id' => [
            'required' => 'Proje seçimi zorunludur',
            'numeric' => 'Geçersiz proje seçimi'
        ],
        'amount' => [
            'required' => 'Ödeme tutarı zorunludur',
            'numeric' => 'Ödeme tutarı sayısal olmalıdır',
            'greater_than' => 'Ödeme tutarı 0\'dan büyük olmalıdır'
        ],
        'payment_date' => [
            'required' => 'Ödeme tarihi zorunludur',
            'valid_date' => 'Geçersiz ödeme tarihi'
        ]
    ];

    protected $skipValidation = false;

    public function getProjectPayments($projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
    }

    public function getTotalPayments($projectId)
    {
        $result = $this->selectSum('amount')
                      ->where('project_id', $projectId)
                      ->first();
        return $result['amount'] ?? 0;
    }

    public function getPaymentDetails($paymentId)
    {
        return $this->select('payments.*, projects.name as project_name, projects.total_amount as project_total')
                    ->join('projects', 'projects.id = payments.project_id')
                    ->where('payments.id', $paymentId)
                    ->first();
    }

    public function getRecentPayments($limit = 10)
    {
        return $this->select('payments.*, projects.name as project_name, projects.customer_id')
                    ->join('projects', 'projects.id = payments.project_id')
                    ->orderBy('payment_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getMonthlyPayments($year, $month)
    {
        $result = $this->selectSum('amount')
                      ->where('YEAR(payment_date)', $year)
                      ->where('MONTH(payment_date)', $month)
                      ->first();
        
        return $result['amount'] ?? 0;
    }

    public function getYearlyPayments($year)
    {
        $result = $this->selectSum('amount')
                      ->where('YEAR(payment_date)', $year)
                      ->first();
        
        return $result['amount'] ?? 0;
    }

    public function getMonthlyPendingPayments($year, $month)
    {
        $result = $this->selectSum('amount')
                      ->join('projects', 'projects.id = payments.project_id')
                      ->where('YEAR(payment_date)', $year)
                      ->where('MONTH(payment_date)', $month)
                      ->first();
        return $result['amount'] ?? 0;
    }

    public function getYearlyPendingPayments($year)
    {
        $result = $this->selectSum('amount')
                      ->join('projects', 'projects.id = payments.project_id')
                      ->where('YEAR(payment_date)', $year)
                      ->first();
        return $result['amount'] ?? 0;
    }

    public function getMonthlyPaymentsTotal($year, $month)
    {
        $result = $this->selectSum('amount')
                      ->where('YEAR(payment_date)', $year)
                      ->where('MONTH(payment_date)', $month)
                      ->first();
        return $result['amount'] ?? 0;
    }

    public function getYearlyPaymentsTotal($year)
    {
        $result = $this->selectSum('amount')
                      ->where('YEAR(payment_date)', $year)
                      ->first();
        return $result['amount'] ?? 0;
    }

    public function getPaymentStatsByProject($projectId)
    {
        return $this->where('project_id', $projectId)
                    ->orderBy('payment_date', 'ASC')
                    ->findAll();
    }

    /**
     * Belirli bir müşteriye ait ödemeleri getirir
     * 
     * @param int $customerId Müşteri ID
     * @return array Müşteriye ait ödemeler
     */
    public function getPaymentsByCustomer($customerId)
    {
        $builder = $this->db->table($this->table);
        $builder->join('projects', 'projects.id = payments.project_id');
        $builder->where('projects.customer_id', $customerId);
        $query = $builder->get();
        
        return $query->getResultArray();
    }

    /**
     * Nakit akışı tahminini getirir
     * 
     * @param int $year Yıl
     * @return array Nakit akışı tahmini
     */
    public function getCashFlowForecast($year)
    {
        // Gelecek 6 ay için nakit akışı tahmini
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $forecast = [
            'months' => [],
            'expected_inflows' => [],
            'expected_outflows' => [],
            'net_cash_flow' => []
        ];
        
        // Geçmiş 6 ayın verilerini al
        $historicalData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;
            
            if ($month <= 0) {
                $month += 12;
                $year--;
            }
            
            $monthName = $this->getMonthName($month);
            $historicalData[$monthName] = [
                'inflow' => $this->getMonthlyPaymentsTotal($year, $month),
                'outflow' => $this->getMonthlyExpensesTotal($year, $month)
            ];
        }
        
        // Gelecek 6 ay için tahmin yap
        for ($i = 1; $i <= 6; $i++) {
            $month = $currentMonth + $i;
            $year = $currentYear;
            
            if ($month > 12) {
                $month -= 12;
                $year++;
            }
            
            $monthName = $this->getMonthName($month);
            $forecast['months'][] = $monthName;
            
            // Basit bir tahmin modeli: Son 6 ayın ortalaması
            $avgInflow = array_sum(array_column($historicalData, 'inflow')) / count($historicalData);
            $avgOutflow = array_sum(array_column($historicalData, 'outflow')) / count($historicalData);
            
            // Mevsimsel faktörler (örnek)
            $seasonalFactor = $this->getSeasonalFactor($month);
            
            $expectedInflow = $avgInflow * $seasonalFactor;
            $expectedOutflow = $avgOutflow * $seasonalFactor;
            
            $forecast['expected_inflows'][] = round($expectedInflow, 2);
            $forecast['expected_outflows'][] = round($expectedOutflow, 2);
            $forecast['net_cash_flow'][] = round($expectedInflow - $expectedOutflow, 2);
        }
        
        return $forecast;
    }
    
    /**
     * Mevsimsel faktörü getirir
     * 
     * @param int $month Ay
     * @return float Mevsimsel faktör
     */
    private function getSeasonalFactor($month)
    {
        // Basit bir mevsimsel faktör modeli
        $factors = [
            1 => 0.8,  // Ocak
            2 => 0.9,  // Şubat
            3 => 1.0,  // Mart
            4 => 1.1,  // Nisan
            5 => 1.2,  // Mayıs
            6 => 1.3,  // Haziran
            7 => 1.2,  // Temmuz
            8 => 1.1,  // Ağustos
            9 => 1.0,  // Eylül
            10 => 0.9, // Ekim
            11 => 0.8, // Kasım
            12 => 1.1  // Aralık
        ];
        
        return $factors[$month] ?? 1.0;
    }
    
    /**
     * Aylık giderleri getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return float Aylık giderler
     */
    private function getMonthlyExpensesTotal($year, $month)
    {
        // Bu fonksiyon gerçek bir uygulamada expenses tablosundan veri çekecektir
        // Şimdilik tahmini bir değer döndürüyoruz
        $builder = $this->db->table('projects');
        $builder->select('SUM(total_amount * 0.2) as total_expenses');
        $builder->where('YEAR(start_date)', $year);
        $builder->where('MONTH(start_date)', $month);
        $builder->where('status', 'Devam Ediyor');
        $query = $builder->get();
        $result = $query->getRowArray();
        
        return $result['total_expenses'] ?? 0;
    }

    /**
     * Ödeme performansı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Ödeme performansı analizi
     */
    public function getPaymentPerformanceAnalysis($year, $month)
    {
        $builder = $this->db->table('projects');
        $builder->select('projects.*, 
                         customers.name as customer_name,
                         categories.name as category_name,
                         (projects.total_amount - projects.paid_amount) as remaining_amount,
                         DATEDIFF(CURRENT_DATE, projects.start_date) as days_since_start,
                         DATEDIFF(CURRENT_DATE, projects.end_date) as days_since_due');
        $builder->join('customers', 'customers.id = projects.customer_id');
        $builder->join('categories', 'categories.id = projects.category_id');
        $builder->where('YEAR(projects.start_date)', $year);
        if ($month != '00') {
            $builder->where('MONTH(projects.start_date)', $month);
        }
        $query = $builder->get();
        
        $projects = $query->getResultArray();
        $analysis = [
            'total_projects' => count($projects),
            'total_amount' => 0,
            'total_paid' => 0,
            'total_remaining' => 0,
            'on_time_payments' => 0,
            'late_payments' => 0,
            'by_customer' => [],
            'by_category' => [],
            'late_payment_projects' => []
        ];
        
        foreach ($projects as $project) {
            $analysis['total_amount'] += $project['total_amount'];
            $analysis['total_paid'] += $project['paid_amount'];
            $analysis['total_remaining'] += $project['remaining_amount'];
            
            // Ödeme durumunu kontrol et
            if ($project['days_since_due'] > 0 && $project['remaining_amount'] > 0) {
                $analysis['late_payments']++;
                $analysis['late_payment_projects'][] = $project;
            } else {
                $analysis['on_time_payments']++;
            }
            
            // Müşteri bazlı analiz
            if (!isset($analysis['by_customer'][$project['customer_name']])) {
                $analysis['by_customer'][$project['customer_name']] = [
                    'count' => 0,
                    'total_amount' => 0,
                    'total_paid' => 0,
                    'remaining_amount' => 0,
                    'on_time_count' => 0,
                    'late_count' => 0
                ];
            }
            $analysis['by_customer'][$project['customer_name']]['count']++;
            $analysis['by_customer'][$project['customer_name']]['total_amount'] += $project['total_amount'];
            $analysis['by_customer'][$project['customer_name']]['total_paid'] += $project['paid_amount'];
            $analysis['by_customer'][$project['customer_name']]['remaining_amount'] += $project['remaining_amount'];
            
            if ($project['days_since_due'] > 0 && $project['remaining_amount'] > 0) {
                $analysis['by_customer'][$project['customer_name']]['late_count']++;
            } else {
                $analysis['by_customer'][$project['customer_name']]['on_time_count']++;
            }
            
            // Kategori bazlı analiz
            if (!isset($analysis['by_category'][$project['category_name']])) {
                $analysis['by_category'][$project['category_name']] = [
                    'count' => 0,
                    'total_amount' => 0,
                    'total_paid' => 0,
                    'remaining_amount' => 0,
                    'on_time_count' => 0,
                    'late_count' => 0
                ];
            }
            $analysis['by_category'][$project['category_name']]['count']++;
            $analysis['by_category'][$project['category_name']]['total_amount'] += $project['total_amount'];
            $analysis['by_category'][$project['category_name']]['total_paid'] += $project['paid_amount'];
            $analysis['by_category'][$project['category_name']]['remaining_amount'] += $project['remaining_amount'];
            
            if ($project['days_since_due'] > 0 && $project['remaining_amount'] > 0) {
                $analysis['by_category'][$project['category_name']]['late_count']++;
            } else {
                $analysis['by_category'][$project['category_name']]['on_time_count']++;
            }
        }
        
        return $analysis;
    }

    /**
     * Ay adını getirir
     * 
     * @param int $month Ay numarası
     * @return string Ay adı
     */
    private function getMonthName($month)
    {
        $months = [
            1 => 'Ocak',
            2 => 'Şubat',
            3 => 'Mart',
            4 => 'Nisan',
            5 => 'Mayıs',
            6 => 'Haziran',
            7 => 'Temmuz',
            8 => 'Ağustos',
            9 => 'Eylül',
            10 => 'Ekim',
            11 => 'Kasım',
            12 => 'Aralık'
        ];
        
        return $months[$month] ?? '';
    }
    
    /**
     * Finansal analiz verilerini getirir
     * 
     * @param int $year Yıl
     * @return array Finansal analiz verileri
     */
    public function getFinancialAnalysis($year)
    {
        $db = \Config\Database::connect();
        
        // Yıllık toplam gelir
        $yearlyRevenue = $this->getYearlyPayments($year);
        
        // Aylık gelir dağılımı
        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyRevenue[$month] = [
                'month' => $this->getMonthName($month),
                'amount' => $this->getMonthlyPayments($year, $month)
            ];
        }
        
        // Proje bazında gelir dağılımı
        $projectRevenue = $db->query("
            SELECT p.id, p.name, SUM(py.amount) as total_amount
            FROM projects p
            LEFT JOIN payments py ON p.id = py.project_id
            WHERE YEAR(py.payment_date) = ?
            GROUP BY p.id, p.name
            ORDER BY total_amount DESC
            LIMIT 10
        ", [$year])->getResultArray();
        
        // Müşteri bazında gelir dağılımı
        $customerRevenue = $db->query("
            SELECT c.id, c.name, SUM(py.amount) as total_amount
            FROM customers c
            JOIN projects p ON c.id = p.customer_id
            LEFT JOIN payments py ON p.id = py.project_id
            WHERE YEAR(py.payment_date) = ?
            GROUP BY c.id, c.name
            ORDER BY total_amount DESC
            LIMIT 10
        ", [$year])->getResultArray();
        
        // Kategori bazında gelir dağılımı
        $categoryRevenue = $db->query("
            SELECT cat.id, cat.name, SUM(py.amount) as total_amount
            FROM categories cat
            JOIN projects p ON cat.id = p.category_id
            LEFT JOIN payments py ON p.id = py.project_id
            WHERE YEAR(py.payment_date) = ?
            GROUP BY cat.id, cat.name
            ORDER BY total_amount DESC
        ", [$year])->getResultArray();
        
        // Ödeme performansı
        $paymentPerformance = $this->getPaymentPerformanceAnalysis($year, date('m'));
        
        // Nakit akışı tahmini
        $cashFlowForecast = $this->getCashFlowForecast($year);
        
        return [
            'yearly_revenue' => $yearlyRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'project_revenue' => $projectRevenue,
            'customer_revenue' => $customerRevenue,
            'category_revenue' => $categoryRevenue,
            'payment_performance' => $paymentPerformance,
            'cash_flow_forecast' => $cashFlowForecast
        ];
    }

    /**
     * Filtrelenmiş ödemeleri getirir
     * 
     * @param array $filters Filtre parametreleri
     * @param string $startDate Başlangıç tarihi
     * @param string $endDate Bitiş tarihi
     * @return array Filtrelenmiş ödemeler
     */
    public function getFilteredPayments($filters, $startDate, $endDate)
    {
        try {
            $builder = $this->db->table($this->table);
            $builder->select('payments.*, projects.name as project_name, customers.name as customer_name');
            $builder->join('projects', 'projects.id = payments.project_id');
            $builder->join('customers', 'customers.id = projects.customer_id');
            
            // Tarih aralığı filtresi
            $builder->where('payments.payment_date >=', $startDate);
            $builder->where('payments.payment_date <=', $endDate);
            
            // Kategori filtresi
            if (!empty($filters['category_id'])) {
                $builder->where('projects.category_id', $filters['category_id']);
            }
            
            // Müşteri filtresi
            if (!empty($filters['customer_id'])) {
                $builder->where('projects.customer_id', $filters['customer_id']);
            }
            
            // Ödeme tipi filtresi
            if (!empty($filters['payment_type'])) {
                $builder->where('payments.payment_type', $filters['payment_type']);
            }
            
            // Ödeme durumu filtresi
            if (!empty($filters['payment_status'])) {
                $builder->where('payments.status', $filters['payment_status']);
            }
            
            // Tutar aralığı filtresi
            if (!empty($filters['min_amount'])) {
                $builder->where('payments.amount >=', $filters['min_amount']);
            }
            if (!empty($filters['max_amount'])) {
                $builder->where('payments.amount <=', $filters['max_amount']);
            }
            
            $query = $builder->get();
            $result = $query->getResultArray();
            
            // Sonuçları kontrol et
            if ($result === null) {
                return [];
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'getFilteredPayments hatası: ' . $e->getMessage());
            return [];
        }
    }
}
