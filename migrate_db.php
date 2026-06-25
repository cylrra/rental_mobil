<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$queries = [
    "ALTER TABLE mobil ADD COLUMN tarif_12_dalam DECIMAL(10,2) NULL, ADD COLUMN tarif_12_luar DECIMAL(10,2) NULL, ADD COLUMN tarif_24_dalam DECIMAL(10,2) NULL, ADD COLUMN tarif_24_luar DECIMAL(10,2) NULL",
    "UPDATE mobil SET tarif_24_dalam = tarif_per_hari, tarif_24_luar = tarif_per_hari * 1.2, tarif_12_dalam = tarif_per_hari * 0.6, tarif_12_luar = tarif_per_hari * 0.8",
    "ALTER TABLE supir ADD COLUMN tarif_12_dalam DECIMAL(10,2) NULL, ADD COLUMN tarif_12_luar DECIMAL(10,2) NULL, ADD COLUMN tarif_24_dalam DECIMAL(10,2) NULL, ADD COLUMN tarif_24_luar DECIMAL(10,2) NULL",
    "UPDATE supir SET tarif_24_dalam = tarif_supir_per_hari, tarif_24_luar = tarif_supir_per_hari * 1.5, tarif_12_dalam = tarif_supir_per_hari * 0.6, tarif_12_luar = tarif_supir_per_hari * 0.8",
    "ALTER TABLE transaksi_sewa ADD COLUMN durasi_sewa ENUM('12 Jam', '24 Jam') DEFAULT '24 Jam', ADD COLUMN area_pemakaian ENUM('Dalam Kota', 'Luar Kota') DEFAULT 'Dalam Kota', ADD COLUMN waktu_pengambilan DATETIME NULL, ADD COLUMN waktu_pengembalian_aktual DATETIME NULL, ADD COLUMN denda_keterlambatan DECIMAL(12,2) DEFAULT 0"
];

foreach ($queries as $q) {
    if (mysqli_query($conn, $q)) {
        echo "Success: " . substr($q, 0, 50) . "...\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\nQuery: $q\n";
    }
}
?>
