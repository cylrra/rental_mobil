<?php
$c = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$r = mysqli_query($c, 'SELECT * FROM nama_akun');
while($row = mysqli_fetch_array($r)) echo $row[0] . ' - ' . $row[1] . "\n";
?>
