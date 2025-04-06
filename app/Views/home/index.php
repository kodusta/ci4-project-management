<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Filtreleme -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url() ?>" method="get" class="form-inline">
                        <div class="form-group mx-sm-3 mb-2">
                            <select name="year" class="form-control">
                                <?php foreach ($years as $y): ?>
                                    <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mx-sm-3 mb-2">
                            <select name="month" class="form-control">
                                <?php foreach ($months as $m => $monthName): ?>
                                    <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= $monthName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Filtrele</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrik Kartları -->
    <div class="row">
        <!-- Toplam Proje -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $metrics['totalProjects'] ?></h3>
                    <p>Toplam Proje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>

        <!-- Tamamlanan Proje -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $metrics['completedProjects'] ?></h3>
                    <p>Tamamlanan Proje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>

        <!-- Devam Eden Proje -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= $metrics['ongoingProjects'] ?></h3>
                    <p>Devam Eden Proje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <!-- Bekleyen Proje -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= $metrics['pendingProjects'] ?></h3>
                    <p>Bekleyen Proje</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Finansal Metrikler -->
    <div class="row">
        <!-- Toplam Tutar -->
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Toplam Tutar</span>
                    <span class="info-box-number"><?= number_format($metrics['totalAmount'], 2) ?> ₺</span>
                </div>
            </div>
        </div>

        <!-- Ödenen Tutar -->
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ödenen Tutar</span>
                    <span class="info-box-number"><?= number_format($metrics['paidAmount'], 2) ?> ₺</span>
                </div>
            </div>
        </div>

        <!-- Bekleyen Tutar -->
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bekleyen Tutar</span>
                    <span class="info-box-number"><?= number_format($metrics['pendingAmount'], 2) ?> ₺</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafikler -->
    <div class="row">
        <!-- Aylık İstatistikler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aylık İstatistikler</h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Yıllık İstatistikler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yıllık İstatistikler</h3>
                </div>
                <div class="card-body">
                    <canvas id="yearlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Yaklaşan Proje Bitiş Tarihleri -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock text-warning"></i> Yaklaşan Proje Bitiş Tarihleri</h3>
                </div>
                <div class="card-body">
                    <?php
                    $today = new DateTime();
                    $twoWeeksFromNow = (new DateTime())->modify('+2 weeks');
                    $upcomingDeadlines = [];
                    
                    foreach ($activeProjects as $project) {
                        if (!empty($project['end_date'])) {
                            $endDate = new DateTime($project['end_date']);
                            if ($endDate <= $twoWeeksFromNow && $endDate >= $today) {
                                $upcomingDeadlines[] = $project;
                            }
                        }
                    }
                    
                    if (!empty($upcomingDeadlines)): ?>
                        <div class="alert alert-warning" id="deadline-alert">
                            <p>Aşağıdaki projelerin bitiş tarihine 2 hafta veya daha az kaldı:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Proje Adı</th>
                                            <th>Müşteri</th>
                                            <th>Kalan Gün</th>
                                            <th>Bitiş Tarihi</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcomingDeadlines as $project): 
                                            $endDate = new DateTime($project['end_date']);
                                            $daysLeft = $today->diff($endDate)->days;
                                        ?>
                                            <tr>
                                                <td><?= esc($project['name']) ?></td>
                                                <td><?= esc($project['customer_name']) ?></td>
                                                <td><?= $daysLeft ?> gün</td>
                                                <td><?= date('d.m.Y', strtotime($project['end_date'])) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= getStatusBadgeClass($project['status']) ?>">
                                                        <?= $project['status'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <p>Yaklaşan bitiş tarihine sahip proje bulunmamaktadır.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Projeler ve En Çok Projeye Sahip Müşteriler -->
    <div class="row">
        <!-- Son Projeler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Projeler</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Proje Adı</th>
                                <th>Müşteri</th>
                                <th>Durum</th>
                                <th>Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProjects as $project): ?>
                                <tr>
                                    <td><?= $project['name'] ?></td>
                                    <td><?= $project['customer_name'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= getStatusBadgeClass($project['status']) ?>">
                                            <?= $project['status'] ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($project['total_amount'], 2) ?> ₺</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- En Çok Projeye Sahip Müşteriler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">En Çok Projeye Sahip Müşteriler</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Müşteri</th>
                                <th>Proje Sayısı</th>
                                <th>Toplam Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topCustomers as $customer): ?>
                                <tr>
                                    <td><?= $customer['name'] ?></td>
                                    <td><?= $customer['project_count'] ?></td>
                                    <td><?= number_format($customer['total_amount'], 2) ?> ₺</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Finansal Analizler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Finansal Analizler</h3>
                </div>
                <div class="card-body">
                    

                    <!-- Bütçe Aşımı ve Kâr Marjı Analizi -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bütçe Aşımı Analizi</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Aşım Oranı > %20</span>
                                                    <span class="info-box-number"><?= count($budgetOverrunAnalysis['overrun_projects'] ?? []) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Normal Bütçe</span>
                                                    <span class="info-box-number"><?= count($budgetOverrunAnalysis['normal_projects'] ?? []) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kâr Marjı Analizi</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Ortalama Kâr Marjı</span>
                                                    <span class="info-box-number"><?= number_format($profitMarginAnalysis['average_profit_percentage'] ?? 0, 1) ?>%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Toplam Kâr</span>
                                                    <span class="info-box-number"><?= number_format($profitMarginAnalysis['total_profit'] ?? 0, 2) ?> ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performans Göstergeleri (KPI) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performans Göstergeleri (KPI)</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Genel Başarı Oranı</span>
                                    <span class="info-box-number"><?= number_format($kpiAnalysis['success_rate']['overall_success_rate'], 1) ?>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Zamanında Tamamlama</span>
                                    <span class="info-box-number"><?= number_format($kpiAnalysis['success_rate']['on_time_percentage'], 1) ?>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bütçe Uyumu</span>
                                    <span class="info-box-number"><?= number_format($kpiAnalysis['success_rate']['within_budget_percentage'], 1) ?>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Dönüşüm Oranı</span>
                                    <span class="info-box-number"><?= number_format($kpiAnalysis['project_conversion']['conversion_rate'], 1) ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Müşteri Analizi -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Müşteri Analizi</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <canvas id="customerSegmentChart" style="min-height: 250px;"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="customerLoyaltyChart" style="min-height: 250px;"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="customerProfitabilityChart" style="min-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tahminsel Analizler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tahminsel Analizler</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="completionTimeChart" style="min-height: 250px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="successRateChart" style="min-height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aylık İstatistikler Grafiği
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Toplam Proje', 'Tamamlanan Proje', 'Toplam Tutar', 'Ödenen Tutar', 'Bekleyen Tutar'],
            datasets: [{
                label: '<?= $months[$month] ?> <?= $year ?>',
                data: [
                    <?= $monthlyStats['totalProjects'] ?>,
                    <?= $monthlyStats['completedProjects'] ?>,
                    <?= $monthlyStats['totalAmount'] ?>,
                    <?= $monthlyStats['paidAmount'] ?>,
                    <?= $monthlyStats['pendingAmount'] ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Yıllık İstatistikler Grafiği
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: ['Toplam Proje', 'Tamamlanan Proje', 'Toplam Tutar', 'Ödenen Tutar', 'Bekleyen Tutar'],
            datasets: [{
                label: '<?= $year ?>',
                data: [
                    <?= $yearlyStats['totalProjects'] ?>,
                    <?= $yearlyStats['completedProjects'] ?>,
                    <?= $yearlyStats['totalAmount'] ?>,
                    <?= $yearlyStats['paidAmount'] ?>,
                    <?= $yearlyStats['pendingAmount'] ?>
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Müşteri Segmentasyonu Grafiği
    const customerSegmentCtx = document.getElementById('customerSegmentChart').getContext('2d');
    new Chart(customerSegmentCtx, {
        type: 'pie',
        data: {
            labels: ['Yüksek Değerli', 'Orta Değerli', 'Düşük Değerli'],
            datasets: [{
                data: [
                    <?= $customerAnalysis['segmentStats']['Yüksek Değerli'] ?>,
                    <?= $customerAnalysis['segmentStats']['Orta Değerli'] ?>,
                    <?= $customerAnalysis['segmentStats']['Düşük Değerli'] ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(201, 203, 207, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Müşteri Segmentasyonu'
                }
            }
        }
    });

    // Müşteri Sadakati Grafiği
    const customerLoyaltyCtx = document.getElementById('customerLoyaltyChart').getContext('2d');
    new Chart(customerLoyaltyCtx, {
        type: 'pie',
        data: {
            labels: ['Sadık', 'Tekrarlayan', 'Yeni'],
            datasets: [{
                data: [
                    <?= $customerAnalysis['loyaltyStats']['Sadık'] ?>,
                    <?= $customerAnalysis['loyaltyStats']['Tekrarlayan'] ?>,
                    <?= $customerAnalysis['loyaltyStats']['Yeni'] ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(201, 203, 207, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Müşteri Sadakati'
                }
            }
        }
    });

    // Müşteri Karlılığı Grafiği
    const customerProfitabilityCtx = document.getElementById('customerProfitabilityChart').getContext('2d');
    new Chart(customerProfitabilityCtx, {
        type: 'pie',
        data: {
            labels: ['Yüksek', 'Orta', 'Düşük'],
            datasets: [{
                data: [
                    <?= $customerAnalysis['profitabilityStats']['Yüksek'] ?>,
                    <?= $customerAnalysis['profitabilityStats']['Orta'] ?>,
                    <?= $customerAnalysis['profitabilityStats']['Düşük'] ?>
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Müşteri Karlılığı'
                }
            }
        }
    });

    // Tamamlanma Süresi Tahmini Grafiği
    const completionTimeCtx = document.getElementById('completionTimeChart').getContext('2d');
    new Chart(completionTimeCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($predictive_analysis)) ?>,
            datasets: [{
                label: 'Tahmini Tamamlanma Süresi (Gün)',
                data: <?= json_encode(array_column($predictive_analysis, 'completion_time')) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Kategori Bazlı Tamamlanma Süresi Tahmini'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Başarı Oranı Tahmini Grafiği
    const successRateCtx = document.getElementById('successRateChart').getContext('2d');
    new Chart(successRateCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($predictive_analysis)) ?>,
            datasets: [{
                label: 'Tahmini Başarı Oranı (%)',
                data: <?= json_encode(array_column(array_column($predictive_analysis, 'success_rate'), 'success_rate')) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Kategori Bazlı Başarı Oranı Tahmini'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?> 