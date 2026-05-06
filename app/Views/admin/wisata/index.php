<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4 g-3">
    <div class="col-sm-6">
        <h2 class="fw-bold mb-0">Destinasi Wisata</h2>
        <p class="text-muted mb-0">Kelola semua titik lokasi wisata di aplikasi Anda.</p>
    </div>
    <div class="col-sm-6 text-sm-end">
        <a href="<?= site_url('admin/wisata/create') ?>" class="btn btn-primary px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i> Tambah Wisata
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="80">No</th>
                        <th width="300">Destinasi</th>
                        <th>Kategori</th>
                        <th>Lokasi (Lat, Long)</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($wisata)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-map display-1 text-light"></i>
                                    <p class="text-muted mt-3">Belum ada data destinasi wisata.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($wisata as $w): ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= get_wisata_image($w['gambar_utama']) ?>" class="rounded-3 shadow-sm" width="60" height="60" style="object-fit: cover;" alt="">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= esc($w['nama']) ?></h6>
                                        <p class="small text-muted mb-0 text-truncate" style="max-width: 200px;"><?= esc($w['alamat']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary-soft"><?= esc($w['kategori']['nama'] ?? '-') ?></span>
                            </td>
                            <td>
                                <div class="small">
                                    <div class="text-dark fw-medium"><?= esc($w['latitude']) ?></div>
                                    <div class="text-muted"><?= esc($w['longitude']) ?></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= site_url('admin/wisata/edit/'.$w['id']) ?>" class="btn btn-sm btn-light text-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= site_url('admin/wisata/delete/'.$w['id']) ?>" class="btn btn-sm btn-light text-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus data wisata ini?')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
