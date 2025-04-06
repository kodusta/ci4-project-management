<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($project['name']) ?> - Proje Notları</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('projects') ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Projelere Dön
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

                    <!-- Not Ekleme Formu -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form id="noteForm">
                                <div class="form-group">
                                    <label for="note">Yeni Not</label>
                                    <textarea class="form-control" id="note" name="note" rows="3" required></textarea>
                                </div>
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="btn btn-primary">Not Ekle</button>
                            </form>
                        </div>
                    </div>

                    <!-- Notlar Listesi -->
                    <div class="timeline">
                        <?php foreach ($notes as $note) : ?>
                            <div class="time-label">
                                <span class="bg-primary"><?= date('d.m.Y H:i', strtotime($note['created_at'])) ?></span>
                            </div>
                            <div>
                                <i class="fas fa-sticky-note bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($note['created_at'])) ?>
                                    </span>
                                    <h3 class="timeline-header">Proje Notu</h3>
                                    <div class="timeline-body">
                                        <?= nl2br(esc($note['note'])) ?>
                                    </div>
                                    <div class="timeline-footer">
                                        <button class="btn btn-danger btn-sm delete-note" data-note-id="<?= $note['id'] ?>">
                                            <i class="fas fa-trash"></i> Sil
                                        </button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Not Ekleme
    document.getElementById('noteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('<?= base_url('projects/add-note') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                toastr.success(data.message);
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Not eklenirken bir hata oluştu.');
        });
    });

    // Not Silme
    document.querySelectorAll('.delete-note').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bu notu silmek istediğinizden emin misiniz?')) {
                const noteId = this.getAttribute('data-note-id');
                
                fetch(`<?= base_url('projects/delete-note/') ?>${noteId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Not silinirken bir hata oluştu.');
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 