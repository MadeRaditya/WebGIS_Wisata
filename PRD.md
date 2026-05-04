# 📄 Product Requirements Document (PRD)

## Aplikasi WebGIS Wisata Berbasis CodeIgniter 4

| Informasi         | Detail                                                                      |
| ----------------- | --------------------------------------------------------------------------- |
| **Nama Proyek**   | WebGIS Wisata                                                               |
| **Versi Dokumen** | 1.0                                                                         |
| **Tanggal**       | 2026-05-04                                                                  |
| **Tech Stack**    | CodeIgniter 4, supabase, PHP 8.1+, Leaflet.js, OpenStreetMap, HTML5/CSS3/JS |
| **Arsitektur**    | MVC + RESTful API                                                           |
| **Status**        | Draft / Siap Implementasi                                                   |

---

## 📖 1. Ringkasan Produk

Aplikasi web berbasis GIS yang menyajikan informasi destinasi wisata secara interaktif. Pengguna dapat menjelajahi peta, melihat detail wisata, memfilter berdasarkan kategori & jarak, serta mendapatkan rekomendasi 5+ destinasi terdekat secara otomatis menggunakan geolocation browser dan perhitungan jarak geografis.

---

## 🛠 2. Tech Stack & Prinsip Arsitektur

| Komponen              | Teknologi                                                                     |
| --------------------- | ----------------------------------------------------------------------------- |
| **Framework Backend** | CodeIgniter 4 (PHP 8.1+)                                                      |
| **Database**          | supabase                                                                      |
| **Frontend**          | HTML5, CSS3, Vanilla JS, Bootstrap 5 (opsional)                               |
| **Peta & GIS**        | Leaflet.js + OpenStreetMap Tile Server                                        |
| **Geolocation**       | HTML5 Geolocation API (`navigator.geolocation`)                               |
| **Arsitektur**        | Model-View-Controller, RESTful Routing, Service-Repository Pattern (opsional) |
| **Keamanan**          | CSRF Protection, Input Validation, Prepared Statements, HTTPS Ready           |

---

## 📁 3. Struktur Project (CodeIgniter 4)

```
webgis-wisata/
├── app/
│   ├── Config/
│   │   ├── Routes.php
│   │   └── Database.php
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── WisataController.php      # Render halaman utama/detail
│   │   ├── ApiController.php         # Endpoint RESTful JSON
│   │   └── AdminController.php       # (Opsional) CRUD admin
│   ├── Models/
│   │   ├── WisataModel.php           # CRUD + query spasial
│   │   ├── KategoriModel.php
│   │   └── GaleriModel.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   └── main.php              # Header, Footer, CDN Leaflet/BS
│   │   ├── wisata/
│   │   │   ├── index.php             # List + Peta + Filter
│   │   │   ├── detail.php            # Halaman detail wisata
│   │   │   └── galeri.php            # Tampilan galeri foto
│   │   └── partials/
│   │       └── map_container.php     # Elemen <div id="map">
│   ├── Libraries/
│   │   └── GeoHelper.php             # Fungsi Haversine & validasi koordinat
│   └── Common.php                    # Helper global
├── public/
│   ├── assets/
│   │   ├── css/style.css
│   │   ├── js/main.js                # Logika Leaflet & AJAX
│   │   └── uploads/                  # Gambar wisata & galeri
│   └── index.php
├── writable/                         # Logs, cache, sessions
└── composer.json / spark
```

---

## 🗃 4. Desain Database & Sample Data

### 4.1 Skema Tabel

```sql
CREATE TABLE kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  slug VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE wisata (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  alamat VARCHAR(255),
  latitude DECIMAL(10,7) NOT NULL,
  longitude DECIMAL(10,7) NOT NULL,
  gambar_utama VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
  INDEX idx_lat_lng (latitude, longitude)
);

CREATE TABLE galeri (
  id INT AUTO_INCREMENT PRIMARY KEY,
  wisata_id INT,
  url_gambar VARCHAR(255) NOT NULL,
  caption VARCHAR(150),
  FOREIGN KEY (wisata_id) REFERENCES wisata(id) ON DELETE CASCADE
);
```

### 4.2 Sample Data

```sql
INSERT INTO kategori (id, nama, slug) VALUES
(1, 'Pantai', 'pantai'), (2, 'Gunung', 'gunung'), (3, 'Budaya', 'budaya'), (4, 'Air Terjun', 'air-terjun');

INSERT INTO wisata (kategori_id, nama, deskripsi, alamat, latitude, longitude, gambar_utama) VALUES
(1, 'Pantai Kuta', 'Pantai ikonik dengan sunset spektakuler.', 'Kuta, Badung, Bali', -8.7184, 115.1686, 'pantai-kuta.jpg'),
(2, 'Gunung Bromo', 'Kawah aktif dengan lautan pasir luas.', 'Probolinggo, Jawa Timur', -7.9425, 112.9510, 'bromo.jpg'),
(3, 'Candi Prambanan', 'Kompleks candi Hindu terbesar di Indonesia.', 'Sleman, Yogyakarta', -7.7520, 110.4914, 'prambanan.jpg'),
(1, 'Pantai Parangtritis', 'Pantai selatan dengan gumuk pasir.', 'Bantul, Yogyakarta', -8.0321, 110.3250, 'parangtritis.jpg'),
(4, 'Air Terjun Tumpak Sewu', 'Air terjun megah bertingkat di lereng Semeru.', 'Lumajang, Jawa Timur', -8.2561, 112.9850, 'tumpaksewu.jpg');

INSERT INTO galeri (wisata_id, url_gambar, caption) VALUES
(1, 'kuta-sunset.jpg', 'Sunset di Pantai Kuta'),
(1, 'kuta-wave.jpg', 'Ombak Pantai Kuta'),
(2, 'bromo-sunrise.jpg', 'Sunrise Bromo'),
(3, 'prambanan-main.jpg', 'Candi Utama Prambanan');
```

---

## 🔀 5. Controller-Model-View Breakdown

| Komponen                  | Tanggung Jawab                                                                                                 |
| ------------------------- | -------------------------------------------------------------------------------------------------------------- |
| `WisataController`        | Menampilkan halaman `index`, `detail`, `galeri`. Memanggil model untuk data awal.                              |
| `ApiController`           | Menangani permintaan AJAX/REST. Endpoint: `/api/wisata`, `/api/rekomendasi`, `/api/galeri`.                    |
| `WisataModel`             | Extends `CI4 Model`. Method: `getAll()`, `getById()`, `getByKategori()`, `getNearest()`, `filterByDistance()`. |
| `KategoriModel`           | CRUD kategori, mapping slug ke ID.                                                                             |
| `GaleriModel`             | Query foto berdasarkan `wisata_id`.                                                                            |
| `Views/wisata/index.php`  | Layout utama: sidebar filter, daftar card, container peta.                                                     |
| `Views/wisata/detail.php` | Info lengkap, peta mini, galeri carousel.                                                                      |
| `Views/layouts/main.php`  | Template global, load CDN Leaflet/Bootstrap, header/footer.                                                    |

---

## 🌐 6. RESTful Routing (`app/Config/Routes.php`)

```php
$routes->get('/', 'WisataController::index');
$routes->get('wisata/(:num)', 'WisataController::detail/$1');
$routes->get('galeri/(:num)', 'WisataController::galeri/$1');

// API Endpoints (RESTful)
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('wisata', 'ApiController::list');           // GET /api/wisata?kategori=1&jarak=10
    $routes->get('wisata/(:num)', 'ApiController::show/$1'); // GET /api/wisata/3
    $routes->post('rekomendasi', 'ApiController::nearest');  // POST {lat, lon}
    $routes->get('galeri/(:num)', 'ApiController::galeri/$1');
});
```

---

## 📐 7. Fungsi Perhitungan Jarak (Haversine Formula)

### 7.1 PHP Helper (`app/Libraries/GeoHelper.php`)

```php
namespace App\Libraries;

class GeoHelper {
    /**
     * Hitung jarak antara dua titik koordinat (km)
     */
    public static function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
```

## 🗺 8. Integrasi Peta Interaktif (Leaflet + OSM)

### 8.1 HTML Container (`views/partials/map_container.php`)

```html
<div id="map" style="height: 60vh; width: 100%; border-radius: 8px;"></div>
```

### 8.2 JavaScript Logika (`public/assets/js/main.js`)

```javascript
document.addEventListener("DOMContentLoaded", () => {
  const map = L.map("map").setView([-7.5, 110.5], 8); // Default view (Indonesia)

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
    maxZoom: 19,
  }).addTo(map);

  let userMarker = null;
  let wisataMarkers = L.layerGroup().addTo(map);

  // 1. Dapatkan lokasi pengguna
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const { latitude, longitude } = pos.coords;
        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([latitude, longitude], { title: "Lokasi Anda" })
          .addTo(map)
          .bindPopup("Anda berada di sini")
          .openPopup();
        map.setView([latitude, longitude], 10);

        // 2. Ambil rekomendasi terdekat
        fetch("/api/rekomendasi", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ lat: latitude, lon: longitude, limit: 5 }),
        })
          .then((res) => res.json())
          .then((data) => renderMarkers(data))
          .catch((err) => console.error("Gagal memuat rekomendasi:", err));
      },
      () => {
        console.warn("Geolocation ditolak. Menggunakan fallback.");
        fetch("/api/wisata")
          .then((res) => res.json())
          .then(renderMarkers);
      },
    );
  }

  // 3. Render Marker
  window.renderMarkers = function (destinations) {
    wisataMarkers.clearLayers();
    destinations.forEach((d) => {
      const marker = L.marker([d.latitude, d.longitude]);
      marker.bindPopup(`
                <strong>${d.nama}</strong><br>
                ${d.kategori_nama || "-"}<br>
                Jarak: ${d.distance_km?.toFixed(1) || "-"} km<br>
                <a href="/wisata/${d.id}">Lihat Detail</a>
            `);
      wisataMarkers.addLayer(marker);
    });
  };
});
```

---

## 🔍 9. Logika Filter (Kategori & Jarak)

```php
// ApiController.php
public function list() {
    $kategori = $this->request->getGet('kategori');
    $jarak    = $this->request->getGet('jarak') ?? 50; // km

    $query = $this->wisataModel->select('wisata.*, kategori.nama as kategori_nama')
                               ->join('kategori', 'kategori.id = wisata.kategori_id', 'left');

    if ($kategori) {
        $query->where('kategori.slug', $kategori);
    }
    // Filter jarak dilakukan via Haversine di Model atau frontend
    // Disarankan server-side untuk performa
    $data = $query->get()->getResultArray();
    return $this->response->setJSON(['status' => true, 'data' => $data]);
}
```

---

## 🛡 10. Persyaratan Non-Fungsional

| Aspek             | Spesifikasi                                                                                                 |
| ----------------- | ----------------------------------------------------------------------------------------------------------- |
| **Performa**      | Index pada `latitude, longitude`. Query spasial di-server. Cache Redis/CI4 Cache untuk data statis.         |
| **Keamanan**      | CSRF Token aktif, validasi input (`$this->validate()`), sanitasi URL gambar, HTTPS wajib untuk geolocation. |
| **Responsif**     | Mobile-first design. Peta menyesuaikan viewport. Grid Bootstrap 12 kolom.                                   |
| **Aksesibilitas** | Alt text pada gambar, kontras warna WCAG AA, navigasi keyboard.                                             |
| **Skalabilitas**  | Pagination pada list > 50 item. Lazy load gambar galeri.                                                    |

---

## 🚀 11. Panduan Deployment & Testing

1. **Setup Environment:** `cp env .env`, konfigurasi `database.default.*` di `.env`.
2. **Database Migration:** Jalankan skema SQL atau gunakan `spark migrate`.
3. **Routing & Rewrite:** Pastikan `mod_rewrite` (Apache) atau `try_files` (Nginx) aktif.
4. **Testing:**
   - Unit Test: `php spark test`
   - API Test: Postman/Insomnia ke `/api/wisata` & `/api/rekomendasi`
   - Geolocation: Gunakan browser DevTools > Sensors untuk mock lokasi.

---

## 🔮 12. Roadmap Pengembangan (Fase Berikutnya)

- [ ] Dashboard Admin untuk CRUD wisata & upload galeri drag-and-drop
- [ ] Sistem review & rating pengguna
- [ ] Routing/ navigasi terintegrasi (OSRM/GraphHopper)
- [ ] Export data ke GPX/KML
- [ ] PWA Support (Service Worker untuk caching peta offline)

---

📝 _Dokumen ini siap digunakan sebagai panduan teknis pengembang front-end, back-end, dan QA. Pastikan semua endpoint diuji dengan payload JSON yang sesuai sebelum integrasi penuh._
