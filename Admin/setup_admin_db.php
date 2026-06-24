<?php
// Script ini digunakan untuk setup awal tabel admin dan update tabel transaksi_sewa.
include 'koneksi.php';

echo "Memulai setup database...<br>\n";

// 1. Buat Tabel Admin
$sql_create_admin = "CREATE TABLE IF NOT EXISTS `admin` (
    `id_admin` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `nama_lengkap` varchar(100) NOT NULL,
    PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $sql_create_admin)) {
    echo "OK: Tabel admin berhasil dibuat / sudah ada.<br>\n";
} else {
    echo "ERROR membuat tabel admin: " . mysqli_error($conn) . "<br>\n";
}

// 2. Insert 6 User Admin
$admins = [
    ['username' => 'aghni', 'nama' => 'Aghni'],
    ['username' => 'cahya', 'nama' => 'Cahya'],
    ['username' => 'ferra', 'nama' => 'Ferra'],
    ['username' => 'haadziq', 'nama' => 'Haadziq'],
    ['username' => 'maia', 'nama' => 'Maia'],
    ['username' => 'zidni', 'nama' => 'Zidni']
];

$password_default = password_hash('12345', PASSWORD_DEFAULT);

foreach ($admins as $admin) {
    $uname = $admin['username'];
    $nama = $admin['nama'];
    
    // Cek apakah user sudah ada
    $check = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$uname'");
    if (mysqli_num_rows($check) == 0) {
        $insert = "INSERT INTO admin (username, password, nama_lengkap) VALUES ('$uname', '$password_default', '$nama')";
        if (mysqli_query($conn, $insert)) {
            echo "OK: User '$uname' berhasil ditambahkan.<br>\n";
        } else {
            echo "ERROR menambahkan user '$uname': " . mysqli_error($conn) . "<br>\n";
        }
    } else {
        echo "INFO: User '$uname' sudah ada, dilewati.<br>\n";
    }
}

// 3. Tambah Kolom status_gaji_supir di transaksi_sewa
// Cek dulu apakah kolom sudah ada
$check_column = mysqli_query($conn, "SHOW COLUMNS FROM `transaksi_sewa` LIKE 'status_gaji_supir'");
if (mysqli_num_rows($check_column) == 0) {
    $alter = "ALTER TABLE `transaksi_sewa` ADD `status_gaji_supir` ENUM('belum','sudah') NOT NULL DEFAULT 'belum' AFTER `status_sewa`";
    if (mysqli_query($conn, $alter)) {
        echo "OK: Kolom status_gaji_supir berhasil ditambahkan ke tabel transaksi_sewa.<br>\n";
    } else {
        echo "ERROR menambahkan kolom: " . mysqli_error($conn) . "<br>\n";
    }
} else {
    echo "INFO: Kolom status_gaji_supir sudah ada di tabel transaksi_sewa.<br>\n";
}

echo "<br>Setup database selesai.\n";
?>
