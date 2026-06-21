<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';
?>

<div class="card shadow-sm border-0 rounded-4 bg-white">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center px-4">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-diagram-3-fill text-primary me-2"></i> Chart of Accounts (COA) / Daftar Akun
        </h5>
        <span class="badge bg-primary rounded-pill px-3 py-2">PT INDOMAX RENTAL</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Akun</th>
                        <th>Nama Akun Pembukuan</th>
                        <th class="text-end pe-4">Saldo Awal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Mengambil data dari tabel nama_akun
                    $query_coa = mysqli_query($conn, "SELECT * FROM nama_akun ORDER BY kode_akun ASC");
                    if ($query_coa && mysqli_num_rows($query_coa) > 0) {
                        while($row = mysqli_fetch_assoc($query_coa)) {
                            // Cek jika akun merupakan header (saldo_awal = 0 dan bukan akun operasional)
                            $is_header = ($row['saldo_awal'] == 0 && !in_array($row['kode_akun'], ['113','312','411','412','511','513']));
                    ?>
                    <tr class="<?= $is_header ? 'table-light fw-bold text-dark' : ''; ?>">
                        <td class="ps-4 font-monospace"><?= htmlspecialchars($row['kode_akun']); ?></td>
                        <td>
                            <?php if(!$is_header): ?>
                                <span class="ms-3 text-secondary">• <?= htmlspecialchars($row['nama_akun']); ?></span>
                            <?php else: ?>
                                <?= htmlspecialchars($row['nama_akun']); ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4 fw-bold <?= $row['saldo_awal'] > 0 ? 'text-primary' : 'text-muted'; ?>">
                            Rp <?= number_format($row['saldo_awal'], 2, ',', '.'); ?>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Data COA kosong.</td></tr>";
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