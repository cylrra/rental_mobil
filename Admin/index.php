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
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total'] ?? 0; // Jika kosong, jadikan 0

// =========================================================================

// 2. Memanggil layout navbar & sidebar
include 'navbar.php'; 
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h1>
    <p class="text-slate-500 mt-1 font-medium italic">Selamat datang kembali, mari kelola armada Anda hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
    
    <div class="glass-card rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group">
        <div class="flex items-start justify-between mb-8">
            <div class="w-12 h-12 bg-pastel-blue-bg rounded-xl flex items-center justify-center">
                <i class="w-6 h-6 text-brand-500" data-lucide="truck"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Armada</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Total Armada Terdaftar</p>
        <h4 class="text-4xl font-bold mt-2 text-slate-800"><?php echo $total_mobil; ?> <span class="text-lg font-medium text-slate-400 ml-1">Unit</span></h4>
    </div>

    <div class="glass-card rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group">
        <div class="flex items-start justify-between mb-8">
            <div class="w-12 h-12 bg-pastel-green-bg rounded-xl flex items-center justify-center">
                <i class="w-6 h-6 text-pastel-green-text" data-lucide="file-text"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Transaksi</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Sewa Terdaftar</p>
        <h4 class="text-4xl font-bold mt-2 text-slate-800"><?php echo $total_transaksi; ?> <span class="text-lg font-medium text-slate-400 ml-1">Data</span></h4>
    </div>

    <div class="glass-card rounded-2xl p-8 hover-lift cursor-pointer relative overflow-hidden group">
        <div class="flex items-start justify-between mb-8">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                <i class="w-6 h-6 text-amber-600" data-lucide="banknote"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Keuangan</span>
        </div>
        <p class="text-slate-500 text-sm font-medium">Total Kas & Pendapatan</p>
        <h4 class="text-3xl font-bold mt-2 text-slate-800">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h4>
    </div>

</div>

<div class="glass-card rounded-2xl p-8 hover-lift">
    <div class="flex items-center justify-between mb-8">
        <h5 class="text-lg font-bold text-slate-800">Akses Cepat Menu Administrasi</h5>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
        <a href="transaksi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-blue-50 text-brand-500 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-brand-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-brand-500/20">
                <i class="w-7 h-7" data-lucide="shopping-cart"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-brand-500 transition-colors">Transaksi</span>
        </a>
        
        <a href="pembayaran.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-emerald-500/20">
                <i class="w-7 h-7" data-lucide="badge-dollar-sign"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-emerald-600 transition-colors">Pembayaran</span>
        </a>
        
        <a href="jurnal_umum.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-sky-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-sky-500/20">
                <i class="w-7 h-7" data-lucide="book"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-sky-600 transition-colors text-center leading-tight">Jurnal</span>
        </a>
        
        <a href="laporan_laba_rugi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-orange-500/20">
                <i class="w-7 h-7" data-lucide="trending-up"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-orange-600 transition-colors text-center leading-tight">Laba Rugi</span>
        </a>
        
        <a href="cetak_kwitansi.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-rose-500/20">
                <i class="w-7 h-7" data-lucide="file-text"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-rose-600 transition-colors">Invoice</span>
        </a>
        
        <a href="grafik_rating.php" class="flex flex-col items-center group">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300 border border-transparent group-hover:shadow-lg group-hover:shadow-indigo-500/20">
                <i class="w-7 h-7" data-lucide="pie-chart"></i>
            </div>
            <span class="text-sm font-bold text-slate-600 group-hover:text-indigo-600 transition-colors text-center leading-tight">Grafik</span>
        </a>
    </div>
</div>

</div> </main> </div> <script>
    lucide.createIcons();
</script>
</body>
</html>