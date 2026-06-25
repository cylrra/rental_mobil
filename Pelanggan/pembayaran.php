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
                                  LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                  LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                  WHERE t.id_sewa = '$id_pilihan_clean' AND t.id_pelanggan = '$id_pelanggan'");
    if ($res_t && $row_t = mysqli_fetch_assoc($res_t)) {
        $total_bayar = (int)$row_t['total_bayar'];
        $sudah_dibayar = (int)$row_t['jumlah_bayar'];
        $initial_tagihan = $total_bayar - $sudah_dibayar;
        if ($initial_tagihan < 0) $initial_tagihan = 0;
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
                    <form action="proses_bayar_gateway.php" method="POST">
                        
                        <!-- Transaksi dropdown (locked by logged in user) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Pilih Transaksi Aktif</label>
                            <select name="id_transaksi" id="id_transaksi_select" class="form-select py-2-5" required onchange="updateEstimasiBayar()">
                                <option value="">-- Pilih Transaksi --</option>
                                <?php 
                                // Ambil hanya transaksi milik pelanggan yang berstatus 'berjalan' atau 'diterima' atau 'DP'
                                $sql_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari, m.merk 
                                                              FROM transaksi_sewa t 
                                                              LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                                              LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                                              WHERE t.status_sewa IN ('berjalan', 'diterima', 'DP') AND t.id_pelanggan = '$id_pelanggan'");
                                
                                while($t = mysqli_fetch_array($sql_t)){
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    
                                    // Gunakan total_bayar asli dari transaksi
                                    $total_tagihan = (int)$t['total_bayar'];
                                    
                                    // Sisa tagihan
                                    $sudah_dibayar = (int)$t['jumlah_bayar'];
                                    $sisa_tagihan = $total_tagihan - $sudah_dibayar;
                                    if ($sisa_tagihan < 0) $sisa_tagihan = 0;
                                    
                                    // Jika sisa tagihan > 0, tampilkan opsi
                                    if ($sisa_tagihan > 0) {
                                        echo "<option value='".$id_db."' data-tagihan='".$sisa_tagihan."' data-is-dp='".($sudah_dibayar > 0 ? "true" : "false")."' $selected>";
                                        echo "Order #SRV-".$id_db." (Mobil: ".$t['merk'].") - Sisa Rp " . number_format($sisa_tagihan, 0, ',', '.');
                                        echo "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Tanggal Pembayaran</label>
                                <input type="date" name="tgl_bayar" class="form-control py-2-5" value="<?php echo date('Y-m-d'); ?>" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Tipe Pembayaran</label>
                                <select name="tipe_pembayaran" id="jenis_pembayaran" class="form-select py-2-5" required onchange="updateEstimasiBayar()">
                                    <option value="Lunas">Bayar Lunas (100%)</option>
                                    <option value="DP">Uang Muka (DP minimal 50%)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Payment Method Grid -->
                        <div class="row mb-3">
                            <div class="col-12 mb-2"><label class="form-label small fw-bold text-secondary">Pilih Metode Pembayaran</label></div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="w-100 h-100">
                                    <input type="radio" name="metode_bayar" value="Transfer Bank BCA" class="btn-check" required onchange="updatePaymentUI()">
                                    <div class="card h-100 payment-card border-2 cursor-pointer bg-light transition-all">
                                        <div class="card-body d-flex align-items-center gap-3 p-3">
                                            <div class="bg-white p-2 rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="BCA">
                                            </div>
                                            <div class="fw-bold text-dark text-sm">BCA Virtual Account</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="w-100 h-100">
                                    <input type="radio" name="metode_bayar" value="Transfer Bank Mandiri" class="btn-check" required onchange="updatePaymentUI()">
                                    <div class="card h-100 payment-card border-2 cursor-pointer bg-light transition-all">
                                        <div class="card-body d-flex align-items-center gap-3 p-3">
                                            <div class="bg-white p-2 rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Mandiri">
                                            </div>
                                            <div class="fw-bold text-dark text-sm">Mandiri Virtual Account</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="w-100 h-100">
                                    <input type="radio" name="metode_bayar" value="E-Wallet GoPay" class="btn-check" required onchange="updatePaymentUI()">
                                    <div class="card h-100 payment-card border-2 cursor-pointer bg-light transition-all">
                                        <div class="card-body d-flex align-items-center gap-3 p-3">
                                            <div class="bg-white p-2 rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="GoPay">
                                            </div>
                                            <div class="fw-bold text-dark text-sm">GoPay</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="w-100 h-100">
                                    <input type="radio" name="metode_bayar" value="E-Wallet OVO" class="btn-check" required onchange="updatePaymentUI()">
                                    <div class="card h-100 payment-card border-2 cursor-pointer bg-light transition-all">
                                        <div class="card-body d-flex align-items-center gap-3 p-3">
                                            <div class="bg-white p-2 rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/OVO_logo.svg" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="OVO">
                                            </div>
                                            <div class="fw-bold text-dark text-sm">OVO</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Summary of payment -->
                        <!-- Summary of payment -->
                        <div class="alert p-3 mb-4 shadow-sm" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-medium">Total Tagihan Rental</span>
                                <span class="fw-semibold text-dark" id="disp_tagihan_asli">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2" id="row_potongan" style="display:none !important;">
                                <span class="text-muted small fw-medium" id="label_potongan">DP 50%</span>
                                <span class="fw-semibold text-danger" id="disp_potongan">Rp 0</span>
                            </div>
                            <hr class="my-2" style="border-color: #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Jumlah Harus Dibayar</span>
                                <span class="fw-black fs-5" style="color: #9e0000; font-weight: 900;" id="disp_jumlah_bayar">Rp 0</span>
                            </div>
                        </div>

                        <!-- Raw amount to send in form -->
                        <input type="hidden" name="jumlah_bayar" id="jumlah_bayar_raw" value="<?= htmlspecialchars($initial_tagihan); ?>">

                        <div class="d-grid gap-2">
                            <button type="submit" name="lanjut_bayar" class="btn fw-bold py-3 d-flex justify-content-center align-items-center gap-2" style="background-color: #d4af37; color: #1a1c1c; border-radius: 12px; box-shadow: 0 4px 6px rgba(212, 175, 55, 0.2); border: none; transition: 0.3s;" onmouseover="this.style.backgroundColor='#c49d2b'" onmouseout="this.style.backgroundColor='#d4af37'">
                                <i class="bi bi-shield-lock"></i> Lanjutkan Pembayaran
                            </button>
                            <a href="riwayat_pembayaran.php" class="btn btn-link text-decoration-none text-muted small mt-2">Batal & Kembali</a>
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
    let isDPPaid = false;
    const selectedOption = transSelect.options[transSelect.selectedIndex];
    if (selectedOption && selectedOption.value !== "") {
        totalAsli = parseFloat(selectedOption.getAttribute('data-tagihan')) || 0;
        isDPPaid = selectedOption.getAttribute('data-is-dp') === 'true';
    }
    
    // Disable DP option if already DP paid
    if (isDPPaid) {
        tipeSelect.value = 'Lunas';
        tipeSelect.style.pointerEvents = 'none';
        tipeSelect.style.backgroundColor = '#e9ecef';
    } else {
        tipeSelect.style.pointerEvents = 'auto';
        tipeSelect.style.backgroundColor = '';
    }

    dispTagihanAsli.innerText = formatRupiah(totalAsli);

    const tipe = tipeSelect.value;
    if (tipe === 'DP' && !isDPPaid) {
        let dp = totalAsli * 0.5;
        hiddenRaw.value = dp;
        dispJumlahBayar.innerText = formatRupiah(dp);
        
        // Show discount/offset breakdown
        rowPotongan.style.setProperty('display', 'flex', 'important');
        labelPotongan.innerText = 'Uang Muka (DP 50%)';
        dispPotongan.innerText = formatRupiah(dp);
    } else {
        hiddenRaw.value = totalAsli;
        dispJumlahBayar.innerText = formatRupiah(totalAsli);
        rowPotongan.style.setProperty('display', 'none', 'important');
    }
}

function updatePaymentUI() {
    // Menghapus highlight dari semua card
    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('border-danger', 'bg-danger-subtle');
        card.classList.add('bg-light');
        card.style.borderColor = '#dee2e6';
    });
    
    // Menambahkan highlight ke card yang dipilih
    const selectedRadio = document.querySelector('input[name="metode_bayar"]:checked');
    if (selectedRadio) {
        const card = selectedRadio.nextElementSibling;
        card.classList.remove('bg-light');
        card.classList.add('bg-danger-subtle');
        card.style.borderColor = '#800000';
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
    .btn-check:checked + .payment-card {
        border-color: #800000 !important;
        background-color: #fff1f2 !important;
        box-shadow: 0 4px 6px -1px rgba(128, 0, 0, 0.1), 0 2px 4px -1px rgba(128, 0, 0, 0.06) !important;
    }
    .payment-card {
        transition: all 0.2s ease-in-out;
    }
    .payment-card:hover {
        transform: translateY(-2px);
        border-color: #d1d5db;
    }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>