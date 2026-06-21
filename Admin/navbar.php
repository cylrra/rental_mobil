<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

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
    <title>Admin Dashboard - PT INDOMAX RENTAL</title>
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
                        // Silent Constellation Palette
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

        /* Sidebar Override */
        .sidebar-constellation { 
            background-color: #0f172a !important;
            border-right: 1px solid #1e293b;
        }

        /* Marker for active nav (Removed for solid design) */
        .nav-marker {
            display: none;
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
    

