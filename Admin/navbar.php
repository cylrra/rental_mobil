<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

// Fungsi pembantu untuk menentukan class CSS menu yang aktif
function getLinkClass($page, $current_page) {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "nav-item-active flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all group";
    }
    return "flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-brand-500 transition-all group";
}

function getIconClass($page, $current_page) {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "w-5 h-5";
    }
    return "w-5 h-5 opacity-70 group-hover:opacity-100";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard - PT INDOMAX RENTAL</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#f8fafc', 500: '#3b82f6', 600: '#2563eb', 900: '#1e293b' },
                        pastel: {
                            blue: { bg: '#e0f2fe', text: '#0369a1' },
                            green: { bg: '#d1fae5', text: '#065f46' },
                            red: { bg: '#fee2e2', text: '#991b1b' }
                        }
                    }
                }
            }
        };
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
        /* Sidebar: Efek Glassmorphism Pastel */
        .sidebar-pastel { 
            background-color: rgba(239, 246, 255, 0.7) !important; 
            backdrop-filter: blur(12px); 
            border-right: 1px solid rgba(191, 219, 254, 0.5); 
        }
    
        /* Navigasi Aktif */
        .nav-item-active { 
            background-color: #dbeafe !important; 
            color: #1e40af !important; 
            font-weight: 600; 
        }
    
        /* Header Minimalis */
        .header-pastel {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="bg-[#f0f4f8] text-slate-800 font-sans antialiased">
<div class="flex h-screen overflow-hidden">
    
    <aside class="w-72 sidebar-pastel text-slate-700 flex-shrink-0 flex flex-col h-full overflow-y-auto" id="sidebar">
        <div class="p-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center shadow-md">
                    <i class="w-5 h-5 text-white" data-lucide="car"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight text-blue-900">INDOMAX</h1>
                    <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Rental System</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 pb-10 space-y-6">
            <div>
                <h2 class="px-4 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dashboard</h2>
                <a class="<?= getLinkClass('index.php', $current_page) ?>" href="index.php">
                    <i class="<?= getIconClass('index.php', $current_page) ?>" data-lucide="layout-dashboard"></i>
                    <span class="text-sm font-semibold">Dashboard Utama</span>
                </a>
            </div>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div>
                <h2 class="px-4 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data Master</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('mobil.php', $current_page) ?>" href="mobil.php"><i class="<?= getIconClass('mobil.php', $current_page) ?>" data-lucide="truck"></i><span class="text-sm font-medium">Mobil</span></a>
                    <a class="<?= getLinkClass('supir.php', $current_page) ?>" href="supir.php"><i class="<?= getIconClass('supir.php', $current_page) ?>" data-lucide="user-square"></i><span class="text-sm font-medium">Supir</span></a>
                    <a class="<?= getLinkClass('pelanggan.php', $current_page) ?>" href="pelanggan.php"><i class="<?= getIconClass('pelanggan.php', $current_page) ?>" data-lucide="users"></i><span class="text-sm font-medium">Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operasional</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page) ?>" href="transaksi.php"><i class="<?= getIconClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page) ?>" data-lucide="clipboard-list"></i><span class="text-sm font-medium">Transaksi Sewa</span></a>
                    <a class="<?= getLinkClass('tracking.php', $current_page) ?>" href="tracking.php"><i class="<?= getIconClass('tracking.php', $current_page) ?>" data-lucide="map-pin"></i><span class="text-sm font-medium">Live Tracking</span></a>
                    <a class="<?= getLinkClass('grafik_rating.php', $current_page) ?>" href="grafik_rating.php"><i class="<?= getIconClass('grafik_rating.php', $current_page) ?>" data-lucide="star"></i><span class="text-sm font-medium">Rating Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pemeliharaan</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('jadwal_service.php', $current_page) ?>" href="jadwal_service.php"><i class="<?= getIconClass('jadwal_service.php', $current_page) ?>" data-lucide="calendar-clock"></i><span class="text-sm font-medium">Jadwal Service</span></a>
                    <a class="<?= getLinkClass('riwayat_pemeliharaan.php', $current_page) ?>" href="riwayat_pemeliharaan.php"><i class="<?= getIconClass('riwayat_pemeliharaan.php', $current_page) ?>" data-lucide="history"></i><span class="text-sm font-medium">Riwayat Perbaikan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Akuntansi</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('pembayaran.php', $current_page) ?>" href="pembayaran.php"><i class="<?= getIconClass('pembayaran.php', $current_page) ?>" data-lucide="wallet"></i><span class="text-sm font-medium">Input Pembayaran</span></a>
                    <a class="<?= getLinkClass('riwayat_pembayaran.php', $current_page) ?>" href="riwayat_pembayaran.php"><i class="<?= getIconClass('riwayat_pembayaran.php', $current_page) ?>" data-lucide="landmark"></i><span class="text-sm font-medium">Riwayat Pembayaran</span></a>
                    <a class="<?= getLinkClass('jurnal_umum.php', $current_page) ?>" href="jurnal_umum.php"><i class="<?= getIconClass('jurnal_umum.php', $current_page) ?>" data-lucide="book-open"></i><span class="text-sm font-medium">Jurnal Umum</span></a>
                    <a class="<?= getLinkClass('laporan_laba_rugi.php', $current_page) ?>" href="laporan_laba_rugi.php"><i class="<?= getIconClass('laporan_laba_rugi.php', $current_page) ?>" data-lucide="bar-chart-3"></i><span class="text-sm font-medium">Laba Rugi</span></a>
                    <a class="<?= getLinkClass('cetak_kwitansi.php', $current_page) ?>" href="cetak_kwitansi.php"><i class="<?= getIconClass('cetak_kwitansi.php', $current_page) ?>" data-lucide="printer"></i><span class="text-sm font-medium">Invoice</span></a>
                </div>
            </div>
            <?php endif; ?>
        </nav>
        
        <div class="p-6">
            <a class="flex items-center justify-center gap-2 bg-pastel-red-bg text-pastel-red-text py-3 rounded-lg font-bold text-sm transition-all hover:bg-red-100" href="logout_admin.php">
                <i class="w-4 h-4" data-lucide="log-out"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="h-20 header-pastel flex items-center justify-between px-10 shrink-0 sticky top-0 z-10">
            <div>
                <h2 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sistem Informasi Armada</h2>
                <h3 class="text-lg font-bold text-blue-900">PT INDOMAX RENTAL</h3>
            </div>
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-blue-100 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center text-white">
                    <i class="w-4 h-4" data-lucide="user"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($_SESSION['nama_user'] ?? 'Administrator'); ?></span>
                    <span class="text-[10px] font-medium text-blue-500 uppercase"><?= htmlspecialchars($_SESSION['role'] ?? 'Super Admin'); ?></span>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-10 space-y-10 custom-scrollbars relative">
    

