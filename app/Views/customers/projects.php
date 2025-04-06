<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($customer['name']) ?> - Projeler</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('customers') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Müşterilere Dön
                        </a>
                        <a href="<?= base_url('projects/create?customer_id=' . $customer['id']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Proje
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')) : ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Müşteri Bilgileri -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Müşteri Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>E-posta:</strong> <?= esc($customer['email']) ?></p>
                                    <p><strong>Telefon:</strong> <?= esc($customer['phone']) ?></p>
                                    <p><strong>Adres:</strong> <?= esc($customer['address']) ?></p>
                                    <?php if (!empty($customer['note'])): ?>
                                    <p>
                                        <strong>Not:</strong> 
                                        <span class="text-warning">
                                            <i class="fas fa-info-circle"></i>
                                            <?= esc($customer['note']) ?>
                                        </span>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Proje İstatistikleri</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>Toplam Proje:</strong> <?= count($projects) ?></p>
                                    <p><strong>Tamamlanan Projeler:</strong> <?= count(array_filter($projects, function($p) { return $p['status'] == 'Tamamlandı'; })) ?></p>
                                    <p><strong>Devam Eden Projeler:</strong> <?= count(array_filter($projects, function($p) { return $p['status'] == 'Devam Ediyor'; })) ?></p>
                                    <p><strong>Toplam Proje Tutarı:</strong> <?= number_format(array_sum(array_column($projects, 'total_amount')), 2, ',', '.') ?> ₺</p>
                                    <?php if (isset($lastProject) && $lastProject): ?>
                                    <p><strong>Son Proje:</strong> <?= esc($lastProject['name']) ?></p>
                                    <p><strong>Son Proje Bitiş Tarihi:</strong> <?= $lastProject['end_date'] ? date('d.m.Y', strtotime($lastProject['end_date'])) : 'Belirlenmedi' ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Proje Listesi -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proje Adı</th>
                                    <th>Kategori</th>
                                    <th>Öncelik</th>
                                    <th>Durum</th>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>Tutar</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($projects)) : ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Bu müşteriye ait proje bulunmamaktadır.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($projects as $project) : ?>
                                        <tr class="<?= getPriorityRowClass($project['priority']) ?>">
                                            <td><?= $project['id'] ?></td>
                                            <td><?= esc($project['name']) ?></td>
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
                                <?php endif; ?>
                            </tbody>
                        </table>
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
                    this.className = 'form-control status-select ' + data.colorClass;
                    
                    toastr.success(data.message);
                    
                    if (value === 'Tamamlandı') {
                        toastr.info('Proje tamamlandı olarak işaretlendi.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
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

    // Proje silme
    document.querySelectorAll('.delete-project').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bu projeyi silmek istediğinizden emin misiniz?')) {
                const projectId = this.getAttribute('data-project-id');
                window.location.href = `<?= base_url('projects/delete/') ?>${projectId}`;
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 