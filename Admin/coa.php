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
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary rounded-pill px-3 py-1.5 btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahAkun">
                <i class="bi bi-plus-circle-fill"></i> Tambah Akun
            </button>
            <span class="badge bg-primary rounded-pill px-3 py-2">PT INDOMAX RENTAL</span>
        </div>
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
                    // Ambil data dari tabel nama_akun
                    $query_coa = mysqli_query($conn, "SELECT * FROM nama_akun ORDER BY kode_akun ASC");
                    
                    $kelompok_akun = [
                        '1' => 'ASET (AKTIVA)',
                        '2' => 'KEWAJIBAN (UTANG)',
                        '3' => 'EKUITAS (MODAL)',
                        '4' => 'PENDAPATAN',
                        '5' => 'BEBAN OPERASIONAL'
                    ];

                    $accounts = [];
                    $total_bank = 0;
                    if ($query_coa && mysqli_num_rows($query_coa) > 0) {
                        while($row = mysqli_fetch_assoc($query_coa)) {
                            $accounts[] = $row;
                            if (strlen($row['kode_akun']) > 3 && substr($row['kode_akun'], 0, 3) === '112') {
                                $total_bank += floatval($row['saldo_awal']);
                            }
                        }
                    }

                    $current_kelompok = '';
                    $in_bank_group = false;

                    if (!empty($accounts)) {
                        foreach($accounts as $row) {
                            $kode = $row['kode_akun'];
                            $awalan = substr($kode, 0, 1);
                            
                            // Print Header Kelompok jika berpindah kelompok
                            if ($current_kelompok !== $awalan && array_key_exists($awalan, $kelompok_akun)) {
                                $current_kelompok = $awalan;
                                ?>
                                <tr class="table-light fw-bold text-dark">
                                    <td colspan="3" class="ps-4">
                                        <i class="bi bi-folder-fill text-warning me-2"></i> <?= htmlspecialchars($kelompok_akun[$awalan]) ?>
                                    </td>
                                </tr>
                                <?php
                            }

                            // Khusus penanganan Akun 112 (Bank)
                            if ($kode === '112') {
                                ?>
                                <tr data-bs-toggle="collapse" data-bs-target="#collapseBank" aria-expanded="false" aria-controls="collapseBank" style="cursor: pointer;" class="bg-light hover-bg">
                                    <td class="ps-4 font-monospace"><span class="ms-4"><i class="bi bi-chevron-down me-2 text-primary" style="font-size: 0.8rem;"></i><?= htmlspecialchars($kode); ?></span></td>
                                    <td>
                                        <strong class="text-dark"><i class="bi bi-bank me-2 text-secondary"></i>Bank (Klik untuk lihat detail)</strong>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-primary">
                                        Rp <?= number_format($total_bank, 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <?php
                                $in_bank_group = true;
                                continue;
                            }

                            // Sub-akun Bank (112x)
                            if ($in_bank_group && substr($kode, 0, 3) === '112' && strlen($kode) > 3) {
                                ?>
                                <tr class="collapse" id="collapseBank" style="background-color: #f8f9fa;">
                                    <td class="ps-4 font-monospace text-muted"><span class="ms-5 ps-3">↳ <?= htmlspecialchars($kode); ?></span></td>
                                    <td class="ps-5">
                                        <span class="text-secondary"><i class="bi bi-credit-card me-2"></i><?= htmlspecialchars($row['nama_akun']); ?></span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold <?= $row['saldo_awal'] > 0 ? 'text-primary' : 'text-muted'; ?>" style="font-size: 0.9em;">
                                        Rp <?= number_format($row['saldo_awal'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <?php
                                continue;
                            } else {
                                $in_bank_group = false; // Keluar dari grup bank jika kode selanjutnya bukan 112x
                            }

                            // Rendering baris normal
                    ?>
                    <tr>
                        <td class="ps-4 font-monospace"><span class="ms-4"><?= htmlspecialchars($kode); ?></span></td>
                        <td>
                            <span class="text-secondary"><?= htmlspecialchars($row['nama_akun']); ?></span>
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

<div class="modal fade" id="modalTambahAkun" tabindex="-1" aria-labelledby="modalTambahAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTambahAkunLabel">Tambah Akun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="box-shadow: none;"></button>
            </div>
            <form action="tambah_akun.php" method="POST">
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label for="kode_akun" class="form-label small fw-semibold text-secondary">Kode Akun</label>
                        <input type="text" class="form-control rounded-3" id="kode_akun" name="kode_akun" placeholder="Contoh: 115" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_akun" class="form-label small fw-semibold text-secondary">Nama Akun Pembukuan</label>
                        <input type="text" class="form-control rounded-3" id="nama_akun" name="nama_akun" placeholder="Contoh: Perlengkapan Kantor" required>
                    </div>
                    <div class="mb-3">
                        <label for="saldo_awal" class="form-label small fw-semibold text-secondary">Saldo Awal (Rp)</label>
                        <input type="number" step="0.01" class="form-control rounded-3" id="saldo_awal" name="saldo_awal" placeholder="0" value="0" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1 text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>