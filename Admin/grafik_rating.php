<?php
session_start();
include 'koneksi.php';

// --- BAGIAN 1: PROSES INSERT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_submit'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $pely = intval($_POST['pely']);
    $supr = intval($_POST['supr']);
    $mobl = intval($_POST['mobl']);
    $ulasan = mysqli_real_escape_string($conn, $_POST['ulasan']);

    $query_insert = "INSERT INTO rating_sewa (id_transaksi, id_pelanggan, rating_pelayanan, rating_supir, rating_mobil, ulasan) 
                     VALUES ('$id_transaksi', '$id_pelanggan', '$pely', '$supr', '$mobl', '$ulasan')";
    
    if (mysqli_query($conn, $query_insert)) {
        echo "<script>alert('Ulasan berhasil dikirim!'); window.location='grafik_rating.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// --- BAGIAN 2: DATA TAMPILAN ---
include 'navbar.php';

// 1. Hitung Statistik Rata-rata
$query_avg = mysqli_query($conn, "SELECT 
    AVG(rating_pelayanan) as avg_pely, 
    AVG(rating_supir) as avg_sup, 
    AVG(rating_mobil) as avg_mobl,
    COUNT(id_rating) as total_ulasan
    FROM rating_sewa");
$data_avg = mysqli_fetch_assoc($query_avg);

// 2. Ambil Daftar Ulasan (DIPERBAIKI: p.nama sesuai struktur database Anda)
$query_ulasan = mysqli_query($conn, "SELECT r.*, p.nama FROM rating_sewa r 
                                     JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                                     ORDER BY r.tgl_rating DESC");
?>

<div class="container-fluid px-4 mt-4">
    <h2 class="fw-bold mb-4">Ulasan & Rating Layanan</h2>
    
    <div class="card shadow-sm border-0 p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <h1 class="display-3 fw-bold"><?= number_format((($data_avg['avg_pely'] ?? 0) + ($data_avg['avg_sup'] ?? 0) + ($data_avg['avg_mobl'] ?? 0))/3, 1) ?></h1>
                <p class="text-muted"><?= $data_avg['total_ulasan'] ?> Ulasan Pelanggan</p>
            </div>
            <div class="col-md-9">
                <p>Pelayanan Kantor: <b><?= number_format($data_avg['avg_pely'] ?? 0, 1) ?> / 5.0</b></p>
                <p>Keramahan Sopir: <b><?= number_format($data_avg['avg_sup'] ?? 0, 1) ?> / 5.0</b></p>
                <p>Kondisi Armada: <b><?= number_format($data_avg['avg_mobl'] ?? 0, 1) ?> / 5.0</b></p>
            </div>
        </div>
    </div>

    <?php
    // Pastikan nama tabel transaksi sesuai, jika bukan 'transaksi' ganti ke 'transaksi_sewa'
    $cek_transaksi = mysqli_query($conn, "SELECT id_transaksi FROM transaksi WHERE id_pelanggan = '".$_SESSION['id_pelanggan']."' AND status = 'selesai' AND id_transaksi NOT IN (SELECT id_transaksi FROM rating_sewa)");
    if($cek_transaksi && mysqli_num_rows($cek_transaksi) > 0) {
        $t = mysqli_fetch_assoc($cek_transaksi);
    ?>
    <div class="card shadow-sm border-0 p-4 mb-4 bg-light">
        <h4 class="mb-3">Beri Rating Pesanan Anda</h4>
        <form method="POST">
            <input type="hidden" name="id_transaksi" value="<?= $t['id_transaksi'] ?>">
            <div class="row">
                <div class="col-md-4 mb-2"><label>Pelayanan</label><input type="number" name="pely" class="form-control" min="1" max="5" required></div>
                <div class="col-md-4 mb-2"><label>Sopir</label><input type="number" name="supr" class="form-control" min="1" max="5" required></div>
                <div class="col-md-4 mb-2"><label>Mobil</label><input type="number" name="mobl" class="form-control" min="1" max="5" required></div>
            </div>
            <textarea name="ulasan" class="form-control mb-3" placeholder="Tulis komentar..." required></textarea>
            <button type="submit" name="btn_submit" class="btn btn-primary">Kirim Ulasan</button>
        </form>
    </div>
    <?php } ?>

    <h4 class="mb-3">Review Pelanggan Terbaru</h4>
    <?php while($row = mysqli_fetch_assoc($query_ulasan)): ?>
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold"><?= htmlspecialchars($row['nama']) ?></h6>
                <small class="text-muted"><?= $row['tgl_rating'] ?></small>
                <p class="mt-2"><?= htmlspecialchars($row['ulasan']) ?></p>
            </div>
        </div>
    <?php endwhile; ?>
</div>