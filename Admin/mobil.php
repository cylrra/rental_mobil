<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// =========================================================================
// PERHITUNGAN STATISTIK GLOBAL (REAL-TIME)
// =========================================================================

// 1. Total Tipe Unit Mobil
$res_tipe = mysqli_query($conn, "SELECT COUNT(*) as total FROM mobil");
$data_tipe = mysqli_fetch_assoc($res_tipe);
$total_tipe = $data_tipe['total'] ?? 0;

// 2. Total Semua Kapasitas Unit Fisik Mobil (Kapasitas Maksimal)
$res_stok = mysqli_query($conn, "SELECT SUM(Unit_Tersedia) as total_stok FROM mobil");
$data_stok = mysqli_fetch_assoc($res_stok);
$total_stok = $data_stok['total_stok'] ?? 0;

// 3. Total Unit yang Sedang Dipakai (Status Sewa 'berjalan')
$res_pakai = mysqli_query($conn, "SELECT COUNT(*) as total_pakai FROM transaksi_sewa WHERE status_sewa = 'berjalan'");
$data_pakai = mysqli_fetch_assoc($res_pakai);
$total_pakai = $data_pakai['total_pakai'] ?? 0;

// 4. Total Unit yang Tersedia / Sisa Siap Sewa (Total Stok - Total Dipakai)
$total_tersedia = $total_stok - $total_pakai;
if ($total_tersedia < 0) { $total_tersedia = 0; } // Pengaman nilai minus
// =========================================================================
?>

<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Armada Mobil</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Kelola ketersediaan dan detail kendaraan operasional.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <span class="bg-blue-50 text-brand-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-blue-100 flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4"></i> <?php echo $total_tipe; ?> Tipe Mobil
        </span>
        
        <span class="bg-emerald-50 text-emerald-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-emerald-100 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> <?php echo $total_tersedia; ?> Unit Tersedia
        </span>
        
        <span class="bg-amber-50 text-amber-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-amber-100 flex items-center gap-2">
            <i data-lucide="navigation" class="w-4 h-4"></i> <?php echo $total_pakai; ?> Unit Dipakai
        </span>
        
        <a href="mobil_tambah.php" class="bg-brand-500 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-brand-500/20 hover:bg-brand-600 transition-all flex items-center gap-2 lg:ml-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Mobil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-10">
    <?php
    // Query untuk mengambil data per mobil dan menghitung stok real-time masing-masing tipe
    $sql_mobil = "SELECT m.*, 
                  (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime
                  FROM mobil m";
    
    $query = mysqli_query($conn, $sql_mobil);
    
    if (mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_array($query)) {
            $stok_sekarang = (int)$row['stok_realtime'];
            if ($stok_sekarang < 0) { $stok_sekarang = 0; } 

            $status_text = ($stok_sekarang > 0) ? 'Tersedia' : 'Booked';
            $status_badge = ($stok_sekarang > 0) ? 'bg-pastel-green-bg text-pastel-green-text' : 'bg-pastel-red-bg text-pastel-red-text';
            
            // Penanganan Pencarian File Gambar
            $nama_file = $row['Gambar']; 
            $path_gambar = "img/" . $nama_file;
            if (!empty($nama_file) && !file_exists($path_gambar)) {
                $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
                $nama_file_alt = ($ext === 'jpg') ? str_replace('.jpg', '.jpeg', $nama_file) : (($ext === 'jpeg') ? str_replace('.jpeg', '.jpg', $nama_file) : $nama_file);
                if (file_exists("img/" . $nama_file_alt)) {
                    $nama_file = $nama_file_alt;
                    $path_gambar = "img/" . $nama_file;
                }
            }
    ?>
    
    <div class="glass-card rounded-[20px] overflow-hidden hover-lift group flex flex-col h-full border border-slate-200">
        
        <div class="relative aspect-video w-full overflow-hidden bg-slate-100">
            <div class="absolute top-4 right-4 z-10">
                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm <?php echo $status_badge; ?>">
                    <?php echo $status_text; ?>
                </span>
            </div>
            
            <?php if (!empty($nama_file) && file_exists($path_gambar)): ?>
                <img src="img/<?php echo $nama_file; ?>" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                alt="<?php echo htmlspecialchars($row['merk']); ?>" 
                loading="lazy">
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full text-slate-400">
                    <i data-lucide="image" class="w-12 h-12 mb-2 opacity-50"></i>
                    <span class="text-xs font-medium">Foto tidak tersedia</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-6 flex flex-col flex-1">
            <div class="flex justify-between items-start mb-2">
                <h5 class="text-xl font-bold text-slate-800 leading-tight"><?php echo htmlspecialchars($row['merk']); ?></h5>
                <span class="bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold px-2 py-1 rounded-lg">
                    <?php echo htmlspecialchars($row['nopol']); ?>
                </span>
            </div>
            
            <p class="text-xs font-medium text-slate-500 flex items-center gap-1.5 mb-4">
                <i data-lucide="tags" class="w-3.5 h-3.5"></i> <?php echo htmlspecialchars($row['jenis']); ?> • <?php echo htmlspecialchars($row['kode_mobil']); ?>
            </p>
            
            <div class="mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-brand-500 flex items-center justify-center"><i data-lucide="package" class="w-4 h-4"></i></div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Sisa Stok Tipe Ini</p>
                    <p class="text-sm font-bold text-slate-700"><?php echo $stok_sekarang; ?> Unit</p>
                </div>
            </div>
            
            <div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5">Tarif Harian</span>
                    <span class="text-lg font-extrabold text-brand-500">Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="mobil_edit.php?kode=<?php echo $row['kode_mobil']; ?>" class="w-9 h-9 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-colors" title="Edit">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                        </a>
                        <a href="mobil_hapus.php?kode=<?php echo $row['kode_mobil']; ?>" class="w-9 h-9 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors" title="Hapus" onclick="return confirm('Yakin ingin menghapus <?php echo $row['merk']; ?>?');">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
        } 
    } else {
        echo '
        <div class="col-span-full text-center py-20">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full mb-4">
                <i data-lucide="inbox" class="w-10 h-10 text-slate-400"></i>
            </div>
            <h4 class="text-lg font-bold text-slate-700">Belum ada armada terdaftar.</h4>
            <p class="text-sm text-slate-500 mt-1">Silakan tambahkan data mobil pertama Anda.</p>
        </div>';
    }
    ?>
</div>

        </div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>