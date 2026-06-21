<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$r = mysqli_query($conn, 'DESCRIBE supir');
while($row = mysqli_fetch_array($r)) echo $row[0] . ' - ' . $row[1] . "\n";
?>
