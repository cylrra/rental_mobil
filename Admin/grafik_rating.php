<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login_admin.php"); exit(); }
include 'koneksi.php';
include 'navbar.php'; 

$dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
$total_ulasan = 0; $jml_bintang = 0;
$q = mysqli_query($conn, "SELECT nilai_rating, COUNT(*) as jml FROM ulasan GROUP BY nilai_rating");
while ($r = mysqli_fetch_assoc($q)) {
    $dist[(int)$r['nilai_rating']] = (int)$r['jml'];
    $total_ulasan += (int)$r['jml'];
    $jml_bintang += ((int)$r['nilai_rating'] * (int)$r['jml']);
}
$rata = ($total_ulasan > 0) ? round($jml_bintang / $total_ulasan, 1) : 0;
?>

<div class="mb-4">
    <h3 class="fw-bold"><i class="bi bi-star text-warning me-2"></i> Kepuasan Pelanggan</h3>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
            <h6 class="text-muted text-uppercase fw-bold mb-3">Rata-rata Rating</h6>
            <h1 class="display-1 fw-bold text-dark mb-0"><?= number_format($rata, 1); ?></h1>
            <p class="text-muted small mb-4">Dari <?= $total_ulasan; ?> Ulasan</p>
            
            <div class="d-flex flex-column gap-2 px-3">
                <?php foreach([5,4,3,2,1] as $b): 
                    $pct = ($total_ulasan > 0) ? round(($dist[$b]/$total_ulasan)*100) : 0; ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-bold"><?= $b ?> <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%"></div>
                    </div>
                    <span class="text-muted small" style="width: 20px;"><?= $dist[$b] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold mb-4">Ulasan Terbaru</h5>
            <div class="row g-3">
                <?php
                $qr = mysqli_query($conn, "SELECT * FROM ulasan ORDER BY tanggal_ulasan DESC LIMIT 6");
                if(mysqli_num_rows($qr) > 0){
                    while($rev = mysqli_fetch_assoc($qr)): ?>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold small"><?= $rev['nama_pelanggan'] ?></span>
                                <div class="text-warning small">
                                    <?php for($i=1; $i<=5; $i++) echo '<i class="bi bi-star'.($i<=$rev['nilai_rating']?'-fill':'').'"></i>'; ?>
                                </div>
                            </div>
                            <p class="text-muted small mb-0 fst-italic">"<?= $rev['komentar'] ?>"</p>
                        </div>
                    </div>
                    <?php endwhile; 
                } else {
                    echo "<div class='text-center text-muted py-4'>Belum ada ulasan.</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

</div> 
    </div> 
</div> 
</body>
</html>