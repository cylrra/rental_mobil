<?php
include 'koneksi.php';
$query = "SELECT r.*, p.nama as nama_pelanggan, m.merk as merk_mobil, m.nopol
          FROM rating_sewa r 
          JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
          JOIN transaksi_sewa t ON r.id_transaksi = t.id_sewa
          JOIN mobil m ON t.kode_mobil = m.kode_mobil
          ORDER BY r.tgl_rating DESC";
$res = mysqli_query($conn, $query);
if(!$res){
    echo "Error: " . mysqli_error($conn);
} else {
    echo "Success. Rows: " . mysqli_num_rows($res);
}
?>
