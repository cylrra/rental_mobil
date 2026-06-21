<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pengguna dengan role 'admin' yang boleh masuk halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

// 1. Memanggil koneksi database
include 'koneksi.php'; 

// =========================================================================
// MENGAMBIL DATA DARI DATABASE (rental_mobil)
// =========================================================================

// Menghitung total mobil terdaftar
$query_mobil = mysqli_query($conn, "SELECT * FROM mobil");
$total_mobil = mysqli_num_rows($query_mobil);

// Menghitung total transaksi sewa
$query_transaksi = mysqli_query($conn, "SELECT * FROM transaksi_sewa");
$total_transaksi = mysqli_num_rows($query_transaksi);

// Menjumlahkan total pembayaran/pendapatan
$query_pendapatan = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
$total_pendapatan = ($query_pendapatan && mysqli_num_rows($query_pendapatan) > 0) ? (mysqli_fetch_assoc($query_pendapatan)['total'] ?? 0) : 0;

// Mengambil rata-rata rating pelayanan dan supir
$query_rating = mysqli_query($conn, "SELECT AVG(rating_pelayanan) as avg_pelayanan, AVG(rating_supir) as avg_supir, COUNT(*) as total_ulasan FROM rating_sewa");
$data_rating = ($query_rating && mysqli_num_rows($query_rating) > 0) ? mysqli_fetch_assoc($query_rating) : ['avg_pelayanan' => 0, 'avg_supir' => 0, 'total_ulasan' => 0];

$avg_pelayanan = round($data_rating['avg_pelayanan'] ?? 0, 1);
$avg_supir = round($data_rating['avg_supir'] ?? 0, 1);
$total_ulasan = $data_rating['total_ulasan'] ?? 0;

// Mengambil distribusi bintang pelayanan
$dist_pelayanan = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
if ($total_ulasan > 0) {
    $q_dist = mysqli_query($conn, "SELECT rating_pelayanan, COUNT(*) as count FROM rating_sewa GROUP BY rating_pelayanan");
    if ($q_dist) {
        while($r = mysqli_fetch_assoc($q_dist)) {
            $dist_pelayanan[(int)$r['rating_pelayanan']] = (int)$r['count'];
        }
    }
}

// =========================================================================

// 2. Memanggil layout navbar & sidebar
include 'navbar.php'; 
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard Overview</h1>
    <p class="text-slate-500 mt-1 font-medium italic">Selamat datang kembali, mari kelola armada Anda hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
    
    <a href="mobil.php" class="bg-gradient-to-br from-[#06588c] to-[#04345a] shadow-lg shadow-[#06588c]/30 rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group block">
        <div class="flex items-start justify-between mb-8 relative z-10">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                <i class="w-6 h-6 text-white" data-lucide="truck"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#c8c6c6]">Armada</span>
        </div>
        <div class="relative z-10">
            <p class="text-[#c8c6c6] text-sm font-medium">Total Armada Terdaftar</p>
            <h4 class="text-4xl font-bold mt-2 text-white"><?php echo $total_mobil; ?> <span class="text-lg font-medium text-[#c8c6c6] ml-1">Unit</span></h4>
        </div>
        <!-- Decorative bg -->
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
    </a>

    <a href="transaksi.php" class="bg-gradient-to-br from-[#10b981] to-[#047857] shadow-lg shadow-[#10b981]/30 rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group block">
        <div class="flex items-start justify-between mb-8 relative z-10">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                <i class="w-6 h-6 text-white" data-lucide="file-text"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-100">Transaksi</span>
        </div>
        <div class="relative z-10">
            <p class="text-emerald-100 text-sm font-medium">Sewa Terdaftar</p>
            <h4 class="text-4xl font-bold mt-2 text-white"><?php echo $total_transaksi; ?> <span class="text-lg font-medium text-emerald-200 ml-1">Data</span></h4>
        </div>
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
    </a>

    <a href="laporan_laba_rugi.php" class="bg-gradient-to-br from-[#f59e0b] to-[#d97706] shadow-lg shadow-[#f59e0b]/30 rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group block">
        <div class="flex items-start justify-between mb-8 relative z-10">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                <i class="w-6 h-6 text-white" data-lucide="banknote"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-amber-100">Keuangan</span>
        </div>
        <div class="relative z-10">
            <p class="text-amber-100 text-sm font-medium">Total Kas & Pendapatan</p>
            <h4 class="text-3xl font-bold mt-2 text-white">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h4>
        </div>
        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
    </a>

</div>

<div class="bg-white rounded-2xl p-8 hover-lift border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-8">
        <h5 class="text-lg font-bold text-slate-800">Akses Cepat Menu Administrasi</h5>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
        <a href="transaksi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#0b2b51]/10 text-[#0b2b51] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#0b2b51] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#0b2b51]/20">
                <i class="w-7 h-7" data-lucide="shopping-cart"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#0b2b51] transition-colors">Transaksi</span>
        </a>
        
        <a href="pembayaran.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#10b981]/10 text-[#10b981] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#10b981] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#10b981]/20">
                <i class="w-7 h-7" data-lucide="badge-dollar-sign"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#10b981] transition-colors">Pembayaran</span>
        </a>
        
        <a href="jurnal_umum.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#f97316]/10 text-[#f97316] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#f97316] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#f97316]/20">
                <i class="w-7 h-7" data-lucide="book"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#f97316] transition-colors text-center leading-tight">Jurnal</span>
        </a>
        
        <a href="laporan_laba_rugi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#e11d48]/10 text-[#e11d48] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#e11d48] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#e11d48]/20">
                <i class="w-7 h-7" data-lucide="trending-up"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#e11d48] transition-colors text-center leading-tight">Laba Rugi</span>
        </a>
        
        <a href="cetak_kwitansi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#0d9488]/10 text-[#0d9488] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#0d9488] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#0d9488]/20">
                <i class="w-7 h-7" data-lucide="file-text"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#0d9488] transition-colors">Invoice</span>
        </a>
        
        <a href="grafik_rating.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-[#6366f1]/10 text-[#6366f1] rounded-2xl flex items-center justify-center mb-3 group-hover:bg-[#6366f1] group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-[#6366f1]/20">
                <i class="w-7 h-7" data-lucide="pie-chart"></i>
            </div>
            <span class="text-sm font-bold text-slate-500 group-hover:text-[#6366f1] transition-colors text-center leading-tight">Grafik</span>
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl p-8 hover-lift mb-10 border border-slate-200 shadow-sm">
    <h5 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <i data-lucide="star" class="w-6 h-6 text-blue-600"></i> Ulasan & Rating Pelayanan
    </h5>
    
    <div class="flex flex-col md:flex-row gap-8 items-center">
        <!-- Kiri: Big Number -->
        <div class="flex flex-col items-center justify-center min-w-[150px]">
            <h1 class="text-6xl font-extrabold text-slate-800 tracking-tighter"><?= number_format($avg_pelayanan, 1) ?></h1>
            <div class="flex text-blue-500 my-2 text-xl">
                <?php 
                $full_stars = floor($avg_pelayanan);
                $half_star = ($avg_pelayanan - $full_stars) >= 0.5;
                for($i=1; $i<=5; $i++) {
                    if($i <= $full_stars) echo '<i class="bi bi-star-fill"></i>';
                    else if($i == $full_stars + 1 && $half_star) echo '<i class="bi bi-star-half"></i>';
                    else echo '<i class="bi bi-star"></i>';
                }
                ?>
            </div>
            <p class="text-sm font-medium text-slate-500"><?= $total_ulasan ?> ulasan</p>
        </div>
        
        <!-- Kanan: Bars -->
        <div class="flex-1 w-full flex flex-col gap-2">
            <?php for($i=5; $i>=1; $i--): 
                $count = $dist_pelayanan[$i];
                $pct = $total_ulasan > 0 ? ($count / $total_ulasan) * 100 : 0;
            ?>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-600 w-3"><?= $i ?></span>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: <?= $pct ?>%;"></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Daftar Performa (Ulasan Terbaru) -->
<div class="bg-white rounded-2xl p-8 hover-lift mb-10 border border-slate-200 shadow-sm">
    <h5 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <i data-lucide="message-square" class="w-6 h-6 text-blue-600"></i> Daftar Performa & Ulasan Terbaru
    </h5>
    <div class="space-y-4">
        <?php
        $q_ulasan = mysqli_query($conn, "
            SELECT r.*, t.id_pelanggan, t.id_supir, p.nama, s.nama_supir 
            FROM rating_sewa r 
            JOIN transaksi_sewa t ON r.id_transaksi = t.id_sewa 
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
            LEFT JOIN supir s ON t.id_supir = s.id_supir 
            ORDER BY r.id_rating DESC LIMIT 5
        ");
        if($q_ulasan && mysqli_num_rows($q_ulasan) > 0) {
            while($u = mysqli_fetch_assoc($q_ulasan)) {
                ?>
                <div class="p-5 rounded-xl bg-slate-50 border border-blue-100/50 hover:border-blue-200 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="font-bold text-slate-800 text-base block"><?= htmlspecialchars($u['nama']) ?></span>
                            <?php if(!empty($u['nama_supir'])): ?>
                                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md mt-1 inline-block">Supir: <?= htmlspecialchars($u['nama_supir']) ?> (Rating: <?= $u['rating_supir'] ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex text-blue-500 text-sm">
                            <?php for($i=0; $i<$u['rating_pelayanan']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                            <?php for($i=$u['rating_pelayanan']; $i<5; $i++) echo '<i class="bi bi-star text-slate-300"></i>'; ?>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 italic">"<?= !empty($u['komentar']) ? htmlspecialchars($u['komentar']) : 'Tidak ada komentar tertulis.' ?>"</p>
                </div>
                <?php
            }
        } else {
            echo '<p class="text-sm text-slate-500 italic">Belum ada ulasan terdaftar.</p>';
        }
        ?>
    </div>
</div>

</div> </main> </div> 

<script>
    lucide.createIcons();
</script>
</body>
</html>