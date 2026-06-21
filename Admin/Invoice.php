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
        <strong>Catatan Khusus:</strong><br>
        <?php if($row['pake_supir'] == 'Ya'): ?>
            <p style="color: #e11d48; font-style: italic; margin-top: 5px;">* Biaya yang tercatat di atas HANYA mencakup Biaya Sewa Mobil dan Jasa Supir. Biaya tersebut <strong>BELUM TERMASUK</strong> Biaya Bensin (BBM), Biaya Tol, Parkir, serta Biaya Makan Supir selama perjalanan.</p>
        <?php else: ?>
            <p style="color: #e11d48; font-style: italic; margin-top: 5px;">* Biaya yang tercatat di atas HANYA mencakup Biaya Sewa Mobil (Lepas Kunci). Biaya tersebut <strong>BELUM TERMASUK</strong> Biaya Bensin (BBM), Biaya Tol, dan Parkir.</p>
        <?php endif; ?>
        
        <?php if(!empty($row['catatan'])): ?>
            <br><strong>Catatan Tambahan:</strong><br>
            <?php echo $row['catatan']; ?>
        <?php endif; ?>
    </div>

    <div style="margin-top: 50px; text-align: right;">
        <p>Status Pembayaran: <strong><?php echo strtoupper($row['status_pembayaran']); ?></strong></p>
        <br><br>
        <p>( Bagian Keuangan )</p>
        
        <?php
        // Generate WA text
        $wa_text = "Halo " . $row['nama_pelanggan'] . ", berikut adalah rincian tagihan sewa mobil Anda di Indomax Rental:\n\n";
        $wa_text .= "No. Invoice: #" . $row['no_invoice'] . "\n";
        $wa_text .= "Total Akhir: Rp " . number_format($row['total_akhir'], 0, ',', '.') . "\n";
        $wa_text .= "Jatuh Tempo: " . date('d/m/Y', strtotime($row['jatuh_tempo'])) . "\n\n";
        $wa_text .= "Harap segera melakukan pembayaran jika status belum lunas. Terima kasih.";
        $wa_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $row['no_telp']) . "?text=" . urlencode($wa_text);
        
        // Generate Email text
        $email_subj = "Invoice Indomax Rental #" . $row['no_invoice'];
        $email_url = "mailto:" . $row['email'] . "?subject=" . urlencode($email_subj) . "&body=" . urlencode($wa_text);
        ?>
        
        <style> 
            @media print { .btn-actions { display: none; } } 
            .btn { padding: 10px 20px; cursor: pointer; border: none; border-radius: 5px; font-weight: bold; text-decoration: none; display: inline-block; margin-left: 5px; }
            .btn-print { background: #3c4d70; color: white; }
            .btn-wa { background: #10b981; color: white; }
            .btn-email { background: #e11d48; color: white; }
        </style>
        
        <div class="btn-actions" style="margin-top: 20px;">
            <button class="btn btn-print" onclick="window.print()">Cetak Sekarang</button>
            <?php if(!empty($row['no_telp'])): ?>
                <a href="<?php echo $wa_url; ?>" target="_blank" class="btn btn-wa">Kirim via WA</a>
            <?php endif; ?>
            <?php if(!empty($row['email'])): ?>
                <a href="<?php echo $email_url; ?>" target="_blank" class="btn btn-email">Kirim via Email</a>
            <?php endif; ?>
        </div>
    </div>
</div>