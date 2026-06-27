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

$search = "";
if (isset($_POST['cari'])) {
    $search = $_POST['keyword'];
} elseif (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
?>

<style>
/* ===== Katalog Premium UI ===== */
.katalog-wrap {
    max-width: 1140px;
    margin: 0 auto;
    padding: 24px 16px 60px;
}
.katalog-hero {
    background: linear-gradient(135deg, #0F172A 0%, #8B0000 60%, #3d0000 100%);
    border-radius: 20px;
    padding: 40px 48px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 32px;
}
.katalog-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -60px;
    width: 320px; height: 320px;
    background: rgba(212,175,55,0.08);
    border-radius: 50%;
}
.katalog-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 15%;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
}
.hero-badge {
    display: inline-block;
    background: #D4AF37;
    color: #1a1a1a;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 6px 16px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
}
.hero-title {
    font-size: 2.2rem;
    font-weight: 800;
    margin: 0 0 8px;
    line-height: 1.2;
}
.hero-sub {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.7);
    max-width: 500px;
    margin: 0;
}

/* Filter bar */
.filter-bar {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 16px;
    padding: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    box-shadow: 0 4px 16px rgba(15,23,42,0.03);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.brand-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.btn-brand {
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #475569;
    background: #F8FAFC;
    border: 1px solid #E8ECF2;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-brand:hover {
    background: rgba(139,0,0,0.05);
    color: #8B0000;
    border-color: rgba(139,0,0,0.2);
}
.btn-brand.active {
    background: #8B0000;
    color: #fff;
    border-color: #8B0000;
    box-shadow: 0 4px 12px rgba(139,0,0,0.2);
}
.search-box {
    display: flex;
    background: #F8FAFC;
    border: 1px solid #E8ECF2;
    border-radius: 50px;
    padding: 4px;
    flex: 1;
    max-width: 320px;
}
.search-box input {
    border: none;
    background: transparent;
    padding: 8px 16px;
    font-size: 0.85rem;
    color: #0F172A;
    width: 100%;
    outline: none;
}
.search-box button {
    background: #0F172A;
    color: #fff;
    border: none;
    width: 36px; height: 36px;
    border-radius: 50px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.search-box button:hover { background: #8B0000; }

/* Car Cards */
.car-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
}
.car-card-modern {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 18px;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}
.car-card-modern:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(15,23,42,0.08);
    border-color: #d1d5db;
}
.car-img-wrap {
    height: 180px;
    position: relative;
    overflow: hidden;
    background: #F8FAFC;
    display: flex; align-items: center; justify-content: center;
}
.car-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.car-card-modern:hover .car-img-wrap img { transform: scale(1.08); }
.car-badge {
    position: absolute;
    top: 12px; left: 12px;
    background: rgba(15,23,42,0.85);
    backdrop-filter: blur(4px);
    color: #fff;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.car-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }
.car-merk { font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0 0 2px; }
.car-price { font-size: 1.05rem; font-weight: 800; color: #8B0000; margin: 0 0 12px; }
.car-price span { font-size: 0.7rem; color: #94A3B8; font-weight: 600; }

.car-amenities {
    display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px;
}
.amenity {
    background: #F1F5F9;
    color: #475569;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 700;
    display: flex; align-items: center; gap: 4px;
}
.amenity.ready { background: rgba(22,163,74,0.1); color: #16A34A; }
.amenity.full { background: rgba(239,68,68,0.1); color: #DC2626; }

.car-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; mt-auto; }
.btn-car-act {
    padding: 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 800;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
    font-family: inherit;
}
.btn-car-act.sewa { background: linear-gradient(135deg, #8B0000, #c0392b); color: #fff; }
.btn-car-act.sewa:hover { box-shadow: 0 4px 12px rgba(139,0,0,0.3); transform: translateY(-1px); }
.btn-car-act.detail { background: #fff; border: 1.5px solid #E8ECF2; color: #475569; }
.btn-car-act.detail:hover { border-color: #0F172A; color: #0F172A; }
.btn-car-act:disabled { background: #E8ECF2; color: #94A3B8; cursor: not-allowed; box-shadow: none; transform: none; }

@media (max-width: 768px) {
    .katalog-hero { padding: 32px 24px; }
    .hero-title { font-size: 1.8rem; }
    .filter-bar { flex-direction: column; align-items: stretch; }
    .search-box { max-width: 100%; }
}
</style>

<div class="katalog-wrap">
    <!-- Hero Banner Section -->
    <div class="katalog-hero">
        <div style="position:relative;z-index:2;">
            <div class="hero-badge"><i class="bi bi-star-fill me-1"></i> Premium Fleet</div>
            <h1 class="hero-title">Pilih Armada Terbaik Anda</h1>
            <p class="hero-sub">Kami menyediakan kendaraan dalam kondisi prima dan terawat demi kenyamanan & keselamatan perjalanan Anda.</p>
        </div>
    </div>

    <!-- Search Bar & Filters -->
    <div class="filter-bar">
        <div class="brand-filters">
            <a href="katalog.php" class="btn-brand <?= empty($brand_filter) ? 'active' : '' ?>">Semua Merk</a>
            <?php
            $brand_query = mysqli_query($conn, "SELECT DISTINCT merk FROM mobil WHERE is_deleted = 0 ORDER BY merk ASC");
            while ($b = mysqli_fetch_array($brand_query)) {
                $b_name = $b['merk'];
                $active_class = ($brand_filter === $b_name) ? 'active' : '';
                echo "<a href='katalog.php?brand=" . urlencode($b_name) . ($search ? "&search=" . urlencode($search) : "") . "' class='btn-brand $active_class'>$b_name</a>";
            }
            ?>
        </div>
        <div class="search-box">
            <input type="text" id="liveSearchInput" placeholder="Ketik merk mobil..." value="<?= htmlspecialchars($search); ?>" onkeyup="liveSearch()">
            <button><i class="bi bi-search"></i></button>
        </div>
    </div>
    
    <script>
        function liveSearch() {
            let input = document.getElementById('liveSearchInput').value.toLowerCase();
            let items = document.querySelectorAll('.car-item');
            let hasVisible = false;
            
            items.forEach(item => {
                let text = item.getAttribute('data-merk');
                if (text.includes(input)) {
                    item.style.display = "block";
                    hasVisible = true;
                } else {
                    item.style.display = "none";
                }
            });
            
            let noResultDiv = document.getElementById('no-result');
            if (noResultDiv) {
                if (!hasVisible) {
                    noResultDiv.style.display = "block";
                } else {
                    noResultDiv.style.display = "none";
                }
            }
        }
    </script>
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
    <div class="car-grid" id="car-list">
        <?php
        $sql = "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m WHERE m.is_deleted = 0";
        $params = [];
        $types = "";
        
        if (!empty($brand_filter)) { 
            $sql .= " AND m.merk = ?"; 
            $params[] = $brand_filter;
            $types .= "s";
        }
        if (!empty($search)) { 
            $sql .= " AND (m.merk LIKE ? OR m.jenis LIKE ?)"; 
            $search_param = "%" . $search . "%";
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "ss";
        }
        
        $stmt = mysqli_prepare($conn, $sql);
        if ($types) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $query = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($query) > 0) {
            while($row = mysqli_fetch_array($query)) {
                $nama_file = $row['Gambar']; 
                $path_gambar = "img/" . $nama_file;
                if (empty($nama_file) || !file_exists($path_gambar)) {
                    $path_gambar = "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=600";
                }
                $stok = max(0, (int)$row['stok_realtime']);
        ?>
        <div class="car-item" data-merk="<?php echo strtolower(htmlspecialchars($row['merk'] . ' ' . $row['jenis'])); ?>">
            <div class="car-card-modern">
                <!-- Image -->
                <div class="car-img-wrap">
                    <span class="car-badge"><?php echo $row['jenis']; ?></span>
                    <img src="<?php echo $path_gambar; ?>" alt="<?php echo $row['merk']; ?>">
                </div>
                <!-- Body -->
                <div class="car-body">
                    <h5 class="car-merk"><?php echo $row['merk']; ?></h5>
                    <p class="car-price">Rp <?php echo number_format($row['tarif_12_dalam'], 0, ',', '.'); ?> <span>/ 12 Jam</span></p>
                    
                    <div class="car-amenities">
                        <span class="amenity"><i class="bi bi-snow2"></i> AC</span>
                        <span class="amenity"><i class="bi bi-disc"></i> Media</span>
                        <?php if ($stok > 0): ?>
                            <span class="amenity ready"><i class="bi bi-check-circle-fill"></i> Ready: <?php echo $stok; ?></span>
                        <?php else: ?>
                            <span class="amenity full"><i class="bi bi-x-circle-fill"></i> Penuh</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="car-actions">
                        <?php if ($stok > 0): ?>
                            <a href="transaksi.php?kode=<?php echo $row['kode_mobil']; ?>" class="btn-car-act sewa">Sewa</a>
                        <?php else: ?>
                            <button class="btn-car-act" disabled>Penuh</button>
                        <?php endif; ?>
                        <button type="button" class="btn-car-act detail" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['kode_mobil']; ?>">Detail</button>
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
                            <tr><td colspan="2" class="text-muted pt-3 pb-1 border-bottom fw-bold"><i class="bi bi-tag-fill me-1"></i> Rincian Tarif Sewa</td></tr>
                            <tr><td class="text-muted py-1">Dalam Kota (12 Jam)</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_12_dalam'], 0, ',', '.'); ?></td></tr>
                            <tr><td class="text-muted py-1">Dalam Kota (24 Jam)</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_24_dalam'], 0, ',', '.'); ?></td></tr>
                            <tr><td class="text-muted py-1">Luar Kota (12 Jam)</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_12_luar'], 0, ',', '.'); ?></td></tr>
                            <tr><td class="text-muted py-1">Luar Kota (24 Jam)</td><td class="fw-bold text-primary py-1">: Rp <?php echo number_format($row['tarif_24_luar'], 0, ',', '.'); ?></td></tr>
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
        
        <div class="col-12 text-center my-5 py-5" id="no-result" style="display: none;">
            <i class="bi bi-search text-muted display-1 d-block mb-3"></i>
            <h5 class="text-muted">Pencarian tidak menemukan hasil.</h5>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>