<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'koneksi.php';
include 'navbar.php';

$id_transaksi = isset($_GET['id_transaksi']) ? mysqli_real_escape_string($conn, $_GET['id_transaksi']) : '';
$id_pelanggan = $_SESSION['id_pelanggan'];

if (empty($id_transaksi)) {
    die("<div class='container my-5 text-center'><h3>Order ID tidak valid.</h3></div>");
}

// Ambil info transaksi untuk check status & check driver
$query_t = mysqli_query($conn, "SELECT t.*, m.merk FROM transaksi_sewa t JOIN mobil m ON t.kode_mobil = m.kode_mobil WHERE t.id_sewa = '$id_transaksi' AND t.id_pelanggan = '$id_pelanggan'");
$trans_data = mysqli_fetch_assoc($query_t);

if (!$trans_data) {
    die("<div class='container my-5 text-center'><h3>Pemesanan tidak ditemukan.</h3></div>");
}

if ($trans_data['status_sewa'] !== 'selesai') {
    die("<div class='container my-5 text-center'><h3>Rating hanya bisa diberikan jika mobil sudah dikembalikan (Status Selesai).</h3></div>");
}

$pakai_supir = !empty($trans_data['id_supir']);

if (isset($_POST['kirim_rating'])) {
    $pely  = intval($_POST['rating_pelayanan']);
    $supr  = $pakai_supir ? intval($_POST['rating_supir']) : 5; // Default 5 jika lepas kunci
    $mobl  = intval($_POST['rating_mobil']);
    $txt   = mysqli_real_escape_string($conn, $_POST['ulasan']);

    $insert = mysqli_query($conn, "INSERT INTO rating_sewa (id_transaksi, id_pelanggan, rating_pelayanan, rating_supir, rating_mobil, ulasan) 
                                      VALUES ('$id_transaksi', '$id_pelanggan', '$pely', '$supr', '$mobl', '$txt')");
    if ($insert) {
        echo "<script>alert('Terima kasih atas penilaian dan ulasan Anda!'); window.location='riwayat_pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirimkan rating: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 my-4">
            <div class="card shadow-sm border-0 rounded-4 bg-white">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h4 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0f172a;"><i class="bi bi-star-fill text-warning me-2"></i> Berikan Penilaian Anda</h4>
                    <p class="text-muted small">Bantu kami meningkatkan layanan dengan memberikan ulasan sewa mobil **<?= htmlspecialchars($trans_data['merk']) ?>** (Order #SRV-<?= $id_transaksi ?>).</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="" method="POST">
                        <p class="text-muted small mb-4">Skala Penilaian: Bintang 1 (Sangat Buruk) hingga Bintang 5 (Sangat Puas)</p>
                        
                        <!-- 1. Pelayanan Rating -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary d-block">Pelayanan Admin & Staff Kantor</label>
                            <div class="d-flex gap-2 rating-group">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <input type="radio" name="rating_pelayanan" id="pely_<?= $i ?>" value="<?= $i ?>" class="btn-check" required>
                                    <label class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold" for="pely_<?= $i ?>"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 2. Driver Rating (Only if driver was used) -->
                        <?php if ($pakai_supir): ?>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary d-block">Kinerja & Keramahan Sopir</label>
                                <div class="d-flex gap-2 rating-group">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <input type="radio" name="rating_supir" id="supr_<?= $i ?>" value="<?= $i ?>" class="btn-check" required>
                                        <label class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold" for="supr_<?= $i ?>"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- 3. Car Condition Rating -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary d-block">Kondisi Fisik & Kebersihan Mobil</label>
                            <div class="d-flex gap-2 rating-group">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <input type="radio" name="rating_mobil" id="mobl_<?= $i ?>" value="<?= $i ?>" class="btn-check" required>
                                    <label class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold" for="mobl_<?= $i ?>"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 4. Text Review Comments -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Ulasan & Masukan Tambahan</label>
                            <textarea name="ulasan" rows="4" class="form-control rounded-4" placeholder="Tuliskan saran atau komentar Anda mengenai pelayanan kami..." required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="kirim_rating" class="btn btn-primary btn-lg py-2-5 rounded-3 fw-bold fs-6">
                                <i class="bi bi-send-fill me-2"></i> Kirim Penilaian
                            </button>
                            <a href="riwayat_pembayaran.php" class="btn btn-link text-decoration-none text-muted small">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .py-2-5 { padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .btn-check:checked + .btn {
        background-color: #0f172a !important;
        color: white !important;
        border-color: #0f172a !important;
    }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>