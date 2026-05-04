<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="WebGIS Wisata - Jelajahi destinasi wisata Indonesia secara interaktif dengan peta digital. Temukan rekomendasi wisata terdekat.">
    <title><?= esc($title ?? 'WebGIS Wisata Indonesia') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <script>window.BASE_URL = '<?= rtrim(base_url(), '/') ?>';</script>

    <nav class="navbar" id="main-navbar">
        <div class="navbar-container">
            <a href="<?= base_url('/') ?>" class="navbar-brand" id="nav-brand">
                <div class="brand-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <span class="brand-text">WebGIS <span class="brand-accent">Wisata</span></span>
            </a>

            <div class="navbar-links" id="nav-links">
                <a href="<?= base_url('/') ?>" class="nav-link active" id="nav-home">
                    <i class="bi bi-map"></i>
                    <span>Peta</span>
                </a>
                <a href="<?= base_url('/') ?>#wisata-list" class="nav-link" id="nav-explore">
                    <i class="bi bi-compass"></i>
                    <span>Jelajahi</span>
                </a>
            </div>

            <button class="navbar-toggle" id="nav-toggle" aria-label="Toggle navigation">
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
                <span class="toggle-bar"></span>
            </button>
        </div>
    </nav>

    <main class="main-content">
        <?= $content ?? '' ?>
    </main>

    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <div class="brand-icon">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <span>WebGIS Wisata</span>
            </div>
            <div class="footer-info">
                <p>&copy; <?= date('Y') ?> WebGIS Wisata Indonesia. Dibangun dengan <i class="bi bi-heart-fill" style="color: var(--accent-coral);"></i> menggunakan Leaflet & OpenStreetMap</p>
            </div>
            <div class="footer-links">
                <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>
                <a href="https://leafletjs.com" target="_blank" rel="noopener">Leaflet.js</a>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
