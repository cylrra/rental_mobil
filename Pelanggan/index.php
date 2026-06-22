<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}
include 'navbar.php'; 
include 'koneksi.php'; 

$id_pelanggan    = $_SESSION['id_pelanggan'] ?? 0;
$query_user      = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$user_data       = mysqli_fetch_assoc($query_user);
$status_verif    = $user_data['status_verifikasi'] ?? 'belum_verifikasi';
$nama_pelanggan  = htmlspecialchars($_SESSION['nama_pelanggan'] ?? 'Pelanggan');

// Stats
$total_mobil_tersedia = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM mobil"));
$total_transaksi      = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'"));
$aktif_sewa           = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan' AND status_sewa = 'berjalan'"));

// Greeting by time
$hour = (int)date('H');
if ($hour < 12) $greeting = "Selamat Pagi";
elseif ($hour < 15) $greeting = "Selamat Siang";
elseif ($hour < 18) $greeting = "Selamat Sore";
else $greeting = "Selamat Malam";
?>

<!-- ═══ WELCOME HEADER ═══ -->
<div class="row align-items-center mb-4">
    <div class="col-lg-7">
        <p class="mb-1" style="color: var(--clear-blue); font-weight: 600; font-size: 0.9rem;">
            <i class="bi bi-sun me-1"></i><?= $greeting ?>!
        </p>
        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--deep-navy); font-size: 1.9rem;">
            <?= $nama_pelanggan ?> 👋
        </h1>
        <p style="color: var(--lilac-dust); font-size: 0.9rem; margin: 0;">
            <?php if ($aktif_sewa > 0): ?>
                Anda memiliki <strong style="color: var(--clear-blue);"><?= $aktif_sewa ?> pesanan aktif</strong>. Cek statusnya di bawah!
            <?php else: ?>
                Siap untuk perjalanan berikutnya? Pilih armada impian Anda sekarang.
            <?php endif; ?>
        </p>
    </div>
    <div class="col-lg-5 mt-3 mt-lg-0">
        <!-- Status Verifikasi Card -->
        <div class="card p-3 d-flex flex-row align-items-center justify-content-between" 
             style="border: 1px solid rgba(135,184,229,0.3);">
            <div>
                <div style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--lilac-dust);">Status Verifikasi Akun</div>
                <?php if ($status_verif === 'terverifikasi'): ?>
                    <span class="badge rounded-pill mt-1 px-3 py-2" style="background: rgba(25,135,84,0.12); color: #0a6640; border: 1px solid rgba(25,135,84,0.25); font-size: 0.8rem;">
                        <i class="bi bi-patch-check-fill me-1"></i>Terverifikasi
                    </span>
                <?php elseif ($status_verif === 'dalam_proses'): ?>
                    <span class="badge rounded-pill mt-1 px-3 py-2" style="background: rgba(48,113,164,0.1); color: var(--clear-blue); border: 1px solid rgba(48,113,164,0.25); font-size: 0.8rem;">
                        <i class="bi bi-clock-history me-1"></i>Sedang Direview
                    </span>
                <?php else: ?>
                    <span class="badge rounded-pill mt-1 px-3 py-2" style="background: rgba(220,53,69,0.1); color: #dc3545; border: 1px solid rgba(220,53,69,0.25); font-size: 0.8rem;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Belum Verifikasi
                    </span>
                <?php endif; ?>
            </div>
            <?php if ($status_verif !== 'terverifikasi'): ?>
                <a href="edit_profil.php" class="btn btn-sm rounded-pill px-3 fw-bold" 
                   style="background: rgba(48,113,164,0.1); color: var(--clear-blue); border: 1.5px solid rgba(48,113,164,0.3); font-size: 0.8rem; white-space: nowrap;">
                    Verif Sekarang
                </a>
            <?php else: ?>
                <i class="bi bi-shield-fill-check" style="font-size: 2rem; color: rgba(25,135,84,0.2);"></i>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ═══ PROMO BANNER ═══ -->
<div class="mb-4">
    <div class="card overflow-hidden" style="background: linear-gradient(135deg, var(--deep-navy) 0%, var(--clear-blue) 100%); border: none; min-height: 160px;">
        <div class="card-body p-4 p-md-5 position-relative" style="color: white;">
            <!-- Decorative circles -->
            <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(135,184,229,0.06); border-radius: 50%;"></div>
            <div style="position: absolute; right: 8%; bottom: -50px; width: 150px; height: 150px; background: rgba(135,184,229,0.04); border-radius: 50%;"></div>
            <div style="position: absolute; left: 40%; top: 50%; transform: translateY(-50%); opacity: 0.04; font-size: 8rem;">🚗</div>
            <div class="row align-items-center position-relative">
                <div class="col-md-8">
                    <span class="badge rounded-pill fw-bold mb-3 px-3 py-2" 
                          style="background: var(--light-blue); color: var(--deep-navy); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        ✨ Promo Khusus Hari Ini
                    </span>
                    <h2 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif; font-size: 1.6rem;">
                        Diskon 15% Weekend Getaway
                    </h2>
                    <p class="mb-0" style="opacity: 0.8; font-size: 0.875rem; line-height: 1.6;">
                        Sewa SUV/MPV minimal 3 hari. Masukkan kode promo <strong>INDOMAXWEEKEND</strong> saat pembayaran.
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="katalog.php" class="btn fw-bold rounded-pill px-4 py-2 shadow-lg"
                       style="background: var(--frost-veil); color: var(--deep-navy); border: none; font-size: 0.9rem;">
                        Cari Mobil Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ STATS ROW ═══ -->
<div class="row g-3 mb-4">
    <!-- Armada Tersedia -->
    <div class="col-md-4">
        <div class="card h-100 p-4" style="border: 1px solid rgba(135,184,229,0.25);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--lilac-dust);">Total Armada</div>
                    <div class="fw-bold mt-1" style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: var(--deep-navy);">
                        <?= $total_mobil_tersedia ?> <span style="font-size: 1rem;">Mobil</span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--lilac-dust);">Siap disewa kapan saja</div>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 52px; height: 52px; background: rgba(48,113,164,0.1); border-radius: 14px;">
                    <i class="bi bi-car-front-fill" style="font-size: 1.4rem; color: var(--clear-blue);"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Transaksi -->
    <div class="col-md-4">
        <div class="card h-100 p-4" style="border: 1px solid rgba(135,184,229,0.25);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--lilac-dust);">Total Transaksi</div>
                    <div class="fw-bold mt-1" style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: var(--deep-navy);">
                        <?= $total_transaksi ?> <span style="font-size: 1rem;">Pesanan</span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--lilac-dust);">Riwayat penyewaan Anda</div>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 52px; height: 52px; background: rgba(135,184,229,0.15); border-radius: 14px;">
                    <i class="bi bi-file-earmark-text-fill" style="font-size: 1.4rem; color: var(--light-blue);"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Aktif Sewa -->
    <div class="col-md-4">
        <div class="card h-100 p-4" style="border: 1px solid <?= $aktif_sewa > 0 ? 'rgba(255,193,7,0.3)' : 'rgba(135,184,229,0.25)' ?>; background: <?= $aktif_sewa > 0 ? 'rgba(255,193,7,0.04)' : 'white' ?>;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--lilac-dust);">Pesanan Aktif</div>
                    <div class="fw-bold mt-1" style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: <?= $aktif_sewa > 0 ? '#856404' : 'var(--deep-navy)' ?>;">
                        <?= $aktif_sewa ?> <span style="font-size: 1rem;">Berjalan</span>
                    </div>
                    <div style="font-size: 0.78rem; color: var(--lilac-dust);">Sewa yang sedang aktif</div>
                </div>
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 52px; height: 52px; background: <?= $aktif_sewa > 0 ? 'rgba(255,193,7,0.15)' : 'rgba(184,170,180,0.1)' ?>; border-radius: 14px;">
                    <i class="bi bi-calendar-check-fill" style="font-size: 1.4rem; color: <?= $aktif_sewa > 0 ? '#856404' : 'var(--lilac-dust)' ?>;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ QUICK ACCESS MENU ═══ -->
<div class="mb-2 d-flex align-items-center justify-content-between">
    <h5 class="fw-bold m-0" style="font-family: 'Outfit', sans-serif; color: var(--deep-navy);">
        Menu Akses Cepat
    </h5>
</div>
<div class="row g-3 mb-4">
    <?php
    $menus = [
        ['href' => 'katalog.php',           'icon' => 'bi-car-front-fill',        'title' => 'Katalog Armada',    'sub' => 'Cari & booking mobil',        'color' => 'var(--clear-blue)'],
        ['href' => 'transaksi.php',         'icon' => 'bi-calendar2-check-fill',  'title' => 'Pesanan Saya',      'sub' => 'Status rental aktif',         'color' => 'var(--light-blue)'],
        ['href' => 'riwayat_pembayaran.php','icon' => 'bi-wallet2',               'title' => 'Keuangan',          'sub' => 'Riwayat & bayar nota',        'color' => 'var(--clear-blue)'],
        ['href' => 'grafik_rating.php',     'icon' => 'bi-star-fill',             'title' => 'Ulasan & Rating',   'sub' => 'Nilai pengalaman sewa',       'color' => '#856404'],
        ['href' => 'edit_profil.php',       'icon' => 'bi-person-gear',           'title' => 'Pengaturan Akun',   'sub' => 'Profil & verifikasi',         'color' => 'var(--lilac-dust)'],
        ['href' => 'bantuan.php',           'icon' => 'bi-headset',               'title' => 'Bantuan & CS',      'sub' => 'FAQ & live chat',             'color' => 'var(--clear-blue)'],
    ];
    foreach ($menus as $m):
    ?>
    <div class="col-6 col-md-4 col-lg-2">
        <a href="<?= $m['href'] ?>" class="text-decoration-none">
            <div class="card h-100 p-3 text-center action-card" style="border: 1px solid rgba(135,184,229,0.2);">
                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center" 
                     style="width: 48px; height: 48px; background: rgba(48,113,164,0.08); border-radius: 14px;">
                    <i class="bi <?= $m['icon'] ?>" style="font-size: 1.3rem; color: <?= $m['color'] ?>;"></i>
                </div>
                <div class="fw-semibold" style="font-size: 0.82rem; color: var(--deep-navy); line-height: 1.3;"><?= $m['title'] ?></div>
                <div style="font-size: 0.72rem; color: var(--lilac-dust); margin-top: 2px;"><?= $m['sub'] ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<!-- ═══ RECENT TRANSACTIONS ═══ -->
<?php
$sql_recent = "SELECT t.*, m.merk, m.jenis FROM transaksi_sewa t 
               JOIN mobil m ON t.kode_mobil = m.kode_mobil 
               WHERE t.id_pelanggan = '$id_pelanggan' 
               ORDER BY t.id_sewa DESC LIMIT 4";
$res_recent = mysqli_query($conn, $sql_recent);
if ($res_recent && mysqli_num_rows($res_recent) > 0):
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold m-0" style="font-family: 'Outfit', sans-serif; color: var(--deep-navy);">Transaksi Terakhir</h5>
    <a href="transaksi.php" style="font-size: 0.82rem; color: var(--clear-blue); font-weight: 600; text-decoration: none;">
        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>
<div class="card" style="border: 1px solid rgba(135,184,229,0.25);">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="ps-4">No. Order</th>
                    <th>Kendaraan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th class="text-center pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($res_recent)):
                    $st = $row['status_sewa'];
                    $bs = $st === 'berjalan'
                        ? "background:rgba(255,193,7,0.12);color:#856404;border:1px solid rgba(255,193,7,0.3);"
                        : "background:rgba(25,135,84,0.1);color:#0a6640;border:1px solid rgba(25,135,84,0.25);";
                    $bt = $st === 'berjalan' ? '🔄 Berjalan' : '✅ Selesai';
                ?>
                <tr>
                    <td class="ps-4"><span class="fw-bold" style="color:var(--clear-blue);font-size:.85rem;">#<?= $row['id_sewa'] ?></span></td>
                    <td>
                        <div class="fw-bold" style="color:var(--deep-navy);font-size:.9rem;"><?= $row['merk'] ?></div>
                        <div style="font-size:.75rem;color:var(--lilac-dust);"><?= $row['jenis'] ?></div>
                    </td>
                    <td style="font-size:.85rem;"><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></td>
                    <td class="fw-semibold" style="font-size:.85rem;color:var(--deep-navy);">Rp <?= number_format($row['total_biaya'],0,',','.') ?></td>
                    <td class="text-center pe-4">
                        <span class="badge rounded-pill px-3 py-2" style="<?= $bs ?> font-size:.75rem;"><?= $bt ?></span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<style>
    .action-card {
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(23,59,111,0.1) !important;
        border-color: rgba(48,113,164,0.3) !important;
    }
    .action-card:hover i {
        transform: scale(1.1);
        transition: transform 0.25s ease;
    }
</style>

<!-- Footer -->
</div> </div> </div> </body>
</html>