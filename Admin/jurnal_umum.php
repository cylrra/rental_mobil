<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';

if (isset($_POST['simpan_jurnal'])) {
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    $akun_debit   = mysqli_real_escape_string($conn, $_POST['akun_debit']);
    $nominal_debit = floatval($_POST['nominal_debit']);
    
    $akun_kredit   = mysqli_real_escape_string($conn, $_POST['akun_kredit']);
    $nominal_kredit = floatval($_POST['nominal_kredit']);

    if ($nominal_debit !== $nominal_kredit) {
        echo "<script>alert('Error: Jurnal tidak seimbang! Nilai Debit & Kredit harus sama.'); window.history.back();</script>";
        exit();
    }

    mysqli_begin_transaction($conn);
    try {
        // 1. Catat Sisi Debit
        mysqli_query($conn, "INSERT INTO jurnal_detail (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                             VALUES ('$tanggal', '$akun_debit', '$nominal_debit', 0, '$keterangan', 1)");
        // 2. Catat Sisi Kredit
        mysqli_query($conn, "INSERT INTO jurnal_detail (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                             VALUES ('$tanggal', '$akun_kredit', 0, '$nominal_kredit', '$keterangan', 1)");

        mysqli_commit($conn);
        echo "<script>alert('Jurnal Umum Berhasil Dibukukan!'); window.location='jurnal_riwayat.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 bg-white mb-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square"></i> Formulir Jurnal Umum Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan Transaksi</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Pembelian bensin operasional Fortuner" required>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border-start border-4 border-success">
                        <label class="form-label fw-bold text-success">Akun Sisi DEBIT</label>
                        <select name="akun_debit" class="form-select mb-2" required>
                            <option value="">-- Pilih Akun Debit --</option>
                            <?php
                            $coas = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM nama_akun ORDER BY kode_akun ASC");
                            while($c = mysqli_fetch_assoc($coas)) {
                                echo "<option value='{$c['kode_akun']}'>{$c['kode_akun']} - {$c['nama_akun']}</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="nominal_debit" class="form-control" placeholder="Masukkan Nominal Rp" min="1" required>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4 border-start border-4 border-danger">
                        <label class="form-label fw-bold text-danger">Akun Sisi KREDIT</label>
                        <select name="akun_kredit" class="form-select mb-2" required>
                            <option value="">-- Pilih Akun Kredit --</option>
                            <?php
                            mysqli_data_seek($coas, 0);
                            while($c = mysqli_fetch_assoc($coas)) {
                                echo "<option value='{$c['kode_akun']}'>{$c['kode_akun']} - {$c['nama_akun']}</option>";
                            }
                            ?>
                        </select>
                        <input type="number" name="nominal_kredit" class="form-control" placeholder="Masukkan Nominal Rp" min="1" required>
                    </div>

                    <button type="submit" name="simpan_jurnal" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold">
                        <i class="bi bi-check-circle-fill"></i> Simpan Pembukuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

        </div> 
    </div> 
</div> 
</body>
</html>