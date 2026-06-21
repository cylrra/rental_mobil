<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'navbar.php';
include 'koneksi.php';

// Proses Tambah Data
if (isset($_POST['tambah_pemeliharaan'])) {
    $kode_mobil = mysqli_real_escape_string($conn, $_POST['kode_mobil']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal_pemeliharaan']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_pemeliharaan']);
    $biaya = floatval($_POST['biaya_pemeliharaan']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $metode_bayar = mysqli_real_escape_string($conn, $_POST['metode_bayar']);
    
    // Tentukan akun kredit berdasarkan metode bayar (111 Kas Tunai, 112 Bank Transfer)
    $akun_kredit = ($metode_bayar === 'Bank') ? '112' : '111';

    // 1. Insert ke tabel pemeliharaan (menggunakan kolom kode_mobil)
    $query = "INSERT INTO pemeliharaan (kode_mobil, tanggal_pemeliharaan, jenis_pemeliharaan, biaya_pemeliharaan, keterangan) 
              VALUES ('$kode_mobil', '$tanggal', '$jenis', '$biaya', '$keterangan')";
    
    if (mysqli_query($conn, $query)) {
        $id_pemeliharaan = mysqli_insert_id($conn);
        $keterangan_jurnal = "Biaya Pemeliharaan Mobil: " . $kode_mobil . " (" . $jenis . ")";
        
        // 2. OTOMATIS MASUK JURNAL (Debit: Beban Perawatan '513', Kredit: Kas '111')
        $q_debit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                    VALUES ('$tanggal', '$keterangan_jurnal', '513', '$biaya', 0, '$id_pemeliharaan')";
        mysqli_query($conn, $q_debit);

        $q_kredit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                     VALUES ('$tanggal', '    $keterangan_jurnal', '$akun_kredit', 0, '$biaya', '$id_pemeliharaan')";
        mysqli_query($conn, $q_kredit);

        echo "<script>alert('Data pemeliharaan berhasil disimpan dan diposting ke jurnal!'); window.location='pemeliharaan.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data pemeliharaan: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}

// Ambil data mobil untuk dropdown
$mobil_opsi = mysqli_query($conn, "SELECT kode_mobil, merk, nopol FROM mobil ORDER BY merk ASC");
// Ambil data riwayat pemeliharaan
$riwayat = mysqli_query($conn, "SELECT p.*, m.merk, m.nopol FROM pemeliharaan p JOIN mobil m ON p.kode_mobil = m.kode_mobil ORDER BY p.tanggal_pemeliharaan DESC");
?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="bi bi-wrench me-2"></i>Input Pemeliharaan</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Mobil</label>
                        <select name="kode_mobil" class="form-select" required>
                            <option value="">-- Pilih Mobil --</option>
                            <?php while($m = mysqli_fetch_assoc($mobil_opsi)) { ?>
                                <option value="<?= $m['kode_mobil']; ?>"><?= htmlspecialchars($m['merk']); ?> [<?= htmlspecialchars($m['nopol']); ?>]</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal Pemeliharaan</label>
                        <input type="date" name="tanggal_pemeliharaan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jenis Pemeliharaan</label>
                        <select name="jenis_pemeliharaan" class="form-select" required>
                            <option value="Servis Rutin">Servis Rutin</option>
                            <option value="Perbaikan Kerusakan">Perbaikan Kerusakan</option>
                            <option value="Ganti Ban">Ganti Ban</option>
                            <option value="Ganti Oli">Ganti Oli</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Biaya (Rp)</label>
                        <input type="number" name="biaya_pemeliharaan" class="form-control" min="0" placeholder="Contoh: 500000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Metode Pembayaran</label>
                        <select name="metode_bayar" class="form-select" required>
                            <option value="Kas">Kas / Tunai</option>
                            <option value="Bank">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi pemeliharaan/perbaikan"></textarea>
                    </div>
                    <button type="submit" name="tambah_pemeliharaan" class="btn btn-primary w-100 py-2 mt-2 rounded-pill shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Pemeliharaan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Riwayat Pemeliharaan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Mobil</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Biaya</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if ($riwayat && mysqli_num_rows($riwayat) > 0) {
                                while($r = mysqli_fetch_assoc($riwayat)) { 
                            ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $no++; ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($r['merk']); ?></span>
                                    <small class="text-muted d-block"><?= htmlspecialchars($r['nopol']); ?></small>
                                </td>
                                <td><?= date('d M Y', strtotime($r['tanggal_pemeliharaan'])); ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-info text-dark">
                                        <?= htmlspecialchars($r['jenis_pemeliharaan']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-danger">Rp <?= number_format($r['biaya_pemeliharaan'], 0, ',', '.'); ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($r['keterangan']); ?></td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada riwayat pemeliharaan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>