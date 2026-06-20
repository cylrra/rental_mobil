<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Hancurkan semua data session
session_unset();
session_destroy();

// Alihkan kembali ke halaman login
header("Location: login_pelanggan.php");
exit();
?>
