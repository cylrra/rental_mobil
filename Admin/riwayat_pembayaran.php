<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mengunci agar menu Riwayat Pembayaran aktif di navbar sidebar
$current_page = 'riwayat_pembayaran.php';

// Fungsi pembantu untuk menentukan class CSS menu yang aktif
function getLinkClass($page, $current_page) {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "nav-item-active flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group bg-blue-600 shadow-md shadow-blue-600/30 text-white";
    }
    return "flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800/80 hover:text-white hover:translate-x-2 transition-all duration-300 group cursor-pointer font-medium";
}

function getIconClass($page, $current_page, $colorClass = 'text-white') {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "w-5 h-5 text-white drop-shadow-sm scale-110 transition-transform duration-300";
    }
    return "w-5 h-5 text-slate-500 group-hover:text-blue-400 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Riwayat Pembayaran - PT INDOMAX RENTAL</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        abyss:    '#050606',
                        skyline:  '#04345a',
                        steel:    '#06588c',
                        mist:     '#626979',
                        silver:   '#c8c6c6',
                        brand: { 50: '#e8f4fd', 100: '#d1e9fb', 200: '#a3d3f7', 500: '#06588c', 600: '#04345a', 900: '#050606' },
                        pastel: {
                            blue:  { bg: '#e0f2fe', text: '#04345a' },
                            green: { bg: '#d1fae5', text: '#065f46' },
                            red:   { bg: '#fee2e2', text: '#991b1b' }
                        }
                    }
                }
            }
        };
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #3c4d70; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #0b2b51; }
    
        /* Sidebar: Corporate Navy */
        .sidebar-constellation { 
            background: #0f172a !important; /* Navy/Slate 900 */
            border-right: 1px solid #1e293b; 
        }
    
        /* Navigasi Aktif */
        .nav-item-active { 
            background-color: #4f46e5 !important; 
            color: #ffffff !important; 
            font-weight: 700;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
            transform: scale(1.02);
        }
    
        /* Header: Clean White */
        .header-constellation {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }
        
        .sidebar-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b; /* Slate 500 */
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 8px 32px rgba(11, 43, 81, 0.05);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(11, 43, 81, 0.1);
        }

        /* Nav Link Base Style */
        .nav-link-base {
            color: #64748b;
            transition: all 0.3s ease;
        }
        .nav-link-base:hover {
            background: #eef2ff;
            color: #4f46e5;
            transform: translateX(5px);
        }

        /* Marker for active nav (Removed for solid design) */
        .nav-marker {
            display: none;
        }

        .financial-paper {
            background: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
        }
        .report-divider {
            border-top: 3px double #2d3748;
            margin-top: 15px;
            margin-bottom: 25px;
        }
        .table-report thead th {
            background-color: #f8f9fc !important;
            color: #4e73df !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0 !important;
        }
        /* Style kustom untuk kotak logo utama kiri atas */
        .brand-logo-box {
            width: 42px;
            height: 42px;
            min-width: 42px;
            background-color: #4f46e5; /* Warna ungu indigo khas Indomax */
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" style="background-image: url('img/bg-pattern.png'); background-size: cover; background-attachment: fixed;">
<div class="flex h-screen overflow-hidden">
    
    <aside class="w-72 sidebar-constellation text-slate-400 flex-shrink-0 flex flex-col h-full overflow-y-auto" id="sidebar">
        <div class="p-8">
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-indigo-600 shadow-lg shadow-indigo-600/40 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                    <i class="w-6 h-6 text-white" data-lucide="car-front"></i>
                </div>
                <div class="group-hover:translate-x-1 transition-transform duration-300">
                    <h1 class="font-black text-2xl tracking-tight text-white leading-none">INDOMAX</h1>
                    <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mt-1">Rental System</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 pb-10 space-y-6">
            <div>
                <h2 class="px-4 mb-3 sidebar-label">Dashboard</h2>
                <a class="<?= getLinkClass('index.php', $current_page) ?>" href="index.php">
                    <i class="<?= getIconClass('index.php', $current_page, 'text-sky-300') ?>" data-lucide="layout-dashboard"></i>
                    <span class="text-sm font-semibold">Dashboard Utama</span>
                </a>
            </div>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div>
                <h2 class="px-4 mb-3 sidebar-label">Data Master</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('mobil.php', $current_page) ?>" href="mobil.php"><i class="<?= getIconClass('mobil.php', $current_page, 'text-emerald-300') ?>" data-lucide="truck"></i><span class="text-sm font-medium">Mobil</span></a>
                    <a class="<?= getLinkClass('supir.php', $current_page) ?>" href="supir.php"><i class="<?= getIconClass('supir.php', $current_page, 'text-amber-300') ?>" data-lucide="user-square"></i><span class="text-sm font-medium">Supir</span></a>
                    <a class="<?= getLinkClass('pelanggan.php', $current_page) ?>" href="pelanggan.php"><i class="<?= getIconClass('pelanggan.php', $current_page, 'text-pink-300') ?>" data-lucide="users"></i><span class="text-sm font-medium">Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Operasional</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page) ?>" href="transaksi.php"><i class="<?= getIconClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page, 'text-cyan-300') ?>" data-lucide="clipboard-list"></i><span class="text-sm font-medium">Transaksi Sewa</span></a>
                    <a class="<?= getLinkClass('tracking.php', $current_page) ?>" href="tracking.php"><i class="<?= getIconClass('tracking.php', $current_page, 'text-red-300') ?>" data-lucide="map-pin"></i><span class="text-sm font-medium">Live Tracking</span></a>
                    <a class="<?= getLinkClass('grafik_rating.php', $current_page) ?>" href="grafik_rating.php"><i class="<?= getIconClass('grafik_rating.php', $current_page, 'text-yellow-300') ?>" data-lucide="star"></i><span class="text-sm font-medium">Rating Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Pemeliharaan</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('jadwal_service.php', $current_page) ?>" href="jadwal_service.php"><i class="<?= getIconClass('jadwal_service.php', $current_page, 'text-orange-300') ?>" data-lucide="calendar-check"></i><span class="text-sm font-medium">Jadwal Servis</span></a>
                    <a class="<?= getLinkClass('riwayat_pemeliharaan.php', $current_page) ?>" href="riwayat_pemeliharaan.php"><i class="<?= getIconClass('riwayat_pemeliharaan.php', $current_page, 'text-stone-300') ?>" data-lucide="clock-history"></i><span class="text-sm font-medium">Riwayat Servis</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Akuntansi</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('pembayaran.php', $current_page) ?>" href="pembayaran.php"><i class="<?= getIconClass('pembayaran.php', $current_page, 'text-lime-300') ?>" data-lucide="wallet"></i><span class="text-sm font-medium">Input Pembayaran</span></a>
                    <a class="<?= getLinkClass('riwayat_pembayaran.php', $current_page) ?>" href="riwayat_pembayaran.php"><i class="<?= getIconClass('riwayat_pembayaran.php', $current_page, 'text-green-300') ?>" data-lucide="landmark"></i><span class="text-sm font-medium">Riwayat Pembayaran</span></a>
                    <a class="<?= getLinkClass('jurnal_umum.php', $current_page) ?>" href="jurnal_umum.php"><i class="<?= getIconClass('jurnal_umum.php', $current_page, 'text-fuchsia-300') ?>" data-lucide="book-open"></i><span class="text-sm font-medium">Jurnal Umum</span></a>
                    <a class="<?= getLinkClass('riwayat_jurnal_umum.php', $current_page) ?>" href="riwayat_jurnal_umum.php"><i class="<?= getIconClass('riwayat_jurnal_umum.php', $current_page, 'text-fuchsia-300') ?>" data-lucide="book-open"></i><span class="text-sm font-medium">Riwayat Jurnal Umum</span></a>
                    <a class="<?= getLinkClass('laporan_laba_rugi.php', $current_page) ?>" href="laporan_laba_rugi.php"><i class="<?= getIconClass('laporan_laba_rugi.php', $current_page, 'text-rose-300') ?>" data-lucide="bar-chart-3"></i><span class="text-sm font-medium">Laba Rugi</span></a>
                    <a class="<?= getLinkClass('coa.php', $current_page) ?>" href="coa.php"><i class="<?= getIconClass('coa.php', $current_page, 'text-teal-300') ?>" data-lucide="list-tree"></i><span class="text-sm font-medium">Chart of Accounts</span></a>
                    <a class="<?= getLinkClass('cetak_kwitansi.php', $current_page) ?>" href="cetak_kwitansi.php"><i class="<?= getIconClass('cetak_kwitansi.php', $current_page, 'text-violet-300') ?>" data-lucide="printer"></i><span class="text-sm font-medium">Invoice</span></a>
                </div>
            </div>
            <?php endif; ?>
        </nav>
        
        <div class="p-6">
            <a class="flex items-center justify-center gap-2 bg-slate-800 text-slate-300 py-3 rounded-xl font-bold text-sm transition-all hover:bg-rose-600 hover:text-white hover:shadow-lg hover:shadow-rose-600/30 hover:-translate-y-1" href="logout_admin.php">
                <i class="w-5 h-5" data-lucide="power"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
        
        <header class="h-20 header-constellation flex items-center justify-between px-10 shrink-0 sticky top-0 z-10">
            <div class="transform transition-transform hover:translate-x-1 cursor-default">
                <h2 class="text-[11px] font-black text-blue-600 uppercase tracking-[0.2em]">Sistem Informasi Armada</h2>
                <h3 class="text-xl font-black text-slate-800 tracking-tight mt-0.5">PT INDOMAX RENTAL</h3>
            </div>
            <div class="flex items-center gap-3 bg-white border border-slate-200 hover:border-blue-300 hover:shadow-md hover:-translate-y-1 transition-all duration-300 px-4 py-2 rounded-xl cursor-pointer group">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <i class="w-5 h-5" data-lucide="user"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors"><?= htmlspecialchars($_SESSION['nama_user'] ?? 'Administrator'); ?></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($_SESSION['role'] ?? 'Super Admin'); ?></span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-10 space-y-10 custom-scrollbars relative">

        <?php 
        include 'koneksi.php'; 

        // =========================================================================
        // 2. QUERY DATA GRAFIK BULANAN (TIDAK ADA YANG DIHILANGKAN)
        // =========================================================================
        $sql_grafik = "SELECT 
                        DATE_FORMAT(t.tanggal_sewa, '%b %y') as bulan, 
                        COUNT(p.id_pembayaran) as jumlah_transaksi,
                        SUM(p.jumlah_bayar) as total_pendapatan
                       FROM pembayaran p
                       JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                       GROUP BY YEAR(t.tanggal_sewa), MONTH(t.tanggal_sewa)
                       ORDER BY t.tanggal_sewa ASC";

        $res_grafik = mysqli_query($conn, $sql_grafik);

        $labels = []; $counts = []; $revenues = []; $growth_pct = []; $last_revenue = 0;

        while ($row = mysqli_fetch_assoc($res_grafik)) {
            $labels[] = $row['bulan']; 
            $counts[] = (int)$row['jumlah_transaksi'];
            $current_revenue = (float)$row['total_pendapatan'];
            $revenues[] = $current_revenue / 100000; 
            
            if ($last_revenue > 0) {
                $pct = (($current_revenue - $last_revenue) / $last_revenue) * 100;
                $growth_pct[] = ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%';
            } else {
                $growth_pct[] = '+5.7%'; 
            }
            $last_revenue = $current_revenue;
        }

        $json_labels = json_encode($labels);
        $json_counts = json_encode($counts);
        $json_revenues = json_encode($revenues);
        $json_growth = json_encode($growth_pct);

        // =========================================================================
        // 3. QUERY RINGKASAN AKUNTANSI (ARUS KAS)
        // =========================================================================
        $q_total = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
        $r_total = mysqli_fetch_assoc($q_total);
        $total_masuk = $r_total['total'] ?? 0;

        $q_cash = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Cash'");
        $r_cash = mysqli_fetch_assoc($q_cash);
        $total_cash = $r_cash['total'] ?? 0;

        $q_transfer = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Transfer'");
        $r_transfer = mysqli_fetch_assoc($q_transfer);
        $total_transfer = $r_transfer['total'] ?? 0;
        ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <div class="container-fluid py-4 px-4" style="background-color: #f8fafc;">
            
            <div class="row align-items-center mb-4">
                <div class="col-md-6 col-7">
                    <div class="d-flex align-items-center">
                        <div class="brand-logo-box d-flex align-items-center justify-content-center text-white me-3 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                                <path d="M2.52 3.515A.5.5 0 0 1 3 3h10a.5.5 0 0 1 .48.341l2.11 6.33A1 1 0 0 1 15 11h-1.071l-1.036 2.418A1 1 0 0 1 11.972 14H4.028a1 1 0 0 1-.921-.582L2.07 11H1a1 1 0 0 1-.99-1.144l2.11-6.34zM3.79 4l-1.6 4.8h11.62L12.21 4H3.79zM4.5 9a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm7 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zM11 11H5v1h6v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <small class="text-primary fw-bold text-uppercase d-block" style="font-size: 0.7rem; letter-spacing: 1.5px;">Sistem Informasi Armada</small>
                            <h3 class="fw-bold text-dark m-0" style="font-size: 1.4rem; letter-spacing: -0.5px;">PT INDOMAX RENTAL</h3>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-5 d-flex justify-content-end">
                    <div class="d-flex align-items-center bg-white border rounded-4 px-3 py-2 shadow-sm" style="max-width: 250px;">
                        <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px; min-width: 38px;">
                            <i class="bi bi-person fs-4"></i>
                        </div>
                        <div class="d-none d-sm-block">
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem; white-space: nowrap;">Administrator Indomax</h6>
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">Admin</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-xl-12">
                    <div class="financial-paper p-4 p-md-5 bg-white">
                        
                        <div class="row align-items-center mb-2">
                            <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                                <div class="d-flex align-items-center justify-content-center justify-content-sm-start">
                                    <div class="brand-logo-box d-flex align-items-center justify-content-center text-white me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-car-front-fill" viewBox="0 0 16 16">
                                            <path d="M2.52 3.515A.5.5 0 0 1 3 3h10a.5.5 0 0 1 .48.341l2.11 6.33A1 1 0 0 1 15 11h-1.071l-1.036 2.418A1 1 0 0 1 11.972 14H4.028a1 1 0 0 1-.921-.582L2.07 11H1a1 1 0 0 1-.99-1.144l2.11-6.34zM3.79 4l-1.6 4.8h11.62L12.21 4H3.79zM4.5 9a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zm7 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1zM11 11H5v1h6v-1z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">INDOMAX</h4>
                                        <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Rental System</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-center text-sm-end">
                                <h5 class="fw-bold text-uppercase text-dark mb-1" style="letter-spacing: 0.5px;">Laporan Arus Kas Masuk</h5>
                                <p class="text-muted small mb-0">Periode Berjalan: <span class="fw-bold text-primary">Juni 2026</span></p>
                            </div>
                        </div>

                        <div class="small text-muted text-center text-sm-start mb-2">
                            <i class="bi bi-geo-alt-fill me-1 text-secondary"></i> PT INDOMAX RENTAL — Sistem Informasi Manajemen Armada & Logistik Finansial
                        </div>

                        <div class="report-divider"></div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-light">
                                    <small class="text-secondary d-block fw-semibold text-uppercase" style="font-size: 0.65rem;">Total Penerimaan Kas</small>
                                    <h4 class="fw-bold text-primary mb-0 mt-1">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-secondary d-block fw-semibold text-uppercase" style="font-size: 0.65rem;"><span class="badge bg-secondary me-1" style="font-size: 0.6rem;">CASH</span> Kas di Tangan</small>
                                    <h5 class="fw-bold text-dark mb-0 mt-1">Rp <?php echo number_format($total_cash, 0, ',', '.'); ?></h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-white">
                                    <small class="text-secondary d-block fw-semibold text-uppercase" style="font-size: 0.65rem;"><span class="badge bg-info text-dark me-1" style="font-size: 0.6rem;">BANK</span> Kas di Bank (Transfer)</small>
                                    <h5 class="fw-bold text-dark mb-0 mt-1">Rp <?php echo number_format($total_transfer, 0, ',', '.'); ?></h5>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mb-4 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;"><i class="bi bi-bar-chart-fill text-danger me-1"></i> Grafik Fluktuasi Pendapatan</span>
                                <span class="badge bg-success-subtle text-success px-2 py-1 small" style="font-size: 0.7rem; font-weight: 600;">Aktif (Real-time)</span>
                            </div>
                            <div style="position: relative; height:240px; width:100%">
                                <canvas id="canvasTransaksi"></canvas>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                            <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Buku Jurnal Riwayat Transaksi</h6>
                            <a href="pembayaran.php" class="btn btn-primary btn-sm rounded-3 px-3 fw-semibold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
                            </a>
                        </div>

                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover table-report align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3 py-3">ID Dokumen</th>
                                        <th class="py-3">Deskripsi / Pelanggan</th>
                                        <th class="py-3">Tanggal Buku</th>
                                        <th class="py-3">Metode Kas</th>
                                        <th class="py-3 text-end pe-3">Nominal Masuk</th>
                                        <th class="text-center py-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT p.*, pl.nama 
                                            FROM pembayaran p
                                            JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                                            JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
                                            ORDER BY p.id_pembayaran DESC";
                                    
                                    $query = mysqli_query($conn, $sql);
                                    
                                    if ($query && mysqli_num_rows($query) > 0) {
                                        while($d = mysqli_fetch_array($query)){
                                            $badge_class = ($d['metode_pembayaran'] == 'Transfer') ? 'bg-info text-dark' : 'bg-secondary text-white';
                                    ?>
                                    <tr>
                                        <td class="ps-3 text-muted small fw-semibold">#PYM-<?php echo $d['id_pembayaran']; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark mb-0" style="font-size: 0.9rem;"><?php echo $d['nama']; ?></div>
                                            <small class="text-muted" style="font-size: 0.75rem;">ID Sewa: #SRV-<?php echo $d['id_sewa']; ?></small>
                                        </td>
                                        <td class="text-secondary small"><?php echo date('d M Y', strtotime($d['tanggal_bayar'])); ?></td>
                                        <td>
                                            <span class="badge <?php echo $badge_class; ?> rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 0.65rem; font-weight: 600;">
                                                <?php echo $d['metode_pembayaran']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-success" style="font-size: 0.9rem;">
                                                Rp <?php echo number_format($d['jumlah_bayar'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="cetak_kwitansi.php?id=<?php echo $d['id_pembayaran']; ?>" class="btn btn-sm btn-light border" target="_blank">
                                                <i class="bi bi-printer text-secondary"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='6' class='text-center py-5 text-muted small'>Tidak ditemukan data pencatatan kas masuk pada periode ini.</td></tr>";
                                        }
                                        ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-5">
                            <small class="text-muted border-top d-block pt-3" style="font-size: 0.7rem; font-style: italic;">
                                Laporan ini dibuat otomatis secara digital oleh INDOMAX Rental System.
                            </small>
                        </div>

                    </div> 
                </div>
            </div>
        </div>

        </div>
    </main>
</div>

<script>
    // Mengaktifkan render seluruh ikon Lucide bawaan
    lucide.createIcons();

    // RENDER SCRIPT GRAFIK CHART.JS (TETAP DIJAGA UTUH)
    const ctx = document.getElementById('canvasTransaksi').getContext('2d');
    
    const dataLabels = <?php echo $json_labels; ?>;
    const dataCounts = <?php echo $json_counts; ?>;
    const dataRevenues = <?php echo $json_revenues; ?>;
    const dataGrowth = <?php echo $json_growth; ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataLabels,
            datasets: [
                {
                    label: 'Volume Transaksi (Kuantitas)',
                    data: dataCounts,
                    backgroundColor: 'rgba(78, 115, 223, 0.15)', 
                    borderColor: '#4e73df',
                    borderWidth: 1.5,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Omset Penjualan (x100.000 Rp)',
                    data: dataRevenues,
                    backgroundColor: '#ff3b5c', 
                    borderColor: '#ff3b5c',
                    borderWidth: 1,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { boxWidth: 12, font: { weight: '600', size: 11 } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f2f6', drawBorder: false },
                    ticks: { font: { size: 10 } }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        },
        plugins: [{
            id: 'customGrowthLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = 'bold 10px sans-serif';
                const meta = chart.getDatasetMeta(1); 
                meta.data.forEach((bar, index) => {
                    const text = dataGrowth[index];
                    ctx.fillStyle = text.includes('-') ? '#ff3838' : '#2ed573';
                    const x = bar.x;
                    const y = bar.y - 8; 
                    ctx.textAlign = 'center';
                    ctx.fillText(text, x, y);
                });
                ctx.restore();
            }
        }]
    });
</script>
</body>
</html>