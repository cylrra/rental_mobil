<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protection: Only admin can delete
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$id_pelanggan = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pelanggan > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM pelanggan WHERE id_pelanggan = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_pelanggan);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Data pelanggan berhasil dihapus!');
                window.location.href = 'pelanggan.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Gagal menghapus data pelanggan: " . addslashes(mysqli_error($conn)) . "');
                window.history.back();
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ID Pelanggan tidak valid!');
            window.location.href = 'pelanggan.php';
          </script>";
    exit();
}
?>
