<?php
include 'koneksi.php';
$res = mysqli_query($conn, 'SHOW TABLES');
while($row = mysqli_fetch_array($res)) {
    echo $row[0]."\n";
    $res2 = mysqli_query($conn, 'DESCRIBE '.$row[0]);
    while($col = mysqli_fetch_assoc($res2)) {
        echo '  - '.$col['Field'].' ('.$col['Type'].')'."\n";
    }
}
?>
