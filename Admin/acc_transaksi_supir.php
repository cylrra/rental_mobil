<?php
include 'koneksi.php';
$id_sewa = $_POST['id_sewa'];
$id_supir = $_POST['id_supir'];

if ($id_supir) {
    mysqli_query($conn, "UPDATE transaksi_sewa SET id_supir = '$id_supir', status_sewa = 'diterima' WHERE id_sewa = '$id_sewa'");
    mysqli_query($conn, "UPDATE supir SET status_supir = 'bertugas' WHERE id_supir = '$id_supir'");
} else {
    mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'diterima' WHERE id_sewa = '$id_sewa'");
}

// Auto-start logic handled globally, but we can do a quick check here too just in case
$check_date = mysqli_query($conn, "SELECT tanggal_sewa FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
$row = mysqli_fetch_assoc($check_date);
if (strtotime($row['tanggal_sewa']) <= strtotime(date('Y-m-d'))) {
    mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'berjalan' WHERE id_sewa = '$id_sewa'");
}

header("Location: transaksi.php");
exit();
?>
