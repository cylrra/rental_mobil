<?php
include 'koneksi.php';
// Clean up invalid account codes
mysqli_query($conn, "UPDATE jurnal SET kode_akun = '111' WHERE kode_akun = '101'");
mysqli_query($conn, "UPDATE jurnal SET kode_akun = '411' WHERE kode_akun = '401'");
echo "Database cleanup completed.\n";
?>
