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
            --deep-navy: #173b6f;
            --clear-blue: #3071a4;
            --light-blue: #87b8e5;
            --lilac-dust: #b8aab4;
            --frost-veil: #ebf6fc;
            --white: #ffffff;
        }
        body { background-color: var(--frost-veil); font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; margin: 0; }
        #wrapper { display: flex; min-height: 100vh; }
        #sidebar-wrapper { min-width: 260px; max-width: 260px; background: linear-gradient(180deg, var(--deep-navy) 0%, #1a4a85 100%); color: var(--white); position: fixed; top: 0; left: 0; z-index: 100; height: 100vh; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 24px; text-decoration: none; color: white; display: flex; flex-direction: column; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; text-decoration: none; color: rgba(255,255,255,0.65); margin-bottom: 2px; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: rgba(135, 184, 229, 0.15); color: var(--white); }
        .sidebar-section-title { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.35); padding: 16px 8px 6px; }
        #page-content-wrapper { margin-left: 260px; width: calc(100% - 260px); }
        .top-navbar { background: var(--white); padding: 14px 28px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(135,184,229,0.3); }
        .page-content { padding: 28px; }
    </style>
</head>
<body>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <a href="index.php" class="sidebar-brand">
            <div style="font-size: 2rem;">🚗</div>
            <strong>INDOMAX RENTAL</strong>
        </a>
        <nav class="sidebar-nav">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan'): ?>
                <div class="sidebar-section-title">Menu Utama</div>
                <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i> Beranda</a>
                <a href="katalog.php" class="<?= $current_page === 'katalog.php' ? 'active' : '' ?>"><i class="bi bi-car-front-fill"></i> Katalog</a>
                <a href="transaksi.php" class="<?= $current_page === 'transaksi.php' ? 'active' : '' ?>"><i class="bi bi-calendar-check-fill"></i> Sewa</a>
                <div class="sidebar-section-title">Keuangan & Ulasan</div>
<<<<<<< HEAD
                <a href="pembayaran.php" class="<?= $current_page === 'pembayaran.php' ? 'active' : '' ?>"><i class="bi bi-wallet2"></i> Pembayaran</a>
                <a href="riwayat_pembayaran.php" class="<?= $current_page === 'riwayat_pembayaran.php' ? 'active' : '' ?>"><i class="bi bi-clock-history"></i> Riwayat</a>
                <a href="ulasan_rating.php" class="<?= $current_page === 'ulasan_rating.php' ? 'active' : '' ?>"><i class="bi bi-star-fill"></i> Ulasan & Rating</a>
=======

                <a href="pembayaran.php" class="<?= $current_page === 'pembayaran.php' ? 'active' : '' ?>">
                    <i class="bi bi-wallet2"></i> Input Pembayaran
                </a>
                <a href="riwayat_pembayaran.php" class="<?= $current_page === 'riwayat_pembayaran.php' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i> Riwayat Transaksi
                </a>
                <a href="input_rating.php" class="<?= $current_page === 'input_rating.php' ? 'active' : '' ?>">
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
>>>>>>> 3e94f89b0148e1f4cea8554f3c108dcc9372a35e
            <?php endif; ?>
        </nav>
    </div>
    <div id="page-content-wrapper">
        <div class="top-navbar">
            <div>Sistem Informasi Pengelolaan Armada</div>
            <div><?= htmlspecialchars($_SESSION['nama_pelanggan'] ?? 'Pengguna') ?></div>
        </div>
        <div class="page-content"></div>