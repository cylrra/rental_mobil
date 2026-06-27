<?php
// cron_job.php
// File ini dirancang untuk dijalankan melalui cron job / task scheduler secara berkala (misal: setiap 5 menit).
// Jangan meng-include file ini di dalam website secara langsung agar tidak membebani pengunjung.

require_once __DIR__ . '/koneksi.php';

echo "Memulai eksekusi Cron Job...\n";

// ======================================================================
// AUTO-TRANSITION LOGIC FOR RENTAL STATUSES
// ======================================================================

// 1. Auto-transition 'diterima' to 'berjalan' when the rental date starts (tanggal_sewa <= CURDATE)
$sql_auto_start = "UPDATE transaksi_sewa 
                   SET status_sewa = 'berjalan', waktu_mulai_perjalanan = NOW() 
                   WHERE status_sewa = 'diterima' AND tanggal_sewa <= CURDATE()";
if (mysqli_query($conn, $sql_auto_start)) {
    echo "- Transaksi diterima -> berjalan: OK (" . mysqli_affected_rows($conn) . " baris diupdate)\n";
} else {
    echo "- Transaksi diterima -> berjalan: GAGAL (" . mysqli_error($conn) . ")\n";
}

// 1b. Set start timestamp when rental starts ('berjalan') from any other source
if (mysqli_query($conn, "UPDATE transaksi_sewa 
                         SET waktu_mulai_perjalanan = NOW() 
                         WHERE status_sewa = 'berjalan' AND waktu_mulai_perjalanan IS NULL")) {
    echo "- Timestamp mulai perjalanan: OK (" . mysqli_affected_rows($conn) . " baris diupdate)\n";
}

// 2. Auto-transition fully paid 'berjalan' rentals to 'selesai' only when the duration has ended
$sql_find_finished = "SELECT id_sewa, kode_mobil 
                      FROM transaksi_sewa 
                      WHERE status_sewa = 'berjalan' 
                        AND jumlah_bayar >= total_bayar 
                        AND DATE_ADD(tanggal_sewa, INTERVAL lama_sewa DAY) <= CURDATE()";
$res_finished = mysqli_query($conn, $sql_find_finished);
$count_finished = 0;
if ($res_finished && mysqli_num_rows($res_finished) > 0) {
    while ($row = mysqli_fetch_assoc($res_finished)) {
        $id_s = $row['id_sewa'];
        $k_mob = $row['kode_mobil'];
        
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = $id_s");
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$k_mob'");
        $count_finished++;
    }
}
echo "- Transaksi selesai (durasi habis): OK ($count_finished mobil dikembalikan)\n";

// 2b. Auto-transition 'berjalan' to 'selesai' once the delivery simulation has completed (16 minutes / 960 seconds)
$sql_delivery_finished = "SELECT id_sewa, kode_mobil 
                          FROM transaksi_sewa 
                          WHERE status_sewa = 'berjalan' 
                            AND waktu_mulai_perjalanan IS NOT NULL 
                            AND DATE_ADD(waktu_mulai_perjalanan, INTERVAL 960 SECOND) <= NOW()";
$res_delivery = mysqli_query($conn, $sql_delivery_finished);
$count_delivery = 0;
if ($res_delivery && mysqli_num_rows($res_delivery) > 0) {
    while ($row = mysqli_fetch_assoc($res_delivery)) {
        $id_s = $row['id_sewa'];
        $k_mob = $row['kode_mobil'];
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = $id_s");
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$k_mob'");
        $count_delivery++;
    }
}
echo "- Transaksi selesai (simulasi 16 menit): OK ($count_delivery mobil dikembalikan)\n";

echo "Eksekusi Cron Job Selesai.\n";
?>
