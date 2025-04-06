<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ödeme Düzenle</h3>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('payments/update/' . $payment['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="project_id">Proje</label>
                            <select name="project_id" id="project_id" class="form-control <?= session('errors.project_id') ? 'is-invalid' : '' ?>" required>
                                <option value="">Proje Seçin</option>
                                <?php foreach ($projects as $project): ?>
                                <option value="<?= $project['id'] ?>" <?= old('project_id', $payment['project_id']) == $project['id'] ? 'selected' : '' ?>>
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

                        <div class="form-group">
                            <label for="amount">Tutar</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control <?= session('errors.amount') ? 'is-invalid' : '' ?>" value="<?= old('amount', $payment['amount']) ?>" required>
                            <?php if (session('errors.amount')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.amount') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="payment_date">Ödeme Tarihi</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control <?= session('errors.payment_date') ? 'is-invalid' : '' ?>" value="<?= old('payment_date', $payment['payment_date']) ?>" required>
                            <?php if (session('errors.payment_date')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.payment_date') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea name="description" id="description" class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" rows="3"><?= old('description', $payment['description']) ?></textarea>
                            <?php if (session('errors.description')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.description') ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?= site_url('payments') ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 