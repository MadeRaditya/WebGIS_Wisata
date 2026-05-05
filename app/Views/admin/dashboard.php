<?= $this->extend('admin/layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .stats-card {
        padding: 1.5rem;
        border-radius: 1.25rem;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .stats-info h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stats-info p {
        color: #64748b;
        margin-bottom: 0;
        font-weight: 500;
    }

    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }

    .welcome-banner {
        background: var(--primary-gradient);
        border-radius: 1.5rem;
        padding: 3rem;
        color: #fff;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="welcome-banner shadow-lg">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold mb-3">Selamat Datang, <?= ucfirst(session()->get('admin_name') ?? 'Admin') ?>!</h1>
            <p class="fs-5 opacity-75 mb-0">Kelola data WebGIS Wisata Anda dengan mudah dan efisien melalui dashboard terpadu ini.</p>
        </div>
        <div class="col-md-4 text-center d-none d-md-block">
            <i class="bi bi-rocket-takeoff display-1 opacity-50"></i>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon bg-primary-soft">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div class="stats-info">
                <h3><?= $total_kategori ?></h3>
                <p>Kategori Wisata</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon bg-success-soft">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="stats-info">
                <h3><?= $total_wisata ?></h3>
                <p>Destinasi Wisata</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon bg-warning-soft">
                <i class="bi bi-images"></i>
            </div>
            <div class="stats-info">
                <h3><?= $total_galeri ?></h3>
                <p>Total Galeri</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Distribusi Wisata per Kategori</span>
                <i class="bi bi-bar-chart-fill text-primary"></i>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="wisataChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                Aksi Cepat
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="<?= site_url('admin/wisata/create') ?>" class="btn btn-primary text-start p-3">
                        <i class="bi bi-plus-circle me-2"></i> Tambah Wisata Baru
                    </a>
                    <a href="<?= site_url('admin/kategori/create') ?>" class="btn btn-outline-primary text-start p-3">
                        <i class="bi bi-folder-plus me-2"></i> Tambah Kategori
                    </a>
                    <a href="<?= site_url('admin/galeri/create') ?>" class="btn btn-outline-secondary text-start p-3">
                        <i class="bi bi-image-fill me-2"></i> Upload Foto Galeri
                    </a>
                </div>
                
                <div class="mt-5 p-4 rounded-4 bg-light text-center">
                    <img src="https://ui-avatars.com/api/?name=<?= session()->get('admin_name') ?? 'A' ?>&background=6366f1&color=fff&size=80" class="rounded-circle mb-3 border border-3 border-white shadow-sm" alt="Avatar">
                    <h5 class="fw-bold mb-1"><?= ucfirst(session()->get('admin_name') ?? 'Admin') ?></h5>
                    <p class="text-muted small">Administrator Utama</p>
                    <hr class="my-3 opacity-25">
                    <div class="d-flex justify-content-center gap-3">
                        <div class="text-center">
                            <span class="d-block fw-bold"><?= $total_wisata ?></span>
                            <span class="small text-muted">Data</span>
                        </div>
                        <div class="vr"></div>
                        <div class="text-center">
                            <span class="d-block fw-bold"><?= $total_kategori ?></span>
                            <span class="small text-muted">Kat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('wisataChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= $chartLabels ?>,
            datasets: [{
                label: 'Jumlah Wisata',
                data: <?= $chartData ?>,
                backgroundColor: gradient,
                borderColor: '#6366f1',
                borderWidth: 2,
                borderRadius: 12,
                borderSkipped: false,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14, family: 'Outfit' },
                    bodyFont: { size: 13, family: 'Outfit' },
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: '#f1f5f9'
                    },
                    ticks: {
                        font: { family: 'Outfit' },
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { family: 'Outfit' }
                    }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
