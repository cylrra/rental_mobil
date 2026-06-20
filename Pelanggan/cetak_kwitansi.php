<?php
include "koneksi.php";

// 1. Ambil ID Pembayaran dari URL dan bersihkan agar hanya angka
$id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';

if (empty($id)) {
    echo "<div style='text-align:center; margin-top:50px; font-family: Arial;'>
            <h3>Silahkan pilih data dari menu Riwayat Pembayaran terlebih dahulu.</h3>
            <a href='riwayat_pembayaran.php'>Kembali ke Riwayat Pembayaran</a>
          </div>";
    exit;
}

// 2. Query yang sudah diperbaiki (pl.nama sesuai database kamu)
$sql = "SELECT p.*, pl.nama, t.id_sewa 
        FROM pembayaran p
        JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
        JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
        WHERE p.id_pembayaran = '$id'";

$query = mysqli_query($conn, $sql);

if (!$query || mysqli_num_rows($query) == 0) {
    die("<h3 style='text-align:center; margin-top:50px;'>Data pembayaran dengan ID #$id tidak ditemukan.</h3>");
}

$row = mysqli_fetch_assoc($query);

// Fungsi Terbilang untuk nominal rupiah
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $terbilang = "";
    if ($angka < 12) { $terbilang = " " . $baca[$angka]; }
    elseif ($angka < 20) { $terbilang = terbilang($angka - 10) . " Belas"; }
    elseif ($angka < 100) { $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10); }
    elseif ($angka < 200) { $terbilang = " Seratus" . terbilang($angka - 100); }
    elseif ($angka < 1000) { $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100); }
    elseif ($angka < 2000) { $terbilang = " Seribu" . terbilang($angka - 1000); }
    elseif ($angka < 1000000) { $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000); }
    elseif ($angka < 1000000000) { $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000); }
    return $terbilang;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi #<?php echo $row['id_pembayaran']; ?></title>
    <style>
        /* Desain Kwitansi */
        body { font-family: 'Courier New', Courier, monospace; color: #333; background-color: #f4f4f4; padding: 20px; }
        .kwitansi-container { 
            width: 800px; 
            margin: 0 auto; 
            padding: 30px; 
            border: 2px solid #333; 
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .content { line-height: 1.8; }
        .row-data { display: flex; border-bottom: 1px dotted #ccc; padding: 5px 0; }
        .label { width: 220px; font-weight: bold; }
        .amount-section { margin-top: 30px; }
        .amount-box { 
            background: #f9f9f9; 
            padding: 10px 20px; 
            font-size: 1.3em; 
            font-weight: bold; 
            display: inline-block; 
            border: 2px solid #333;
        }
        .terbilang-text { 
            font-style: italic; 
            background: #eee; 
            padding: 5px 10px; 
            display: block; 
            margin-top: 5px;
            font-size: 0.9em;
        }
        .footer { margin-top: 50px; display: flex; justify-content: space-between; }
        
        /* Tombol Cetak (Akan hilang saat di-print) */
        .btn-print { 
            margin: 20px auto; 
            display: block; 
            padding: 10px 25px; 
            background: #28a745; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            border-radius: 5px;
        }

        /* CSS khusus untuk mode cetak */
        @media print { 
            .btn-print { display: none; } 
            body { background: none; padding: 0; }
            .kwitansi-container { box-shadow: none; border: 2px solid #000; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="kwitansi-container">
        <div class="header">
            <h1 style="margin: 0;">KWITANSI PEMBAYARAN</h1>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>INDOMAX RENTAL MOBIL</strong></p>
            <p style="margin: 0; font-size: 0.9em;">Jl. Raya Solo - Semarang, Jawa Tengah</p>
        </div>

        <div class="content">
            <div class="row-data">
                <div class="label">No. Kwitansi</div>
                <div>: #PYM-<?php echo str_pad($row['id_pembayaran'], 5, "0", STR_PAD_LEFT); ?></div>
            </div>
            <div class="row-data">
                <div class="label">Telah Terima Dari</div>
                <div>: <strong><?php echo strtoupper($row['nama']); ?></strong></div>
            </div>
            <div class="row-data">
                <div class="label">Untuk Pembayaran</div>
                <div>: Sewa Kendaraan (ID Sewa: #SRV-<?php echo $row['id_sewa']; ?>)</div>
            </div>
            <div class="row-data">
                <div class="label">Metode Pembayaran</div>
                <div>: <?php echo !empty($row['metode_pembayaran']) ? $row['metode_pembayaran'] : '-'; ?></div>
            </div>
            <div class="row-data">
                <div class="label">Tanggal Bayar</div>
                <div>: <?php echo date('d F Y', strtotime($row['tanggal_bayar'])); ?></div>
            </div>

            <div class="amount-section">
                <div class="amount-box">
                    TOTAL: Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?>,-
                </div>
                <div class="terbilang-text">
                    Terbilang: <?php echo terbilang($row['jumlah_bayar']); ?> Rupiah
                </div>
            </div>
        </div>

        <div class="footer">
            <div style="text-align: center;">
                <p>Penerima,</p>
                <br><br><br>
                <p>___________________</p>
            </div>
            <div style="text-align: center;">
                <p>Semarang, <?php echo date('d M Y'); ?></p>
                <p>Bagian Kasir,</p>
                <br><br><br>
                <p><strong>( Admin Indomax )</strong></p>
            </div>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()">Klik Untuk Cetak Kwitansi</button>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>