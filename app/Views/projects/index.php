<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Projeler</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('projects/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Proje
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success alert-dismissible auto-hide">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible auto-hide">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Yaklaşan Bitiş Tarihi Uyarısı -->
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
                        <h5><i class="fas fa-clock"></i> Yaklaşan Proje Bitiş Tarihleri</h5>
                        <p>Aşağıdaki projelerin bitiş tarihine 2 hafta veya daha az kaldı:</p>
                        <ul>
                            <?php foreach ($upcomingDeadlines as $project): 
                                $endDate = new DateTime($project['end_date']);
                                $daysLeft = $today->diff($endDate)->days;
                            ?>
                                <li>
                                    <strong><?= esc($project['name']) ?></strong> - 
                                    <?= $daysLeft ?> gün kaldı (Bitiş: <?= date('d.m.Y', strtotime($project['end_date'])) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Tab Menüsü -->
                    <ul class="nav nav-tabs mb-3" id="projectTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">
                                <i class="fas fa-spinner"></i> Aktif Projeler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab">
                                <i class="fas fa-check-circle"></i> Tamamlanan Projeler
                            </a>
                        </li>
                    </ul>

                    <!-- Tab İçerikleri -->
                    <div class="tab-content" id="projectTabsContent">
                        <!-- Aktif Projeler -->
                        <div class="tab-pane fade show active" id="active" role="tabpanel">
                            <!-- Masaüstü Tablo Görünümü -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Proje Adı</th>
                                                <th>Müşteri</th>
                                                <th>Kategori</th>
                                                <th>Öncelik</th>
                                                <th>Durum</th>
                                                <th>Başlangıç</th>
                                                <th>Bitiş</th>
                                                <th>Tutar</th>
                                                <th>Kalan Tutar</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($activeProjects as $project) : ?>
                                                <tr class="<?= getPriorityClass($project['priority']) ?>">
                                                    <td><?= $project['id'] ?></td>
                                                    <td>
                                                        <?= esc($project['name']) ?>
                                                        <?php if ($project['total_amount'] <= $project['paid_amount'] && $project['status'] != 'Tamamlandı'): ?>
                                                            <span class="badge bg-warning text-dark ms-2">
                                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                                Ödeme Tamamlandı
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($project['customer_name']) ?></td>
                                                    <td><?= esc($project['category_name']) ?></td>
                                                    <td>
                                                        <select name="priority" class="form-control priority-select <?= getPrioritySelectClass($project['priority']) ?>" data-project-id="<?= $project['id'] ?>">
                                                            <option value="Düşük" <?= $project['priority'] == 'Düşük' ? 'selected' : '' ?>>Düşük</option>
                                                            <option value="Orta" <?= $project['priority'] == 'Orta' ? 'selected' : '' ?>>Orta</option>
                                                            <option value="Yüksek" <?= $project['priority'] == 'Yüksek' ? 'selected' : '' ?>>Yüksek</option>
                                                            <option value="Acil" <?= $project['priority'] == 'Acil' ? 'selected' : '' ?>>Acil</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="status" class="form-control status-select <?= getStatusSelectClass($project['status']) ?>" data-project-id="<?= $project['id'] ?>">
                                                            <option value="Başlamadı" <?= $project['status'] == 'Başlamadı' ? 'selected' : '' ?>>Başlamadı</option>
                                                            <option value="Devam Ediyor" <?= $project['status'] == 'Devam Ediyor' ? 'selected' : '' ?>>Devam Ediyor</option>
                                                            <option value="Beklemede" <?= $project['status'] == 'Beklemede' ? 'selected' : '' ?>>Beklemede</option>
                                                            <option value="Ödeme Bekliyor" <?= $project['status'] == 'Ödeme Bekliyor' ? 'selected' : '' ?>>Ödeme Bekliyor</option>
                                                            <option value="Tamamlandı" <?= $project['status'] == 'Tamamlandı' ? 'selected' : '' ?>>Tamamlandı</option>
                                                        </select>
                                                    </td>
                                                    <td><?= date('d.m.Y', strtotime($project['start_date'])) ?></td>
                                                    <td><?= $project['end_date'] ? date('d.m.Y', strtotime($project['end_date'])) : '-' ?></td>
                                                    <td><?= number_format($project['total_amount'], 2, ',', '.') ?> ₺</td>
                                                    <td><?= number_format($project['total_amount'] - $project['paid_amount'], 2, ',', '.') ?> ₺</td>
                                                    <td>
                                                        <a href="<?= base_url('projects/view/' . $project['id']) ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/edit/' . $project['id']) ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/notes/' . $project['id']) ?>" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/delete/' . $project['id']) ?>" class="btn btn-danger btn-sm delete-project" data-project-id="<?= $project['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Mobil Kart Görünümü -->
                            <div class="d-md-none">
                                <div class="row">
                                    <?php foreach ($activeProjects as $project) : ?>
                                        <div class="col-12 mb-3">
                                            <div class="card <?= getPriorityClass($project['priority']) ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <?= esc($project['name']) ?>
                                                        <?php if ($project['total_amount'] <= $project['paid_amount'] && $project['status'] != 'Tamamlandı'): ?>
                                                            <span class="badge bg-warning text-dark ms-2">
                                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                                Ödeme Tamamlandı
                                                            </span>
                                                        <?php endif; ?>
                                                    </h5>
                                                    <div class="card-text">
                                                        <p class="mb-1">
                                                            <i class="fas fa-user text-primary"></i>
                                                            <?= esc($project['customer_name']) ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-folder text-secondary"></i>
                                                            <?= esc($project['category_name']) ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-flag text-warning"></i>
                                                            <select name="priority" class="form-control priority-select <?= getPrioritySelectClass($project['priority']) ?>" data-project-id="<?= $project['id'] ?>">
                                                                <option value="Düşük" <?= $project['priority'] == 'Düşük' ? 'selected' : '' ?>>Düşük</option>
                                                                <option value="Orta" <?= $project['priority'] == 'Orta' ? 'selected' : '' ?>>Orta</option>
                                                                <option value="Yüksek" <?= $project['priority'] == 'Yüksek' ? 'selected' : '' ?>>Yüksek</option>
                                                                <option value="Acil" <?= $project['priority'] == 'Acil' ? 'selected' : '' ?>>Acil</option>
                                                            </select>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-tasks text-info"></i>
                                                            <select name="status" class="form-control status-select <?= getStatusSelectClass($project['status']) ?>" data-project-id="<?= $project['id'] ?>">
                                                                <option value="Başlamadı" <?= $project['status'] == 'Başlamadı' ? 'selected' : '' ?>>Başlamadı</option>
                                                                <option value="Devam Ediyor" <?= $project['status'] == 'Devam Ediyor' ? 'selected' : '' ?>>Devam Ediyor</option>
                                                                <option value="Beklemede" <?= $project['status'] == 'Beklemede' ? 'selected' : '' ?>>Beklemede</option>
                                                                <option value="Ödeme Bekliyor" <?= $project['status'] == 'Ödeme Bekliyor' ? 'selected' : '' ?>>Ödeme Bekliyor</option>
                                                                <option value="Tamamlandı" <?= $project['status'] == 'Tamamlandı' ? 'selected' : '' ?>>Tamamlandı</option>
                                                            </select>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-calendar text-success"></i>
                                                            <?= date('d.m.Y', strtotime($project['start_date'])) ?> - 
                                                            <?= $project['end_date'] ? date('d.m.Y', strtotime($project['end_date'])) : '-' ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-money-bill-wave text-danger"></i>
                                                            <?= number_format($project['total_amount'], 2, ',', '.') ?> ₺
                                                        </p>
                                                        <p class="mb-3">
                                                            <i class="fas fa-money-bill-alt text-warning"></i>
                                                            Kalan: <?= number_format($project['total_amount'] - $project['paid_amount'], 2, ',', '.') ?> ₺
                                                        </p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <a href="<?= base_url('projects/view/' . $project['id']) ?>" class="btn btn-info btn-sm me-2">
                                                            <i class="fas fa-eye"></i> Görüntüle
                                                        </a>
                                                        <a href="<?= base_url('projects/edit/' . $project['id']) ?>" class="btn btn-primary btn-sm me-2">
                                                            <i class="fas fa-edit"></i> Düzenle
                                                        </a>
                                                        <a href="<?= base_url('projects/notes/' . $project['id']) ?>" class="btn btn-warning btn-sm me-2">
                                                            <i class="fas fa-sticky-note"></i> Notlar
                                                        </a>
                                                        <a href="<?= base_url('projects/delete/' . $project['id']) ?>" class="btn btn-danger btn-sm delete-project" data-project-id="<?= $project['id'] ?>">
                                                            <i class="fas fa-trash"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tamamlanan Projeler -->
                        <div class="tab-pane fade" id="completed" role="tabpanel">
                            <!-- Masaüstü Tablo Görünümü -->
                            <div class="d-none d-md-block">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Proje Adı</th>
                                                <th>Müşteri</th>
                                                <th>Kategori</th>
                                                <th>Başlangıç</th>
                                                <th>Bitiş</th>
                                                <th>Tutar</th>
                                                <th>Kalan Tutar</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($completedProjects as $project) : ?>
                                                <tr>
                                                    <td><?= $project['id'] ?></td>
                                                    <td>
                                                        <?= esc($project['name']) ?>
                                                        <?php if ($project['total_amount'] <= $project['paid_amount'] && $project['status'] != 'Tamamlandı'): ?>
                                                            <span class="badge bg-warning text-dark ms-2">
                                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                                Ödeme Tamamlandı
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($project['customer_name']) ?></td>
                                                    <td><?= esc($project['category_name']) ?></td>
                                                    <td><?= date('d.m.Y', strtotime($project['start_date'])) ?></td>
                                                    <td><?= date('d.m.Y', strtotime($project['end_date'])) ?></td>
                                                    <td><?= number_format($project['total_amount'], 2, ',', '.') ?> ₺</td>
                                                    <td><?= number_format($project['total_amount'] - $project['paid_amount'], 2, ',', '.') ?> ₺</td>
                                                    <td>
                                                        <a href="<?= base_url('projects/view/' . $project['id']) ?>" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/edit/' . $project['id']) ?>" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/notes/' . $project['id']) ?>" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </a>
                                                        <a href="<?= base_url('projects/delete/' . $project['id']) ?>" class="btn btn-danger btn-sm delete-project" data-project-id="<?= $project['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Mobil Kart Görünümü -->
                            <div class="d-md-none">
                                <div class="row">
                                    <?php foreach ($completedProjects as $project) : ?>
                                        <div class="col-12 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <?= esc($project['name']) ?>
                                                        <?php if ($project['total_amount'] <= $project['paid_amount'] && $project['status'] != 'Tamamlandı'): ?>
                                                            <span class="badge bg-warning text-dark ms-2">
                                                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                                Ödeme Tamamlandı
                                                            </span>
                                                        <?php endif; ?>
                                                    </h5>
                                                    <div class="card-text">
                                                        <p class="mb-1">
                                                            <i class="fas fa-user text-primary"></i>
                                                            <?= esc($project['customer_name']) ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-folder text-secondary"></i>
                                                            <?= esc($project['category_name']) ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-calendar text-success"></i>
                                                            <?= date('d.m.Y', strtotime($project['start_date'])) ?> - 
                                                            <?= date('d.m.Y', strtotime($project['end_date'])) ?>
                                                        </p>
                                                        <p class="mb-1">
                                                            <i class="fas fa-money-bill-wave text-danger"></i>
                                                            <?= number_format($project['total_amount'], 2, ',', '.') ?> ₺
                                                        </p>
                                                        <p class="mb-3">
                                                            <i class="fas fa-money-bill-alt text-warning"></i>
                                                            Kalan: <?= number_format($project['total_amount'] - $project['paid_amount'], 2, ',', '.') ?> ₺
                                                        </p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <a href="<?= base_url('projects/view/' . $project['id']) ?>" class="btn btn-info btn-sm me-2">
                                                            <i class="fas fa-eye"></i> Görüntüle
                                                        </a>
                                                        <a href="<?= base_url('projects/edit/' . $project['id']) ?>" class="btn btn-primary btn-sm me-2">
                                                            <i class="fas fa-edit"></i> Düzenle
                                                        </a>
                                                        <a href="<?= base_url('projects/notes/' . $project['id']) ?>" class="btn btn-warning btn-sm me-2">
                                                            <i class="fas fa-sticky-note"></i> Notlar
                                                        </a>
                                                        <a href="<?= base_url('projects/delete/' . $project['id']) ?>" class="btn btn-danger btn-sm delete-project" data-project-id="<?= $project['id'] ?>">
                                                            <i class="fas fa-trash"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Öncelik değişikliği
    document.querySelectorAll('.priority-select').forEach(select => {
        select.addEventListener('change', function() {
            const projectId = this.getAttribute('data-project-id');
            const value = this.value;
            const originalValue = this.getAttribute('data-original-value') || this.value;

            fetch('<?= base_url('projects/updatePriority') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    project_id: projectId,
                    priority: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Selectbox rengini güncelle
                    this.className = 'form-control priority-select ' + data.colorClass;
                    
                    // Tablo satırının rengini güncelle
                    const row = this.closest('tr');
                    if (row) {
                        // Önceki öncelik sınıflarını temizle
                        row.classList.remove('table-danger', 'table-warning', 'table-info', 'table-success');
                        // Yeni sınıfı ekle
                        row.classList.add(data.rowClass);
                    }
                    
                    // Mobil kart görünümü için
                    const card = this.closest('.card');
                    if (card) {
                        // Önceki öncelik sınıflarını temizle
                        card.classList.remove('table-danger', 'table-warning', 'table-info', 'table-success');
                        // Yeni sınıfı ekle
                        card.classList.add(data.rowClass);
                    }
                    
                    toastr.success(data.message);
                } else {
                    this.value = originalValue;
                    toastr.error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.value = originalValue;
                toastr.error('Güncelleme sırasında bir hata oluştu.');
            });
        });
    });

    // Durum değişikliği
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const projectId = this.getAttribute('data-project-id');
            const value = this.value;
            const originalValue = this.getAttribute('data-original-value') || this.value;

            // Eğer "Tamamlandı" seçeneği seçildiyse onay kutusu göster
            if (value === 'Tamamlandı') {
                // Önce değeri eski haline getir
                this.value = originalValue;
                
                // SweetAlert tarzı onay kutusu
                Swal.fire({
                    title: 'Projeyi Tamamlandı Olarak İşaretle',
                    text: 'Bu projeyi tamamlandı olarak işaretlemek istediğinizden emin misiniz?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Evet, Tamamlandı',
                    cancelButtonText: 'İptal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kullanıcı onayladıysa durumu güncelle
                        updateProjectStatus(projectId, value, this, originalValue);
                    }
                });
            } else {
                // Diğer durumlar için normal güncelleme
                updateProjectStatus(projectId, value, this, originalValue);
            }
        });
    });

    // Proje durumunu güncelleyen fonksiyon
    function updateProjectStatus(projectId, value, selectElement, originalValue) {
        fetch('<?= base_url('projects/updateStatus') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                project_id: projectId,
                status: value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Sadece selectbox rengini güncelle
                selectElement.className = 'form-control status-select ' + data.colorClass;
                
                toastr.success(data.message);
                
                if (value === 'Tamamlandı') {
                    toastr.info('Proje tamamlandı olarak işaretlendi.');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                selectElement.value = originalValue;
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            selectElement.value = originalValue;
            toastr.error('Güncelleme sırasında bir hata oluştu.');
        });
    }

    // Proje silme
    document.querySelectorAll('.delete-project').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.getAttribute('data-project-id');
            
            Swal.fire({
                title: 'Projeyi Silmek İstediğinize Emin misiniz?',
                text: "Bu işlem geri alınamaz!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`<?= base_url('projects/delete/') ?>${projectId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            // Sayfayı yenile
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Silme işlemi sırasında bir hata oluştu.');
                    });
                }
            });
        });
    });

    // Tab geçişlerini etkinleştir
    var triggerTabList = [].slice.call(document.querySelectorAll('#projectTabs a'));
    triggerTabList.forEach(function(triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function(event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
