<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Müşteri Ekle</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')) : ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <ul>
                                <?php foreach (session('errors') as $error) : ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <form action="<?= base_url('customers/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="name">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                id="name" name="name" value="<?= old('name') ?>" required>
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.name') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="email">E-posta</label>
                            <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                id="email" name="email" value="<?= old('email') ?>">
                            <?php if (session('errors.email')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.email') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="text" class="form-control <?= session('errors.phone') ? 'is-invalid' : '' ?>" 
                                id="phone" name="phone" value="<?= old('phone') ?>">
                            <?php if (session('errors.phone')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.phone') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="address">Adres</label>
                            <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>" 
                                id="address" name="address" rows="3"><?= old('address') ?></textarea>
                            <?php if (session('errors.address')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.address') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="note">Not</label>
                            <textarea class="form-control" id="note" name="note" rows="2" placeholder="Müşteri hakkında önemli notlar..."><?= old('note') ?></textarea>
                            <small class="form-text text-muted">Müşteri hakkında önemli notları buraya ekleyebilirsiniz (örn: sorunlu müşteri, tekrar iş yapma vb.)</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                            <a href="<?= base_url('customers') ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
