<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi — INDOMAX-RENT</title>
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
      --green:        #16a34a;
      --green-bg:     #dcfce7;
      --blue:         #2563eb;
      --blue-bg:      #eff6ff;
      --radius-sm:    4px;
      --radius:       8px;
      --radius-md:    12px;
      --radius-lg:    16px;
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

    /* ── Header ── */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 24px 0 8px;
      animation: fadeUp .4s ease .05s both;
    }

    .page-header h1 {
      font-size: 28px;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    .badge-count {
      background: var(--primary-dark);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.04em;
      padding: 5px 12px;
      border-radius: var(--radius-sm);
    }

    .page-sub {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 32px;
      animation: fadeUp .4s ease .08s both;
    }

    /* ── Timeline ── */
    .timeline {
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .timeline::before {
      content: '';
      position: absolute;
      left: 23px;
      top: 28px;
      bottom: 28px;
      width: 2px;
      background: linear-gradient(to bottom, var(--primary-dark), var(--outline-mid));
      border-radius: 2px;
    }

    /* ── Notif item ── */
    .notif-item {
      display: flex;
      gap: 20px;
      align-items: flex-start;
      padding: 0 0 20px;
      position: relative;
      animation: fadeUp .4s ease both;
    }

    .notif-item:nth-child(1) { animation-delay: .1s; }
    .notif-item:nth-child(2) { animation-delay: .22s; }
    .notif-item:nth-child(3) { animation-delay: .34s; }

    .notif-icon {
      width: 48px; height: 48px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      position: relative;
      z-index: 1;
      border: 2px solid var(--outline-mid);
      background: var(--surface-card);
    }

    .notif-icon svg {
      width: 20px; height: 20px;
      stroke-width: 2;
      fill: none;
      stroke-linecap: round; stroke-linejoin: round;
    }

    .icon-red   { border-color: rgba(204,0,0,.3);   background: rgba(204,0,0,.06); }
    .icon-red   svg { stroke: var(--primary-dark); }
    .icon-amber { border-color: rgba(253,192,3,.4); background: rgba(253,192,3,.08); }
    .icon-amber svg { stroke: var(--amber-dark); }
    .icon-blue  { border-color: rgba(37,99,235,.3); background: var(--blue-bg); }
    .icon-blue  svg { stroke: var(--blue); }

    /* ── Card body ── */
    .notif-body {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-lg);
      padding: 16px 18px;
      flex: 1;
      transition: border-color .2s, box-shadow .2s;
    }

    .notif-body:hover {
      border-color: var(--primary-dark);
      box-shadow: 0 4px 16px rgba(204,0,0,.06);
    }

    .notif-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 6px;
      gap: 8px;
    }

    .notif-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
    }

    .notif-time {
      font-size: 10px;
      font-weight: 700;
      color: var(--text-subtle);
      white-space: nowrap;
      letter-spacing: 0.04em;
      padding-top: 2px;
    }

    .notif-desc {
      font-size: 12px;
      font-weight: 500;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 12px;
    }

    /* ── Bottom row: chip + action button ── */
    .notif-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .notif-chip {
      display: inline-block;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: var(--radius-sm);
    }

    .chip-red   { background: rgba(204,0,0,.08); color: var(--primary-dark); border: 1px solid rgba(204,0,0,.15); }
    .chip-amber { background: rgba(253,192,3,.1); color: var(--amber-dark);  border: 1px solid rgba(253,192,3,.3); }
    .chip-blue  { background: var(--blue-bg);     color: var(--blue);        border: 1px solid rgba(37,99,235,.2); }

    /* action link button */
    .notif-action {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-decoration: none;
      padding: 6px 12px;
      border-radius: var(--radius-sm);
      border: 1.5px solid;
      transition: background .15s, color .15s;
      white-space: nowrap;
    }

    .notif-action svg {
      width: 12px; height: 12px;
      stroke-width: 2.5;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }

    .action-red {
      color: var(--primary-dark);
      border-color: rgba(204,0,0,.3);
      background: transparent;
    }
    .action-red:hover {
      background: var(--primary-dark);
      color: #fff;
    }
    .action-red:hover svg { stroke: #fff; }

    .action-amber {
      color: var(--amber-dark);
      border-color: rgba(230,168,0,.4);
      background: transparent;
    }
    .action-amber:hover {
      background: var(--amber-dark);
      color: #fff;
    }
    .action-amber:hover svg { stroke: #fff; }

    .action-blue {
      color: var(--blue);
      border-color: rgba(37,99,235,.3);
      background: transparent;
    }
    .action-blue:hover {
      background: var(--blue);
      color: #fff;
    }
    .action-blue:hover svg { stroke: #fff; }

    /* ── Back button ── */
    .btn-back {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      margin-top: 8px;
      padding: 14px;
      background: transparent;
      color: var(--text-subtle);
      font-family: 'Montserrat', sans-serif;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      cursor: pointer;
      text-decoration: none;
      transition: border-color .15s, color .15s;
      animation: fadeUp .4s ease .5s both;
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
      body { padding: 32px 16px; }
      .timeline::before { left: 19px; }
      .notif-icon { width: 40px; height: 40px; }
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
    <span class="logo">INDOMAX<span>-RENT</span></span>
  </div>

  <div class="page-header">
    <h1>Notifikasi</h1>
    <span class="badge-count">3 Baru</span>
  </div>
  <p class="page-sub">Pembaruan perjalanan Anda</p>

  <div class="timeline">

    <!-- 1: Booking dikonfirmasi → detail_booking.php -->
    <div class="notif-item">
      <div class="notif-icon icon-red">
        <svg viewBox="0 0 24 24">
          <path d="M9 11l3 3L22 4"/>
          <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
        </svg>
      </div>
      <div class="notif-body">
        <div class="notif-top">
          <span class="notif-title">Booking Dikonfirmasi</span>
          <span class="notif-time">08:00</span>
        </div>
        <p class="notif-desc">Pesanan Anda telah berhasil dikonfirmasi. Pengemudi sedang disiapkan untuk perjalanan Anda.</p>
        <div class="notif-footer">
          <span class="notif-chip chip-red">&#10003; Dikonfirmasi</span>
          <a class="notif-action action-red" href="detail_booking.php">
            Lihat Detail
            <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>

    <!-- 2: Mobil menuju lokasi → tracking.php -->
    <div class="notif-item">
      <div class="notif-icon icon-amber">
        <svg viewBox="0 0 24 24">
          <rect x="1" y="3" width="15" height="13" rx="2"/>
          <path d="M16 8h4l3 5v3h-7V8z"/>
          <circle cx="5.5" cy="18.5" r="2.5"/>
          <circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
      </div>
      <div class="notif-body">
        <div class="notif-top">
          <span class="notif-title">Mobil Menuju Lokasi</span>
          <span class="notif-time">08:15</span>
        </div>
        <p class="notif-desc">Pengemudi sedang dalam perjalanan menuju lokasi penjemputan Anda. Harap bersiap.</p>
        <div class="notif-footer">
          <span class="notif-chip chip-amber">&#8987; Dalam Perjalanan</span>
          <a class="notif-action action-amber" href="tracking.php">
            Lacak Mobil
            <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </a>
        </div>
      </div>
    </div>

    <!-- 3: Mobil telah tiba → tracking.php -->
    <div class="notif-item">
      <div class="notif-icon icon-blue">
        <svg viewBox="0 0 24 24">
          <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
      <div class="notif-body">
        <div class="notif-top">
          <span class="notif-title">Mobil Telah Tiba</span>
          <span class="notif-time">08:32</span>
        </div>
        <p class="notif-desc">Pengemudi sudah tiba di lokasi Anda. Segera menuju kendaraan untuk memulai perjalanan.</p>
        <div class="notif-footer">
          <span class="notif-chip chip-blue">&#9679; Menunggu Anda</span>
          <a class="notif-action action-blue" href="tracking.php">
            Lihat Tracking
            <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>

  </div>

  <a class="btn-back" href="booking.php">&#8592; Kembali ke Beranda</a>

</div>

</body>
</html>
