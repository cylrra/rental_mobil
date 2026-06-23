<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    $id_pelanggan  = $_SESSION['id_pelanggan'];
    $kode_mobil    = $_POST['kode_mobil'];
    $nama_penyewa  = $_POST['nama_penyewa'];
    $tanggal_sewa  = $_POST['tanggal_sewa'];
    $lama_sewa     = (int)$_POST['lama_sewa'];
    $lokasi_jemput = $_POST['lokasi_jemput'];
    $alamat_detail = $_POST['alamat_detail'];
    $id_supir      = (!empty($_POST['id_supir']) && is_numeric($_POST['id_supir'])) ? (int)$_POST['id_supir'] : NULL;
    $status_sewa   = 'menunggu_konfirmasi';

    // AMBIL TARIF DARI DB
    $q_tarif = mysqli_query($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $d_tarif = mysqli_fetch_assoc($q_tarif);
    $tarif_mobil = $d_tarif['tarif_per_hari'];
    $tarif_supir = ($id_supir !== NULL) ? 200000 : 0;
    
    // HITUNG TOTAL
    $total_bayar = ($tarif_mobil + $tarif_supir) * $lama_sewa;

    // INSERT KE DB (Pastikan tabel Anda punya kolom total_bayar)
    $stmt = $conn->prepare("INSERT INTO transaksi_sewa 
            (id_pelanggan, kode_mobil, nama_penyewa, id_supir, tanggal_sewa, lama_sewa, lokasi_jemput, alamat_detail, status_sewa, total_bayar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ississsssi", $id_pelanggan, $kode_mobil, $nama_penyewa, $id_supir, $tanggal_sewa, $lama_sewa, $lokasi_jemput, $alamat_detail, $status_sewa, $total_bayar);

    if ($stmt->execute()) {
        echo "<script>alert('Berhasil!'); window.location='riwayat_pembayaran.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>