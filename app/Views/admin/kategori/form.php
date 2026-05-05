<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="fw-bold mb-0"><?= $title ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/kategori') ?>">Kategori</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?php $isEdit = isset($kategori); ?>
                <form action="<?= $isEdit ? site_url('admin/kategori/update/'.$kategori['id']) : site_url('admin/kategori/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-folder"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" name="nama" value="<?= $isEdit ? esc($kategori['nama']) : '' ?>" placeholder="Masukkan nama kategori" required>
                        </div>
                        <div class="form-text small opacity-75">Gunakan nama yang unik untuk kategori wisata.</div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= site_url('admin/kategori') ?>" class="btn btn-light text-muted">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-1"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
