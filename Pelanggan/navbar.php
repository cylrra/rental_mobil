<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="d-flex" id="wrapper">
    
    <!-- Sidebar -->
    <div class="border-end border-light" id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-4 fs-4 border-bottom">
            🚗 <strong style="font-family: 'Outfit', sans-serif;">INDOMAX <span>RENT</span></strong>
        </div>
        
        <div class="list-group list-group-flush px-3 py-3 gap-1">
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan'): ?>
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-2 pb-1">Menu Utama</div>
                
                <a href="index.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-grid-fill me-2 text-primary"></i> Beranda / Dashboard
                </a>
                
                <a href="katalog.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-car-front-fill me-2 text-primary"></i> Katalog Mobil
                </a>
                
                <a href="transaksi.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-calendar-check-fill me-2 text-primary"></i> Sewa & Pesanan Saya
                </a>

                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Keuangan & Ulasan</div>
                
                <a href="pembayaran.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-wallet2 me-2 text-primary"></i> Input Pembayaran
                </a>
                
                <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-clock-history me-2 text-primary"></i> Riwayat Transaksi
                </a>
                
                <a href="grafik_rating.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-star-fill me-2 text-primary"></i> Ulasan & Rating
                </a>

                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Pengaturan</div>
                
                <a href="edit_profil.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-person-gear me-2 text-primary"></i> Pengaturan Akun
                </a>
                
                <a href="bantuan.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2-5 my-1">
                    <i class="bi bi-question-circle-fill me-2 text-primary"></i> Bantuan & CS
                </a>
            <?php endif; ?>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-2 pb-1">Dashboard Admin</div>
                <a href="index.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Utama
                </a>
                <a href="katalog.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-car-front me-2 text-primary"></i> Katalog Mobil
                </a>
                <a href="supir.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-person-badge me-2 text-success"></i> Supir
                </a>
                <a href="pelanggan.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-people me-2 text-info"></i> Pelanggan
                </a>
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Operasional</div>
                <a href="transaksi.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-card-list me-2 text-light"></i> Transaksi
                </a>
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Akuntansi</div>
                <a href="pembayaran.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-plus-circle-fill me-2 text-success"></i> Input Pembayaran
                </a>
                <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-clock-history me-2 text-warning"></i> Riwayat Pembayaran
                </a>
                <a href="jurnal_umum.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-journal-check me-2 text-info"></i> Jurnal Umum
                </a>
                <a href="jurnal_detail.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-search me-2 text-info"></i> Detail Jurnal
                </a>
                <a href="cetak_kwitansi.php" class="list-group-item list-group-item-action border-0 rounded-3 py-2">
                    <i class="bi bi-printer-fill me-2 text-danger"></i> Cetak Kwitansi
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="page-content-wrapper" class="w-100 bg-light">
        
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-3 border-bottom">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                
                <div class="d-none d-sm-block">
                    <span class="text-muted small d-block" style="font-size: 0.75rem; letter-spacing: 0.5px;">Sistem Informasi Pengelolaan Armada</span>
                    <span class="fw-bold text-secondary" style="font-size: 0.9rem;">PT INDOMAX RENTAL MOBIL</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['role'])): ?>
                        <span class="badge bg-light text-dark border p-2 shadow-sm fs-7 fw-semibold">
                            <i class="bi bi-person-circle me-1 text-primary"></i> <?= htmlspecialchars($_SESSION['nama_pelanggan'] ?? $_SESSION['nama_user'] ?? 'Pengguna'); ?> (<?= ucfirst($_SESSION['role'] ?? 'Tamu'); ?>)
                        </span>
                        <a class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" href="logout_pelanggan.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    <?php else: ?>
                        <a class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" href="login_pelanggan.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </nav>
        
        <div class="container-fluid p-4">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Premium Theme Variables Override */
    :root {
        --primary-blue: #0f172a; /* Slate 900 */
        --accent-blue: #1e3a8a;  /* Deep blue */
        --gradient-blue: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --grey-light: #f8fafc;
        --grey-hint: #e2e8f0;
        --font-display: 'Outfit', sans-serif;
    }

    body {
        background-color: var(--grey-light);
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden;
    }

    #sidebar-wrapper {
        min-width: 260px;
        max-width: 260px;
        min-height: 100vh;
        background-color: var(--primary-blue);
        color: white;
        transition: all 0.3s;
    }

    #sidebar-wrapper .sidebar-heading {
        color: white;
        font-weight: 800;
        border-bottom-color: rgba(255,255,255,0.08) !important;
    }
    
    #sidebar-wrapper .sidebar-heading span {
        color: #3b82f6;
    }

    .sidebar-category-title {
        font-size: 0.7rem;
        letter-spacing: 1.5px;
        color: rgba(255,255,255,0.4) !important;
        margin-top: 15px;
    }

    #sidebar-wrapper .list-group-item {
        background-color: transparent !important;
        color: rgba(255, 255, 255, 0.7) !important;
        font-size: 0.88rem;
        font-weight: 500;
        padding-left: 15px;
        transition: all 0.2s ease-in-out;
    }

    #sidebar-wrapper .list-group-item i {
        color: rgba(255, 255, 255, 0.5) !important;
        transition: all 0.2s ease;
    }

    /* Active & Hover item */
    #sidebar-wrapper .list-group-item:hover,
    #sidebar-wrapper .list-group-item.active {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: white !important;
        font-weight: 600;
    }

    #sidebar-wrapper .list-group-item:hover i,
    #sidebar-wrapper .list-group-item.active i {
        color: #3b82f6 !important;
        transform: scale(1.1);
    }
    
    .py-2-5 {
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }

    .fs-7 {
        font-size: 0.82rem;
    }
</style>
<script>
    // Automatically set active menu class based on current URL path
    document.addEventListener("DOMContentLoaded", function() {
        var currentUrl = window.location.pathname.split("/").pop();
        var menuItems = document.querySelectorAll("#sidebar-wrapper .list-group-item");
        menuItems.forEach(function(item) {
            var href = item.getAttribute("href");
            if (currentUrl === href || (currentUrl === "" && href === "index.php")) {
                item.classList.add("active");
            }
        });
    });
</script>