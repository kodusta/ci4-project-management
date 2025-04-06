<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ödemeler</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="<?= site_url('payments/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Ödeme
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Proje</th>
                                    <th>Tutar</th>
                                    <th>Ödeme Tarihi</th>
                                    <th>Açıklama</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td>
                                        <a href="<?= site_url('projects/view/' . $payment['project_id']) ?>">
                                            <?= esc($payment['project_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= number_format($payment['amount'], 2, ',', '.') ?> ₺</td>
                                    <td><?= date('d.m.Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= esc($payment['description']) ?></td>
                                    <td>
                                        <a href="<?= site_url('payments/edit/' . $payment['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-payment" data-id="<?= $payment['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Ödeme Sil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Bu ödemeyi silmek istediğinizden emin misiniz?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ödeme silme işlemi
    const deleteButtons = document.querySelectorAll('.delete-payment');
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.dataset.id;
            deleteForm.action = `<?= site_url('payments/delete/') ?>/${paymentId}`;
            $(deleteModal).modal('show');
        });
    });
});
</script>
<?= $this->endSection() ?>



