<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Booking — INDOMAX-RENT</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --primary-dark: #cc0000; --amber: #fdc003; --amber-dark: #e6a800;
      --charcoal: #1a1c1c; --surface: #f9f9f9; --surface-low: #f3f3f3; --surface-card: #ffffff;
      --outline-mid: #d0d0d0; --text-main: #1a1c1c; --text-muted: #5e3f3a; --text-subtle: #926e69;
      --radius-sm: 4px; --radius: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-xl: 24px;
    }
    body { font-family: 'Montserrat', sans-serif; background: var(--surface); color: var(--text-main); min-height: 100vh; padding: 48px 24px; }
    body::before { content: ''; position: fixed; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary-dark) 0%, var(--amber) 100%); z-index: 100; }
    .page-wrap { max-width: 480px; margin: 0 auto; }
    .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; animation: fadeUp .4s ease both; }
    .brand-icon { width: 40px; height: 40px; background: var(--primary-dark); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .brand-icon svg { width: 22px; height: 22px; stroke: #fff; stroke-width: 2; fill: none; stroke-linecap: round; stroke-linejoin: round; }
    .logo { font-size: 22px; font-weight: 900; letter-spacing: -0.02em; color: var(--primary-dark); }
    .logo span { color: var(--amber-dark); }
    .page-title { font-size: 28px; font-weight: 900; letter-spacing: -0.02em; margin: 24px 0 4px; animation: fadeUp .4s ease .05s both; }
    .page-sub { font-size: 12px; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-subtle); margin-bottom: 28px; animation: fadeUp .4s ease .08s both; }
    /* summary */
    .summary { background: var(--charcoal); border-radius: var(--radius-lg); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 32px; position: relative; overflow: hidden; animation: fadeUp .4s ease .1s both; }
    .summary::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: var(--primary-dark); }
    .summary-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,.45); margin-bottom: 4px; }
    .summary-car { font-size: 18px; font-weight: 900; color: #fff; letter-spacing: -0.02em; margin-bottom: 2px; }
    .summary-plate { font-size: 11px; font-weight: 600; color: rgba(255,255,255,.45); }
    .summary-price { text-align: right; flex-shrink: 0; }
    .summary-price .amount { font-size: 20px; font-weight: 900; color: var(--amber); letter-spacing: -0.02em; }
    .summary-price .per { font-size: 10px; color: rgba(255,255,255,.4); font-weight: 600; margin-top: 2px; }
    /* section label */
    .section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-subtle); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .section-label::after { content: ''; flex: 1; height: 1px; background: var(--outline-mid); }
    /* steps */
    .steps { position: relative; display: flex; flex-direction: column; margin-bottom: 36px; }
    .steps::before { content: ''; position: absolute; left: 19px; top: 40px; bottom: 40px; width: 2px; background: var(--outline-mid); border-radius: 2px; }
    .step { display: flex; gap: 18px; align-items: flex-start; padding-bottom: 24px; position: relative; animation: fadeUp .4s ease both; }
    .step:last-child { padding-bottom: 0; }
    .step:nth-child(1) { animation-delay: .1s; }
    .step:nth-child(2) { animation-delay: .2s; }
    .step:nth-child(3) { animation-delay: .3s; }
    .step:nth-child(4) { animation-delay: .4s; }
    .step-dot { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; z-index: 1; }
    .step-dot svg { width: 17px; height: 17px; stroke-width: 2.2; fill: none; stroke-linecap: round; stroke-linejoin: round; }
    /* active */
    .step.active .step-dot { background: var(--primary-dark); box-shadow: 0 0 0 3px rgba(204,0,0,.15); }
    .step.active .step-dot svg { stroke: #fff; }
    .step.active .step-dot::after { content: ''; position: absolute; inset: -6px; border-radius: 50%; border: 2px solid var(--primary-dark); animation: pulse 1.8s ease-out infinite; }
    @keyframes pulse { 0% { transform: scale(.9); opacity: .5; } 100% { transform: scale(1.5); opacity: 0; } }
    /* done */
    .step.done .step-dot { background: var(--surface-low); border: 2px solid var(--outline-mid); }
    .step.done .step-dot svg { stroke: var(--outline-mid); }
    /* pending */
    .step.pending .step-dot { background: var(--surface-card); border: 2px solid var(--outline-mid); }
    .step.pending .step-dot svg { stroke: var(--outline-mid); }
    .step-content { flex: 1; padding-top: 8px; }
    .step-title { font-size: 14px; font-weight: 700; color: var(--text-main); }
    .step.pending .step-title { color: var(--text-subtle); }
    .step-desc { font-size: 12px; color: var(--text-muted); margin-top: 3px; line-height: 1.6; font-weight: 500; }
    .step.pending .step-desc { color: var(--text-subtle); }
    .step-badge { display: inline-block; margin-top: 8px; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: var(--radius-sm); letter-spacing: 0.06em; text-transform: uppercase; }
    .step.active .step-badge { background: rgba(204,0,0,.08); color: var(--primary-dark); border: 1px solid rgba(204,0,0,.2); }
    .step.pending .step-badge { display: none; }
    /* btn */
    .btn-primary { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px; background: var(--amber); color: var(--charcoal); font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; border: none; border-radius: var(--radius); cursor: pointer; box-shadow: 0 4px 16px rgba(253,192,3,.35); transition: transform .15s, box-shadow .15s, background .15s; text-decoration: none; animation: fadeUp .4s ease .5s both; }
    .btn-primary svg { width: 18px; height: 18px; stroke: var(--charcoal); stroke-width: 2.5; fill: none; stroke-linecap: round; stroke-linejoin: round; }
    .btn-primary:hover { background: var(--amber-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(253,192,3,.45); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 480px) { body { padding: 32px 16px; } }
  </style>
</head>
<body>
<div class="page-wrap">
  <div class="brand">
    <div class="brand-icon"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
    <span class="logo">INDOMAX<span>-RENT</span></span>
  </div>
  <h1 class="page-title">Status Booking</h1>
  <p class="page-sub">Pantau pemesanan secara real-time</p>
 
  <div class="summary">
    <div>
      <div class="summary-label">Kendaraan</div>
      <div class="summary-car">Toyota Avanza</div>
      <div class="summary-plate">H 0102 AC</div>
    </div>
    <div class="summary-price">
      <div class="amount">Rp275.000</div>
      <div class="per">per hari</div>
    </div>
  </div>
 
  <div class="section-label">Progres Pemesanan</div>
  <div class="steps">
    <div class="step active">
      <div class="step-dot"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
      <div class="step-content">
        <div class="step-title">Menunggu Konfirmasi</div>
        <div class="step-desc">Pesanan Anda sedang diproses oleh tim kami.</div>
        <span class="step-badge">● Sedang berlangsung</span>
      </div>
    </div>
    <div class="step pending">
      <div class="step-dot"><svg viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
      <div class="step-content">
        <div class="step-title">Persiapan</div>
        <div class="step-desc">Pengemudi dan kendaraan sedang disiapkan.</div>
        <span class="step-badge"></span>
      </div>
    </div>
    <div class="step pending">
      <div class="step-dot"><svg viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      <div class="step-content">
        <div class="step-title">Menuju Lokasi Jemput</div>
        <div class="step-desc">Pengemudi dalam perjalanan ke titik penjemputan.</div>
        <span class="step-badge"></span>
      </div>
    </div>
    <div class="step pending">
      <div class="step-dot"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
      <div class="step-content">
        <div class="step-title">Tiba di Lokasi Jemput</div>
        <div class="step-desc">Pengemudi sudah tiba dan menunggu Anda.</div>
        <span class="step-badge"></span>
      </div>
    </div>
  </div>
 
  <a class="btn-primary" href="tracking.php">
    <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
    Lihat Tracking
  </a>
</div>
</body>
</html>
 