<?php
include "koneksi.php";

if (isset($_POST['simpan_pembayaran'])) {
    // 1. Menangkap data
    $id_sewa      = mysqli_real_escape_string($conn, $_POST['id_transaksi']); 
    $tgl_bayar    = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);     
    $jumlah_bayar = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
    $metode       = mysqli_real_escape_string($conn, $_POST['metode_bayar']);  
    $keterangan   = "Pembayaran Sewa Mobil ID: " . $id_sewa;

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
        $query_bayar = "INSERT INTO pembayaran (id_sewa, tanggal_bayar, jumlah_bayar, metode_pembayaran, keterangan) 
                        VALUES ('$id_sewa', '$tgl_bayar', '$jumlah_bayar', '$metode', '$keterangan')";
        
        if (!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Logika Akuntansi
        // Baris DEBIT: Kas (Akun 101) bertambah
        $q_debit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                        VALUES ('$tgl_bayar', '101', '$jumlah_bayar', 0, '$keterangan', '$id_sumber')";
        
        if (!mysqli_query($conn, $q_debit_sql)) {
            throw new Exception("Gagal posting jurnal (Debit): " . mysqli_error($conn));
        }

        // Baris KREDIT: Pendapatan Sewa (Akun 401) bertambah
        $q_kredit_sql = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                         VALUES ('$tgl_bayar', '401', 0, '$jumlah_bayar', '    $keterangan', '$id_sumber')";
        
        if (!mysqli_query($conn, $q_kredit_sql)) {
            throw new Exception("Gagal posting jurnal (Kredit): " . mysqli_error($conn));
        }

        // 4. Update status transaksi_sewa menjadi 'Selesai'
        if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'Selesai' WHERE id_sewa = '$id_sewa'")) {
            throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
        }

        // 4.5. UPDATE STATUS MOBIL menjadi 'tersedia'
        // PERBAIKAN: Menggunakan variabel '$kode_mobil' dan mengubah string menjadi 'tersedia' (huruf kecil semua) sesuai isi database
        $query_update_mobil = "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$kode_mobil'";
        if (!mysqli_query($conn, $query_update_mobil)) {
            throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
        }

        mysqli_commit($conn);
        echo "<script>alert('Pembayaran Berhasil, Jurnal Terbentuk, & Mobil Siap Disewa Kembali!'); window.location='riwayat_pembayaran.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<div style='color:red; padding:20px; border:1px solid red; font-family: sans-serif;'>";
        echo "<strong>Terjadi Kesalahan Sistem:</strong><br>" . $e->getMessage();
        echo "<br><br><a href='pembayaran.php' style='color: blue;'>Kembali ke Form</a>";
        echo "</div>";
    }
}
?>