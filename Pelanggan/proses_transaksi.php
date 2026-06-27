<?php
session_start();
include 'koneksi.php';
// Cek apakah request berasal dari form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pastikan user sudah login
    if (!isset($_SESSION['id_pelanggan'])) {
        echo "Error: Sesi berakhir, silakan login kembali.";
        exit();
    }
    
    // Ambil input dari form
    $id_pelanggan   = $_SESSION['id_pelanggan'];
    $kode_mobil     = $_POST['kode_mobil'];
    $nama_penyewa   = $_POST['nama_penyewa'];
    
    // Mengambil data dari tgl_mulai dan tgl_kembali
    $tgl_mulai      = $_POST['tgl_mulai']; 
    $tgl_kembali    = $_POST['tgl_kembali'];
    
    // Hitung lama sewa dalam hari
    $start_date = new DateTime($tgl_mulai);
    $end_date   = new DateTime($tgl_kembali);
    $interval   = $start_date->diff($end_date);
    $lama_sewa  = $interval->days ?: 1;

    // Hitung selisih jam untuk menentukan tarif sopir yang tepat
    $diff_seconds = $end_date->getTimestamp() - $start_date->getTimestamp();
    $diff_hours   = $diff_seconds / 3600;

    $lokasi_jemput  = $_POST['lokasi_jemput'];
    $alamat_detail  = $_POST['alamat_detail'] ?? '';
    $lokasi_kembali = $_POST['lokasi_kembali'] ?? 'Kembalikan ke Kantor';
    $alamat_kembali = $_POST['alamat_kembali'] ?? '';
    $jumlah         = (int)$_POST['jumlah'];
    $area_pemakaian = $_POST['area_pemakaian'] ?? 'Dalam Kota';
    
    // Logika supir
    $id_supir   = (isset($_POST['id_supir']) && $_POST['id_supir'] == '999') ? 999 : NULL;
    $pake_supir = ($id_supir !== NULL) ? 'Ya' : 'Tidak';
    $status_sewa = 'pending';

    $stmt_tarif = mysqli_prepare($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = ?");
    mysqli_stmt_bind_param($stmt_tarif, "s", $kode_mobil);
    mysqli_stmt_execute($stmt_tarif);
    $q_tarif = mysqli_stmt_get_result($stmt_tarif);
    if (!$q_tarif) {
        echo "Error: Gagal mengambil data tarif.";
        exit();
    }
    $d_tarif = mysqli_fetch_assoc($q_tarif);
    $tarif_mobil = $d_tarif['tarif_per_hari'] ?? 0;

    // Hitung biaya sopir sesuai keterangan UI (250k < 12 jam, 375k untuk 12-24 jam), per sesi
    $biaya_supir = 0;
    if ($id_supir !== NULL) {
        $sisa_jam = $diff_hours;
        while ($sisa_jam > 0) {
            $sesi_ini = min($sisa_jam, 24);
            $biaya_supir += ($sesi_ini <= 12) ? 250000 : 375000;
            $sisa_jam -= 24;
        }
    }

    // Biaya luar kota
    $biaya_luar_kota = ($area_pemakaian === 'Luar Kota') ? 100000 : 0;

    // Hitung total — tarif mobil x hari x jumlah + biaya sopir + luar kota (sinkron dengan JS)
    $total_biaya = ($tarif_mobil * $lama_sewa * $jumlah) + $biaya_supir + $biaya_luar_kota;
    $total_bayar = $total_biaya;

    // VERIFIKASI ID PELANGGAN (Cegah error Foreign Key)
    $stmt_cek = mysqli_prepare($conn, "SELECT id_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
    mysqli_stmt_bind_param($stmt_cek, "i", $id_pelanggan);
    mysqli_stmt_execute($stmt_cek);
    $check_pelanggan = mysqli_stmt_get_result($stmt_cek);
    if(mysqli_num_rows($check_pelanggan) == 0) {
        echo "Error: ID Pelanggan '$id_pelanggan' tidak ditemukan di tabel database pelanggan. Silakan Logout lalu Login kembali.";
        exit();
    }

    $awal_bayar = 0;

    // INSERT KE DB — 18 kolom, 18 variabel, 18 karakter bind string
    $stmt = $conn->prepare("INSERT INTO transaksi_sewa 
        (id_pelanggan, kode_mobil, nama_penyewa, pake_supir, id_supir, biaya_supir, tanggal_sewa, tanggal_kembali, lama_sewa, lokasi_jemput, alamat_detail, lokasi_kembali, alamat_kembali, status_sewa, total_biaya, total_bayar, jumlah_bayar, tujuan_perjalanan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isssidssisssssddis", 
            $id_pelanggan, $kode_mobil, $nama_penyewa, $pake_supir, $id_supir, 
            $biaya_supir, $tgl_mulai, $tgl_kembali, $lama_sewa, $lokasi_jemput, $alamat_detail, 
            $lokasi_kembali, $alamat_kembali, $status_sewa, $total_biaya, $total_bayar,
            $awal_bayar, $area_pemakaian
        );
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            echo "sukses|$insert_id"; 
        } else {
            echo "Error Database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error Query: " . $conn->error;
    }
} else {
    echo "Metode request tidak valid.";
}
?>
