<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (isset($_POST['submit'])) {
    // Amankan data input form
    $id_pelanggan = mysqli_real_escape_string($conn, $_POST['id_pelanggan']);
    $kode_mobil   = mysqli_real_escape_string($conn, $_POST['kode_mobil']);
    $pake_supir   = mysqli_real_escape_string($conn, $_POST['pake_supir']); // Menangkap opsi 'Ya' / 'Tidak'
    $tanggal_sewa = mysqli_real_escape_string($conn, $_POST['tanggal_sewa']);
    $lama_sewa    = intval($_POST['lama_sewa']);

    // Validasi input id_supir berdasarkan pilihan layanan supir
    $id_supir = (isset($_POST['id_supir']) && !empty($_POST['id_supir']) && $pake_supir === 'Ya') 
                ? mysqli_real_escape_string($conn, $_POST['id_supir']) 
                : null;

    $durasi_sewa  = mysqli_real_escape_string($conn, $_POST['durasi_sewa']);
    $area_pemakaian = mysqli_real_escape_string($conn, $_POST['area_pemakaian']);

    // 1. Ambil Tarif Mobil dari database
    $stmt_mobil = mysqli_prepare($conn, "SELECT * FROM mobil WHERE kode_mobil = ?");
    mysqli_stmt_bind_param($stmt_mobil, "s", $kode_mobil);
    mysqli_stmt_execute($stmt_mobil);
    $query_mobil = mysqli_stmt_get_result($stmt_mobil);
    $data_mobil  = mysqli_fetch_assoc($query_mobil);
    
    if ($durasi_sewa == '12 Jam' && $area_pemakaian == 'Dalam Kota') {
        $tarif_mobil = $data_mobil['tarif_12_dalam'];
    } elseif ($durasi_sewa == '12 Jam' && $area_pemakaian == 'Luar Kota') {
        $tarif_mobil = $data_mobil['tarif_12_luar'];
    } elseif ($durasi_sewa == '24 Jam' && $area_pemakaian == 'Dalam Kota') {
        $tarif_mobil = $data_mobil['tarif_24_dalam'];
    } else {
        $tarif_mobil = $data_mobil['tarif_24_luar'];
    }

    // 2. Hitung Total Biaya Mobil komponen utama (lama_sewa adalah jumlah paket)
    $total_biaya_mobil = $tarif_mobil * $lama_sewa;

    // 3. Ambil Tarif Supir & Hitung Biaya Supir secara Terpisah (Jika pakai supir)
    $biaya_supir = 0;
    if ($id_supir !== null) {
        $stmt_sup = mysqli_prepare($conn, "SELECT * FROM supir WHERE id_supir = ?");
        mysqli_stmt_bind_param($stmt_sup, "s", $id_supir);
        mysqli_stmt_execute($stmt_sup);
        $query_supir = mysqli_stmt_get_result($stmt_sup);
        $data_supir = mysqli_fetch_assoc($query_supir);
        
        if ($durasi_sewa == '12 Jam' && $area_pemakaian == 'Dalam Kota') {
            $tarif_supir = $data_supir['tarif_12_dalam'];
        } elseif ($durasi_sewa == '12 Jam' && $area_pemakaian == 'Luar Kota') {
            $tarif_supir = $data_supir['tarif_12_luar'];
        } elseif ($durasi_sewa == '24 Jam' && $area_pemakaian == 'Dalam Kota') {
            $tarif_supir = $data_supir['tarif_24_dalam'];
        } else {
            $tarif_supir = $data_supir['tarif_24_luar'];
        }
        
        $biaya_supir = $tarif_supir * $lama_sewa;
    }

    // 4. Hitung Akumulasi Total Harga Gabungan (Untuk masuk ke kolom total_harga / total_biaya)
    $total_harga = $total_biaya_mobil + $biaya_supir;

    // Siapkan nilai SQL untuk id_supir agar bisa bernilai NULL di database jika lepas kunci
    $id_supir_db = ($id_supir !== null) ? "'$id_supir'" : "NULL";
    
    // Hitung tanggal kembali (untuk referensi pengembalian)
    $jam_tambah = ($durasi_sewa == '12 Jam') ? (12 * $lama_sewa) : (24 * $lama_sewa);
    $tanggal_kembali = date('Y-m-d H:i:s', strtotime($tanggal_sewa . " + $jam_tambah hours"));
    
    // CEK KETERSEDIAAN JADWAL (Overlapping Check)
    $stmt_cek = mysqli_prepare($conn, "SELECT id_sewa FROM transaksi_sewa 
                                         WHERE kode_mobil = ? 
                                         AND status_sewa IN ('pending', 'diterima', 'berjalan', 'dp')
                                         AND (
                                            (tanggal_sewa <= ? AND tanggal_kembali >= ?)
                                         )");
    mysqli_stmt_bind_param($stmt_cek, "sss", $kode_mobil, $tanggal_kembali, $tanggal_sewa);
    mysqli_stmt_execute($stmt_cek);
    $q_cek_jadwal = mysqli_stmt_get_result($stmt_cek);
    if (mysqli_num_rows($q_cek_jadwal) > 0) {
        echo "<script>
                alert('Gagal: Mobil tidak tersedia pada rentang waktu yang dipilih (Sudah dibooking).');
                window.history.back();
              </script>";
        exit();
    }

    // 5. Masukkan data ke tabel transaksi_sewa lengkap dengan kolom relasi supir yang baru
    $stmt_in = mysqli_prepare($conn, "INSERT INTO transaksi_sewa (id_pelanggan, kode_mobil, pake_supir, id_supir, biaya_supir, tanggal_sewa, tanggal_kembali, lama_sewa, durasi_sewa, area_pemakaian, total_biaya, status_sewa) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'berjalan')");
    mysqli_stmt_bind_param($stmt_in, "ssssississd", $id_pelanggan, $kode_mobil, $pake_supir, $id_supir, $biaya_supir, $tanggal_sewa, $tanggal_kembali, $lama_sewa, $durasi_sewa, $area_pemakaian, $total_harga);

    if (mysqli_stmt_execute($stmt_in)) {
        // Ketersediaan mobil & supir otomatis aman (real-time) selama status_sewa bernilai 'berjalan'
        echo "<script>
                alert('Transaksi Berhasil Disimpan!\\nTotal Sewa Mobil: Rp " . number_format($total_biaya_mobil, 0, ',', '.') . "\\nTotal Jasa Supir: Rp " . number_format($biaya_supir, 0, ',', '.') . "\\nAkumulasi Total: Rp " . number_format($total_harga, 0, ',', '.') . "');
                window.location = 'transaksi.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan transaksi: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: transaksi.php");
    exit();
}
?>