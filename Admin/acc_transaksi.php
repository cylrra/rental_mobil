<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protection: Only admin can approve
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$id_sewa = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status_target = (isset($_GET['status']) && $_GET['status'] === 'diterima') ? 'diterima' : 'berjalan';

if ($id_sewa > 0) {
    // Check if the transaction exists
    $stmt_check = mysqli_prepare($conn, "SELECT status_sewa FROM transaksi_sewa WHERE id_sewa = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $id_sewa);
    mysqli_stmt_execute($stmt_check);
    $check_query = mysqli_stmt_get_result($stmt_check);
    if ($check_query && mysqli_num_rows($check_query) > 0) {
        $row = mysqli_fetch_assoc($check_query);
        if ($row['status_sewa'] === 'pending') {
            // Update status_sewa to target status
            $stmt_update = mysqli_prepare($conn, "UPDATE transaksi_sewa SET status_sewa = ? WHERE id_sewa = ?");
            mysqli_stmt_bind_param($stmt_update, "si", $status_target, $id_sewa);
            if (mysqli_stmt_execute($stmt_update)) {
                echo "<script>
                        alert('Pesanan #" . $id_sewa . " berhasil disetujui (ACC)!');
                        window.location = 'transaksi.php';
                      </script>";
                exit();
            } else {
                echo "<script>
                        alert('Gagal menyetujui pesanan: " . addslashes(mysqli_error($conn)) . "');
                        window.location = 'transaksi.php';
                      </script>";
                exit();
            }
        } else {
            echo "<script>
                    alert('Transaksi sudah dalam status " . htmlspecialchars($row['status_sewa']) . "!');
                    window.location = 'transaksi.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Transaksi tidak ditemukan!');
                window.location = 'transaksi.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('ID Transaksi tidak valid!');
            window.location = 'transaksi.php';
          </script>";
    exit();
}
?>
