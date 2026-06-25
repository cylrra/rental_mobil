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
        <h4 class="fw-bold m-0" style="color: var(--on-surface);"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Riwayat Pembayaran Saya</h4>
        <a href="transaksi.php" class="btn btn-primary px-4 rounded-pill shadow-sm fw-bold"><i class="bi bi-plus-circle me-1"></i> Sewa Baru</a>
    </div>

    <div class="card p-0 shadow-sm border-0 rounded-4 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <tr class="text-secondary text-uppercase">
                        <th class="ps-4 py-3">No. Order</th>
                        <th class="py-3">Armada</th>
                        <th class="py-3">Tujuan / Lokasi</th>
                        <th class="py-3">Tanggal Sewa</th>
                        <th class="py-3 text-end">Total Tagihan</th>
                        <th class="py-3 text-end">Sudah Dibayar</th>
                        <th class="py-3 text-end">Sisa Tagihan</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="pe-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem;">
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
                $status_label = "SELESAI"; $status_class = "bg-success text-white";
            } elseif ($dibayar <= 0) {
                $status_label = "BELUM LUNAS"; $status_class = "bg-danger text-white";
            } elseif ($sisa > 0) {
                $status_label = "DP DIBAYAR"; $status_class = "bg-info text-dark";
            } else {
                $status_label = "LUNAS"; $status_class = "bg-primary text-white";
            }
    ?>
    <tr>
        <td class="ps-4 fw-bold text-primary">#SRV-<?= $row['id_sewa'] ?></td>
        <td><strong style="font-size: 0.95rem;"><?= htmlspecialchars($row['merk'] . ' ' . $row['jenis']) ?></strong></td>
        <td>
            <div class="mb-1">
                <span class="badge bg-light text-dark border px-2 py-1 mb-1" style="font-size: 0.65rem;">Jemput</span>
                <span class="d-block text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($tujuan_jemput ?? 'Ambil di Kantor') ?></span>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-2 py-1 mb-1" style="font-size: 0.65rem;">Kembali</span>
                <span class="d-block text-muted" style="font-size: 0.8rem;"><?= htmlspecialchars($tujuan_kembali) ?></span>
            </div>
        </td>
        <td><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></td>
        <td class="text-end fw-semibold">Rp <?= number_format($total, 0, ',', '.') ?></td>
        <td class="text-end fw-semibold text-success">Rp <?= number_format($dibayar, 0, ',', '.') ?></td>
        <td class="text-end fw-bold text-danger">Rp <?= number_format($sisa, 0, ',', '.') ?></td>
        <td class="text-center">
            <span class="badge rounded-pill px-3 py-2 <?= $status_class ?>" style="font-size: 0.7rem; letter-spacing: 0.5px;"><?= $status_label ?></span>
        </td>
        <td class="pe-4">
            <div class="d-grid gap-2">
                <a href="invoice.php?id=<?= $row['id_sewa'] ?>" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" style="font-size: 0.75rem;"><i class="bi bi-printer me-1"></i> Invoice</a>
                <?php if ($sisa > 0 && (!isset($row['status_sewa']) || $row['status_sewa'] != 'selesai')): ?>
                    <a href="pembayaran.php?id=<?= $row['id_sewa'] ?>" class="btn btn-sm btn-danger rounded-pill fw-bold shadow-sm" style="font-size: 0.75rem; background-color: #9e0000; border-color: #9e0000;"><i class="bi bi-wallet2 me-1"></i> Bayar Sisa</a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php 
        } 
    } else {
        echo "<tr><td colspan='9' class='text-center p-5 text-muted'><i class='bi bi-journal-x fs-1 d-block mb-2'></i>Belum ada riwayat pembayaran tagihan.</td></tr>";
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