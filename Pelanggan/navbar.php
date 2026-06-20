<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="d-flex" id="wrapper">
    
    <div class="bg-dark text-white border-end border-secondary" id="sidebar-wrapper" style="min-width: 250px; max-width: 250px; min-height: 100vh; transition: all 0.3s;">
        <div class="sidebar-heading text-center py-4 fs-4 border-bottom border-secondary bg-danger">
            🚗 <strong>INDOMAX <span class="text-info">RENTAL</span></strong>
        </div>
        
        <div class="list-group list-group-flush px-2 py-3 gap-1">
            
            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-2 pb-1">Dashboard</div>
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Utama
            
            <a href="katalog.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                <i class="bi bi-speedometer2 me-2 text-primary"></i> katalog mobil
            </a>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="supir.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-person-badge me-2 text-success"></i> Supir
                </a>
                
                <a href="pelanggan.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-people me-2 text-info"></i> Pelanggan
                </a>
                
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Operasional</div>
                <a href="transaksi.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-card-list me-2 text-light"></i> Transaksi
                </a>

                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Akuntansi</div>
                
                <a href="pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-plus-circle-fill me-2 text-success"></i> Input Pembayaran
                </a>
                
                <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-clock-history me-2 text-warning"></i> Riwayat Pembayaran
                </a>
                
                <a href="jurnal_umum.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-journal-check me-2 text-info"></i> Jurnal Umum
                </a>
                
                <a href="jurnal_detail.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-search me-2 text-info"></i> Detail Jurnal
                </a>
                
                <a href="cetak_kwitansi.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-printer-fill me-2 text-danger"></i> Cetak Kwitansi
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div id="page-content-wrapper" class="w-100 bg-light">
        
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-3 border-bottom">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                
                <div class="d-none d-sm-block">
                    <span class="text-muted small d-block">Sistem Informasi Pengelolaan Armada</span>
                    <span class="fw-bold text-secondary">PT INDOMAX RENTAL</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['role'])): ?>
                        <span class="badge bg-secondary p-2 text-white shadow-sm fs-7">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['nama_user'] ?? 'Pengguna'); ?> (<?= ucfirst($_SESSION['role'] ?? 'Tamu'); ?>)
                        </span>
                        <a class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" href="logout_pelanggan.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    <?php else: ?>
                        <a class="btn btn-sm btn-info text-dark rounded-pill px-3 fw-bold" href="login_pelanggan.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </nav>
        
        <div class="container-fluid p-4">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Mengatur style teks judul kategori agar terlihat kontras dan jelas */
    .sidebar-category-title {
        font-size: 0.75rem;
        letter-spacing: 1.5px;
        color: #a4b0be !important; /* Warna abu-abu terang */
    }

    /* Styling Item Sidebar & Efek Hover */
    #sidebar-wrapper .list-group-item {
        transition: all 0.2s ease-in-out;
        font-size: 0.92rem;
    }
    #sidebar-wrapper .list-group-item:hover {
        background-color: #2c3034 !important;
        color: #0dcaf0 !important;
        padding-left: 1.25rem;
    }
    .fs-7 {
        font-size: 0.85rem;
    }
    body {
        background-color: #f8f9fa;
        overflow-x: hidden;
    }
</style>