<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<style>
    #sidebar-wrapper {
        /* Biru pastel sangat muda dengan transparansi */
        background-color: rgba(219, 235, 255, 0.75) !important;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-right: 1px solid rgba(191, 219, 254, 0.5) !important;
    }
    .sidebar-heading {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(191, 219, 254, 0.5) !important;
    }
    .brand-title {
        color: #1e40af !important; /* Biru yang lebih dalam dan elegan */
    }
    .sidebar-category-title {
        font-size: 0.65rem;
        letter-spacing: 0.15em;
        color: #64748b !important; /* Warna teks lebih jelas */
    }
    .list-group-item-action {
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 10px !important;
        margin: 4px 10px;
        width: auto;
        padding: 10px 16px;
        transition: all 0.3s ease-in-out;
        color: #475569 !important;
        display: flex;
        align-items: center;
    }
    .list-group-item-action i {
        color: #94a3b8;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }
    .list-group-item-action:hover {
        background-color: rgba(219, 234, 254, 0.6) !important;
        color: #1e40af !important;
    }
    .list-group-item-action:hover i {
        color: #1e40af !important;
    }
    /* Style saat menu aktif */
    .active-menu {
        background-color: #dbeafe !important; /* Biru pastel solid */
        color: #1e40af !important;
        font-weight: 600;
    }
    .active-menu i {
        color: #1e40af !important;
    }
</style>

<div id="sidebar-wrapper" style="min-width: 260px; max-width: 260px; min-height: 100vh;">
    <div class="sidebar-heading text-center py-4 fs-5">
        <div class="d-flex align-items-center justify-content-center gap-3">
            <div class="text-white rounded-xl flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #60a5fa); box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);">
                <i class="bi bi-car-front-fill fs-5"></i>
            </div>
            <div class="text-start lh-1">
                <span class="fw-bold brand-title d-block fs-5 tracking-tight">INDOMAX</span>
                <span class="fw-bold text-uppercase" style="font-size: 0.6rem; color: #64748b; letter-spacing: 0.15em;">Rental System</span>
            </div>
        </div>
    </div>
    
    <div class="list-group list-group-flush px-3 py-3 gap-1">
        <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-2 pb-1">Dashboard</div>
        <a href="index.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'index.php') ? 'active-menu' : ''; ?>">
            <i class="bi bi-speedometer2 me-3"></i> Dashboard Utama
        </a>
        
        <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Data Master</div>
        <a href="mobil.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'mobil.php') ? 'active-menu' : ''; ?>">
            <i class="bi bi-car-front me-3"></i> Data Mobil
        </a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="supir.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'supir.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-person-badge me-3"></i> Data Supir
            </a>
            <a href="pelanggan.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'pelanggan.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-people me-3"></i> Data Pelanggan
            </a>
            
            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Operasional</div>
            <a href="transaksi.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'transaksi.php' || $current_page == 'Transaksi_Baru.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-card-list me-3"></i> Transaksi Sewa
            </a>
            <a href="tracking.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'tracking.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-geo-alt me-3"></i> Tracking Mobil
            </a>
            
            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Pemeliharaan</div>
            <a href="jadwal_service.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'jadwal_service.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-calendar3 me-3"></i> Jadwal Service
            </a>
            <a href="riwayat_pemeliharaan.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'riwayat_pemeliharaan.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-wrench-adjustable me-3"></i> Riwayat Service
            </a>

            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Akuntansi</div>
            <a href="pembayaran.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'pembayaran.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-wallet2 me-3"></i> Input Pembayaran
            </a>
            <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'riwayat_pembayaran.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-clock-history me-3"></i> Riwayat Pembayaran
            </a>
            <a href="jurnal_umum.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'jurnal_umum.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-journal-check me-3"></i> Jurnal Umum
            </a>
            <a href="buku_besar.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'buku_besar.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-book me-3"></i> Buku Besar
            </a>
            <a href="neraca_saldo.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'neraca_saldo.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-calculator me-3"></i> Neraca Saldo
            </a>
            <a href="laporan_laba_rugi.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'laporan_laba_rugi.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-graph-up-arrow me-3"></i> Laba Rugi
            </a>
            <a href="neraca.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'neraca.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-scale me-3"></i> Neraca (Posisi Keuangan)
            </a>
            <a href="coa.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'coa.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-diagram-3-fill me-3"></i> Chart of Accounts (COA)
            </a>
            <a href="penyusutan.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'penyusutan.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-car-front me-3"></i> Penyusutan Kendaraan
            </a>
            <a href="cetak_kwitansi.php" class="list-group-item list-group-item-action bg-transparent border-0 <?= ($current_page == 'cetak_kwitansi.php') ? 'active-menu' : ''; ?>">
                <i class="bi bi-printer me-3"></i> Cetak Kwitansi
            </a>
        <?php endif; ?>
    </div>
</div>