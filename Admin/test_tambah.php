<?php
require 'c:/xampp/htdocs/rental_mobil/Admin/koneksi.php';

$_POST['simpan'] = true;
$_POST['kode_mobil'] = 'TEST002';
$_POST['nopol'] = 'B 9999 TST';
$_POST['merk'] = 'Test Tambah';
$_POST['jenis'] = 'SUV';
$_POST['tarif_per_hari'] = '400000';
$_POST['Unit_Tersedia'] = '3';

$_FILES['gambar'] = [
    'name' => 'test.jpg',
    'tmp_name' => 'c:/temp/test.jpg',
    'error' => 0,
    'size' => 100
];

include 'c:/xampp/htdocs/rental_mobil/Admin/mobil_tambah_proses.php';
?>
