<?php
include 'koneksi.php';
$q = mysqli_query($conn, 'SELECT username, password FROM admin');
while($r = mysqli_fetch_assoc($q)) {
    echo $r['username'] . ' : ' . $r['password'] . PHP_EOL;
}
?>
