<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// PROTEKSI KETAT: Hanya pelanggan yang boleh transaksi
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

if (isset($_POST['submit'])) {
    // Amankan data input form
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $kode_mobil   = mysqli_real_escape_string($conn, $_POST['kode_mobil']);
    $id_supir     = !empty($_POST['id_supir']) ? mysqli_real_escape_string($conn, $_POST['id_supir']) : null;
    $lokasi_jemput = mysqli_real_escape_string($conn, $_POST['lokasi_jemput']);
    $tanggal_sewa = mysqli_real_escape_string($conn, $_POST['tanggal_sewa']);
    $lama_sewa    = intval($_POST['lama_sewa']);

    // 1. Ambil Tarif Harian Mobil
    $query_mobil = mysqli_query($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data_mobil  = mysqli_fetch_assoc($query_mobil);
    $tarif_mobil = $data_mobil['tarif_per_hari'] ?? 0;

    // 2. Ambil Tarif Harian Supir
    $tarif_supir_harian = 0;
    if ($id_supir !== null) {
        $query_supir = mysqli_query($conn, "SELECT tarif_supir_per_hari FROM supir WHERE id_supir = '$id_supir'");
        $data_supir  = mysqli_fetch_assoc($query_supir);
        $tarif_supir_harian = $data_supir['tarif_supir_per_hari'] ?? 0;
    }

    // 3. Hitung Biaya & Tanggal Kembali
    $biaya_supir = $tarif_supir_harian * $lama_sewa;
    $total_biaya = ($tarif_mobil * $lama_sewa) + $biaya_supir;
    
    // Hitung tanggal kembali otomatis
    $tanggal_kembali = date('Y-m-d', strtotime("+$lama_sewa days", strtotime($tanggal_sewa)));

    // Siapkan nilai untuk opsi supir
    $pake_supir  = ($id_supir !== null) ? 'Ya' : 'Tidak';
    $opsi_supir  = ($id_supir !== null) ? 'ya' : 'tidak';
    $id_supir_db = ($id_supir !== null) ? "'$id_supir'" : "NULL";

    // 4. Masukkan data ke tabel transaksi_sewa dengan status_sewa 'berjalan'
    $query_insert = "INSERT INTO transaksi_sewa (id_pelanggan, pake_supir, kode_mobil, id_supir, biaya_supir, opsi_supir, lokasi_jemput, tanggal_sewa, tanggal_kembali, lama_sewa, total_biaya, status_sewa) 
                     VALUES ('$id_pelanggan', '$pake_supir', '$kode_mobil', $id_supir_db, '$biaya_supir', '$opsi_supir', '$lokasi_jemput', '$tanggal_sewa', '$tanggal_kembali', '$lama_sewa', '$total_biaya', 'berjalan')";

    if (mysqli_query($conn, $query_insert)) {
        // Logika bisnis: kurangi ketersediaan stok mobil
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'disewa' WHERE kode_mobil = '$kode_mobil'");
        
        echo "<script>
                alert('Pemesanan Berhasil Disimpan! Estimasi Biaya: Rp " . number_format($total_biaya, 0, ',', '.') . "');
                window.location = 'transaksi.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan pemesanan: " . mysqli_escape_string($conn, mysqli_error($conn)) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: transaksi.php");
    exit();
}
?>