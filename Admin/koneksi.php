<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "rental_mobil"; // Sesuaikan dengan nama schema di Workbench Anda

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>