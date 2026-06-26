<?php
include "koneksi.php";

if (isset($_POST['simpan_pembayaran'])) {
    // 1. Menangkap data
    $id_sewa      = mysqli_real_escape_string($conn, $_POST['id_transaksi']); 
    $tgl_bayar    = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);     
    $jumlah_bayar = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
    $metode       = mysqli_real_escape_string($conn, $_POST['metode_bayar']);  
    $jenis_bayar  = mysqli_real_escape_string($conn, $_POST['jenis_pembayaran']);
    $bank_tujuan  = isset($_POST['bank_tujuan']) ? mysqli_real_escape_string($conn, $_POST['bank_tujuan']) : '';
    $keterangan   = "Pembayaran Sewa Mobil ID: " . $id_sewa;
    
    if ($metode === 'transfer' && !empty($bank_tujuan)) {
        $bank_name = '';
        if ($bank_tujuan == '1121') $bank_name = 'BCA';
        else if ($bank_tujuan == '1122') $bank_name = 'BNI';
        else if ($bank_tujuan == '1123') $bank_name = 'Mandiri';
        $keterangan .= " (Transfer $bank_name)";
    }

    mysqli_begin_transaction($conn);

    try {
        // 1.5. AMBIL KODE_MOBIL dari transaksi_sewa terlebih dahulu
        // PERBAIKAN: Mengubah 'id_mobil' menjadi 'kode_mobil' sesuai dengan nama kolom relasi di database
        $query_cari_mobil = "SELECT kode_mobil FROM transaksi_sewa WHERE id_sewa = '$id_sewa'";
        $result_mobil     = mysqli_query($conn, $query_cari_mobil);
        
        if (!$result_mobil) {
            throw new Exception("Gagal mengecek data transaksi sewa: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result_mobil) == 0) {
            throw new Exception("Data transaksi sewa tidak ditemukan.");
        }
        
        $data_mobil = mysqli_fetch_assoc($result_mobil);
        $kode_mobil = $data_mobil['kode_mobil'];

        // 2. Simpan ke Tabel Pembayaran
        $query_bayar = "INSERT INTO pembayaran (id_sewa, tanggal_bayar, jumlah_bayar, metode_pembayaran, jenis_pembayaran, keterangan) 
                        VALUES ('$id_sewa', '$tgl_bayar', '$jumlah_bayar', '$metode', '$jenis_bayar', '$keterangan')";
        
        if (!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Logika Akuntansi
        // Tentukan akun debit berdasarkan metode pembayaran
        $akun_debit = ($metode === 'transfer' && !empty($bank_tujuan)) ? $bank_tujuan : '111';

        // Baris DEBIT: Kas/Bank bertambah
        $q_debit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                        VALUES ('$tgl_bayar', '$akun_debit', '$jumlah_bayar', 0, '$keterangan', '$id_sumber')";
        
        if (!mysqli_query($conn, $q_debit_sql)) {
            throw new Exception("Gagal posting jurnal (Debit): " . mysqli_error($conn));
        }

        // Baris KREDIT: Pendapatan Sewa (Akun 411) bertambah
        $q_kredit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                         VALUES ('$tgl_bayar', '411', 0, '$jumlah_bayar', '    $keterangan', '$id_sumber')";
        
        if (!mysqli_query($conn, $q_kredit_sql)) {
            throw new Exception("Gagal posting jurnal (Kredit): " . mysqli_error($conn));
        }

        // UPDATE jumlah_bayar di transaksi_sewa
        $q_update_transaksi = "UPDATE transaksi_sewa SET jumlah_bayar = jumlah_bayar + $jumlah_bayar WHERE id_sewa = '$id_sewa'";
        if (!mysqli_query($conn, $q_update_transaksi)) {
            throw new Exception("Gagal mengupdate jumlah bayar di transaksi: " . mysqli_error($conn));
        }

        // 4. Update status transaksi_sewa dan mobil HANYA JIKA pelunasan
        if ($jenis_bayar === 'pelunasan') {
            // Check if the rental period has actually ended before setting it to 'selesai'
            $check_time = mysqli_query($conn, "SELECT tanggal_sewa, lama_sewa FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
            $time_data = mysqli_fetch_assoc($check_time);
            $end_date_str = date('Y-m-d', strtotime($time_data['tanggal_sewa'] . ' + ' . $time_data['lama_sewa'] . ' days'));
            $is_ended = (date('Y-m-d') >= $end_date_str);

            if ($is_ended) {
                if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = '$id_sewa'")) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }

                // UPDATE STATUS MOBIL menjadi 'tersedia'
                $query_update_mobil = "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$kode_mobil'";
                if (!mysqli_query($conn, $query_update_mobil)) {
                    throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
                }
            } else {
                // Keep it running (berjalan) if the rental period is still ongoing
                if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'berjalan' WHERE id_sewa = '$id_sewa'")) {
                    throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
                }
            }
        } else if ($jenis_bayar === 'dp') {
            if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'DP' WHERE id_sewa = '$id_sewa' AND status_sewa != 'berjalan'")) {
                throw new Exception("Gagal update status sewa (DP): " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        $alert_msg = ($jenis_bayar === 'pelunasan') ? 'Pelunasan Berhasil, Jurnal Terbentuk, & Mobil Siap Disewa Kembali!' : 'Pembayaran DP Berhasil & Jurnal Terbentuk!';
        echo "<script>alert('$alert_msg'); window.location='riwayat_pembayaran.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<div style='color:red; padding:20px; border:1px solid red; font-family: sans-serif;'>";
        echo "<strong>Terjadi Kesalahan Sistem:</strong><br>" . $e->getMessage();
        echo "<br><br><a href='pembayaran.php' style='color: blue;'>Kembali ke Form</a>";
        echo "</div>";
    }
}
?>