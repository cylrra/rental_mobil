<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

// Helper function to determine the active navigation link class (Active = Yellow/Amber)
function getLinkClass($page, $current_page) {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "nav-item-active flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group bg-[#d4af37] shadow-md shadow-[#d4af37]/30 text-[#1a1c1c]";
    }
    return "flex items-center gap-3 px-4 py-3 rounded-xl text-rose-100 hover:bg-black/10 hover:text-white hover:translate-x-2 transition-all duration-300 group cursor-pointer font-semibold";
}

// Helper function to get the icon classes, keeping original colors for inactive, dark for active
function getIconClass($page, $current_page, $colorClass = 'text-white') {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "w-5 h-5 text-[#1a1c1c] drop-shadow-sm scale-110 transition-transform duration-300";
    }
    return "w-5 h-5 " . $colorClass . " group-hover:scale-110 group-hover:rotate-6 transition-all duration-300";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard - PT INDOMAX RENTAL</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Bootstrap CSS for layout support -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Montserrat', 'sans-serif'] 
                    },
                    colors: {
                        // Dynamic Energy Theme Colors
                        primary: '#800000',      // Maroon
                        secondary: '#d4af37',    // Gold
                        tertiary: '#4d4c4c',     // Deep Charcoal
                        surface: '#ffffff',
                        background: '#f9f9f9',
                        brand: {
                            DEFAULT: '#800000',
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#800000',
                            600: '#600000',
                            700: '#4a0000',
                            800: '#300000',
                            900: '#1a0000',
                        }
                    }
                }
            }
        };
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #4d4c4c; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #800000; }
    
        /* Sidebar: Dynamic Crimson Red background */
        .sidebar-constellation { 
            background: linear-gradient(180deg, #800000 0%, #4a0000 100%) !important; 
            border-right: 1px solid #600000; 
        }
    
        /* Active Navigation Item: Energetic Yellow/Amber */
        .nav-item-active { 
            background-color: #d4af37 !important; 
            color: #1a1c1c !important; 
            font-weight: 700;
            box-shadow: 0 10px 15px -3px rgba(212, 175, 55, 0.3) !important;
            transform: scale(1.02);
        }
    
        /* Header: Clean Corporate White */
        .header-constellation {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e8e8e8;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        
        .sidebar-label {
            font-size: 0.72rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid #e2e2e2;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.02);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-[#f9f9f9] text-[#1a1c1c] font-sans antialiased">
<div class="flex h-screen overflow-hidden">
    
    <aside class="w-72 sidebar-constellation text-rose-100 flex-shrink-0 flex flex-col h-full overflow-y-auto" id="sidebar">
        <div class="p-8">
            <div class="flex items-center gap-3 group cursor-pointer">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-white shadow-lg shadow-black/10 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                    <i class="w-6 h-6 text-[#800000]" data-lucide="car-front"></i>
                </div>
                <div class="group-hover:translate-x-1 transition-transform duration-300">
                    <h1 class="font-black text-2xl tracking-tight text-white leading-none">INDOMAX</h1>
                    <p class="text-[10px] font-bold text-[#d4af37] uppercase tracking-widest mt-1">Rental System</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 pb-10 space-y-6">
            <div>
                <h2 class="px-4 mb-3 sidebar-label">Dashboard</h2>
                <a class="<?= getLinkClass('index.php', $current_page) ?>" href="index.php">
                    <i class="<?= getIconClass('index.php', $current_page, 'text-yellow-300') ?>" data-lucide="layout-dashboard"></i>
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
                    <a class="<?= getLinkClass('buku_besar.php', $current_page) ?>" href="buku_besar.php"><i class="<?= getIconClass('buku_besar.php', $current_page, 'text-indigo-300') ?>" data-lucide="book-marked"></i><span class="text-sm font-medium">Buku Besar</span></a>
                    <a class="<?= getLinkClass('laporan_laba_rugi.php', $current_page) ?>" href="laporan_laba_rugi.php"><i class="<?= getIconClass('laporan_laba_rugi.php', $current_page, 'text-rose-300') ?>" data-lucide="bar-chart-3"></i><span class="text-sm font-medium">Laba Rugi</span></a>
                    <a class="<?= getLinkClass('coa.php', $current_page) ?>" href="coa.php"><i class="<?= getIconClass('coa.php', $current_page, 'text-teal-300') ?>" data-lucide="list-tree"></i><span class="text-sm font-medium">Chart of Accounts</span></a>
                    <a class="<?= getLinkClass('cetak_kwitansi.php', $current_page) ?>" href="cetak_kwitansi.php"><i class="<?= getIconClass('cetak_kwitansi.php', $current_page, 'text-violet-300') ?>" data-lucide="printer"></i><span class="text-sm font-medium">Invoice</span></a>
                </div>
            </div>
            <?php endif; ?>
        </nav>
        
        <div class="p-6">
            <a class="flex items-center justify-center gap-2 bg-[#600000] text-rose-100 py-3 rounded-xl font-bold text-sm transition-all hover:bg-black/20 hover:text-white hover:-translate-y-1" href="logout_admin.php">
                <i class="w-5 h-5" data-lucide="power"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#f9f9f9]">
        
        <header class="h-20 header-constellation flex items-center justify-between px-10 shrink-0 sticky top-0 z-10">
            <div class="transform transition-transform hover:translate-x-1 cursor-default">
                <h2 class="text-xs font-black text-[#d4af37] uppercase tracking-[0.2em]">Sistem Informasi Armada</h2>
                <h3 class="text-2xl font-black text-[#800000] tracking-tight mt-0.5">PT INDOMAX RENTAL</h3>
            </div>
            <div class="flex items-center gap-3 bg-white border border-[#e2e2e2] hover:border-[#800000] hover:shadow-md hover:-translate-y-1 transition-all duration-300 px-4 py-2 rounded-xl cursor-pointer group">
                <div class="w-10 h-10 rounded-lg bg-[#800000]/10 flex items-center justify-center text-[#800000] group-hover:bg-[#800000] group-hover:text-white transition-colors duration-300">
                    <i class="w-5 h-5" data-lucide="user"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800 group-hover:text-[#800000] transition-colors"><?= htmlspecialchars($_SESSION['nama_user'] ?? 'Administrator'); ?></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider"><?= htmlspecialchars($_SESSION['role'] ?? 'Super Admin'); ?></span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-10 space-y-10 custom-scrollbars relative">
