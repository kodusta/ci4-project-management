<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rapor Arşivi</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('reports') ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left"></i> Raporlara Dön
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($files)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Henüz arşivlenmiş rapor bulunmamaktadır.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Rapor Adı</th>
                                        <th>Yıl</th>
                                        <th>Ay</th>
                                        <th>Boyut</th>
                                        <th>Oluşturulma Tarihi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($files as $file): ?>
                                        <tr>
                                            <td><?= $file['name'] ?></td>
                                            <td><?= $file['year'] ?></td>
                                            <td><?= $file['month'] ?></td>
                                            <td><?= formatFileSize($file['size']) ?></td>
                                            <td><?= date('d.m.Y H:i', $file['date']) ?></td>
                                            <td>
                                                <a href="<?= base_url('reports/downloadArchive/' . $file['name']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i> İndir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            },
            "order": [[4, "desc"]]
        });
    });
</script>
<?= $this->endSection() ?> 