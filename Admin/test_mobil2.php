<?php
require 'c:/xampp/htdocs/rental_mobil/Admin/koneksi.php';

$sql_mobil = "SELECT m.*, 
                  (CAST(m.Unit_Tersedia AS SIGNED) - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime,
                  (SELECT tanggal_pemeliharaan FROM pemeliharaan p WHERE p.kode_mobil = m.kode_mobil AND p.status = 'terjadwal' ORDER BY tanggal_pemeliharaan ASC LIMIT 1) AS jadwal_servis
                  FROM mobil m";
                  
$query = mysqli_query($conn, $sql_mobil);

if (!$query) {
    echo "Error: " . mysqli_error($conn) . "\n";
} else {
    echo "Success: " . mysqli_num_rows($query) . " rows\n";
}
?>
