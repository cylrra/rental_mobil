<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'koneksi.php'; 

$query_mobil = mysqli_query($conn, "SELECT COUNT(*) as total FROM mobil");
$total_mobil = mysqli_fetch_assoc($query_mobil)['total'] ?? 0;

$query_transaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi_sewa");
$total_transaksi = mysqli_fetch_assoc($query_transaksi)['total'] ?? 0;

$query_berjalan = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi_sewa WHERE status_sewa = 'berjalan'");
$total_berjalan = mysqli_fetch_assoc($query_berjalan)['total'] ?? 0;

$query_pelanggan = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan");
$total_pelanggan = mysqli_fetch_assoc($query_pelanggan)['total'] ?? 0;

$query_pendapatan = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
$total_pendapatan = mysqli_fetch_assoc($query_pendapatan)['total'] ?? 0;

$query_rating = mysqli_query($conn, "SELECT AVG(rating_pelayanan) as avg_pelayanan, COUNT(*) as total_ulasan FROM rating_sewa");
$data_rating = mysqli_fetch_assoc($query_rating);
$avg_pelayanan = round($data_rating['avg_pelayanan'] ?? 0, 1);
$total_ulasan = $data_rating['total_ulasan'] ?? 0;

// Transaksi terbaru
$q_recent = mysqli_query($conn, "SELECT t.id_sewa, p.nama, m.merk, t.tanggal_sewa, t.status_sewa, t.total_biaya 
    FROM transaksi_sewa t 
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
    JOIN mobil m ON t.kode_mobil = m.kode_mobil 
    ORDER BY t.id_sewa DESC LIMIT 6");

// Distribusi rating
$dist_pelayanan = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
if ($total_ulasan > 0) {
    $q_dist = mysqli_query($conn, "SELECT rating_pelayanan, COUNT(*) as count FROM rating_sewa GROUP BY rating_pelayanan");
    while($r = mysqli_fetch_assoc($q_dist)) {
        $dist_pelayanan[(int)$r['rating_pelayanan']] = (int)$r['count'];
    }
}

include 'navbar.php'; 
?>

<style>
.stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important; }
.quick-link { transition: all 0.25s ease; }
.quick-link:hover { transform: translateY(-3px); }
.badge-status-berjalan { background: linear-gradient(135deg, #16a34a, #15803d); }
.badge-status-pending { background: linear-gradient(135deg, #d97706, #b45309); }
.badge-status-selesai { background: linear-gradient(135deg, #64748b, #475569); }
.badge-status-diterima { background: linear-gradient(135deg, #0891b2, #0e7490); }
.table-row-hover:hover { background: #fff8f8 !important; }
</style>

<div class="space-y-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="text-xs font-black text-[#d4af37] uppercase tracking-[0.25em] mb-1">Selamat Datang Kembali 👋</p>
            <h1 class="text-4xl font-black text-[#800000] tracking-tight leading-none">Dashboard Overview</h1>
            <p class="text-slate-500 mt-2 font-medium">Ringkasan operasional PT INDOMAX RENTAL hari ini.</p>
        </div>
        <div class="flex items-center gap-2 bg-white border border-[#e2e2e2] px-4 py-2.5 rounded-xl shadow-sm">
            <i data-lucide="calendar" class="w-4 h-4 text-[#800000]"></i>
            <span class="text-sm font-bold text-slate-700"><?= date('l, d F Y') ?></span>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">

        <a href="mobil.php" class="stat-card bg-gradient-to-br from-[#800000] to-[#4a0000] rounded-2xl p-6 shadow-lg shadow-[#800000]/20 text-white relative overflow-hidden block">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full"></div>
            <div class="absolute -right-2 -bottom-8 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mb-4 backdrop-blur-sm">
                <i data-lucide="truck" class="w-6 h-6 text-white"></i>
            </div>
            <p class="text-white/70 text-xs font-bold uppercase tracking-wider mb-1">Total Armada</p>
            <h3 class="text-4xl font-black"><?= $total_mobil ?></h3>
            <p class="text-white/60 text-xs mt-1">Unit Terdaftar</p>
        </a>

        <a href="transaksi.php" class="stat-card bg-gradient-to-br from-[#d4af37] to-[#a07c10] rounded-2xl p-6 shadow-lg shadow-[#d4af37]/20 text-white relative overflow-hidden block">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
            </div>
            <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">Transaksi Aktif</p>
            <h3 class="text-4xl font-black"><?= $total_berjalan ?></h3>
            <p class="text-white/70 text-xs mt-1">Sedang Berjalan</p>
        </a>

        <a href="pelanggan.php" class="stat-card bg-gradient-to-br from-[#1e3a5f] to-[#0d1f35] rounded-2xl p-6 shadow-lg shadow-[#1e3a5f]/20 text-white relative overflow-hidden block">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full"></div>
            <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="users" class="w-6 h-6 text-white"></i>
            </div>
            <p class="text-white/70 text-xs font-bold uppercase tracking-wider mb-1">Pelanggan</p>
            <h3 class="text-4xl font-black"><?= $total_pelanggan ?></h3>
            <p class="text-white/60 text-xs mt-1">Terdaftar</p>
        </a>

        <a href="laporan_laba_rugi.php" class="stat-card bg-gradient-to-br from-[#166534] to-[#052e16] rounded-2xl p-6 shadow-lg shadow-[#166534]/20 text-white relative overflow-hidden block">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full"></div>
            <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
            </div>
            <p class="text-white/70 text-xs font-bold uppercase tracking-wider mb-1">Total Pendapatan</p>
            <h3 class="text-2xl font-black">Rp <?= number_format($total_pendapatan/1000000, 1) ?>jt</h3>
            <p class="text-white/60 text-xs mt-1">Kas Masuk</p>
        </a>

    </div>

    <!-- Quick Access + Rating -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Quick Access -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-[#e2e2e2] shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-9 h-9 rounded-lg bg-[#800000]/10 flex items-center justify-center">
                    <i data-lucide="zap" class="w-4 h-4 text-[#800000]"></i>
                </div>
                <h5 class="text-base font-black text-[#1a1c1c]">Akses Cepat Menu</h5>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-4">
                <?php
                $menus = [
                    ['href'=>'transaksi.php', 'icon'=>'clipboard-list', 'label'=>'Transaksi', 'bg'=>'bg-[#800000]/10', 'text'=>'text-[#800000]', 'hover_bg'=>'group-hover:bg-[#800000]'],
                    ['href'=>'pembayaran.php', 'icon'=>'wallet', 'label'=>'Pembayaran', 'bg'=>'bg-[#d4af37]/10', 'text'=>'text-[#a07c10]', 'hover_bg'=>'group-hover:bg-[#d4af37]'],
                    ['href'=>'mobil.php', 'icon'=>'truck', 'label'=>'Armada', 'bg'=>'bg-blue-50', 'text'=>'text-blue-600', 'hover_bg'=>'group-hover:bg-blue-600'],
                    ['href'=>'pelanggan.php', 'icon'=>'users', 'label'=>'Pelanggan', 'bg'=>'bg-purple-50', 'text'=>'text-purple-600', 'hover_bg'=>'group-hover:bg-purple-600'],
                    ['href'=>'supir.php', 'icon'=>'user-square', 'label'=>'Supir', 'bg'=>'bg-cyan-50', 'text'=>'text-cyan-600', 'hover_bg'=>'group-hover:bg-cyan-600'],
                    ['href'=>'jadwal_service.php', 'icon'=>'calendar-check', 'label'=>'Servis', 'bg'=>'bg-orange-50', 'text'=>'text-orange-600', 'hover_bg'=>'group-hover:bg-orange-600'],
                    ['href'=>'jurnal_umum.php', 'icon'=>'book-open', 'label'=>'Jurnal', 'bg'=>'bg-emerald-50', 'text'=>'text-emerald-600', 'hover_bg'=>'group-hover:bg-emerald-600'],
                    ['href'=>'cetak_kwitansi.php', 'icon'=>'printer', 'label'=>'Invoice', 'bg'=>'bg-rose-50', 'text'=>'text-rose-600', 'hover_bg'=>'group-hover:bg-rose-600'],
                    ['href'=>'laporan_laba_rugi.php', 'icon'=>'trending-up', 'label'=>'Laba Rugi', 'bg'=>'bg-teal-50', 'text'=>'text-teal-600', 'hover_bg'=>'group-hover:bg-teal-600'],
                    ['href'=>'grafik_rating.php', 'icon'=>'star', 'label'=>'Rating', 'bg'=>'bg-amber-50', 'text'=>'text-amber-600', 'hover_bg'=>'group-hover:bg-amber-600'],
                    ['href'=>'grafik_transaksi.php', 'icon'=>'bar-chart-3', 'label'=>'Grafik', 'bg'=>'bg-indigo-50', 'text'=>'text-indigo-600', 'hover_bg'=>'group-hover:bg-indigo-600'],
                    ['href'=>'tracking.php', 'icon'=>'map-pin', 'label'=>'Tracking', 'bg'=>'bg-red-50', 'text'=>'text-red-600', 'hover_bg'=>'group-hover:bg-red-600'],
                ];
                foreach($menus as $m):
                ?>
                <a href="<?= $m['href'] ?>" class="quick-link flex flex-col items-center gap-2 group cursor-pointer">
                    <div class="w-14 h-14 <?= $m['bg'] ?> <?= $m['text'] ?> rounded-2xl flex items-center justify-center <?= $m['hover_bg'] ?> group-hover:text-white transition-all duration-300 shadow-sm">
                        <i data-lucide="<?= $m['icon'] ?>" class="w-6 h-6"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 group-hover:text-slate-800 transition-colors text-center leading-tight"><?= $m['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Rating Summary -->
        <div class="bg-white rounded-2xl p-6 border border-[#e2e2e2] shadow-sm">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-lg bg-[#d4af37]/15 flex items-center justify-center">
                    <i data-lucide="star" class="w-4 h-4 text-[#d4af37]"></i>
                </div>
                <h5 class="text-base font-black text-[#1a1c1c]">Rating Pelayanan</h5>
            </div>
            <div class="flex flex-col items-center mb-4">
                <span class="text-6xl font-black text-[#800000]"><?= number_format($avg_pelayanan, 1) ?></span>
                <div class="flex gap-1 my-2">
                    <?php 
                    $full = floor($avg_pelayanan); $half = ($avg_pelayanan - $full) >= 0.5;
                    for($i=1;$i<=5;$i++) {
                        if($i<=$full) echo '<i class="bi bi-star-fill text-[#d4af37] text-lg"></i>';
                        else if($i==$full+1 && $half) echo '<i class="bi bi-star-half text-[#d4af37] text-lg"></i>';
                        else echo '<i class="bi bi-star text-slate-300 text-lg"></i>';
                    } ?>
                </div>
                <span class="text-xs font-medium text-slate-400"><?= $total_ulasan ?> ulasan</span>
            </div>
            <div class="space-y-2">
                <?php for($i=5;$i>=1;$i--):
                    $count = $dist_pelayanan[$i];
                    $pct = $total_ulasan > 0 ? ($count/$total_ulasan)*100 : 0;
                ?>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-slate-500 w-3 text-right"><?= $i ?></span>
                    <i class="bi bi-star-fill text-[#d4af37] text-[10px]"></i>
                    <div class="flex-1 bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-[#800000] to-[#d4af37] transition-all duration-500" style="width:<?= $pct ?>%"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 w-5"><?= $count ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl border border-[#e2e2e2] shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#800000]/10 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-[#800000]"></i>
                </div>
                <h5 class="text-base font-black text-[#1a1c1c]">Transaksi Terbaru</h5>
            </div>
            <a href="transaksi.php" class="text-xs font-bold text-[#800000] hover:text-[#d4af37] transition-colors flex items-center gap-1">
                Lihat Semua <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Mobil</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-[10px] font-black text-slate-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    $statusMap = [
                        'berjalan' => ['label'=>'Berjalan','class'=>'badge-status-berjalan'],
                        'pending'  => ['label'=>'Pending','class'=>'badge-status-pending'],
                        'selesai'  => ['label'=>'Selesai','class'=>'badge-status-selesai'],
                        'diterima' => ['label'=>'Diterima','class'=>'badge-status-diterima'],
                    ];
                    if ($q_recent && mysqli_num_rows($q_recent) > 0):
                        while($r = mysqli_fetch_assoc($q_recent)):
                            $st = $statusMap[$r['status_sewa']] ?? ['label'=>$r['status_sewa'],'class'=>'badge-status-selesai'];
                    ?>
                    <tr class="table-row-hover transition-colors cursor-pointer" onclick="window.location='transaksi.php'">
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-[#800000]/10 text-[#800000]">#<?= str_pad($r['id_sewa'],4,'0',STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($r['nama']) ?></span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm text-slate-600 font-medium"><?= htmlspecialchars($r['merk']) ?></span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-xs font-medium text-slate-500"><?= date('d M Y', strtotime($r['tanggal_sewa'])) ?></span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="<?= $st['class'] ?> text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider"><?= $st['label'] ?></span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <span class="text-sm font-black text-[#800000]">Rp <?= number_format($r['total_biaya'],0,',','.') ?></span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                            <p class="text-sm font-medium">Belum ada transaksi terdaftar.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</div></main></div>
<script>lucide.createIcons();</script>
</body>
</html>