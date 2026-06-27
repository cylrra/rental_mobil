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

<style>
/* ===== Pembayaran Premium UI ===== */
.pay-wrap {
    max-width: 780px;
    margin: 0 auto;
    padding: 24px 16px 48px;
}
.pay-header-banner {
    background: linear-gradient(135deg, #0F172A 0%, #8B0000 100%);
    border-radius: 18px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.pay-header-banner::after {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: rgba(212,175,55,0.1);
    border-radius: 50%;
}
.pay-card {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(15,23,42,0.07);
    overflow: hidden;
    margin-bottom: 20px;
}
.pay-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    gap: 12px;
    background: #FAFBFC;
}
.pay-card-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(139,0,0,0.1);
    color: #8B0000;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.pay-card-title { font-weight: 800; color: #0F172A; font-size: 0.95rem; margin: 0; }
.pay-card-sub   { font-size: 0.75rem; color: #94A3B8; margin: 0; }
.pay-card-body  { padding: 24px; }

/* Pending status warning */
.pending-notice {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: rgba(217,119,6,0.08);
    border: 1px solid rgba(217,119,6,0.2);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 16px;
}
.pending-notice i { color: #D97706; font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
.pending-notice-text { font-size: 0.83rem; color: #92400E; }
.pending-notice-text strong { display: block; margin-bottom: 3px; }

/* Form select styling */
.form-label-modern {
    font-size: 0.73rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748B;
    display: block;
    margin-bottom: 8px;
}
.select-modern, .input-modern {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #E8ECF2;
    border-radius: 10px;
    font-family: 'Plus Jakarta Sans', 'Montserrat', sans-serif;
    font-size: 0.9rem;
    color: #0F172A;
    background: #FAFBFC;
    appearance: none;
    transition: all 0.2s;
    outline: none;
}
.select-modern:focus, .input-modern:focus {
    border-color: #8B0000;
    box-shadow: 0 0 0 3px rgba(139,0,0,0.1);
    background: #fff;
}

/* Payment method cards */
.pay-method-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
.pay-method-card {
    border: 2px solid #E8ECF2;
    border-radius: 12px;
    padding: 14px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s;
    background: #FAFBFC;
}
.pay-method-card:hover {
    border-color: #8B0000;
    background: rgba(139,0,0,0.03);
    transform: translateY(-1px);
}
.pay-method-card.selected {
    border-color: #8B0000;
    background: rgba(139,0,0,0.06);
    box-shadow: 0 0 0 1px #8B0000;
}
.pay-method-logo {
    width: 44px; height: 34px;
    border-radius: 7px;
    background: #fff;
    border: 1px solid #E8ECF2;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 0.65rem;
    font-weight: 900;
}
.pay-method-name { font-size: 0.78rem; font-weight: 700; color: #0F172A; line-height: 1.3; }

/* Summary box */
.pay-summary {
    background: linear-gradient(135deg, #0F172A 0%, #1e293b 100%);
    border-radius: 14px;
    padding: 20px 22px;
    margin: 16px 0;
}
.pay-summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 0.85rem;
}
.pay-summary-row .lbl { color: rgba(255,255,255,0.6); }
.pay-summary-row .val { color: #fff; font-weight: 600; }
.pay-summary-row.total { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.pay-summary-row.total .lbl { color: rgba(255,255,255,0.8); font-weight: 700; font-size: 0.9rem; }
.pay-summary-row.total .val { color: #D4AF37; font-weight: 900; font-size: 1.2rem; }

/* Submit button */
.btn-pay-submit {
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #8B0000, #c0392b);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: 'Plus Jakarta Sans', 'Montserrat', sans-serif;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 20px rgba(139,0,0,0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-pay-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(139,0,0,0.45);
}

@media (max-width: 640px) {
    .pay-method-grid { grid-template-columns: repeat(2,1fr); }
    .pay-header-banner { padding: 22px 20px; }
    .pay-card-body { padding: 18px; }
}
</style>

    <!-- Banner -->
    <div class="pay-header-banner">
        <div style="position:relative;z-index:2;">
            <div style="font-size:0.7rem;font-weight:800;color:#D4AF37;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;"><i class="bi bi-wallet2 me-2"></i>Portal Pembayaran</div>
            <h1 style="font-size:1.5rem;font-weight:800;margin:0 0 6px;color:#fff;">Pembayaran Sewa Mobil</h1>
            <p style="font-size:0.85rem;color:rgba(255,255,255,0.65);margin:0;">Selesaikan pembayaran untuk mengaktifkan pesanan Anda.</p>
        </div>
    </div>

    <!-- Pay Card -->
    <div class="pay-card">
        <div class="pay-card-header">
            <div class="pay-card-icon"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <p class="pay-card-title">Detail Pembayaran</p>
                <p class="pay-card-sub">Pilih transaksi, metode, dan tipe pembayaran</p>
            </div>
        </div>
        <div class="pay-card-body">
        <form action="proses_bayar_gateway.php" method="POST">
            <!-- Transaksi dropdown -->
            <div style="margin-bottom:16px;">
                <label class="form-label-modern">Pilih Transaksi Aktif</label>
                <div style="position:relative;">
                    <select name="id_transaksi" id="id_transaksi_select" class="select-modern" required onchange="updateEstimasiBayar()" style="padding-right:40px;">
                        <option value="">— Pilih Order —</option>
                                <option value="">-- Pilih Transaksi --</option>
                                <?php 
                                // Ambil hanya transaksi milik pelanggan yang berstatus 'berjalan' atau 'diterima' atau 'DP'
                                $sql_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari, m.merk 
                                                              FROM transaksi_sewa t 
                                                              LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                                              LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                                              WHERE t.status_sewa IN ('berjalan', 'diterima', 'DP', 'pending') AND t.id_pelanggan = '$id_pelanggan'");
                                
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
                    <i class="bi bi-chevron-down" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#94A3B8;pointer-events:none;"></i>
                </div>
            </div>

            <!-- Date + Type Row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label class="form-label-modern">Tanggal Pembayaran</label>
                    <input type="date" name="tgl_bayar" class="input-modern" value="<?php echo date('Y-m-d'); ?>" required readonly>
                </div>
                <div>
                    <label class="form-label-modern">Tipe Pembayaran</label>
                    <div style="position:relative;">
                        <select name="tipe_pembayaran" id="jenis_pembayaran" class="select-modern" required onchange="updateEstimasiBayar()" style="padding-right:36px;">
                            <option value="Lunas">Bayar Lunas (100%)</option>
                            <option value="DP">Uang Muka (DP 50%)</option>
                        </select>
                        <i class="bi bi-chevron-down" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94A3B8;pointer-events:none;"></i>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div style="margin-bottom:16px;">
                <label class="form-label-modern">Pilih Metode Pembayaran</label>
                <div class="pay-method-grid">
                <!-- Metode items -->
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="Transfer Bank BCA" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'Transfer Bank BCA')">
                        <div class="pay-method-logo"><span style="color:#0066AE;font-size:0.7rem;font-weight:900;">BCA</span></div>
                        <div class="pay-method-name">BCA<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">Virtual Account</span></div>
                    </div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="Transfer Bank Mandiri" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'Transfer Bank Mandiri')">
                        <div class="pay-method-logo"><span style="color:#003D79;font-size:0.65rem;font-weight:900;">MANDIRI</span></div>
                        <div class="pay-method-name">Mandiri<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">Virtual Account</span></div>
                    </div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="Transfer Bank BNI" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'Transfer Bank BNI')">
                        <div class="pay-method-logo"><span style="color:#F15A24;font-size:0.7rem;font-weight:900;font-style:italic;">BNI</span></div>
                        <div class="pay-method-name">BNI<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">Virtual Account</span></div>
                    </div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="E-Wallet GoPay" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'E-Wallet GoPay')">
                        <div class="pay-method-logo"><span style="color:#00AED6;font-size:0.65rem;font-weight:900;">GoPay</span></div>
                        <div class="pay-method-name">GoPay<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">E-Wallet</span></div>
                    </div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="E-Wallet OVO" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'E-Wallet OVO')">
                        <div class="pay-method-logo"><span style="color:#4C2A86;font-size:0.7rem;font-weight:900;">OVO</span></div>
                        <div class="pay-method-name">OVO<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">E-Wallet</span></div>
                    </div>
                </label>
                <label style="cursor:pointer;">
                    <input type="radio" name="metode_bayar" value="Cash / Tunai" class="btn-check" required onchange="updatePaymentUI()" style="display:none;">
                    <div class="pay-method-card" onclick="selectMethod(this,'Cash / Tunai')">
                        <div class="pay-method-logo"><i class="bi bi-cash-stack" style="color:#16A34A;font-size:1.1rem;"></i></div>
                        <div class="pay-method-name">Tunai<br><span style="font-size:0.65rem;color:#94A3B8;font-weight:500;">Bayar di Tempat</span></div>
                    </div>
                </label>
            </div>
            </div><!-- end metode -->

            <!-- Summary Box -->
            <div class="pay-summary">
                <div class="pay-summary-row">
                    <span class="lbl">Total Tagihan Rental</span>
                    <span class="val" id="disp_tagihan_asli">Rp 0</span>
                </div>
                <div class="pay-summary-row" id="row_potongan" style="display:none;">
                    <span class="lbl" id="label_potongan">DP 50%</span>
                    <span class="val" id="disp_potongan">Rp 0</span>
                </div>
                <div class="pay-summary-row total">
                    <span class="lbl">Jumlah Harus Dibayar</span>
                    <span class="val" id="disp_jumlah_bayar">Rp 0</span>
                </div>
            </div>

            <!-- Hidden input -->
            <input type="hidden" name="jumlah_bayar" id="jumlah_bayar_raw" value="<?= htmlspecialchars($initial_tagihan); ?>">

            <!-- Submit -->
            <button type="submit" name="lanjut_bayar" class="btn-pay-submit">
                <i class="bi bi-shield-lock-fill"></i> Lanjutkan Pembayaran
            </button>
            <a href="riwayat_pembayaran.php" style="display:block;text-align:center;margin-top:12px;font-size:0.82rem;color:#94A3B8;text-decoration:none;">← Batal &amp; Kembali</a>
        </form>
        </div><!-- pay-card-body -->
    </div><!-- pay-card -->

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