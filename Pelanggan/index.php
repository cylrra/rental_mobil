<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}
include 'navbar.php'; 
include 'koneksi.php'; 

date_default_timezone_set('Asia/Jakarta');
$hour = (int)date('H');
if ($hour >= 5 && $hour < 11)       $greeting = "Selamat Pagi";
elseif ($hour >= 11 && $hour < 15)  $greeting = "Selamat Siang";
elseif ($hour >= 15 && $hour < 19)  $greeting = "Selamat Sore";
else                                 $greeting = "Selamat Malam";

$id_pelanggan = $_SESSION['id_pelanggan'] ?? 0;
$query_user   = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$user_data    = mysqli_fetch_assoc($query_user);
$status_verif = $user_data['status_verifikasi'] ?? 'belum_verifikasi';

// Stats
$total_mobil      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM mobil WHERE is_deleted = 0"))['t'] ?? 0;
$total_transaksi  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'"))['t'] ?? 0;
$total_berjalan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan' AND status_sewa IN ('berjalan','diterima','pending')"))['t'] ?? 0;
$total_selesai    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan' AND status_sewa = 'selesai'"))['t'] ?? 0;

// Recent transactions with full detail
$res_recent = mysqli_query($conn, "SELECT t.id_sewa, t.status_sewa, t.tanggal_sewa, t.lama_sewa, t.total_bayar, t.jumlah_bayar,
                                          m.merk, m.jenis, m.Gambar
                                   FROM transaksi_sewa t
                                   JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                   WHERE t.id_pelanggan = '$id_pelanggan'
                                   ORDER BY t.id_sewa DESC LIMIT 5");
?>

<style>
/* ===== Dashboard-specific styles ===== */
.welcome-hero {
    background: linear-gradient(135deg, #0F172A 0%, #8B0000 60%, #3d0000 100%);
    border-radius: 20px;
    padding: 32px 36px;
    position: relative;
    overflow: hidden;
    color: #fff;
    margin-bottom: 28px;
}
.welcome-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: rgba(212,175,55,0.08);
    border-radius: 50%;
}
.welcome-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 30%;
    width: 200px; height: 200px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
}
.welcome-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: center;
    position: relative;
    z-index: 2;
}
.greeting-eyebrow {
    font-size: 0.72rem;
    font-weight: 700;
    color: #D4AF37;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 8px;
}
.greeting-name {
    font-size: 1.7rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 6px;
    line-height: 1.2;
}
.greeting-sub {
    font-size: 0.88rem;
    color: rgba(255,255,255,0.65);
    margin: 0;
}

/* Status badge inside hero */
.verif-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 0.78rem;
    font-weight: 700;
    white-space: nowrap;
}
.verif-badge.verified { background: rgba(22,163,74,0.2); color: #4ade80; border: 1px solid rgba(74,222,128,0.3); }
.verif-badge.pending  { background: rgba(217,119,6,0.2);  color: #fbbf24; border: 1px solid rgba(251,191,36,0.3); }
.verif-badge.unverif  { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(252,165,165,0.3); }

/* Stat cards grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card-p {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 16px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(15,23,42,0.05);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}
.stat-card-p::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 100%; height: 3px;
    opacity: 0;
    transition: opacity 0.3s;
}
.stat-card-p:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(15,23,42,0.1); color: inherit; }
.stat-card-p:hover::after { opacity: 1; }
.stat-card-p.crimson::after { background: linear-gradient(90deg, #8B0000, #c0392b); }
.stat-card-p.gold::after    { background: linear-gradient(90deg, #D4AF37, #f0d060); }
.stat-card-p.green::after   { background: linear-gradient(90deg, #16A34A, #4ade80); }
.stat-card-p.blue::after    { background: linear-gradient(90deg, #2563EB, #60a5fa); }

.stat-ico {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.stat-ico.crimson { background: rgba(139,0,0,0.08); color: #8B0000; }
.stat-ico.gold    { background: rgba(212,175,55,0.12); color: #b8971f; }
.stat-ico.green   { background: rgba(22,163,74,0.1); color: #16A34A; }
.stat-ico.blue    { background: rgba(37,99,235,0.1); color: #2563EB; }

.stat-num {
    font-size: 1.6rem;
    font-weight: 800;
    color: #0F172A;
    line-height: 1;
    margin-bottom: 2px;
}
.stat-lbl { font-size: 0.75rem; color: #94A3B8; font-weight: 600; }

/* Quick menu */
.quickmenu-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 28px;
}
.quick-item {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.quick-item:hover {
    border-color: #8B0000;
    background: rgba(139,0,0,0.03);
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(139,0,0,0.1);
    color: #8B0000;
}
.quick-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    background: #F4F6F9;
    transition: all 0.3s;
}
.quick-item:hover .quick-icon {
    background: rgba(139,0,0,0.1);
    color: #8B0000;
}
.quick-label { font-size: 0.78rem; font-weight: 700; color: #475569; }
.quick-item:hover .quick-label { color: #8B0000; }
.quick-sub { font-size: 0.68rem; color: #94A3B8; }

/* Recent transactions */
.recent-table-wrap {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15,23,42,0.04);
    margin-bottom: 28px;
}
.status-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px; border-radius: 50px;
    font-size: 0.72rem; font-weight: 700;
    white-space: nowrap;
}
.status-chip.berjalan  { background: rgba(22,163,74,0.1);   color: #16A34A; }
.status-chip.diterima  { background: rgba(37,99,235,0.1);   color: #2563EB; }
.status-chip.pending   { background: rgba(217,119,6,0.1);   color: #D97706; animation: pulse 2s infinite; }
.status-chip.selesai   { background: #F1F5F9; color: #64748B; }
.status-chip.dp        { background: rgba(139,0,0,0.08);    color: #8B0000; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }

/* Promo card */
.promo-cta {
    background: linear-gradient(135deg, #8B0000 0%, #3d0000 100%);
    border-radius: 18px;
    padding: 28px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
}
.promo-cta::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 160px; height: 160px;
    background: rgba(212,175,55,0.12);
    border-radius: 50%;
}
.promo-cta-badge {
    display: inline-block;
    background: #D4AF37;
    color: #1a1a1a;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 4px 14px;
    border-radius: 50px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 10px;
}
.promo-cta-title { font-size: 1.25rem; font-weight: 800; color: #fff; margin: 0 0 6px; }
.promo-cta-desc  { font-size: 0.85rem; color: rgba(255,255,255,0.7); max-width: 340px; margin: 0; }
.btn-promo-cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px;
    background: #fff;
    color: #8B0000;
    border-radius: 10px;
    font-weight: 800;
    font-size: 0.85rem;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.3s;
    flex-shrink: 0;
}
.btn-promo-cta:hover { background: #D4AF37; color: #1a1a1a; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }

@media (max-width: 992px) {
    .stats-grid, .quickmenu-grid { grid-template-columns: repeat(2, 1fr); }
    .welcome-grid { grid-template-columns: 1fr; }
}
@media (max-width: 576px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .quickmenu-grid { grid-template-columns: repeat(2, 1fr); }
    .welcome-hero { padding: 24px 20px; }
    .greeting-name { font-size: 1.35rem; }
}
</style>

<div class="container-fluid px-4 py-2">

    <!-- Welcome Hero -->
    <div class="welcome-hero">
        <div class="welcome-grid">
            <div>
                <div class="greeting-eyebrow">📅 <?= date('l, d F Y') ?></div>
                <h1 class="greeting-name"><?= $greeting ?>, <?= htmlspecialchars($user_data['nama'] ?? $_SESSION['nama_pelanggan'] ?? 'Pelanggan') ?>! 👋</h1>
                <p class="greeting-sub">Selamat datang kembali di portal INDOMAX RENTAL. Apa yang bisa kami bantu hari ini?</p>
            </div>
            <div style="position:relative;z-index:2;">
                <?php if ($status_verif === 'terverifikasi'): ?>
                    <span class="verif-badge verified"><i class="bi bi-patch-check-fill"></i> Akun Terverifikasi</span>
                <?php elseif ($status_verif === 'dalam_proses'): ?>
                    <span class="verif-badge pending"><i class="bi bi-clock-history"></i> Sedang Direview</span>
                <?php else: ?>
                    <a href="edit_profil.php" class="verif-badge unverif text-decoration-none">
                        <i class="bi bi-exclamation-triangle-fill"></i> Belum Verifikasi — Klik untuk Verif
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
        <a href="katalog.php" class="stat-card-p crimson">
            <div class="stat-ico crimson"><i class="bi bi-car-front-fill"></i></div>
            <div>
                <div class="stat-num"><?= $total_mobil ?></div>
                <div class="stat-lbl">Armada Tersedia</div>
            </div>
        </a>
        <a href="transaksi.php" class="stat-card-p gold">
            <div class="stat-ico gold"><i class="bi bi-calendar2-check-fill"></i></div>
            <div>
                <div class="stat-num"><?= $total_transaksi ?></div>
                <div class="stat-lbl">Total Pesanan Anda</div>
            </div>
        </a>
        <a href="transaksi.php" class="stat-card-p blue">
            <div class="stat-ico blue"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-num"><?= $total_berjalan ?></div>
                <div class="stat-lbl">Pesanan Aktif</div>
            </div>
        </a>
        <a href="riwayat_pembayaran.php" class="stat-card-p green">
            <div class="stat-ico green"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-num"><?= $total_selesai ?></div>
                <div class="stat-lbl">Perjalanan Selesai</div>
            </div>
        </a>
    </div>

    <!-- Quick Menu -->
    <h5 class="fw-bold mb-3" style="color:#0F172A;font-size:1rem;">⚡ Akses Cepat</h5>
    <div class="quickmenu-grid">
        <a href="katalog.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-search" style="color:#8B0000;"></i></div>
            <div class="quick-label">Katalog Armada</div>
            <div class="quick-sub">Cari & booking mobil</div>
        </a>
        <a href="transaksi.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-calendar-range-fill" style="color:#8B0000;"></i></div>
            <div class="quick-label">Pesanan Saya</div>
            <div class="quick-sub">Status rental aktif</div>
        </a>
        <a href="pembayaran.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-wallet2" style="color:#8B0000;"></i></div>
            <div class="quick-label">Pembayaran</div>
            <div class="quick-sub">Bayar tagihan</div>
        </a>
        <a href="riwayat_pembayaran.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-clock-history" style="color:#8B0000;"></i></div>
            <div class="quick-label">Riwayat</div>
            <div class="quick-sub">Histori pembayaran</div>
        </a>
        <a href="ulasan_rating.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-star-fill" style="color:#D4AF37;"></i></div>
            <div class="quick-label">Ulasan & Rating</div>
            <div class="quick-sub">Beri penilaian</div>
        </a>
        <a href="edit_profil.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-person-gear" style="color:#8B0000;"></i></div>
            <div class="quick-label">Profil Saya</div>
            <div class="quick-sub">Edit data akun</div>
        </a>
        <a href="bantuan.php" class="quick-item">
            <div class="quick-icon"><i class="bi bi-headset" style="color:#8B0000;"></i></div>
            <div class="quick-label">Bantuan & CS</div>
            <div class="quick-sub">Chat customer service</div>
        </a>
        <a href="katalog.php" class="quick-item" style="background:linear-gradient(135deg,#8B0000,#3d0000);border-color:transparent;">
            <div class="quick-icon" style="background:rgba(255,255,255,0.15);"><i class="bi bi-plus-circle-fill" style="color:#fff;"></i></div>
            <div class="quick-label" style="color:#fff;">Sewa Sekarang</div>
            <div class="quick-sub" style="color:rgba(255,255,255,0.6);">Pesan mobil baru</div>
        </a>
    </div>

    <!-- Promo Banner -->
    <div class="promo-cta">
        <div style="position:relative;z-index:2;">
            <div class="promo-cta-badge">⚡ Promo Aktif</div>
            <h2 class="promo-cta-title">Diskon 20% Weekend Getaway!</h2>
            <p class="promo-cta-desc">Sewa minimal 3 hari di akhir pekan. Masukkan kode <strong>INDOMAXWEEKEND</strong> saat konfirmasi.</p>
        </div>
        <a href="katalog.php" class="btn-promo-cta" style="position:relative;z-index:2;">
            <i class="bi bi-car-front-fill"></i> Pilih Armada
        </a>
    </div>

    <!-- Recent Transactions -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold m-0" style="color:#0F172A;font-size:1rem;">📋 Transaksi Terakhir</h5>
        <a href="transaksi.php" class="btn-outline-modern d-inline-flex align-items-center gap-2" style="font-size:0.8rem;padding:7px 16px;">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="recent-table-wrap">
        <?php if ($res_recent && mysqli_num_rows($res_recent) > 0): ?>
        <table class="table-modern w-100">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Armada</th>
                    <th>Tanggal Sewa</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="text-align:right;padding-right:20px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($res_recent)):
                $img_src = (!empty($row['Gambar']) && file_exists('img/'.$row['Gambar'])) ? 'img/'.$row['Gambar'] : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=80&q=60';
                $sisa = (int)$row['total_bayar'] - (int)$row['jumlah_bayar'];
            ?>
            <tr>
                <td>
                    <span style="font-weight:800;color:#0F172A;font-size:0.85rem;">#SRV-<?= $row['id_sewa'] ?></span>
                    <div style="font-size:0.7rem;color:#94A3B8;margin-top:2px;"><?= $row['lama_sewa'] ?> Hari</div>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?= $img_src ?>" style="width:42px;height:28px;object-fit:cover;border-radius:6px;border:1px solid #E8ECF2;" alt="">
                        <div>
                            <div style="font-weight:700;font-size:0.85rem;color:#0F172A;"><?= htmlspecialchars($row['merk']) ?></div>
                            <div style="font-size:0.7rem;color:#94A3B8;"><?= htmlspecialchars($row['jenis']) ?></div>
                        </div>
                    </div>
                </td>
                <td style="font-size:0.82rem;color:#475569;"><?= date('d M Y', strtotime($row['tanggal_sewa'])) ?></td>
                <td>
                    <div style="font-weight:700;font-size:0.85rem;color:#0F172A;">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></div>
                    <?php if ($sisa > 0): ?>
                    <div style="font-size:0.7rem;color:#D97706;">Sisa Rp <?= number_format($sisa, 0, ',', '.') ?></div>
                    <?php else: ?>
                    <div style="font-size:0.7rem;color:#16A34A;font-weight:700;">✓ Lunas</div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $s = $row['status_sewa'];
                    $chip_map = [
                        'berjalan' => ['berjalan', '<i class="bi bi-play-circle-fill"></i> Berjalan'],
                        'diterima' => ['diterima', '<i class="bi bi-check-circle"></i> Diterima'],
                        'pending'  => ['pending',  '<i class="bi bi-clock-fill"></i> Menunggu ACC'],
                        'selesai'  => ['selesai',  '<i class="bi bi-flag-fill"></i> Selesai'],
                        'DP'       => ['dp',        '<i class="bi bi-wallet2"></i> DP'],
                    ];
                    $chip = $chip_map[$s] ?? ['selesai', ucfirst($s)];
                    echo "<span class='status-chip {$chip[0]}'>{$chip[1]}</span>";
                    ?>
                </td>
                <td style="text-align:right;padding-right:16px;">
                    <?php if ($sisa > 0 && in_array($s, ['diterima','berjalan','pending','DP'])): ?>
                    <a href="pembayaran.php?id=<?= $row['id_sewa'] ?>" class="action-btn success" title="Bayar Sekarang">
                        <i class="bi bi-wallet2"></i>
                    </a>
                    <?php endif; ?>
                    <a href="riwayat_pembayaran.php" class="action-btn view ms-1" title="Lihat Detail">
                        <i class="bi bi-eye-fill"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <span class="empty-state-icon"><i class="bi bi-calendar-x"></i></span>
            <div class="empty-state-title">Belum Ada Transaksi</div>
            <div class="empty-state-desc">Yuk buat pesanan pertama Anda sekarang!</div>
            <a href="katalog.php" class="btn-primary-modern mt-3 d-inline-flex">
                <i class="bi bi-car-front-fill"></i> Lihat Armada
            </a>
        </div>
        <?php endif; ?>
    </div>

</div>