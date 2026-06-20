<?php
include "koneksi.php";

// ==========================================
// PART 1: LOGIKA BACKEND PROSES SIMPAN (PHP)
// ==========================================
if (isset($_POST['simpan_pembayaran'])) {
    // 1. Menangkap data dari form input
    $id_sewa        = mysqli_real_escape_string($conn, $_POST['id_transaksi']); 
    $tgl_bayar      = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);     
    $jumlah_bayar   = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
    $metode         = mysqli_real_escape_string($conn, $_POST['metode_bayar']);  
    $jenis_bayar    = mysqli_real_escape_string($conn, $_POST['jenis_pembayaran']); // Menangkap opsi dp / pelunasan
    $keterangan     = "Pembayaran " . strtoupper($jenis_bayar) . " Sewa Mobil ID: " . $id_sewa;

    mysqli_begin_transaction($conn);

    try {
        // 1.5. AMBIL KODE_MOBIL dari transaksi_sewa terlebih dahulu
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

        // 2. Simpan ke Tabel Pembayaran (Kolom jenis_pembayaran ikut diisi)
        $query_bayar = "INSERT INTO pembayaran (id_sewa, jenis_pembayaran, metode_pembayaran, tanggal_bayar, jumlah_bayar, keterangan) 
                        VALUES ('$id_sewa', '$jenis_bayar', '$metode', '$tgl_bayar', '$jumlah_bayar', '$keterangan')";
        
        if (!mysqli_query($conn, $query_bayar)) {
            throw new Exception("Gagal simpan pembayaran: " . mysqli_error($conn));
        }

        $id_sumber = mysqli_insert_id($conn);
        
        // 3. Logika Akuntansi (Sesuai Nominal Input Apakah DP atau Lunas)
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

        // 4. Update status transaksi_sewa dan mobil jika pembayaran bersifat "pelunasan" / "Lunas"
        if ($jenis_bayar == 'pelunasan') {
            if (!mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'Selesai' WHERE id_sewa = '$id_sewa'")) {
                throw new Exception("Gagal update status transaksi sewa: " . mysqli_error($conn));
            }

            $query_update_mobil = "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$kode_mobil'";
            if (!mysqli_query($conn, $query_update_mobil)) {
                throw new Exception("Gagal mengubah status mobil menjadi tersedia: " . mysqli_error($conn));
            }
        } else {
            // Jika status masih DP, update status sewa menjadi sedang berjalan / dikonfirmasi sesuai alur bisnis Anda
            mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'DP' WHERE id_sewa = '$id_sewa'");
        }

        mysqli_commit($conn);
        echo "<script>alert('Pembayaran Berhasil Diproses, Jurnal Terbentuk!'); window.location='riwayat_pembayaran.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<div style='color:red; padding:20px; border:1px solid red; font-family: sans-serif;'>";
        echo "<strong>Terjadi Kesalahan Sistem:</strong><br>" . $e->getMessage();
        echo "<br><br><a href='pembayaran.php' style='color: blue;'>Kembali ke Form</a>";
        echo "</div>";
    }
}

// Simulasi nilai total harga sewa (Ganti atau ambil dari query sewa Anda jika form ini berada di halaman yang sama)
$total_harga = isset($total_harga) ? $total_harga : 500000; 
?>

<form action="" method="POST" style="font-family: sans-serif; padding: 20px; max-width: 500px;">
    <input type="hidden" name="id_transaksi" value="1"> <input type="hidden" name="metode_bayar" value="transfer"> <div class="form-group" style="margin-bottom: 15px;">
        <label>Tanggal Bayar</label>
        <input type="date" name="tgl_bayar" class="form-control" style="width: 100%; padding: 8px;" value="<?= date('Y-m-d'); ?>" required>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label>Metode Pembayaran</label>
        <select name="jenis_pembayaran" id="jenis_pembayaran" onchange="hitungPembayaran()" class="form-control" style="width: 100%; padding: 8px;" required>
            <option value="pelunasan">Langsung Lunas</option>
            <option value="dp">Uang Muka (DP 30%)</option>
        </select>
    </div>

    <div class="form-group" style="margin-bottom: 15px;">
        <label>Jumlah yang Harus Dibayar (Rp)</label>
        <input type="number" id="total_asli" value="<?= $total_harga; ?>" hidden>
        <input type="text" name="jumlah_bayar" id="jumlah_bayar" value="<?= $total_harga; ?>" readonly style="width: 100%; padding: 8px; background: #eee; font-weight: bold;">
    </div>

    <button type="submit" name="simpan_pembayaran" style="background: #e74c3c; color: white; padding: 10px 20px; border: none; cursor: pointer; width: 100%;">Konfirmasi Pembayaran</button>
</form>

<script>
function hitungPembayaran() {
    var tipe = document.getElementById('jenis_pembayaran').value;
    var totalAsli = parseFloat(document.getElementById('total_asli').value);
    var gantiBayar = document.getElementById('jumlah_bayar');

    if (tipe === 'dp') {
        // Otomatis memotong jumlah tagihan menjadi nominal DP 30%
        var dp = totalAsli * 0.3;
        gantiBayar.value = dp;
    } else {
        // Kembali ke harga asli (Lunas)
        gantiBayar.value = totalAsli;
    }
}
</script>