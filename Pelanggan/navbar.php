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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Dynamic Energy Color Scheme */
            --primary: #9e0000;         /* Bold Crimson */
            --primary-container: #cc0000;
            --secondary: #785900;       /* Dark Amber */
            --secondary-container: #fdc003; /* Energetic Amber */
            --tertiary: #4d4c4c;        /* Deep Charcoal */
            --background: #f9f9f9;
            --surface: #ffffff;
            --surface-container-low: #f3f3f3;
            --on-surface: #1a1c1c;
            --outline: #926e69;

            /* Compatibility mappings to harmonize remaining legacy page elements */
            --clear-blue: var(--primary);
            --deep-navy: var(--on-surface);
            --light-blue: var(--secondary-container);
            --frost-veil: var(--surface-container-low);
            --lilac-dust: var(--outline);
        }

        /* Global Bootstrap Overrides to match the brand Crimson Red and eliminate all blue elements */
        .text-primary {
            color: var(--primary) !important;
        }
        .text-success {
            color: #198754 !important; /* Professional Success Green */
        }
        .bg-primary {
            background-color: var(--primary) !important;
            color: #ffffff !important;
        }
        .bg-success {
            background-color: #198754 !important;
            color: #ffffff !important;
        }
        .bg-success-subtle {
            background-color: rgba(25, 135, 84, 0.12) !important;
            color: #198754 !important;
        }
        .btn-primary {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #ffffff !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #7a0000 !important;
            border-color: #7a0000 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary {
            color: var(--primary) !important;
            border-color: var(--primary) !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            border-color: var(--primary) !important;
        }
        .btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #ffffff !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-success:hover, .btn-success:focus, .btn-success:active {
            background-color: #157347 !important;
            border-color: #157347 !important;
            color: #ffffff !important;
        }
        .border-primary {
            border-color: var(--primary) !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(158, 0, 0, 0.25) !important;
        }
        .form-check-input:checked {
            background-color: var(--primary) !important;
            border-color: var(--primary) !important;
        }
        .badge.bg-primary {
            background-color: var(--primary) !important;
            color: #ffffff !important;
        }
        .badge.bg-success {
            background-color: var(--secondary-container) !important;
            color: #1a1c1c !important;
        }
        a {
            color: var(--primary);
        }
        a:hover {
            color: #7a0000;
        }

        body { 
            background-color: var(--background); 
            font-family: 'Montserrat', sans-serif; 
            overflow-x: hidden; 
            margin: 0; 
            color: var(--on-surface);
        }

        #wrapper { 
            display: flex; 
            min-height: 100vh; 
        }

        /* Sidebar: Unified Crimson Red gradient */
        #sidebar-wrapper { 
            min-width: 260px; 
            max-width: 260px; 
            background: linear-gradient(180deg, #800000 0%, #4a0000 100%) !important; 
            color: #ffffff; 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 100; 
            height: 100vh; 
            display: flex; 
            flex-direction: column;
            border-right: 1px solid #600000;
        }

        .sidebar-brand { 
            padding: 24px; 
            text-decoration: none; 
            color: #ffffff; 
            display: flex; 
            flex-direction: column; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand:hover {
            color: #ffffff;
        }

        .sidebar-nav { 
            flex: 1; 
            padding: 20px 14px; 
            overflow-y: auto; 
        }

        .sidebar-nav a { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 14px; 
            border-radius: 8px; /* 8px Moderate rounded corners */
            text-decoration: none; 
            color: rgba(255, 255, 255, 0.75); 
            margin-bottom: 6px; 
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .sidebar-nav a:hover { 
            background: rgba(0, 0, 0, 0.1); 
            color: #ffffff; 
            transform: translateX(4px);
        }

        /* Active navigation item: Energetic Amber/Yellow */
        .sidebar-nav a.active { 
            background-color: var(--secondary-container) !important; 
            color: #1a1c1c !important; 
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(253, 192, 3, 0.25);
        }

        /* Set active icon to dark matching the text */
        .sidebar-nav a.active i {
            color: #1a1c1c !important;
        }

        .sidebar-section-title { 
            font-size: 0.65rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            color: rgba(255, 255, 255, 0.4); 
            padding: 14px 8px 6px; 
            letter-spacing: 0.1em;
        }

        #page-content-wrapper { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Sidebar Toggle Transitions */
        #sidebar-wrapper, #page-content-wrapper {
            transition: all 0.3s ease-in-out;
        }
        .sidebar-hidden #sidebar-wrapper {
            margin-left: -260px;
        }
        .sidebar-hidden #page-content-wrapper {
            margin-left: 0;
            width: 100%;
        }

        .top-navbar { 
            background: var(--surface); 
            padding: 18px 32px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid var(--surface-container-low);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }

        .top-navbar-title {
            font-weight: 700;
            color: var(--tertiary);
            font-size: 0.9rem;
            letter-spacing: 0.02em;
        }

        .top-navbar-user {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-content { 
            padding: 32px; 
            flex-grow: 1;
            background-color: var(--background);
        }

        .sidebar-nav i {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <a href="index.php" class="sidebar-brand">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 42px; height: 42px; flex-shrink: 0;">
                    <i class="bi bi-car-front-fill" style="color: var(--primary); font-size: 1.3rem;"></i>
                </div>
                <div>
                    <h1 class="h5 m-0 fw-black tracking-tight text-white leading-none" style="font-weight: 900; font-family: 'Montserrat', sans-serif;">INDOMAX</h1>
                    <span class="text-uppercase text-secondary-container" style="font-size: 0.55rem; letter-spacing: 0.15em; font-weight: 800; color: var(--secondary-container) !important; display: block; margin-top: 4px;">PORTAL PELANGGAN</span>
                </div>
            </div>
        </a>
        <nav class="sidebar-nav">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan'): ?>
                <div class="sidebar-section-title">Menu Utama</div>
                <a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i> Beranda</a>
                <a href="katalog.php" class="<?= $current_page === 'katalog.php' ? 'active' : '' ?>"><i class="bi bi-car-front-fill"></i> Katalog</a>
                <a href="transaksi.php" class="<?= $current_page === 'transaksi.php' ? 'active' : '' ?>"><i class="bi bi-calendar-check-fill"></i> Sewa</a>
                
                <div class="sidebar-section-title">Keuangan & Ulasan</div>
                <a href="pembayaran.php" class="<?= $current_page === 'pembayaran.php' ? 'active' : '' ?>"><i class="bi bi-wallet2"></i> Pembayaran</a>
                <a href="riwayat_pembayaran.php" class="<?= $current_page === 'riwayat_pembayaran.php' ? 'active' : '' ?>"><i class="bi bi-clock-history"></i> Riwayat</a>
                <a href="ulasan_rating.php" class="<?= $current_page === 'ulasan_rating.php' ? 'active' : '' ?>"><i class="bi bi-star-fill"></i> Ulasan & Rating</a>

                <div class="sidebar-section-title">Pengaturan</div>
                <a href="edit_profil.php" class="<?= $current_page === 'edit_profil.php' ? 'active' : '' ?>"><i class="bi bi-person-gear"></i> Pengaturan Akun</a>
                <a href="bantuan.php" class="<?= $current_page === 'bantuan.php' ? 'active' : '' ?>"><i class="bi bi-question-circle-fill"></i> Bantuan & CS</a>
            <?php endif; ?>
        </nav>
        
        <div class="p-3 border-top border-secondary-subtle" style="border-color: rgba(255,255,255,0.05) !important;">
            <a href="logout_pelanggan.php" class="btn btn-outline-danger w-100 py-2 border-0 rounded-3 text-start d-flex align-items-center gap-2" style="font-size: 0.8rem; font-weight: 600; color: #ffb4a8;">
                <i class="bi bi-power"></i> Keluar Akun
            </a>
        </div>
    </div>
    <div id="page-content-wrapper">
        <div class="top-navbar">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebarToggle" class="btn btn-sm btn-light border p-1 d-flex align-items-center justify-content-center text-primary" style="width: 36px; height: 36px; border-radius: 8px;" title="Toggle Sidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="top-navbar-title m-0">Sistem Informasi Pengelolaan Armada</div>
            </div>
            <a href="edit_profil.php" class="top-navbar-user text-decoration-none">
                <i class="bi bi-person-circle fs-5"></i>
                <span><?= htmlspecialchars($_SESSION['nama_pelanggan'] ?? 'Pengguna') ?></span>
            </a>
        </div>
        <div class="page-content">
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const wrapper = document.getElementById('wrapper');
                const toggleBtn = document.getElementById('sidebarToggle');
                
                if (wrapper && toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        wrapper.classList.toggle('sidebar-hidden');
                        if(wrapper.classList.contains('sidebar-hidden')) {
                            localStorage.setItem('pelangganSidebarState', 'hidden');
                        } else {
                            localStorage.setItem('pelangganSidebarState', 'visible');
                        }
                    });
                    
                    if(localStorage.getItem('pelangganSidebarState') === 'hidden') {
                        wrapper.classList.add('sidebar-hidden');
                    }
                }
            });
        </script>