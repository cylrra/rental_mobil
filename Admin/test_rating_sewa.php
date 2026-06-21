<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$r = mysqli_query($conn, 'DESCRIBE rating_sewa');
if ($r) {
    while($row = mysqli_fetch_array($r)) echo $row[0] . ' - ' . $row[1] . "\n";
} else {
    echo "ERROR: " . mysqli_error($conn);
}
?>
