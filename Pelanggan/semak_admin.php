<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum log masuk, atau log masuk tetapi BUKAN admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Tendang pengguna kembali ke halaman login pelanggan
    header("Location: ../login.php?mesej=akses_disekat");
    exit();
}
?>