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
    
    $id_pelanggan  = $_SESSION['id_pelanggan'];
    $kode_mobil    = $_POST['kode_mobil'];
    $nama_penyewa  = $_POST['nama_penyewa'];
    $tanggal_sewa  = $_POST['tanggal_sewa'];
    $lama_sewa     = (int)$_POST['lama_sewa'];
    $lokasi_jemput = $_POST['lokasi_jemput'];
    $alamat_detail = $_POST['alamat_detail'];
    $lokasi_kembali = $_POST['lokasi_kembali'] ?? 'Kembalikan ke Kantor';
    $alamat_kembali = $_POST['alamat_kembali'] ?? '';
    
    // Logika supir: Jika kirim "999" maka pakai supir, jika tidak maka NULL/Lepas Kunci
    $id_supir = (isset($_POST['id_supir']) && $_POST['id_supir'] == '999') ? 999 : NULL;
    $pake_supir = ($id_supir !== NULL) ? 'Ya' : 'Tidak';
    $status_sewa   = 'pending';

    // AMBIL TARIF DARI DB
    $q_tarif = mysqli_query($conn, "SELECT tarif_per_hari FROM mobil WHERE kode_mobil = '$kode_mobil'");
    if (!$q_tarif) {
        echo "Error: Gagal mengambil data tarif.";
        exit();
    }
    $d_tarif = mysqli_fetch_assoc($q_tarif);
    $tarif_mobil = $d_tarif['tarif_per_hari'] ?? 0;
    
    // Tarif supir tetap 200rb
    $tarif_supir = ($id_supir !== NULL) ? 200000 : 0;
    $biaya_supir = $tarif_supir * $lama_sewa;
    
    // HITUNG TOTAL
    $total_biaya = ($tarif_mobil + $tarif_supir) * $lama_sewa;
    $total_bayar = $total_biaya;

    // INSERT KE DB
    $stmt = $conn->prepare("INSERT INTO transaksi_sewa 
            (id_pelanggan, kode_mobil, nama_penyewa, pake_supir, id_supir, biaya_supir, tanggal_sewa, lama_sewa, lokasi_jemput, alamat_detail, lokasi_kembali, alamat_kembali, status_sewa, total_biaya, total_bayar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("isssidsisssssdi", $id_pelanggan, $kode_mobil, $nama_penyewa, $pake_supir, $id_supir, $biaya_supir, $tanggal_sewa, $lama_sewa, $lokasi_jemput, $alamat_detail, $lokasi_kembali, $alamat_kembali, $status_sewa, $total_biaya, $total_bayar);

        if ($stmt->execute()) {
            // KIRIM RESPONSE "sukses" agar AJAX di transaksi.php tahu proses selesai
            echo "sukses"; 
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