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

    // 1. Ambil Tarif Harian Mobil dari database
    $query_mobil = mysqli_query($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data_mobil  = mysqli_fetch_assoc($query_mobil);
    $tarif_mobil = $data_mobil['tarif_per_hari'];

    // 2. Hitung Total Biaya Mobil komponen utama
    $total_biaya_mobil = $tarif_mobil * $lama_sewa;

    // 3. Ambil Tarif Harian Supir & Hitung Biaya Supir secara Terpisah (Jika pakai supir)
    $biaya_supir = 0;
    if ($id_supir !== null) {
        $tarif_supir = 200000; // Hardcode tagihan tambah 200 ribu
        
        // Kalkulasi biaya supir sesuai lama hari sewa
        $biaya_supir = $tarif_supir * $lama_sewa;
    }

    // 4. Hitung Akumulasi Total Harga Gabungan (Untuk masuk ke kolom total_harga / total_biaya)
    $total_harga = $total_biaya_mobil + $biaya_supir;

    // Siapkan nilai SQL untuk id_supir agar bisa bernilai NULL di database jika lepas kunci
    $id_supir_db = ($id_supir !== null) ? "'$id_supir'" : "NULL";

    // 5. Masukkan data ke tabel transaksi_sewa lengkap dengan kolom relasi supir yang baru
    // Sesuaikan nama kolom 'total_harga' atau 'total_biaya' sesuai struktur asli tabel Anda
    $query_insert = "INSERT INTO transaksi_sewa (id_pelanggan, kode_mobil, pake_supir, id_supir, biaya_supir, tanggal_sewa, lama_sewa, total_biaya, status_sewa) 
                     VALUES ('$id_pelanggan', '$kode_mobil', '$pake_supir', $id_supir_db, '$biaya_supir', '$tanggal_sewa', '$lama_sewa', '$total_harga', 'berjalan')";

    if (mysqli_query($conn, $query_insert)) {
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