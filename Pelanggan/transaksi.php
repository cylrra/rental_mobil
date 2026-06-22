<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI: Hanya pelanggan yang boleh masuk
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
$nama_default = $user_data['nama'] ?? $_SESSION['nama_pelanggan'] ?? '';
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--deep-navy); font-size: 1.75rem;">
            <i class="bi bi-calendar-check me-2" style="color: var(--clear-blue);"></i>Sewa Mobil & Aktivitas
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Pesan rental baru atau lihat status pesanan Anda di bawah ini.</p>
    </div>
    <a href="katalog.php" class="btn btn-outline-primary px-4 rounded-pill fw-semibold">
        <i class="bi bi-grid me-1"></i> Lihat Katalog
    </a>
</div>

<!-- Verification Warning Banner -->
<?php if ($status_verif !== 'terverifikasi'): ?>
<div class="alert d-flex align-items-start gap-3 mb-4 p-3" 
     style="background: linear-gradient(135deg, rgba(23,59,111,0.05), rgba(48,113,164,0.08)); border: 1px solid rgba(48,113,164,0.2); border-radius: 14px; border-left: 4px solid var(--clear-blue);">
    <i class="bi bi-shield-exclamation fs-4" style="color: var(--clear-blue);"></i>
    <div>
        <strong style="color: var(--deep-navy);">Verifikasi Dokumen Diperlukan</strong>
        <p class="mb-0 mt-1" style="font-size: 0.875rem; color: #555;">
            Akun Anda belum terverifikasi <span class="badge" style="background: rgba(184,170,180,0.2); color: var(--deep-navy); border: 1px solid var(--lilac-dust);"><?= str_replace('_', ' ', ucfirst($status_verif)) ?></span>.
            Untuk sewa <strong>Lepas Kunci</strong> (tanpa sopir), Anda wajib unggah KTP & SIM di
            <a href="edit_profil.php" style="color: var(--clear-blue); font-weight: 600;">Pengaturan Akun</a>.
            Anda tetap bisa menyewa dengan jasa sopir kami.
        </p>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">

    <!-- ═══ FORM SEWA BARU ═══ -->
    <div class="col-lg-5">
        <div class="card h-100" style="border: 1px solid rgba(135,184,229,0.25);">
            <div class="card-header bg-white border-0 py-3 px-4" 
                 style="border-bottom: 1px solid rgba(135,184,229,0.2) !important; border-radius: 16px 16px 0 0;">
                <h5 class="fw-bold m-0" style="color: var(--deep-navy); font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-calendar-plus me-2" style="color: var(--clear-blue);"></i> Form Sewa Baru
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="proses_transaksi.php" method="POST" id="formRental">
                    <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">

                    <!-- Nama Penyewa (Editable, default dari session) -->
                    <div class="mb-3">
                        <label class="form-label">Nama Penyewa</label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius: 10px 0 0 10px; border: 1.5px solid rgba(135,184,229,0.5); border-right: none; background: var(--frost-veil);">
                                <i class="bi bi-person" style="color: var(--clear-blue);"></i>
                            </span>
                            <input type="text" name="nama_penyewa" class="form-control" 
                                   style="border-radius: 0 10px 10px 0; border-left: none;"
                                   value="<?= htmlspecialchars($nama_default) ?>" 
                                   placeholder="Nama penyewa..." required>
                        </div>
                        <div class="mt-1" style="font-size: 0.75rem; color: var(--lilac-dust);">
                            <i class="bi bi-info-circle me-1"></i>Nama ini digunakan untuk keperluan booking saja.
                        </div>
                    </div>

                    <!-- Pilih Mobil -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Mobil</label>
                        <select name="kode_mobil" id="kode_mobil_select" class="form-select" required onchange="hitungTotalEstimasi()">
                            <option value="">— Pilih Armada Tersedia —</option>
                            <?php
                            $mob = mysqli_query($conn, "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m");
                            while($m = mysqli_fetch_array($mob)) {
                                $stok = (int)$m['stok_realtime'];
                                if ($stok > 0) {
                                    $selected = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                    echo "<option value='{$m['kode_mobil']}' data-tarif='{$m['tarif_per_hari']}' $selected>
                                            {$m['merk']} {$m['jenis']} — Rp " . number_format($m['tarif_per_hari'], 0, ',', '.') . "/hari
                                          </option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Layanan Sopir -->
                    <div class="mb-3">
                        <label class="form-label">Layanan Sopir</label>
                        <select name="id_supir" id="id_supir_select" class="form-select" onchange="hitungTotalEstimasi()">
                            <option value="" data-tarif-supir="0">Tidak — Lepas Kunci (Tanpa Sopir)</option>
                            <option value="999" data-tarif-supir="200000">Ya — Pakai Jasa Sopir (+Rp 200.000/hari)</option>
                        </select>
                    </div>

                    <!-- Lokasi Jemput -->
                    <div class="mb-3">
                        <label class="form-label">Lokasi Jemput Mobil</label>
                        <select name="lokasi_jemput" id="lokasiSelect" class="form-select" required onchange="toggleAlamatInput()">
                            <option value="Ambil di Kantor">Ambil Langsung di Kantor</option>
                            <option value="Antar ke Alamat lainnya">Antar ke Alamat Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-3" id="inputAlamatCustom" style="display: none;">
                        <label class="form-label" style="color: var(--clear-blue);">Alamat Lengkap Pengantaran</label>
                        <input type="text" name="alamat_detail" id="alamatDetail" class="form-control" 
                               placeholder="Cth: Hotel Aston Solo, Jl. Slamet Riyadi No. 10">
                    </div>

                    <!-- Tanggal & Durasi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Mulai Sewa</label>
                            <input type="date" name="tanggal_sewa" id="tanggal_sewa" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required onchange="hitungTotalEstimasi()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durasi (Hari)</label>
                            <input type="number" name="lama_sewa" id="lama_sewa" class="form-control" 
                                   placeholder="Jumlah hari" min="1" required oninput="hitungTotalEstimasi()">
                        </div>
                    </div>

                    <!-- Estimasi Biaya -->
                    <div class="mb-4 p-3 rounded-3" style="background: var(--frost-veil); border: 1px solid rgba(135,184,229,0.35);">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="font-size: 0.85rem; color: var(--lilac-dust);">Tarif Mobil</span>
                            <span class="fw-semibold" style="color: var(--deep-navy); font-size: 0.85rem;" id="disp_tarif_mobil">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span style="font-size: 0.85rem; color: var(--lilac-dust);">Tarif Sopir</span>
                            <span class="fw-semibold" style="color: var(--deep-navy); font-size: 0.85rem;" id="disp_tarif_sopir">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2" style="border-top: 1.5px dashed rgba(135,184,229,0.5);">
                            <span class="fw-bold" style="color: var(--deep-navy);">Estimasi Total</span>
                            <span class="fw-bold" style="color: var(--clear-blue); font-size: 1.15rem;" id="disp_total_biaya">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold" style="font-size: 1rem; letter-spacing: 0.3px;">
                        <i class="bi bi-check-circle me-2"></i> Ajukan Rental Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ═══ RIWAYAT PESANAN ═══ -->
    <div class="col-lg-7">
        <div class="card" style="border: 1px solid rgba(135,184,229,0.25);">
            <div class="card-header bg-white border-0 py-3 px-4"
                 style="border-bottom: 1px solid rgba(135,184,229,0.2) !important; border-radius: 16px 16px 0 0;">
                <h5 class="fw-bold m-0" style="color: var(--deep-navy); font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-clock-history me-2" style="color: var(--clear-blue);"></i> Daftar Sewa Mobil Saya
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
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
                            $sql_riwayat = "SELECT t.*, m.merk, m.jenis, m.nopol 
                                            FROM transaksi_sewa t
                                            JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                            WHERE t.id_pelanggan = '$id_pelanggan'
                                            ORDER BY t.id_sewa DESC";
                            $res_riwayat = mysqli_query($conn, $sql_riwayat);
                            
                            if($res_riwayat && mysqli_num_rows($res_riwayat) > 0){
                                while($row = mysqli_fetch_array($res_riwayat)) {
                                    $id_sewa = $row['id_sewa'];
                                    $status  = $row['status_sewa'];
                                    
                                    if ($status == 'berjalan') {
                                        $badge_style = "background: rgba(255,193,7,0.15); color: #856404; border: 1px solid rgba(255,193,7,0.3);";
                                        $badge_text  = '🔄 Sedang Berjalan';
                                    } elseif ($status == 'selesai') {
                                        $badge_style = "background: rgba(25,135,84,0.12); color: #0a6640; border: 1px solid rgba(25,135,84,0.25);";
                                        $badge_text  = '✅ Selesai';
                                    } else {
                                        $badge_style = "background: rgba(108,117,125,0.12); color: #555; border: 1px solid rgba(108,117,125,0.25);";
                                        $badge_text  = ucfirst($status);
                                    }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold" style="color: var(--clear-blue); font-size: 0.85rem;">#<?= $id_sewa ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: var(--deep-navy); font-size: 0.9rem;"><?= $row['merk'] ?></div>
                                    <div style="font-size: 0.78rem; color: var(--lilac-dust);"><?= $row['jenis'] ?> • <?= $row['nopol'] ?></div>
                                </td>
                                <td style="font-size: 0.85rem;"><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></td>
                                <td style="font-size: 0.85rem;"><?= $row['lama_sewa'] ?> Hari</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" style="<?= $badge_style ?> font-size: 0.75rem;"><?= $badge_text ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if ($status == 'berjalan'): ?>
                                        <a href="pembayaran.php?id=<?= $id_sewa ?>" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                            <i class="bi bi-wallet me-1"></i>Bayar
                                        </a>
                                    <?php elseif ($status == 'selesai'): ?>
                                        <?php
                                        $cek_rating   = mysqli_query($conn, "SELECT * FROM rating_sewa WHERE id_transaksi = '$id_sewa'");
                                        $sudah_rating = mysqli_num_rows($cek_rating) > 0;
                                        if ($sudah_rating): ?>
                                            <span style="color: #0a6640; font-size: 0.8rem; font-weight: 600;">
                                                <i class="bi bi-check2-circle me-1"></i>Dinilai
                                            </span>
                                        <?php else: ?>
                                            <a href="input_rating.php?id_transaksi=<?= $id_sewa ?>" 
                                               class="btn btn-sm rounded-pill px-3 fw-semibold"
                                               style="background: rgba(48,113,164,0.12); color: var(--clear-blue); border: 1px solid rgba(48,113,164,0.25);">
                                                <i class="bi bi-star me-1"></i>Rating
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: var(--lilac-dust); font-size: 0.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center py-5'>
                                        <i class='bi bi-inbox' style='font-size: 2.5rem; color: var(--lilac-dust); display: block; margin-bottom: 12px;'></i>
                                        <span style='color: var(--lilac-dust);'>Anda belum melakukan transaksi penyewaan apapun.</span>
                                        <br><a href='katalog.php' class='btn btn-primary btn-sm mt-3 rounded-pill px-4'>Sewa Sekarang</a>
                                      </td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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

    let tarifMobil = 0;
    let tarifSupir = 0;
    let lamaSewa   = parseInt(lamaSewaInput.value) || 0;

    const selectedMobil = mobilSelect.options[mobilSelect.selectedIndex];
    if (selectedMobil && selectedMobil.value !== "") {
        tarifMobil = parseFloat(selectedMobil.getAttribute('data-tarif')) || 0;
    }
    const selectedSupir = supirSelect.options[supirSelect.selectedIndex];
    if (selectedSupir) {
        tarifSupir = parseFloat(selectedSupir.getAttribute('data-tarif-supir')) || 0;
    }

    document.getElementById('disp_tarif_mobil').innerText = formatRupiah(tarifMobil) + ' × ' + lamaSewa + ' hari';
    document.getElementById('disp_tarif_sopir').innerText  = formatRupiah(tarifSupir) + ' × ' + lamaSewa + ' hari';
    document.getElementById('disp_total_biaya').innerText  = formatRupiah((tarifMobil + tarifSupir) * lamaSewa);
}

function toggleAlamatInput() {
    const val = document.getElementById("lokasiSelect").value;
    const box = document.getElementById("inputAlamatCustom");
    const field = document.getElementById("alamatDetail");
    if (val === "Antar ke Alamat lainnya") {
        box.style.display = "block"; field.required = true;
    } else {
        box.style.display = "none"; field.required = false; field.value = "";
    }
}

document.getElementById('formRental').addEventListener('submit', function(e) {
    const supirVal  = document.getElementById('id_supir_select').value;
    const statusVerif = "<?= $status_verif ?>";
    if (supirVal === "" && statusVerif !== 'terverifikasi') {
        e.preventDefault();
        alert('⚠️ Rental Lepas Kunci memerlukan verifikasi dokumen.\n\nSilakan unggah KTP & SIM di menu Pengaturan Akun, atau pilih opsi Dengan Sopir.');
    }
});

window.onload = hitungTotalEstimasi;
</script>

<!-- Footer component closes wrapper divs -->
</div> </div> </div> </body>
</html>