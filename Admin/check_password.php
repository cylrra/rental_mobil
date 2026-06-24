<?php
include 'koneksi.php';
$res = mysqli_query($conn, "SELECT username, password FROM pelanggan LIMIT 3");
while($row = mysqli_fetch_assoc($res)){
    echo "Username: " . $row['username'] . " - Password: " . $row['password'] . "\n";
}
?>
