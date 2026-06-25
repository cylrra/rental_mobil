<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (isset($_POST['submit_pengembalian'])) {
    $id_sewa = mysqli_real_escape_string($conn, $_POST['id_sewa']);
    $waktu_pengembalian_aktual = mysqli_real_escape_string($conn, $_POST['waktu_pengembalian_aktual']);

    // Ambil data transaksi sewa
    $query = mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('Data transaksi tidak ditemukan!'); window.location.href='transaksi.php';</script>";
        exit;
    }

    $tanggal_kembali_seharusnya = $data['tanggal_kembali'];
    $total_biaya = $data['total_biaya'];

    // Hitung selisih hari keterlambatan
    $denda_keterlambatan = 0;
    
    // Ubah ke timestamp (hanya tanggal)
    $tgl_seharusnya_date = date('Y-m-d', strtotime($tanggal_kembali_seharusnya));
    $tgl_aktual_date = date('Y-m-d', strtotime($waktu_pengembalian_aktual));

    if ($tgl_aktual_date > $tgl_seharusnya_date) {
        $diff = strtotime($tgl_aktual_date) - strtotime($tgl_seharusnya_date);
        $hari_terlambat = floor($diff / (60 * 60 * 24));
        
        if ($hari_terlambat > 0) {
            // Denda 10% per hari dari total biaya (atau bisa juga dari tarif harian mobil, sesuai requirement: "denda keterlambatan pengembalian sebesar 10% dari harga sewa")
            // Asumsi: 10% dari total tagihan keseluruhan untuk setiap hari keterlambatan
            $denda_keterlambatan = (0.10 * $total_biaya) * $hari_terlambat;
        }
    }

    $total_tagihan_akhir = $total_biaya + $denda_keterlambatan;

    // Update status transaksi, tanggal kembali aktual, dan denda
    $query_update = "UPDATE transaksi_sewa SET 
                        status_sewa = 'selesai',
                        waktu_pengembalian_aktual = '$waktu_pengembalian_aktual',
                        denda_keterlambatan = '$denda_keterlambatan',
                        total_biaya = '$total_tagihan_akhir'
                     WHERE id_sewa = '$id_sewa'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>
                alert('Pengembalian berhasil diproses!\\nDenda Keterlambatan: Rp " . number_format($denda_keterlambatan, 0, ',', '.') . "\\nTotal Tagihan Akhir: Rp " . number_format($total_tagihan_akhir, 0, ',', '.') . "');
                window.location.href = 'transaksi.php';
              </script>";
    } else {
        echo "<script>alert('Gagal memproses pengembalian: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }

} else {
    header("Location: transaksi.php");
}
?>
