<section class="hero-section" id="hero">
    <div class="hero-overlay"></div>
    <div class="hero-particles" id="hero-particles"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="bi bi-stars"></i>
            <span>Jelajahi Indonesia</span>
        </div>
        <h1 class="hero-title">Temukan Destinasi <br><span class="gradient-text">Wisata Terbaik</span></h1>
        <p class="hero-subtitle">Eksplorasi ratusan tempat wisata menakjubkan di seluruh Indonesia dengan peta interaktif dan rekomendasi lokasi terdekat.</p>
        <div class="hero-actions">
            <a href="#map-section" class="btn btn-primary" id="btn-explore-map">
                <i class="bi bi-map"></i>
                <span>Buka Peta</span>
            </a>
            <a href="#wisata-list" class="btn btn-outline" id="btn-explore-list">
                <i class="bi bi-grid-3x3-gap"></i>
                <span>Lihat Semua</span>
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number" id="stat-wisata"><?= count($wisata ?? []) ?></span>
                <span class="stat-label">Destinasi</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="stat-kategori"><?= count($kategori ?? []) ?></span>
                <span class="stat-label">Kategori</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="stat-provinsi">5+</span>
                <span class="stat-label">Provinsi</span>
            </div>
        </div>
    </div>
</section>

<section class="map-section" id="map-section">
    <div class="section-container">
        <div class="section-header">
            <div class="section-badge">
                <i class="bi bi-pin-map-fill"></i>
                <span>Peta Interaktif</span>
            </div>
            <h2 class="section-title">Jelajahi Peta Wisata</h2>
            <p class="section-subtitle">Klik marker untuk melihat informasi destinasi. Izinkan akses lokasi untuk rekomendasi terdekat.</p>
        </div>

        <div class="map-wrapper" id="map-wrapper">
            <div class="map-controls">
                <button class="map-control-btn" id="btn-locate-me" title="Temukan Lokasi Saya">
                    <i class="bi bi-crosshair"></i>
                    <span>Lokasi Saya</span>
                </button>
                <button class="map-control-btn" id="btn-show-all" title="Tampilkan Semua Marker">
                    <i class="bi bi-pin-map"></i>
                    <span>Semua Marker</span>
                </button>
            </div>
            <div id="map" class="map-container"></div>
            <div class="map-loading" id="map-loading">
                <div class="spinner"></div>
                <span>Memuat peta...</span>
            </div>
        </div>
    </div>
</section>

<section class="filter-section" id="filter-section">
    <div class="section-container">
        <div class="filter-bar">
            <div class="filter-group">
                <label class="filter-label" for="filter-kategori">
                    <i class="bi bi-funnel"></i>
                    Kategori
                </label>
                <select class="filter-select" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    <?php if (!empty($kategori)): ?>
                        <?php foreach ($kategori as $kat): ?>
                            <option value="<?= esc($kat['slug']) ?>"><?= esc($kat['nama']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="filter-jarak">
                    <i class="bi bi-rulers"></i>
                    Jarak Maks
                </label>
                <select class="filter-select" id="filter-jarak">
                    <option value="">Tanpa Batas</option>
                    <option value="10">10 km</option>
                    <option value="25">25 km</option>
                    <option value="50">50 km</option>
                    <option value="100">100 km</option>
                    <option value="250">250 km</option>
                    <option value="500">500 km</option>
                </select>
            </div>

            <div class="filter-group filter-search">
                <label class="filter-label" for="filter-search">
                    <i class="bi bi-search"></i>
                    Cari
                </label>
                <input type="text" class="filter-input" id="filter-search" placeholder="Cari destinasi..." autocomplete="off">
            </div>

            <button class="btn btn-primary filter-btn" id="btn-filter">
                <i class="bi bi-funnel-fill"></i>
                <span>Filter</span>
            </button>

            <button class="btn btn-outline filter-btn" id="btn-reset-filter">
                <i class="bi bi-arrow-counterclockwise"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>
</section>

<section class="wisata-section" id="wisata-list">
    <div class="section-container">
        <div class="section-header">
            <div class="section-badge">
                <i class="bi bi-compass-fill"></i>
                <span>Destinasi Wisata</span>
            </div>
            <h2 class="section-title">Semua Destinasi</h2>
            <p class="section-subtitle" id="wisata-count">Menampilkan <?= count($wisata ?? []) ?> destinasi wisata</p>
        </div>

        <div class="wisata-grid" id="wisata-grid">
            <?php if (!empty($wisata)): ?>
                <?php foreach ($wisata as $item): ?>
                    <article class="wisata-card" id="wisata-card-<?= $item['id'] ?>" data-id="<?= $item['id'] ?>" data-lat="<?= $item['latitude'] ?>" data-lng="<?= $item['longitude'] ?>">
                        <div class="card-image">
                            <?php if (!empty($item['gambar_utama'])): ?>
                                <img src="<?= (strpos($item['gambar_utama'], 'http') === 0) ? esc($item['gambar_utama']) : base_url('assets/uploads/' . esc($item['gambar_utama'])) ?>" alt="<?= esc($item['nama']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=400&h=300&fit=crop'">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=400&h=300&fit=crop" alt="<?= esc($item['nama']) ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="card-badge">
                                <i class="bi bi-tag-fill"></i>
                                <?= esc($item['kategori']['nama'] ?? 'Umum') ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= esc($item['nama']) ?></h3>
                            <p class="card-address">
                                <i class="bi bi-geo-alt"></i>
                                <?= esc($item['alamat'] ?? 'Indonesia') ?>
                            </p>
                            <p class="card-desc"><?= esc(mb_substr($item['deskripsi'] ?? '', 0, 100)) ?><?= mb_strlen($item['deskripsi'] ?? '') > 100 ? '...' : '' ?></p>
                            <div class="card-footer">
                                <span class="card-distance" id="distance-<?= $item['id'] ?>">
                                    <i class="bi bi-signpost-2"></i>
                                    <span>-</span>
                                </span>
                                <a href="<?= base_url('wisata/' . $item['id']) ?>" class="card-link" id="link-wisata-<?= $item['id'] ?>">
                                    Detail
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" id="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>Belum ada destinasi</h3>
                    <p>Destinasi wisata akan muncul di sini setelah ditambahkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
