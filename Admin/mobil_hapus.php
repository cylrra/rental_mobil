<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya admin yang boleh melakukan penghapusan data
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Akses Ditolak! Anda tidak memiliki izin untuk menghapus data.');
            window.location.href = 'mobil.php';
          </script>";
    exit();
}

include 'koneksi.php';

// Pastikan ada parameter kode yang dikirim dari URL
if (isset($_GET['kode'])) {
    $kode_mobil = mysqli_real_escape_string($conn, $_GET['kode']);

    // 1. Ambil nama gambar terlebih dahulu untuk dihapus dari folder img/
    $query_gambar = mysqli_query($conn, "SELECT Gambar, merk, nopol FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data = mysqli_fetch_assoc($query_gambar);
    
    if ($data) {
        $nama_gambar = $data['Gambar'];
        $path_gambar = "img/" . $nama_gambar;
        
        // Hapus file gambar dari folder lokal jika filenya ada
        if (!empty($nama_gambar) && file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }

    // 2. Jalankan query hapus data dari database
    $delete = mysqli_query($conn, "DELETE FROM mobil WHERE kode_mobil = '$kode_mobil'");

    if ($delete) {
        echo "<script>
                alert('Data armada " . $data['merk'] . " (" . $data['nopol'] . ") berhasil dihapus!');
                window.location.href = 'mobil.php';
              </script>";
    } else {
        // Peringatan ini penting jika mobil gagal dihapus karena masih ada riwayat transaksinya (Foreign Key Restrict)
        echo "<script>
                alert('Gagal menghapus data! Mobil ini kemungkinan masih terikat dengan data transaksi penyewaan.');
                window.location.href = 'mobil.php';
              </script>";
    }
} else {
    header("Location: mobil.php");
    exit();
}
?>