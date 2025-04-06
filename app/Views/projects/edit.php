<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Proje Düzenle</h3>
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

                    <form action="<?= base_url('projects/update/' . $project['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="customer_id">Müşteri <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.customer_id') ? 'is-invalid' : '' ?>" 
                                id="customer_id" name="customer_id" required>
                                <option value="">Müşteri Seçin</option>
                                <?php foreach ($customers as $customer) : ?>
                                    <option value="<?= $customer['id'] ?>" <?= old('customer_id', $project['customer_id']) == $customer['id'] ? 'selected' : '' ?>>
                                        <?= esc($customer['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?php if (session('errors.customer_id')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.customer_id') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="category_id">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.category_id') ? 'is-invalid' : '' ?>" 
                                id="category_id" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id', $project['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                        <?= esc($category['name']) ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?php if (session('errors.category_id')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.category_id') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="name">Proje Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                id="name" name="name" value="<?= old('name', $project['name']) ?>" required>
                            <?php if (session('errors.name')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.name') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                id="description" name="description" rows="3"><?= old('description', $project['description']) ?></textarea>
                            <?php if (session('errors.description')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.description') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="task">Görev</label>
                            <textarea class="form-control <?= session('errors.task') ? 'is-invalid' : '' ?>" 
                                id="task" name="task" rows="3"><?= old('task', $project['task']) ?></textarea>
                            <?php if (session('errors.task')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.task') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="priority">Öncelik <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.priority') ? 'is-invalid' : '' ?>" 
                                id="priority" name="priority" required>
                                <option value="">Öncelik Seçin</option>
                                <?php foreach ($priorityList as $key => $value) : ?>
                                    <option value="<?= $key ?>" <?= old('priority', $project['priority']) == $key ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?php if (session('errors.priority')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.priority') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Durum <span class="text-danger">*</span></label>
                            <select class="form-control <?= session('errors.status') ? 'is-invalid' : '' ?>" 
                                id="status" name="status" required>
                                <option value="">Durum Seçin</option>
                                <?php foreach ($statusList as $key => $value) : ?>
                                    <option value="<?= $key ?>" <?= old('status', $project['status']) == $key ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <?php if (session('errors.status')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.status') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="start_date">Başlangıç Tarihi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?= session('errors.start_date') ? 'is-invalid' : '' ?>" 
                                id="start_date" name="start_date" value="<?= old('start_date', $project['start_date']) ?>" required>
                            <?php if (session('errors.start_date')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.start_date') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="end_date">Bitiş Tarihi</label>
                            <input type="date" class="form-control <?= session('errors.end_date') ? 'is-invalid' : '' ?>" 
                                id="end_date" name="end_date" value="<?= old('end_date', $project['end_date']) ?>">
                            <?php if (session('errors.end_date')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.end_date') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="total_amount">Toplam Tutar <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control <?= session('errors.total_amount') ? 'is-invalid' : '' ?>" 
                                id="total_amount" name="total_amount" value="<?= old('total_amount', $project['total_amount']) ?>" required>
                            <?php if (session('errors.total_amount')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.total_amount') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <label for="paid_amount">Ödenen Tutar</label>
                            <input type="number" step="0.01" class="form-control <?= session('errors.paid_amount') ? 'is-invalid' : '' ?>" 
                                id="paid_amount" name="paid_amount" value="<?= old('paid_amount', $project['paid_amount']) ?>">
                            <?php if (session('errors.paid_amount')) : ?>
                                <div class="invalid-feedback">
                                    <?= session('errors.paid_amount') ?>
                                </div>
                            <?php endif ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?= base_url('projects') ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>



