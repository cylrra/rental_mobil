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
    $jenis_bayar    = mysqli_real_escape_string($conn, $_POST['tipe_pembayaran']); // 'DP' atau 'Lunas'
    
    $tipe_pembayaran = ($jenis_bayar === 'DP') ? 'DP' : 'Lunas';
    $keterangan     = "Pembayaran " . strtoupper($tipe_pembayaran) . " Sewa Mobil ID: " . $id_sewa;

    mysqli_begin_transaction($conn);

    try {
        // 1. Ambil KODE_MOBIL, TOTAL_BAYAR, dan JUMLAH_BAYAR dari transaksi_sewa
        $query_cek = "SELECT kode_mobil, total_bayar, jumlah_bayar FROM transaksi_sewa WHERE id_sewa = '$id_sewa'";
        $result_cek = mysqli_query($conn, $query_cek);
        
        if (!$result_cek || mysqli_num_rows($result_cek) == 0) {
            throw new Exception("Data sewa tidak ditemukan.");
        }
        
        $data_trx = mysqli_fetch_assoc($result_cek);
        $kode_mobil = $data_trx['kode_mobil'];
        $total_tagihan = (int)$data_trx['total_bayar'];
        $sudah_dibayar = (int)$data_trx['jumlah_bayar'];
        $nominal_input = (int)$jumlah_bayar;

        // LOGIKA PENGUNCI AGAR TIDAK LEBIH DARI TAGIHAN
        $total_setelah_bayar = $sudah_dibayar + $nominal_input;
        $nominal_final = $nominal_input;

        if ($total_setelah_bayar > $total_tagihan) {
            $nominal_final = $total_tagihan - $sudah_dibayar;
        }

        // 2. Simpan ke Tabel Pembayaran
        $query_bayar = "INSERT INTO pembayaran (id_sewa, jenis_pembayaran, metode_pembayaran, tanggal_bayar, jumlah_bayar, status_konfirmasi, keterangan, tipe_pembayaran) 
                        VALUES ('$id_sewa', '$jenis_bayar', '$metode', '$tgl_bayar', '$nominal_final', 'menunggu', '$keterangan', '$tipe_pembayaran')";
        
        if (!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Posting ke Jurnal Umum
        $q_debit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                        VALUES ('$tgl_bayar', '101', '$nominal_final', 0, '$keterangan', '$id_sumber')";
        if (!mysqli_query($conn, $q_debit_sql)) {
            throw new Exception("Gagal posting jurnal (Debit): " . mysqli_error($conn));
        }

        $q_kredit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                         VALUES ('$tgl_bayar', '401', 0, '$nominal_final', '$keterangan', '$id_sumber')";
        if (!mysqli_query($conn, $q_kredit_sql)) {
            throw new Exception("Gagal posting jurnal (Kredit): " . mysqli_error($conn));
        }

        // 4. Update tabel transaksi_sewa
        $q_update_transaksi = "UPDATE transaksi_sewa SET jumlah_bayar = jumlah_bayar + $nominal_final WHERE id_sewa = '$id_sewa'";
        if (!mysqli_query($conn, $q_update_transaksi)) {
            throw new Exception("Gagal mengupdate jumlah bayar di transaksi: " . mysqli_error($conn));
        }

        // 5. Update status transaksi_sewa & ketersediaan mobil
        if ($total_setelah_bayar >= $total_tagihan) {
            // Check if the rental period has actually ended before setting it to 'selesai'
            $check_time = mysqli_query($conn, "SELECT tanggal_sewa, lama_sewa FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
            $time_data = mysqli_fetch_assoc($check_time);
            $end_date_str = date('Y-m-d', strtotime($time_data['tanggal_sewa'] . ' + ' . $time_data['lama_sewa'] . ' days'));
            $is_ended = (date('Y-m-d') >= $end_date_str);

            if ($is_ended) {
                if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = '$id_sewa'")) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }
                if (!mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$kode_mobil'")) {
                    throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
                }
            } else {
                // Keep it running (berjalan) if the rental period is still ongoing
                if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'berjalan' WHERE id_sewa = '$id_sewa'")) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }
            }
        } else {
            if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'DP' WHERE id_sewa = '$id_sewa'")) {
                throw new Exception("Gagal update status sewa (DP): " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        // Respon sukses untuk AJAX
        echo "success";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
} else {
    header("Location: pembayaran.php");
    exit();
}
?>