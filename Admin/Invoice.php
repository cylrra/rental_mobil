<?php
include "koneksi.php";

// Mengambil ID dari URL (disarankan menggunakan no_invoice atau id_sewa)
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($id)) {
    die("Data tidak ditemukan.");
}

// SQL Query yang menghubungkan tabel invoice, pelanggan, dan transaksi_sewa
// Sesuaikan nama tabel 'invoice' jika di database Anda berbeda
$query = "SELECT * FROM invoice 
          JOIN pelanggan ON invoice.id_pelanggan = pelanggan.id_pelanggan 
          JOIN transaksi_sewa ON invoice.id_sewa = transaksi_sewa.id_sewa
          WHERE invoice.no_invoice = '$id' OR invoice.id_sewa = '$id'";

$data = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($data);

if (!$row) {
    die("Data invoice tidak ditemukan di database.");
}
?>

<div id="invoice-box" style="padding: 30px; border: 1px solid #ddd; max-width: 800px; margin: auto; font-family: sans-serif;">
    <table width="100%">
        <tr>
            <td>
                <h2 style="margin: 0;">INDOMAX RENTAL</h2>
                <p>Solo, Jawa Tengah</p>
            </td>
            <td align="right">
                <h1 style="color: #444; margin: 0;">INVOICE</h1>
                <strong>No: #<?php echo $row['no_invoice']; ?></strong>
            </td>
        </tr>
    </table>
    <hr>
    
    <table width="100%" style="margin-top: 20px;">
        <tr>
            <td>
                <strong>Kepada:</strong><br>
                <?php echo $row['nama_pelanggan']; ?><br>
                <?php echo $row['alamat']; // Pastikan kolom ini ada di tabel pelanggan ?>
            </td>
            <td align="right" valign="top">
                <strong>Tanggal:</strong> <?php echo date('d/m/Y', strtotime($row['tanggal_invoice'])); ?><br>
                <strong>Jatuh Tempo:</strong> <?php echo date('d/m/Y', strtotime($row['jatuh_tempo'])); ?>
            </td>
        </tr>
    </table>

    <table border="1" width="100%" cellspacing="0" cellpadding="10" style="margin-top: 30px; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa;">
                <th>Deskripsi Sewa</th>
                <th width="150">Harga Satuan</th>
                <th width="150">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Sewa Mobil (<?php echo $row['kode_mobil']; ?>)<br>
                    <small>Durasi: <?php echo $row['lama_sewa']; ?> Hari</small>
                </td>
                <td align="right">Rp <?php echo number_format($row['subtotal'] / $row['lama_sewa'], 0, ',', '.'); ?></td>
                <td align="right">Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" align="right">Subtotal</th>
                <td align="right">Rp <?php echo number_format($row['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th colspan="2" align="right">Diskon</th>
                <td align="right">- Rp <?php echo number_format($row['potongan_diskon'], 0, ',', '.'); ?></td>
            </tr>
            <tr style="background: #eee; font-size: 1.2em;">
                <th colspan="2" align="right">TOTAL AKHIR</th>
                <td align="right"><strong>Rp <?php echo number_format($row['total_akhir'], 0, ',', '.'); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <strong>Catatan:</strong><br>
        <?php echo $row['catatan'] ? $row['catatan'] : '-'; ?>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>Status Pembayaran: <strong><?php echo strtoupper($row['status_pembayaran']); ?></strong></p>
        <br><br>
        <p>( Bagian Keuangan )</p>
        <style> @media print { .btn-print { display: none; } } </style>
        <button class="btn-print" onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Sekarang</button>
    </div>
</div>