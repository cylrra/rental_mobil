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

$id_pelanggan = $_SESSION['id_pelanggan'];

// 1. PROSES INPUT RATING (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rating'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $pely = intval($_POST['pely']);
    $supr = intval($_POST['supr']);
    $mobl = intval($_POST['mobl']);
    $ulasan = mysqli_real_escape_string($conn, $_POST['ulasan']);

    // Validasi dasar
    if (empty($id_transaksi) || empty($pely) || empty($mobl) || empty($ulasan)) {
        echo "<script>alert('Harap isi semua bidang dan berikan bintang penilaian!'); window.history.back();</script>";
        exit;
    }

    // Insert ke rating_sewa
    $query = "INSERT INTO rating_sewa (id_transaksi, id_pelanggan, rating_pelayanan, rating_supir, rating_mobil, ulasan) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiiiis", $id_transaksi, $id_pelanggan, $pely, $supr, $mobl, $ulasan);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Ulasan berhasil dikirim!'); window.location='ulasan_rating.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error: " . mysqli_escape_string($conn, mysqli_error($conn)) . "'); window.history.back();</script>";
        exit;
    }
}

// 2. DETEKSI TRANSAKSI YANG BELUM DINILAI
$unrated_list = [];
$stmt_unrated = mysqli_prepare($conn, "
    SELECT t.id_sewa, m.merk, m.nopol, t.pake_supir 
    FROM transaksi_sewa t
    LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil
    WHERE t.id_pelanggan = ? 
      AND t.status_sewa = 'selesai' 
      AND t.id_sewa NOT IN (SELECT id_transaksi FROM rating_sewa)
    ORDER BY t.id_sewa DESC
");
mysqli_stmt_bind_param($stmt_unrated, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_unrated);
$query_unrated = mysqli_stmt_get_result($stmt_unrated);

while ($r = mysqli_fetch_assoc($query_unrated)) {
    $unrated_list[] = $r;
}

// Pilih transaksi aktif untuk dinilai
$transaction_to_rate = null;
if (isset($_GET['id_sewa'])) {
    $req_id_sewa = intval($_GET['id_sewa']);
    foreach ($unrated_list as $t) {
        if ($t['id_sewa'] == $req_id_sewa) {
            $transaction_to_rate = $t;
            break;
        }
    }
} elseif (!empty($unrated_list)) {
    // Default transaksi pertama yang butuh rating
    $transaction_to_rate = $unrated_list[0];
}

// 3. HITUNG STATISTIK UTAMA (Grafik Penilaian)
$query_avg = mysqli_query($conn, "
    SELECT 
        AVG(rating_pelayanan) as avg_pely, 
        AVG(rating_supir) as avg_supr, 
        AVG(rating_mobil) as avg_mobl, 
        COUNT(*) as total_ulasan 
    FROM rating_sewa
");
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

// Hitung jumlah distribusi bintang (1-5) seperti di Google Play Store
$distribusi = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$sql_dist = "
    SELECT ROUND((rating_pelayanan + rating_supir + rating_mobil) / 3) as bintang_rata, COUNT(*) as jumlah 
    FROM rating_sewa 
    GROUP BY bintang_rata
";
$res_dist = mysqli_query($conn, $sql_dist);
while ($r = mysqli_fetch_assoc($res_dist)) {
    $star_val = intval($r['bintang_rata']);
    if (isset($distribusi[$star_val])) {
        $distribusi[$star_val] = intval($r['jumlah']);
    }
}

include 'navbar.php';
?>

<div class="container-fluid px-4">
    <!-- Banner -->
    <div class="pay-header-banner">
        <div style="position:relative;z-index:2;">
            <div style="font-size:0.7rem;font-weight:800;color:#D4AF37;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;"><i class="bi bi-star-fill me-2"></i>Ulasan & Rating</div>
            <h1 style="font-size:1.5rem;font-weight:800;margin:0 0 6px;color:#fff;">Ulasan & Rating Layanan</h1>
            <p style="font-size:0.85rem;color:rgba(255,255,255,0.65);margin:0;">Kritik dan saran Anda adalah energi bagi kami untuk terus memberikan pelayanan terbaik.</p>
        </div>
    </div>

    <!-- ═══ SECTION BERI RATING (FORM INPUT) ═══ -->
    <?php if ($transaction_to_rate): ?>
    <div class="row mb-4" id="ratingFormSection">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow border-0 rounded-4 bg-white overflow-hidden animate-fade-in">
                <!-- Banner header form -->
                <div class="p-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #173b6f 0%, #3071a4 100%);">
                    <div>
                        <span class="badge bg-warning text-dark mb-1 fw-bold">Belum Dinilai</span>
                        <h4 class="fw-bold mb-0">Beri Rating Penilaian</h4>
                        <small class="opacity-75">Sewa Mobil: <b><?= htmlspecialchars($transaction_to_rate['merk']) ?></b> (<?= htmlspecialchars($transaction_to_rate['nopol']) ?>) — Order #<?= $transaction_to_rate['id_sewa'] ?></small>
                    </div>
                    <button class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold" id="btnIsiNanti">
                        <i class="bi bi-eye-slash me-1"></i> Isi Nanti
                    </button>
                </div>
                
                <div class="card-body p-4" id="ratingFormBody">
                    <form action="ulasan_rating.php" method="POST">
                        <input type="hidden" name="id_transaksi" value="<?= $transaction_to_rate['id_sewa'] ?>">
                        
                        <!-- 1. Pelayanan Kantor -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1">Pelayanan Kantor</label>
                            <p class="text-muted small mb-2">Nilai keramahan staf administrasi dan kemudahan proses administrasi.</p>
                            <div class="star-rating d-flex gap-2 align-items-center" data-category="pely">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star star-icon fs-2 text-secondary cursor-pointer" data-value="<?= $i ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-3 fw-bold text-muted rating-label" id="pely-label">Pilih Rating</span>
                                <input type="hidden" name="pely" id="pely-input" value="" required>
                            </div>
                        </div>

                        <!-- 2. Rating Sopir (Hanya jika pakai supir) -->
                        <?php if ($transaction_to_rate['pake_supir'] == 'Ya'): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1">Keramahan & Performa Sopir</label>
                            <p class="text-muted small mb-2">Nilai ketepatan waktu, keramahan, dan cara berkendara sopir.</p>
                            <div class="star-rating d-flex gap-2 align-items-center" data-category="supr">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star star-icon fs-2 text-secondary cursor-pointer" data-value="<?= $i ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-3 fw-bold text-muted rating-label" id="supr-label">Pilih Rating</span>
                                <input type="hidden" name="supr" id="supr-input" value="" required>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Tanpa sopir (lepas kunci), beri nilai default 5 -->
                        <input type="hidden" name="supr" value="5">
                        <?php endif; ?>

                        <!-- 3. Kondisi Armada -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1">Kondisi Mobil / Armada</label>
                            <p class="text-muted small mb-2">Nilai kebersihan, kenyamanan, dan performa mesin armada.</p>
                            <div class="star-rating d-flex gap-2 align-items-center" data-category="mobl">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star star-icon fs-2 text-secondary cursor-pointer" data-value="<?= $i ?>"></i>
                                <?php endfor; ?>
                                <span class="ms-3 fw-bold text-muted rating-label" id="mobl-label">Pilih Rating</span>
                                <input type="hidden" name="mobl" id="mobl-input" value="" required>
                            </div>
                        </div>

                        <!-- 4. Ulasan Komentar -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-1">Tulis Ulasan & Komentar Anda</label>
                            <p class="text-muted small mb-2">Ceritakan pengalaman berkendara Anda bersama armada kami.</p>
                            <textarea name="ulasan" class="form-control rounded-3 p-3" rows="3" placeholder="Ulasan Anda sangat berarti bagi peningkatan mutu layanan kami..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="submit" name="submit_rating" class="btn btn-primary px-4 py-2-5 rounded-pill fw-bold">
                                <i class="bi bi-send-fill me-2"></i>Kirim Penilaian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert jika form disembunyikan/Isi Nanti -->
    <div class="row mb-4 d-none" id="ratingSkippedAlert">
        <div class="col-lg-8 col-md-10">
            <div class="alert alert-light border shadow-sm rounded-4 d-flex justify-content-between align-items-center p-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle-fill text-primary fs-3"></i>
                    <div>
                        <strong class="text-dark">Penilaian Dilewati</strong>
                        <p class="mb-0 text-muted small">Anda dapat menilai order #<?= $transaction_to_rate['id_sewa'] ?> kapan saja dengan menekan tombol tampilkan.</p>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" id="btnTampilkanForm">
                    <i class="bi bi-star-fill me-1" style="font-size: 0.8rem;"></i> Beri Rating Sekarang
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ═══ SECTION GRAFIK PENILAIAN (PLAY STORE STYLE) ═══ -->
    <div class="row mb-5">
        <div class="col-lg-8 col-md-10">
            <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #0f172a;"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Grafik Penilaian</h5>
            <div class="card shadow-sm border-0 rounded-4 bg-white p-4">
                <div class="row align-items-center">
                    
                    <!-- Left: Big Overall Score -->
                    <div class="col-sm-4 text-center border-end-sm mb-4 mb-sm-0" style="border-color: #e2e8f0;">
                        <h1 class="fw-extrabold text-dark display-3 m-0" style="font-family: 'Outfit', sans-serif; font-weight: 800;"><?= $overall_avg; ?></h1>
                        <div class="text-warning my-2">
                            <?php 
                            $floor_stars = floor($overall_avg);
                            $remainder = $overall_avg - $floor_stars;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $floor_stars) {
                                    echo '<i class="bi bi-star-fill fs-5 me-1"></i>';
                                } elseif ($i == $floor_stars + 1 && $remainder >= 0.3) {
                                    echo '<i class="bi bi-star-half fs-5 me-1"></i>';
                                } else {
                                    echo '<i class="bi bi-star fs-5 me-1"></i>';
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
                        <strong class="fs-5 text-dark"><?= $pely; ?> <i class="bi bi-star-fill text-warning" style="font-size: 0.95rem;"></i></strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Keramahan Sopir</span>
                        <strong class="fs-5 text-dark"><?= $supr; ?> <i class="bi bi-star-fill text-warning" style="font-size: 0.95rem;"></i></strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Kondisi Armada</span>
                        <strong class="fs-5 text-dark"><?= $mobl; ?> <i class="bi bi-star-fill text-warning" style="font-size: 0.95rem;"></i></strong>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ═══ SECTION RIWAYAT ULASAN PELANGGAN ═══ -->
    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: #0f172a;"><i class="bi bi-chat-left-text me-2 text-primary"></i>Review Pelanggan Terbaru</h5>
    <div class="row mb-5">
        <div class="col-lg-8 col-md-10">
            <div class="d-grid gap-3">
                <?php 
                $query_feed = mysqli_query($conn, "
                    SELECT r.*, p.nama as nama 
                    FROM rating_sewa r 
                    JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                    ORDER BY r.tgl_rating DESC 
                    LIMIT 10
                ");
                if (mysqli_num_rows($query_feed) > 0) {
                    while ($f = mysqli_fetch_assoc($query_feed)) {
                        $f_overall = round(($f['rating_pelayanan'] + $f['rating_supir'] + $f['rating_mobil']) / 3, 1);
                ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white card-review">
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
            border-right: 1px solid #e2e8f0 !important;
        }
    }
    .py-1-5 { padding-top: 0.4rem; padding-bottom: 0.4rem; }
    .py-2-5 { padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .cursor-pointer { cursor: pointer; }
    .star-icon {
        transition: transform 0.15s ease-in-out, color 0.15s ease-in-out;
    }
    .star-icon:hover {
        transform: scale(1.2);
    }
    .card-review {
        transition: transform 0.2s;
    }
    .card-review:hover {
        transform: translateY(-2px);
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Logika Pengisian Rating Bintang Interaktif
    const starRatings = document.querySelectorAll('.star-rating');
    const labelTexts = {
        1: 'Sangat Buruk',
        2: 'Buruk',
        3: 'Cukup',
        4: 'Baik',
        5: 'Sangat Baik'
    };

    starRatings.forEach(container => {
        const stars = container.querySelectorAll('.star-icon');
        const hiddenInput = container.querySelector('input[type="hidden"]');
        const category = container.getAttribute('data-category');
        const label = document.getElementById(`${category}-label`);

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.getAttribute('data-value'));
                hiddenInput.value = value;
                
                // Update text label deskripsi
                if (label) {
                    label.innerText = labelTexts[value];
                    label.classList.remove('text-muted');
                    label.classList.add('text-primary');
                }

                // Update visual bintang
                stars.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-value'));
                    if (sVal <= value) {
                        s.classList.remove('bi-star', 'text-secondary');
                        s.classList.add('bi-star-fill', 'text-warning');
                    } else {
                        s.classList.remove('bi-star-fill', 'text-warning');
                        s.classList.add('bi-star', 'text-secondary');
                    }
                });
            });

            // Hover preview
            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.getAttribute('data-value'));
                stars.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-value'));
                    if (sVal <= value) {
                        s.classList.add('text-warning');
                    } else {
                        s.classList.remove('text-warning');
                    }
                });
            });

            // Mouse leave restore
            star.addEventListener('mouseleave', function() {
                const activeValue = parseInt(hiddenInput.value) || 0;
                stars.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-value'));
                    if (sVal <= activeValue) {
                        s.classList.remove('text-secondary');
                        s.classList.add('text-warning', 'bi-star-fill');
                    } else {
                        s.classList.remove('text-warning', 'bi-star-fill');
                        s.classList.add('text-secondary', 'bi-star');
                    }
                });
            });
        });
    });

    // 2. Logika Toggle Isi Nanti
    const btnIsiNanti = document.getElementById('btnIsiNanti');
    const btnTampilkanForm = document.getElementById('btnTampilkanForm');
    const ratingFormSection = document.getElementById('ratingFormSection');
    const ratingSkippedAlert = document.getElementById('ratingSkippedAlert');

    if (btnIsiNanti && ratingFormSection && ratingSkippedAlert) {
        btnIsiNanti.addEventListener('click', function() {
            ratingFormSection.classList.add('d-none');
            ratingSkippedAlert.classList.remove('d-none');
            sessionStorage.setItem('skip_rating_#<?= $transaction_to_rate["id_sewa"] ?? "" ?>', 'true');
        });

        // Cek status skip session
        if (sessionStorage.getItem('skip_rating_#<?= $transaction_to_rate["id_sewa"] ?? "" ?>') === 'true') {
            ratingFormSection.classList.add('d-none');
            ratingSkippedAlert.classList.remove('d-none');
        }
    }

    if (btnTampilkanForm) {
        btnTampilkanForm.addEventListener('click', function() {
            ratingFormSection.classList.remove('d-none');
            ratingSkippedAlert.classList.add('d-none');
            sessionStorage.removeItem('skip_rating_#<?= $transaction_to_rate["id_sewa"] ?? "" ?>');
        });
    }
});
</script>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>