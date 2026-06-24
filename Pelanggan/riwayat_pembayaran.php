<?php
session_start();
include 'koneksi.php';
include 'navbar.php'; 

// Cek sesi login pelanggan
if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login_pelanggan.php");
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// 1. QUERY UNTUK MENGHITUNG STATISTIK SECARA MANUAL (AGAR SINKRON)
$q_data = mysqli_query($conn, "SELECT total_bayar, jumlah_bayar FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'");

$total_trx = 0;
$total_dibayar = 0;
$total_sisa = 0;

while($row = mysqli_fetch_assoc($q_data)) {
    $total_trx++;
    $total = (int)$row['total_bayar'];
    $raw_dibayar = (int)$row['jumlah_bayar'];
    
    // Logika pembatasan agar tidak minus/lebih
    $dibayar = ($raw_dibayar > $total) ? $total : $raw_dibayar;
    $sisa = $total - $dibayar;
    $sisa = ($sisa < 0) ? 0 : $sisa;
    
    $total_dibayar += $dibayar;
    $total_sisa += $sisa;
}
?>

<style>
    .btn-invoice { 
        background-color: #ffffff; 
        border: 1px solid var(--primary); 
        color: var(--primary); 
        padding: 5px 15px; 
        display: block; 
        margin-bottom: 5px; 
        text-align: center; 
        width: 100px; 
        text-decoration: none; 
        border-radius: 50px; 
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }
    .btn-invoice:hover { 
        background-color: var(--primary); 
        color: #ffffff;
    }
    .btn-bayar { 
        background-color: var(--primary); 
        color: white; 
        border: none; 
        padding: 5px 15px; 
        display: block; 
        width: 100px; 
        text-align: center; 
        text-decoration: none; 
        border-radius: 50px; 
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
    }
    .btn-bayar:hover { 
        background-color: #7a0000; 
        color: white; 
    }
</style>

<div class="container-fluid px-4 py-2">
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 bg-white rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Transaksi</span>
                        <h3 class="fw-bold m-0 text-dark"><?= $total_trx ?></h3>
                    </div>
                    <div class="p-3 rounded-4" style="background-color: rgba(158, 0, 0, 0.08); color: var(--primary);">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 bg-white rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Dibayar</span>
                        <h3 class="fw-bold m-0" style="color: var(--primary) !important;">Rp <?= number_format($total_dibayar, 0, ',', '.') ?></h3>
                    </div>
                    <div class="p-3 rounded-4" style="background-color: rgba(253, 192, 3, 0.15); color: #785900;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-4 bg-white rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Sisa Tagihan</span>
                        <h3 class="fw-bold m-0" style="color: var(--primary) !important;">Rp <?= number_format($total_sisa, 0, ',', '.') ?></h3>
                    </div>
                    <div class="p-3 rounded-4" style="background-color: rgba(158, 0, 0, 0.08); color: var(--primary);">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0"><i class="bi bi-receipt-cutoff me-2"></i>Daftar Pembayaran Saya</h4>
        <a href="transaksi.php" class="btn btn-primary px-4 rounded-pill shadow-sm">+ Sewa Baru</a>
    </div>

    <div class="card p-4 shadow-sm border-0 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>NO. RESI</th>
                        <th>MOBIL</th>
                        <th>TUJUAN</th>
                        <th>TANGGAL</th>
                        <th>TOTAL</th>
                        <th>DIBAYAR</th>
                        <th>SISA</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    $sql = "SELECT t.*, m.merk, m.jenis 
            FROM transaksi_sewa t 
            JOIN mobil m ON t.kode_mobil = m.kode_mobil 
            WHERE t.id_pelanggan = '$id_pelanggan' 
            ORDER BY t.id_sewa DESC";
    
    $res = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            $total = (int)$row['total_bayar'];
            $raw_dibayar = (int)$row['jumlah_bayar'];
            
            // Logic yang sama persis dengan perhitungan di atas
            $dibayar = ($raw_dibayar > $total) ? $total : $raw_dibayar;
            $sisa = $total - $dibayar;
            $sisa = ($sisa < 0) ? 0 : $sisa;
            
            $tujuan_jemput = ($row['lokasi_jemput'] === 'Antar ke Alamat lainnya') ? $row['alamat_detail'] : $row['lokasi_jemput'];
            $tujuan_kembali = (!empty($row['lokasi_kembali'])) ? (($row['lokasi_kembali'] === 'Jemput di Alamat lainnya') ? $row['alamat_kembali'] : $row['lokasi_kembali']) : 'Kembalikan ke Kantor';
            
            if (isset($row['status_sewa']) && $row['status_sewa'] == 'selesai') {
                $status_label = "SELESAI"; $status_class = "bg-success";
            } elseif ($dibayar <= 0) {
                $status_label = "BELUM LUNAS"; $status_class = "bg-danger";
            } elseif ($sisa > 0) {
                $status_label = "DP"; $status_class = "bg-info text-dark";
            } else {
                $status_label = "LUNAS"; $status_class = "bg-primary";
            }
    ?>
    <tr>
        <td>#SRV-<?= $row['id_sewa'] ?></td>
        <td><strong><?= htmlspecialchars($row['merk'] . ' ' . $row['jenis']) ?></strong></td>
        <td>
            <div class="mb-1">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.62rem; letter-spacing: 0.5px;">Jemput:</small>
                <span class="d-block font-semibold text-dark" style="font-size: 0.82rem;"><?= htmlspecialchars($tujuan_jemput ?? 'Ambil di Kantor') ?></span>
            </div>
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.62rem; letter-spacing: 0.5px;">Kembali:</small>
                <span class="d-block font-semibold text-dark" style="font-size: 0.82rem;"><?= htmlspecialchars($tujuan_kembali) ?></span>
            </div>
        </td>
        <td><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></td>
        <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
        <td>Rp <?= number_format($dibayar, 0, ',', '.') ?></td>
        <td class="text-danger fw-bold">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
        <td><span class="badge <?= $status_class ?>"><?= $status_label ?></span></td>
        <td>
            <a href="invoice.php?id=<?= $row['id_sewa'] ?>" class="btn-invoice">Invoice</a>
            <?php if ($sisa > 0 && (!isset($row['status_sewa']) || $row['status_sewa'] != 'selesai')): ?>
                <a href="input_pembayaran.php?id=<?= $row['id_sewa'] ?>" class="btn-bayar">Bayar</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php 
        } 
    } else {
        echo "<tr><td colspan='9' class='text-center p-4'>Belum ada transaksi sewa.</td></tr>";
    }
    ?>
</tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>