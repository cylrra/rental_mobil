<?php
include 'koneksi.php';

echo "Table rating_sewa:\n";
$q1 = mysqli_query($conn, "SELECT * FROM rating_sewa LIMIT 5");
while ($r = mysqli_fetch_assoc($q1)) {
    print_r($r);
}

echo "Table ulasan:\n";
$q2 = mysqli_query($conn, "SELECT * FROM ulasan LIMIT 5");
while ($r = mysqli_fetch_assoc($q2)) {
    print_r($r);
}
?>
