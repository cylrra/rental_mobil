<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==========================================
// KONEKSI DATABASE
// ==========================================
$host = 'localhost';
$db   = 'rental_mobil'; // Sudah disesuaikan dengan database phpMyAdmin Anda
$user = 'root';               
$pass = '';                   
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass, [
         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
         PDO::ATTR_EMULATE_PREPARES   => false,
     ]);
} catch (\PDOException $e) {
     die("Koneksi database gagal: " . $e->getMessage());
}

// Ambil data rekap laba rugi dari tabel asli Anda
// SUDAH DIPERBAIKI: Menggunakan nama tabel 'laporan_laba_rugi'
$sql = "SELECT periode, pendapatan_total, beban_total, laba_bersih FROM laporan_laba_rugi ORDER BY periode DESC";
$stmt = $pdo->query($sql);
$reports = $stmt->fetchAll();

// Hitung total akumulasi untuk ditaruh di kartu ringkasan atas (opsional)
$total_pendapatan_kumulatif = 0;
$total_beban_kumulatif = 0;
$total_laba_kumulatif = 0;

foreach ($reports as $row) {
    $total_pendapatan_kumulatif += $row['pendapatan_total'];
    $total_beban_kumulatif += $row['beban_total'];
    $total_laba_kumulatif += $row['laba_bersih'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - INDOMAX RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .sidebar-category-title {
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            color: #a4b0be !important; 
        }
        #sidebar-wrapper .list-group-item {
            transition: all 0.2s ease-in-out;
            font-size: 0.92rem;
        }
        #sidebar-wrapper .list-group-item:hover {
            background-color: #2c3034 !important;
            color: #0dcaf0 !important;
            padding-left: 1.25rem;
        }
        .fs-7 {
            font-size: 0.85rem;
        }
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    
    <div class="bg-dark text-white border-end border-secondary" id="sidebar-wrapper" style="min-width: 250px; max-width: 250px; min-height: 100vh; transition: all 0.3s;">
        <div class="sidebar-heading text-center py-4 fs-4 border-bottom border-secondary bg-danger">
            🚗 <strong>INDOMAX <span class="text-info">RENTAL</span></strong>
        </div>
        
        <div class="list-group list-group-flush px-2 py-3 gap-1">
            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-2 pb-1">Dashboard</div>
            <a href="index.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                <i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard Utama
            </a>
            
            <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Data Master</div>
            <a href="mobil.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                <i class="bi bi-car-front me-2 text-warning"></i> Mobil
            </a>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="supir.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-person-badge me-2 text-success"></i> Supir
                </a>
                
                <a href="pelanggan.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-people me-2 text-info"></i> Pelanggan
                </a>
                
                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Operasional</div>
                <a href="transaksi.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-card-list me-2 text-light"></i> Transaksi
                </a>

                <div class="sidebar-category-title text-uppercase fw-bold px-3 pt-3 pb-1">Akuntansi</div>
                
                <a href="pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-plus-circle-fill me-2 text-success"></i> Input Pembayaran
                </a>
                
                <a href="riwayat_pembayaran.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-clock-history me-2 text-warning"></i> Riwayat Pembayaran
                </a>
                
                <a href="jurnal_umum.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-journal-check me-2 text-info"></i> Jurnal Umum
                </a>
                
                <a href="jurnal_detail.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-search me-2 text-info"></i> Detail Jurnal
                </a>

                <a href="laporan_laba_rugi.php" class="list-group-item list-group-item-action bg-secondary text-white border-0 rounded-3 py-2 active">
                    <i class="bi bi-graph-up-arrow me-2 text-white"></i> Laporan Laba Rugi
                </a>
                
                <a href="cetak_kwitansi.php" class="list-group-item list-group-item-action bg-dark text-white border-0 rounded-3 py-2">
                    <i class="bi bi-printer-fill me-2 text-danger"></i> Cetak Kwitansi
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div id="page-content-wrapper" class="w-100 bg-light">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-3 border-bottom">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Sistem Informasi Pengelolaan Armada</span>
                    <span class="fw-bold text-secondary">PT INDOMAX RENTAL</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if (isset($_SESSION['role'])): ?>
                        <span class="badge bg-secondary p-2 text-white shadow-sm fs-7">
                            <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['nama_user'] ?? 'Pengguna'); ?> (<?= ucfirst($_SESSION['role'] ?? 'Tamu'); ?>)
                        </span>
                        <a class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </a>
                    <?php else: ?>
                        <a class="btn btn-sm btn-info text-dark rounded-pill px-3 fw-bold" href="login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-dark m-0"><i class="bi bi-calculator me-2 text-danger"></i>Laporan Laba Rugi</h3>
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Halaman
                </button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-warning text-dark p-3 rounded-4">
                        <small class="text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Total Pendapatan (Kumulatif)</small>
                        <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_pendapatan_kumulatif, 0, ',', '.'); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-danger text-white p-3 rounded-4">
                        <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Total Beban Operasional</small>
                        <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_beban_kumulatif, 0, ',', '.'); ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white p-3 rounded-4">
                        <small class="text-uppercase fw-bold text-white-50" style="font-size: 0.75rem;">Total Laba Bersih</small>
                        <h3 class="fw-bold m-0 mt-1">Rp <?= number_format($total_laba_kumulatif, 0, ',', '.'); ?></h3>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="table-light text-secondary text-uppercase" style="font-size: 0.8rem; border-bottom: 2px solid #dee2e6;">
                                <tr>
                                    <th class="py-3 px-4 text-center" style="width: 15%;">Periode Rekap</th>
                                    <th class="py-3 px-4 text-end">Total Pendapatan</th>
                                    <th class="py-3 px-4 text-end">Total Beban</th>
                                    <th class="py-3 px-4 text-end" style="width: 25%;">Laba Bersih</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 0.95rem;">
                                <?php if (empty($reports)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                            Belum ada data rekap keuangan yang terekam.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reports as $row): ?>
                                        <tr>
                                            <td class="px-4 text-center fw-bold text-secondary">
                                                <?= date('d M Y', strtotime($row['periode'])); ?>
                                            </td>
                                            <td class="px-4 text-end text-success fw-semibold">
                                                Rp <?= number_format($row['pendapatan_total'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="px-4 text-end text-danger fw-semibold">
                                                Rp <?= number_format($row['beban_total'], 2, ',', '.'); ?>
                                            </td>
                                            <td class="px-4 text-end">
                                                <?php if ($row['laba_bersih'] >= 0): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success p-2 rounded-3 w-100 text-end">
                                                        Rp <?= number_format($row['laba_bersih'], 2, ',', '.'); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger p-2 rounded-3 w-100 text-end">
                                                        Rp <?= number_format($row['laba_bersih'], 2, ',', '.'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> 
    </div> 
</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>