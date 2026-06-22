<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Tangkap kode mobil dari URL (jika ada)
$kode_selected = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';

// Mengambil ID Pelanggan dan status verifikasi dari database
$id_pelanggan = $_SESSION['id_pelanggan'];
$query_user = mysqli_query($conn, "SELECT nama, status_verifikasi FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$user_data = mysqli_fetch_assoc($query_user);
$status_verif = $user_data['status_verifikasi'] ?? 'belum_verifikasi';
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 my-2">
            <h1 class="fw-bold" style="font-family: 'Outfit', sans-serif; color: #0f172a;">Sewa Mobil & Aktivitas</h1>
            <p class="text-muted">Kelola rental kendaraan aktif Anda atau sewa mobil baru di bawah ini.</p>
        </div>
    </div>

    <!-- Self-Drive Verification Warning -->
    <?php if ($status_verif !== 'terverifikasi'): ?>
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4 p-3 d-flex align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-warning"></i>
            <div>
                <strong class="d-block mb-1">Verifikasi Dokumen Diperlukan!</strong>
                Akun Anda belum terverifikasi (<span class="text-capitalize"><?= str_replace('_', ' ', $status_verif) ?></span>). Untuk sewa **Lepas Kunci (Tanpa Sopir)**, Anda wajib mengunggah foto KTP dan SIM A di menu <a href="edit_profil.php" class="alert-link fw-bold text-decoration-underline">Pengaturan Akun</a>. Anda tetap bisa menyewa mobil menggunakan jasa sopir kami.
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- FORM RENTAL BARU (Left Column) -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-white h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold text-dark m-0"><i class="bi bi-calendar-plus text-primary me-2"></i> Form Sewa Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="proses_transaksi.php" method="POST" id="formRental">
                        <!-- Locked Customer (Hidden Input & Readonly Text) -->
                        <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Pelanggan</label>
                            <input type="text" class="form-control bg-light border-0 py-2-5" value="<?= htmlspecialchars($user_data['nama']) ?>" readonly>
                        </div>

                        <!-- Car Selector -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Pilih Mobil</label>
                            <select name="kode_mobil" id="kode_mobil_select" class="form-select py-2-5" required onchange="hitungTotalEstimasi()">
                                <option value="">-- Pilih Mobil Tersedia --</option>
                                <?php
                                // Ambil daftar mobil yang memiliki unit tersedia secara realtime
                                $mob = mysqli_query($conn, "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m");
                                while($m = mysqli_fetch_array($mob)) {
                                    $stok = (int)$m['stok_realtime'];
                                    if ($stok > 0) {
                                        $selected = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                        echo "<option value='{$m['kode_mobil']}' data-tarif='{$m['tarif_per_hari']}' $selected>{$m['merk']} ({$m['jenis']}) - Rp " . number_format($m['tarif_per_hari'], 0, ',', '.') . "/hari</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Driver Options -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Layanan Sopir</label>
                            <select name="id_supir" id="id_supir_select" class="form-select py-2-5" onchange="hitungTotalEstimasi()">   
                                <option value="" data-tarif-supir="0">Tidak (Lepas Kunci)</option>
                    
                                <option value="999" data-tarif-supir="200000">Ya (Pakai Jasa Sopir)</option>
                            </select>
                        </div>

                        <!-- Pickup Location (lokasi_jemput) -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Lokasi Jemput Mobil</label>
                            <select name="lokasi_jemput" id="lokasiSelect" class="form-select py-2-5" required onchange="toggleAlamatInput()">
                                <option value="Ambil di Kantor">Ambil Langsung di Kantor</option>
                                <option value="Antar ke Alamat lainnya">Antar ke Alamat lainnya</option>
                            </select>
                        </div>

                        <div class="mb-3" id="inputAlamatCustom" style="display: none;">
                            <label class="form-label small fw-bold text-danger">Masukkan Alamat Lengkap Pengantaran</label>
                            <input type="text" name="alamat_detail" id="alamatDetail" class="form-control py-2-5" placeholder="Contoh: Hotel Aston Solo, Kamar 302 / Jl. Slamet Riyadi No. 10">
                        </div>

                        <script>
                        function toggleAlamatInput() {
                            var selectBox = document.getElementById("lokasiSelect");
                            var selectedValue = selectBox.options[selectBox.selectedIndex].value;
                            var customInputDiv = document.getElementById("inputAlamatCustom");
                            var alamatInputField = document.getElementById("alamatDetail");

                            if (selectedValue === "Antar ke Alamat lainnya") {
                                // Tampilkan inputan ketik jika memilih opsi alamat
                                customInputDiv.style.display = "block";
                                alamatInputField.required = true; // Wajib diisi kalau muncul
                            } else {
                                // Sembunyikan dan kosongkan jika memilih opsi lain
                                customInputDiv.style.display = "none";
                                alamatInputField.required = false;
                                alamatInputField.value = ""; 
                            }
                        }
                        </script>

                        <!-- Date & Duration -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa" class="form-control py-2-5" value="<?php echo date('Y-m-d'); ?>" required onchange="hitungTotalEstimasi()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Durasi Sewa (Hari)</label>
                                <input type="number" name="lama_sewa" id="lama_sewa" class="form-control py-2-5" placeholder="Hari" min="1" required oninput="hitungTotalEstimasi()">
                            </div>
                        </div>

                        <!-- Estimation Alert -->
                        <div class="alert bg-light border p-3 rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Tarif Mobil</span>
                                <span class="fw-semibold text-dark" id="disp_tarif_mobil">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Tarif Sopir</span>
                                <span class="fw-semibold text-dark" id="disp_tarif_sopir">Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Estimasi Total Biaya</span>
                                <span class="fw-extrabold text-primary fs-5" id="disp_total_biaya">Rp 0</span>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Ajukan Rental Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- PESANAN SAYA & RIWAYAT (Right Column) -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 rounded-4 bg-white">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold text-dark m-0"><i class="bi bi-clock-history text-primary me-2"></i> Daftar Sewa Mobil Saya</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No. Order</th>
                                    <th>Detail Mobil</th>
                                    <th>Tgl Mulai</th>
                                    <th>Durasi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Ambil hanya transaksi milik pelanggan bersangkutan
                                $sql_riwayat = "SELECT t.*, m.merk, m.jenis, m.nopol 
                                                FROM transaksi_sewa t
                                                JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                                WHERE t.id_pelanggan = '$id_pelanggan'
                                                ORDER BY t.id_sewa DESC";
                                $res_riwayat = mysqli_query($conn, $sql_riwayat);
                                
                                if($res_riwayat && mysqli_num_rows($res_riwayat) > 0){
                                    while($row = mysqli_fetch_array($res_riwayat)) {
                                        $id_sewa = $row['id_sewa'];
                                        $status = $row['status_sewa'];
                                        
                                        // Badge Status
                                        if ($status == 'berjalan') {
                                            $badge_class = 'bg-warning text-dark';
                                            $badge_text = 'Sedang Berjalan';
                                        } elseif ($status == 'selesai') {
                                            $badge_class = 'bg-success text-white';
                                            $badge_text = 'Selesai';
                                        } elseif ($status == 'DP') {
                                            $badge_class = 'bg-info text-dark';
                                            $badge_text = 'Terkonfirmasi DP';
                                        } else {
                                            $badge_class = 'bg-secondary text-white';
                                            $badge_text = ucfirst($status);
                                        }
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted small">#SRV-<?= $id_sewa ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= $row['merk'] ?></div>
                                        <div class="text-muted small"><?= $row['jenis'] ?> (<?= $row['nopol'] ?>)</div>
                                    </td>
                                    <td><small><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></small></td>
                                    <td><?= $row['lama_sewa'] ?> Hari</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?= $badge_class ?> px-2.5 py-1.5"><?= $badge_text ?></span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <?php if ($status == 'berjalan'): ?>
                                                <a href="pembayaran.php?id=<?= $id_sewa ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    Bayar
                                                </a>
                                            <?php elseif ($status == 'selesai'): ?>
                                                <?php
                                                // Cek apakah user sudah memberikan rating untuk transaksi ini
                                                $cek_rating = mysqli_query($conn, "SELECT * FROM rating_sewa WHERE id_transaksi = '$id_sewa'");
                                                $sudah_rating = mysqli_num_rows($cek_rating) > 0;
                                                
                                                if ($sudah_rating): ?>
                                                    <span class="text-success small fw-bold"><i class="bi bi-check2-circle"></i> Dinilai</span>
                                                <?php else: ?>
                                                    <a href="input_rating.php?id_transaksi=<?= $id_sewa ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                                        Beri Rating
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'><i class='bi bi-inbox fs-2 d-block mb-2'></i> Anda belum melakukan transaksi penyewaan apapun.</td></tr>";
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

<script>
function formatRupiah(angka) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

function hitungTotalEstimasi() {
    const mobilSelect = document.getElementById('kode_mobil_select');
    const supirSelect = document.getElementById('id_supir_select');
    const lamaSewaInput = document.getElementById('lama_sewa');

    const dispTarifMobil = document.getElementById('disp_tarif_mobil');
    const dispTarifSupir = document.getElementById('disp_tarif_sopir');
    const dispTotalBiaya = document.getElementById('disp_total_biaya');

    let tarifMobil = 0;
    let tarifSupir = 0;
    let lamaSewa = parseInt(lamaSewaInput.value) || 0;

    const selectedMobil = mobilSelect.options[mobilSelect.selectedIndex];
    if (selectedMobil && selectedMobil.value !== "") {
        tarifMobil = parseFloat(selectedMobil.getAttribute('data-tarif')) || 0;
    }

    const selectedSupir = supirSelect.options[supirSelect.selectedIndex];
    if (selectedSupir && selectedSupir.value !== "") {
        tarifSupir = parseFloat(selectedSupir.getAttribute('data-tarif-supir')) || 0;
    }

    // Display sub-rates
    dispTarifMobil.innerText = formatRupiah(tarifMobil) + ' x ' + lamaSewa + ' Hari';
    dispTarifSupir.innerText = formatRupiah(tarifSupir) + ' x ' + lamaSewa + ' Hari';

    // Calculate total
    let total = (tarifMobil + tarifSupir) * lamaSewa;
    dispTotalBiaya.innerText = formatRupiah(total);
}

// Intercept form submission to warn about Lepas Kunci document requirements
document.getElementById('formRental').addEventListener('submit', function(e) {
    const supirSelect = document.getElementById('id_supir_select');
    const statusVerif = "<?= $status_verif ?>";
    
    // If Lepas Kunci (no driver selected) and user is unverified
    if (supirSelect.value === "" && statusVerif !== 'terverifikasi') {
        e.preventDefault();
        alert('Pengajuan Gagal: Rental Lepas Kunci memerlukan dokumen identitas terverifikasi. Silakan unggah KTP & SIM di menu Pengaturan Akun, atau pilih opsi Dengan Sopir.');
    }
});

// Run estimate calculation once on load
window.onload = function() {
    hitungTotalEstimasi();
}
</script>

<style>
    .py-2-5 { padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .btn-outline-warning:hover {
        color: #000;
    }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>