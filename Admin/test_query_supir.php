<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$id_sewa = 1;
$supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan' AND t.id_sewa != $id_sewa) = 0");
if (!$supir_query) {
    echo "Query Error: " . mysqli_error($conn) . "\n";
} else {
    echo "Query OK. Rows: " . mysqli_num_rows($supir_query) . "\n";
}
?>
