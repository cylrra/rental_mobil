<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracking Selesai — INDOMAX-RENT</title>
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
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
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
      padding: 56px 48px 48px;
      max-width: 480px;
      width: 100%;
      text-align: center;
      animation: slideUp .5s cubic-bezier(.22,1,.36,1) both;
    }
 
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(32px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    /* ── Check icon ── */
    .check-wrap {
      width: 96px;
      height: 96px;
      margin: 0 auto 32px;
      position: relative;
    }
 
    .ripple {
      position: absolute;
      inset: -12px;
      border-radius: 50%;
      border: 2px solid var(--amber);
      opacity: 0;
      animation: ripple 2s ease-out 0.6s infinite;
    }
    .ripple:nth-child(2) { animation-delay: 1.1s; }
 
    @keyframes ripple {
      0%   { transform: scale(.8); opacity: .5; }
      100% { transform: scale(1.5); opacity: 0; }
    }
 
    .check-circle {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      background: var(--charcoal);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      box-shadow: 0 0 32px rgba(253,192,3,.25);
      animation: popIn .4s cubic-bezier(.34,1.56,.64,1) .2s both;
    }
 
    @keyframes popIn {
      from { transform: scale(0); }
      to   { transform: scale(1); }
    }
 
    .check-circle svg {
      width: 44px;
      height: 44px;
      stroke: var(--amber);
      stroke-width: 2.5;
      fill: none;
      stroke-linecap: round;
      stroke-linejoin: round;
    }
 
    .check-circle svg path {
      stroke-dasharray: 60;
      stroke-dashoffset: 60;
      animation: drawCheck .4s ease .6s forwards;
    }
 
    @keyframes drawCheck {
      to { stroke-dashoffset: 0; }
    }
 
    /* ── Brand mini ── */
    .brand-mini {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 20px;
      animation: fadeIn .4s ease .75s both;
    }
 
    .brand-mini-icon {
      width: 28px; height: 28px;
      background: var(--primary-dark);
      border-radius: 6px;
      display: flex; align-items: center; justify-content: center;
    }
 
    .brand-mini-icon svg {
      width: 15px; height: 15px;
      stroke: #fff; stroke-width: 2;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }
 
    .brand-mini-text {
      font-size: 14px;
      font-weight: 900;
      color: var(--primary-dark);
      letter-spacing: -0.01em;
    }
 
    /* ── Chip ── */
    .chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(22,163,74,.08);
      border: 1px solid rgba(22,163,74,.25);
      color: var(--green);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 6px 14px;
      border-radius: var(--radius-sm);
      margin-bottom: 20px;
      animation: fadeIn .4s ease .8s both;
    }
 
    .chip::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--green);
    }
 
    h1 {
      font-size: 30px;
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 1.15;
      color: var(--text-main);
      margin-bottom: 12px;
      animation: fadeIn .4s ease .9s both;
    }
 
    p.sub {
      color: var(--text-muted);
      font-size: 13px;
      font-weight: 500;
      line-height: 1.7;
      margin-bottom: 32px;
      animation: fadeIn .4s ease 1s both;
    }
 
    .divider {
      height: 1px;
      background: var(--outline-mid);
      margin-bottom: 28px;
      animation: fadeIn .4s ease 1.05s both;
    }
 
    /* ── Info grid ── */
    .info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 32px;
      animation: fadeIn .4s ease 1.1s both;
    }
 
    .info-box {
      background: var(--surface-low);
      border: 1px solid var(--outline-mid);
      border-radius: var(--radius);
      padding: 14px 16px;
      text-align: left;
    }
 
    .info-box .label {
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-subtle);
      margin-bottom: 4px;
    }
 
    .info-box .value {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-main);
    }
 
    .info-box .value.accent { color: var(--green); }
 
    /* ── Buttons ── */
    .btn-primary {
      display: block;
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
      text-align: center;
      animation: fadeIn .4s ease 1.2s both;
    }
 
    .btn-primary:hover {
      background: var(--amber-dark);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(253,192,3,.45);
    }
 
    .btn-primary:active { transform: translateY(0); }
 
    .btn-secondary {
      display: block;
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
      margin-top: 10px;
      transition: border-color .15s, color .15s;
      text-decoration: none;
      text-align: center;
      animation: fadeIn .4s ease 1.3s both;
    }
 
    .btn-secondary:hover {
      border-color: var(--primary-dark);
      color: var(--primary-dark);
    }
 
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
 
    @media (max-width: 480px) {
      .card { padding: 40px 24px 32px; }
      h1 { font-size: 24px; }
      .info-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
 
<div class="card">
 
  <div class="check-wrap">
    <div class="ripple"></div>
    <div class="ripple"></div>
    <div class="check-circle">
      <svg viewBox="0 0 24 24">
        <path d="M5 13l4 4L19 7"/>
      </svg>
    </div>
  </div>
 
  <div class="brand-mini">
    <div class="brand-mini-icon">
      <svg viewBox="0 0 24 24">
        <rect x="1" y="3" width="15" height="13" rx="2"/>
        <path d="M16 8h4l3 5v3h-7V8z"/>
        <circle cx="5.5" cy="18.5" r="2.5"/>
        <circle cx="18.5" cy="18.5" r="2.5"/>
      </svg>
    </div>
    <span class="brand-mini-text">INDOMAX-RENT</span>
  </div>
 
  <div class="chip">Perjalanan Selesai</div>
 
  <h1>Tracking Selesai!</h1>
  <p class="sub">Terima kasih telah menggunakan layanan kami. Perjalanan Anda telah berhasil diselesaikan.</p>
 
  <div class="divider"></div>
 
  <div class="info-grid">
    <div class="info-box">
      <div class="label">Status</div>
      <div class="value accent">&#10003; Completed</div>
    </div>
    <div class="info-box">
      <div class="label">Tanggal</div>
      <div class="value"><?php echo date('d M Y'); ?></div>
    </div>
  </div>
 
  <a class="btn-primary" href="booking.php">Booking Lagi</a>
  <a class="btn-secondary" href="riwayat.php">Lihat Riwayat Perjalanan</a>
 
</div>
 
</body>
</html>
 