<?php
require 'c:/xampp/htdocs/rental_mobil/Admin/koneksi.php';
$res = mysqli_query($conn, "DESCRIBE mobil");
while($r=mysqli_fetch_assoc($res)){
    print_r($r);
}
?>
