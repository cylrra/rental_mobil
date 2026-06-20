<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (isset($_POST['submit'])) {
    // Amankan data input form
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $kode_mobil   = mysqli_real_escape_string($conn, $_POST['kode_mobil']);
    $id_supir     = !empty($_POST['id_supir']) ? mysqli_real_escape_string($conn, $_POST['id_supir']) : null;
    $tanggal_sewa = mysqli_real_escape_string($conn, $_POST['tanggal_sewa']);
    $lama_sewa    = intval($_POST['lama_sewa']);

    // 1. Ambil Tarif Harian Mobil dari database
    $query_mobil = mysqli_query($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data_mobil  = mysqli_fetch_assoc($query_mobil);
    $tarif_mobil = $data_mobil['tarif_per_hari'];

    // 2. Ambil Tarif Harian Supir (jika memilih opsi menggunakan supir)
    $tarif_supir = 0;
    if ($id_supir !== null) {
        $query_supir = mysqli_query($conn, "SELECT tarif_supir_per_hari FROM supir WHERE id_supir = '$id_supir'");
        $data_supir  = mysqli_fetch_assoc($query_supir);
        $tarif_supir = $data_supir['tarif_supir_per_hari'];
    }

    // 3. Hitung otomatis total biaya berdasarkan hari sewa
    $total_harga = ($tarif_mobil + $tarif_supir) * $lama_sewa;

    // Siapkan nilai SQL untuk id_supir agar bisa bernilai NULL di database jika lepas kunci
    $id_supir_db = ($id_supir !== null) ? "'$id_supir'" : "NULL";

    // 4. Masukkan data hasil perhitungan ke tabel transaksi_sewa dengan status_sewa 'berjalan'
    $query_insert = "INSERT INTO transaksi_sewa (id_pelanggan, kode_mobil, id_supir, tanggal_sewa, lama_sewa, total_harga, status_sewa) 
                     VALUES ('$id_pelanggan', '$kode_mobil', $id_supir_db, '$tanggal_sewa', '$lama_sewa', '$total_harga', 'berjalan')";

    if (mysqli_query($conn, $query_insert)) {
        // CATATAN: Kode mengubah status fisik supir sengaja dihapus karena 
        // ketersediaan supir sekarang dihitung otomatis (real-time) berdasarkan status_sewa 'berjalan'.
        
        echo "<script>
                alert('Transaksi Berhasil Disimpan! Total Biaya: Rp " . number_format($total_harga, 0, ',', '.') . "');
                window.location = 'transaksi.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan transaksi: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: transaksi.php");
    exit();
}
?>