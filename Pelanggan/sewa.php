<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'koneksi.php'; 

// Jika tombol "btn_sewa" ditekan
if (isset($_POST['btn_sewa'])) {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $kode_mobil = $_POST['kode_mobil'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan_perjalanan']);

    // 1. Ambil harga sewa dari tabel mobil
    $q_harga = mysqli_query($conn, "SELECT harga_sewa FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data_m = mysqli_fetch_assoc($q_harga);
    $harga = $data_m['harga_sewa'];

    // 2. Hitung durasi
    $d1 = new DateTime($tanggal_sewa);
    $d2 = new DateTime($tanggal_kembali);
    $durasi = $d1->diff($d2)->days;
    $durasi = ($durasi == 0) ? 1 : $durasi; // Minimal 1 hari
    $total_biaya = $harga * $durasi;

    // 3. Simpan ke database
    $sql = "INSERT INTO transaksi_sewa (id_pelanggan, kode_mobil, tanggal_sewa, tanggal_kembali, total_biaya, tujuan_perjalanan) 
            VALUES ('$id_pelanggan', '$kode_mobil', '$tanggal_sewa', '$tanggal_kembali', '$total_biaya', '$tujuan')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Sewa Berhasil!'); window.location='riwayat_pembayaran.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sewa Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-sm border-0">
        <h3 class="mb-4">Form Sewa Mobil</h3>
        <form method="POST">
            <div class="mb-3">
                <label>Pilih Mobil</label>
                <select name="kode_mobil" class="form-control" required>
                    <?php 
                    $m = mysqli_query($conn, "SELECT * FROM mobil");
                    while($row = mysqli_fetch_array($m)) {
                        echo "<option value='".$row['kode_mobil']."'>".$row['merk']." - Rp ".number_format($row['harga_sewa'])." /hari</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Tanggal Sewa</label>
                    <input type="date" name="tanggal_sewa" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Tujuan Perjalanan</label>
                <input type="text" name="tujuan_perjalanan" class="form-control" placeholder="Masukkan tujuan perjalanan" required>
            </div>
            <button type="submit" name="btn_sewa" class="btn btn-primary w-100">Konfirmasi Sewa</button>
        </form>
    </div>
</div>
</body>
</html>