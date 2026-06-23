<?php 
include 'navbar.php'; 
include 'koneksi.php'; 

$id_pilihan = isset($_GET['id']) ? trim($_GET['id']) : '';

$total_tagihan = 0;
if ($id_pilihan) {
    $q_total = mysqli_query($conn, "SELECT total_bayar FROM transaksi_sewa WHERE id_sewa = '$id_pilihan'");
    $d_total = mysqli_fetch_assoc($q_total);
    $total_tagihan = $d_total['total_bayar'] ?? 0;
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i> Input Pembayaran Baru</h4>
                    <p class="mb-0 opacity-75">Silakan isi detail pembayaran sewa mobil</p>
                </div>
                <div class="card-body p-4">
                    <form id="formPembayaran">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Transaksi / Sewa</label>
                            <select name="id_transaksi" class="form-select" required>
                                <option value="">-- Pilih Transaksi --</option>
                                <?php 
                                $sql_t = mysqli_query($conn, "SELECT * FROM transaksi_sewa ORDER BY id_sewa DESC");
                                while($t = mysqli_fetch_array($sql_t)){
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    echo "<option value='".$id_db."' $selected>";
                                    echo "#SRV-".$id_db." (Mobil: ".$t['kode_mobil'].")";
                                    echo "</option>";
                                }
                                ?>
                            </select>
                            <?php if($id_pilihan): ?>
                                <div class="form-text text-success">
                                    <i class="bi bi-check2-circle"></i> ID Transaksi #<?= htmlspecialchars($id_pilihan) ?> telah terpilih otomatis.
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Bayar</label>
                                <input type="date" name="tgl_bayar" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <select name="metode_bayar" class="form-select" required>
                                    <option value="Transfer Bank">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="E-Wallet">E-Wallet (OVO / GoPay / ShopeePay)</option>
                                    <option value="Tunai">Tunai / Cash di Tempat</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Pembayaran</label>
                            <select name="tipe_pembayaran" id="tipe_pembayaran" class="form-select" onchange="hitungBayar()" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Lunas">Bayar Lunas (100%)</option>
                                <option value="DP">Uang Muka (DP 30%)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah Bayar (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-control form-control-lg text-primary fw-bold" placeholder="Contoh: 500000" readonly required>
                            <small class="text-muted">Nilai terisi otomatis saat tipe dipilih.</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" onclick="jalankanSimpan()" class="btn btn-primary btn-lg rounded-3">
                                <i class="bi bi-save me-2"></i> Simpan Pembayaran & Posting Jurnal
                            </button>
                            <a href="riwayat_pembayaran.php" class="btn btn-light text-muted">Lihat Riwayat</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBerhasil" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center" style="border-radius: 20px; padding: 30px; border: none;">
            <div style="width: 80px; height: 80px; background-color: #e8f5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto;">
                <i class="bi bi-check-lg" style="font-size: 45px; color: #4caf50;"></i>
            </div>
            
            <h4 class="fw-bold" style="margin-bottom: 10px;">Berhasil!</h4>
            <p style="color: #6c757d; margin-bottom: 25px; line-height: 1.5;">
                Pembayaran berhasil dilakukan, <br> mohon menunggu konfirmasi admin.
            </p>
            
            <button type="button" class="btn btn-primary" style="width: 120px; margin: 0 auto; padding: 10px; border-radius: 8px; font-weight: 500;" onclick="window.location.href='riwayat_pembayaran.php'">OK</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const totalTagihan = <?php echo $total_tagihan; ?>;

    function hitungBayar() {
        const tipe = document.getElementById('tipe_pembayaran').value;
        const inputBayar = document.getElementById('jumlah_bayar');
        if (tipe === 'Lunas') {
            inputBayar.value = totalTagihan;
        } else if (tipe === 'DP') {
            inputBayar.value = Math.round(totalTagihan * 0.3);
        } else {
            inputBayar.value = '';
        }
    }

    function jalankanSimpan() {
        var form = document.getElementById('formPembayaran');
        var formData = new FormData(form);
        formData.append('simpan_pembayaran', '1');

        fetch('proses_bayar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(res => {
            if(res.trim() == "success") {
                new bootstrap.Modal(document.getElementById('modalBerhasil')).show();
            } else {
                alert("Server: " + res);
            }
        })
        .catch(err => alert("Gagal konek!"));
    }
</script>