<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
// try to update a transaction
$q = mysqli_query($conn, "UPDATE transaksi_sewa SET id_supir = NULL WHERE id_sewa = 1");
if (!$q) {
    echo "Error: " . mysqli_error($conn) . "\n";
} else {
    echo "Update OK\n";
}
?>
