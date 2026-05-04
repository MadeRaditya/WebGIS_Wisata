<section class="galeri-hero" id="galeri-hero">
    <div class="section-container">
        <a href="<?= base_url('wisata/' . $wisata['id']) ?>" class="back-link" id="btn-back-detail">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Detail
        </a>
        <h1 class="galeri-title">
            <i class="bi bi-images"></i>
            Galeri <?= esc($wisata['nama']) ?>
        </h1>
        <p class="galeri-subtitle"><?= count($galeri) ?> foto tersedia</p>
    </div>
</section>

<section class="galeri-content" id="galeri-content">
    <div class="section-container">
        <?php if (!empty($galeri)): ?>
            <div class="galeri-masonry" id="galeri-masonry">
                <?php foreach ($galeri as $foto): ?>
                    <div class="galeri-masonry-item" id="galeri-full-<?= $foto['id'] ?>">
                        <img src="<?= base_url('assets/uploads/' . esc($foto['url_gambar'])) ?>" alt="<?= esc($foto['caption'] ?? $wisata['nama']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=600&h=400&fit=crop'">
                        <?php if (!empty($foto['caption'])): ?>
                            <div class="galeri-masonry-caption">
                                <p><?= esc($foto['caption']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state" id="empty-galeri">
                <i class="bi bi-images"></i>
                <h3>Belum ada foto</h3>
                <p>Galeri foto untuk destinasi ini belum tersedia.</p>
                <a href="<?= base_url('wisata/' . $wisata['id']) ?>" class="btn btn-primary">Kembali ke Detail</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="lightbox" id="lightbox" style="display:none;">
    <button class="lightbox-close" id="lightbox-close">&times;</button>
    <button class="lightbox-prev" id="lightbox-prev"><i class="bi bi-chevron-left"></i></button>
    <button class="lightbox-next" id="lightbox-next"><i class="bi bi-chevron-right"></i></button>
    <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="">
        <p class="lightbox-caption" id="lightbox-caption"></p>
    </div>
</div>
