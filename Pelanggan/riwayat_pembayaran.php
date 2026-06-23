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

// 1. QUERY STATISTIK RINGKASAN
$q_stats = mysqli_query($conn, "SELECT 
    COUNT(id_sewa) as total_trx, 
    SUM(jumlah_bayar) as total_dibayar, 
    SUM(total_bayar - jumlah_bayar) as total_sisa 
    FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'");
$stats = mysqli_fetch_assoc($q_stats);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-invoice { 
            background-color: #ffffff; border: 1px solid #3071a4; color: #3071a4; padding: 5px 15px; display: block; margin-bottom: 5px; text-align: center; width: 100px; text-decoration: none; border-radius: 50px; font-size: 14px;
        }
        .btn-invoice:hover { background-color: #f1f5f9; }
        .btn-bayar { 
            background-color: #3071a4; color: white; border: none; padding: 5px 15px; display: block; width: 100px; text-align: center; text-decoration: none; border-radius: 50px; font-size: 14px;
        }
        .btn-bayar:hover { background-color: #255a85; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 bg-primary text-white rounded-4">
                <h6>Total Transaksi</h6>
                <h3 class="fw-bold"><?= $stats['total_trx'] ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 bg-success text-white rounded-4">
                <h6>Total Dibayar</h6>
                <h3 class="fw-bold">Rp <?= number_format($stats['total_dibayar'], 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 p-3 bg-danger text-white rounded-4">
                <h6>Total Sisa Tagihan</h6>
                <h3 class="fw-bold">Rp <?= number_format($stats['total_sisa'], 0, ',', '.') ?></h3>
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
    $sql = "SELECT t.*, m.merk FROM transaksi_sewa t 
            JOIN mobil m ON t.kode_mobil = m.kode_mobil 
            WHERE t.id_pelanggan = '$id_pelanggan' 
            ORDER BY t.id_sewa DESC";
    
    $res = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        while($row = mysqli_fetch_assoc($res)) {
            $total = isset($row['total_bayar']) ? (int)$row['total_bayar'] : 0;
            $dibayar = isset($row['jumlah_bayar']) ? (int)$row['jumlah_bayar'] : 0;
            $sisa = $total - $dibayar;
            
            // LOGIKA STATUS BARU
            if (isset($row['status_sewa']) && $row['status_sewa'] == 'selesai') {
                $status_label = "SELESAI";
                $status_class = "bg-success";
            } elseif ($dibayar <= 0) {
                $status_label = "BELUM LUNAS";
                $status_class = "bg-danger";
            } elseif ($sisa > 0) {
                $status_label = "DP";
                $status_class = "bg-info text-dark";
            } else {
                $status_label = "LUNAS";
                $status_class = "bg-primary";
            }
    ?>
    <tr>
        <td>#SRV-<?= $row['id_sewa'] ?></td>
        <td><?= htmlspecialchars($row['merk']) ?></td>
        <td><?= htmlspecialchars($row['lokasi_jemput']) ?></td>
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