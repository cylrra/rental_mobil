<?php
// Sesuaikan variabel koneksi ini di file PHP kamu
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "rental_mobil";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Alias to maintain compatibility with existing project code
$conn = $koneksi;


?>
