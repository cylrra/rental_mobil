<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat — INDOMAX-RENT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
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
      --text-main:      #1a1c1c;
      --text-muted:     #5e3f3a;
      --text-subtle:    #926e69;
      --green:          #16a34a;
      --green-bg:       #dcfce7;
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
      color: var(--text-main);
      margin: 24px 0 4px;
      animation: fadeUp .4s ease .05s both;
    }
 
    .page-sub {
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 32px;
      animation: fadeUp .4s ease .08s both;
    }
 
    /* ── Stats ── */
    .stats {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 10px;
      margin-bottom: 32px;
      animation: fadeUp .4s ease .1s both;
    }
 
    .stat-box {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-md);
      padding: 18px 12px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
 
    .stat-box.accent-red { border-top: 3px solid var(--primary-dark); }
    .stat-box.accent-amber { border-top: 3px solid var(--amber); }
    .stat-box.accent-charcoal { border-top: 3px solid var(--charcoal); }
 
    .stat-val {
      font-size: 28px;
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 1;
      margin-bottom: 6px;
    }
 
    .stat-val.red    { color: var(--primary-dark); }
    .stat-val.amber  { color: var(--amber-dark); }
    .stat-val.dark   { color: var(--charcoal); }
 
    .stat-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--text-subtle);
    }
 
    /* ── Tabs ── */
    .tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      animation: fadeUp .4s ease .13s both;
    }
 
    .tab {
      flex: 1;
      padding: 10px 16px;
      border-radius: var(--radius);
      border: 1px solid var(--outline-mid);
      background: transparent;
      color: var(--text-subtle);
      font-family: 'Montserrat', sans-serif;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      cursor: pointer;
      text-align: center;
      transition: all .2s;
    }
 
    .tab:hover {
      border-color: var(--primary-dark);
      color: var(--primary-dark);
    }
 
    .tab.active {
      background: var(--primary-dark);
      border-color: var(--primary-dark);
      color: #fff;
    }
 
    /* ── Booking cards ── */
    .booking-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
 
    .booking-card {
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      transition: border-color .2s, transform .15s, box-shadow .15s;
      text-decoration: none;
      color: inherit;
      display: block;
      animation: fadeUp .4s ease both;
      position: relative;
      overflow: hidden;
    }
 
    .booking-card::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 4px;
      border-radius: 4px 0 0 4px;
    }
 
    .booking-card[data-status="aktif"]::before   { background: var(--primary-dark); }
    .booking-card[data-status="selesai"]::before { background: var(--green); }
 
    .booking-card:nth-child(1) { animation-delay: .18s; }
    .booking-card:nth-child(2) { animation-delay: .26s; }
    .booking-card:nth-child(3) { animation-delay: .34s; }
 
    .booking-card:hover {
      border-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(204,0,0,.1);
    }
 
    .booking-card.hidden { display: none; }
 
    .card-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 14px;
      gap: 12px;
    }
 
    .car-row {
      display: flex;
      align-items: center;
      gap: 12px;
    }
 
    .car-thumb {
      width: 44px; height: 44px;
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
 
    .car-thumb svg {
      width: 22px; height: 22px;
      stroke-width: 1.8;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .thumb-aktif   { background: rgba(204,0,0,.08); }
    .thumb-aktif   svg { stroke: var(--primary-dark); }
    .thumb-selesai { background: var(--green-bg); }
    .thumb-selesai svg { stroke: var(--green); }
 
    .car-title {
      font-size: 16px;
      font-weight: 700;
      letter-spacing: -0.01em;
      color: var(--text-main);
    }
 
    .car-code {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-subtle);
      margin-top: 3px;
      letter-spacing: 0.03em;
    }
 
    /* badge */
    .badge {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 5px 12px;
      border-radius: var(--radius-sm);
      white-space: nowrap;
      flex-shrink: 0;
    }
 
    .badge-aktif {
      background: rgba(204,0,0,.08);
      border: 1px solid rgba(204,0,0,.2);
      color: var(--primary-dark);
    }
 
    .badge-selesai {
      background: var(--green-bg);
      border: 1px solid rgba(22,163,74,.2);
      color: var(--green);
    }
 
    /* route row */
    .route-row {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--surface-low);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      padding: 10px 14px;
      margin-bottom: 14px;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
    }
 
    .route-dot {
      width: 7px; height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }
 
    .dot-red    { background: var(--primary-dark); }
    .dot-amber  { background: var(--amber-dark); }
 
    .route-arrow {
      color: var(--outline-mid);
      font-size: 14px;
      margin: 0 2px;
    }
 
    /* meta row */
    .meta-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }
 
    .meta-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 11px;
      font-weight: 600;
      color: var(--text-subtle);
      letter-spacing: 0.02em;
    }
 
    .meta-item svg {
      width: 13px; height: 13px;
      stroke: var(--text-subtle); stroke-width: 2;
      fill: none; stroke-linecap: round;
      flex-shrink: 0;
    }
 
    .price-tag {
      font-size: 15px;
      font-weight: 900;
      color: var(--primary-dark);
      letter-spacing: -0.01em;
    }
 
    /* ── Empty state ── */
    .empty-state {
      display: none;
      text-align: center;
      padding: 56px 0;
      animation: fadeUp .3s ease both;
    }
 
    .empty-icon {
      width: 52px; height: 52px;
      margin: 0 auto 16px;
      background: var(--surface-card);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius-md);
      display: flex; align-items: center; justify-content: center;
    }
 
    .empty-icon svg {
      width: 24px; height: 24px;
      stroke: var(--text-subtle); stroke-width: 1.6;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .empty-title {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 4px;
    }
 
    .empty-sub {
      font-size: 12px;
      font-weight: 500;
      color: var(--text-subtle);
    }
 
    /* ── Back button ── */
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
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      cursor: pointer;
      text-decoration: none;
      margin-top: 20px;
      transition: border-color .15s, color .15s;
      animation: fadeUp .4s ease .4s both;
    }
 
    .btn-back:hover {
      border-color: var(--primary-dark);
      color: var(--primary-dark);
    }
 
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 400px) {
      .stats { grid-template-columns: 1fr 1fr; }
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
 
  <h1 class="page-title">Riwayat Booking</h1>
  <p class="page-sub">Semua perjalanan Anda</p>
 
  <!-- Stats -->
  <div class="stats">
    <div class="stat-box accent-red">
      <div class="stat-val red">1</div>
      <div class="stat-label">Aktif</div>
    </div>
    <div class="stat-box accent-amber">
      <div class="stat-val amber">1</div>
      <div class="stat-label">Selesai</div>
    </div>
    <div class="stat-box accent-charcoal">
      <div class="stat-val dark">2</div>
      <div class="stat-label">Total</div>
    </div>
  </div>
 
  <!-- Tabs -->
  <div class="tabs">
    <button class="tab active" onclick="filterTab('semua', this)">Semua</button>
    <button class="tab" onclick="filterTab('aktif', this)">Aktif</button>
    <button class="tab" onclick="filterTab('selesai', this)">Selesai</button>
  </div>
 
  <!-- Booking list -->
  <div class="booking-list" id="bookingList">
 
    <!-- Booking Aktif -->
    <a class="booking-card" href="detail_booking.php" data-status="aktif">
      <div class="card-top">
        <div class="car-row">
          <div class="car-thumb thumb-aktif">
            <svg viewBox="0 0 24 24">
              <rect x="1" y="3" width="15" height="13" rx="2"/>
              <path d="M16 8h4l3 5v3h-7V8z"/>
              <circle cx="5.5" cy="18.5" r="2.5"/>
              <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
          </div>
          <div>
            <div class="car-title">Toyota Avanza</div>
            <div class="car-code">#BK001 &nbsp;&middot;&nbsp; H 0102 AC</div>
          </div>
        </div>
        <span class="badge badge-aktif">&#9679; Aktif</span>
      </div>
 
      <div class="route-row">
        <span class="route-dot dot-red"></span>
        Jl. Pemuda No. 12
        <span class="route-arrow">&#8594;</span>
        <span class="route-dot dot-amber"></span>
        Bandara Ahmad Yani
      </div>
 
      <div class="meta-row">
        <div class="meta-item">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          25 Juni 2026
        </div>
        <div class="meta-item">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          ± 25 Menit
        </div>
        <div class="price-tag">Rp275.000</div>
      </div>
    </a>
 
    <!-- Booking Selesai -->
    <a class="booking-card" href="detail_booking.php" data-status="selesai">
      <div class="card-top">
        <div class="car-row">
          <div class="car-thumb thumb-selesai">
            <svg viewBox="0 0 24 24">
              <rect x="1" y="3" width="15" height="13" rx="2"/>
              <path d="M16 8h4l3 5v3h-7V8z"/>
              <circle cx="5.5" cy="18.5" r="2.5"/>
              <circle cx="18.5" cy="18.5" r="2.5"/>
            </svg>
          </div>
          <div>
            <div class="car-title">Honda Brio</div>
            <div class="car-code">#BK000 &nbsp;&middot;&nbsp; H 5588 BC</div>
          </div>
        </div>
        <span class="badge badge-selesai">&#10003; Selesai</span>
      </div>
 
      <div class="route-row">
        <span class="route-dot dot-red"></span>
        Stasiun Tawang
        <span class="route-arrow">&#8594;</span>
        <span class="route-dot dot-amber"></span>
        Hotel Grand Candi
      </div>
 
      <div class="meta-row">
        <div class="meta-item">
          <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          20 Juni 2026
        </div>
        <div class="meta-item">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          ± 18 Menit
        </div>
        <div class="price-tag">Rp200.000</div>
      </div>
    </a>
 
  </div>
 
  <!-- Empty state -->
  <div class="empty-state" id="emptyState">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24">
        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
        <rect x="9" y="3" width="6" height="4" rx="1"/>
        <path d="M9 12h6M9 16h4"/>
      </svg>
    </div>
    <div class="empty-title">Tidak ada booking ditemukan</div>
    <div class="empty-sub">Belum ada perjalanan di kategori ini</div>
  </div>
 
  <a class="btn-back" href="booking.php">&#8592; Kembali ke Beranda</a>
 
</div>
 
<script>
  function filterTab(filter, el) {
    document.querySelectorAll('.tab').forEach(function(t) {
      t.classList.remove('active');
    });
    el.classList.add('active');
 
    var cards = document.querySelectorAll('.booking-card');
    var visible = 0;
 
    cards.forEach(function(card) {
      var status = card.getAttribute('data-status');
      var show = (filter === 'semua') || (status === filter);
      if (show) {
        card.classList.remove('hidden');
        visible++;
      } else {
        card.classList.add('hidden');
      }
    });
 
    var emptyState = document.getElementById('emptyState');
    emptyState.style.display = visible === 0 ? 'block' : 'none';
  }
</script>
 
</body>
</html>
 