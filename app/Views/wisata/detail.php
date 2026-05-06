<section class="detail-hero" id="detail-hero">
    <div class="detail-hero-bg">
        <?php if (!empty($wisata['gambar_utama'])): ?>
            <img src="<?= (strpos($wisata['gambar_utama'], 'http') === 0) ? esc($wisata['gambar_utama']) : base_url('assets/uploads/' . esc($wisata['gambar_utama'])) ?>" alt="<?= esc($wisata['nama']) ?>" onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=1200&h=600&fit=crop'">
        <?php else: ?>
            <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=1200&h=600&fit=crop" alt="<?= esc($wisata['nama']) ?>">
        <?php endif; ?>
        <div class="detail-hero-overlay"></div>
    </div>
    <div class="detail-hero-content">
        <a href="<?= base_url('/') ?>" class="back-link" id="btn-back">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Peta
        </a>
        <div class="detail-badge">
            <i class="bi bi-tag-fill"></i>
            <?= esc($wisata['kategori']['nama'] ?? 'Umum') ?>
        </div>
        <h1 class="detail-title"><?= esc($wisata['nama']) ?></h1>
        <p class="detail-location">
            <i class="bi bi-geo-alt-fill"></i>
            <?= esc($wisata['alamat'] ?? 'Indonesia') ?>
        </p>
    </div>
</section>

<section class="detail-content" id="detail-content">
    <div class="section-container">
        <div class="detail-grid">
            <div class="detail-main">
                <div class="detail-card" id="card-deskripsi">
                    <div class="detail-card-header">
                        <i class="bi bi-info-circle-fill"></i>
                        <h2>Tentang Destinasi</h2>
                    </div>
                    <div class="detail-card-body">
                        <p class="detail-desc"><?= nl2br(esc($wisata['deskripsi'] ?? 'Deskripsi belum tersedia.')) ?></p>
                    </div>
                </div>

                <div class="detail-card" id="card-info">
                    <div class="detail-card-header">
                        <i class="bi bi-card-list"></i>
                        <h2>Informasi</h2>
                    </div>
                    <div class="detail-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-tag"></i></div>
                                <div class="info-text">
                                    <span class="info-label">Kategori</span>
                                    <span class="info-value"><?= esc($wisata['kategori']['nama'] ?? 'Umum') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                                <div class="info-text">
                                    <span class="info-label">Alamat</span>
                                    <span class="info-value"><?= esc($wisata['alamat'] ?? '-') ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-pin-map"></i></div>
                                <div class="info-text">
                                    <span class="info-label">Koordinat</span>
                                    <span class="info-value"><?= esc($wisata['latitude']) ?>, <?= esc($wisata['longitude']) ?></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="bi bi-calendar3"></i></div>
                                <div class="info-text">
                                    <span class="info-label">Ditambahkan</span>
                                    <span class="info-value"><?= date('d M Y', strtotime($wisata['created_at'] ?? 'now')) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($galeri)): ?>
                <div class="detail-card" id="card-galeri">
                    <div class="detail-card-header">
                        <i class="bi bi-images"></i>
                        <h2>Galeri Foto</h2>
                        <a href="<?= base_url('galeri/' . $wisata['id']) ?>" class="detail-card-link" id="link-galeri-all">
                            Lihat Semua
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="detail-card-body">
                        <div class="galeri-grid">
                            <?php foreach ($galeri as $foto): ?>
                                <div class="galeri-item" id="galeri-item-<?= $foto['id'] ?>">
                                    <img src="<?= (strpos($foto['url_gambar'], 'http') === 0) ? esc($foto['url_gambar']) : base_url('assets/uploads/' . esc($foto['url_gambar'])) ?>" alt="<?= esc($foto['caption'] ?? '') ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=400&h=300&fit=crop'">
                                    <?php if (!empty($foto['caption'])): ?>
                                        <div class="galeri-caption"><?= esc($foto['caption']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="detail-sidebar">
                <div class="detail-card sticky-card" id="card-map-mini">
                    <div class="detail-card-header">
                        <i class="bi bi-map-fill"></i>
                        <h2>Lokasi di Peta</h2>
                    </div>
                    <div class="detail-card-body">
                        <div id="detail-map" class="detail-map" data-lat="<?= esc($wisata['latitude']) ?>" data-lng="<?= esc($wisata['longitude']) ?>" data-name="<?= esc($wisata['nama']) ?>"></div>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $wisata['latitude'] ?>,<?= $wisata['longitude'] ?>" target="_blank" rel="noopener" class="btn btn-primary btn-block" id="btn-directions">
                            <i class="bi bi-sign-turn-right"></i>
                            Petunjuk Arah
                        </a>
                    </div>
                </div>

                <div class="detail-card" id="card-distance">
                    <div class="detail-card-header">
                        <i class="bi bi-signpost-2-fill"></i>
                        <h2>Jarak dari Anda</h2>
                    </div>
                    <div class="detail-card-body">
                        <div class="distance-display" id="distance-display">
                            <div class="distance-loading">
                                <div class="spinner-sm"></div>
                                <span>Menghitung jarak...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
