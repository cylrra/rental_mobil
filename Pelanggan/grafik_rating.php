<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'koneksi.php';
include 'navbar.php';

// 1. Ambil nilai rata-rata tiap indikator dari database
$query_avg = mysqli_query($conn, "SELECT AVG(rating_pelayanan) as avg_pely, AVG(rating_supir) as avg_supr, AVG(rating_mobil) as avg_mobl, COUNT(*) as total_ulasan FROM rating_sewa");
$row_avg = mysqli_fetch_assoc($query_avg);

$pely = round($row_avg['avg_pely'], 1) ?? 0;
$supr = round($row_avg['avg_supr'], 1) ?? 0;
$mobl = round($row_avg['avg_mobl'], 1) ?? 0;
$total_reviews = intval($row_avg['total_ulasan']);

// Hitung total rata-rata global
$overall_avg = 0;
if ($total_reviews > 0) {
    $overall_avg = round(($pely + $supr + $mobl) / 3, 1);
}

// 2. Hitung jumlah distribusi bintang (1-5) seperti di Google Play Store
$distribusi = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$sql_dist = "SELECT ROUND((rating_pelayanan + rating_supir + rating_mobil) / 3) as bintang_rata, COUNT(*) as jumlah 
             FROM rating_sewa 
             GROUP BY bintang_rata";
$res_dist = mysqli_query($conn, $sql_dist);
while ($r = mysqli_fetch_assoc($res_dist)) {
    $star_val = intval($r['bintang_rata']);
    if (isset($distribusi[$star_val])) {
        $distribusi[$star_val] = intval($r['jumlah']);
    }
}
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 my-2">
            <h1 class="fw-bold" style="font-family: 'Outfit', sans-serif; color: #0f172a;">Ulasan & Rating Layanan</h1>
            <p class="text-muted">Kritik dan saran Anda adalah energi bagi kami untuk terus memberikan pelayanan terbaik.</p>
        </div>
    </div>

    <!-- Play Store Style Rating Summary Card -->
    <div class="row mb-5">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm border-0 rounded-4 bg-white p-4">
                <div class="row align-items-center">
                    
                    <!-- Left: Big Overall Score -->
                    <div class="col-sm-4 text-center border-end-sm mb-4 mb-sm-0" style="border-color: #e2e8f0;">
                        <h1 class="fw-extrabold text-dark display-3 m-0" style="font-family: 'Outfit', sans-serif;"><?= $overall_avg; ?></h1>
                        <div class="text-warning my-2">
                            <?php 
                            $floor_stars = floor($overall_avg);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $floor_stars) {
                                    echo '<i class="bi bi-star-fill fs-5"></i>';
                                } else {
                                    echo '<i class="bi bi-star fs-5"></i>';
                                }
                            }
                            ?>
                        </div>
                        <small class="text-muted d-block"><?= number_format($total_reviews, 0, ',', '.'); ?> Ulasan Pelanggan</small>
                    </div>

                    <!-- Right: Distribution Progress Bars (Play Store Style) -->
                    <div class="col-sm-8 px-lg-4">
                        <div class="d-grid gap-2">
                            <?php 
                            for ($star = 5; $star >= 1; $star--): 
                                $qty = $distribusi[$star];
                                $pct = $total_reviews > 0 ? ($qty / $total_reviews) * 100 : 0;
                            ?>
                            <div class="d-flex align-items-center">
                                <span class="small fw-semibold text-dark me-2" style="width: 15px;"><?= $star; ?></span>
                                <i class="bi bi-star-fill text-warning me-3" style="font-size: 0.85rem;"></i>
                                <div class="progress w-100 rounded-pill bg-light" style="height: 10px;">
                                    <div class="progress-bar bg-dark rounded-pill" role="progressbar" style="width: <?= $pct; ?>%;" aria-valuenow="<?= $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="small text-muted ms-3" style="width: 30px; text-align: right;"><?= $qty; ?></span>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                </div>

                <!-- Sub-Ratings Details -->
                <div class="row mt-4 pt-4 border-top text-center" style="border-color: #f1f5f9;">
                    <div class="col-4">
                        <span class="text-muted small d-block">Pelayanan Kantor</span>
                        <strong class="fs-5 text-dark"><?= $pely; ?> / 5.0</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Keramahan Sopir</span>
                        <strong class="fs-5 text-dark"><?= $supr; ?> / 5.0</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Kondisi Armada</span>
                        <strong class="fs-5 text-dark"><?= $mobl; ?> / 5.0</strong>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Review Comments List (Riwayat Ulasan) -->
    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #0f172a;">Review Pelanggan Terbaru</h5>
    <div class="row mb-5">
        <div class="col-lg-8 col-md-10">
            <div class="d-grid gap-3">
                <?php 
                $query_feed = mysqli_query($conn, "SELECT r.*, p.nama FROM rating_sewa r JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan ORDER BY r.tgl_rating DESC LIMIT 10");
                if (mysqli_num_rows($query_feed) > 0) {
                    while ($f = mysqli_fetch_assoc($query_feed)) {
                        $f_overall = round(($f['rating_pelayanan'] + $f['rating_supir'] + $f['rating_mobil']) / 3, 1);
                ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($f['nama']); ?></h6>
                            <small class="text-muted" style="font-size: 0.75rem;"><?= date('d F Y, H:i', strtotime($f['tgl_rating'])); ?></small>
                        </div>
                        <!-- Stars badge for this specific review -->
                        <div class="badge bg-light text-dark border rounded-pill px-3 py-1-5">
                            <span class="fw-bold me-1 text-primary"><?= $f_overall; ?></span>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                    </div>
                    
                    <p class="text-dark small mb-3"><?= nl2br(htmlspecialchars($f['ulasan'])); ?></p>
                    
                    <!-- Stars sub-score breakdown in small fonts -->
                    <div class="d-flex flex-wrap gap-3 text-muted small" style="font-size: 0.75rem;">
                        <span>Pelayanan: <strong><?= $f['rating_pelayanan']; ?> <i class="bi bi-star-fill text-warning"></i></strong></span>
                        <span>Sopir: <strong><?= $f['rating_supir']; ?> <i class="bi bi-star-fill text-warning"></i></strong></span>
                        <span>Kondisi Mobil: <strong><?= $f['rating_mobil']; ?> <i class="bi bi-star-fill text-warning"></i></strong></span>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo '<div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white text-muted">
                            <i class="bi bi-chat-left-text fs-1 mb-2"></i> Belum ada ulasan masuk dari pelanggan.
                          </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
    @media (min-width: 576px) {
        .border-end-sm {
            border-right: 1px solid var(--grey-hint) !important;
        }
    }
    .py-1-5 { padding-top: 0.4rem; padding-bottom: 0.4rem; }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>