<?php
include 'auth.php';
requireAdmin();
include 'koneksi.php'; // Menggunakan variabel $conn

if (isset($_POST['submit'])) {
    $id_supir = $_POST['id_supir'];
    $nama_supir = $_POST['nama_supir'];
    $no_telp = $_POST['no_telp'];
    $tarif_supir_per_hari = $_POST['tarif_supir_per_hari'];
    $tarif_12_dalam = $_POST['tarif_12_dalam'];
    $tarif_12_luar = $_POST['tarif_12_luar'];
    $tarif_24_dalam = $_POST['tarif_24_dalam'];
    $tarif_24_luar = $_POST['tarif_24_luar'];
    $status_supir = $_POST['status_supir'];

    // Query INSERT data ke dalam tabel supir
    $query_insert = "INSERT INTO supir (id_supir, nama_supir, no_telp, tarif_supir_per_hari, tarif_12_dalam, tarif_12_luar, tarif_24_dalam, tarif_24_luar, status_supir) 
                     VALUES ('$id_supir', '$nama_supir', '$no_telp', '$tarif_supir_per_hari', '$tarif_12_dalam', '$tarif_12_luar', '$tarif_24_dalam', '$tarif_24_luar', '$status_supir')";

    $simpan = mysqli_query($conn, $query_insert);

    if ($simpan) {
        echo "<script>
                alert('Data supir baru berhasil ditambahkan!');
                window.location.href='supir.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan supir: " . mysqli_error($conn) . "');
                window.location.href='tambah_supir.php';
              </script>";
    }
}
?>