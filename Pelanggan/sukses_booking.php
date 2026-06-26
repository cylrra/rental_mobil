<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Driver — INDOMAX-RENT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
    :root {
      --primary-dark: #cc0000;
      --amber:        #fdc003;
      --amber-dark:   #e6a800;
      --charcoal:     #1a1c1c;
      --surface:      #f9f9f9;
      --surface-low:  #f3f3f3;
      --surface-card: #ffffff;
      --outline-mid:  #d0d0d0;
      --text-main:    #1a1c1c;
      --text-muted:   #5e3f3a;
      --text-subtle:  #926e69;
      --green:        #16a34a;
      --green-bg:     #dcfce7;
      --radius-sm:    4px;
      --radius:       8px;
      --radius-md:    12px;
      --radius-lg:    16px;
      --radius-xl:    24px;
    }
 
    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--surface);
      color: var(--text-main);
      min-height: 100vh;
      padding: 48px 24px;
      overflow-y: auto;
    }
 
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-dark) 0%, var(--amber) 100%);
      z-index: 100;
    }
 
    .page-wrap {
      max-width: 480px;
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
 
    .logo span { color: var(--amber-dark); }
 
    .page-title {
      font-size: 28px;
      font-weight: 900;
      letter-spacing: -0.02em;
      margin: 24px 0 4px;
      animation: fadeUp .4s ease .05s both;
    }
 
    .page-sub {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 24px;
      animation: fadeUp .4s ease .08s both;
    }
 
    /* ── Status banner ── */
    .status-banner {
      background: var(--charcoal);
      border-radius: var(--radius-lg);
      padding: 20px 24px;
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 16px;
      position: relative;
      overflow: hidden;
      animation: fadeUp .4s ease .1s both;
      transition: all .4s ease;
    }
 
    .status-banner::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 5px;
      background: var(--primary-dark);
      transition: background .4s ease;
    }
 
    .status-banner.arrived::before  { background: var(--amber); }
    .status-banner.done::before     { background: var(--green); }
 
    .status-pulse {
      width: 44px; height: 44px;
      border-radius: 50%;
      background: rgba(204,0,0,.15);
      border: 2px solid rgba(204,0,0,.4);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      position: relative;
      transition: all .4s ease;
    }
 
    .status-pulse::after {
      content: '';
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      border: 2px solid var(--primary-dark);
      animation: pulse 1.8s ease-out infinite;
      transition: border-color .4s ease;
    }
 
    .status-banner.arrived .status-pulse         { background: rgba(253,192,3,.15); border-color: rgba(253,192,3,.4); }
    .status-banner.arrived .status-pulse::after  { border-color: var(--amber); }
    .status-banner.done .status-pulse            { background: rgba(22,163,74,.12); border-color: rgba(22,163,74,.4); }
    .status-banner.done .status-pulse::after     { border-color: var(--green); animation: none; opacity: 0; }
 
    @keyframes pulse {
      0%   { transform: scale(.85); opacity: .6; }
      100% { transform: scale(1.5); opacity: 0; }
    }
 
    .status-pulse svg {
      width: 20px; height: 20px;
      stroke: var(--primary-dark); stroke-width: 2;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
      transition: stroke .4s ease;
    }
 
    .status-banner.arrived .status-pulse svg { stroke: var(--amber); }
    .status-banner.done    .status-pulse svg { stroke: var(--green); }
 
    .status-text .label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,.45);
      margin-bottom: 4px;
    }
 
    .status-text .value {
      font-size: 17px;
      font-weight: 900;
      color: #fff;
      letter-spacing: -0.01em;
      transition: color .3s ease;
    }
 
    .status-text .sub-info {
      font-size: 11px;
      font-weight: 600;
      color: rgba(255,255,255,.45);
      margin-top: 3px;
    }
 
    /* ── ETA badge ── */
    .eta-badge {
      margin-left: auto;
      text-align: center;
      background: rgba(253,192,3,.12);
      border: 1px solid rgba(253,192,3,.35);
      border-radius: var(--radius);
      padding: 10px 16px;
      flex-shrink: 0;
      transition: all .4s ease;
    }
 
    .status-banner.done .eta-badge {
      background: rgba(22,163,74,.1);
      border-color: rgba(22,163,74,.3);
    }
 
    .eta-badge .eta-val {
      font-size: 24px;
      font-weight: 900;
      color: var(--amber);
      line-height: 1;
      letter-spacing: -0.02em;
      transition: color .4s ease;
    }
 
    .status-banner.done .eta-badge .eta-val { color: var(--green); }
 
    .eta-badge .eta-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: rgba(253,192,3,.6);
      margin-top: 3px;
      transition: color .4s ease;
    }
 
    .status-banner.done .eta-badge .eta-label { color: rgba(22,163,74,.7); }
 
    /* ── Map ── */
    .section-label {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
      animation: fadeUp .4s ease .13s both;
    }
 
    .section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--outline-mid);
    }
 
    #map {
      height: 260px;
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--outline-mid);
      margin-bottom: 16px;
      animation: fadeUp .4s ease .16s both;
    }
 
    /* ── Car info card ── */
    .car-card {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-lg);
      padding: 18px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 20px;
      animation: fadeUp .4s ease .2s both;
    }
 
    .car-thumb {
      width: 44px; height: 44px;
      background: rgba(204,0,0,.08);
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
 
    .car-thumb svg {
      width: 22px; height: 22px;
      stroke: var(--primary-dark); stroke-width: 1.8;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .car-info { flex: 1; }
 
    .car-info-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-subtle);
      margin-bottom: 2px;
    }
 
    .car-info-name {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
    }
 
    .car-info-plate {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-subtle);
      margin-top: 1px;
    }
 
    .car-badge {
      background: var(--primary-dark);
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: var(--radius-sm);
      flex-shrink: 0;
    }
 
    /* ── Button ── */
    .btn-primary {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 16px;
      background: var(--amber);
      color: var(--charcoal);
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      box-shadow: 0 4px 16px rgba(253,192,3,.35);
      transition: transform .15s, box-shadow .15s, background .15s;
      animation: fadeUp .4s ease .25s both;
    }
 
    .btn-primary svg {
      width: 18px; height: 18px;
      stroke: var(--charcoal); stroke-width: 2.5;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .btn-primary:hover {
      background: var(--amber-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(253,192,3,.45);
    }
 
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 480px) {
      body { padding: 32px 16px; }
      #map { height: 220px; }
    }
  </style>
</head>
<body>
 
<div class="page-wrap">
 
  <!-- Brand -->
  <div class="brand">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24">
        <rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <span class="logo">INDOMAX<span>-RENT</span></span>
  </div>
 
  <h1 class="page-title">Status Driver</h1>
  <p class="page-sub">Posisi pengemudi secara real-time</p>
 
  <!-- Status banner -->
  <div class="status-banner" id="statusBanner">
    <div class="status-pulse" id="statusPulse">
      <svg viewBox="0 0 24 24">
        <rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <div class="status-text">
      <div class="label">Status Saat Ini</div>
      <div class="value" id="status">Driver menuju lokasi</div>
      <div class="sub-info" id="info">Estimasi 7 menit</div>
    </div>
    <div class="eta-badge">
      <div class="eta-val" id="etaVal">7</div>
      <div class="eta-label" id="etaLabel">Menit</div>
    </div>
  </div>
 
  <!-- Map -->
  <div class="section-label">Lokasi Real-Time</div>
  <div id="map"></div>
 
  <!-- Car info -->
  <div class="car-card">
    <div class="car-thumb">
      <svg viewBox="0 0 24 24">
        <rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <div class="car-info">
      <div class="car-info-label">Kendaraan</div>
      <div class="car-info-name">Toyota Avanza</div>
      <div class="car-info-plate">H 1234 AA</div>
    </div>
    <div class="car-badge">MPV</div>
  </div>
 
  <!-- Button -->
  <button class="btn-primary" onclick="location.href='tracking.php'">
    <svg viewBox="0 0 24 24">
      <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/>
      <circle cx="12" cy="10" r="3"/>
    </svg>
    Lihat Perjalanan
  </button>
 
</div>
 
<script>
  var map = L.map('map').setView([-6.98, 110.41], 13);
 
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);
 
  var tujuan = [-6.98, 110.41];
 
  var iconTujuan = L.divIcon({
    className: '',
    html: '<div style="width:14px;height:14px;background:#fdc003;border:3px solid #fff;border-radius:50%;box-shadow:0 0 8px rgba(253,192,3,.7)"></div>',
    iconAnchor: [7, 7]
  });
 
  var iconDriver = L.divIcon({
    className: '',
    html: '<div style="width:28px;height:28px;background:#1a1c1c;border:3px solid #cc0000;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 14px rgba(204,0,0,.6);font-size:13px;">🚗</div>',
    iconAnchor: [14, 14]
  });
 
  L.marker(tujuan, { icon: iconTujuan }).addTo(map).bindPopup('📍 Titik Penjemputan');
 
  var driver = L.marker([-7.02, 110.35], { icon: iconDriver }).addTo(map).bindPopup('🚗 Toyota Avanza — H 1234 AA');
 
  var step = 0;
  var etaVal = 7;
 
  var timer = setInterval(function() {
    step++;
 
    var p = driver.getLatLng();
    driver.setLatLng([p.lat + 0.004, p.lng + 0.004]);
 
    if (etaVal > 0) {
      etaVal--;
      document.getElementById('etaVal').innerHTML = etaVal;
    }
 
    if (step == 6) {
      document.getElementById('status').innerHTML = 'Driver tiba';
      document.getElementById('info').innerHTML   = 'Silakan naik';
      document.getElementById('etaVal').innerHTML  = '✓';
      document.getElementById('etaLabel').innerHTML = 'Tiba';
      document.getElementById('statusBanner').classList.add('arrived');
    }
 
    if (step == 10) {
      document.getElementById('status').innerHTML = 'Perjalanan dimulai';
      document.getElementById('info').innerHTML   = 'Menuju tujuan';
    }
 
    if (step == 16) {
      document.getElementById('status').innerHTML  = 'Perjalanan selesai';
      document.getElementById('info').innerHTML    = 'Terima kasih!';
      document.getElementById('etaVal').innerHTML  = '✓';
      document.getElementById('etaLabel').innerHTML = 'Selesai';
      document.getElementById('statusBanner').classList.remove('arrived');
      document.getElementById('statusBanner').classList.add('done');
      clearInterval(timer);
    }
 
  }, 2000);
</script>
 
</body>
</html>
 