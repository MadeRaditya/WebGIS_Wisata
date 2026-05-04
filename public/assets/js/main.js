document.addEventListener("DOMContentLoaded", () => {
  // Navbar scroll effect
  const navbar = document.getElementById("main-navbar");
  if (navbar) {
    window.addEventListener("scroll", () => {
      navbar.classList.toggle("scrolled", window.scrollY > 50);
    });
  }

  // Mobile nav toggle
  const navToggle = document.getElementById("nav-toggle");
  const navLinks = document.getElementById("nav-links");
  if (navToggle && navLinks) {
    navToggle.addEventListener("click", () => navLinks.classList.toggle("active"));
  }

  // Hero particles
  const particlesContainer = document.getElementById("hero-particles");
  if (particlesContainer) {
    for (let i = 0; i < 30; i++) {
      const particle = document.createElement("div");
      particle.className = "particle";
      particle.style.left = Math.random() * 100 + "%";
      particle.style.top = Math.random() * 100 + "%";
      particle.style.animationDuration = 3 + Math.random() * 6 + "s";
      particle.style.animationDelay = Math.random() * 4 + "s";
      particle.style.width = particle.style.height = 2 + Math.random() * 4 + "px";
      particlesContainer.appendChild(particle);
    }
  }

  // Main map initialization
  const mapEl = document.getElementById("map");
  if (mapEl && typeof L !== "undefined") {
    initMainMap();
  }

  // Detail map initialization
  const detailMapEl = document.getElementById("detail-map");
  if (detailMapEl && typeof L !== "undefined") {
    initDetailMap(detailMapEl);
  }

  // Filter functionality
  initFilters();

  // Lightbox functionality
  initLightbox();
});

function initMainMap() {
  const map = L.map("map", {
    zoomControl: true,
    scrollWheelZoom: true,
  }).setView([-7.5, 110.5], 6);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
  }).addTo(map);

  let userMarker = null;
  let wisataMarkers = L.layerGroup().addTo(map);
  let userLat = null;
  let userLon = null;

  const mapLoading = document.getElementById("map-loading");

  // Custom marker icons
  const wisataIcon = L.divIcon({
    className: "custom-marker",
    html: '<div style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:50%;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><svg width="14" height="14" viewBox="0 0 16 16" fill="white"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });

  const userIcon = L.divIcon({
    className: "custom-marker",
    html: '<div style="width:36px;height:36px;background:linear-gradient(135deg,#10b981,#06b6d4);border-radius:50%;border:3px solid white;box-shadow:0 0 20px rgba(16,185,129,0.4);display:flex;align-items:center;justify-content:center;animation:pulse 2s infinite;"><svg width="16" height="16" viewBox="0 0 16 16" fill="white"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg></div>',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36],
  });

  // Add pulse animation style
  const style = document.createElement("style");
  style.textContent = `@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,0.4)}50%{box-shadow:0 0 0 12px rgba(16,185,129,0)}}`;
  document.head.appendChild(style);

  function renderMarkers(destinations) {
    wisataMarkers.clearLayers();
    destinations.forEach((d) => {
      const marker = L.marker([d.latitude, d.longitude], { icon: wisataIcon });
      const distText = d.distance_km != null ? `<br><span style="color:#818cf8;font-weight:600;">📍 ${d.distance_km.toFixed(1)} km</span>` : "";
      marker.bindPopup(`
        <strong>${d.nama}</strong>
        <span style="color:#94a3b8;font-size:0.8rem;">${d.kategori_nama || d.kategori?.nama || "-"}</span>
        ${distText}
        <br><a href="${window.BASE_URL}/wisata/${d.id}" style="margin-top:0.5rem;display:inline-block;">Lihat Detail →</a>
      `);
      wisataMarkers.addLayer(marker);
    });

    // Update card distances
    destinations.forEach((d) => {
      const distEl = document.getElementById("distance-" + d.id);
      if (distEl && d.distance_km != null) {
        distEl.querySelector("span").textContent = d.distance_km.toFixed(1) + " km";
      }
    });
  }

  // Make globally available
  window.renderMarkers = renderMarkers;
  window.mainMap = map;
  window.wisataMarkers = wisataMarkers;

  // Geolocation
  function locateUser() {
    if (!navigator.geolocation) {
      loadAllWisata();
      return;
    }
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        userLat = pos.coords.latitude;
        userLon = pos.coords.longitude;
        window.userLat = userLat;
        window.userLon = userLon;

        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([userLat, userLon], { icon: userIcon })
          .addTo(map)
          .bindPopup("<strong>📍 Lokasi Anda</strong><br>Anda berada di sini")
          .openPopup();
        map.setView([userLat, userLon], 10);

        fetch(window.BASE_URL + "/api/rekomendasi", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ lat: userLat, lon: userLon, limit: 10 }),
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.status && data.data) {
              renderMarkers(data.data);
            }
            hideLoading();
          })
          .catch(() => {
            loadAllWisata();
          });
      },
      () => {
        loadAllWisata();
      },
      { timeout: 10000 }
    );
  }

  function loadAllWisata() {
    fetch(window.BASE_URL + "/api/wisata")
      .then((res) => res.json())
      .then((data) => {
        if (data.status && data.data) {
          renderMarkers(data.data);
          if (data.data.length > 0) {
            const bounds = data.data.map((d) => [d.latitude, d.longitude]);
            map.fitBounds(bounds, { padding: [50, 50] });
          }
        }
        hideLoading();
      })
      .catch(() => hideLoading());
  }

  function hideLoading() {
    if (mapLoading) mapLoading.classList.add("hidden");
  }

  // Start
  locateUser();

  // Locate me button
  const btnLocate = document.getElementById("btn-locate-me");
  if (btnLocate) {
    btnLocate.addEventListener("click", () => locateUser());
  }

  // Show all markers button
  const btnShowAll = document.getElementById("btn-show-all");
  if (btnShowAll) {
    btnShowAll.addEventListener("click", () => loadAllWisata());
  }
}

function initDetailMap(el) {
  const lat = parseFloat(el.dataset.lat);
  const lng = parseFloat(el.dataset.lng);
  const name = el.dataset.name;

  const map = L.map(el).setView([lat, lng], 14);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; OpenStreetMap',
    maxZoom: 19,
  }).addTo(map);

  const icon = L.divIcon({
    className: "custom-marker",
    html: '<div style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:50%;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><svg width="14" height="14" viewBox="0 0 16 16" fill="white"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });

  L.marker([lat, lng], { icon }).addTo(map).bindPopup(`<strong>${name}</strong>`).openPopup();

  // Calculate distance from user
  const distDisplay = document.getElementById("distance-display");
  if (distDisplay && navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const dist = haversine(pos.coords.latitude, pos.coords.longitude, lat, lng);
        distDisplay.innerHTML = `
          <div class="distance-value">${dist.toFixed(1)}</div>
          <div class="distance-unit">kilometer dari lokasi Anda</div>
        `;
      },
      () => {
        distDisplay.innerHTML = '<span style="color:var(--text-muted);font-size:0.9rem;">Izinkan akses lokasi untuk melihat jarak</span>';
      }
    );
  }
}

function haversine(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a = Math.sin(dLat / 2) ** 2 + Math.cos((lat1 * Math.PI) / 180) * Math.cos((lat2 * Math.PI) / 180) * Math.sin(dLon / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function initFilters() {
  const btnFilter = document.getElementById("btn-filter");
  const btnReset = document.getElementById("btn-reset-filter");
  const filterKategori = document.getElementById("filter-kategori");
  const filterJarak = document.getElementById("filter-jarak");
  const filterSearch = document.getElementById("filter-search");

  if (!btnFilter) return;

  btnFilter.addEventListener("click", applyFilter);

  if (filterSearch) {
    let debounce;
    filterSearch.addEventListener("input", () => {
      clearTimeout(debounce);
      debounce = setTimeout(applyFilter, 400);
    });
  }

  if (btnReset) {
    btnReset.addEventListener("click", () => {
      if (filterKategori) filterKategori.value = "";
      if (filterJarak) filterJarak.value = "";
      if (filterSearch) filterSearch.value = "";
      applyFilter();
    });
  }

  function applyFilter() {
    const params = new URLSearchParams();
    if (filterKategori && filterKategori.value) params.set("kategori", filterKategori.value);
    if (filterJarak && filterJarak.value) params.set("jarak", filterJarak.value);
    if (filterSearch && filterSearch.value.trim()) params.set("search", filterSearch.value.trim());

    if (window.userLat && window.userLon) {
      params.set("lat", window.userLat);
      params.set("lon", window.userLon);
    }

    fetch(window.BASE_URL + "/api/wisata?" + params.toString())
      .then((res) => res.json())
      .then((data) => {
        if (data.status && data.data) {
          if (typeof window.renderMarkers === "function") {
            window.renderMarkers(data.data);
          }
          updateWisataGrid(data.data);

          const countEl = document.getElementById("wisata-count");
          if (countEl) countEl.textContent = `Menampilkan ${data.total} destinasi wisata`;

          if (window.mainMap && data.data.length > 0) {
            const bounds = data.data.map((d) => [d.latitude, d.longitude]);
            window.mainMap.fitBounds(bounds, { padding: [50, 50] });
          }
        }
      })
      .catch((err) => console.error("Filter error:", err));
  }
}

function updateWisataGrid(data) {
  const grid = document.getElementById("wisata-grid");
  if (!grid) return;

  if (data.length === 0) {
    grid.innerHTML = `
      <div class="empty-state">
        <i class="bi bi-search"></i>
        <h3>Tidak ada hasil</h3>
        <p>Coba ubah filter atau kata kunci pencarian Anda.</p>
      </div>
    `;
    return;
  }

  grid.innerHTML = data
    .map(
      (item) => `
    <article class="wisata-card" id="wisata-card-${item.id}" data-id="${item.id}" data-lat="${item.latitude}" data-lng="${item.longitude}">
      <div class="card-image">
        <img src="${item.gambar_utama ? window.BASE_URL + "/assets/uploads/" + item.gambar_utama : "https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=400&h=300&fit=crop"}" 
             alt="${item.nama}" loading="lazy" 
             onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=400&h=300&fit=crop'">
        <div class="card-badge">
          <i class="bi bi-tag-fill"></i>
          ${item.kategori_nama || item.kategori?.nama || "Umum"}
        </div>
      </div>
      <div class="card-body">
        <h3 class="card-title">${item.nama}</h3>
        <p class="card-address"><i class="bi bi-geo-alt"></i> ${item.alamat || "Indonesia"}</p>
        <p class="card-desc">${(item.deskripsi || "").substring(0, 100)}${(item.deskripsi || "").length > 100 ? "..." : ""}</p>
        <div class="card-footer">
          <span class="card-distance"><i class="bi bi-signpost-2"></i> <span>${item.distance_km != null ? item.distance_km.toFixed(1) + " km" : "-"}</span></span>
          <a href="${window.BASE_URL}/wisata/${item.id}" class="card-link">Detail <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </article>
  `
    )
    .join("");
}

function initLightbox() {
  const lightbox = document.getElementById("lightbox");
  if (!lightbox) return;

  const img = document.getElementById("lightbox-img");
  const caption = document.getElementById("lightbox-caption");
  const closeBtn = document.getElementById("lightbox-close");
  const prevBtn = document.getElementById("lightbox-prev");
  const nextBtn = document.getElementById("lightbox-next");

  let images = [];
  let currentIndex = 0;

  document.querySelectorAll(".galeri-masonry-item, .galeri-item").forEach((item, i) => {
    const imgEl = item.querySelector("img");
    const capEl = item.querySelector(".galeri-masonry-caption p, .galeri-caption");
    images.push({ src: imgEl.src, caption: capEl ? capEl.textContent : "" });

    item.addEventListener("click", () => {
      currentIndex = i;
      showImage();
      lightbox.style.display = "flex";
    });
  });

  function showImage() {
    if (images[currentIndex]) {
      img.src = images[currentIndex].src;
      caption.textContent = images[currentIndex].caption;
    }
  }

  if (closeBtn) closeBtn.addEventListener("click", () => (lightbox.style.display = "none"));
  if (prevBtn) prevBtn.addEventListener("click", () => { currentIndex = (currentIndex - 1 + images.length) % images.length; showImage(); });
  if (nextBtn) nextBtn.addEventListener("click", () => { currentIndex = (currentIndex + 1) % images.length; showImage(); });

  lightbox.addEventListener("click", (e) => { if (e.target === lightbox) lightbox.style.display = "none"; });

  document.addEventListener("keydown", (e) => {
    if (lightbox.style.display === "none") return;
    if (e.key === "Escape") lightbox.style.display = "none";
    if (e.key === "ArrowLeft" && prevBtn) prevBtn.click();
    if (e.key === "ArrowRight" && nextBtn) nextBtn.click();
  });
}
