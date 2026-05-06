<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="row align-items-center mb-4 g-3">
    <div class="col-sm-6">
        <h2 class="fw-bold mb-0">Kelola Kategori</h2>
        <p class="text-muted mb-0">Atur pengelompokan destinasi wisata Anda.</p>
    </div>
    <div class="col-sm-6 text-sm-end">
        <a href="<?= site_url('admin/kategori/create') ?>" class="btn btn-primary px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i> Tambah Kategori
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="80">ID</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($kategori)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-folder-x display-1 text-light"></i>
                                    <p class="text-muted mt-3">Belum ada data kategori.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($kategori as $k): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $k['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-2 rounded bg-primary-soft">
                                        <i class="bi bi-folder2-open text-primary"></i>
                                    </div>
                                    <span class="fw-semibold"><?= esc($k['nama']) ?></span>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-primary small"><?= esc($k['slug']) ?></code>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= site_url('admin/kategori/edit/'.$k['id']) ?>" class="btn btn-sm btn-light text-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= site_url('admin/kategori/delete/'.$k['id']) ?>" class="btn btn-sm btn-light text-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
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
