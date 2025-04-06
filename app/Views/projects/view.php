<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Proje Detayı</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('payments/create?project_id=' . $project['id']) ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-money-bill-wave"></i> Ödeme Yap
                        </a>
                        <a href="<?= base_url('projects/edit/' . $project['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="<?= base_url('projects') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Proje Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Proje Adı</th>
                                    <td><?= esc($project['name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Müşteri</th>
                                    <td><?= esc($project['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td><?= esc($project['category_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Öncelik</th>
                                    <td>
                                        <span class="badge badge-<?= $project['priority'] === 'Acil' ? 'danger' : ($project['priority'] === 'Yüksek' ? 'warning' : ($project['priority'] === 'Orta' ? 'info' : 'secondary')) ?>">
                                            <?= esc($project['priority']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Durum</th>
                                    <td>
                                        <span class="badge badge-<?= $project['status'] === 'Tamamlandı' ? 'success' : ($project['status'] === 'Devam Ediyor' ? 'primary' : ($project['status'] === 'Beklemede' ? 'warning' : ($project['status'] === 'Başlamadı' ? 'secondary' : 'danger'))) ?>">
                                            <?= esc($project['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Başlangıç Tarihi</th>
                                    <td><?= date('d/m/Y', strtotime($project['start_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Bitiş Tarihi</th>
                                    <td><?= $project['end_date'] ? date('d/m/Y', strtotime($project['end_date'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Toplam Tutar</th>
                                    <td class="text-right"><?= number_format($project['total_amount'], 2, ',', '.') ?> ₺</td>
                                </tr>
                                <tr>
                                    <th>Ödenen Tutar</th>
                                    <td class="text-right"><?= number_format($project['paid_amount'], 2, ',', '.') ?> ₺</td>
                                </tr>
                                <tr>
                                    <th>Kalan Tutar</th>
                                    <td class="text-right"><?= number_format($project['total_amount'] - $project['paid_amount'], 2, ',', '.') ?> ₺</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Müşteri Bilgileri</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Müşteri Adı</th>
                                    <td><?= esc($project['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>E-posta</th>
                                    <td><?= esc($project['customer_email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Telefon</th>
                                    <td><?= esc($project['customer_phone']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Açıklama</h5>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(esc($project['description'])) ?: 'Açıklama bulunmuyor.' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Görev</h5>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br(esc($project['task'])) ?: 'Görev bulunmuyor.' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Proje Notları Bölümü -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Proje Notları</h5>
                                <a href="<?= base_url('projects/notes/' . $project['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sticky-note"></i> Tüm Notları Görüntüle
                                </a>
                            </div>
                            
                            <?php if (empty($recentNotes)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Bu proje için henüz not eklenmemiş.
                                </div>
                            <?php else: ?>
                                <div class="timeline">
                                    <?php foreach ($recentNotes as $note): ?>
                                        <div class="time-label">
                                            <span class="bg-primary"><?= date('d.m.Y H:i', strtotime($note['created_at'])) ?></span>
                                        </div>
                                        <div>
                                            <i class="fas fa-sticky-note bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time">
                                                    <i class="fas fa-clock"></i> <?= date('H:i', strtotime($note['created_at'])) ?>
                                                </span>
                                                <h3 class="timeline-header">Proje Notu</h3>
                                                <div class="timeline-body">
                                                    <?= nl2br(esc($note['note'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 