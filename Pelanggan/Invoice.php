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

// 1. Coba ambil dari tabel invoice
$query_inv = "SELECT i.*, p.nama, p.alamat, t.kode_mobil, t.lama_sewa, t.total_biaya 
              FROM invoice i
              JOIN pelanggan p ON i.id_pelanggan = p.id_pelanggan 
              JOIN transaksi_sewa t ON i.id_sewa = t.id_sewa
              WHERE i.no_invoice = '$id' OR i.id_sewa = '$id'";
$res_inv = mysqli_query($conn, $query_inv);
$row = mysqli_fetch_assoc($res_inv);

// 2. Fallback: Jika tidak ditemukan di tabel invoice, buat invoice dinamis dari transaksi_sewa
if (!$row) {
    $query_ts = "SELECT t.*, p.nama, p.alamat, m.merk, m.jenis 
                 FROM transaksi_sewa t
                 JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                 JOIN mobil m ON t.kode_mobil = m.kode_mobil
                 WHERE t.id_sewa = '$id'";
    $res_ts = mysqli_query($conn, $query_ts);
    $row_ts = mysqli_fetch_assoc($res_ts);
    
    if (!$row_ts) {
        die("<div style='text-align:center; margin-top:50px; font-family: sans-serif;'><h3>Data invoice / sewa tidak ditemukan di database.</h3></div>");
    }
    
    // Mapping format data agar kompatibel dengan layout
    $row = [
        'no_invoice' => 'INV-' . str_pad($row_ts['id_sewa'], 5, '0', STR_PAD_LEFT),
        'tanggal_invoice' => $row_ts['tanggal_sewa'],
        'jatuh_tempo' => date('Y-m-d', strtotime("+" . $row_ts['lama_sewa'] . " days", strtotime($row_ts['tanggal_sewa']))),
        'nama' => $row_ts['nama'],
        'alamat' => $row_ts['alamat'],
        'kode_mobil' => $row_ts['kode_mobil'] . " - " . $row_ts['merk'] . " " . $row_ts['jenis'],
        'lama_sewa' => $row_ts['lama_sewa'],
        'subtotal' => $row_ts['total_biaya'],
        'potongan_diskon' => 0.00,
        'total_akhir' => $row_ts['total_biaya'],
        'status_pembayaran' => ($row_ts['status_sewa'] == 'selesai') ? 'lunas' : (($row_ts['status_sewa'] == 'DP') ? 'dp' : 'belum lunas'),
        'catatan' => 'Generated dynamically from transaction log.'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $row['no_invoice']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            padding: 40px 0;
        }
        .invoice-box {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            max-width: 800px;
            margin: auto;
            padding: 45px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }
        .invoice-header {
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
        .brand-title {
            font-weight: 800;
            color: #0f172a;
        }
        .brand-title span {
            color: #2563eb;
        }
        .table-invoice th {
            background-color: #f8fafc;
            font-weight: 700;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .btn-print {
            background-color: #0f172a;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            background-color: #1e3a8a;
            color: white;
        }
        @media print {
            body { background: none; padding: 0; }
            .invoice-box { border: none; box-shadow: none; padding: 0; }
            .btn-print, .btn-back { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <!-- Top Toolbar (Hidden on print) -->
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
                <h2 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; color: #1e3a8a;">Invoice</h2>
                <strong class="text-muted">No: #<?php echo $row['no_invoice']; ?></strong>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-md-6">
                <strong class="text-uppercase text-muted small d-block mb-1">Ditujukan Kepada:</strong>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($row['nama']); ?></h5>
                <p class="text-muted small mb-0" style="max-width: 300px;"><?php echo htmlspecialchars($row['alamat']); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Tanggal Invoice:</strong> <?php echo date('d M Y', strtotime($row['tanggal_invoice'])); ?></p>
                <p class="mb-0"><strong>Jatuh Tempo:</strong> <?php echo date('d M Y', strtotime($row['jatuh_tempo'])); ?></p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table align-middle table-invoice">
                <thead>
                    <tr>
                        <th class="ps-3">Deskripsi Layanan</th>
                        <th class="text-center" width="100">Durasi</th>
                        <th class="text-end pe-3" width="180">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 py-3">
                            <span class="fw-bold text-dark">Sewa Kendaraan</span>
                            <div class="text-muted small mt-1">Armada: <?php echo htmlspecialchars($row['kode_mobil']); ?></div>
                        </td>
                        <td class="text-center py-3"><?php echo $row['lama_sewa']; ?> Hari</td>
                        <td class="text-end pe-3 py-3 fw-bold">Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end text-muted small py-2">Subtotal</td>
                        <td class="text-end pe-3 fw-semibold py-2">Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end text-muted small py-2">Diskon/Potongan</td>
                        <td class="text-end pe-3 text-danger py-2">- Rp <?php echo number_format($row['potongan_diskon'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="table-group-divider" style="font-size: 1.15rem;">
                        <th colspan="2" class="text-end py-3">Total Akhir</th>
                        <td class="text-end pe-3 text-primary fw-extrabold py-3">Rp <?php echo number_format($row['total_akhir'], 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row align-items-end">
            <div class="col-md-7 mb-3 mb-md-0">
                <strong class="small text-muted d-block mb-1">Catatan Tambahan:</strong>
                <p class="text-muted small mb-0"><?php echo !empty($row['catatan']) ? htmlspecialchars($row['catatan']) : 'Terima kasih telah mempercayakan perjalanan Anda kepada PT INDOMAX RENTAL.'; ?></p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="bg-light p-3 rounded-4 border text-center">
                    <span class="small text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Status Pembayaran</span>
                    <strong class="text-uppercase text-dark fs-5"><?php echo strtoupper($row['status_pembayaran']); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>