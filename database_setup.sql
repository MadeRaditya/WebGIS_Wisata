-- =============================================
-- WebGIS Wisata - Supabase Database Setup
-- Run this SQL in Supabase SQL Editor
-- =============================================

-- 1. Create kategori table
CREATE TABLE IF NOT EXISTS kategori (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  slug VARCHAR(50) UNIQUE NOT NULL
);

-- 2. Create wisata table
CREATE TABLE IF NOT EXISTS wisata (
  id SERIAL PRIMARY KEY,
  kategori_id INT REFERENCES kategori(id) ON DELETE SET NULL,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT,
  alamat VARCHAR(255),
  latitude DECIMAL(10,7) NOT NULL,
  longitude DECIMAL(10,7) NOT NULL,
  gambar_utama VARCHAR(255),
  created_at TIMESTAMPTZ DEFAULT NOW(),
  updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_wisata_lat_lng ON wisata(latitude, longitude);
CREATE INDEX IF NOT EXISTS idx_wisata_kategori ON wisata(kategori_id);

-- 3. Create galeri table
CREATE TABLE IF NOT EXISTS galeri (
  id SERIAL PRIMARY KEY,
  wisata_id INT REFERENCES wisata(id) ON DELETE CASCADE,
  url_gambar VARCHAR(255) NOT NULL,
  caption VARCHAR(150)
);

CREATE INDEX IF NOT EXISTS idx_galeri_wisata ON galeri(wisata_id);

-- 4. Enable Row Level Security (RLS) with public read access
ALTER TABLE kategori ENABLE ROW LEVEL SECURITY;
ALTER TABLE wisata ENABLE ROW LEVEL SECURITY;
ALTER TABLE galeri ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Public read kategori" ON kategori FOR SELECT USING (true);
CREATE POLICY "Public read wisata" ON wisata FOR SELECT USING (true);
CREATE POLICY "Public read galeri" ON galeri FOR SELECT USING (true);

-- 5. Insert sample data
INSERT INTO kategori (id, nama, slug) VALUES
(1, 'Pantai', 'pantai'),
(2, 'Gunung', 'gunung'),
(3, 'Budaya', 'budaya'),
(4, 'Air Terjun', 'air-terjun')
ON CONFLICT (id) DO NOTHING;

INSERT INTO wisata (kategori_id, nama, deskripsi, alamat, latitude, longitude, gambar_utama) VALUES
(1, 'Pantai Kuta', 'Pantai ikonik dengan sunset spektakuler dan ombak yang cocok untuk berselancar. Terletak di Badung, Bali, pantai ini menjadi destinasi utama wisatawan dari seluruh dunia.', 'Kuta, Badung, Bali', -8.7184000, 115.1686000, 'pantai-kuta.jpg'),
(2, 'Gunung Bromo', 'Kawah aktif dengan lautan pasir luas yang memukau. Terletak di Probolinggo, Jawa Timur, Gunung Bromo menawarkan pemandangan sunrise yang menakjubkan.', 'Probolinggo, Jawa Timur', -7.9425000, 112.9510000, 'bromo.jpg'),
(3, 'Candi Prambanan', 'Kompleks candi Hindu terbesar di Indonesia dan salah satu warisan dunia UNESCO. Terletak di Sleman, Yogyakarta, candi ini menawarkan arsitektur megah peninggalan abad ke-9.', 'Sleman, Yogyakarta', -7.7520000, 110.4914000, 'prambanan.jpg'),
(1, 'Pantai Parangtritis', 'Pantai selatan Yogyakarta dengan gumuk pasir yang eksotis dan legenda mistis Nyi Roro Kidul. Cocok untuk menikmati sunset dan bermain ATV di atas pasir.', 'Bantul, Yogyakarta', -8.0321000, 110.3250000, 'parangtritis.jpg'),
(4, 'Air Terjun Tumpak Sewu', 'Air terjun megah bertingkat di lereng Gunung Semeru setinggi 120 meter. Panoramanya yang menyerupai tirai air menjadikannya salah satu air terjun terindah di Indonesia.', 'Lumajang, Jawa Timur', -8.2561000, 112.9850000, 'tumpaksewu.jpg');

INSERT INTO galeri (wisata_id, url_gambar, caption) VALUES
(1, 'kuta-sunset.jpg', 'Sunset di Pantai Kuta'),
(1, 'kuta-wave.jpg', 'Ombak Pantai Kuta'),
(2, 'bromo-sunrise.jpg', 'Sunrise Bromo'),
(3, 'prambanan-main.jpg', 'Candi Utama Prambanan');

-- 6. Reset sequences
SELECT setval('kategori_id_seq', (SELECT MAX(id) FROM kategori));
SELECT setval('wisata_id_seq', (SELECT MAX(id) FROM wisata));
SELECT setval('galeri_id_seq', (SELECT MAX(id) FROM galeri));
