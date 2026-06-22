<?php
include "koneksi.php";

// 1. Ambil ID Pembayaran dari URL dan bersihkan
$id = isset($_GET['id']) ? preg_replace('/[^0-9]/', '', $_GET['id']) : '';

if (empty($id)) {
    echo "<div style='text-align:center; margin-top:50px; font-family: Arial;'>
            <h3>Silahkan pilih data dari menu Riwayat Pembayaran terlebih dahulu.</h3>
            <a href='riwayat_pembayaran.php'>Kembali ke Riwayat Pembayaran</a>
          </div>";
    exit;
}

// 2. Query pembayaran
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

// Fungsi Terbilang
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Courier New', Courier, monospace; 
            color: #1e293b; 
            background-color: #f1f5f9; 
            padding: 40px 20px; 
        }
        .kwitansi-container { 
            max-width: 750px; 
            margin: 0 auto; 
            padding: 40px; 
            border: 2px solid #0f172a; 
            background: #fff;
            position: relative;
        }
        .kwitansi-header { 
            border-bottom: 3px double #0f172a; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .row-data { 
            display: flex; 
            border-bottom: 1px dotted #cbd5e1; 
            padding: 8px 0; 
            font-size: 1.05rem;
        }
        .label-col { 
            width: 220px; 
            font-weight: bold; 
        }
        .amount-section { 
            margin-top: 30px; 
            background: #f8fafc;
            border: 1px solid #0f172a;
            padding: 20px;
            border-radius: 4px;
        }
        .amount-box { 
            font-size: 1.4rem; 
            font-weight: 800; 
            color: #0f172a;
        }
        .terbilang-text { 
            font-style: italic; 
            font-size: 0.95rem;
            color: #475569;
            margin-top: 5px;
        }
        .kwitansi-footer { 
            margin-top: 40px; 
            display: flex; 
            justify-content: space-between; 
        }
        .btn-print { 
            margin: 20px auto 0; 
            display: block; 
            padding: 10px 25px; 
            background: #0f172a; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            border-radius: 50px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #1e3a8a;
        }
        @media print { 
            .btn-print { display: none !important; } 
            body { background: none; padding: 0; }
            .kwitansi-container { border: 2px solid #000; box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body>

    <div class="kwitansi-container">
        <!-- Stamp Watermark -->
        <div style="position: absolute; right: 80px; top: 120px; border: 3px solid rgba(37,99,235,0.15); color: rgba(37,99,235,0.15); font-size: 2rem; font-weight: 800; padding: 5px 15px; transform: rotate(-15deg); border-radius: 8px; text-transform: uppercase;">
            Paid
        </div>

        <div class="kwitansi-header text-center">
            <h2 class="fw-bold m-0" style="letter-spacing: 1px;">KWITANSI BUKTI PEMBAYARAN</h2>
            <h4 class="m-0 mt-1"><strong>PT INDOMAX RENTAL MOBIL</strong></h4>
            <p class="m-0 small text-muted">Solo - Semarang, Jawa Tengah | Telp: 0812345678</p>
        </div>

        <div class="content">
            <div class="row-data">
                <div class="label-col">No. Resi</div>
                <div>: #PYM-<?php echo str_pad($row['id_pembayaran'], 5, "0", STR_PAD_LEFT); ?></div>
            </div>
            <div class="row-data">
                <div class="label-col">Telah Terima Dari</div>
                <div>: <strong><?php echo strtoupper($row['nama']); ?></strong></div>
            </div>
            <div class="row-data">
                <div class="label-col">Untuk Pembayaran</div>
                <div>: Sewa Kendaraan (Order ID: #SRV-<?php echo $row['id_sewa']; ?>)</div>
            </div>
            <div class="row-data">
                <div class="label-col">Metode Bayar</div>
                <div class="text-uppercase">: <?php echo !empty($row['metode_pembayaran']) ? $row['metode_pembayaran'] : '-'; ?></div>
            </div>
            <div class="row-data">
                <div class="label-col">Tanggal Bayar</div>
                <div>: <?php echo date('d F Y', strtotime($row['tanggal_bayar'])); ?></div>
            </div>

            <div class="amount-section">
                <div class="amount-box">
                    NOMINAL: Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?>,-
                </div>
                <div class="terbilang-text">
                    Terbilang: <?php echo trim(terbilang($row['jumlah_bayar'])); ?> Rupiah
                </div>
            </div>
        </div>

        <div class="kwitansi-footer">
            <div class="text-center">
                <p class="mb-0">Penyewa,</p>
                <br><br>
                <p class="fw-bold mb-0">___________________</p>
            </div>
            <div class="text-center">
                <p class="mb-0">Semarang, <?php echo date('d M Y', strtotime($row['tanggal_bayar'])); ?></p>
                <p class="mb-0">Bagian Kasir,</p>
                <br><br>
                <p class="fw-bold mb-0"><strong>( Kasir Indomax )</strong></p>
            </div>
        </div>
    </div>

    <button class="btn-print shadow" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Kwitansi</button>

    <script>
        window.onload = function() {
            // Trigger automatic printing popup
            window.print();
        }
    </script>

</body>
</html>