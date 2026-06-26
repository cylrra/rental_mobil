<?php
// Sesuaikan variabel koneksi ini di file PHP kamu
$host = "sql112.infinityfree.com";
$user = "if0_41895608";
$pass = "wisudabesokya24"; 
$db   = "if0_41895608_rental_mobil";

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Alias to maintain compatibility with existing project code
$conn = $koneksi;

// ======================================================================
// AUTO-TRANSITION LOGIC FOR RENTAL STATUSES
// ======================================================================

// 1. Auto-transition 'diterima' to 'berjalan' when the rental date starts (tanggal_sewa <= CURDATE)
$sql_auto_start = "UPDATE transaksi_sewa 
                   SET status_sewa = 'berjalan', waktu_mulai_perjalanan = NOW() 
                   WHERE status_sewa = 'diterima' AND tanggal_sewa <= CURDATE()";
mysqli_query($conn, $sql_auto_start);

// 1b. Set start timestamp when rental starts ('berjalan') from any other source
mysqli_query($conn, "UPDATE transaksi_sewa 
                     SET waktu_mulai_perjalanan = NOW() 
                     WHERE status_sewa = 'berjalan' AND waktu_mulai_perjalanan IS NULL");

// 2. Auto-transition fully paid 'berjalan' rentals to 'selesai' only when the duration has ended
// This keeps the car 'disewa' and status 'berjalan' even if paid upfront, until the rental period actually ends.
$sql_find_finished = "SELECT id_sewa, kode_mobil 
                      FROM transaksi_sewa 
                      WHERE status_sewa = 'berjalan' 
                        AND jumlah_bayar >= total_bayar 
                        AND DATE_ADD(tanggal_sewa, INTERVAL lama_sewa DAY) <= CURDATE()";
$res_finished = mysqli_query($conn, $sql_find_finished);
if ($res_finished && mysqli_num_rows($res_finished) > 0) {
    while ($row = mysqli_fetch_assoc($res_finished)) {
        $id_s = $row['id_sewa'];
        $k_mob = $row['kode_mobil'];
        
        // Mark rental as selesai
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = $id_s");
        
        // Release the car
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$k_mob'");
    }
}

// 2b. Auto-transition 'berjalan' to 'selesai' once the delivery simulation has completed (16 minutes / 960 seconds)
$sql_delivery_finished = "SELECT id_sewa, kode_mobil 
                          FROM transaksi_sewa 
                          WHERE status_sewa = 'berjalan' 
                            AND waktu_mulai_perjalanan IS NOT NULL 
                            AND DATE_ADD(waktu_mulai_perjalanan, INTERVAL 960 SECOND) <= NOW()";
$res_delivery = mysqli_query($conn, $sql_delivery_finished);
if ($res_delivery && mysqli_num_rows($res_delivery) > 0) {
    while ($row = mysqli_fetch_assoc($res_delivery)) {
        $id_s = $row['id_sewa'];
        $k_mob = $row['kode_mobil'];
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = $id_s");
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$k_mob'");
    }
}
?>
