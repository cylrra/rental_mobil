<?php
include "c:/xampp/htdocs/rental_mobil/koneksi.php";

$sql1 = "UPDATE coa2 SET `name akun` = 'Pendapatan Rental Mobil & Supir' WHERE `nomor akun` = '411'";
$sql2 = "UPDATE nama_akun SET nama_akun = 'Pendapatan Rental Mobil & Supir' WHERE kode_akun = '411'";

if (mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
    echo "Account names updated successfully.\n";
} else {
    echo "Error updating account names: " . mysqli_error($conn) . "\n";
}
?>
