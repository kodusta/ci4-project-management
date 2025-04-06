<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Müşteriler</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('customers/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Yeni Müşteri
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
                                        <th>Müşteri Adı</th>
                                        <th>E-posta</th>
                                        <th>Telefon</th>
                                        <th>Adres</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer) : ?>
                                        <tr>
                                            <td><?= $customer['id'] ?></td>
                                            <td>
                                                <?= esc($customer['name']) ?>
                                                <?php if (!empty($customer['note'])): ?>
                                                    <i class="fas fa-info-circle text-warning" data-toggle="tooltip" title="<?= esc($customer['note']) ?>"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($customer['email']) ?></td>
                                            <td><?= esc($customer['phone']) ?></td>
                                            <td><?= esc($customer['address']) ?></td>
                                            <td>
                                                <a href="<?= base_url('customers/projects/' . $customer['id']) ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-project-diagram"></i>
                                                </a>
                                                <a href="<?= base_url('customers/edit/' . $customer['id']) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('customers/delete/' . $customer['id']) ?>" class="btn btn-danger btn-sm delete-customer" data-customer-id="<?= $customer['id'] ?>">
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
                            <?php foreach ($customers as $customer) : ?>
                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($customer['name']) ?></h5>
                                            <div class="card-text">
                                                <p class="mb-1">
                                                    <i class="fas fa-envelope text-primary"></i>
                                                    <?= esc($customer['email']) ?>
                                                </p>
                                                <p class="mb-1">
                                                    <i class="fas fa-phone text-success"></i>
                                                    <?= esc($customer['phone']) ?>
                                                </p>
                                                <p class="mb-3">
                                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                                    <?= esc($customer['address']) ?>
                                                </p>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <a href="<?= base_url('customers/projects/' . $customer['id']) ?>" class="btn btn-info btn-sm me-2">
                                                    <i class="fas fa-project-diagram"></i> Projeler
                                                </a>
                                                <a href="<?= base_url('customers/edit/' . $customer['id']) ?>" class="btn btn-primary btn-sm me-2">
                                                    <i class="fas fa-edit"></i> Düzenle
                                                </a>
                                                <a href="<?= base_url('customers/delete/' . $customer['id']) ?>" class="btn btn-danger btn-sm delete-customer" data-customer-id="<?= $customer['id'] ?>">
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
    // Müşteri silme işlemi
    document.querySelectorAll('.delete-customer').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')) {
                const customerId = this.getAttribute('data-customer-id');
                window.location.href = `<?= base_url('customers/delete/') ?>${customerId}`;
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
