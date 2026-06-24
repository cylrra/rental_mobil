<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pengguna dengan role 'pelanggan' yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Logika Fitur Pencarian & Filter Brand
$search = "";
if (isset($_POST['cari'])) {
    $search = mysqli_real_escape_string($conn, $_POST['keyword']);
} elseif (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$brand_filter = isset($_GET['brand']) ? mysqli_real_escape_string($conn, $_GET['brand']) : '';
?>

<div class="container-fluid px-4">
    <!-- Hero Banner Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 overflow-hidden shadow-sm text-white" style="background: linear-gradient(135deg, #0f172a 60%, #9e0000 100%); min-height: 180px;">
                <div class="card-body p-5 d-flex align-items-center position-relative">
                    <div style="position: absolute; right: -30px; top: -30px; width: 200px; height: 200px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                    <div>
                        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Pilih Armada Terbaik Anda</h1>
                        <p class="mb-0 opacity-75">Kami menyediakan kendaraan dalam kondisi prima dan terawat demi keselamatan perjalanan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar & Filters -->
    <div class="row mb-4 justify-content-between align-items-center g-3">
        <!-- Brand Filters (Tombol filter kategori mobil) -->
        <div class="col-lg-8">
            <div class="d-flex flex-wrap gap-2">
                <a href="katalog.php" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold <?= empty($brand_filter) ? 'btn-primary' : 'btn-outline-dark bg-white border' ?>">
                    Semua Merk
                </a>
                <?php
                // Ambil daftar brand unik secara dinamis dari database
                $brand_query = mysqli_query($conn, "SELECT DISTINCT merk FROM mobil ORDER BY merk ASC");
                while ($b = mysqli_fetch_array($brand_query)) {
                    $b_name = $b['merk'];
                    $active_class = ($brand_filter === $b_name) ? 'btn-primary' : 'btn-outline-dark bg-white border';
                    echo "<a href='katalog.php?brand=" . urlencode($b_name) . ($search ? "&search=" . urlencode($search) : "") . "' class='btn btn-sm rounded-pill px-3 py-2 fw-semibold $active_class'>$b_name</a>";
                }
                ?>
            </div>
        </div>
        <!-- Search Input -->
        <div class="col-lg-4">
            <form action="katalog.php<?= $brand_filter ? "?brand=" . urlencode($brand_filter) : "" ?>" method="POST" class="shadow-sm rounded-pill overflow-hidden border bg-white p-1 d-flex">
                <input type="text" name="keyword" class="form-control border-0 px-3 py-2" 
                       placeholder="Cari nama mobil..." 
                       value="<?= htmlspecialchars($search); ?>">
                <button type="submit" name="cari" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Active Search Reset info -->
    <?php if (!empty($search) || !empty($brand_filter)): ?>
        <div class="mb-4 text-muted small">
            Menampilkan hasil filter: 
            <?php if ($brand_filter) echo "Merk <strong>\"" . htmlspecialchars($brand_filter) . "\"</strong> "; ?>
            <?php if ($search) echo ($brand_filter ? "dan " : "") . "Kata kunci <strong>\"" . htmlspecialchars($search) . "\"</strong> "; ?>
            | <a href="katalog.php" class="text-decoration-none text-danger fw-bold">Reset Semua</a>
        </div>
    <?php endif; ?>

    <!-- Cars Card Grid -->
    <div class="row">
        <?php
        // Query dinamis dengan filter brand & keyword
        $sql = "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m WHERE 1=1";
        
        if (!empty($brand_filter)) {
            $sql .= " AND m.merk = '$brand_filter'";
        }
        if (!empty($search)) {
            $sql .= " AND (m.merk LIKE '%$search%' OR m.jenis LIKE '%$search%')";
        }
        
        $query = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)) {
                $nama_file = $row['Gambar']; 
                $path_gambar = "img/" . $nama_file;
                
                // Fallback gambar
                if (empty($nama_file) || !file_exists($path_gambar)) {
                    $path_gambar = "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=600";
                }
                
                $stok = max(0, (int)$row['stok_realtime']);
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-katalog transition-all">
                <!-- Cover Image & Badge -->
                <div class="position-relative bg-light overflow-hidden d-flex align-items-center justify-content-center" style="height: 180px;">
                    <img src="<?php echo $path_gambar; ?>" class="img-fluid w-100 h-100 img-zoom" style="object-fit: cover;" alt="<?php echo $row['merk']; ?>">
                    <span class="position-absolute top-0 start-0 m-3 badge bg-dark opacity-75 rounded-pill px-2 py-1-5 small text-uppercase">
                        <?php echo $row['jenis']; ?>
                    </span>
                </div>

                <!-- Body Details -->
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="fw-bold text-dark mb-1"><?php echo $row['merk']; ?></h5>
                    <p class="text-primary fw-bold mb-3" style="font-size: 1.15rem;">
                        Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?> <span class="text-muted fw-normal" style="font-size: 0.8rem;">/ Hari</span>
                    </p>
                    
                    <!-- Amenities -->
                    <div class="d-flex gap-1 mb-4 flex-wrap">
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small" style="font-size: 0.7rem;"><i class="bi bi-snow2 text-info me-1"></i> AC</span>
                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small" style="font-size: 0.7rem;"><i class="bi bi-disc text-primary me-1"></i> Media</span>
                        <?php if ($stok > 0): ?>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small" style="font-size: 0.7rem;"><i class="bi bi-check-circle-fill me-1"></i> Ready: <?php echo $stok; ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small" style="font-size: 0.7rem;"><i class="bi bi-x-circle-fill me-1"></i> Penuh</span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto row g-2">
                        <div class="col-6">
                            <?php if ($stok > 0): ?>
                                <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-sm btn-primary w-100 fw-bold rounded-3 py-2">Sewa</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-light border text-muted w-100 fw-bold rounded-3 py-2" disabled>Penuh</button>
                            <?php endif; ?>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-sm btn-outline-dark w-100 rounded-3 py-2" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['kode_mobil']; ?>">Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="modalDetail<?php echo $row['kode_mobil']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold text-dark">Spesifikasi <?php echo $row['merk']; ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <img src="<?php echo $path_gambar; ?>" class="img-fluid rounded-3 mb-3 w-100" style="max-height: 220px; object-fit: cover;">
                        <table class="table table-sm table-borderless text-start align-middle">
                            <tr><td class="text-muted py-1">Kode Mobil</td><td class="fw-bold py-1">: <?php echo $row['kode_mobil']; ?></td></tr>
                            <tr><td class="text-muted py-1">Nomor Polisi</td><td class="fw-bold py-1">: <span class="badge bg-light text-dark border"><?php echo $row['nopol']; ?></span></td></tr>
                            <tr><td class="text-muted py-1">Jenis Kategori</td><td class="fw-bold py-1">: <?php echo $row['jenis']; ?></td></tr>
                            <tr><td class="text-muted py-1">Stok Ready</td><td class="fw-bold py-1">: <?php echo $stok; ?> Unit</td></tr>
                            <tr><td class="text-muted py-1">Biaya Harian</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_per_hari'], 0, ',', '.'); ?> / Hari</td></tr>
                        </table>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                        <?php if ($stok > 0): ?>
                            <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn btn-primary rounded-3 fw-bold px-4">Sewa Sekarang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            } 
        } else {
            echo '<div class="col-12 text-center my-5 py-5">
                    <i class="bi bi-exclamation-circle text-muted display-1 d-block mb-3"></i>
                    <h5 class="text-muted">Maaf, mobil tidak ditemukan untuk filter saat ini.</h5>
                    <a href="katalog.php" class="btn btn-sm btn-primary mt-2">Reset Filter</a>
                  </div>';
        }
        ?>
    </div>
</div>

<style>
    .py-1-5 { padding-top: 0.4rem; padding-bottom: 0.4rem; }
    .card-katalog {
        background-color: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.05) !important;
    }
    .card-katalog:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08) !important;
    }
    .card-katalog:hover .img-zoom {
        transform: scale(1.06);
    }
    .img-zoom {
        transition: transform 0.5s ease;
    }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>