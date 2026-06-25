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
    
    $durasi_sewa   = $_POST['durasi_sewa'] ?? '24 Jam';
    $area_pemakaian = $_POST['area_pemakaian'] ?? 'Dalam Kota';
    
    // Logika supir: Jika kirim "999" maka pakai supir, jika tidak maka NULL/Lepas Kunci
    $id_supir = (isset($_POST['id_supir']) && $_POST['id_supir'] == '999') ? 999 : NULL;
    $pake_supir = ($id_supir !== NULL) ? 'Ya' : 'Tidak';
    $status_sewa   = 'pending';

    // AMBIL TARIF DARI DB
    $q_tarif = mysqli_query($conn, "SELECT tarif_12_dalam, tarif_12_luar, tarif_24_dalam, tarif_24_luar FROM mobil WHERE kode_mobil = '$kode_mobil'");
    if (!$q_tarif) {
        echo "Error: Gagal mengambil data tarif mobil.";
        exit();
    }
    $d_tarif = mysqli_fetch_assoc($q_tarif);
    
    $tarif_mobil = 0;
    if ($durasi_sewa === '12 Jam' && $area_pemakaian === 'Dalam Kota') {
        $tarif_mobil = $d_tarif['tarif_12_dalam'] ?? 0;
    } elseif ($durasi_sewa === '12 Jam' && $area_pemakaian === 'Luar Kota') {
        $tarif_mobil = $d_tarif['tarif_12_luar'] ?? 0;
    } elseif ($durasi_sewa === '24 Jam' && $area_pemakaian === 'Dalam Kota') {
        $tarif_mobil = $d_tarif['tarif_24_dalam'] ?? 0;
    } else {
        $tarif_mobil = $d_tarif['tarif_24_luar'] ?? 0;
    }
    
    // Tarif supir generic estimation
    $tarif_supir = 0;
    if ($id_supir !== NULL) {
        if ($durasi_sewa === '12 Jam' && $area_pemakaian === 'Dalam Kota') $tarif_supir = 100000;
        elseif ($durasi_sewa === '12 Jam' && $area_pemakaian === 'Luar Kota') $tarif_supir = 150000;
        elseif ($durasi_sewa === '24 Jam' && $area_pemakaian === 'Dalam Kota') $tarif_supir = 200000;
        else $tarif_supir = 300000;
    }
    $biaya_supir = $tarif_supir * $lama_sewa;
    
    // HITUNG TANGGAL KEMBALI
    $jam_tambah = ($durasi_sewa == '12 Jam') ? (12 * $lama_sewa) : (24 * $lama_sewa);
    $tanggal_kembali = date('Y-m-d H:i:s', strtotime($tanggal_sewa . " + $jam_tambah hours"));
    
    // CEK KETERSEDIAAN JADWAL (Overlapping Check)
    $q_cek_jadwal = mysqli_query($conn, "SELECT id_sewa FROM transaksi_sewa 
                                         WHERE kode_mobil = '$kode_mobil' 
                                         AND status_sewa IN ('pending', 'diterima', 'berjalan', 'dp')
                                         AND (
                                            (tanggal_sewa <= '$tanggal_kembali' AND tanggal_kembali >= '$tanggal_sewa')
                                         )");
    if (mysqli_num_rows($q_cek_jadwal) > 0) {
        echo "Error: Mobil tidak tersedia pada rentang waktu yang dipilih (Sudah dibooking).";
        exit();
    }
    
    // HITUNG TOTAL
    $total_biaya = ($tarif_mobil + $tarif_supir) * $lama_sewa;
    $total_bayar = $total_biaya;

    // INSERT KE DB
    $stmt = $conn->prepare("INSERT INTO transaksi_sewa 
            (id_pelanggan, kode_mobil, nama_penyewa, pake_supir, id_supir, biaya_supir, tanggal_sewa, tanggal_kembali, lama_sewa, durasi_sewa, area_pemakaian, lokasi_jemput, alamat_detail, lokasi_kembali, alamat_kembali, status_sewa, total_biaya, total_bayar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("isssidssisssssssdi", $id_pelanggan, $kode_mobil, $nama_penyewa, $pake_supir, $id_supir, $biaya_supir, $tanggal_sewa, $tanggal_kembali, $lama_sewa, $durasi_sewa, $area_pemakaian, $lokasi_jemput, $alamat_detail, $lokasi_kembali, $alamat_kembali, $status_sewa, $total_biaya, $total_bayar);

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