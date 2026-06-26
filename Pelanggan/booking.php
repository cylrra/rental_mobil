<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking — INDOMAX-RENT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
    :root {
      --primary:        #9e0000;
      --primary-dark:   #cc0000;
      --amber:          #fdc003;
      --amber-dark:     #e6a800;
      --charcoal:       #1a1c1c;
      --charcoal-mid:   #4d4c4c;
      --surface:        #f9f9f9;
      --surface-low:    #f3f3f3;
      --surface-card:   #ffffff;
      --outline:        #e8bdb6;
      --outline-mid:    #d0d0d0;
      --on-primary:     #ffffff;
      --text-main:      #1a1c1c;
      --text-muted:     #5e3f3a;
      --text-subtle:    #926e69;
      --radius-sm:      4px;
      --radius:         8px;
      --radius-md:      12px;
      --radius-lg:      16px;
      --radius-xl:      24px;
    }
 
    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--surface);
      color: var(--text-main);
      min-height: 100vh;
      padding: 48px 24px;
      overflow-y: auto;
    }
 
    /* ── Grid accent bar top ── */
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-dark) 0%, var(--amber) 100%);
      z-index: 100;
    }
 
    .page-wrap {
      max-width: 560px;
      margin: 0 auto;
    }
 
    /* ── Brand ── */
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 6px;
      animation: fadeUp .4s ease both;
    }
 
    .brand-icon {
      width: 40px; height: 40px;
      background: var(--primary-dark);
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
 
    .brand-icon svg {
      width: 22px; height: 22px;
      stroke: #fff; stroke-width: 2;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .logo {
      font-size: 22px;
      font-weight: 900;
      letter-spacing: -0.02em;
      color: var(--primary-dark);
    }
 
    .logo span {
      color: var(--amber-dark);
    }
 
    .page-sub {
      font-size: 14px;
      color: var(--text-subtle);
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-bottom: 32px;
      animation: fadeUp .4s ease .05s both;
    }
 
    /* ── Card wrapper ── */
    .card {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-xl);
      padding: 36px 32px 40px;
      animation: fadeUp .5s cubic-bezier(.22,1,.36,1) both;
      animation-delay: .08s;
    }
 
    /* ── Section label ── */
    .section-label {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 12px;
      margin-top: 28px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
 
    .section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--outline-mid);
    }
 
    /* ── Input fields ── */
    .input-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
 
    .field {
      position: relative;
    }
 
    .field-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      width: 18px; height: 18px;
      stroke: var(--text-subtle);
      stroke-width: 2;
      fill: none;
      stroke-linecap: round;
      stroke-linejoin: round;
      pointer-events: none;
    }
 
    .field input {
      width: 100%;
      height: 52px;
      padding: 0 18px 0 48px;
      background: var(--surface-low);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      color: var(--text-main);
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
      font-weight: 500;
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }
 
    .field input::placeholder { color: var(--text-subtle); font-weight: 400; }
 
    .field input:focus {
      border-color: var(--primary-dark);
      border-width: 2px;
      box-shadow: 0 0 0 3px rgba(204,0,0,.08);
    }
 
    .field input[type="date"]::-webkit-calendar-picker-indicator {
      cursor: pointer;
      opacity: 0.5;
    }
 
    /* connector */
    .connector {
      display: flex;
      align-items: center;
      padding-left: 24px;
      margin: -2px 0;
      height: 18px;
    }
 
    .connector-line {
      width: 2px; height: 100%;
      background: var(--outline-mid);
      border-radius: 2px;
    }
 
    /* ── Car card ── */
    .car-card {
      background: var(--charcoal);
      border-radius: var(--radius-lg);
      padding: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-top: 12px;
      position: relative;
      overflow: hidden;
    }
 
    .car-card::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 5px;
      background: var(--primary-dark);
    }
 
    .car-card::after {
      content: '';
      position: absolute;
      right: -24px; top: -24px;
      width: 100px; height: 100px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
    }
 
    .car-info { flex: 1; }
 
    .car-name {
      font-size: 20px;
      font-weight: 900;
      color: #fff;
      letter-spacing: -0.02em;
      margin-bottom: 8px;
    }
 
    .car-meta {
      font-size: 12px;
      color: rgba(255,255,255,.55);
      font-weight: 600;
      display: flex;
      flex-direction: column;
      gap: 4px;
      letter-spacing: 0.02em;
    }
 
    .car-badge {
      display: inline-block;
      background: var(--primary-dark);
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 3px 10px;
      border-radius: var(--radius-sm);
      margin-bottom: 8px;
    }
 
    .car-price-block {
      text-align: right;
      flex-shrink: 0;
    }
 
    .car-price {
      font-size: 22px;
      font-weight: 900;
      color: var(--amber);
      letter-spacing: -0.02em;
    }
 
    .car-price-label {
      font-size: 11px;
      font-weight: 600;
      color: rgba(255,255,255,.45);
      text-align: right;
      margin-top: 2px;
    }
 
    .car-icon-bg {
      position: absolute;
      right: 20px;
      bottom: 8px;
      opacity: .06;
    }
 
    .car-icon-bg svg {
      width: 80px; height: 80px;
      stroke: #fff; stroke-width: 1;
      fill: none;
    }
 
    /* ── Map ── */
    #map {
      height: 220px;
      border-radius: var(--radius-md);
      overflow: hidden;
      margin-top: 12px;
      border: 1px solid var(--outline-mid);
    }
 
    /* ── Submit button ── */
    .btn-submit {
      width: 100%;
      height: 56px;
      border: none;
      border-radius: var(--radius);
      background: var(--amber);
      color: var(--charcoal);
      font-family: 'Montserrat', sans-serif;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      cursor: pointer;
      margin-top: 32px;
      transition: transform .15s, box-shadow .15s, background .15s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 4px 16px rgba(253,192,3,.35);
    }
 
    .btn-submit svg {
      width: 20px; height: 20px;
      stroke: var(--charcoal); stroke-width: 2.5;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .btn-submit:hover {
      background: var(--amber-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(253,192,3,.45);
    }
 
    .btn-submit:active { transform: translateY(0); }
 
    /* ── Ghost back link ── */
    .btn-back {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 14px;
      background: transparent;
      color: var(--text-subtle);
      font-family: 'Montserrat', sans-serif;
      font-size: 13px;
      font-weight: 600;
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      cursor: pointer;
      text-decoration: none;
      margin-top: 12px;
      transition: border-color .15s, color .15s;
      letter-spacing: 0.03em;
    }
 
    .btn-back:hover {
      border-color: var(--primary-dark);
      color: var(--primary-dark);
    }
 
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 480px) {
      .card { padding: 24px 18px 28px; }
      .car-name { font-size: 17px; }
      .car-price { font-size: 18px; }
    }
  </style>
</head>
<body>
 
<div class="page-wrap">
 
  <div class="brand">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24">
        <rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <div class="logo">INDOMAX<span>-RENT</span></div>
  </div>
  <p class="page-sub">Atur perjalanan rentalmu</p>
 
  <form class="card" action="status_booking.php" method="POST">
 
    <!-- Route -->
    <div class="section-label">Rute Perjalanan</div>
    <div class="input-group">
      <div class="field">
        <svg class="field-icon" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/>
          <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
        </svg>
        <input type="text" name="jemput" placeholder="Titik penjemputan" required/>
      </div>
      <div class="connector"><div class="connector-line"></div></div>
      <div class="field">
        <svg class="field-icon" viewBox="0 0 24 24">
          <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        <input type="text" name="tujuan" placeholder="Lokasi tujuan" required/>
      </div>
    </div>
 
    <!-- Date -->
    <div class="section-label">Tanggal Rental</div>
    <div class="field">
      <svg class="field-icon" viewBox="0 0 24 24">
        <rect x="3" y="4" width="18" height="18" rx="2"/>
        <path d="M16 2v4M8 2v4M3 10h18"/>
      </svg>
      <input type="date" name="tanggal" required/>
    </div>
 
    <!-- Car -->
    <div class="section-label">Kendaraan Dipilih</div>
    <div class="car-card">
      <div class="car-info">
        <div class="car-badge">MPV · 7 Kursi</div>
        <div class="car-name">Toyota Avanza</div>
        <div class="car-meta">
          <span>Plat: H 0102 AC</span>
          <span>Transmisi: Manual</span>
        </div>
      </div>
      <div class="car-price-block">
        <div class="car-price">Rp275.000</div>
        <div class="car-price-label">per hari</div>
      </div>
      <div class="car-icon-bg">
        <svg viewBox="0 0 24 24">
          <rect x="1" y="3" width="15" height="13" rx="2"/>
          <path d="M16 8h4l3 5v3h-7V8z"/>
          <circle cx="5.5" cy="18.5" r="2.5"/>
          <circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
      </div>
    </div>
 
    <!-- Map -->
    <div class="section-label">Pratinjau Rute</div>
    <div id="map"></div>
 
    <!-- Submit -->
    <button type="submit" class="btn-submit">
      <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      Pesan Sekarang
    </button>
 
    <a class="btn-back" href="riwayat.php">&#8592; Lihat Riwayat Booking</a>
 
  </form>
</div>
 
<script>
  var map = L.map('map').setView([-6.9667, 110.4167], 13);
 
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);
 
  var iconJemput = L.divIcon({
    className: '',
    html: '<div style="width:14px;height:14px;background:#cc0000;border:3px solid #fff;border-radius:50%;box-shadow:0 0 8px rgba(204,0,0,.5)"></div>',
    iconAnchor: [7, 7]
  });
 
  var iconTujuan = L.divIcon({
    className: '',
    html: '<div style="width:14px;height:14px;background:#fdc003;border:3px solid #fff;border-radius:50%;box-shadow:0 0 8px rgba(253,192,3,.5)"></div>',
    iconAnchor: [7, 7]
  });
 
  L.marker([-6.980, 110.420], {icon: iconJemput}).addTo(map).bindPopup('Titik Penjemputan');
  L.marker([-6.970, 110.460], {icon: iconTujuan}).addTo(map).bindPopup('Lokasi Tujuan');
 
  var route = [[-6.980,110.420],[-6.976,110.430],[-6.973,110.445],[-6.970,110.460]];
 
  L.polyline(route, {
    color: '#cc0000',
    weight: 4,
    dashArray: '8, 6',
    opacity: .85
  }).addTo(map);
 
  map.fitBounds(L.polyline(route).getBounds(), {padding: [20, 20]});
</script>
 
</body>
</html>
 