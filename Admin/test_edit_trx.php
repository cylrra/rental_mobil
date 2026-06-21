<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$id_sewa = 1; // Assuming 1 exists
$query = mysqli_query($conn, "SELECT t.*, p.nama, m.merk, m.tarif_per_hari FROM transaksi_sewa t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan JOIN mobil m ON t.kode_mobil = m.kode_mobil WHERE t.id_sewa = $id_sewa");
if (!$query) {
    echo "Error 1: " . mysqli_error($conn) . "\n";
} else {
    $trx = mysqli_fetch_assoc($query);
    if (!$trx) { echo "No transaction 1 found\n"; }
    else {
        // query supir
        $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan' AND t.id_sewa != $id_sewa) = 0");
        if (!$supir_query) {
             echo "Error 2: " . mysqli_error($conn) . "\n";
        } else {
             echo "Success\n";
        }
    }
}
?>
