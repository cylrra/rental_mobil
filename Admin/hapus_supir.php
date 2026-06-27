<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_supir = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Soft delete: Update is_deleted menjadi 1 tanpa menghapus gambar
    $stmt_del = mysqli_prepare($conn, "UPDATE supir SET is_deleted = 1, status_supir = 'bertugas' WHERE id_supir = ?");
    mysqli_stmt_bind_param($stmt_del, "s", $id_supir);
    $delete = mysqli_stmt_execute($stmt_del);
    
    if ($delete) {
        echo "<script>alert('Data supir berhasil dihapus!'); window.location.href='supir.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . mysqli_error($conn) . "'); window.location.href='supir.php';</script>";
    }
} else {
    header("Location: supir.php");
}
?>
