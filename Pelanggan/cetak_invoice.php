<?php
session_start();
if (!isset($_SESSION['role'])) {
    die("Akses ditolak.");
}
include 'koneksi.php';

$id_sewa = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
if (empty($id_sewa)) {
    die("ID Transaksi tidak valid.");
}

$query = mysqli_query($conn, "SELECT t.*, m.merk, m.nopol, p.nama, p.no_telp, p.alamat 
                              FROM transaksi_sewa t 
                              JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                              JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                              WHERE t.id_sewa = '$id_sewa'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan.");
}

$lunas = ($data['jumlah_bayar'] >= $data['total_biaya']);
$status_bayar = $lunas ? "LUNAS" : ($data['jumlah_bayar'] > 0 ? "UANG MUKA (DP)" : "BELUM BAYAR");
$color_bayar = $lunas ? "#16a34a" : ($data['jumlah_bayar'] > 0 ? "#2563eb" : "#dc2626");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $data['id_sewa'] ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 32px; line-height: 45px; color: #800000; font-weight: bold; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        @media print {
            .invoice-box { box-shadow: none; border: 0; margin: 0; padding: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">INDOMAX RENTAL</td>
                            <td>
                                Invoice #: <strong><?= $data['id_sewa'] ?></strong><br>
                                Diterbitkan: <?= date('d M Y') ?><br>
                                Status: <strong style="color: <?= $color_bayar ?>;"><?= $status_bayar ?></strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>PT INDOMAX RENTAL</strong><br>
                                Jl. Sudirman No. 123, Jakarta<br>
                                CS: 0812-3456-7890
                            </td>
                            <td>
                                <strong>Tagihan Kepada:</strong><br>
                                <?= htmlspecialchars($data['nama']) ?><br>
                                <?= htmlspecialchars($data['no_telp']) ?><br>
                                <?= htmlspecialchars($data['alamat']) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Deskripsi Layanan</td>
                <td>Harga</td>
            </tr>
            <tr class="item">
                <td>
                    Sewa Mobil <strong><?= htmlspecialchars($data['merk']) ?> (<?= htmlspecialchars($data['nopol']) ?>)</strong><br>
                    <small>Tanggal: <?= date('d M Y H:i', strtotime($data['tanggal_sewa'])) ?> s/d <?= date('d M Y H:i', strtotime($data['tanggal_kembali'])) ?></small><br>
                    <small>Durasi: <?= $data['durasi_sewa'] ?> (<?= $data['lama_sewa'] ?> Paket)</small>
                </td>
                <td>
                    Rp <?= number_format($data['total_biaya'] - $data['biaya_supir'], 0, ',', '.') ?>
                </td>
            </tr>
            <?php if ($data['biaya_supir'] > 0): ?>
            <tr class="item">
                <td>Jasa Supir</td>
                <td>Rp <?= number_format($data['biaya_supir'], 0, ',', '.') ?></td>
            </tr>
            <?php endif; ?>
            <tr class="item last">
                <td>Area Pemakaian</td>
                <td><?= $data['area_pemakaian'] ?></td>
            </tr>
            <tr class="total">
                <td>Total Tagihan</td>
                <td>Rp <?= number_format($data['total_biaya'], 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td>Telah Dibayar</td>
                <td style="color: #2563eb;">Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.') ?></td>
            </tr>
            <tr class="total">
                <td>Sisa Tagihan</td>
                <td style="color: #dc2626;">Rp <?= number_format(max(0, $data['total_biaya'] - $data['jumlah_bayar']), 0, ',', '.') ?></td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 50px; font-size: 12px; color: #777;">
            <p>Terima kasih telah mempercayakan perjalanan Anda kepada INDOMAX RENTAL.</p>
            <button onclick="window.print()" style="padding: 10px 20px; background: #800000; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak Invoice PDF</button>
        </div>
    </div>
</body>
</html>
