<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// PROTEKSI KETAT: Hanya pelanggan yang boleh bayar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

if (isset($_POST['simpan_pembayaran'])) {
    $id_sewa        = mysqli_real_escape_string($conn, $_POST['id_transaksi']); 
    $tgl_bayar      = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);     
    $jumlah_bayar   = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
    $metode         = mysqli_real_escape_string($conn, $_POST['metode_bayar']);  
    $jenis_bayar    = mysqli_real_escape_string($conn, $_POST['jenis_pembayaran']); // 'dp' atau 'pelunasan'
    
    $tipe_pembayaran = ($jenis_bayar === 'dp') ? 'DP' : 'Lunas';
    $keterangan     = "Pembayaran " . strtoupper($tipe_pembayaran) . " Sewa Mobil ID: " . $id_sewa;

    mysqli_begin_transaction($conn);

    try {
        // 1. Ambil KODE_MOBIL dari transaksi_sewa
        $query_cari_mobil = "SELECT kode_mobil FROM transaksi_sewa WHERE id_sewa = '$id_sewa'";
        $result_mobil     = mysqli_query($conn, $query_cari_mobil);
        
        if (!$result_mobil || mysqli_num_rows($result_mobil) == 0) {
            throw new Exception("Data sewa tidak ditemukan.");
        }
        
        $data_mobil = mysqli_fetch_assoc($result_mobil);
        $kode_mobil = $data_mobil['kode_mobil'];

        // 2. Simpan ke Tabel Pembayaran
        $query_bayar = "INSERT INTO pembayaran (id_sewa, jenis_pembayaran, metode_pembayaran, tanggal_bayar, jumlah_bayar, status_konfirmasi, keterangan, tipe_pembayaran) 
                        VALUES ('$id_sewa', '$jenis_bayar', '$metode', '$tgl_bayar', '$jumlah_bayar', 'menunggu', '$keterangan', '$tipe_pembayaran')";
        
        if (!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Posting ke Jurnal Umum (Debet: Kas/Bank '101', Kredit: Pendapatan Sewa '401')
        $q_debit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                        VALUES ('$tgl_bayar', '101', '$jumlah_bayar', 0, '$keterangan', '$id_sumber')";
        if (!mysqli_query($conn, $q_debit_sql)) {
            throw new Exception("Gagal posting jurnal (Debit): " . mysqli_error($conn));
        }

        $q_kredit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                         VALUES ('$tgl_bayar', '401', 0, '$jumlah_bayar', '    $keterangan', '$id_sumber')";
        if (!mysqli_query($conn, $q_kredit_sql)) {
            throw new Exception("Gagal posting jurnal (Kredit): " . mysqli_error($conn));
        }

        // 4. Update status transaksi_sewa & ketersediaan mobil
        if ($jenis_bayar == 'pelunasan') {
            // Update transaksi sewa menjadi Selesai
            if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = '$id_sewa'")) {
                throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
            }

            // Kembalikan status mobil menjadi tersedia
            if (!mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$kode_mobil'")) {
                throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
            }
        } else {
            // Jika Uang Muka (DP), update status sewa menjadi DP
            if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'DP' WHERE id_sewa = '$id_sewa'")) {
                throw new Exception("Gagal update status sewa (DP): " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        echo "<script>alert('Pembayaran Berhasil Dikirim! Mohon tunggu konfirmasi admin.'); window.location='riwayat_pembayaran.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<div style='color:red; padding:20px; border:1px solid red; font-family: sans-serif; max-width:600px; margin:50px auto; background:#fff5f5; border-radius:10px;'>";
        echo "<strong>Terjadi Kesalahan Sistem:</strong><br>" . htmlspecialchars($e->getMessage());
        echo "<br><br><a href='pembayaran.php' style='color: blue; font-weight:bold;'>Kembali ke Form</a>";
        echo "</div>";
    }
} else {
    header("Location: pembayaran.php");
    exit();
}
?>