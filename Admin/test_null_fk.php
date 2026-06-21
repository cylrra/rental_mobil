<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
if(mysqli_query($conn, "UPDATE transaksi_sewa SET id_supir = NULL WHERE id_sewa = 260002")) {
    echo "NULL OK\n";
} else {
    echo "NULL ERROR: " . mysqli_error($conn) . "\n";
}
?>
