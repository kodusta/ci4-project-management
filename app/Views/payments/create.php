<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Ödeme</h3>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('payments/store') ?>" method="post">
                        <div class="form-group">
                            <label for="project_id">Proje</label>
                            <select name="project_id" id="project_id" class="form-control <?= session('errors.project_id') ? 'is-invalid' : '' ?>" required>
                                <option value="">Proje Seçin</option>
                                <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" <?= (old('project_id') == $project['id'] || (isset($selectedProject) && $selectedProject['id'] == $project['id'])) ? 'selected' : '' ?>>
                                    <?= esc($project['name']) ?> - <?= number_format($project['total_amount'], 2, ',', '.') ?> ₺
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session('errors.project_id')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.project_id') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($selectedProject)): ?>
                        <div class="alert alert-info p-3" id="projectInfoAlert">
                            <h5><i class="fas fa-info-circle"></i> Proje Bilgileri</h5>
                            <p><strong>Proje Adı:</strong> <?= esc($selectedProject['name']) ?></p>
                            <p><strong>Toplam Tutar:</strong> <?= number_format($selectedProject['total_amount'], 2, ',', '.') ?> ₺</p>
                            <p><strong>Ödenen Tutar:</strong> <?= number_format($selectedProject['paid_amount'], 2, ',', '.') ?> ₺</p>
                            <p><strong>Kalan Tutar:</strong> <?= number_format($selectedProject['total_amount'] - $selectedProject['paid_amount'], 2, ',', '.') ?> ₺</p>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="amount">Tutar</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control <?= session('errors.amount') ? 'is-invalid' : '' ?>" value="<?= old('amount') ?>" required>
                            <?php if (session('errors.amount')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.amount') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="payment_date">Ödeme Tarihi</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control <?= session('errors.payment_date') ? 'is-invalid' : '' ?>" value="<?= old('payment_date', date('Y-m-d')) ?>" required>
                            <?php if (session('errors.payment_date')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.payment_date') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea name="description" id="description" class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" rows="3"><?= old('description') ?></textarea>
                            <?php if (session('errors.description')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.description') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                            <a href="<?= site_url('payments') ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Proje bilgileri alert kutusunun otomatik kapanmasını engelle
    if ($('#projectInfoAlert').length) {
        // Ana şablondaki otomatik kapanma kodunu geçersiz kıl
        $('#projectInfoAlert').removeClass('alert').addClass('alert-info project-info-box');
        
        // Sayfa yüklendikten sonra otomatik kapanma kodunu devre dışı bırak
        $(document).on('click', '.project-info-box', function(e) {
            e.stopPropagation();
        });
    }
});
</script>
<?= $this->endSection() ?>


