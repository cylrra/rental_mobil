<?php
include 'koneksi.php';

$sql = "ALTER TABLE pelanggan ADD COLUMN email varchar(150) NULL AFTER nama";
if(mysqli_query($conn, $sql)){
    echo "Column 'email' added successfully to pelanggan.\n";
} else {
    echo "Error or column already exists: " . mysqli_error($conn) . "\n";
}
?>
