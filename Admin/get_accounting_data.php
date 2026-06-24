<?php
include 'koneksi.php';
$res = mysqli_query($conn, 'SELECT * FROM nama_akun');
echo "Accounts:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r)."\n";
}

$res = mysqli_query($conn, 'SELECT * FROM jurnal ORDER BY tanggal DESC LIMIT 10');
echo "\nJurnal entries:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r)."\n";
}
?>
