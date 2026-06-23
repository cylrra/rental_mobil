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

$kode_selected = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
$id_pelanggan = $_SESSION['id_pelanggan'];
$query_user = mysqli_query($conn, "SELECT nama, status_verifikasi FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");

if ($query_user) {
    $user_data = mysqli_fetch_assoc($query_user);
    $status_verif = $user_data['status_verifikasi'] ?? 'belum_verifikasi';
    $nama_default = $user_data['nama'] ?? $_SESSION['nama_pelanggan'] ?? '';
} else {
    $status_verif = 'belum_verifikasi';
    $nama_default = $_SESSION['nama_pelanggan'] ?? 'Pelanggan';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --deep-navy: #0f172a; --clear-blue: #3071a4; --frost-veil: #f8fafc; --lilac-dust: #94a3b8; }
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; }
        .card { border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: var(--deep-navy); font-size: 0.9rem; }
        .form-select, .form-control { border-radius: 10px; padding: 10px 15px; border: 1.5px solid #e2e8f0; }
        .btn-primary { background-color: var(--clear-blue); border: none; border-radius: 12px; transition: 0.3s; }
        #catatan_supir { font-size: 0.85rem; color: #dc3545; font-style: italic; display: none; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: var(--deep-navy); font-size: 1.75rem;">
                <i class="bi bi-calendar-check me-2" style="color: var(--clear-blue);"></i>Sewa Mobil & Aktivitas
            </h1>
            <p class="text-muted mb-0">Pesan rental baru atau lihat status pesanan Anda di bawah ini.</p>
        </div>
        <a href="katalog.php" class="btn btn-outline-primary px-4 rounded-pill fw-semibold">
            <i class="bi bi-grid me-1"></i> Lihat Katalog
        </a>
    </div>

    <?php if ($status_verif !== 'terverifikasi'): ?>
    <div class="alert d-flex align-items-start gap-3 mb-4 p-3" style="background: white; border-left: 4px solid #f59e0b; border-radius: 12px;">
        <i class="bi bi-shield-exclamation fs-4 text-warning"></i>
        <div>
            <strong style="color: var(--deep-navy);">Verifikasi Diperlukan</strong>
            <p class="mb-0 mt-1" style="font-size: 0.875rem; color: #555;">
                Akun belum terverifikasi. Untuk sewa <strong>Lepas Kunci</strong>, wajib unggah KTP & SIM di <a href="edit_profil.php" class="fw-bold text-decoration-none">Pengaturan Akun</a>.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100 border-0">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Sewa Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form id="formRental"> <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" class="form-control" value="<?= htmlspecialchars($nama_default) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Mobil</label>
                            <select name="kode_mobil" id="kode_mobil_select" class="form-select" required onchange="hitungTotalEstimasi()">
                                <option value="">— Pilih Armada —</option>
                                <?php
                                $mob = mysqli_query($conn, "SELECT * FROM mobil WHERE Unit_Tersedia > 0");
                                while($m = mysqli_fetch_array($mob)) {
                                    $sel = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                    echo "<option value='{$m['kode_mobil']}' data-tarif='{$m['tarif_per_hari']}' $sel>{$m['merk']} {$m['jenis']} (".number_format($m['tarif_per_hari'],0,',','.')."/hr)</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Layanan Sopir</label>
                            <select name="id_supir" id="id_supir_select" class="form-select" onchange="hitungTotalEstimasi(); toggleCatatanSupir();">
                                <option value="" data-tarif-supir="0">Tidak — Lepas Kunci (Tanpa Sopir)</option>
                                <option value="999" data-tarif-supir="200000">Ya — Pakai Jasa Sopir (+Rp 200.000/hari)</option>
                            </select>
                            <div id="catatan_supir" class="mt-2 text-danger"><i class="bi bi-info-circle me-1"></i>*Harga belum termasuk bensin & makan supir.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi Jemput</label>
                            <select name="lokasi_jemput" id="lokasiSelect" class="form-select" onchange="toggleAlamatInput()">
                                <option value="Ambil di Kantor">Ambil Langsung di Kantor</option>
                                <option value="Antar ke Alamat lainnya">Antar ke Alamat Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inputAlamatCustom" style="display: none;">
                            <input type="text" name="alamat_detail" id="alamatDetail" class="form-control" placeholder="Masukkan alamat lengkap">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Tgl Mulai</label>
                                <input type="date" name="tanggal_sewa" id="tanggal_sewa" class="form-control" value="<?= date('Y-m-d') ?>" required onchange="hitungTotalEstimasi()">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Durasi (Hari)</label>
                                <input type="number" name="lama_sewa" id="lama_sewa" class="form-control" min="1" required oninput="hitungTotalEstimasi()">
                            </div>
                        </div>
                        <div class="mb-4 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Estimasi Total Biaya</span>
                                <span class="fw-bold text-primary" id="disp_total_biaya" style="font-size: 1.1rem;">Rp 0</span>
                            </div>
                        </div>
                        <button type="button" id="btnAjukan" class="btn btn-primary w-100 py-3 fw-bold"><i class="bi bi-check-circle me-2"></i>Ajukan Sewa Sekarang</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-primary"></i>Pesanan Saya</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="tabelPesanan"> <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Order</th>
                                    <th>Mobil</th>
                                    <th>Mulai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT t.id_sewa, t.tanggal_sewa, t.status_sewa, m.merk, m.nopol, r.id_rating 
                                        FROM transaksi_sewa t 
                                        LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                        LEFT JOIN rating_sewa r ON t.id_sewa = r.id_transaksi
                                        WHERE t.id_pelanggan = '$id_pelanggan' 
                                        ORDER BY t.id_sewa DESC";
                                $res = mysqli_query($conn, $sql);
                                
                                if(mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $st = !empty($row['status_sewa']) ? $row['status_sewa'] : 'pending';
                                        $merk = !empty($row['merk']) ? $row['merk'] : 'Mobil Dihapus';
                                        
                                        if($st == 'selesai') { $clr = 'success'; }
                                        elseif($st == 'berjalan') { $clr = 'warning'; }
                                        else { $clr = 'secondary'; }
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#<?= $row['id_sewa'] ?></td>
                                    <td><strong><?= htmlspecialchars($merk) ?></strong><br><small class="text-muted"><?= htmlspecialchars($row['nopol'] ?? '-') ?></small></td>
                                    <td><?= date('d/m/y', strtotime($row['tanggal_sewa'])) ?></td>
                                    <td class="text-center"><span class="badge rounded-pill bg-<?= $clr ?> opacity-75"><?= ucfirst($st) ?></span></td>
                                    <td class="text-center pe-4 d-flex justify-content-center gap-1">
                                        <a href="riwayat_pembayaran.php" class="btn btn-sm btn-light border rounded-pill px-3">Detail</a>
                                        <?php if($st == 'selesai'): ?>
                                            <?php if(empty($row['id_rating'])): ?>
                                                <a href="ulasan_rating.php?id_sewa=<?= $row['id_sewa'] ?>" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold"><i class="bi bi-star-fill me-1"></i>Rating</a>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 d-flex align-items-center"><i class="bi bi-check2-circle me-1"></i>Dinilai</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php } } else { echo "<tr><td colspan='5' class='text-center py-5 text-muted'>Belum ada transaksi.</td></tr>"; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi bawaan lo
    function formatRupiah(angka) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka); }
    function hitungTotalEstimasi() {
        const mSelect = document.getElementById('kode_mobil_select');
        const sSelect = document.getElementById('id_supir_select');
        const inputHari = document.getElementById('lama_sewa');
        const hari = parseInt(inputHari.value) || 0;
        let tMobil = (mSelect.selectedIndex > 0) ? parseFloat(mSelect.options[mSelect.selectedIndex].getAttribute('data-tarif')) : 0;
        let tSupir = (sSelect.selectedIndex > 0) ? parseFloat(sSelect.options[sSelect.selectedIndex].getAttribute('data-tarif-supir')) : 0;
        document.getElementById('disp_total_biaya').innerText = formatRupiah((tMobil + tSupir) * hari);
    }
    function toggleCatatanSupir() { document.getElementById('catatan_supir').style.display = (document.getElementById('id_supir_select').value === "999") ? "block" : "none"; }
    function toggleAlamatInput() { document.getElementById("inputAlamatCustom").style.display = (document.getElementById("lokasiSelect").value === "Antar ke Alamat lainnya") ? "block" : "none"; }

    // Logika AJAX
    $('#btnAjukan').click(function() {
        const supirVal = document.getElementById('id_supir_select').value;
        const statusVerif = "<?= $status_verif ?>";

        if (supirVal === "" && statusVerif !== 'terverifikasi') {
            Swal.fire('Peringatan', 'Akun belum terverifikasi. Sewa Lepas Kunci memerlukan KTP & SIM.', 'warning');
            return;
        }

        var formData = $('#formRental').serialize();

        $.ajax({
            url: 'proses_transaksi.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.trim() === "sukses") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan telah diajukan.',
                        showConfirmButton: true
                    }).then(() => {
                        // Refresh tabel saja
                        $('#tabelPesanan').load(location.href + ' #tabelPesanan > table');
                        $('#formRental')[0].reset();
                        $('#disp_total_biaya').text('Rp 0');
                    });
                } else {
                    Swal.fire('Error', response, 'error');
                }
            }
        });
    });
</script>
</body>
</html>