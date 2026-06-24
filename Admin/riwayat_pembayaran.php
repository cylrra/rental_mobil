<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = 'riwayat_pembayaran.php';

function getLinkClass($page, $current_page) {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "nav-item-active flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group bg-[#d4af37] shadow-md shadow-[#d4af37]/30 text-[#1a1c1c]";
    }
    return "flex items-center gap-3 px-4 py-3 rounded-xl text-rose-100 hover:bg-[#800000]/80 hover:text-white hover:translate-x-2 transition-all duration-300 group cursor-pointer font-semibold";
}

function getIconClass($page, $current_page, $colorClass = 'text-white') {
    $pages = is_array($page) ? $page : [$page];
    if (in_array($current_page, $pages)) {
        return "w-5 h-5 text-[#1a1c1c] drop-shadow-sm scale-110 transition-transform duration-300";
    }
    return "w-5 h-5 text-rose-200 group-hover:text-white group-hover:scale-110 group-hover:rotate-6 transition-all duration-300";
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] },
                }
            }
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #4d4c4c; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #800000; }
        .sidebar-constellation { background: linear-gradient(180deg, #800000 0%, #4a0000 100%) !important; border-right: 1px solid #600000; }
        .nav-item-active { background-color: #d4af37 !important; color: #1a1c1c !important; font-weight: 700; box-shadow: 0 10px 15px -3px rgba(212, 175, 55, 0.3) !important; transform: scale(1.02); }
        .header-constellation { background-color: #ffffff !important; border-bottom: 1px solid #e8e8e8; box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: all 0.3s ease; }
        .sidebar-label { font-size: 0.72rem; font-weight: 800; color: rgba(255, 255, 255, 0.4); text-transform: uppercase; letter-spacing: 0.15em; }
        .hover-lift { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04); }
        .brand-logo-box { width: 42px; height: 42px; min-width: 42px; background-color: #800000; border-radius: 10px; }
        .table-report thead th { background-color: #f8f9fc !important; color: #800000 !important; font-weight: 800; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; border-bottom: 2px solid #e3e6f0 !important; }
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
                    <i class="<?= getIconClass('index.php', $current_page) ?>" data-lucide="layout-dashboard"></i>
                    <span class="text-sm font-semibold">Dashboard Utama</span>
                </a>
            </div>
            
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div>
                <h2 class="px-4 mb-3 sidebar-label">Data Master</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('mobil.php', $current_page) ?>" href="mobil.php"><i class="<?= getIconClass('mobil.php', $current_page) ?>" data-lucide="truck"></i><span class="text-sm font-medium">Mobil</span></a>
                    <a class="<?= getLinkClass('supir.php', $current_page) ?>" href="supir.php"><i class="<?= getIconClass('supir.php', $current_page) ?>" data-lucide="user-square"></i><span class="text-sm font-medium">Supir</span></a>
                    <a class="<?= getLinkClass('pelanggan.php', $current_page) ?>" href="pelanggan.php"><i class="<?= getIconClass('pelanggan.php', $current_page) ?>" data-lucide="users"></i><span class="text-sm font-medium">Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Operasional</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page) ?>" href="transaksi.php"><i class="<?= getIconClass(['transaksi.php', 'Transaksi_Baru.php'], $current_page) ?>" data-lucide="clipboard-list"></i><span class="text-sm font-medium">Transaksi Sewa</span></a>
                    <a class="<?= getLinkClass('tracking.php', $current_page) ?>" href="tracking.php"><i class="<?= getIconClass('tracking.php', $current_page) ?>" data-lucide="map-pin"></i><span class="text-sm font-medium">Live Tracking</span></a>
                    <a class="<?= getLinkClass('grafik_rating.php', $current_page) ?>" href="grafik_rating.php"><i class="<?= getIconClass('grafik_rating.php', $current_page) ?>" data-lucide="star"></i><span class="text-sm font-medium">Rating Pelanggan</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Pemeliharaan</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('jadwal_service.php', $current_page) ?>" href="jadwal_service.php"><i class="<?= getIconClass('jadwal_service.php', $current_page) ?>" data-lucide="calendar-check"></i><span class="text-sm font-medium">Jadwal Servis</span></a>
                    <a class="<?= getLinkClass('riwayat_pemeliharaan.php', $current_page) ?>" href="riwayat_pemeliharaan.php"><i class="<?= getIconClass('riwayat_pemeliharaan.php', $current_page) ?>" data-lucide="clock-history"></i><span class="text-sm font-medium">Riwayat Servis</span></a>
                </div>
            </div>

            <div>
                <h2 class="px-4 mb-3 sidebar-label">Akuntansi</h2>
                <div class="space-y-1">
                    <a class="<?= getLinkClass('pembayaran.php', $current_page) ?>" href="pembayaran.php"><i class="<?= getIconClass('pembayaran.php', $current_page) ?>" data-lucide="wallet"></i><span class="text-sm font-medium">Input Pembayaran</span></a>
                    <a class="<?= getLinkClass('riwayat_pembayaran.php', $current_page) ?>" href="riwayat_pembayaran.php"><i class="<?= getIconClass('riwayat_pembayaran.php', $current_page) ?>" data-lucide="landmark"></i><span class="text-sm font-medium">Riwayat Pembayaran</span></a>
                    <a class="<?= getLinkClass('jurnal_umum.php', $current_page) ?>" href="jurnal_umum.php"><i class="<?= getIconClass('jurnal_umum.php', $current_page) ?>" data-lucide="book-open"></i><span class="text-sm font-medium">Jurnal Umum</span></a>
                    <a class="<?= getLinkClass('riwayat_jurnal_umum.php', $current_page) ?>" href="riwayat_jurnal_umum.php"><i class="<?= getIconClass('riwayat_jurnal_umum.php', $current_page) ?>" data-lucide="book-open"></i><span class="text-sm font-medium">Riwayat Jurnal Umum</span></a>
                    <a class="<?= getLinkClass('laporan_laba_rugi.php', $current_page) ?>" href="laporan_laba_rugi.php"><i class="<?= getIconClass('laporan_laba_rugi.php', $current_page) ?>" data-lucide="bar-chart-3"></i><span class="text-sm font-medium">Laba Rugi</span></a>
                    <a class="<?= getLinkClass('coa.php', $current_page) ?>" href="coa.php"><i class="<?= getIconClass('coa.php', $current_page) ?>" data-lucide="list-tree"></i><span class="text-sm font-medium">Chart of Accounts</span></a>
                    <a class="<?= getLinkClass('cetak_kwitansi.php', $current_page) ?>" href="cetak_kwitansi.php"><i class="<?= getIconClass('cetak_kwitansi.php', $current_page) ?>" data-lucide="printer"></i><span class="text-sm font-medium">Invoice</span></a>
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

        <?php 
        include 'koneksi.php'; 

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

        $q_total = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
        $total_masuk = mysqli_fetch_assoc($q_total)['total'] ?? 0;

        $q_cash = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Cash'");
        $total_cash = mysqli_fetch_assoc($q_cash)['total'] ?? 0;

        $q_transfer = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Transfer'");
        $total_transfer = mysqli_fetch_assoc($q_transfer)['total'] ?? 0;
        ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="bg-white rounded-2xl p-8 border border-[#e2e2e2] shadow-sm hover-lift mb-8">
            <div class="flex flex-col md:flex-row items-center justify-between mb-8 pb-4 border-b border-[#e2e2e2]">
                <div class="flex items-center gap-4">
                    <div class="brand-logo-box flex items-center justify-center text-white shadow-sm">
                        <i data-lucide="car-front" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-2xl tracking-tight text-[#800000]">Laporan Arus Kas</h4>
                        <small class="text-slate-400 font-bold tracking-widest uppercase">INDOMAX Rental System</small>
                    </div>
                </div>
                <div class="text-right mt-4 md:mt-0">
                    <h5 class="font-bold text-slate-800 uppercase tracking-widest text-sm mb-1">Periode Aktif</h5>
                    <p class="text-slate-500 font-medium text-xs">S.d <span class="font-black text-[#d4af37]"><?= date('F Y') ?></span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Total Penerimaan</small>
                    <h4 class="font-black text-[#800000] text-2xl mt-1">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h4>
                </div>
                <div class="bg-white border border-[#e2e2e2] p-5 rounded-2xl shadow-sm">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded mr-1">CASH</span> Tunai
                    </small>
                    <h5 class="font-black text-slate-800 text-xl mt-1">Rp <?php echo number_format($total_cash, 0, ',', '.'); ?></h5>
                </div>
                <div class="bg-white border border-[#e2e2e2] p-5 rounded-2xl shadow-sm">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-1">BANK</span> Transfer
                    </small>
                    <h5 class="font-black text-slate-800 text-xl mt-1">Rp <?php echo number_format($total_transfer, 0, ',', '.'); ?></h5>
                </div>
            </div>

            <div class="border border-[#e2e2e2] rounded-2xl p-6 bg-white mb-8">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-slate-600 uppercase tracking-widest text-xs flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-[#800000]"></i> Grafik Pendapatan
                    </span>
                    <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Live</span>
                </div>
                <div style="position: relative; height:280px; width:100%">
                    <canvas id="canvasTransaksi"></canvas>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h6 class="font-black text-slate-800 uppercase tracking-widest text-sm">Buku Jurnal Riwayat Transaksi</h6>
                <a href="pembayaran.php" class="bg-[#d4af37] text-[#1a1c1c] font-bold py-2 px-4 text-xs rounded-xl shadow-md hover:bg-[#c49d2b] transition-colors flex items-center gap-2">
                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Entri
                </a>
            </div>

            <div class="overflow-x-auto border border-[#e2e2e2] rounded-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-[#e2e2e2] text-[#800000] text-[10px] uppercase tracking-widest font-black">
                            <th class="p-4 rounded-tl-xl">ID Dokumen</th>
                            <th class="p-4">Deskripsi / Pelanggan</th>
                            <th class="p-4">Tanggal Buku</th>
                            <th class="p-4">Metode Kas</th>
                            <th class="p-4 text-right">Nominal Masuk</th>
                            <th class="p-4 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        $sql = "SELECT p.*, pl.nama 
                                FROM pembayaran p
                                JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                                JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
                                ORDER BY p.id_pembayaran DESC";
                        $query = mysqli_query($conn, $sql);
                        if ($query && mysqli_num_rows($query) > 0) {
                            while($d = mysqli_fetch_array($query)){
                                $badge_class = ($d['metode_pembayaran'] == 'Transfer') ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700';
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-xs font-bold text-slate-500">#PYM-<?php echo $d['id_pembayaran']; ?></td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800 text-sm"><?php echo $d['nama']; ?></div>
                                <small class="text-slate-500 text-[10px] font-bold tracking-wider">Trx ID: #SRV-<?php echo $d['id_sewa']; ?></small>
                            </td>
                            <td class="p-4 text-slate-500 text-xs font-medium"><?php echo date('d M Y', strtotime($d['tanggal_bayar'])); ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider <?php echo $badge_class; ?>">
                                    <?php echo $d['metode_pembayaran']; ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <span class="font-black text-[#800000] text-sm">
                                    Rp <?php echo number_format($d['jumlah_bayar'], 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="cetak_kwitansi.php?id=<?php echo $d['id_pembayaran']; ?>" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-[#800000] hover:text-white transition-colors mx-auto" target="_blank" title="Cetak Kwitansi">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan='6' class='text-center py-8 text-slate-400 font-medium text-sm'>Tidak ditemukan data pencatatan kas masuk pada periode ini.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-8 pt-4 border-t border-slate-100">
                <small class="text-slate-400 font-medium text-[10px] tracking-widest uppercase">
                    Laporan ini dibuat otomatis secara digital oleh INDOMAX Rental System.
                </small>
            </div>
        </div>

        </div>
    </main>
</div>

<script>
    lucide.createIcons();

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
                    backgroundColor: 'rgba(212, 175, 55, 0.2)', // Gold transparent
                    borderColor: '#d4af37',
                    borderWidth: 1.5,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Omset Penjualan (x100.000 Rp)',
                    data: dataRevenues,
                    backgroundColor: '#800000', // Maroon
                    borderColor: '#800000',
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
                    labels: { boxWidth: 12, font: { family: "'Montserrat', sans-serif", weight: '700', size: 11 } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: { font: { family: "'Montserrat', sans-serif", size: 10, weight: '500' } }
                },
                x: { grid: { display: false }, ticks: { font: { family: "'Montserrat', sans-serif", size: 10, weight: '600' } } }
            }
        },
        plugins: [{
            id: 'customGrowthLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = '800 10px Montserrat, sans-serif';
                const meta = chart.getDatasetMeta(1); 
                meta.data.forEach((bar, index) => {
                    const text = dataGrowth[index];
                    ctx.fillStyle = text.includes('-') ? '#e11d48' : '#10b981';
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