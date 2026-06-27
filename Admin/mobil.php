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
$res_tipe = mysqli_query($conn, "SELECT COUNT(*) as total FROM mobil WHERE is_deleted = 0");
$data_tipe = mysqli_fetch_assoc($res_tipe);
$total_tipe = $data_tipe['total'] ?? 0;

// 2. Total Semua Kapasitas Unit Fisik Mobil (Kapasitas Maksimal)
$res_stok = mysqli_query($conn, "SELECT SUM(Unit_Tersedia) as total_stok FROM mobil WHERE is_deleted = 0");
$data_stok = mysqli_fetch_assoc($res_stok);
$total_stok = $data_stok['total_stok'] ?? 0;

// 3. Total Unit yang Sedang Dipakai (Status Sewa 'berjalan')
$res_pakai = mysqli_query($conn, "SELECT COUNT(*) as total_pakai FROM transaksi_sewa WHERE status_sewa = 'berjalan'");
$data_pakai = mysqli_fetch_assoc($res_pakai);
$total_pakai = $data_pakai['total_pakai'] ?? 0;

// 4. Total Unit yang Tersedia / Sisa Siap Sewa (Total Stok - Total Dipakai)
$total_tersedia = $total_stok - $total_pakai;
if ($total_tersedia < 0) { $total_tersedia = 0; } // Pengaman nilai minus

// 5. Data Unit Terpakai Detail
$query_terpakai = "SELECT t.id_sewa, m.merk, m.nopol, p.nama, t.tanggal_sewa, t.lama_sewa 
                   FROM transaksi_sewa t 
                   JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                   JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                   WHERE t.status_sewa = 'berjalan' ORDER BY t.tanggal_sewa DESC";
$res_detail_pakai = mysqli_query($conn, $query_terpakai);
// =========================================================================
?>

<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Daftar Armada Mobil</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Kelola ketersediaan dan detail kendaraan operasional.</p>
    </div>
    
    <div class="flex flex-wrap items-center justify-end gap-3 w-full lg:w-auto">
        <div class="relative w-full sm:w-64 mb-2 lg:mb-0">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari mobil..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#800000] focus:ring-1 focus:ring-[#800000]">
        </div>
        <span class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-blue-100 flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4"></i> <?php echo $total_tipe; ?> Tipe Mobil
        </span>
        
        <span class="bg-emerald-50 text-emerald-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-emerald-100 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> <?php echo $total_tersedia; ?> Unit Tersedia
        </span>
        
        <button data-bs-toggle="modal" data-bs-target="#modalUnitTerpakai" class="bg-amber-50 text-amber-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-amber-100 flex items-center gap-2 hover:bg-amber-100 transition-colors">
            <i data-lucide="navigation" class="w-4 h-4"></i> <?php echo $total_pakai; ?> Unit Dipakai (Detail)
        </button>
        
        <a href="mobil_tambah.php" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-600/20 hover:bg-blue-700 transition-all flex items-center gap-2 lg:ml-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Mobil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-10">
    <?php
    // Query untuk mengambil data per mobil, menghitung stok real-time, dan cek jadwal pemeliharaan terdekat
    $sql_mobil = "SELECT m.*, 
                  (CAST(m.Unit_Tersedia AS SIGNED) - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime,
                  (SELECT tanggal_pemeliharaan FROM pemeliharaan p WHERE p.kode_mobil = m.kode_mobil AND p.status = 'terjadwal' ORDER BY tanggal_pemeliharaan ASC LIMIT 1) AS jadwal_servis
                  FROM mobil m WHERE m.is_deleted = 0";
    
    $query = mysqli_query($conn, $sql_mobil);
    
    if (mysqli_num_rows($query) > 0) {
        while($row = mysqli_fetch_array($query)) {
            $stok_sekarang = (int)$row['stok_realtime'];
            if ($stok_sekarang < 0) { $stok_sekarang = 0; } 

            $status_text = ($stok_sekarang > 0) ? 'Tersedia' : 'Booked';
            $status_badge = ($stok_sekarang > 0) ? 'bg-pastel-green-bg text-pastel-green-text' : 'bg-pastel-red-bg text-pastel-red-text';
            
            $jadwal_servis = $row['jadwal_servis'];
            $is_need_service = !empty($jadwal_servis);
            
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
    
    <div class="car-card bg-white rounded-[20px] overflow-hidden hover-lift group flex flex-col h-full border border-slate-200 shadow-sm">
        
        <div class="relative h-56 w-full overflow-hidden bg-white border-b border-slate-100">
            <div class="absolute top-4 right-4 z-10 flex flex-col gap-2 items-end">
                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm <?php echo $status_badge; ?>">
                    <?php echo $status_text; ?>
                </span>
                <?php if($is_need_service): ?>
                <span class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm bg-orange-100 text-orange-600 flex items-center gap-1" title="Jadwal Servis">
                    <i data-lucide="wrench" class="w-3 h-3"></i> <?= date('d M Y', strtotime($jadwal_servis)) ?>
                </span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($nama_file) && file_exists($path_gambar)): ?>
                <img src="img/<?php echo $nama_file; ?>" 
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" 
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
            
            <div class="mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="package" class="w-4 h-4"></i></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Sisa Stok</p>
                        <p class="text-sm font-bold text-slate-800"><?php echo $stok_sekarang; ?> Unit</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase block mb-0.5">Mulai Dari</span>
                    <span class="text-lg font-extrabold text-blue-600">Rp <?php echo number_format($row['tarif_12_dalam'], 0, ',', '.'); ?></span>
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

<div class="modal fade" id="modalUnitTerpakai" tabindex="-1" aria-labelledby="modalUnitTerpakaiLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-amber-50 border-0 py-3">
        <h5 class="modal-title fw-bold text-amber-600 flex items-center gap-2" id="modalUnitTerpakaiLabel">
            <i data-lucide="navigation" class="w-5 h-5"></i> Daftar Unit Terpakai
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No. Transaksi</th>
                        <th>Mobil (Nopol)</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Sewa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($res_detail_pakai) > 0) {
                        while($row_pakai = mysqli_fetch_assoc($res_detail_pakai)) {
                            echo "<tr>";
                            echo "<td class='ps-4'><span class='badge bg-indigo-100 text-indigo-700 rounded-pill'>TRX-" . $row_pakai['id_sewa'] . "</span></td>";
                            echo "<td class='fw-bold text-slate-800'>" . htmlspecialchars($row_pakai['merk']) . " <span class='text-slate-500 fw-normal'>(" . htmlspecialchars($row_pakai['nopol']) . ")</span></td>";
                            echo "<td><i class='bi bi-person text-slate-400 me-1'></i> " . htmlspecialchars($row_pakai['nama']) . "</td>";
                            
                            $tgl_kembali = date('d M Y', strtotime($row_pakai['tanggal_sewa'] . ' + ' . $row_pakai['lama_sewa'] . ' days'));
                            echo "<td><i class='bi bi-calendar text-slate-400 me-1'></i> " . date('d M Y', strtotime($row_pakai['tanggal_sewa'])) . " - " . $tgl_kembali . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center py-5 text-muted'><i class='bi bi-info-circle mb-2 d-block fs-3'></i>Tidak ada mobil yang sedang dipakai saat ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer border-0 bg-slate-50">
        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
        <a href="transaksi.php" class="btn btn-primary rounded-pill px-4">Kelola Transaksi</a>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function liveSearch() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toUpperCase();
        const cards = document.getElementsByClassName("car-card");

        for (let i = 0; i < cards.length; i++) {
            const textContent = cards[i].textContent || cards[i].innerText;
            if (textContent.toUpperCase().indexOf(filter) > -1) {
                cards[i].parentElement.style.display = ""; // Assuming cards might be wrapped, actually cards themselves can be hidden
                cards[i].style.display = "flex";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>