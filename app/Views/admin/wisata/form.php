<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4">
    <div class="col">
        <h2 class="fw-bold mb-0"><?= $title ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= site_url('admin/wisata') ?>">Wisata</a></li>
                <li class="breadcrumb-item active"><?= $title ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4 p-lg-5">
        <?php $isEdit = isset($wisata); ?>
        <form action="<?= $isEdit ? site_url('admin/wisata/update/'.$wisata['id']) : site_url('admin/wisata/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="row g-4">
                <!-- Kiri: Detail Informasi -->
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle text-primary me-2"></i>Informasi Destinasi</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nama Destinasi</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0 shadow-none" name="nama" value="<?= $isEdit ? esc($wisata['nama']) : '' ?>" placeholder="e.g. Pantai Pandawa" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Kategori</label>
                        <select class="form-select bg-light border-0 shadow-none py-3" name="kategori_id" required>
                            <option value="" disabled <?= !$isEdit ? 'selected' : '' ?>>Pilih Kategori</option>
                            <?php foreach($kategori as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= ($isEdit && $wisata['kategori_id'] == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea class="form-control bg-light border-0 shadow-none" name="deskripsi" rows="6" placeholder="Masukkan deskripsi lengkap destinasi wisata..." required><?= $isEdit ? esc($wisata['deskripsi']) : '' ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat</label>
                        <textarea class="form-control bg-light border-0 shadow-none" name="alamat" rows="2" placeholder="Alamat lengkap lokasi..." required><?= $isEdit ? esc($wisata['alamat']) : '' ?></textarea>
                    </div>
                </div>

                <!-- Kanan: Lokasi & Gambar -->
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-pin-map text-primary me-2"></i>Lokasi & Visual</h5>

                    <div class="p-4 rounded-4 bg-light mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" class="form-control bg-white border-0 shadow-none py-2" name="latitude" value="<?= $isEdit ? esc($wisata['latitude']) : '' ?>" placeholder="e.g. -8.8456" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" class="form-control bg-white border-0 shadow-none py-2" name="longitude" value="<?= $isEdit ? esc($wisata['longitude']) : '' ?>" placeholder="e.g. 115.1876" required>
                        </div>
                        <div class="mt-3 small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Gunakan koordinat dari Google Maps.
                        </div>
                    </div>

                    <div class="p-4 rounded-4 bg-light">
                        <label class="form-label fw-bold">Gambar Utama</label>
                        <?php if($isEdit && !empty($wisata['gambar_utama'])): ?>
                            <div class="mb-3">
                                <img src="<?= base_url('assets/uploads/'.$wisata['gambar_utama']) ?>" class="img-fluid rounded-3 shadow-sm border border-white" alt="">
                            </div>
                        <?php endif; ?>
                        <div class="input-group">
                            <input type="file" class="form-control bg-white border-0 shadow-none py-2" name="gambar_utama" accept="image/*" <?= $isEdit ? '' : 'required' ?>>
                        </div>
                        <div class="mt-2 small text-muted">Format: JPG, PNG, WEBP (Max 2MB).</div>
                    </div>
                </div>
            </div>

            <hr class="my-5 opacity-25">

            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= site_url('admin/wisata') ?>" class="btn btn-light px-4">
                    <i class="bi bi-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary px-5 py-3 fs-5 shadow">
                    <i class="bi bi-save me-2"></i> <?= $isEdit ? 'Simpan Perubahan' : 'Terbitkan Destinasi' ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
