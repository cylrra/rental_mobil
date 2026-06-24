<?php
include 'koneksi.php';

// Add Bank accounts
$queries = [
    "INSERT IGNORE INTO nama_akun (kode_akun, nama_akun, saldo_awal) VALUES ('1121', 'Bank BCA', 0)",
    "INSERT IGNORE INTO nama_akun (kode_akun, nama_akun, saldo_awal) VALUES ('1122', 'Bank BNI', 0)",
    "INSERT IGNORE INTO nama_akun (kode_akun, nama_akun, saldo_awal) VALUES ('1123', 'Bank Mandiri', 0)",
    "UPDATE nama_akun SET nama_akun = 'Beban Gaji Admin' WHERE kode_akun = '511'",
    "INSERT IGNORE INTO nama_akun (kode_akun, nama_akun, saldo_awal) VALUES ('518', 'Beban Gaji Supir', 0)"
];

foreach ($queries as $q) {
    if (mysqli_query($conn, $q)) {
        echo "Success: $q\n";
    } else {
        echo "Error: " . mysqli_error($conn) . " in $q\n";
    }
}
?>
