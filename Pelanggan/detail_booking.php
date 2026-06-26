<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Booking — INDOMAX-RENT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
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
      --blue:         #2563eb;
      --blue-bg:      #eff6ff;
      --purple:       #7c3aed;
      --purple-bg:    #f5f3ff;
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
 
    .card {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-xl);
      padding: 36px 32px 40px;
      max-width: 480px;
      margin: 0 auto;
      animation: slideUp .5s cubic-bezier(.22,1,.36,1) both;
    }
 
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    /* ── Brand ── */
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 6px;
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
    }
 
    .page-sub {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 28px;
    }
 
    /* ── Code banner ── */
    .code-banner {
      background: var(--charcoal);
      border-radius: var(--radius-lg);
      padding: 18px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden;
      animation: fadeUp .4s ease .1s both;
    }
 
    .code-banner::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 5px;
      background: var(--primary-dark);
    }
 
    .code-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: rgba(255,255,255,.45);
      margin-bottom: 4px;
    }
 
    .code-val {
      font-size: 28px;
      font-weight: 900;
      letter-spacing: 0.04em;
      color: #fff;
    }
 
    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(204,0,0,.15);
      border: 1px solid rgba(204,0,0,.35);
      color: #ff6b6b;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 7px 14px;
      border-radius: var(--radius-sm);
    }
 
    .status-chip::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: #ff6b6b;
      animation: blink 1.4s ease-in-out infinite;
    }
 
    @keyframes blink {
      0%, 100% { opacity: 1; }
      50%       { opacity: .3; }
    }
 
    /* ── Section label ── */
    .section-label {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 12px;
      margin-top: 24px;
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
 
    /* ── Car card ── */
    .car-card {
      background: var(--charcoal);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      position: relative;
      overflow: hidden;
      animation: fadeUp .4s ease .15s both;
    }
 
    .car-card::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 5px;
      background: var(--primary-dark);
    }
 
    .car-badge {
      display: inline-block;
      background: var(--primary-dark);
      color: #fff;
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 3px 8px;
      border-radius: var(--radius-sm);
      margin-bottom: 6px;
    }
 
    .car-name {
      font-size: 20px;
      font-weight: 900;
      color: #fff;
      letter-spacing: -0.02em;
      margin-bottom: 4px;
    }
 
    .car-meta {
      font-size: 11px;
      color: rgba(255,255,255,.5);
      font-weight: 600;
    }
 
    .car-price { text-align: right; flex-shrink: 0; }
 
    .car-price .amount {
      font-size: 20px;
      font-weight: 900;
      color: var(--amber);
      letter-spacing: -0.02em;
    }
 
    .car-price .per {
      font-size: 10px;
      color: rgba(255,255,255,.4);
      font-weight: 600;
      margin-top: 2px;
    }
 
    /* ── Detail grid ── */
    .detail-grid {
      display: flex;
      flex-direction: column;
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-lg);
      overflow: hidden;
      animation: fadeUp .4s ease .2s both;
    }
 
    .detail-row {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 16px 20px;
      border-bottom: 1px solid var(--outline-mid);
      transition: background .15s;
    }
 
    .detail-row:last-child { border-bottom: none; }
    .detail-row:hover { background: var(--surface-low); }
 
    .row-icon {
      width: 36px; height: 36px;
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
 
    .row-icon svg {
      width: 17px; height: 17px;
      stroke-width: 2;
      fill: none;
      stroke-linecap: round;
      stroke-linejoin: round;
    }
 
    .icon-red    { background: rgba(204,0,0,.08); }
    .icon-red    svg { stroke: var(--primary-dark); }
    .icon-amber  { background: rgba(253,192,3,.12); }
    .icon-amber  svg { stroke: var(--amber-dark); }
    .icon-blue   { background: var(--blue-bg); }
    .icon-blue   svg { stroke: var(--blue); }
    .icon-purple { background: var(--purple-bg); }
    .icon-purple svg { stroke: var(--purple); }
 
    .row-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-subtle);
      margin-bottom: 2px;
    }
 
    .row-value {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
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
      animation: fadeUp .4s ease .25s both;
    }
 
    .driver-avatar {
      width: 48px; height: 48px;
      border-radius: 50%;
      background: var(--charcoal);
      border: 2px solid var(--outline-mid);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
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
      display: flex; align-items: center; gap: 4px;
      font-size: 15px; font-weight: 900;
      color: var(--amber-dark);
    }
 
    .driver-rating svg {
      width: 14px; height: 14px;
      fill: var(--amber-dark); stroke: none;
    }
 
    /* ── Buttons ── */
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
      text-decoration: none;
      margin-top: 28px;
      animation: fadeUp .4s ease .35s both;
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
 
    .btn-secondary {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 14px;
      background: transparent;
      color: var(--text-subtle);
      font-family: 'Montserrat', sans-serif;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      cursor: pointer;
      text-decoration: none;
      margin-top: 10px;
      transition: border-color .15s, color .15s;
      animation: fadeUp .4s ease .4s both;
    }
 
    .btn-secondary:hover {
      border-color: var(--primary-dark);
      color: var(--primary-dark);
    }
 
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 480px) {
      body { padding: 32px 16px; }
      .card { padding: 28px 20px 32px; }
    }
  </style>
</head>
<body>
 
<div class="card">
 
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
 
  <h1 class="page-title">Detail Booking</h1>
  <p class="page-sub">Informasi lengkap pemesanan Anda</p>
 
  <!-- Booking code -->
  <div class="code-banner">
    <div>
      <div class="code-label">Kode Booking</div>
      <div class="code-val">BK001</div>
    </div>
    <span class="status-chip">Aktif</span>
  </div>
 
  <!-- Car card -->
  <div class="section-label">Kendaraan</div>
  <div class="car-card">
    <div>
      <div class="car-badge">MPV · 7 Kursi</div>
      <div class="car-name">Toyota Avanza</div>
      <div class="car-meta">Plat: H 0102 AC &nbsp;&middot;&nbsp; 7 Penumpang</div>
    </div>
    <div class="car-price">
      <div class="amount">Rp275.000</div>
      <div class="per">per hari</div>
    </div>
  </div>
 
  <!-- Detail rows -->
  <div class="section-label">Informasi Perjalanan</div>
  <div class="detail-grid">
 
    <div class="detail-row">
      <div class="row-icon icon-red">
        <svg viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="3"/>
          <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
        </svg>
      </div>
      <div>
        <div class="row-label">Titik Jemput</div>
        <div class="row-value">Jl. Pemuda No. 12, Semarang</div>
      </div>
    </div>
 
    <div class="detail-row">
      <div class="row-icon icon-amber">
        <svg viewBox="0 0 24 24">
          <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
      <div>
        <div class="row-label">Tujuan</div>
        <div class="row-value">Bandara Ahmad Yani, Semarang</div>
      </div>
    </div>
 
    <div class="detail-row">
      <div class="row-icon icon-blue">
        <svg viewBox="0 0 24 24">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
      </div>
      <div>
        <div class="row-label">Tanggal</div>
        <div class="row-value">25 Juni 2026</div>
      </div>
    </div>
 
    <div class="detail-row">
      <div class="row-icon icon-purple">
        <svg viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/>
          <path d="M12 6v6l4 2"/>
        </svg>
      </div>
      <div>
        <div class="row-label">Estimasi Durasi</div>
        <div class="row-value">± 25 Menit</div>
      </div>
    </div>
 
  </div>
 
  <!-- Driver -->
  <div class="section-label">Pengemudi</div>
  <div class="driver-card">
    <div class="driver-avatar">&#128100;</div>
    <div class="driver-info">
      <div class="name">Budi Santoso</div>
      <div class="role">Pengemudi &middot; 3 tahun pengalaman</div>
    </div>
    <div class="driver-rating">
      <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      4.9
    </div>
  </div>
 
  <!-- Actions -->
  <a class="btn-primary" href="tracking.php">
    <svg viewBox="0 0 24 24">
      <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/>
      <circle cx="12" cy="10" r="3"/>
    </svg>
    Lacak Kendaraan
  </a>
  <a class="btn-secondary" href="status_booking.php">
    &#8592; Kembali ke Status Booking
  </a>
 
</div>
 
</body>
</html>
 