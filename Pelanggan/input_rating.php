<?php
include 'navbar.php';
include 'koneksi.php';

// Pastikan pelanggan login
if (!isset($_SESSION['id_pelanggan'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='login_pelanggan.php';</script>";
    exit;
}

$id_transaksi = $_GET['id'] ?? ''; // Mengambil ID dari URL
?>

<div class="page-content">
    <div class="card p-4">
        <h4>Berikan Ulasan Layanan</h4>
        <form action="ulasan_rating.php" method="POST">
            <input type="hidden" name="id_transaksi" value="<?php echo htmlspecialchars($id_transaksi); ?>">
            
            <div class="mb-3">
                <label>Rating Pelayanan (1-5)</label>
                <input type="number" name="pely" class="form-control" min="1" max="5" required>
            </div>
            <div class="mb-3">
                <label>Rating Supir (1-5)</label>
                <input type="number" name="supr" class="form-control" min="1" max="5" required>
            </div>
            <div class="mb-3">
                <label>Rating Mobil (1-5)</label>
                <input type="number" name="mobl" class="form-control" min="1" max="5" required>
            </div>
            <div class="mb-3">
                <label>Ulasan Anda</label>
                <textarea name="ulasan" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
        </form>
    </div>
</div>