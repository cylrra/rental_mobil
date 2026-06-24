<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Logic Greeting (Waktu Lokal Semarang)
date_default_timezone_set('Asia/Jakarta');
$hour = (int)date('H');
if ($hour >= 5 && $hour < 11) $greeting = "Selamat Pagi";
elseif ($hour >= 11 && $hour < 15) $greeting = "Selamat Siang";
elseif ($hour >= 15 && $hour < 19) $greeting = "Selamat Sore";
else $greeting = "Selamat Malam";

$id_pelanggan = $_SESSION['id_pelanggan'] ?? 0;
$query_user = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$user_data = mysqli_fetch_assoc($query_user);
$status_verif = $user_data['status_verifikasi'] ?? 'belum_verifikasi';

$total_mobil = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM mobil WHERE status_mobil = 'tersedia'"));
$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'"));
?>

<!-- Import Font Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body, h1, h2, h3, h4, h5, h6, p, span, div, td, th, button, a { font-family: 'Inter', sans-serif !important; }
    .action-card { transition: all 0.3s ease; border: 1px solid transparent !important; }
    .action-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08) !important; border-color: rgba(30, 58, 138, 0.1) !important; }
    .py-1-5 { padding-top: 0.4rem; padding-bottom: 0.4rem; }
    .py-2-5 { padding-top: 0.6rem; padding-bottom: 0.6rem; }
</style>

<div class="container-fluid px-4">
    <!-- Header & Status Verifikasi -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 class="fw-bold" style="color: #0f172a; margin-top: 15px;">Dashboard Pelanggan</h1>
            <p class="text-muted"><?= $greeting ?>, <strong class="text-primary"><?= htmlspecialchars($_SESSION['nama_pelanggan'] ?? 'Pelanggan'); ?></strong>! Siap untuk berkendara hari ini?</p>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Status Verifikasi Akun</small>
                        <?php if ($status_verif === 'terverifikasi'): ?>
                            <span class="badge bg-success rounded-pill px-3 py-1-5 mt-1"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi</span>
                        <?php elseif ($status_verif === 'dalam_proses'): ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1-5 mt-1"><i class="bi bi-clock-history me-1"></i> Sedang Direview</span>
                        <?php else: ?>
                            <span class="badge bg-danger rounded-pill px-3 py-1-5 mt-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Verifikasi</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($status_verif === 'belum_verifikasi'): ?>
                        <a href="edit_profil.php" class="btn btn-sm btn-outline-danger rounded-pill fw-bold">Verif Sekarang</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); color: white;">
                <div class="card-body p-5 position-relative">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-1-5 rounded-pill text-uppercase">PROMO KHUSUS HARI INI</span>
                            <h2 class="fw-bold mb-2">Diskon 15% Weekend Getaway</h2>
                            <p class="mb-0 opacity-75">Sewa kendaraan kategori SUV/MPV minimal 3 hari dan masukkan kode promo <strong>INDOMAXWEEKEND</strong> saat konfirmasi pembayaran.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-4 mt-md-0">
                            <a href="katalog.php" class="btn btn-light text-dark fw-bold rounded-pill px-4 py-2-5 shadow-lg">Cari Mobil Sekarang <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">Armada Tersedia</h6>
                        <h2 class="fw-bold m-0 text-primary"><?= $total_mobil; ?> Mobil</h2>
                        <small class="text-muted">Semua unit siap pakai</small>
                    </div>
                    <div class="bg-light p-3 rounded-4"><i class="bi bi-car-front-fill fs-2 text-primary"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">Total Transaksi Anda</h6>
                        <h2 class="fw-bold m-0 text-success"><?= $total_transaksi; ?> Riwayat</h2>
                        <small class="text-muted">Pesanan yang pernah dibuat</small>
                    </div>
                    <div class="bg-light p-3 rounded-4"><i class="bi bi-file-earmark-text-fill fs-2 text-success"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Akses Cepat -->
    <h5 class="fw-bold mb-3">Menu Akses Cepat</h5>
    <div class="row mb-5 g-3">
        <div class="col-6 col-md-3"><a href="katalog.php" class="text-decoration-none"><div class="card border-0 shadow-sm rounded-4 p-4 text-center action-card h-100 bg-white"><i class="bi bi-search fs-1 text-primary mb-2"></i><h6 class="fw-semibold text-dark m-0">Katalog Armada</h6><small class="text-muted">Cari & booking mobil</small></div></a></div>
        <div class="col-6 col-md-3"><a href="transaksi.php" class="text-decoration-none"><div class="card border-0 shadow-sm rounded-4 p-4 text-center action-card h-100 bg-white"><i class="bi bi-calendar2-range fs-1 text-primary mb-2"></i><h6 class="fw-semibold text-dark m-0">Pesanan Saya</h6><small class="text-muted">Status rental aktif</small></div></a></div>
        <div class="col-6 col-md-3"><a href="riwayat_pembayaran.php" class="text-decoration-none"><div class="card border-0 shadow-sm rounded-4 p-4 text-center action-card h-100 bg-white"><i class="bi bi-wallet-fill fs-1 text-primary mb-2"></i><h6 class="fw-semibold text-dark m-0">Keuangan</h6><small class="text-muted">Riwayat & Bayar nota</small></div></a></div>
        <div class="col-6 col-md-3"><a href="bantuan.php" class="text-decoration-none"><div class="card border-0 shadow-sm rounded-4 p-4 text-center action-card h-100 bg-white"><i class="bi bi-headset fs-1 text-primary mb-2"></i><h6 class="fw-semibold text-dark m-0">Pusat Bantuan</h6><small class="text-muted">Tanya Jawab & Chat</small></div></a></div>
    </div>

    <!-- Transaksi Terakhir -->
    <h5 class="fw-bold mb-3">Transaksi Terakhir</h5>
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light"><tr><th class="px-4 py-3">Order</th><th>Mobil</th><th>Status</th></tr></thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT t.id_sewa, m.merk, m.jenis, t.status_sewa FROM transaksi_sewa t JOIN mobil m ON t.kode_mobil = m.kode_mobil WHERE t.id_pelanggan = '$id_pelanggan' ORDER BY t.id_sewa DESC LIMIT 5");
                    while($row = mysqli_fetch_assoc($res)) {
                        $nama_mobil = $row['merk'] . " " . $row['jenis'];
                        $status_tampil = ($row['status_sewa'] == 'selesai') ? "Selesai" : "Menunggu Konfirmasi";
                        $badge = ($row['status_sewa'] == 'selesai') ? "bg-success" : "bg-secondary";
                        echo "<tr>
                            <td class='px-4 py-3 fw-bold'>#{$row['id_sewa']}</td>
                            <td>{$nama_mobil}</td>
                            <td><span class='badge {$badge}'>{$status_tampil}</span></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>