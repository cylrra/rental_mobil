<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INDOMAX RENTAL MOBIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --deep-navy:   #173b6f;
            --clear-blue:  #3071a4;
            --light-blue:  #87b8e5;
            --lilac-dust:  #b8aab4;
            --frost-veil:  #ebf6fc;
            --white:       #ffffff;
            --font-display: 'Outfit', sans-serif;
            --font-body:    'Plus Jakarta Sans', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--frost-veil);
            font-family: var(--font-body);
            overflow-x: hidden;
            margin: 0;
        }

        /* ── WRAPPER ── */
        #wrapper { display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        #sidebar-wrapper {
            min-width: 260px;
            max-width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--deep-navy) 0%, #1a4a85 100%);
            color: var(--white);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(23, 59, 111, 0.3);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(135, 184, 229, 0.15);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--clear-blue), var(--light-blue));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(48, 113, 164, 0.4);
        }
        .sidebar-brand-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--white);
            line-height: 1.2;
        }
        .sidebar-brand-title span { color: var(--light-blue); }
        .sidebar-brand-sub {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        /* Nav Menu */
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-section-title {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            color: rgba(255,255,255,0.35);
            padding: 16px 8px 6px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255,255,255,0.65);
            margin-bottom: 2px;
            transition: all 0.2s ease;
        }
        .sidebar-nav a i {
            font-size: 1rem;
            color: rgba(255,255,255,0.4);
            transition: all 0.2s ease;
            width: 20px;
            text-align: center;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(135, 184, 229, 0.15);
            color: var(--white);
        }
        .sidebar-nav a:hover i,
        .sidebar-nav a.active i {
            color: var(--light-blue);
        }
        .sidebar-nav a.active {
            background: linear-gradient(90deg, rgba(48,113,164,0.35), rgba(135,184,229,0.1));
            border-left: 3px solid var(--light-blue);
            color: var(--white);
            font-weight: 600;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid rgba(135, 184, 229, 0.12);
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
            transition: all 0.2s;
        }
        .sidebar-footer a:hover {
            background: rgba(220,50,50,0.15);
            color: #fc8181;
        }

        /* ── MAIN CONTENT ── */
        #page-content-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            background: var(--white);
            border-bottom: 1px solid rgba(135,184,229,0.3);
            padding: 14px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: 0 2px 12px rgba(23, 59, 111, 0.06);
        }
        .top-navbar-brand {
            font-size: 0.8rem;
            color: var(--lilac-dust);
            line-height: 1.4;
        }
        .top-navbar-brand strong {
            display: block;
            font-size: 0.9rem;
            color: var(--deep-navy);
            font-weight: 700;
        }
        .top-navbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--frost-veil);
            border: 1px solid rgba(135,184,229,0.4);
            padding: 7px 14px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--deep-navy);
        }
        .user-badge i { color: var(--clear-blue); }
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 50px;
            border: 1.5px solid #e53e3e;
            color: #e53e3e;
            font-size: 0.82rem;
            font-weight: 600;
            background: transparent;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: #e53e3e;
            color: var(--white);
        }

        /* Main page content container */
        .page-content {
            flex: 1;
            padding: 28px 28px;
        }

        /* ── GLOBAL UTILITIES ── */
        .py-2-5 { padding-top: 0.6rem; padding-bottom: 0.6rem; }
        .fs-7 { font-size: 0.82rem; }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(23,59,111,0.07);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--clear-blue), var(--deep-navy));
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(48,113,164,0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(48,113,164,0.4);
            background: linear-gradient(135deg, var(--deep-navy), var(--clear-blue));
        }
        .btn-outline-primary {
            border: 1.5px solid var(--clear-blue);
            color: var(--clear-blue);
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: var(--clear-blue);
            border-color: var(--clear-blue);
        }

        /* Form */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid rgba(135,184,229,0.5);
            background: var(--white);
            font-size: 0.9rem;
            padding: 10px 14px;
            color: var(--deep-navy);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--clear-blue);
            box-shadow: 0 0 0 3px rgba(48,113,164,0.12);
            outline: none;
        }
        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--lilac-dust);
            margin-bottom: 6px;
        }

        /* Alert */
        .alert-warning {
            background: rgba(184, 170, 180, 0.15);
            border: 1px solid rgba(184,170,180,0.4);
            color: var(--deep-navy);
            border-radius: 12px;
        }

        /* Tables */
        .table thead th {
            background: var(--frost-veil);
            color: var(--deep-navy);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(135,184,229,0.3);
            padding: 14px 16px;
        }
        .table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            color: var(--deep-navy);
            border-bottom: 1px solid rgba(135,184,229,0.15);
        }
        .table-hover tbody tr:hover {
            background: var(--frost-veil);
        }

        /* Badge customizations */
        .badge-primary-custom {
            background: rgba(48,113,164,0.12);
            color: var(--clear-blue);
            border: 1px solid rgba(48,113,164,0.2);
        }
    </style>
</head>
<body>
<div id="wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <div id="sidebar-wrapper">

        <!-- Brand -->
        <a href="index.php" class="sidebar-brand d-flex flex-column">
            <div class="sidebar-brand-icon">🚗</div>
            <div class="sidebar-brand-title">INDOMAX <span>RENT</span></div>
            <div class="sidebar-brand-sub">Portal Pelanggan</div>
        </a>

        <!-- Nav Links -->
        <nav class="sidebar-nav">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan'): ?>

                <div class="sidebar-section-title">Menu Utama</div>

                <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid-fill"></i> Beranda / Dashboard
                </a>
                <a href="katalog.php" class="<?= $current_page === 'katalog.php' ? 'active' : '' ?>">
                    <i class="bi bi-car-front-fill"></i> Katalog Armada
                </a>
                <a href="transaksi.php" class="<?= $current_page === 'transaksi.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check-fill"></i> Sewa & Pesanan Saya
                </a>

                <div class="sidebar-section-title">Keuangan & Ulasan</div>

                <a href="pembayaran.php" class="<?= $current_page === 'pembayaran.php' ? 'active' : '' ?>">
                    <i class="bi bi-wallet2"></i> Input Pembayaran
                </a>
                <a href="riwayat_pembayaran.php" class="<?= $current_page === 'riwayat_pembayaran.php' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i> Riwayat Transaksi
                </a>
                <a href="grafik_rating.php" class="<?= $current_page === 'grafik_rating.php' ? 'active' : '' ?>">
                    <i class="bi bi-star-fill"></i> Ulasan & Rating
                </a>

                <div class="sidebar-section-title">Pengaturan</div>

                <a href="edit_profil.php" class="<?= $current_page === 'edit_profil.php' ? 'active' : '' ?>">
                    <i class="bi bi-person-gear"></i> Pengaturan Akun
                </a>
                <a href="bantuan.php" class="<?= $current_page === 'bantuan.php' ? 'active' : '' ?>">
                    <i class="bi bi-question-circle-fill"></i> Bantuan & CS
                </a>

            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="sidebar-section-title">Dashboard Admin</div>
                <a href="index.php"><i class="bi bi-speedometer2"></i> Dashboard Utama</a>
                <a href="katalog.php"><i class="bi bi-car-front"></i> Katalog Mobil</a>
                <a href="supir.php"><i class="bi bi-person-badge"></i> Supir</a>
                <a href="pelanggan.php"><i class="bi bi-people"></i> Pelanggan</a>
                <div class="sidebar-section-title">Operasional</div>
                <a href="transaksi.php"><i class="bi bi-card-list"></i> Transaksi</a>
                <div class="sidebar-section-title">Akuntansi</div>
                <a href="pembayaran.php"><i class="bi bi-plus-circle-fill"></i> Input Pembayaran</a>
                <a href="riwayat_pembayaran.php"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</a>
                <a href="jurnal_umum.php"><i class="bi bi-journal-check"></i> Jurnal Umum</a>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <a href="logout_pelanggan.php">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>
    <!-- ═══ END SIDEBAR ═══ -->

    <!-- ═══ MAIN CONTENT ═══ -->
    <div id="page-content-wrapper">

        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="top-navbar-brand">
                Sistem Informasi Pengelolaan Armada
                <strong>PT INDOMAX RENTAL MOBIL</strong>
            </div>
            <div class="top-navbar-user">
                <?php if (isset($_SESSION['role'])): ?>
                    <div class="user-badge">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($_SESSION['nama_pelanggan'] ?? $_SESSION['nama_user'] ?? 'Pengguna') ?>
                        <span style="color: var(--lilac-dust); font-weight: 400;">(<?= ucfirst($_SESSION['role'] ?? 'Tamu') ?>)</span>
                    </div>
                    <a href="logout_pelanggan.php" class="btn-logout">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                <?php else: ?>
                    <a href="login_pelanggan.php" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>