<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// Logika Fitur Pencarian / Search Bar
$search = "";
if (isset($_POST['cari'])) {
    $search = mysqli_real_escape_string($conn, $_POST['keyword']);
}
?>

<!-- 1. BANNER HERO SECTION -->
<div class="container-fluid bg-warning text-dark p-5 mb-4 position-relative overflow-hidden rounded-4 shadow-sm" style="background: linear-gradient(135deg, #ffc107 60%, #e0a800 100%); min-height: 220px;">
    <div class="row align-items-center py-2 px-3">
        <!-- Sisi Kiri: Teks Promosi -->
        <div class="col-md-7 z-1">
            <h1 class="display-4 fw-black mb-2" style="letter-spacing: -1px; font-weight: 900;">Cari Rental Mobil?</h1>
            <h2 class="fw-bold text-dark opacity-90 mb-3">Yuk Rental Disini Aja!</h2>
            <p class="lead mb-0 text-dark opacity-75 fs-6">Dapatkan penawaran harga terbaik untuk armada premium PT INDOMAX.</p>
        </div>
        
        <!-- Sisi Kanan: Menampilkan Gambar Rental.jpg -->
        <div class="col-md-5 d-none d-md-block text-end position-absolute end-0 top-50 translate-middle-y me-5" style="max-height: 100%;">
            <!-- Efek multiply digunakan agar background putih pada Rental.jpg menyatu dengan warna kuning banner -->
            <img src="img/Rental.jpg" alt="Armada Indomax" class="img-fluid rounded-3" style="max-height: 180px; object-fit: contain; mix-blend-mode: multiply;">
        </div>
    </div>
</div>

<div class="container mb-5">
    
    <!-- 2. SEARCH BAR SECTION (Fitur Cari Mobil) -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-8 col-lg-6">
            <form action="" method="POST" class="shadow-sm rounded-pill overflow-hidden border bg-white p-1 d-flex">
                <input type="text" name="keyword" class="form-control border-0 px-4 py-2-5" 
                       placeholder="Cari mobil disini (misal: Avanza, Toyota)..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" name="cari" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm ms-1">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </form>
            <?php if (!empty($search)): ?>
                <div class="text-center mt-2 small text-muted">
                    Menampilkan hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search); ?>"</strong> 
                    | <a href="?" class="text-decoration-none">Reset</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. GRID CARD ARMAHA MOBIL -->
    <div class="row">
        <?php
        // Query dinamis: mengambil data mobil yang 'tersedia' dan mencocokkan dengan keyword pencarian
        $sql = "SELECT * FROM mobil WHERE status_mobil = 'tersedia'";
        if (!empty($search)) {
            $sql .= " AND (merk LIKE '%$search%' OR jenis LIKE '%$search%')";
        }
        
        $query = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)) {
                // Konfigurasi file gambar bawaan
                $nama_file = $row['Gambar']; 
                $path_gambar = "img/" . $nama_file;
                
                // Fallback jika file gambar tidak ditemukan di folder lokal
                if (empty($nama_file) || !file_exists($path_gambar)) {
                    $path_gambar = "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=600";
                }
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-katalog transition-all">
                
                <!-- Gambar Mobil dengan Badge Brand Atas -->
                <div class="position-relative bg-light overflow-hidden d-flex align-items-center justify-content-center" style="height: 180px;">
                    <img src="<?php echo $path_gambar; ?>" class="img-fluid w-100 h-100 img-zoom" style="object-fit: cover;" alt="<?php echo $row['merk']; ?>">
                    <span class="position-absolute top-0 start-0 m-3 badge bg-dark opacity-75 rounded-pill px-2 py-1 small">
                        <?php echo strtoupper($row['jenis']); ?>
                    </span>
                </div>

                <!-- Detail Deskripsi Mobil -->
                <div class="card-body d-flex flex-column text-center p-3">
                    <h5 class="fw-bold text-dark mb-1"><?php echo $row['merk']; ?></h5>
                    <p class="text-primary fw-bold mb-2 small" style="font-size: 1.1rem;">
                        Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?> <span class="text-muted fw-normal" style="font-size: 0.8rem;">/ Hari</span>
                    </p>
                    
                    <!-- Fitur Fasilitas Mobil (Badge Hijau seperti Gambar) -->
                    <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                        <span class="badge bg-success opacity-90 rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="bi bi-snow2"></i> AC</span>
                        <span class="badge bg-success opacity-90 rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="bi bi-disc"></i> Media Player</span>
                        <span class="badge bg-info text-dark rounded-pill px-2 py-1" style="font-size: 0.7rem;"><i class="bi bi-box-seam-fill"></i> Stok: <?php echo $row['Unit_Tersedia']; ?></span>
                    </div>

                    <!-- Tombol Aksi Bawah Card -->
                    <div class="mt-auto row g-1">
                        <div class="col-6">
                            <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-sm btn-primary w-100 fw-bold rounded-3">Rental</a>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-secondary w-100 text-white rounded-3" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['kode_mobil']; ?>">Detail</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL POPUP: Detail Spesifikasi Armada -->
        <div class="modal fade" id="modalDetail<?php echo $row['kode_mobil']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold text-dark">Spesifikasi <?php echo $row['merk']; ?></h5>
                        <button type="button" class="btn-close" data-bs-shadow="none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <img src="<?php echo $path_gambar; ?>" class="img-fluid rounded-3 mb-3 w-100" style="max-height: 220px; object-fit: cover;">
                        <table class="table table-sm table-borderless text-start align-middle">
                            <tr><td class="text-muted py-1">Kode Mobil</td><td class="fw-bold py-1">: <?php echo $row['kode_mobil']; ?></td></tr>
                            <tr><td class="text-muted py-1">Nomor Polisi</td><td class="fw-bold py-1">: <span class="badge bg-light text-dark border"><?php echo $row['nopol']; ?></span></td></tr>
                            <tr><td class="text-muted py-1">Kategori Jenis</td><td class="fw-bold py-1">: <?php echo $row['jenis']; ?></td></tr>
                            <tr><td class="text-muted py-1">Unit Tersedia</td><td class="fw-bold py-1">: <?php echo $row['Unit_Tersedia']; ?> Unit</td></tr>
                            <tr><td class="text-muted py-1">Biaya Sewa</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?> / Hari</td></tr>
                        </table>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-primary rounded-3 fw-bold px-4">Sewa Sekarang</a>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            } 
        } else {
            echo '<div class="col-12 text-center my-5 py-4">
                    <i class="bi bi-exclamation-circle text-muted display-4"></i>
                    <h5 class="mt-3 text-muted">Maaf, armada mobil yang Anda cari tidak tersedia atau sedang disewa.</h5>
                  </div>';
        }
        ?>
    </div>
</div>

<!-- 4. AKHIR WRAPPER SISTEM SIDEBAR (Penutup Komponen Layout) -->
        </div> <!-- Menutup container-fluid p-4 -->
    </div> <!-- Menutup page-content-wrapper -->
</div> <!-- Menutup wrapper utama -->

<style>
    /* Styling khusus agar persis seperti Gambar 6c5fdf.png */
    .fw-black { font-weight: 900; }
    .opacity-90 { opacity: 0.9; }
    .py-2-5 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
    .transition-all { transition: all 0.25s ease-in-out; }
    
    .card-katalog {
        background-color: #ffffff;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .card-katalog:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .card-katalog:hover .img-zoom {
        transform: scale(1.06);
    }
    .img-zoom {
        transition: transform 0.5s ease;
    }
</style>
</body>
</html>