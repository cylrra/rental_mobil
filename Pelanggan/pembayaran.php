<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh bayar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Ambil ID dari URL (fitur otomatis dari tombol Bayar di daftar transaksi)
$id_pilihan = isset($_GET['id']) ? str_replace('#', '', trim($_GET['id'])) : '';
$id_pelanggan = $_SESSION['id_pelanggan'];

// 2. Hitung total biaya awal jika ID terpilih otomatis
$initial_tagihan = "";
if ($id_pilihan) {
    $id_pilihan_clean = mysqli_real_escape_string($conn, $id_pilihan);
    $res_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari 
                                  FROM transaksi_sewa t 
                                  JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                  LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                  WHERE t.id_sewa = '$id_pilihan_clean' AND t.id_pelanggan = '$id_pelanggan'");
    if ($res_t && $row_t = mysqli_fetch_assoc($res_t)) {
        $tarif_mobil = $row_t['tarif_per_hari'];
        $tarif_supir = ($row_t['opsi_supir'] == 'ya' && !empty($row_t['tarif_supir_per_hari'])) ? $row_t['tarif_supir_per_hari'] : 0;
        $initial_tagihan = ($tarif_mobil * $row_t['lama_sewa']) + ($tarif_supir * $row_t['lama_sewa']);
    }
}
?>

<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 my-4">
            <div class="card shadow-sm border-0 rounded-4 bg-white">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h4 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: #0f172a;"><i class="bi bi-wallet2 text-primary me-2"></i> Input Pembayaran Baru</h4>
                    <p class="text-muted small">Selesaikan pembayaran untuk penyewaan mobil Anda.</p>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="proses_bayar.php" method="POST">
                        
                        <!-- Transaksi dropdown (locked by logged in user) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Pilih Transaksi Aktif</label>
                            <select name="id_transaksi" id="id_transaksi_select" class="form-select py-2-5" required onchange="updateEstimasiBayar()">
                                <option value="">-- Pilih Transaksi --</option>
                                <?php 
                                // Ambil hanya transaksi milik pelanggan yang berstatus 'berjalan'
                                $sql_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari 
                                                              FROM transaksi_sewa t 
                                                              JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                                              LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                                              WHERE t.status_sewa = 'berjalan' AND t.id_pelanggan = '$id_pelanggan'");
                                
                                while($t = mysqli_fetch_array($sql_t)){
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    
                                    // Hitung total tagihan
                                    $tarif_mobil = $t['tarif_per_hari'];
                                    $tarif_supir = ($t['opsi_supir'] == 'ya' && !empty($t['tarif_supir_per_hari'])) ? $t['tarif_supir_per_hari'] : 0;
                                    $tagihan = ($tarif_mobil * $t['lama_sewa']) + ($tarif_supir * $t['lama_sewa']);
                                    
                                    echo "<option value='".$id_db."' data-tagihan='".$tagihan."' $selected>";
                                    echo "Order #SRV-".$id_db." (Mobil: ".$t['merk'].")";
                                    echo "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Date & Payment Method -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Tanggal Pembayaran</label>
                                <input type="date" name="tgl_bayar" class="form-control py-2-5" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Metode Pembayaran</label>
                                <select name="metode_bayar" class="form-select py-2-5" required>
                                    <option value="transfer">Transfer Bank (BCA / Mandiri)</option>
                                    <option value="e-wallet">E-Wallet (OVO / GoPay / ShopeePay)</option>
                                    <option value="cash">Tunai / Cash di Tempat</option>
                                </select>
                            </div>
                        </div>

                        <!-- DP / LUNAS Selection -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Tipe Pembayaran</label>
                            <select name="jenis_pembayaran" id="jenis_pembayaran" class="form-select py-2-5" required onchange="updateEstimasiBayar()">
                                <option value="pelunasan">Bayar Lunas (100%)</option>
                                <option value="dp">Uang Muka (DP 30%)</option>
                            </select>
                        </div>

                        <!-- Summary of payment -->
                        <div class="alert bg-light border p-3 rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Total Tagihan Rental</span>
                                <span class="fw-semibold text-dark" id="disp_tagihan_asli">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2" id="row_potongan" style="display:none !important;">
                                <span class="text-muted small" id="label_potongan">DP 30%</span>
                                <span class="fw-semibold text-danger" id="disp_potongan">Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Jumlah Harus Dibayar</span>
                                <span class="fw-extrabold text-primary fs-5" id="disp_jumlah_bayar">Rp 0</span>
                            </div>
                        </div>

                        <!-- Raw amount to send in form -->
                        <input type="hidden" name="jumlah_bayar" id="jumlah_bayar_raw" value="<?= htmlspecialchars($initial_tagihan); ?>">

                        <div class="d-grid gap-2">
                            <button type="submit" name="simpan_pembayaran" class="btn btn-primary btn-lg py-2-5 rounded-3 fw-bold fs-6">
                                <i class="bi bi-shield-check me-2"></i> Konfirmasi Pembayaran
                            </button>
                            <a href="riwayat_pembayaran.php" class="btn btn-link text-decoration-none text-muted small">Lihat Riwayat Transaksi</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

function updateEstimasiBayar() {
    const transSelect = document.getElementById('id_transaksi_select');
    const tipeSelect = document.getElementById('jenis_pembayaran');
    const hiddenRaw = document.getElementById('jumlah_bayar_raw');

    const dispTagihanAsli = document.getElementById('disp_tagihan_asli');
    const dispJumlahBayar = document.getElementById('disp_jumlah_bayar');
    const rowPotongan = document.getElementById('row_potongan');
    const dispPotongan = document.getElementById('disp_potongan');
    const labelPotongan = document.getElementById('label_potongan');

    let totalAsli = 0;
    const selectedOption = transSelect.options[transSelect.selectedIndex];
    if (selectedOption && selectedOption.value !== "") {
        totalAsli = parseFloat(selectedOption.getAttribute('data-tagihan')) || 0;
    }

    dispTagihanAsli.innerText = formatRupiah(totalAsli);

    const tipe = tipeSelect.value;
    if (tipe === 'dp') {
        let dp = totalAsli * 0.3;
        hiddenRaw.value = dp;
        dispJumlahBayar.innerText = formatRupiah(dp);
        
        // Show discount/offset breakdown
        rowPotongan.style.setProperty('display', 'flex', 'important');
        labelPotongan.innerText = 'Uang Muka (DP 30%)';
        dispPotongan.innerText = formatRupiah(dp);
    } else {
        hiddenRaw.value = totalAsli;
        dispJumlahBayar.innerText = formatRupiah(totalAsli);
        rowPotongan.style.setProperty('display', 'none', 'important');
    }
}

// Run immediately on page load to initialize estimation if redirected with query param ID
window.onload = function() {
    updateEstimasiBayar();
}
</script>

<style>
    .py-2-5 { padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .fs-7 { font-size: 0.82rem; }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>