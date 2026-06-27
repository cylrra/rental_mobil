<?php 
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}
include 'navbar.php';
include 'koneksi.php'; 

// Jika tombol "btn_sewa" ditekan
if (isset($_POST['btn_sewa'])) {
    $id_pelanggan = $_SESSION['id_pelanggan'];
    $kode_mobil = $_POST['kode_mobil'];
    $tanggal_sewa = $_POST['tanggal_sewa'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $tujuan = $_POST['tujuan_perjalanan'];
    $pake_supir = $_POST['pake_supir']; // 'Ya' atau 'Tidak'

    // Tentukan Area Pemakaian berdasarkan string "(Luar Kota)"
    $area_pemakaian = (strpos($tujuan, '(Luar Kota)') !== false) ? 'Luar Kota' : 'Dalam Kota';

    // 1. Ambil harga sewa dari tabel mobil (Prepared Statement)
    $stmt_harga = mysqli_prepare($conn, "SELECT harga_sewa FROM mobil WHERE kode_mobil = ?");
    mysqli_stmt_bind_param($stmt_harga, "s", $kode_mobil);
    mysqli_stmt_execute($stmt_harga);
    $res_harga = mysqli_stmt_get_result($stmt_harga);
    $data_m = mysqli_fetch_assoc($res_harga);
    $harga_dasar = $data_m['harga_sewa'];
    mysqli_stmt_close($stmt_harga);

    // 2. Hitung durasi dan validasi H-1
    $d1 = new DateTime($tanggal_sewa);
    $d2 = new DateTime($tanggal_kembali);
    
    $today = new DateTime(date('Y-m-d'));
    if ($d1 <= $today) {
        echo "<script>alert('Pemesanan gagal! Minimal pemesanan adalah H+1 (untuk besok).'); window.history.back();</script>";
        exit;
    }
    if ($d2 < $d1) {
        echo "<script>alert('Pemesanan gagal! Tanggal kembali tidak boleh lebih awal dari tanggal sewa.'); window.history.back();</script>";
        exit;
    }
    
    $durasi = $d1->diff($d2)->days;
    $durasi = ($durasi == 0) ? 1 : $durasi; // Minimal 1 hari

    // 2.5 CEK BENTROK DENGAN JADWAL SERVIS
    $stmt_cek_servis = mysqli_prepare($conn, "SELECT tanggal_pemeliharaan, jenis_pemeliharaan FROM pemeliharaan WHERE kode_mobil = ? AND status = 'terjadwal' AND tanggal_pemeliharaan BETWEEN ? AND ?");
    mysqli_stmt_bind_param($stmt_cek_servis, "sss", $kode_mobil, $tanggal_sewa, $tanggal_kembali);
    mysqli_stmt_execute($stmt_cek_servis);
    $res_servis = mysqli_stmt_get_result($stmt_cek_servis);
    if (mysqli_num_rows($res_servis) > 0) {
        $data_servis = mysqli_fetch_assoc($res_servis);
        $tgl_servis_format = date('d M Y', strtotime($data_servis['tanggal_pemeliharaan']));
        $jenis_servis = $data_servis['jenis_pemeliharaan'];
        echo "<script>alert('Pemesanan gagal! Mobil ini dijadwalkan untuk \"$jenis_servis\" pada tanggal $tgl_servis_format. Silakan pilih tanggal lain atau mobil lain.'); window.history.back();</script>";
        exit;
    }
    mysqli_stmt_close($stmt_cek_servis);

    // 3. Kalkulasi Harga Baru (Lebih Manusiawi)
    $biaya_sewa_mobil = $harga_dasar * $durasi;
    
    // Biaya Tambahan Luar Kota (Tarif penyesuaian: Rp 50.000 / hari)
    $biaya_luar_kota = ($area_pemakaian === 'Luar Kota') ? (50000 * $durasi) : 0;
    
    // Biaya Supir (Tarif manusiawi: Rp 150.000 / hari)
    $tarif_supir_per_hari = 150000;
    $biaya_supir = ($pake_supir === 'Ya') ? ($tarif_supir_per_hari * $durasi) : 0;

    $opsi_supir_enum = ($pake_supir === 'Ya') ? 'ya' : 'tidak';

    $total_biaya = $biaya_sewa_mobil + $biaya_luar_kota + $biaya_supir;
    $total_bayar = $total_biaya; // Karena sama

    // 4. Simpan ke database
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO transaksi_sewa 
        (id_pelanggan, kode_mobil, tanggal_sewa, tanggal_kembali, total_biaya, total_bayar, tujuan_perjalanan, pake_supir, opsi_supir, biaya_supir, area_pemakaian, lama_sewa) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "isssiisssdsi", 
        $id_pelanggan, 
        $kode_mobil, 
        $tanggal_sewa, 
        $tanggal_kembali, 
        $total_biaya, 
        $total_bayar,
        $tujuan, 
        $pake_supir,
        $opsi_supir_enum, 
        $biaya_supir, 
        $area_pemakaian,
        $durasi
    );

    if (mysqli_stmt_execute($stmt_insert)) {
        echo "<script>alert('Sewa Berhasil! Silakan lakukan pembayaran.'); window.location='pembayaran.php';</script>";
    } else {
        echo "<script>alert('Error: Gagal membuat transaksi. ".mysqli_error($conn)."'); window.history.back();</script>";
    }
    mysqli_stmt_close($stmt_insert);
}
?>

<div class="container-fluid px-4 py-2">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-black mb-1" style="color: var(--primary);">Form Sewa Mobil</h1>
            <p class="text-muted" style="font-size: 0.9rem;">Lengkapi data pemesanan rental mobil Anda.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4 p-md-5">
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size: 0.9rem; color: var(--tertiary);">Pilih Mobil</label>
                    <select name="kode_mobil" id="kode_mobil" class="form-select form-select-lg" style="border-radius: 10px; font-size: 0.95rem;" required onchange="calculateTotal()">
                        <option value="" data-harga="0">-- Pilih Armada --</option>
                        <?php 
                        $m = mysqli_query($conn, "SELECT * FROM mobil WHERE is_deleted = 0");
                        while($row = mysqli_fetch_array($m)) {
                            echo "<option value='".$row['kode_mobil']."' data-harga='".$row['harga_sewa']."'>".$row['merk']." - Rp ".number_format($row['harga_sewa'], 0, ',', '.')." /hari</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: var(--tertiary);">Tanggal Sewa (Min. H+1)</label>
                        <input type="date" name="tanggal_sewa" id="tanggal_sewa" class="form-control form-control-lg" style="border-radius: 10px; font-size: 0.95rem;" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" onchange="calculateTotal()">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: var(--tertiary);">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control form-control-lg" style="border-radius: 10px; font-size: 0.95rem;" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" onchange="calculateTotal()">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: var(--tertiary);">Tujuan Perjalanan (Kota)</label>
                        <select name="tujuan_perjalanan" id="tujuan_perjalanan" class="form-select form-select-lg" style="border-radius: 10px; font-size: 0.95rem;" required onchange="calculateTotal()">
                            <option value="">-- Pilih Kota Tujuan --</option>
                            <optgroup label="Dalam Kota">
                                <option value="Semarang (Dalam Kota)">Semarang Raya (Dalam Kota)</option>
                                <option value="Kendal (Dalam Kota)">Kendal (Dalam Kota)</option>
                                <option value="Demak (Dalam Kota)">Demak (Dalam Kota)</option>
                                <option value="Ungaran (Dalam Kota)">Ungaran (Dalam Kota)</option>
                            </optgroup>
                            <optgroup label="Luar Kota (Pulau Jawa) (+ Rp 50.000/hari)">
                                <option value="Jakarta (Luar Kota)">Jakarta (Luar Kota)</option>
                                <option value="Bandung (Luar Kota)">Bandung (Luar Kota)</option>
                                <option value="Surabaya (Luar Kota)">Surabaya (Luar Kota)</option>
                                <option value="Yogyakarta (Luar Kota)">Yogyakarta (Luar Kota)</option>
                                <option value="Solo (Luar Kota)">Solo (Luar Kota)</option>
                                <option value="Malang (Luar Kota)">Malang (Luar Kota)</option>
                                <option value="Banyuwangi (Luar Kota)">Banyuwangi (Luar Kota)</option>
                                <option value="Cirebon (Luar Kota)">Cirebon (Luar Kota)</option>
                                <option value="Purwokerto (Luar Kota)">Purwokerto (Luar Kota)</option>
                                <option value="Tegal (Luar Kota)">Tegal (Luar Kota)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 0.9rem; color: var(--tertiary);">Layanan Supir</label>
                        <select name="pake_supir" id="pake_supir" class="form-select form-select-lg" style="border-radius: 10px; font-size: 0.95rem;" required onchange="calculateTotal()">
                            <option value="Tidak">Lepas Kunci (Tanpa Supir)</option>
                            <option value="Ya">Dengan Supir (+ Rp 150.000/hari)</option>
                        </select>
                    </div>
                </div>

                <!-- Ringkasan Biaya -->
                <div class="mb-4 p-4 rounded-3" style="background-color: #f8f9fa; border: 1px solid #e2e8f0;">
                    <h5 class="fw-bold mb-3" style="font-size: 1rem;">Ringkasan Biaya</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary" style="font-size: 0.9rem;">Durasi Sewa</span>
                        <span class="fw-bold" id="txt_durasi">0 Hari</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary" style="font-size: 0.9rem;">Biaya Mobil</span>
                        <span class="fw-bold" id="txt_biaya_mobil">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary" style="font-size: 0.9rem;">Biaya Luar Kota</span>
                        <span class="fw-bold" id="txt_biaya_luarkota">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-secondary" style="font-size: 0.9rem;">Biaya Supir</span>
                        <span class="fw-bold" id="txt_biaya_supir">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark" style="font-size: 1.1rem;">Total Estimasi Pembayaran</span>
                        <span class="fw-black text-danger" style="font-size: 1.2rem;" id="txt_total_semua">Rp 0</span>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" name="btn_sewa" id="btn_sewa" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm" style="border-radius: 12px; letter-spacing: 0.5px;" disabled>
                        <i class="bi bi-calendar-check me-2"></i> Konfirmasi Pesanan Sewa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const elMobil = document.getElementById('kode_mobil');
    const elTglSewa = document.getElementById('tanggal_sewa');
    const elTglKembali = document.getElementById('tanggal_kembali');
    const elTujuan = document.getElementById('tujuan_perjalanan');
    const elSupir = document.getElementById('pake_supir');
    const btnSewa = document.getElementById('btn_sewa');

    if (!elMobil.value || !elTglSewa.value || !elTglKembali.value || !elTujuan.value) {
        btnSewa.disabled = true;
        return;
    }

    const tgl1 = new Date(elTglSewa.value);
    const tgl2 = new Date(elTglKembali.value);

    // Validasi dasar
    if (tgl2 < tgl1) {
        alert('Tanggal kembali tidak boleh lebih awal dari tanggal sewa');
        elTglKembali.value = '';
        btnSewa.disabled = true;
        return;
    }

    let diffTime = Math.abs(tgl2 - tgl1);
    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    if (diffDays === 0) diffDays = 1;

    const hargaMobilPerHari = parseInt(elMobil.options[elMobil.selectedIndex].getAttribute('data-harga')) || 0;
    
    // Luar kota = 50.000 / hari
    const isLuarKota = elTujuan.value.includes('(Luar Kota)');
    const hargaLuarKotaPerHari = isLuarKota ? 50000 : 0;

    // Supir = 150.000 / hari
    const isPakeSupir = (elSupir.value === 'Ya');
    const hargaSupirPerHari = isPakeSupir ? 150000 : 0;

    const totalMobil = hargaMobilPerHari * diffDays;
    const totalLuarKota = hargaLuarKotaPerHari * diffDays;
    const totalSupir = hargaSupirPerHari * diffDays;
    const grandTotal = totalMobil + totalLuarKota + totalSupir;

    document.getElementById('txt_durasi').innerText = diffDays + ' Hari';
    document.getElementById('txt_biaya_mobil').innerText = 'Rp ' + totalMobil.toLocaleString('id-ID');
    document.getElementById('txt_biaya_luarkota').innerText = 'Rp ' + totalLuarKota.toLocaleString('id-ID');
    document.getElementById('txt_biaya_supir').innerText = 'Rp ' + totalSupir.toLocaleString('id-ID');
    document.getElementById('txt_total_semua').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

    btnSewa.disabled = false;
}
</script>

</div>
</div>
</body>
</html>