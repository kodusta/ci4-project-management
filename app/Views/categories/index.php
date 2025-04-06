<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kategoriler</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('categories/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Kategori
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

                    <!-- Masaüstü Tablo Görünümü -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kategori Adı</th>
                                        <th>Açıklama</th>
                                        <th>Proje Sayısı</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category) : ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><?= esc($category['name']) ?></td>
                                            <td><?= esc($category['description']) ?></td>
                                            <td><?= $category['project_count'] ?></td>
                                            <td>
                                                <a href="<?= base_url('categories/edit/' . $category['id']) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('categories/delete/' . $category['id']) ?>" class="btn btn-danger btn-sm delete-category" data-category-id="<?= $category['id'] ?>">
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
                            <?php foreach ($categories as $category) : ?>
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($category['name']) ?></h5>
                                            <div class="card-text">
                                                <p class="mb-1">
                                                    <i class="fas fa-info-circle text-primary"></i>
                                                    <?= esc($category['description']) ?>
                                                </p>
                                                <p class="mb-3">
                                                    <i class="fas fa-project-diagram text-success"></i>
                                                    Proje Sayısı: <?= $category['project_count'] ?>
                                                </p>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <a href="<?= base_url('categories/edit/' . $category['id']) ?>" class="btn btn-primary btn-sm me-2">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                                <a href="<?= base_url('categories/delete/' . $category['id']) ?>" class="btn btn-danger btn-sm delete-category" data-category-id="<?= $category['id'] ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kategori silme işlemi
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bu kategoriyi silmek istediğinizden emin misiniz?')) {
                const categoryId = this.getAttribute('data-category-id');
                window.location.href = `<?= base_url('categories/delete/') ?>${categoryId}`;
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 