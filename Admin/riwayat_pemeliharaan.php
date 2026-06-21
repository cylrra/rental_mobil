<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'navbar.php';
include 'koneksi.php';

// Ambil data riwayat pemeliharaan (status 'selesai')
$riwayat = mysqli_query($conn, "SELECT p.*, m.merk, m.nopol FROM pemeliharaan p JOIN mobil m ON p.kode_mobil = m.kode_mobil WHERE p.status = 'selesai' ORDER BY p.tanggal_pemeliharaan DESC");
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold"><i class="bi bi-clock-history text-[#06588c] me-2"></i> Riwayat Pemeliharaan</h3>
        <p class="text-muted">Catatan historis servis dan perbaikan armada mobil yang telah selesai.</p>
    </div>
    <a href="jadwal_service.php" class="btn text-white rounded-pill px-4" style="background-color:#06588c;">
        <i class="bi bi-calendar-check me-2"></i>Ke Jadwal Servis
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="mb-0 fw-bold text-[#04345a]"><i class="bi bi-check-all me-2"></i>Daftar Pemeliharaan Selesai</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Mobil</th>
                        <th>Tanggal Selesai</th>
                        <th>Jenis</th>
                        <th>Total Biaya</th>
                        <th>Keterangan</th>
                        <th class="pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    if ($riwayat && mysqli_num_rows($riwayat) > 0) {
                        while($r = mysqli_fetch_assoc($riwayat)) { 
                    ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $no++; ?></td>
                        <td>
                            <span class="fw-bold"><?= htmlspecialchars($r['merk']); ?></span>
                            <small class="text-muted d-block"><?= htmlspecialchars($r['nopol']); ?></small>
                        </td>
                        <td><?= date('d M Y', strtotime($r['tanggal_pemeliharaan'])); ?></td>
                        <td>
                            <span class="badge rounded-pill text-dark" style="background-color:#c8c6c6;">
                                <?= htmlspecialchars($r['jenis_pemeliharaan']); ?>
                            </span>
                        </td>
                        <td class="fw-bold text-danger">Rp <?= number_format($r['biaya_pemeliharaan'], 0, ',', '.'); ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($r['keterangan']); ?></td>
                        <td class="pe-4">
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i>Selesai
                            </span>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada riwayat pemeliharaan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
