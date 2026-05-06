<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4 g-3">
    <div class="col-sm-6">
        <h2 class="fw-bold mb-0">Galeri Foto</h2>
        <p class="text-muted mb-0">Kelola koleksi foto untuk setiap destinasi wisata.</p>
    </div>
    <div class="col-sm-6 text-sm-end">
        <a href="<?= site_url('admin/galeri/create') ?>" class="btn btn-primary px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i> Tambah Foto
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
                        <th width="200">Foto</th>
                        <th>Keterangan</th>
                        <th>Destinasi</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($galeri)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-images display-1 text-light"></i>
                                    <p class="text-muted mt-3">Belum ada data foto galeri.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($galeri as $g): ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                            <td>
                                <img src="<?= (strpos($g['url_gambar'], 'http') === 0) ? esc($g['url_gambar']) : base_url('assets/uploads/'.$g['url_gambar']) ?>" class="rounded-3 shadow-sm border" width="150" height="100" style="object-fit: cover;" alt="">
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc($g['caption']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-success-soft">
                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                    <?= esc($g['wisata']['nama'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= site_url('admin/galeri/edit/'.$g['id']) ?>" class="btn btn-sm btn-light text-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= site_url('admin/galeri/delete/'.$g['id']) ?>" class="btn btn-sm btn-light text-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus foto ini?')">
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
