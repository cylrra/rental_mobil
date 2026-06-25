<?php
include 'koneksi.php';
$id_sewa = $_REQUEST['id_sewa'] ?? '';

if (!$id_sewa) {
    header("Location: transaksi.php");
    exit();
}

$q_cek = mysqli_query($conn, "SELECT pake_supir FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
if ($q_cek && mysqli_num_rows($q_cek) > 0) {
    $r_cek = mysqli_fetch_assoc($q_cek);
    
    if ($r_cek['pake_supir'] == 'Ya') {
        $q_supir = mysqli_query($conn, "SELECT id_supir FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') = 0 LIMIT 1");
        
        if ($q_supir && mysqli_num_rows($q_supir) > 0) {
            $r_supir = mysqli_fetch_assoc($q_supir);
            $id_supir_auto = $r_supir['id_supir'];
            mysqli_query($conn, "UPDATE transaksi_sewa SET id_supir = '$id_supir_auto', status_sewa = 'diterima' WHERE id_sewa = '$id_sewa'");
            mysqli_query($conn, "UPDATE supir SET status_supir = 'bertugas' WHERE id_supir = '$id_supir_auto'");
        } else {
            echo "<script>alert('Gagal ACC: Tidak ada supir yang tersedia saat ini!'); window.location='transaksi.php';</script>";
            exit();
        }
    } else {
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'diterima' WHERE id_sewa = '$id_sewa'");
    }
}

// Auto-start logic
$check_date = mysqli_query($conn, "SELECT tanggal_sewa FROM transaksi_sewa WHERE id_sewa = '$id_sewa'");
if ($check_date && mysqli_num_rows($check_date) > 0) {
    $row = mysqli_fetch_assoc($check_date);
    if (strtotime($row['tanggal_sewa']) <= strtotime(date('Y-m-d'))) {
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'berjalan' WHERE id_sewa = '$id_sewa'");
    }
}

header("Location: transaksi.php?acc_wa_id=" . urlencode($id_sewa));
exit();
?>
