<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

/**
 * Catatan untuk Database Anda:
 * Berdasarkan struktur yang Anda tunjukkan:
 * - Nama kolom gambar adalah 'Gambar' (G Kapital)
 * - Ada kolom 'nopol' untuk nomor polisi
 * - Tarif menggunakan tipe data decimal
 */
?>

<div class="container mt-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold"><i class="bi bi-car-front-fill text-primary"></i> Daftar Armada Mobil</h2>
            <p class="text-muted">Pilih armada terbaik untuk perjalanan Anda bersama PT INDOMAX</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="mb-2">
                <a href="mobil_tambah.php" class="btn bg-info text-dark fw-bold px-3 shadow-sm btn-sm border-0 rounded-pill">
                    <i class="bi bi-plus-lg"></i> Tambah Mobil
                </a>
            </div>
            <?php 
                // Mengambil total tipe unit dengan query yang efisien
                $sql_count = "SELECT COUNT(*) as total FROM mobil";
                $res_count = mysqli_query($conn, $sql_count);
                $data_count = mysqli_fetch_assoc($res_count);
            ?>
            <span class="badge bg-info p-2 text-dark shadow-sm">
                <i class="bi bi-collection-fill"></i> Total: <?php echo $data_count['total']; ?> Tipe Unit
            </span>
        </div>
    </div>

    <div class="row">
        <?php
        // PERBAIKAN UTAMA: Mengurangi nilai 'Unit_Tersedia' asli dengan jumlah transaksi sewa yang sedang 'berjalan'
        $sql_mobil = "SELECT m.*, 
                      (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime
                      FROM mobil m";
        
        $query = mysqli_query($conn, $sql_mobil);
        
        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)) {
                
                // Ambil nilai perhitungan stok realtime
                $stok_sekarang = (int)$row['stok_realtime'];
                if ($stok_sekarang < 0) { $stok_sekarang = 0; } // Mencegah nilai minus jika ada overload data

                // LOGIKA OTOMATIS: Jika stok > 0 maka 'tersedia', jika habis maka 'booked'
                $status_text = ($stok_sekarang > 0) ? 'Tersedia' : 'Booked';
                $status_class = ($stok_sekarang > 0) ? 'bg-success' : 'bg-danger';
                
                // Menangani Gambar
                $nama_file = $row['Gambar']; 
                $path_gambar = "img/" . $nama_file;

                // Mengantisipasi perbedaan ekstensi (.jpg di database vs .jpeg di folder img/)
                if (!empty($nama_file) && !file_exists($path_gambar)) {
                    $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
                    if ($ext === 'jpg') {
                        $nama_file_alt = str_replace('.jpg', '.jpeg', $nama_file);
                    } elseif ($ext === 'jpeg') {
                        $nama_file_alt = str_replace('.jpeg', '.jpg', $nama_file);
                    } else {
                        $nama_file_alt = $nama_file;
                    }
                    if (file_exists("img/" . $nama_file_alt)) {
                        $nama_file = $nama_file_alt;
                        $path_gambar = "img/" . $nama_file;
                    }
                }
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden hover-card">
                
                <div class="position-relative overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 2;">
                        <span class="badge <?php echo $status_class; ?> shadow-sm px-3 py-2 rounded-pill">
                            <?php echo $status_text; ?>
                        </span>
                    </div>

                    <?php if (!empty($nama_file) && file_exists($path_gambar)): ?>
                        <img src="img/<?php echo $nama_file; ?>" 
                             class="img-fluid w-100 h-100" 
                             style="object-fit: cover;" 
                             alt="<?php echo $row['merk']; ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="text-center text-secondary">
                            <i class="bi bi-image display-1"></i>
                            <p class="small mb-0">Foto tidak tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title fw-bold mb-1"><?php echo $row['merk']; ?></h5>
                            <small class="badge bg-light text-dark border"><?php echo $row['nopol']; ?></small>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag-fill me-1"></i> <?php echo $row['jenis']; ?> • <?php echo $row['kode_mobil']; ?>
                        </p>
                        <div class="text-secondary small d-flex align-items-center">
                            <i class="bi bi-box-seam-fill text-info me-2"></i>
                            <span>Stok Terakhir: <strong><?php echo $stok_sekarang; ?> Unit</strong></span>
                        </div>
                    </div>
                    
                    <div class="mt-auto pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.7rem;">TARIF HARIAN</small>
                                <span class="text-primary fw-bold fs-4">Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?></span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-1">
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <a href="mobil_edit.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-sm btn-outline-warning rounded-circle" title="Edit Data">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="mobil_hapus.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-sm btn-outline-danger rounded-circle me-2" title="Hapus Data" onclick="return confirm('Apakah anda yakin ingin menghapus armada <?php echo $row['merk']; ?> (<?php echo $row['nopol']; ?>) ini?');">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($stok_sekarang > 0): ?>
                                    <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-primary rounded-pill px-4 fw-bold">Sewa</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary rounded-pill px-4" disabled>Booked</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php 
            } 
        } else {
            echo '
            <div class="col-12 text-center my-5 py-5">
                <i class="bi bi-inboxes text-muted display-1"></i>
                <h4 class="mt-3 text-muted">Belum ada armada yang terdaftar di sistem.</h4>
            </div>';
        }
        ?>
    </div>
</div>

</div>
    </div>
</div>

<style>
    .hover-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }
    .hover-card:hover img {
        transform: scale(1.08);
        transition: transform 0.6s ease;
    }
    .card-title {
        color: #2d3436;
        font-size: 1.15rem;
    }
    .bg-info {
        background-color: #e3f2fd !important;
        border: 1px solid #bbdefb;
    }
</style>
</body>
</html>