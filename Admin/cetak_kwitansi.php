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

// 2. Query yang diperluas untuk menarik data Pelanggan, Mobil, dan Supir via Transaksi_Sewa
$sql = "SELECT p.*, pl.nama AS nama_pelanggan, t.id_sewa, t.pake_supir, t.biaya_supir, 
               m.nama_mobil, m.plat_nomor, s.nama_supir
        FROM pembayaran p
        JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
        JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
        JOIN mobil m ON t.id_mobil = m.id_mobil
        LEFT JOIN supir s ON t.id_supir = s.id_supir
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
        body { font-family: 'Courier New', Courier, monospace; color: #333; background-color: #f4f4f4; padding: 20px; }
        .kwitansi-container { 
            width: 850px; 
            margin: 0 auto; 
            padding: 30px; 
            border: 2px solid #333; 
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px; }
        .content { line-height: 1.8; }
        .row-data { display: flex; border-bottom: 1px dotted #ccc; padding: 6px 0; }
        .label { width: 240px; font-weight: bold; }
        .val-data { flex: 1; }
        .amount-section { margin-top: 25px; display: flex; flex-direction: column; gap: 5px; }
        .amount-box { 
            background: #f9f9f9; 
            padding: 10px 20px; 
            font-size: 1.3em; 
            font-weight: bold; 
            display: inline-block; 
            border: 2px solid #333;
            width: fit-content;
        }
        .terbilang-text { 
            font-style: italic; 
            background: #eee; 
            padding: 8px 12px; 
            display: block; 
            font-size: 0.95em;
            border-left: 4px solid #333;
        }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .actions-btn { text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 10px; }
        
        .btn { padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; border-radius: 5px; text-decoration: none; font-family: Arial, sans-serif; }
        .btn-print { background: #28a745; color: white; }
        .btn-invoice { background: #17a2b8; color: white; }

        @media print { 
            .actions-btn { display: none; } 
            body { background: none; padding: 0; }
            .kwitansi-container { box-shadow: none; border: 2px solid #000; margin: 0; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="kwitansi-container">
        <div class="header">
            <h1 style="margin: 0; letter-spacing: 2px;">KWITANSI PEMBAYARAN</h1>
            <p style="margin: 5px 0; font-size: 1.2em;"><strong>PT INDOMAX RENTAL MOBIL</strong></p>
            <p style="margin: 0; font-size: 0.9em;">Jl. Raya Solo - Semarang, Jawa Tengah</p>
        </div>

        <div class="content">
            <div class="row-data">
                <div class="label">No. Kwitansi</div>
                <div class="val-data">: #PYM-<?php echo str_pad($row['id_pembayaran'], 5, "0", STR_PAD_LEFT); ?></div>
            </div>
            <div class="row-data">
                <div class="label">No. Invoice / Sewa</div>
                <div class="val-data">: #INV-<?php echo $row['id_sewa']; ?></div>
            </div>
            <div class="row-data">
                <div class="label">Telah Terima Dari</div>
                <div class="val-data">: <strong><?php echo strtoupper($row['nama_pelanggan']); ?></strong></div>
            </div>
            <div class="row-data">
                <div class="label">Untuk Pembayaran</div>
                <div class="val-data">: 
                    Sewa Mobil <strong><?php echo $row['nama_mobil']; ?></strong> (<?php echo $row['plat_nomor']; ?>) 
                    <?php echo ($row['pake_supir'] == 'Ya') ? " + Jasa Supir (" . $row['nama_supir'] . ")" : " (Lepas Kunci)"; ?>
                </div>
            </div>
            <div class="row-data">
                <div class="label">Jenis Pembayaran</div>
                <div class="val-data">: <?php echo strtoupper($row['jenis_pembayaran']); ?> (<?php echo $row['tipe_pembayaran']; ?>)</div>
            </div>
            <div class="row-data">
                <div class="label">Metode Pembayaran</div>
                <div class="val-data">: <?php echo !empty($row['metode_pembayaran']) ? strtoupper($row['metode_pembayaran']) : '-'; ?></div>
            </div>
            <div class="row-data">
                <div class="label">Tanggal Pembayaran</div>
                <div class="val-data">: <?php echo date('d F Y', strtotime($row['tanggal_bayar'])); ?></div>
            </div>

            <div class="amount-section">
                <div class="amount-box">
                    JUMLAH BAYAR: Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?>,-
                </div>
                <div class="terbilang-text">
                    Terbilang: <?php echo trim(terbilang($row['jumlah_bayar'])); ?> Rupiah
                </div>
            </div>
        </div>

        <div class="footer">
            <div style="text-align: center;">
                <p>Penyewa / Pelanggan,</p>
                <br><br><br>
                <p><strong>( <?php echo strtoupper($row['nama_pelanggan']); ?> )</strong></p>
            </div>
            <div style="text-align: center;">
                <p>Semarang, <?php echo date('d F Y', strtotime($row['tanggal_bayar'])); ?></p>
                <p>Bagian Kasir,</p>
                <br><br><br>
                <p><strong>( Admin Indomax )</strong></p>
            </div>
        </div>
    </div>

    <div class="actions-btn">
        <button class="btn btn-print" onclick="window.print()">Cetak Kwitansi (PDF)</button>
        <a href="Invoice.php?id=<?php echo $row['id_sewa']; ?>" class="btn btn-invoice">Lihat Detail Invoice</a>
    </div>

    <script>
        // Otomatis memicu cetak saat dokumen dimuat
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>