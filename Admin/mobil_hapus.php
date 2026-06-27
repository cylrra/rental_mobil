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
    $stmt_gambar = mysqli_prepare($conn, "SELECT Gambar, merk, nopol FROM mobil WHERE kode_mobil = ?");
    mysqli_stmt_bind_param($stmt_gambar, "s", $kode_mobil);
    mysqli_stmt_execute($stmt_gambar);
    $query_gambar = mysqli_stmt_get_result($stmt_gambar);
    $data = mysqli_fetch_assoc($query_gambar);
    
    if ($data) {
        $nama_gambar = $data['Gambar'];
        // Do NOT delete the image for soft delete, we keep it for history
    }

    // 2. Jalankan query soft delete data dari database
    $stmt_del = mysqli_prepare($conn, "UPDATE mobil SET is_deleted = 1, status_mobil = 'tidak tersedia' WHERE kode_mobil = ?");
    mysqli_stmt_bind_param($stmt_del, "s", $kode_mobil);
    $delete = mysqli_stmt_execute($stmt_del);

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