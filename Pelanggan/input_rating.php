<?php
include 'koneksi.php';
session_start();

$id_transaksi = $_GET['id_transaksi']; // Diambil dari parameter URL riwayat
$id_pelanggan = $_SESSION['id_pelanggan'];

if (isset($_POST['kirim_rating'])) {
    $pely  = $_POST['rating_pelayanan'];
    $supr  = $_POST['rating_supir'];
    $mobl  = $_POST['rating_mobil'];
    $txt   = mysqli_real_escape_string($koneksi, $_POST['ulasan']);

    $insert = mysqli_query($koneksi, "INSERT INTO rating_sewa (id_transaksi, id_pelanggan, rating_pelayanan, rating_supir, rating_mobil, ulasan) 
                                      VALUES ('$id_transaksi', '$id_pelanggan', '$pely', '$supr', '$mobl', '$txt')");
    if ($insert) {
        echo "<script>alert('Terima kasih atas penilaian Anda!'); window.location='riwayat_pembayaran.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Beri Rating Rental</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Beri Penilaian Pengalaman Rental</h2>
    <form action="" method="POST" style="max-width: 400px;">
        <p>Skala Penilaian (1 = Sangat Buruk, 5 = Sangat Puas)</p>
        
        <label>Pelayanan Admin/Kantor:</label><br>
        <input type="number" name="rating_pelayanan" min="1" max="5" required style="padding:5px; margin-bottom:10px;"><br>

        <label>Kinerja & Keramahan Supir:</label><br>
        <input type="number" name="rating_supir" min="1" max="5" required style="padding:5px; margin-bottom:10px;"><br>

        <label>Kondisi & Kebersihan Mobil:</label><br>
        <input type="number" name="rating_mobil" min="1" max="5" required style="padding:5px; margin-bottom:10px;"><br>

        <label>Ulasan / Masukan tambahan:</label><br>
        <textarea name="ulasan" style="width:100%; height:80px; margin-bottom:10px;"></textarea><br>

        <button type="submit" name="kirim_rating" style="background:#2ecc71; color:white; border:none; padding:10px;">Kirim Rating</button>
    </form>
</body>
</html>