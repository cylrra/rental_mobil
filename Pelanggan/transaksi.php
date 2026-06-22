<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'koneksi.php';

if (!isset($_SESSION['nama'])) {
    $_SESSION['id_pelanggan'] = 1;
    $_SESSION['nama'] = "Atarada Saputra";
}

$id_pelanggan = $_SESSION['id_pelanggan'];
$nama_pelanggan = $_SESSION['nama'];

$kode_mobil = isset($_GET['kode']) ? trim(mysqli_real_escape_string($conn, $_GET['kode'])) : '';

$mobil_terpilih = null;
if (!empty($kode_mobil)) {
    $q_mobil = mysqli_query($conn, "SELECT * FROM mobil WHERE UPPER(kode_mobil) = UPPER('$kode_mobil')");
    $mobil_terpilih = mysqli_fetch_assoc($q_mobil);
}

$harga_harian = 0;
if ($mobil_terpilih) {
    if (isset($mobil_terpilih['harga'])) { $harga_harian = (int)$mobil_terpilih['harga']; }
    elseif (isset($mobil_terpilih['harga_per_hari'])) { $harga_harian = (int)$mobil_terpilih['harga_per_hari']; }
    elseif (isset($mobil_terpilih['tarif'])) { $harga_harian = (int)$mobil_terpilih['tarif']; }
    elseif (isset($mobil_terpilih['harga_mobil'])) { $harga_harian = (int)$mobil_terpilih['harga_mobil']; }
}

if ($harga_harian <= 0) { $harga_harian = 350000; }
$nama_mobil = $mobil_terpilih ? ($mobil_terpilih['merk'] . ' ' . $mobil_terpilih['jenis']) : 'Mobil Belum Dipilih';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Transaksi Baru - INDOMAX RENT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 260px; background-color: #0f172a; min-height: 100vh; color: #fff; position: fixed; }
        .sidebar .nav-link { color: #94a3b8; padding: 12px 20px; display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .sidebar .nav-link.active { background-color: #1e293b; color: #38bdf8; border-left: 4px solid #38bdf8; }
        .main-content { margin-left: 260px; padding: 30px; }
        .navbar-top { background: #fff; padding: 15px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: flex-end; align-items: center; gap: 15px; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .ringkasan-box { background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column p-3">
    <div class="py-3 px-2 mb-4 border-bottom border-secondary">
        <h4 class="fw-bold text-info mb-0"><i class="bi bi-car-front-fill"></i> INDOMAX RENT</h4>
        <small class="text-muted text-uppercase" style="font-size: 10px;">Sistem Armada Pelanggan</small>
    </div>
    <nav class="d-flex flex-column gap-1">
        <a href="katalog.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> Katalog Mobil</a>
        <a href="riwayat_pembayaran.php" class="nav-link active"><i class="bi bi-calendar-check-fill"></i> Sewa & Pesanan Saya</a>
    </nav>
</div>

<div class="main-content">
    <div class="navbar-top card-custom mb-4 px-4 py-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border p-2"><i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($nama_pelanggan); ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
        </div>
    </div>

    <div class="container-fluid px-0">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-pencil-square"></i> Formulir Sewa Armada</h5>
                    
                    <form action="proses_tambah_transaksi.php" method="POST">
                        <input type="hidden" name="kode_mobil" value="<?= htmlspecialchars($kode_mobil); ?>">
                        <input type="hidden" name="id_pelanggan" value="<?= htmlspecialchars($id_pelanggan); ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Nama Penyewa</label>
                            <input type="text" class="form-control bg-light fw-bold" value="<?= htmlspecialchars($nama_pelanggan); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Mobil Terpilih</label>
                            <input type="text" class="form-control bg-light fw-bold text-primary" value="<?= htmlspecialchars($nama_mobil); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Gunakan Jasa Supir?</label>
                            <select class="form-select" name="status_supir" id="status_supir" onchange="hitungTotal()">
                                <option value="Tidak">Tidak (Lepas Kunci)</option>
                                <option value="Ya">Ya (Menggunakan Supir +Rp 200.000/Hari)</option>
                            </select>
                            <div id="ket_supir" class="mt-2 text-danger small fst-italic" style="display: none;">
                                *Catatan: Harga belum termasuk biaya bensin dan makan supir.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted">Tanggal Mulai Sewa</label>
                            <input type="date" class="form-control" name="tgl_sewa" value="<?= date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-muted">Lama Sewa (Hari)</label>
                            <input type="number" class="form-control" name="lama_sewa" id="lama_sewa" min="1" value="1" oninput="hitungTotal()" required>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="katalog.php" class="btn btn-secondary px-4">Kembali</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold flex-grow-1">Simpan Pesanan Sewa</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-secondary mb-3">Ringkasan Biaya</h5>
                    <hr class="mt-0">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Biaya Sewa Mobil (<span id="view_hari">1</span> Hari) x</span>
                        <span class="fw-semibold text-dark">Rp <?= number_format($harga_harian, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Tambahan Jasa Supir</span>
                        <span class="fw-semibold text-dark" id="view_sopir">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mb-3">
                        <span class="text-muted small fw-bold">Total Tagihan</span>
                        <span class="text-dark fw-bold" id="view_total_tagihan">Rp <?= number_format($harga_harian, 0, ',', '.'); ?></span>
                    </div>
                    <div class="ringkasan-box p-3 text-center">
                        <div class="text-uppercase tracking-wider text-muted fw-bold mb-1" style="font-size: 11px;">Total Biaya Sewa</div>
                        <h3 class="text-primary fw-bold mb-0" id="grand_total_box">Rp <?= number_format($harga_harian, 0, ',', '.'); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const hargaMobilPerHari = <?= $harga_harian; ?>;

function hitungTotal() {
    let durasiHari = parseInt(document.getElementById('lama_sewa').value) || 1;
    if(durasiHari < 1) durasiHari = 1;
    
    let statusSupir = document.getElementById('status_supir').value;
    let ketSupir = document.getElementById('ket_supir');
    
    // Tampilkan keterangan jika pilih Ya
    if (statusSupir === 'Ya') {
        ketSupir.style.display = 'block';
    } else {
        ketSupir.style.display = 'none';
    }

    let tarifSupirPerHari = (statusSupir === 'Ya') ? 200000 : 0;
    let totalSewaMobil = hargaMobilPerHari * durasiHari;
    let totalSewaSupir = tarifSupirPerHari * durasiHari;
    let grandTotal = totalSewaMobil + totalSewaSupir;

    document.getElementById('view_hari').innerText = durasiHari;
    document.getElementById('view_sopir').innerText = "Rp " + totalSewaSupir.toLocaleString('id-ID');
    document.getElementById('view_total_tagihan').innerText = "Rp " + grandTotal.toLocaleString('id-ID');
    document.getElementById('grand_total_box').innerText = "Rp " + grandTotal.toLocaleString('id-ID');
}
</script>

</body>
</html>