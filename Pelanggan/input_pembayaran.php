<?php 
include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Ambil ID dari URL dan bersihkan
$id_pilihan = isset($_GET['id']) ? trim($_GET['id']) : '';

// 2. Ambil total tagihan dari database untuk perhitungan otomatis
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
                    <form action="proses_bayar.php" method="POST">
                        
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
                            <button type="submit" name="simpan_pembayaran" class="btn btn-primary btn-lg rounded-3">
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

<script>
    // Variabel total dari PHP untuk digunakan di JavaScript
    const totalTagihan = <?php echo $total_tagihan; ?>;

    function hitungBayar() {
        const tipe = document.getElementById('tipe_pembayaran').value;
        const inputBayar = document.getElementById('jumlah_bayar');
        
        if (tipe === 'Lunas') {
            inputBayar.value = totalTagihan;
        } else if (tipe === 'DP') {
            // Hitung 30% dari total
            const dp = Math.round(totalTagihan * 0.3);
            inputBayar.value = dp;
        } else {
            inputBayar.value = '';
        }
    }
</script>