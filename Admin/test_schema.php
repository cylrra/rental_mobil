<?php
require 'c:/xampp/htdocs/rental_mobil/Admin/koneksi.php';
$res = mysqli_query($conn, "DESCRIBE transaksi_sewa");
while($r=mysqli_fetch_assoc($res)){
    print_r($r);
}
?>
