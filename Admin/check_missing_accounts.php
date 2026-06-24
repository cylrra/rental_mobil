<?php
include 'koneksi.php';
$res = mysqli_query($conn, 'SELECT DISTINCT kode_akun FROM jurnal WHERE kode_akun NOT IN (SELECT kode_akun FROM nama_akun)');
while($r=mysqli_fetch_array($res)) {
    echo "Missing Account in jurnal: " . $r[0] . "\n";
}
?>
