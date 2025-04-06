<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Raporlar</h3>
                    <div class="card-tools">
                        <form action="<?= base_url('reports') ?>" method="get" class="d-inline-block mr-2">
                            <div class="input-group">
                                <select name="year" class="form-control form-control-sm">
                                    <?php foreach ($years as $y): ?>
                                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="month" class="form-control form-control-sm">
                                    <?php foreach ($months as $m => $monthName): ?>
                                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= $monthName ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-primary">Filtrele</button>
                                </div>
                            </div>
                        </form>
                        <a href="<?= base_url('reports/exportPDF') ?>" class="btn btn-sm btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF'e Aktar
                        </a>
                        <a href="<?= base_url('reports/exportExcel') ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel"></i> Excel'e Aktar
                        </a>
                        <a href="<?= base_url('reports/archive') ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-archive"></i> Rapor Arşivi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Aylık İstatistikler -->
                    <div class="row">
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Müşteri İstatistikleri</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <canvas id="customerChart"></canvas>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Müşteri</th>
                                                            <th>Proje Sayısı</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($customerStats['customerProjects'] as $customer): ?>
                                                        <tr>
                                                            <td><?= $customer['name'] ?></td>
                                                            <td class="text-center"><?= $customer['project_count'] ?></td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Bazlı Analizler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kategori Bazlı Analizler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Kategori Dağılımı -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Kategori Dağılımı</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="categoryDistributionChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Kategori Performansı -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Kategori Performansı</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="categoryPerformanceChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Kategori Karlılığı -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Kategori Karlılığı</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="categoryProfitChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Kategori Detaylı Tablo -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Kategori Detaylı Analiz</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Kategori</th>
                                                                    <th class="text-center">Toplam Proje</th>
                                                                    <th class="text-center">Tamamlanan</th>
                                                                    <th class="text-center">Tamamlanma Oranı</th>
                                                                    <th class="text-center">Ort. Tamamlanma Süresi (Gün)</th>
                                                                    <th class="text-right">Toplam Tutar</th>
                                                                    <th class="text-right">Ödenen</th>
                                                                    <th class="text-right">Bekleyen</th>
                                                                    <th class="text-right">Kâr Marjı</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($categoryStats as $category): ?>
                                                                <tr>
                                                                    <td><?= $category['name'] ?></td>
                                                                    <td class="text-center"><?= $category['totalProjects'] ?></td>
                                                                    <td class="text-center"><?= $category['completedProjects'] ?></td>
                                                                    <td class="text-center">%<?= $category['completionRate'] ?></td>
                                                                    <td class="text-center"><?= $category['avgCompletionTime'] ?></td>
                                                                    <td class="text-right"><?= number_format($category['totalAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($category['paidAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($category['pendingAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($category['profitMargin'], 2) ?> ₺</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Müşteri Bazlı Analizler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Müşteri Bazlı Analizler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Müşteri Segmentasyonu -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Müşteri Segmentasyonu</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="customerSegmentChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Müşteri Sadakati -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Müşteri Sadakati</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="customerLoyaltyChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Müşteri Karlılığı -->
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Müşteri Karlılığı</h4>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="customerProfitabilityChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Müşteri Detaylı Tablo -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Müşteri Detaylı Analiz</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Müşteri</th>
                                                                    <th>İletişim</th>
                                                                    <th class="text-center">Toplam Proje</th>
                                                                    <th class="text-center">Tamamlanan</th>
                                                                    <th class="text-right">Toplam Tutar</th>
                                                                    <th class="text-right">Ödenen</th>
                                                                    <th class="text-right">Bekleyen</th>
                                                                    <th class="text-right">Ort. Proje Değeri</th>
                                                                    <th class="text-center">Ödeme Performansı</th>
                                                                    <th>Segment</th>
                                                                    <th>Sadakat</th>
                                                                    <th>Karlılık</th>
                                                                    <th>Son Proje</th>
                                                                    <th>Son Ödeme</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($customerAnalysis['customers'] as $customer): ?>
                                                                <tr>
                                                                    <td><?= $customer['name'] ?></td>
                                                                    <td>
                                                                        <?= $customer['email'] ?><br>
                                                                        <?= $customer['phone'] ?>
                                                                    </td>
                                                                    <td class="text-center"><?= $customer['totalProjects'] ?></td>
                                                                    <td class="text-center"><?= $customer['completedProjects'] ?></td>
                                                                    <td class="text-right"><?= number_format($customer['totalAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($customer['paidAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($customer['pendingAmount'], 2) ?> ₺</td>
                                                                    <td class="text-right"><?= number_format($customer['avgProjectValue'], 2) ?> ₺</td>
                                                                    <td class="text-center">%<?= $customer['paymentPerformance'] ?></td>
                                                                    <td>
                                                                        <?php if ($customer['segment'] == 'Yüksek Değerli'): ?>
                                                                            <span class="badge badge-success">Yüksek Değerli</span>
                                                                        <?php elseif ($customer['segment'] == 'Orta Değerli'): ?>
                                                                            <span class="badge badge-warning">Orta Değerli</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-secondary">Düşük Değerli</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($customer['loyalty'] == 'Sadık'): ?>
                                                                            <span class="badge badge-success">Sadık</span>
                                                                        <?php elseif ($customer['loyalty'] == 'Tekrarlayan'): ?>
                                                                            <span class="badge badge-info">Tekrarlayan</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-secondary">Yeni</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if ($customer['profitability'] == 'Yüksek'): ?>
                                                                            <span class="badge badge-success">Yüksek</span>
                                                                        <?php elseif ($customer['profitability'] == 'Orta'): ?>
                                                                            <span class="badge badge-warning">Orta</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-danger">Düşük</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= $customer['lastProjectDate'] ? date('d.m.Y', strtotime($customer['lastProjectDate'])) : '-' ?></td>
                                                                    <td><?= $customer['lastPaymentDate'] ? date('d.m.Y', strtotime($customer['lastPaymentDate'])) : '-' ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detaylı İstatistikler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detaylı İstatistikler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info"><i class="fas fa-project-diagram"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Toplam Proje</span>
                                                    <span class="info-box-number"><?= $monthlyStats['totalProjects'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Tamamlanan Proje</span>
                                                    <span class="info-box-number"><?= $monthlyStats['completedProjects'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-money-bill"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Toplam Tutar</span>
                                                    <span class="info-box-number"><?= number_format($monthlyStats['totalAmount'], 2) ?> ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Bekleyen Ödeme</span>
                                                    <span class="info-box-number"><?= number_format($monthlyStats['pendingAmount'], 2) ?> ₺</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zaman Bazlı Analizler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Zaman Bazlı Analizler</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Proje Süre Analizi -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Proje Süre Analizi</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Ortalama Planlanan Süre</span>
                                                                    <span class="info-box-number"><?= $projectDurationAnalysis['avg_planned_duration'] ?> gün</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Ortalama Gerçekleşen Süre</span>
                                                                    <span class="info-box-number"><?= $projectDurationAnalysis['avg_actual_duration'] ?> gün</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Zamanında Tamamlanan</span>
                                                                    <span class="info-box-number"><?= $projectDurationAnalysis['on_time_projects'] ?> proje</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Geciken</span>
                                                                    <span class="info-box-number"><?= $projectDurationAnalysis['delayed_projects'] ?> proje</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gecikme Analizi -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Gecikme Analizi</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-times"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Ortalama Gecikme</span>
                                                                    <span class="info-box-number"><?= $delayAnalysis['total_delayed'] > 0 ? $projectDurationAnalysis['avg_delay'] : 0 ?> gün</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Maksimum Gecikme</span>
                                                                    <span class="info-box-number"><?= $projectDurationAnalysis['max_delay'] ?> gün</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sezonsal Trendler -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Sezonsal Trendler</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <canvas id="seasonalProjectChart"></canvas>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <canvas id="seasonalAmountChart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detaylı Tablolar -->
                                    <div class="row mt-4">
                                        <!-- Geciken Projeler Tablosu -->
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Geciken Projeler</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Proje</th>
                                                                    <th>Kategori</th>
                                                                    <th>Müşteri</th>
                                                                    <th class="text-center">Planlanan Süre</th>
                                                                    <th class="text-center">Gerçekleşen Süre</th>
                                                                    <th class="text-center">Gecikme (Gün)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($delayAnalysis['projects'] as $project): ?>
                                                                <tr>
                                                                    <td><?= $project['name'] ?></td>
                                                                    <td><?= $project['category_name'] ?></td>
                                                                    <td><?= $project['customer_name'] ?></td>
                                                                    <td class="text-center"><?= $project['planned_duration'] ?> gün</td>
                                                                    <td class="text-center"><?= $project['actual_duration'] ?> gün</td>
                                                                    <td class="text-center"><?= $project['delay_days'] ?> gün</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                                    <!-- Nakit Akışı Tahmini -->
                                    <div class="row">
                                        <div class="col-12">
                                            <h4>Nakit Akışı Tahmini</h4>
                                            <canvas id="cashFlowChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>

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
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Proje</th>
                                                                    <th>Müşteri</th>
                                                                    <th>Toplam Tutar</th>
                                                                    <th>Tahmini Maliyet</th>
                                                                    <th>Aşım Oranı</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($budgetOverrunAnalysis['overrun_projects'] ?? [] as $project): ?>
                                                                <tr>
                                                                    <td><?= $project['name'] ?></td>
                                                                    <td><?= $project['customer_name'] ?></td>
                                                                    <td><?= number_format($project['total_amount'], 2) ?> ₺</td>
                                                                    <td><?= number_format($project['estimated_cost'], 2) ?> ₺</td>
                                                                    <td><?= number_format($project['overrun_percentage'], 1) ?>%</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
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
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Kategori</th>
                                                                    <th>Proje Sayısı</th>
                                                                    <th>Toplam Tutar</th>
                                                                    <th>Toplam Kâr</th>
                                                                    <th>Kâr Marjı</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($profitMarginAnalysis['by_category'] ?? [] as $category): ?>
                                                                <tr>
                                                                    <td><?= $category['name'] ?></td>
                                                                    <td><?= $category['project_count'] ?></td>
                                                                    <td><?= number_format($category['total_amount'], 2) ?> ₺</td>
                                                                    <td><?= number_format($category['total_profit'], 2) ?> ₺</td>
                                                                    <td><?= number_format($category['profit_percentage'], 1) ?>%</td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ödeme Performansı Analizi -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Ödeme Performansı Analizi</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Zamanında Ödeme</span>
                                                                    <span class="info-box-number"><?= $paymentPerformanceAnalysis['on_time_payments'] ?? 0 ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Gecikmeli Ödeme</span>
                                                                    <span class="info-box-number"><?= $paymentPerformanceAnalysis['late_payments'] ?? 0 ?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-info"><i class="fas fa-money-bill-wave"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Toplam Tahsilat</span>
                                                                    <span class="info-box-number"><?= number_format($paymentPerformanceAnalysis['total_paid'] ?? 0, 2) ?> ₺</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="info-box">
                                                                <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                                                                <div class="info-box-content">
                                                                    <span class="info-box-text">Bekleyen Tahsilat</span>
                                                                    <span class="info-box-number"><?= number_format($paymentPerformanceAnalysis['total_remaining'] ?? 0, 2) ?> ₺</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive mt-3">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Müşteri</th>
                                                                    <th>Proje Sayısı</th>
                                                                    <th>Toplam Tutar</th>
                                                                    <th>Tahsil Edilen</th>
                                                                    <th>Bekleyen</th>
                                                                    <th>Zamanında</th>
                                                                    <th>Gecikmeli</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($paymentPerformanceAnalysis['by_customer'] ?? [] as $customer => $stats): ?>
                                                                <tr>
                                                                    <td><?= $customer ?></td>
                                                                    <td><?= $stats['count'] ?></td>
                                                                    <td><?= number_format($stats['total_amount'], 2) ?> ₺</td>
                                                                    <td><?= number_format($stats['total_paid'], 2) ?> ₺</td>
                                                                    <td><?= number_format($stats['remaining_amount'], 2) ?> ₺</td>
                                                                    <td><?= $stats['on_time_count'] ?></td>
                                                                    <td><?= $stats['late_count'] ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
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
                                                    <!-- Proje Başarı Oranı -->
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

                                                    <!-- Proje Verimliliği -->
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Proje Verimliliği</h3>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="table-responsive">
                                                                        <table class="table table-bordered">
                                                                            <tr>
                                                                                <th>Ortalama Tamamlanma Süresi</th>
                                                                                <td><?= $kpiAnalysis['project_efficiency']['avg_completion_time'] ?> gün</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Planlanan/Gerçekleşen Oran</th>
                                                                                <td><?= number_format($kpiAnalysis['project_efficiency']['planned_vs_actual'], 1) ?>%</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Bütçe Verimliliği</th>
                                                                                <td><?= number_format($kpiAnalysis['project_efficiency']['budget_efficiency'], 2) ?> ₺</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Proje Durumu Dağılımı</h3>
                                                                </div>
                                                                <div class="card-body">
                                                                    <canvas id="projectStatusChart" style="min-height: 250px;"></canvas>
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

                    <!-- Tahminsel Analizler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tahminsel Analizler</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Kategori Bazlı Tahminler -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Tahmini Tamamlanma Süresi (Gün)</th>
                                                    <th>Tahmini Bütçe Aşımı (%)</th>
                                                    <th>Tahmini Başarı Oranı (%)</th>
                                                    <th>Günlük Kaynak İhtiyacı (₺)</th>
                                                    <th>Toplam Kaynak İhtiyacı (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($predictive_analysis as $category => $predictions): ?>
                                                <tr>
                                                    <td><?= $category ?></td>
                                                    <td class="text-center"><?= $predictions['completion_time'] ?></td>
                                                    <td class="text-center"><?= number_format($predictions['budget_overrun']['overrun_percentage'], 1) ?></td>
                                                    <td class="text-center"><?= number_format($predictions['success_rate']['success_rate'], 1) ?></td>
                                                    <td class="text-end"><?= number_format($predictions['resource_need']['daily_cost'], 2) ?></td>
                                                    <td class="text-end"><?= number_format($predictions['resource_need']['total_cost'], 2) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Tahminsel Grafikler -->
                                    <div class="row mt-4">
                                        <!-- Tamamlanma Süresi Tahmini -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Tahmini Tamamlanma Süreleri</h3>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="completionTimeChart" style="min-height: 250px;"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Başarı Oranı Tahmini -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Tahmini Başarı Oranları</h3>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="successRateChart" style="min-height: 250px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kaynak İhtiyacı Tahmini -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Tahmini Kaynak İhtiyaçları</h3>
                                                </div>
                                                <div class="card-body">
                                                    <canvas id="resourceNeedChart" style="min-height: 250px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Karşılaştırmalı Analizler Bölümü -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Karşılaştırmalı Analizler</h5>
                        </div>
                        <div class="card-body">
                            <!-- Yıllık Karşılaştırma -->
                            <div class="mb-4">
                                <h6 class="mb-3">Yıllık Karşılaştırma</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Proje Sayısı</h6>
                                                <p class="mb-1">Bu Yıl: <?= $comparative_analysis['yearly_comparison']['current_year']['total_projects'] ?></p>
                                                <p class="mb-1">Geçen Yıl: <?= $comparative_analysis['yearly_comparison']['previous_year']['total_projects'] ?></p>
                                                <p class="mb-0 <?= $comparative_analysis['yearly_comparison']['growth']['projects'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    Büyüme: <?= $comparative_analysis['yearly_comparison']['growth']['projects'] ?>%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Toplam Gelir</h6>
                                                <p class="mb-1">Bu Yıl: <?= number_format($comparative_analysis['yearly_comparison']['current_year']['total_amount'], 2) ?> ₺</p>
                                                <p class="mb-1">Geçen Yıl: <?= number_format($comparative_analysis['yearly_comparison']['previous_year']['total_amount'], 2) ?> ₺</p>
                                                <p class="mb-0 <?= $comparative_analysis['yearly_comparison']['growth']['amount'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    Büyüme: <?= $comparative_analysis['yearly_comparison']['growth']['amount'] ?>%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Tamamlanma Oranı</h6>
                                                <p class="mb-1">Bu Yıl: <?= $comparative_analysis['yearly_comparison']['current_year']['completion_rate'] ?>%</p>
                                                <p class="mb-1">Geçen Yıl: <?= $comparative_analysis['yearly_comparison']['previous_year']['completion_rate'] ?>%</p>
                                                <p class="mb-0 <?= $comparative_analysis['yearly_comparison']['growth']['completion_rate'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    Değişim: <?= $comparative_analysis['yearly_comparison']['growth']['completion_rate'] ?>%
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hedef vs Gerçekleşen -->
                            <div class="mb-4">
                                <h6 class="mb-3">Hedef vs Gerçekleşen</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Proje Sayısı</h6>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= $comparative_analysis['target_vs_actual']['projects']['achievement_rate'] ?>%"
                                                         aria-valuenow="<?= $comparative_analysis['target_vs_actual']['projects']['achievement_rate'] ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?= $comparative_analysis['target_vs_actual']['projects']['achievement_rate'] ?>%
                                                    </div>
                                                </div>
                                                <p class="mb-1">Hedef: <?= $comparative_analysis['target_vs_actual']['projects']['target'] ?></p>
                                                <p class="mb-0">Gerçekleşen: <?= $comparative_analysis['target_vs_actual']['projects']['actual'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Gelir</h6>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= $comparative_analysis['target_vs_actual']['revenue']['achievement_rate'] ?>%"
                                                         aria-valuenow="<?= $comparative_analysis['target_vs_actual']['revenue']['achievement_rate'] ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?= $comparative_analysis['target_vs_actual']['revenue']['achievement_rate'] ?>%
                                                    </div>
                                                </div>
                                                <p class="mb-1">Hedef: <?= number_format($comparative_analysis['target_vs_actual']['revenue']['target'], 2) ?> ₺</p>
                                                <p class="mb-0">Gerçekleşen: <?= number_format($comparative_analysis['target_vs_actual']['revenue']['actual'], 2) ?> ₺</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">Tamamlanma Oranı</h6>
                                                <div class="progress mb-2">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= $comparative_analysis['target_vs_actual']['completion_rate']['achievement_rate'] ?>%"
                                                         aria-valuenow="<?= $comparative_analysis['target_vs_actual']['completion_rate']['achievement_rate'] ?>" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        <?= $comparative_analysis['target_vs_actual']['completion_rate']['achievement_rate'] ?>%
                                                    </div>
                                                </div>
                                                <p class="mb-1">Hedef: <?= $comparative_analysis['target_vs_actual']['completion_rate']['target'] ?>%</p>
                                                <p class="mb-0">Gerçekleşen: <?= $comparative_analysis['target_vs_actual']['completion_rate']['actual'] ?>%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aylık Trendler -->
                            <div>
                                <h6 class="mb-3">Aylık Trendler</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ay</th>
                                                <th>Proje Sayısı (Bu Yıl)</th>
                                                <th>Proje Sayısı (Geçen Yıl)</th>
                                                <th>Büyüme (%)</th>
                                                <th>Gelir (Bu Yıl)</th>
                                                <th>Gelir (Geçen Yıl)</th>
                                                <th>Büyüme (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($comparative_analysis['monthly_trends'] as $trend): ?>
                                            <tr>
                                                <td><?= $trend['month'] ?></td>
                                                <td><?= $trend['current_year']['total_projects'] ?></td>
                                                <td><?= $trend['previous_year']['total_projects'] ?></td>
                                                <td class="<?= $trend['growth']['projects'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= $trend['growth']['projects'] ?>%
                                                </td>
                                                <td><?= number_format($trend['current_year']['total_amount'], 2) ?> ₺</td>
                                                <td><?= number_format($trend['previous_year']['total_amount'], 2) ?> ₺</td>
                                                <td class="<?= $trend['growth']['amount'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= $trend['growth']['amount'] ?>%
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İnteraktif Raporlama Araçları -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">İnteraktif Raporlama Araçları</h3>
                </div>
                <div class="card-body">
                    <!-- Filtreleme Formu -->
                    <form id="filterForm" class="mb-4">
                        <div class="row">
                            <!-- Tarih Aralığı -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" name="start_date" value="<?= date('Y-m-01') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bitiş Tarihi</label>
                                    <input type="date" class="form-control" name="end_date" value="<?= date('Y-m-t') ?>" required>
                                </div>
                            </div>
                            
                            <!-- Kategori ve Müşteri -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select class="form-control" name="category_id">
                                        <option value="">Tümü</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Müşteri</label>
                                    <select class="form-control" name="customer_id">
                                        <option value="">Tümü</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <!-- Durum ve Öncelik -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Durum</label>
                                    <select class="form-control" name="status">
                                        <option value="">Tümü</option>
                                        <option value="completed">Tamamlandı</option>
                                        <option value="ongoing">Devam Ediyor</option>
                                        <option value="not_started">Başlamadı</option>
                                        <option value="on_hold">Beklemede</option>
                                        <option value="cancelled">İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Öncelik</label>
                                    <select class="form-control" name="priority">
                                        <option value="">Tümü</option>
                                        <option value="low">Düşük</option>
                                        <option value="medium">Orta</option>
                                        <option value="high">Yüksek</option>
                                        <option value="urgent">Acil</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Bütçe Aralığı -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Min. Bütçe</label>
                                    <input type="number" class="form-control" name="min_amount" min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Max. Bütçe</label>
                                    <input type="number" class="form-control" name="max_amount" min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <!-- Proje Süresi -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Min. Süre (Gün)</label>
                                    <input type="number" class="form-control" name="min_duration" min="0">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Max. Süre (Gün)</label>
                                    <input type="number" class="form-control" name="max_duration" min="0">
                                </div>
                            </div>
                            
                            <!-- Ödeme Durumu -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ödeme Durumu</label>
                                    <select class="form-control" name="payment_status">
                                        <option value="">Tümü</option>
                                        <option value="paid">Ödenmiş</option>
                                        <option value="partial">Kısmi Ödenmiş</option>
                                        <option value="unpaid">Ödenmemiş</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Filtreleme Butonları -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filtrele
                                        </button>
                                        <button type="reset" class="btn btn-secondary">
                                            <i class="fas fa-undo"></i> Sıfırla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Filtrelenmiş Sonuçlar -->
                    <div id="filteredResults">
                        <!-- Projeler Tablosu -->
                        <div class="table-responsive mb-4">
                            <h4>Filtrelenmiş Projeler</h4>
                            <table class="table table-bordered table-striped" id="projectsTable">
                                <thead>
                                    <tr>
                                        <th>Proje Adı</th>
                                        <th>Müşteri</th>
                                        <th>Kategori</th>
                                        <th>Durum</th>
                                        <th>Öncelik</th>
                                        <th>Başlangıç</th>
                                        <th>Bitiş</th>
                                        <th>Toplam Tutar</th>
                                        <th>Ödenen</th>
                                        <th>Kalan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JavaScript ile doldurulacak -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Ödemeler Tablosu -->
                        <div class="table-responsive">
                            <h4>Filtrelenmiş Ödemeler</h4>
                            <table class="table table-bordered table-striped" id="paymentsTable">
                                <thead>
                                    <tr>
                                        <th>Proje</th>
                                        <th>Müşteri</th>
                                        <th>Tutar</th>
                                        <th>Ödeme Tarihi</th>
                                        <th>Açıklama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JavaScript ile doldurulacak -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Görselleştirme İyileştirmeleri -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Görselleştirme İyileştirmeleri</h3>
                </div>
                <div class="card-body">
                    <!-- Proje Yoğunluğu Isı Haritası -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Proje Yoğunluğu Isı Haritası</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="heatMapChart" style="min-height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gantt Şeması -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Proje Zaman Çizelgesi</h4>
                                </div>
                                <div class="card-body">
                                    <div id="ganttChart" style="height: 400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bubble Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Proje Analiz Grafiği</h4>
                                    <small class="text-muted">Süre (X) - Tutar (Y) - Ödeme Oranı (Büyüklük)</small>
                                </div>
                                <div class="card-body">
                                    <canvas id="bubbleChart" style="min-height: 300px;"></canvas>
                                </div>
                            </div>
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

    // Müşteri İstatistikleri Grafiği
    const customerCtx = document.getElementById('customerChart').getContext('2d');
    new Chart(customerCtx, {
        type: 'pie',
        data: {
            labels: [
                <?php foreach ($customerStats['customerProjects'] as $customer): ?>
                '<?= $customer['name'] ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [
                    <?php foreach ($customerStats['customerProjects'] as $customer): ?>
                    <?= $customer['project_count'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Kategori Dağılımı Grafiği
    const categoryDistributionCtx = document.getElementById('categoryDistributionChart').getContext('2d');
    new Chart(categoryDistributionCtx, {
        type: 'pie',
        data: {
            labels: [
                <?php foreach ($categoryStats as $category): ?>
                '<?= $category['name'] ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [
                    <?php foreach ($categoryStats as $category): ?>
                    <?= $category['totalProjects'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(201, 203, 207, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(54, 162, 235, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(201, 203, 207, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Kategori Performansı Grafiği
    const categoryPerformanceCtx = document.getElementById('categoryPerformanceChart').getContext('2d');
    new Chart(categoryPerformanceCtx, {
        type: 'bar',
        data: {
            labels: [
                <?php foreach ($categoryStats as $category): ?>
                '<?= $category['name'] ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Tamamlanma Oranı (%)',
                data: [
                    <?php foreach ($categoryStats as $category): ?>
                    <?= $category['completionRate'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Kategori Karlılığı Grafiği
    const categoryProfitCtx = document.getElementById('categoryProfitChart').getContext('2d');
    new Chart(categoryProfitCtx, {
        type: 'bar',
        data: {
            labels: [
                <?php foreach ($categoryStats as $category): ?>
                '<?= $category['name'] ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Kâr Marjı (₺)',
                data: [
                    <?php foreach ($categoryStats as $category): ?>
                    <?= $category['profitMargin'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
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
                legend: {
                    position: 'bottom'
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
                legend: {
                    position: 'bottom'
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
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Sezonsal Proje Sayısı Grafiği
    const seasonalProjectCtx = document.getElementById('seasonalProjectChart').getContext('2d');
    new Chart(seasonalProjectCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($seasonalTrends['months']) ?>,
            datasets: [{
                label: 'Proje Sayısı',
                data: <?= json_encode($seasonalTrends['project_counts']) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Aylık Proje Sayısı Trendi'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Sezonsal Tutar Grafiği
    const seasonalAmountCtx = document.getElementById('seasonalAmountChart').getContext('2d');
    new Chart(seasonalAmountCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($seasonalTrends['months']) ?>,
            datasets: [{
                label: 'Toplam Tutar (₺)',
                data: <?= json_encode($seasonalTrends['total_amounts']) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Aylık Toplam Tutar Trendi'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('tr-TR', {
                                style: 'currency',
                                currency: 'TRY'
                            });
                        }
                    }
                }
            }
        }
    });

    // Filtreleme formu
    const filterForm = document.getElementById('filterForm');
    const projectsTable = document.getElementById('projectsTable').getElementsByTagName('tbody')[0];
    const paymentsTable = document.getElementById('paymentsTable').getElementsByTagName('tbody')[0];

    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Form verilerini al
        const formData = new FormData(filterForm);
        
        // AJAX isteği gönder
        fetch('<?= base_url('reports/filter') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Sunucu hatası: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Gelen veri:', data);
            
            // Projeleri tabloya ekle
            projectsTable.innerHTML = '';
            if (data.data && data.data.projects && Array.isArray(data.data.projects)) {
                console.log('Projeler:', data.data.projects);
                data.data.projects.forEach(project => {
                    const row = projectsTable.insertRow();
                    row.innerHTML = `
                        <td>${project.name || ''}</td>
                        <td>${project.customer_name || ''}</td>
                        <td>${project.category_name || ''}</td>
                        <td>${project.status || ''}</td>
                        <td>${project.priority || ''}</td>
                        <td>${project.start_date || ''}</td>
                        <td>${project.end_date || '-'}</td>
                        <td>${project.total_amount || '0'}</td>
                        <td>${project.paid_amount || '0'}</td>
                        <td>${(project.total_amount || 0) - (project.paid_amount || 0)}</td>
                    `;
                });
            } else {
                console.log('Proje verisi bulunamadı veya geçersiz format');
                projectsTable.innerHTML = '<tr><td colspan="10" class="text-center">Proje bulunamadı</td></tr>';
            }
            
            // Ödemeleri tabloya ekle
            paymentsTable.innerHTML = '';
            if (data.data && data.data.payments && Array.isArray(data.data.payments)) {
                console.log('Ödemeler:', data.data.payments);
                data.data.payments.forEach(payment => {
                    const row = paymentsTable.insertRow();
                    row.innerHTML = `
                        <td>${payment.project_name || ''}</td>
                        <td>${payment.customer_name || ''}</td>
                        <td>${payment.amount || '0'}</td>
                        <td>${payment.payment_date || ''}</td>
                        <td>${payment.description || '-'}</td>
                    `;
                });
            } else {
                console.log('Ödeme verisi bulunamadı veya geçersiz format');
                paymentsTable.innerHTML = '<tr><td colspan="5" class="text-center">Ödeme bulunamadı</td></tr>';
            }
        })
        .catch(error => {
            console.error('Filtreleme hatası:', error);
            alert('Filtreleme sırasında bir hata oluştu: ' + error.message);
            
            // Tabloları temizle
            projectsTable.innerHTML = '<tr><td colspan="10" class="text-center">Veri yüklenirken hata oluştu</td></tr>';
            paymentsTable.innerHTML = '<tr><td colspan="5" class="text-center">Veri yüklenirken hata oluştu</td></tr>';
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">

<script>


// Isı Haritası
const heatMapCtx = document.getElementById('heatMapChart').getContext('2d');
const heatMapData = <?= json_encode($heatMapData['data']) ?>;
const weekDays = <?= json_encode($heatMapData['weekDays']) ?>;

new Chart(heatMapCtx, {
    type: 'scatter',
    data: {
        datasets: [{
            label: 'Proje Yoğunluğu',
            data: heatMapData,
            backgroundColor(context) {
                const value = context.raw.value;
                const alpha = Math.min(value / 10, 1); // Maksimum 10 proje için normalize et
                return `rgba(75, 192, 192, ${alpha})`;
            },
            pointRadius: 25,
            pointHoverRadius: 30,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                type: 'linear',
                position: 'bottom',
                min: -0.5,
                max: 6.5,
                ticks: {
                    callback: function(value) {
                        return weekDays[value] || '';
                    }
                },
                grid: {
                    display: false
                }
            },
            y: {
                type: 'linear',
                min: -0.5,
                max: 5.5,
                ticks: {
                    callback: function(value) {
                        return `Hafta ${value + 1}`;
                    }
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const v = context.raw;
                        return [`${weekDays[v.x]}, Hafta ${v.y + 1}`, `${v.value} proje`];
                    }
                }
            },
            legend: {
                display: false
            }
        }
    }
});

// Gantt Şeması
const ganttTasks = <?= json_encode($ganttChartData) ?>;
const ganttChart = new Gantt("#ganttChart", ganttTasks, {
    header_height: 50,
    column_width: 30,
    step: 24,
    view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
    bar_height: 20,
    bar_corner_radius: 3,
    arrow_curve: 5,
    padding: 18,
    view_mode: 'Month',
    date_format: 'YYYY-MM-DD',
    language: 'tr',
    custom_popup_html: function(task) {
        return `
            <div class="details-container">
                <h5>${task.name}</h5>
                <p>
                    <strong>Başlangıç:</strong> ${task.start}<br>
                    <strong>Bitiş:</strong> ${task.end}<br>
                    <strong>İlerleme:</strong> %${task.progress}
                </p>
            </div>
        `;
    }
});

// Bubble Chart
const bubbleCtx = document.getElementById('bubbleChart').getContext('2d');
const bubbleData = <?= json_encode($bubbleChartData) ?>;

new Chart(bubbleCtx, {
    type: 'bubble',
    data: {
        datasets: [{
            label: 'Projeler',
            data: bubbleData,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Süre (Gün)'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Toplam Tutar (₺)'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('tr-TR', {
                            style: 'currency',
                            currency: 'TRY'
                        }).format(value);
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const data = context.raw;
                        return [
                            data.name,
                            `Süre: ${data.x} gün`,
                            `Tutar: ${new Intl.NumberFormat('tr-TR', {
                                style: 'currency',
                                currency: 'TRY'
                            }).format(data.y)}`,
                            `Ödeme Oranı: %${Math.round((data.r / 20) * 100)}`
                        ];
                    }
                }
            }
        }
    }
});

</script>

<!-- Gantt Chart Stilleri -->
<style>
.gantt .bar {
    fill: #2c3e50;
}

.gantt .bar-tamamlandı {
    fill: #2ecc71;
}

.gantt .bar-devam-ediyor {
    fill: #3498db;
}

.gantt .bar-başlamadı {
    fill: #95a5a6;
}

.gantt .bar-beklemede {
    fill: #f1c40f;
}

.gantt .bar-ödeme-bekliyor {
    fill: #e74c3c;
}

.gantt .lower-text, .gantt .upper-text {
    font-size: 12px;
}

.gantt .grid-header {
    fill: #ffffff;
    stroke: #e0e0e0;
}

.gantt .grid-row {
    fill: #ffffff;
}

.gantt .grid-row:nth-child(even) {
    fill: #f5f5f5;
}

.gantt .row-line {
    stroke: #ebeff2;
}

.gantt .tick {
    stroke: #e0e0e0;
}

.gantt .today-highlight {
    fill: #fcf8e3;
    opacity: 0.5;
}

.gantt .arrow {
    fill: none;
    stroke: #666;
    stroke-width: 1.4;
}

.details-container {
    background: white;
    padding: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 4px;
}

.details-container h5 {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #2c3e50;
}

.details-container p {
    margin: 0;
    font-size: 12px;
    color: #7f8c8d;
}
</style>


</script>
<?= $this->endSection() ?>
