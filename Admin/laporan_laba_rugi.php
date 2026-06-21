<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya admin yang boleh mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Ambil data rekap laba rugi dari database
$sql = "SELECT periode, pendapatan_total, beban_total, laba_bersih FROM laporan_laba_rugi ORDER BY periode DESC";
$res = mysqli_query($conn, $sql);

$total_pendapatan_kumulatif = 0;
$total_beban_kumulatif = 0;
$total_laba_kumulatif = 0;

$reports = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $reports[] = $row;
        $total_pendapatan_kumulatif += $row['pendapatan_total'];
        $total_beban_kumulatif += $row['beban_total'];
        $total_laba_kumulatif += $row['laba_bersih'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark m-0"><i class="bi bi-calculator me-2 text-danger"></i>Laporan Laba Rugi</h3>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-bold rounded-3 shadow-sm">
        <i class="bi bi-printer me-1"></i> Cetak Halaman
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-dark p-3 rounded-4">
            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Total Pendapatan (Kumulatif)</small>
            <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_pendapatan_kumulatif, 0, ',', '.'); ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-danger text-white p-3 rounded-4">
            <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Total Beban Operasional</small>
            <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_beban_kumulatif, 0, ',', '.'); ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white p-3 rounded-4">
            <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Total Laba Bersih</small>
            <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_laba_kumulatif, 0, ',', '.'); ?></h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="table-light text-secondary text-uppercase" style="font-size: 0.8rem; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th class="py-3 px-4 text-center" style="width: 15%;">Periode Rekap</th>
                        <th class="py-3 px-4 text-end">Total Pendapatan</th>
                        <th class="py-3 px-4 text-end">Total Beban</th>
                        <th class="py-3 px-4 text-end" style="width: 25%;">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.95rem;">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada data rekap keuangan yang terekam.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $row): ?>
                            <tr>
                                <td class="px-4 text-center fw-bold text-secondary">
                                    <?= date('d M Y', strtotime($row['periode'])); ?>
                                </td>
                                <td class="px-4 text-end text-success fw-semibold">
                                    Rp <?= number_format($row['pendapatan_total'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-4 text-end text-danger fw-semibold">
                                    Rp <?= number_format($row['beban_total'], 2, ',', '.'); ?>
                                </td>
                                <td class="px-4 text-end">
                                    <?php if ($row['laba_bersih'] >= 0): ?>
                                        <span class="badge bg-success-subtle text-success border border-success p-2 rounded-3 w-100 text-end">
                                            Rp <?= number_format($row['laba_bersih'], 2, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger p-2 rounded-3 w-100 text-end">
                                            Rp <?= number_format($row['laba_bersih'], 2, ',', '.'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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