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
               m.merk AS nama_mobil, m.nopol AS plat_nomor, s.nama_supir
        FROM pembayaran p
        JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
        JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
        JOIN mobil m ON t.kode_mobil = m.kode_mobil
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
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Gaya Umum Layar Browser */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            color: #333; 
            background-color: #f8f9fa; 
            padding: 30px; 
            margin: 0;
        }
        
        .kwitansi-container { 
            width: 780px; 
            margin: 0 auto; 
            padding: 25px; 
            border: 1px solid #e0e0e0; 
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Desain Header Berdasarkan Logo Gambar 3 */
        .header-brand {
            display: table;
            width: 100%;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a252f;
            margin-bottom: 20px;
        }
        .logo-section {
            display: table-cell;
            vertical-align: middle;
            width: 45%;
        }
        .logo-box {
            display: inline-block;
            background-color: #0f172a; 
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1.2em;
            letter-spacing: 1px;
        }
        .logo-box span {
            color: #38bdf8; 
            font-size: 0.75em;
            display: block;
            font-weight: normal;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .title-section {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 55%;
        }
        .title-section h1 {
            margin: 0;
            font-size: 1.5em;
            color: #1e293b;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .title-section p {
            margin: 4px 0 0 0;
            font-size: 0.9em;
            color: #475569;
        }

        /* Gaya Data Konten */
        .content { 
            margin-bottom: 15px;
        }
        .row-data { 
            display: table; 
            width: 100%;
            padding: 5px 0;
            font-size: 0.95em;
        }
        .label { 
            display: table-cell;
            width: 180px; 
            color: #64748b;
            font-weight: 500;
        }
        .val-data { 
            display: table-cell;
            color: #1e293b;
        }

        /* Kotak Nominal Jumlah Bayar */
        .amount-section { 
            margin: 15px 0; 
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 15px;
        }
        .amount-box { 
            font-size: 1.2em; 
            font-weight: bold; 
            color: #0f172a;
            margin-bottom: 5px;
        }
        .terbilang-text { 
            font-style: italic; 
            color: #475569;
            font-size: 0.88em;
            padding-left: 10px;
            border-left: 3px solid #38bdf8;
        }

        /* Tanda Tangan */
        .footer { 
            margin-top: 25px; 
            display: table;
            width: 100%;
        }
        .footer-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 0.9em;
        }
        .signature-space {
            height: 60px;
        }

        /* Tombol Aksi */
        .actions-btn { 
            text-align: center; 
            margin-top: 25px; 
            display: flex; 
            justify-content: center; 
            gap: 12px; 
        }
        .btn { 
            padding: 10px 22px; 
            border: none; 
            cursor: pointer; 
            font-weight: 600; 
            border-radius: 6px; 
            text-decoration: none; 
            font-family: Arial, sans-serif;
            font-size: 0.95em;
        }
        .btn-print { background: #0f172a; color: white; }
        .btn-invoice { background: #e2e8f0; color: #334155; }

        /* OPTIMASI SELESAI CETAK 1 HALAMAN (Mencegah Halaman Terpotong) */
        @media print { 
            @page { 
                size: A5 landscape; 
                margin: 0; 
            }
            html, body {
                background: #fff;
                padding: 0;
                margin: 0;
                height: 100%;
                max-height: 100vh;
                overflow: hidden; /* Mengunci paksa agar tidak membuat page ke-2 */
            }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 10mm 15mm; /* Margin aman printer */
            }
            .actions-btn { 
                display: none; 
            } 
            .kwitansi-container { 
                box-shadow: none; 
                border: none; 
                padding: 0; 
                width: 100%;
                max-width: 100%;
                height: auto;
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            /* Kompresi spacing khusus cetak agar hemat ruang vertical */
            .header-brand { padding-bottom: 10px; margin-bottom: 12px; }
            .content { margin-bottom: 10px; }
            .row-data { padding: 3px 0; font-size: 0.9em; }
            .amount-section { margin: 10px 0; padding: 10px 12px; }
            .footer { margin-top: 15px; }
            .signature-space { height: 40px; } /* Diperkecil agar muat aman di satu kertas */
        }
    </style>
</head>
<body>

    <div class="kwitansi-container">
        <div class="header-brand">
            <div class="logo-section">
                <div class="logo-box">
                    INDOMAX
                    <span>RENTAL SYSTEM</span>
                </div>
            </div>
            <div class="title-section">
                <h1>KWITANSI PEMBAYARAN</h1>
                <p><strong>PT INDOMAX RENTAL MOBIL</strong></p>
                <p style="font-size: 0.75em; margin-top: 2px; color:#64748b;">Jl. Raya Solo - Semarang, Jawa Tengah</p>
            </div>
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
                <div class="label">Jenis / Tipe</div>
                <div class="val-data">: <?php echo strtoupper($row['jenis_pembayaran']); ?> (<?php echo $row['tipe_pembayaran']; ?>)</div>
            </div>
            <div class="row-data">
                <div class="label">Metode Pembayaran</div>
                <div class="val-data">: <?php echo !empty($row['metode_pembayaran']) ? strtoupper($row['metode_pembayaran']) : '-'; ?></div>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-box">
                JUMLAH BAYAR: Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?>,-
            </div>
            <div class="terbilang-text">
                Terbilang: <?php echo trim(terbilang($row['jumlah_bayar'])); ?> Rupiah
            </div>
        </div>

        <div class="footer">
            <div class="footer-col">
                <p style="margin: 0 0 5px 0;">Penyewa / Pelanggan,</p>
                <div class="signature-space"></div>
                <p><strong>( <?php echo strtoupper($row['nama_pelanggan']); ?> )</strong></p>
            </div>
            <div class="footer-col">
                <p style="margin: 0 0 5px 0;">Semarang, <?php echo date('d F Y', strtotime($row['tanggal_bayar'])); ?></p>
                <p style="margin: 0 0 5px 0; color:#64748b;">Bagian Kasir,</p>
                <div class="signature-space"></div>
                <p><strong>( Admin Indomax )</strong></p>
            </div>
        </div>
    </div>

    <div class="actions-btn">
        <button class="btn btn-print" onclick="window.print()">Cetak Kwitansi (PDF)</button>
        <a href="Invoice.php?id=<?php echo $row['id_sewa']; ?>" class="btn btn-invoice">Lihat Detail Invoice</a>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>