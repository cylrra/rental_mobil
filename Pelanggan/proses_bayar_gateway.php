<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// PROTEKSI KETAT: Hanya pelanggan yang boleh bayar
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

if (!isset($_POST['lanjut_bayar'])) {
    header("Location: pembayaran.php");
    exit();
}

$id_sewa        = mysqli_real_escape_string($conn, $_POST['id_transaksi']); 
$tgl_bayar      = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);    
$jumlah_bayar   = mysqli_real_escape_string($conn, $_POST['jumlah_bayar']);
$metode         = mysqli_real_escape_string($conn, $_POST['metode_bayar']);  
$tipe_bayar     = mysqli_real_escape_string($conn, $_POST['tipe_pembayaran']);

// Ambil detail sewa untuk ditampilkan
$query = "SELECT t.*, m.merk, m.nopol 
          FROM transaksi_sewa t 
          JOIN mobil m ON t.kode_mobil = m.kode_mobil 
          WHERE t.id_sewa = '$id_sewa'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Dummy Account Numbers / QR
$is_qris = (strpos(strtolower($metode), 'e-wallet') !== false || strpos(strtolower($metode), 'gopay') !== false || strpos(strtolower($metode), 'ovo') !== false);
$virtual_account = '8077' . str_pad($data['no_telp'] ?? '12345678', 12, '0', STR_PAD_RIGHT); 

include 'navbar.php'; 
?>

<style>
    .bg-gateway { background-color: #f8fafc; }
    .card-gateway { border-radius: 24px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .header-gateway { background-color: #1a1c1c; color: #fff; padding: 24px; text-align: center; }
    .pulse-dot { width: 8px; height: 8px; border-radius: 50%; background-color: #34d399; display: inline-block; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); } 70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(52, 211, 153, 0); } 100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); } }
    .box-qr-va { background-color: #f8fafc; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; text-align: center; position: relative; overflow: hidden; }
    .btn-bayar-sekarang { background-color: #9e0000; color: #fff; font-weight: 700; padding: 14px; border-radius: 12px; width: 100%; border: none; transition: 0.3s; }
    .btn-bayar-sekarang:hover { background-color: #7a0000; transform: translateY(-2px); }
</style>

<div class="container-fluid px-4 py-5 bg-gateway min-vh-100 d-flex align-items-center justify-content-center">
    <div class="w-100" style="max-width: 450px; margin: 0 auto;">
        
        <div class="card-gateway bg-white">
            
            <!-- Gateway Header -->
            <div class="header-gateway">
                <h5 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 2px; color: #cbd5e1;">INDOMAX PAY</h5>
                <p class="small text-muted mb-0" style="color: #94a3b8 !important;">Secure Payment Gateway</p>
                <div class="mt-3 d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background: rgba(255,255,255,0.1);">
                    <div class="pulse-dot"></div>
                    <span class="fw-bold" style="letter-spacing: 1px;" id="countdown">15:00</span>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold text-secondary" style="font-size: 0.9rem;">ID Pesanan</span>
                    <span class="fw-bolder text-dark" style="font-size: 0.9rem;">#SRV-<?= htmlspecialchars($id_sewa) ?></span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold text-secondary" style="font-size: 0.9rem;">Tipe Pembayaran</span>
                    <span class="badge bg-warning text-dark fw-bolder text-uppercase" style="letter-spacing: 1px; padding: 6px 12px;"><?= htmlspecialchars($tipe_bayar) ?></span>
                </div>

                <div class="py-4 border-top border-bottom mb-4 text-center">
                    <span class="fw-bold text-secondary d-block mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 2px;">Total Pembayaran</span>
                    <h2 class="fw-black mb-0" style="color: #9e0000; font-weight: 900; font-size: 2.2rem;">Rp <?= number_format($jumlah_bayar, 0, ',', '.') ?></h2>
                </div>

                <?php 
                $is_cash = (strpos(strtolower($metode), 'cash') !== false || strpos(strtolower($metode), 'tunai') !== false);
                ?>
                <div class="mb-4">
                    <span class="fw-bold text-secondary d-block mb-3" style="font-size: 0.9rem;">
                        <?= $is_qris ? 'Scan QRIS' : ($is_cash ? 'Instruksi Pembayaran Tunai' : 'Nomor Virtual Account') ?>
                    </span>
                    
                    <div class="box-qr-va">
                        <!-- Background Accent -->
                        <div style="position: absolute; right: -40px; top: -40px; width: 120px; height: 120px; background: rgba(226, 232, 240, 0.5); border-radius: 50%; filter: blur(20px);"></div>
                        
                        <?php if ($is_qris): 
                            $qr_text = urlencode("Tujuan Pembayaran: 0881010715798\nTotal Tagihan: Rp " . number_format($jumlah_bayar, 0, ',', '.'));
                        ?>
                            <div class="bg-white p-3 d-inline-block rounded-3 shadow-sm border mb-3 position-relative" style="z-index: 10;">
                                <!-- Dummy QR Code with Amount -->
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $qr_text ?>" alt="QRIS" width="130" height="130">
                            </div>
                            <h5 class="fw-bold text-dark mb-1 position-relative" style="z-index: 10;"><?= htmlspecialchars($metode) ?></h5>
                            <p class="small text-secondary fw-medium position-relative" style="z-index: 10;">Scan QR code di atas menggunakan aplikasi E-Wallet Anda.</p>
                        <?php elseif ($is_cash): ?>
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2 position-relative" style="z-index: 10;">
                                <i class="bi bi-cash-coin text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1 position-relative" style="z-index: 10;"><?= htmlspecialchars($metode) ?></h5>
                            <p class="small text-secondary fw-medium position-relative" style="z-index: 10;">Silakan lakukan pembayaran langsung di loket kami saat Anda mengambil mobil.</p>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2 position-relative" style="z-index: 10;">
                                <h3 class="fw-bold text-dark mb-0" style="letter-spacing: 2px;" id="va_number"><?= $virtual_account ?></h3>
                                <button onclick="copyVA()" class="btn btn-sm btn-light border d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Salin">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                            <h6 class="fw-bold text-secondary mb-1 position-relative" style="z-index: 10;"><?= htmlspecialchars($metode) ?></h6>
                            <p class="small text-secondary fw-medium position-relative" style="z-index: 10;">Gunakan metode transfer ke Virtual Account di atas.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form to actual processing -->
                <form action="proses_bayar.php" method="POST" id="form-simulasi">
                    <input type="hidden" name="id_transaksi" value="<?= htmlspecialchars($id_sewa) ?>">
                    <input type="hidden" name="tgl_bayar" value="<?= htmlspecialchars($tgl_bayar) ?>">
                    <input type="hidden" name="tipe_pembayaran" value="<?= htmlspecialchars($tipe_bayar) ?>">
                    <input type="hidden" name="metode_bayar" value="<?= htmlspecialchars($metode) ?>">
                    <input type="hidden" name="jumlah_bayar" value="<?= htmlspecialchars($jumlah_bayar) ?>">
                    
                    <!-- Ini yang ditangkap proses_bayar.php untuk deteksi form submit -->
                    <input type="hidden" name="simpan_pembayaran" value="1">
                    
                    <button type="button" onclick="simulasikanPembayaran()" class="btn-bayar-sekarang d-flex justify-content-center align-items-center gap-2">
                        <i class="bi bi-check-circle"></i> Bayar Sekarang (Simulasi)
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="pembayaran.php" class="small fw-bold text-decoration-none text-muted">Batal & Kembali</a>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Modal Processing -->
<div class="modal fade" id="processingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body text-center">
                <div class="bg-white rounded-2xl p-6 shadow-2xl mx-auto inline-block">
                    <div class="spinner-border text-[#800000] mb-4" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h6 class="font-bold text-slate-800 mb-1">Memproses Pembayaran</h6>
                    <p class="text-xs text-slate-500 mb-0">Mohon tunggu sebentar...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Countdown Timer logic
    let totalSeconds = 15 * 60; // 15 minutes
    const countdownEl = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        totalSeconds--;
        if (totalSeconds < 0) {
            clearInterval(timer);
            alert("Waktu pembayaran telah habis!");
            window.location.href = 'pembayaran.php';
            return;
        }
        
        let m = Math.floor(totalSeconds / 60);
        let s = totalSeconds % 60;
        
        countdownEl.innerText = m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }, 1000);

    // Copy VA Logic
    function copyVA() {
        const vaNumber = document.getElementById('va_number').innerText;
        navigator.clipboard.writeText(vaNumber).then(() => {
            alert('Nomor Virtual Account disalin: ' + vaNumber);
        });
    }

    // Simulasi AJAX Request
    function simulasikanPembayaran() {
        const modal = new bootstrap.Modal(document.getElementById('processingModal'));
        modal.show();
        
        const form = document.getElementById('form-simulasi');
        const formData = new FormData(form);
        
        // Simulasikan delay API
        setTimeout(() => {
            fetch('proses_bayar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                modal.hide();
                if (data.trim() === 'success') {
                    // Berhasil, arahkan ke riwayat
                    window.location.href = 'riwayat_pembayaran.php?status=success';
                } else {
                    alert('Gagal memproses pembayaran: ' + data);
                }
            })
            .catch(error => {
                modal.hide();
                alert('Terjadi kesalahan jaringan.');
            });
        }, 2000); // 2 detik delay
    }
</script>
</body>
</html>
