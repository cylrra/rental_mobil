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
            <i class="bi bi-book-half text-info me-2"></i> Riwayat Buku Jurnal Detail
        </h5>
        <a href="jurnal_umum.php" class="btn btn-sm btn-outline-primary rounded-3">
            <i class="bi bi-plus-lg"></i> Tambah Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Kode & Nama Akun</th>
                        <th>Keterangan</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end pe-4">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_view = "SELECT jd.*, na.nama_akun 
                                 FROM (
                                     SELECT id_jurnal, tanggal, kode_akun, debit, kredit, keterangan, id_sumber FROM jurnal_detail
                                     UNION ALL
                                     SELECT id_jurnal, tanggal, kode_akun, Debit AS debit, Kredit AS kredit, keterangan, id_sumber FROM jurnal
                                 ) jd
                                 LEFT JOIN nama_akun na ON jd.kode_akun = na.kode_akun
                                 ORDER BY jd.tanggal DESC, jd.id_jurnal DESC";
                    $res_view = mysqli_query($conn, $sql_view);

                    if (mysqli_num_rows($res_view) > 0) {
                        while($j = mysqli_fetch_assoc($res_view)) {
                            // Geser teks ke kanan jika akun berada di sisi Kredit
                            $indent_css = ($j['kredit'] > 0) ? 'style="padding-left: 30px;"' : '';
                    ?>
                    <tr class="small">
                        <td class="ps-4 text-muted"><?= date('d M Y', strtotime($j['tanggal'])); ?></td>
                        <td>
                            <div <?= $indent_css; ?> class="<?= ($j['kredit'] > 0) ? 'text-muted' : 'fw-bold text-dark'; ?>">
                                <?= $j['kode_akun']; ?> - <?= $j['nama_akun']; ?>
                            </div>
                        </td>
                        <td class="text-secondary"><?= $j['keterangan']; ?></td>
                        <td class="text-end text-success fw-bold"><?= ($j['debit'] > 0) ? 'Rp '.number_format($j['debit'], 0, ',', '.') : '-'; ?></td>
                        <td class="text-end text-danger fw-bold pe-4"><?= ($j['kredit'] > 0) ? 'Rp '.number_format($j['kredit'], 0, ',', '.') : '-'; ?></td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-5 text-muted'>Belum ada transaksi akuntansi tercatat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

        </div> 
    </div> 
</div> 
</body>
</html>