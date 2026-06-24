<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Ambil ID dari URL (id_sewa)
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($id)) {
    die("<div style='text-align:center; margin-top:50px; font-family: sans-serif;'><h3>Parameter ID tidak ditemukan.</h3></div>");
}

// Ambil data dari transaksi_sewa dan pelanggan
$query_ts = "SELECT t.*, p.nama as nama, p.alamat, m.merk, m.jenis 
             FROM transaksi_sewa t
             JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
             JOIN mobil m ON t.kode_mobil = m.kode_mobil
             WHERE t.id_sewa = '$id'";
$res_ts = mysqli_query($conn, $query_ts);
$row_ts = mysqli_fetch_assoc($res_ts);

if (!$row_ts) {
    die("<div style='text-align:center; margin-top:50px; font-family: sans-serif;'><h3>Data invoice / sewa tidak ditemukan.</h3></div>");
}

// LOGIKA HITUNGAN:
$total_semua = (int)$row_ts['total_bayar'];
$sudah_dibayar = (int)$row_ts['jumlah_bayar'];
$sisa_tagihan = $total_semua - $sudah_dibayar;

// Mapping format data agar kompatibel dengan layout
$row = [
    'no_invoice' => 'INV-' . str_pad($row_ts['id_sewa'], 5, '0', STR_PAD_LEFT),
    'tanggal_invoice' => $row_ts['tanggal_sewa'],
    'jatuh_tempo' => date('Y-m-d', strtotime("+" . $row_ts['lama_sewa'] . " days", strtotime($row_ts['tanggal_sewa']))),
    'nama' => $row_ts['nama'],
    'alamat' => $row_ts['alamat'],
    'kode_mobil' => $row_ts['kode_mobil'] . " - " . $row_ts['merk'] . " " . $row_ts['jenis'],
    'lama_sewa' => $row_ts['lama_sewa'],
    'total_awal' => $total_semua,
    'terbayar' => $sudah_dibayar,
    'sisa_tagihan' => $sisa_tagihan,
    'potongan_diskon' => 0.00,
    'status_pembayaran' => ($sisa_tagihan <= 0) ? 'LUNAS' : (($sudah_dibayar > 0) ? 'DP' : 'BELUM LUNAS'),
    'catatan' => 'Terima kasih telah mempercayakan perjalanan Anda kepada PT INDOMAX RENTAL.'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $row['no_invoice']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8fafc; color: #0f172a; padding: 40px 0; }
        .invoice-box { background-color: white; border: 1px solid #e2e8f0; border-radius: 20px; max-width: 800px; margin: auto; padding: 45px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04); }
        .invoice-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 30px; }
        .brand-title { font-weight: 800; color: #0f172a; }
        .brand-title span { color: #9e0000; }
        .table-invoice th { background-color: #f8fafc; font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; }
        .btn-print { background-color: #0f172a; color: white; border: none; border-radius: 50px; padding: 10px 24px; font-weight: 600; }
        @media print { .btn-print, .btn-back { display: none !important; } }
    </style>
</head>
<body>

    <div class="invoice-box">
        <div class="d-flex justify-content-between mb-4 btn-back">
            <a href="riwayat_pembayaran.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button class="btn btn-sm btn-print" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak Invoice</button>
        </div>

        <div class="invoice-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="brand-title mb-1">🚗 INDOMAX<span>RENTAL</span></h3>
                <p class="text-muted small mb-0">Solo, Jawa Tengah | support@indomax.com</p>
            </div>
            <div class="text-end">
                <h2 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #9e0000;">Invoice</h2>
                <strong class="text-muted">No: #<?php echo $row['no_invoice']; ?></strong>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <strong class="text-uppercase text-muted small d-block mb-1">Ditujukan Kepada:</strong>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($row['nama']); ?></h5>
                <p class="text-muted small mb-0"><?php echo htmlspecialchars($row['alamat']); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal:</strong> <?php echo date('d M Y', strtotime($row['tanggal_invoice'])); ?></p>
            </div>
        </div>

        <table class="table align-middle table-invoice">
            <thead>
                <tr>
                    <th class="ps-3">Deskripsi Layanan</th>
                    <th class="text-end pe-3">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="ps-3 py-3">Total Biaya Sewa</td>
                    <td class="text-end pe-3 py-3 fw-bold">Rp <?php echo number_format($row['total_awal'], 0, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="ps-3 py-3 text-success">Sudah Dibayar</td>
                    <td class="text-end pe-3 py-3 fw-bold text-success">- Rp <?php echo number_format($row['terbayar'], 0, ',', '.'); ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="font-size: 1.2rem;">
                    <th class="text-end py-4">SISA TAGIHAN</th>
                    <td class="text-end pe-3 text-primary fw-extrabold py-4">Rp <?php echo number_format($row['sisa_tagihan'], 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-4 text-center">
            <div class="p-3 border rounded-4 bg-light">
                <span class="small text-muted text-uppercase fw-bold">Status Pembayaran</span>
                <h4 class="text-uppercase my-1"><?php echo $row['status_pembayaran']; ?></h4>
            </div>
        </div>
    </div>
</body>
</html>