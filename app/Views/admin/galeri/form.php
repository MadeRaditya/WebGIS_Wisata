<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="row align-items-center mb-4">
            <div class="col">
                <h2 class="fw-bold mb-0"><?= $title ?></h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('admin/galeri') ?>">Galeri</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4 p-lg-5">
                <?php $isEdit = isset($galeri); ?>
                <form action="<?= $isEdit ? site_url('admin/galeri/update/'.$galeri['id']) : site_url('admin/galeri/store') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Destinasi Wisata</label>
                        <select class="form-select form-select-lg bg-light border-0 shadow-none py-3" name="wisata_id" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Pilih Lokasi</option>
                            <?php foreach($wisata as $w): ?>
                                <option value="<?= $w['id'] ?>" <?= ($isEdit && $galeri['wisata_id'] == $w['id']) ? 'selected' : '' ?>><?= esc($w['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan / Caption Foto</label>
                        <textarea class="form-control bg-light border-0 shadow-none" name="caption" rows="3" placeholder="Masukkan keterangan singkat foto..." required><?= $isEdit ? esc($galeri['caption']) : '' ?></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">File Foto</label>
                        <div class="p-4 rounded-4 bg-light text-center border-2 border-dashed border-secondary-subtle">
                            <?php if($isEdit && !empty($galeri['url_gambar'])): ?>
                                <div class="mb-4">
                                    <img src="<?= base_url('assets/uploads/'.$galeri['url_gambar']) ?>" class="rounded-4 shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;" alt="">
                                </div>
                            <?php endif; ?>
                            
                            <div class="input-group">
                                <input type="file" class="form-control bg-white border-0 shadow-none py-2" name="url_gambar" accept="image/*" <?= $isEdit ? '' : 'required' ?>>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Klik untuk memilih atau ganti file foto (Max 2MB).</p>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= site_url('admin/galeri') ?>" class="btn btn-light px-4">
                            <i class="bi bi-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-3 shadow">
                            <i class="bi bi-cloud-arrow-up me-2"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Upload Foto' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
