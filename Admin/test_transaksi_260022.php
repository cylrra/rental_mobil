<?php
include 'koneksi.php';
$id_sewa = 260022;
$query = mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
$data = mysqli_fetch_assoc($query);
echo json_encode($data, JSON_PRETTY_PRINT);
?>
