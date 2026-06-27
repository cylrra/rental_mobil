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
    $id_sewa        = $_POST['id_transaksi']; 
    $tgl_bayar      = $_POST['tgl_bayar'];    
    $jumlah_bayar   = $_POST['jumlah_bayar'];
    $metode         = $_POST['metode_bayar'];  
    $jenis_bayar    = $_POST['tipe_pembayaran']; // 'DP' atau 'Lunas'
    $bank_tujuan    = isset($_POST['bank_tujuan']) ? $_POST['bank_tujuan'] : '';
    
    $tipe_pembayaran = ($jenis_bayar === 'DP') ? 'DP' : 'Lunas';
    $keterangan     = "Pembayaran " . strtoupper($tipe_pembayaran) . " Sewa Mobil ID: " . $id_sewa;

    // Tambahkan metode spesifik ke keterangan (karena value metode_bayar sekarang "Transfer Bank BCA", dll)
    if (!empty($metode)) {
        $keterangan .= " ($metode)";
    }

    mysqli_begin_transaction($conn);

    try {
        // 1. Ambil KODE_MOBIL, TOTAL_BAYAR, dan JUMLAH_BAYAR dari transaksi_sewa
        $stmt_cek = mysqli_prepare($conn, "SELECT kode_mobil, total_bayar, jumlah_bayar FROM transaksi_sewa WHERE id_sewa = ?");
        mysqli_stmt_bind_param($stmt_cek, "i", $id_sewa);
        mysqli_stmt_execute($stmt_cek);
        $result_cek = mysqli_stmt_get_result($stmt_cek);
        
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
        $status_konf = 'menunggu';
        $stmt_bayar = mysqli_prepare($conn, "INSERT INTO pembayaran (id_sewa, jenis_pembayaran, metode_pembayaran, tanggal_bayar, jumlah_bayar, status_konfirmasi, keterangan, tipe_pembayaran) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_bayar, "isssdsss", $id_sewa, $jenis_bayar, $metode, $tgl_bayar, $nominal_final, $status_konf, $keterangan, $tipe_pembayaran);
        if (!mysqli_stmt_execute($stmt_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Posting ke Jurnal Umum
        $akun_debit = '111'; // Default Kas
        if (strpos(strtolower($metode), 'bca') !== false) {
            $akun_debit = '1121';
        } elseif (strpos(strtolower($metode), 'bni') !== false) {
            $akun_debit = '1122';
        } elseif (strpos(strtolower($metode), 'mandiri') !== false) {
            $akun_debit = '1123';
        }
        
        $kredit_nol = 0;
        $stmt_debit = mysqli_prepare($conn, "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                        VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_debit, "ssdssi", $tgl_bayar, $akun_debit, $nominal_final, $kredit_nol, $keterangan, $id_sumber);
        if (!mysqli_stmt_execute($stmt_debit)) {
            throw new Exception("Gagal posting jurnal (Debit): " . mysqli_error($conn));
        }

        $akun_kredit = '411';
        $debit_nol = 0;
        $stmt_kredit = mysqli_prepare($conn, "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                         VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt_kredit, "ssdssi", $tgl_bayar, $akun_kredit, $debit_nol, $nominal_final, $keterangan, $id_sumber);
        if (!mysqli_stmt_execute($stmt_kredit)) {
            throw new Exception("Gagal posting jurnal (Kredit): " . mysqli_error($conn));
        }

        // 4. Update tabel transaksi_sewa
        $stmt_update_trx = mysqli_prepare($conn, "UPDATE transaksi_sewa SET jumlah_bayar = jumlah_bayar + ? WHERE id_sewa = ?");
        mysqli_stmt_bind_param($stmt_update_trx, "di", $nominal_final, $id_sewa);
        if (!mysqli_stmt_execute($stmt_update_trx)) {
            throw new Exception("Gagal mengupdate jumlah bayar di transaksi: " . mysqli_error($conn));
        }

        // 5. Update status transaksi_sewa & ketersediaan mobil
        if ($total_setelah_bayar >= $total_tagihan) {
            // Check if the rental period has actually ended before setting it to 'selesai'
            $stmt_cek_time = mysqli_prepare($conn, "SELECT tanggal_sewa, lama_sewa FROM transaksi_sewa WHERE id_sewa = ?");
            mysqli_stmt_bind_param($stmt_cek_time, "i", $id_sewa);
            mysqli_stmt_execute($stmt_cek_time);
            $check_time = mysqli_stmt_get_result($stmt_cek_time);
            $time_data = mysqli_fetch_assoc($check_time);
            $end_date_str = date('Y-m-d', strtotime($time_data['tanggal_sewa'] . ' + ' . $time_data['lama_sewa'] . ' days'));
            $is_ended = (date('Y-m-d') >= $end_date_str);

            if ($is_ended) {
                $status_selesai = 'selesai';
                $stmt_selesai = mysqli_prepare($conn, "UPDATE transaksi_sewa SET status_sewa = ? WHERE id_sewa = ?");
                mysqli_stmt_bind_param($stmt_selesai, "si", $status_selesai, $id_sewa);
                if (!mysqli_stmt_execute($stmt_selesai)) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }
                
                $status_tersedia = 'tersedia';
                $stmt_mobil = mysqli_prepare($conn, "UPDATE mobil SET status_mobil = ? WHERE kode_mobil = ?");
                mysqli_stmt_bind_param($stmt_mobil, "ss", $status_tersedia, $kode_mobil);
                if (!mysqli_stmt_execute($stmt_mobil)) {
                    throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
                }
            } else {
                // Keep it running (berjalan) if the rental period is still ongoing
                $status_berjalan = 'berjalan';
                $stmt_berjalan = mysqli_prepare($conn, "UPDATE transaksi_sewa SET status_sewa = ? WHERE id_sewa = ?");
                mysqli_stmt_bind_param($stmt_berjalan, "si", $status_berjalan, $id_sewa);
                if (!mysqli_stmt_execute($stmt_berjalan)) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }
            }
        } else {
            $status_dp = 'DP';
            $stmt_dp = mysqli_prepare($conn, "UPDATE transaksi_sewa SET status_sewa = ? WHERE id_sewa = ?");
            mysqli_stmt_bind_param($stmt_dp, "si", $status_dp, $id_sewa);
            if (!mysqli_stmt_execute($stmt_dp)) {
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