<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kategori Düzenle</h3>
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

                    <form action="<?= base_url('categories/update/' . $category['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="name">Kategori Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                id="name" name="name" value="<?= old('name', $category['name']) ?>" required>
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.name') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                id="description" name="description" rows="3"><?= old('description', $category['description']) ?></textarea>
                            <?php if (session('errors.description')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.description') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?= base_url('categories') ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 