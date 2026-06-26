<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$id_sewa = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;
$id_pelanggan = $_SESSION['id_pelanggan'];

$query = "SELECT t.*, m.merk, m.jenis, m.nopol, s.nama_supir, s.no_telp as no_telp_supir 
          FROM transaksi_sewa t 
          LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
          LEFT JOIN supir s ON t.id_supir = s.id_supir 
          WHERE t.id_sewa = '$id_sewa' AND t.id_pelanggan = '$id_pelanggan'";
$res = mysqli_query($conn, $query);

if (!$res || mysqli_num_rows($res) == 0) {
    echo "<script>alert('Transaksi tidak ditemukan atau akses ditolak!'); window.location='transaksi.php';</script>";
    exit();
}

$data = mysqli_fetch_assoc($res);
$mobil_merk = htmlspecialchars($data['merk']);
$mobil_jenis = htmlspecialchars($data['jenis']);
$mobil_full = $mobil_merk . " " . $mobil_jenis;
$mobil_nopol = htmlspecialchars($data['nopol']);
$pake_supir = $data['pake_supir'];
$nama_supir = ($pake_supir == 'Ya' && !empty($data['nama_supir'])) ? htmlspecialchars($data['nama_supir']) : 'Sewa Lepas Kunci';
$role_supir = ($pake_supir == 'Ya') ? 'Pengemudi &middot; ' . $mobil_merk : 'Anda mengemudikan kendaraan ini sendiri';

$status_sewa = $data['status_sewa'];
$status_text = 'Menunggu Pembayaran / Persiapan';
$eta_val = '--';
$eta_label = 'Menit';

if ($status_sewa == 'berjalan') {
    $status_text = 'Dalam Perjalanan (Berjalan)';
    $eta_val = '12';
} else if ($status_sewa == 'selesai') {
    $status_text = 'Perjalanan Selesai';
    $eta_val = '0';
} else if ($status_sewa == 'dp' || $status_sewa == 'diterima') {
    $status_text = 'Kendaraan Sedang Disiapkan';
    $eta_val = '45';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracking #<?= $id_sewa ?> — INDOMAX-RENT</title>
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
      max-width: 520px;
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
    }
 
    .status-banner::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 5px;
      background: <?php echo ($status_sewa == 'selesai') ? 'var(--green)' : 'var(--primary-dark)'; ?>;
    }
 
    .status-pulse {
      width: 44px; height: 44px;
      border-radius: 50%;
      background: <?php echo ($status_sewa == 'selesai') ? 'rgba(22,163,74,.15)' : 'rgba(204,0,0,.15)'; ?>;
      border: 2px solid <?php echo ($status_sewa == 'selesai') ? 'rgba(22,163,74,.4)' : 'rgba(204,0,0,.4)'; ?>;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      position: relative;
    }
 
    .status-pulse::after {
      content: '';
      position: absolute;
      inset: -6px;
      border-radius: 50%;
      border: 2px solid <?php echo ($status_sewa == 'selesai') ? 'var(--green)' : 'var(--primary-dark)'; ?>;
      animation: pulse 1.8s ease-out infinite;
      <?php if ($status_sewa == 'selesai') echo 'animation: none; opacity: 1;'; ?>
    }
 
    @keyframes pulse {
      0%   { transform: scale(.85); opacity: .6; }
      100% { transform: scale(1.5); opacity: 0; }
    }
 
    .status-pulse svg {
      width: 20px; height: 20px;
      stroke: <?php echo ($status_sewa == 'selesai') ? 'var(--green)' : 'var(--primary-dark)'; ?>; stroke-width: 2;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .status-text .label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,.45);
      margin-bottom: 4px;
    }
 
    .status-text .value {
      font-size: 16px;
      font-weight: 700;
      color: #fff;
      letter-spacing: -0.01em;
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
    }
 
    .eta-badge .eta-val {
      font-size: 24px;
      font-weight: 900;
      color: var(--amber);
      line-height: 1;
      letter-spacing: -0.02em;
    }
 
    .eta-badge .eta-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: rgba(253,192,3,.6);
      margin-top: 3px;
    }
 
    /* ── Info grid ── */
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 16px;
      animation: fadeUp .4s ease .2s both;
    }
 
    .info-box {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      padding: 14px 16px;
    }
 
    .info-box .label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-subtle);
      margin-bottom: 4px;
    }
 
    .info-box .value {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
    }
 
    /* ── Section label ── */
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
      animation: fadeUp .4s ease .25s both;
    }
 
    .section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--outline-mid);
    }
 
    /* ── Map ── */
    #map {
      height: 260px;
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--outline-mid);
      margin-bottom: 16px;
      animation: fadeUp .4s ease .3s both;
    }
 
    /* ── Driver card ── */
    .driver-card {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-lg);
      padding: 18px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 24px;
      animation: fadeUp .4s ease .35s both;
    }
 
    .driver-avatar {
      width: 48px; height: 48px;
      border-radius: 50%;
      background: var(--charcoal);
      border: 2px solid var(--outline-mid);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      font-size: 20px;
      color: white;
    }
 
    .driver-info .name {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
    }
 
    .driver-info .role {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-subtle);
      margin-top: 2px;
    }
 
    .driver-rating {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 15px;
      font-weight: 900;
      color: var(--amber-dark);
    }
 
    .driver-rating svg {
      width: 14px; height: 14px;
      fill: var(--amber-dark);
      stroke: none;
    }
 
    /* ── Button ── */
    .btn-close {
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
      text-decoration: none;
      animation: fadeUp .4s ease .45s both;
    }
 
    .btn-close svg {
      width: 18px; height: 18px;
      stroke: var(--charcoal); stroke-width: 2.5;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .btn-close:hover {
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
      .info-grid { grid-template-columns: 1fr; }
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
 
  <h1 class="page-title">Tracking Pesanan #<?= $id_sewa ?></h1>
  <p class="page-sub">Pantau posisi kendaraan secara langsung</p>
 
  <!-- Status banner + ETA -->
  <div class="status-banner">
    <div class="status-pulse">
      <svg viewBox="0 0 24 24">
        <?php if ($status_sewa == 'selesai') { ?>
            <path d="M5 13l4 4L19 7"/>
        <?php } else { ?>
            <rect x="1" y="3" width="15" height="13" rx="2"/>
            <path d="M16 8h4l3 5v3h-7V8z"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
        <?php } ?>
      </svg>
    </div>
    <div class="status-text">
      <div class="label">Status Saat Ini</div>
      <div class="value"><?= $status_text ?></div>
    </div>
    <div class="eta-badge">
      <div class="eta-val"><?= $eta_val ?></div>
      <div class="eta-label"><?= $eta_label ?></div>
    </div>
  </div>
 
  <!-- Info grid -->
  <div class="info-grid">
    <div class="info-box">
      <div class="label">Kendaraan</div>
      <div class="value"><?= $mobil_full ?></div>
    </div>
    <div class="info-box">
      <div class="label">Plat Nomor</div>
      <div class="value"><?= $mobil_nopol ?></div>
    </div>
  </div>
 
  <!-- Map -->
  <div class="section-label">Lokasi Real-Time (Visualisasi Virtual)</div>
  <div id="map"></div>
 
  <!-- Driver -->
  <div class="driver-card">
    <div class="driver-avatar"><?php echo ($pake_supir == 'Ya') ? '&#128104;' : '&#128273;'; ?></div>
    <div class="driver-info">
      <div class="name"><?= $nama_supir ?></div>
      <div class="role"><?= $role_supir ?></div>
    </div>
    <?php if ($pake_supir == 'Ya') { ?>
    <div class="driver-rating">
      <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      5.0
    </div>
    <?php } ?>
  </div>
 
  <!-- Close button -->
  <a class="btn-close" href="transaksi.php">
    <svg viewBox="0 0 24 24">
      <path d="M5 12h14M12 5l7 7-7 7"/>
    </svg>
    Selesai &amp; Kembali
  </a>
 
</div>
 
<script>
  var map = L.map('map').setView([-6.9667, 110.4167], 14);
 
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);
 
  var dotRed = L.divIcon({
    className: '',
    html: '<div style="width:14px;height:14px;background:#cc0000;border:3px solid #fff;border-radius:50%;box-shadow:0 0 10px rgba(204,0,0,.7)"></div>',
    iconAnchor: [7, 7]
  });
 
  var dotAmber = L.divIcon({
    className: '',
    html: '<div style="width:14px;height:14px;background:#fdc003;border:3px solid #fff;border-radius:50%;box-shadow:0 0 10px rgba(253,192,3,.7)"></div>',
    iconAnchor: [7, 7]
  });
 
  var carMarker = L.divIcon({
    className: '',
    html: '<div style="width:28px;height:28px;background:#1a1c1c;border:3px solid #cc0000;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 16px rgba(204,0,0,.6);font-size:13px;">&#128663;</div>',
    iconAnchor: [14, 14]
  });
 
  var route = [
    [-6.980, 110.420],
    [-6.976, 110.430],
    [-6.973, 110.445],
    [-6.970, 110.460]
  ];
 
  L.marker(route[0], {icon: dotRed}).addTo(map).bindPopup('&#127974; Lokasi Penjemputan');
  L.marker(route[route.length - 1], {icon: dotAmber}).addTo(map).bindPopup('&#128205; Lokasi Tujuan');
 
  var carPos = route[1];
  L.marker(carPos, {icon: carMarker}).addTo(map).bindPopup('&#128663; <?= $mobil_full ?> — <?= $mobil_nopol ?>');
 
  L.polyline(route, {
    color: '#d0d0d0',
    weight: 3,
    dashArray: '6, 5',
    opacity: .6
  }).addTo(map);
 
  L.polyline([route[0], route[1]], {
    color: '#cc0000',
    weight: 4,
    opacity: .9
  }).addTo(map);
 
  map.fitBounds(L.polyline(route).getBounds(), {padding: [24, 24]});
</script>
 
</body>
</html>