<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'customer_id', 'category_id', 'name', 'description', 'task', 
        'priority', 'status', 'start_date', 'end_date', 
        'total_amount', 'paid_amount'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    protected $validationRules = [
        'customer_id' => 'required|numeric',
        'category_id' => 'required|numeric',
        'name' => 'required|min_length[3]|max_length[200]',
        'description' => 'permit_empty',
        'task' => 'permit_empty',
        'priority' => 'required|in_list[Düşük,Orta,Yüksek,Acil]',
        'status' => 'required|in_list[Ödeme Bekliyor,Başlamadı,Devam Ediyor,Tamamlandı,Beklemede]',
        'start_date' => 'required|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'total_amount' => 'required|numeric|greater_than[0]',
        'paid_amount' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Müşteri seçimi zorunludur',
            'numeric' => 'Geçersiz müşteri seçimi'
        ],
        'category_id' => [
            'required' => 'Kategori seçimi zorunludur',
            'numeric' => 'Geçersiz kategori seçimi'
        ],
        'name' => [
            'required' => 'Proje adı zorunludur',
            'min_length' => 'Proje adı en az 3 karakter olmalıdır',
            'max_length' => 'Proje adı en fazla 200 karakter olabilir'
        ],
        'priority' => [
            'required' => 'Öncelik seçimi zorunludur',
            'in_list' => 'Geçersiz öncelik seçimi'
        ],
        'status' => [
            'required' => 'Durum seçimi zorunludur',
            'in_list' => 'Geçersiz durum seçimi'
        ],
        'start_date' => [
            'required' => 'Başlangıç tarihi zorunludur',
            'valid_date' => 'Geçersiz başlangıç tarihi'
        ],
        'end_date' => [
            'valid_date' => 'Geçersiz bitiş tarihi'
        ],
        'total_amount' => [
            'required' => 'Toplam tutar zorunludur',
            'numeric' => 'Toplam tutar sayısal olmalıdır',
            'greater_than' => 'Toplam tutar 0\'dan büyük olmalıdır'
        ],
        'paid_amount' => [
            'numeric' => 'Ödenen tutar sayısal olmalıdır',
            'greater_than_equal_to' => 'Ödenen tutar 0 veya daha büyük olmalıdır'
        ]
    ];

    protected $skipValidation = false;

    // Proje durumlarını getir
    public function getStatusList()
    {
        return [
            'Ödeme Bekliyor' => 'Ödeme Bekliyor',
            'Başlamadı' => 'Başlamadı',
            'Devam Ediyor' => 'Devam Ediyor',
            'Tamamlandı' => 'Tamamlandı',
            'Beklemede' => 'Beklemede'
        ];
    }

    // Proje önceliklerini getir
    public function getPriorityList()
    {
        return [
            'Düşük' => 'Düşük',
            'Orta' => 'Orta',
            'Yüksek' => 'Yüksek',
            'Acil' => 'Acil'
        ];
    }

    // Müşteriye ait projeleri getir
    public function getCustomerProjects($customerId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, categories.name as category_name');
        $builder->join('categories', 'categories.id = projects.category_id', 'left');
        $builder->where('customer_id', $customerId);
        return $builder->get()->getResultArray();
    }

    // Proje detaylarını müşteri ve kategori bilgileriyle birlikte getir
    public function getProjectWithDetails($id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, customers.name as customer_name, customers.email as customer_email, customers.phone as customer_phone, categories.name as category_name');
        $builder->join('customers', 'customers.id = projects.customer_id');
        $builder->join('categories', 'categories.id = projects.category_id', 'left');
        $builder->where('projects.id', $id);
        return $builder->get()->getRowArray();
    }

    // Aylık proje sayısını getir
    public function getMonthlyProjectCount($year, $month)
    {
        return $this->where('YEAR(start_date)', $year)
                    ->where('MONTH(start_date)', $month)
                    ->countAllResults();
    }

    // Aylık tamamlanan proje sayısını getir
    public function getMonthlyCompletedProjectCount($year, $month)
    {
        return $this->where('YEAR(start_date)', $year)
                    ->where('MONTH(start_date)', $month)
                    ->where('status', 'Tamamlandı')
                    ->countAllResults();
    }

    // Aylık proje tutarını getir
    public function getMonthlyProjectAmount($year, $month)
    {
        $result = $this->selectSum('total_amount')
                      ->where('YEAR(start_date)', $year)
                      ->where('MONTH(start_date)', $month)
                      ->first();
        
        return $result['total_amount'] ?? 0;
    }

    // Yıllık proje sayısını getir
    public function getYearlyProjectCount($year)
    {
        return $this->where('YEAR(start_date)', $year)
                    ->countAllResults();
    }

    // Yıllık tamamlanan proje sayısını getir
    public function getYearlyCompletedProjectCount($year)
    {
        return $this->where('YEAR(start_date)', $year)
                    ->where('status', 'Tamamlandı')
                    ->countAllResults();
    }

    // Yıllık proje tutarını getir
    public function getYearlyProjectAmount($year)
    {
        $result = $this->selectSum('total_amount')
                      ->where('YEAR(start_date)', $year)
                      ->first();
        
        return $result['total_amount'] ?? 0;
    }

    /**
     * Belirli bir müşteriye ait projeleri getirir
     * 
     * @param int $customerId Müşteri ID
     * @return array Müşteriye ait projeler
     */
    public function getProjectsByCustomer($customerId)
    {
        $builder = $this->db->table($this->table);
        $builder->where('customer_id', $customerId);
        $query = $builder->get();
        
        return $query->getResultArray();
    }

    // Kategoriye göre projeleri getir
    public function getProjectsByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)->findAll();
    }

    /**
     * Proje süre analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Proje süre analizi
     */
    public function getProjectDurationAnalysis($year, $month)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, 
                         DATEDIFF(projects.end_date, projects.start_date) as planned_duration,
                         DATEDIFF(projects.updated_at, projects.start_date) as actual_duration,
                         DATEDIFF(projects.updated_at, projects.end_date) as delay_days');
        $builder->where('YEAR(projects.start_date)', $year);
        if ($month != '00') {
            $builder->where('MONTH(projects.start_date)', $month);
        }
        $builder->where('projects.status', 'Tamamlandı');
        $query = $builder->get();
        
        $projects = $query->getResultArray();
        $analysis = [
            'total_projects' => count($projects),
            'on_time_projects' => 0,
            'delayed_projects' => 0,
            'avg_planned_duration' => 0,
            'avg_actual_duration' => 0,
            'avg_delay' => 0,
            'max_delay' => 0,
            'projects' => $projects
        ];
        
        $total_planned_duration = 0;
        $total_actual_duration = 0;
        $total_delay = 0;
        
        foreach ($projects as $project) {
            if ($project['delay_days'] <= 0) {
                $analysis['on_time_projects']++;
            } else {
                $analysis['delayed_projects']++;
                $total_delay += $project['delay_days'];
                $analysis['max_delay'] = max($analysis['max_delay'], $project['delay_days']);
            }
            
            $total_planned_duration += $project['planned_duration'];
            $total_actual_duration += $project['actual_duration'];
        }
        
        if ($analysis['total_projects'] > 0) {
            $analysis['avg_planned_duration'] = round($total_planned_duration / $analysis['total_projects'], 1);
            $analysis['avg_actual_duration'] = round($total_actual_duration / $analysis['total_projects'], 1);
            $analysis['avg_delay'] = round($total_delay / $analysis['delayed_projects'], 1);
        }
        
        return $analysis;
    }

    /**
     * Gecikme analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Gecikme analizi
     */
    public function getDelayAnalysis($year, $month)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, 
                         DATEDIFF(projects.updated_at, projects.end_date) as delay_days,
                         categories.name as category_name,
                         customers.name as customer_name');
        $builder->join('categories', 'categories.id = projects.category_id');
        $builder->join('customers', 'customers.id = projects.customer_id');
        $builder->where('YEAR(projects.start_date)', $year);
        if ($month != '00') {
            $builder->where('MONTH(projects.start_date)', $month);
        }
        $builder->where('projects.status', 'Tamamlandı');
        $builder->where('projects.updated_at > projects.end_date');
        $query = $builder->get();
        
        $delayedProjects = $query->getResultArray();
        $analysis = [
            'total_delayed' => count($delayedProjects),
            'by_category' => [],
            'by_customer' => [],
            'projects' => $delayedProjects
        ];
        
        // Kategori bazlı gecikme analizi
        foreach ($delayedProjects as $project) {
            if (!isset($analysis['by_category'][$project['category_name']])) {
                $analysis['by_category'][$project['category_name']] = [
                    'count' => 0,
                    'total_delay' => 0,
                    'avg_delay' => 0
                ];
            }
            $analysis['by_category'][$project['category_name']]['count']++;
            $analysis['by_category'][$project['category_name']]['total_delay'] += $project['delay_days'];
        }
        
        foreach ($analysis['by_category'] as &$category) {
            $category['avg_delay'] = round($category['total_delay'] / $category['count'], 1);
        }
        
        // Müşteri bazlı gecikme analizi
        foreach ($delayedProjects as $project) {
            if (!isset($analysis['by_customer'][$project['customer_name']])) {
                $analysis['by_customer'][$project['customer_name']] = [
                    'count' => 0,
                    'total_delay' => 0,
                    'avg_delay' => 0
                ];
            }
            $analysis['by_customer'][$project['customer_name']]['count']++;
            $analysis['by_customer'][$project['customer_name']]['total_delay'] += $project['delay_days'];
        }
        
        foreach ($analysis['by_customer'] as &$customer) {
            $customer['avg_delay'] = round($customer['total_delay'] / $customer['count'], 1);
        }
        
        return $analysis;
    }

    /**
     * Sezonsal trendleri getirir
     * 
     * @param int $year Yıl
     * @return array Sezonsal trendler
     */
    public function getSeasonalTrends($year)
    {
        $builder = $this->db->table($this->table);
        $builder->select('MONTH(projects.start_date) as month,
                         COUNT(*) as project_count,
                         SUM(projects.total_amount) as total_amount,
                         COUNT(CASE WHEN projects.status = "Tamamlandı" THEN 1 END) as completed_count');
        $builder->where('YEAR(projects.start_date)', $year);
        $builder->groupBy('MONTH(projects.start_date)');
        $query = $builder->get();
        
        $monthlyData = $query->getResultArray();
        $trends = [
            'months' => [],
            'project_counts' => [],
            'total_amounts' => [],
            'completion_rates' => []
        ];
        
        // Tüm aylar için veri hazırla
        for ($i = 1; $i <= 12; $i++) {
            $trends['months'][] = $this->getMonthName($i);
            $trends['project_counts'][] = 0;
            $trends['total_amounts'][] = 0;
            $trends['completion_rates'][] = 0;
        }
        
        // Mevcut verileri yerleştir
        foreach ($monthlyData as $data) {
            $monthIndex = $data['month'] - 1;
            $trends['project_counts'][$monthIndex] = $data['project_count'];
            $trends['total_amounts'][$monthIndex] = $data['total_amount'];
            $trends['completion_rates'][$monthIndex] = $data['project_count'] > 0 
                ? round(($data['completed_count'] / $data['project_count']) * 100, 1)
                : 0;
        }
        
        return $trends;
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
        
        return $months[$month];
    }

    /**
     * Bütçe aşımı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Bütçe aşımı analizi
     */
    public function getBudgetOverrunAnalysis($year, $month)
    {
        $builder = $this->db->table('projects');
        $builder->select('projects.id, projects.name, projects.total_amount, projects.paid_amount, projects.start_date,
                         customers.name as customer_name,
                         categories.name as category_name,
                         (projects.total_amount - projects.paid_amount) as remaining_amount,
                         (projects.total_amount * 0.7) as estimated_cost,
                         ((projects.total_amount - (projects.total_amount * 0.7)) / (projects.total_amount * 0.7) * 100) as overrun_percentage');
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
            'overrun_projects' => [],
            'normal_projects' => [],
            'total_overrun' => 0,
            'average_overrun' => 0
        ];
        
        $totalOverrun = 0;
        $overrunCount = 0;
        
        foreach ($projects as $project) {
            // Bütçe aşımı %20'den fazla olan projeler
            if ($project['overrun_percentage'] > 20) {
                $analysis['overrun_projects'][] = $project;
                $totalOverrun += $project['overrun_percentage'];
                $overrunCount++;
            } else {
                $analysis['normal_projects'][] = $project;
            }
        }
        
        $analysis['total_overrun'] = $totalOverrun;
        $analysis['average_overrun'] = $overrunCount > 0 ? $totalOverrun / $overrunCount : 0;
        
        return $analysis;
    }

    /**
     * Kâr marjı analizini getirir
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Kâr marjı analizi
     */
    public function getProfitMarginAnalysis($year, $month)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, 
                         categories.name as category_name,
                         customers.name as customer_name,
                         (projects.total_amount - projects.paid_amount) as remaining_amount,
                         (projects.total_amount * 0.2) as estimated_cost,
                         (projects.total_amount - (projects.total_amount * 0.2)) as profit_margin,
                         ((projects.total_amount - (projects.total_amount * 0.2)) / projects.total_amount * 100) as profit_percentage');
        $builder->join('categories', 'categories.id = projects.category_id');
        $builder->join('customers', 'customers.id = projects.customer_id');
        $builder->where('YEAR(projects.start_date)', $year);
        if ($month != '00') {
            $builder->where('MONTH(projects.start_date)', $month);
        }
        $query = $builder->get();
        
        $projects = $query->getResultArray();
        $analysis = [
            'total_projects' => count($projects),
            'total_amount' => 0,
            'total_profit' => 0,
            'avg_profit_percentage' => 0,
            'by_category' => [],
            'by_customer' => [],
            'high_profit_projects' => [],
            'low_profit_projects' => []
        ];
        
        $total_profit_percentage = 0;
        
        foreach ($projects as $project) {
            $analysis['total_amount'] += $project['total_amount'];
            $analysis['total_profit'] += $project['profit_margin'];
            $total_profit_percentage += $project['profit_percentage'];
            
            // Kategori bazlı analiz
            if (!isset($analysis['by_category'][$project['category_name']])) {
                $analysis['by_category'][$project['category_name']] = [
                    'name' => $project['category_name'],
                    'count' => 0,
                    'project_count' => 0,
                    'total_amount' => 0,
                    'total_profit' => 0,
                    'profit_percentage' => 0
                ];
            }
            $analysis['by_category'][$project['category_name']]['count']++;
            $analysis['by_category'][$project['category_name']]['project_count']++;
            $analysis['by_category'][$project['category_name']]['total_amount'] += $project['total_amount'];
            $analysis['by_category'][$project['category_name']]['total_profit'] += $project['profit_margin'];
            
            // Müşteri bazlı analiz
            if (!isset($analysis['by_customer'][$project['customer_name']])) {
                $analysis['by_customer'][$project['customer_name']] = [
                    'count' => 0,
                    'total_amount' => 0,
                    'total_profit' => 0,
                    'avg_profit_percentage' => 0
                ];
            }
            $analysis['by_customer'][$project['customer_name']]['count']++;
            $analysis['by_customer'][$project['customer_name']]['total_amount'] += $project['total_amount'];
            $analysis['by_customer'][$project['customer_name']]['total_profit'] += $project['profit_margin'];
            
            // Yüksek ve düşük kârlı projeleri belirle
            if ($project['profit_percentage'] >= 25) {
                $analysis['high_profit_projects'][] = $project;
            } elseif ($project['profit_percentage'] <= 10) {
                $analysis['low_profit_projects'][] = $project;
            }
        }
        
        // Ortalama kâr yüzdesini hesapla
        $analysis['avg_profit_percentage'] = $analysis['total_projects'] > 0 ? 
            round($total_profit_percentage / $analysis['total_projects'], 2) : 0;
        
        // Kategori ve müşteri bazlı ortalama kâr yüzdelerini hesapla
        foreach ($analysis['by_category'] as &$category) {
            $category['profit_percentage'] = $category['count'] > 0 ? 
                round(($category['total_profit'] / $category['total_amount']) * 100, 2) : 0;
        }
        
        foreach ($analysis['by_customer'] as &$customer) {
            $customer['avg_profit_percentage'] = $customer['count'] > 0 ? 
                round(($customer['total_profit'] / $customer['total_amount']) * 100, 2) : 0;
        }
        
        return $analysis;
    }

    /**
     * Performans göstergelerini (KPI) hesaplar
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array KPI analizi
     */
    public function getKPIAnalysis($year, $month)
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, 
                         DATEDIFF(projects.end_date, projects.start_date) as planned_duration,
                         DATEDIFF(COALESCE(projects.updated_at, CURRENT_DATE), projects.start_date) as actual_duration,
                         (projects.total_amount - projects.paid_amount) as remaining_amount,
                         (projects.total_amount * 0.7) as estimated_cost');
        $builder->where('YEAR(projects.start_date)', $year);
        if ($month != '00') {
            $builder->where('MONTH(projects.start_date)', $month);
        }
        $query = $builder->get();
        
        $projects = $query->getResultArray();
        $analysis = [
            'total_projects' => count($projects),
            'success_rate' => [
                'on_time_count' => 0,
                'within_budget_count' => 0,
                'total_completed' => 0,
                'on_time_percentage' => 0,
                'within_budget_percentage' => 0,
                'overall_success_rate' => 0
            ],
            'project_efficiency' => [
                'avg_completion_time' => 0,
                'planned_vs_actual' => 0,
                'budget_efficiency' => 0
            ],
            'project_conversion' => [
                'total_proposals' => 0,
                'converted_projects' => 0,
                'conversion_rate' => 0
            ],
            'status_distribution' => [
                'Ödeme Bekliyor' => 0,
                'Başlamadı' => 0,
                'Devam Ediyor' => 0,
                'Tamamlandı' => 0,
                'Beklemede' => 0
            ]
        ];
        
        $total_planned_duration = 0;
        $total_actual_duration = 0;
        $total_budget_variance = 0;
        
        foreach ($projects as $project) {
            // Durum dağılımını hesapla
            $analysis['status_distribution'][$project['status']]++;
            
            // Tamamlanan projeler için başarı oranlarını hesapla
            if ($project['status'] === 'Tamamlandı') {
                $analysis['success_rate']['total_completed']++;
                
                // Zamanında tamamlanan projeler
                if ($project['actual_duration'] <= $project['planned_duration']) {
                    $analysis['success_rate']['on_time_count']++;
                }
                
                // Bütçe dahilinde tamamlanan projeler
                if ($project['paid_amount'] <= $project['estimated_cost']) {
                    $analysis['success_rate']['within_budget_count']++;
                }
                
                $total_planned_duration += $project['planned_duration'];
                $total_actual_duration += $project['actual_duration'];
                $total_budget_variance += ($project['paid_amount'] - $project['estimated_cost']);
            }
        }
        
        // Başarı oranlarını hesapla
        if ($analysis['success_rate']['total_completed'] > 0) {
            $analysis['success_rate']['on_time_percentage'] = round(
                ($analysis['success_rate']['on_time_count'] / $analysis['success_rate']['total_completed']) * 100, 
                1
            );
            $analysis['success_rate']['within_budget_percentage'] = round(
                ($analysis['success_rate']['within_budget_count'] / $analysis['success_rate']['total_completed']) * 100, 
                1
            );
            $analysis['success_rate']['overall_success_rate'] = round(
                (($analysis['success_rate']['on_time_count'] + $analysis['success_rate']['within_budget_count']) / 
                ($analysis['success_rate']['total_completed'] * 2)) * 100,
                1
            );
            
            // Verimlilik metriklerini hesapla
            $analysis['project_efficiency']['avg_completion_time'] = round(
                $total_actual_duration / $analysis['success_rate']['total_completed'],
                1
            );
            $analysis['project_efficiency']['planned_vs_actual'] = $total_planned_duration > 0 ? round(
                ($total_actual_duration / $total_planned_duration) * 100,
                1
            ) : 0;
            $analysis['project_efficiency']['budget_efficiency'] = round(
                ($total_budget_variance / $analysis['success_rate']['total_completed']),
                2
            );
        }
        
        // Proje dönüşüm oranını hesapla (örnek veriler)
        $analysis['project_conversion']['total_proposals'] = $analysis['total_projects'] + rand(1, 5); // Gerçek veride proposal tablosundan gelecek
        $analysis['project_conversion']['converted_projects'] = $analysis['total_projects'];
        $analysis['project_conversion']['conversion_rate'] = $analysis['project_conversion']['total_proposals'] > 0 ? 
            round(($analysis['project_conversion']['converted_projects'] / $analysis['project_conversion']['total_proposals']) * 100, 1) : 0;
        
        return $analysis;
    }

    /**
     * Karşılaştırmalı analizleri hesaplar
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array Karşılaştırmalı analiz sonuçları
     */
    public function getComparativeAnalysis($year, $month)
    {
        $analysis = [
            'yearly_comparison' => [
                'current_year' => [
                    'year' => $year,
                    'total_projects' => 0,
                    'completed_projects' => 0,
                    'total_amount' => 0,
                    'avg_project_value' => 0,
                    'completion_rate' => 0
                ],
                'previous_year' => [
                    'year' => $year - 1,
                    'total_projects' => 0,
                    'completed_projects' => 0,
                    'total_amount' => 0,
                    'avg_project_value' => 0,
                    'completion_rate' => 0
                ],
                'growth' => [
                    'projects' => 0,
                    'amount' => 0,
                    'completion_rate' => 0
                ]
            ],
            'target_vs_actual' => [
                'projects' => [
                    'target' => 0,
                    'actual' => 0,
                    'achievement_rate' => 0
                ],
                'revenue' => [
                    'target' => 0,
                    'actual' => 0,
                    'achievement_rate' => 0
                ],
                'completion_rate' => [
                    'target' => 85, // Hedef tamamlanma oranı %85
                    'actual' => 0,
                    'achievement_rate' => 0
                ]
            ],
            'monthly_trends' => []
        ];

        // Mevcut yıl analizi
        $currentYearData = $this->getYearlyData($year);
        $analysis['yearly_comparison']['current_year'] = $currentYearData;

        // Önceki yıl analizi
        $previousYearData = $this->getYearlyData($year - 1);
        $analysis['yearly_comparison']['previous_year'] = $previousYearData;

        // Büyüme oranlarını hesapla
        if ($previousYearData['total_projects'] > 0) {
            $analysis['yearly_comparison']['growth']['projects'] = round(
                (($currentYearData['total_projects'] - $previousYearData['total_projects']) / 
                $previousYearData['total_projects']) * 100,
                1
            );
        }

        if ($previousYearData['total_amount'] > 0) {
            $analysis['yearly_comparison']['growth']['amount'] = round(
                (($currentYearData['total_amount'] - $previousYearData['total_amount']) / 
                $previousYearData['total_amount']) * 100,
                1
            );
        }

        if ($previousYearData['completion_rate'] > 0) {
            $analysis['yearly_comparison']['growth']['completion_rate'] = round(
                $currentYearData['completion_rate'] - $previousYearData['completion_rate'],
                1
            );
        }

        // Hedef vs Gerçekleşen analizi
        // Not: Hedefler şu an sabit değerler, gerçek uygulamada bir hedefler tablosundan çekilebilir
        $yearlyTarget = $this->getYearlyTargets($year);
        
        $analysis['target_vs_actual']['projects']['target'] = $yearlyTarget['project_count'];
        $analysis['target_vs_actual']['projects']['actual'] = $currentYearData['total_projects'];
        $analysis['target_vs_actual']['projects']['achievement_rate'] = $yearlyTarget['project_count'] > 0 ? 
            round(($currentYearData['total_projects'] / $yearlyTarget['project_count']) * 100, 1) : 0;

        $analysis['target_vs_actual']['revenue']['target'] = $yearlyTarget['revenue'];
        $analysis['target_vs_actual']['revenue']['actual'] = $currentYearData['total_amount'];
        $analysis['target_vs_actual']['revenue']['achievement_rate'] = $yearlyTarget['revenue'] > 0 ? 
            round(($currentYearData['total_amount'] / $yearlyTarget['revenue']) * 100, 1) : 0;

        $analysis['target_vs_actual']['completion_rate']['actual'] = $currentYearData['completion_rate'];
        $analysis['target_vs_actual']['completion_rate']['achievement_rate'] = 
            round(($currentYearData['completion_rate'] / $analysis['target_vs_actual']['completion_rate']['target']) * 100, 1);

        // Aylık trendler
        for ($i = 1; $i <= 12; $i++) {
            $currentMonthData = $this->getMonthlyData($year, sprintf('%02d', $i));
            $previousMonthData = $this->getMonthlyData($year - 1, sprintf('%02d', $i));

            $analysis['monthly_trends'][] = [
                'month' => $this->getMonthName($i),
                'current_year' => $currentMonthData,
                'previous_year' => $previousMonthData,
                'growth' => [
                    'projects' => $previousMonthData['total_projects'] > 0 ? 
                        round((($currentMonthData['total_projects'] - $previousMonthData['total_projects']) / 
                        $previousMonthData['total_projects']) * 100, 1) : 0,
                    'amount' => $previousMonthData['total_amount'] > 0 ? 
                        round((($currentMonthData['total_amount'] - $previousMonthData['total_amount']) / 
                        $previousMonthData['total_amount']) * 100, 1) : 0
                ]
            ];
        }

        return $analysis;
    }

    /**
     * Yıllık hedefleri getirir
     * Not: Gerçek uygulamada bu veriler bir hedefler tablosundan çekilebilir
     */
    private function getYearlyTargets($year)
    {
        // Örnek hedefler (gerçek uygulamada veritabanından gelecek)
        return [
            'project_count' => 24, // Yıllık hedef proje sayısı
            'revenue' => 500000, // Yıllık hedef gelir
            'completion_rate' => 85 // Hedef tamamlanma oranı
        ];
    }

    /**
     * Yıllık verileri getirir
     */
    private function getYearlyData($year)
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(*) as total_projects, 
                         COUNT(CASE WHEN status = "Tamamlandı" THEN 1 END) as completed_projects,
                         SUM(total_amount) as total_amount');
        $builder->where('YEAR(start_date)', $year);
        $result = $builder->get()->getRowArray();

        $totalProjects = (int)$result['total_projects'];
        $completedProjects = (int)$result['completed_projects'];
        $totalAmount = (float)$result['total_amount'];

        return [
            'year' => $year,
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'total_amount' => $totalAmount,
            'avg_project_value' => $totalProjects > 0 ? round($totalAmount / $totalProjects, 2) : 0,
            'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0
        ];
    }

    /**
     * Aylık verileri getirir
     */
    private function getMonthlyData($year, $month)
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(*) as total_projects, 
                         COUNT(CASE WHEN status = "Tamamlandı" THEN 1 END) as completed_projects,
                         SUM(total_amount) as total_amount');
        $builder->where('YEAR(start_date)', $year);
        $builder->where('MONTH(start_date)', $month);
        $result = $builder->get()->getRowArray();

        $totalProjects = (int)$result['total_projects'];
        $completedProjects = (int)$result['completed_projects'];
        $totalAmount = (float)$result['total_amount'];

        return [
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'total_amount' => $totalAmount,
            'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 1) : 0
        ];
    }

    /**
     * Filtrelenmiş projeleri getirir
     * 
     * @param array $filters Filtre parametreleri
     * @param string $startDate Başlangıç tarihi
     * @param string $endDate Bitiş tarihi
     * @return array Filtrelenmiş projeler
     */
    public function getFilteredProjects($filters, $startDate, $endDate)
    {
        try {
            $builder = $this->db->table($this->table);
            $builder->select('projects.*, customers.name as customer_name, categories.name as category_name');
            $builder->join('customers', 'customers.id = projects.customer_id');
            $builder->join('categories', 'categories.id = projects.category_id');
            
            // Tarih aralığı filtresi
            $builder->where('projects.start_date >=', $startDate);
            $builder->where('projects.start_date <=', $endDate);
            
            // Kategori filtresi
            if (!empty($filters['category_id'])) {
                $builder->where('projects.category_id', $filters['category_id']);
            }
            
            // Müşteri filtresi
            if (!empty($filters['customer_id'])) {
                $builder->where('projects.customer_id', $filters['customer_id']);
            }
            
            // Durum filtresi
            if (!empty($filters['status'])) {
                $builder->where('projects.status', $filters['status']);
            }
            
            // Öncelik filtresi
            if (!empty($filters['priority'])) {
                $builder->where('projects.priority', $filters['priority']);
            }
            
            // Bütçe aralığı filtresi
            if (!empty($filters['min_amount'])) {
                $builder->where('projects.total_amount >=', $filters['min_amount']);
            }
            if (!empty($filters['max_amount'])) {
                $builder->where('projects.total_amount <=', $filters['max_amount']);
            }
            
            // Proje süresi filtresi
            if (!empty($filters['min_duration'])) {
                $builder->where('DATEDIFF(projects.end_date, projects.start_date) >=', $filters['min_duration']);
            }
            if (!empty($filters['max_duration'])) {
                $builder->where('DATEDIFF(projects.end_date, projects.start_date) <=', $filters['max_duration']);
            }
            
            // Ödeme durumu filtresi
            if (!empty($filters['payment_status'])) {
                switch ($filters['payment_status']) {
                    case 'paid':
                        $builder->where('projects.paid_amount >= projects.total_amount');
                        break;
                    case 'partial':
                        $builder->where('projects.paid_amount > 0');
                        $builder->where('projects.paid_amount < projects.total_amount');
                        break;
                    case 'unpaid':
                        $builder->where('projects.paid_amount', 0);
                        break;
                }
            }
            
            $query = $builder->get();
            $result = $query->getResultArray();
            
            // Sonuçları kontrol et
            if ($result === null) {
                return [];
            }
            
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'getFilteredProjects hatası: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Proje tamamlanma süresi tahmini
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array
     */
    public function getCompletionTimePrediction($year, $month)
    {
        // Son 6 ayın verilerini al
        $startDate = date('Y-m-d', strtotime("$year-$month-01 -6 months"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));

        $builder = $this->db->table($this->table);
        $builder->select('category_id, AVG(DATEDIFF(end_date, start_date)) as avg_duration');
        $builder->where('start_date >=', $startDate);
        $builder->where('start_date <=', $endDate);
        $builder->where('status', 'Tamamlandı');
        $builder->groupBy('category_id');
        $query = $builder->get();

        $predictions = [];
        foreach ($query->getResultArray() as $row) {
            $predictions[$row['category_id']] = round($row['avg_duration']);
        }

        return $predictions;
    }

    /**
     * Bütçe aşımı tahmini
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array
     */
    public function getBudgetOverrunPrediction($year, $month)
    {
        // Son 6 ayın verilerini al
        $startDate = date('Y-m-d', strtotime("$year-$month-01 -6 months"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));

        $builder = $this->db->table($this->table);
        $builder->select('category_id, 
            AVG(total_amount) as avg_total_amount,
            AVG(paid_amount) as avg_paid_amount');
        $builder->where('start_date >=', $startDate);
        $builder->where('start_date <=', $endDate);
        $builder->where('status', 'Tamamlandı');
        $builder->groupBy('category_id');
        $query = $builder->get();

        $predictions = [];
        foreach ($query->getResultArray() as $row) {
            $overrunPercentage = $row['avg_total_amount'] > 0 ? 
                (($row['avg_total_amount'] - $row['avg_paid_amount']) / $row['avg_total_amount'] * 100) : 0;
            
            $predictions[$row['category_id']] = [
                'overrun_percentage' => round($overrunPercentage, 1),
                'estimated_amount' => round($row['avg_total_amount'], 2),
                'estimated_paid' => round($row['avg_paid_amount'], 2)
            ];
        }

        return $predictions;
    }

    /**
     * Proje başarı tahmini
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array
     */
    public function getProjectSuccessPrediction($year, $month)
    {
        // Son 6 ayın verilerini al
        $startDate = date('Y-m-d', strtotime("$year-$month-01 -6 months"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));

        $builder = $this->db->table($this->table);
        $builder->select('category_id, 
            COUNT(*) as total_projects,
            SUM(CASE WHEN status = "Tamamlandı" AND paid_amount >= total_amount THEN 1 ELSE 0 END) as successful_projects');
        $builder->where('start_date >=', $startDate);
        $builder->where('start_date <=', $endDate);
        $builder->groupBy('category_id');
        $query = $builder->get();

        $predictions = [];
        foreach ($query->getResultArray() as $row) {
            $successRate = $row['total_projects'] > 0 ? 
                ($row['successful_projects'] / $row['total_projects'] * 100) : 0;
            
            $predictions[$row['category_id']] = [
                'success_rate' => round($successRate, 1),
                'total_projects' => $row['total_projects'],
                'successful_projects' => $row['successful_projects']
            ];
        }

        return $predictions;
    }

    /**
     * Kaynak ihtiyacı tahmini
     * 
     * @param int $year Yıl
     * @param int $month Ay
     * @return array
     */
    public function getResourceNeedPrediction($year, $month)
    {
        // Son 6 ayın verilerini al
        $startDate = date('Y-m-d', strtotime("$year-$month-01 -6 months"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));

        $builder = $this->db->table($this->table);
        $builder->select('category_id, 
            AVG(total_amount / DATEDIFF(end_date, start_date)) as avg_daily_cost,
            AVG(DATEDIFF(end_date, start_date)) as avg_duration');
        $builder->where('start_date >=', $startDate);
        $builder->where('start_date <=', $endDate);
        $builder->where('status', 'Tamamlandı');
        $builder->groupBy('category_id');
        $query = $builder->get();

        $predictions = [];
        foreach ($query->getResultArray() as $row) {
            $predictions[$row['category_id']] = [
                'daily_cost' => round($row['avg_daily_cost'], 2),
                'duration' => round($row['avg_duration']),
                'total_cost' => round($row['avg_daily_cost'] * $row['avg_duration'], 2)
            ];
        }

        return $predictions;
    }

    /**
     * Tüm projeleri müşteri ve kategori bilgileriyle birlikte getirir
     * 
     * @return array Tüm projeler
     */
    public function getAllProjects()
    {
        $builder = $this->db->table($this->table);
        $builder->select('projects.*, customers.name as customer_name, categories.name as category_name');
        $builder->join('customers', 'customers.id = projects.customer_id');
        $builder->join('categories', 'categories.id = projects.category_id', 'left');
        return $builder->get()->getResultArray();
    }
}
