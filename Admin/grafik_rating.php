<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login_admin.php"); exit(); }
include 'koneksi.php';

// Handle Admin Reply
if (isset($_POST['reply_rating'])) {
    $id_rating = intval($_POST['id_rating']);
    $jawaban = mysqli_real_escape_string($conn, $_POST['jawaban_admin']);
    mysqli_query($conn, "UPDATE rating_sewa SET jawaban_admin = '$jawaban' WHERE id_rating = $id_rating");
    echo "<script>alert('Balasan berhasil disimpan!'); window.location='grafik_rating.php';</script>";
    exit();
}

include 'navbar.php'; 

// Mengambil rata-rata rating pelayanan
$query_rating = mysqli_query($conn, "SELECT AVG(rating_pelayanan) as avg_pelayanan, AVG(rating_supir) as avg_supir, COUNT(*) as total_ulasan FROM rating_sewa");
$data_rating = ($query_rating && mysqli_num_rows($query_rating) > 0) ? mysqli_fetch_assoc($query_rating) : ['avg_pelayanan' => 0, 'avg_supir' => 0, 'total_ulasan' => 0];

$avg_pelayanan = round($data_rating['avg_pelayanan'] ?? 0, 1);
$total_ulasan = $data_rating['total_ulasan'] ?? 0;

// Mengambil distribusi bintang pelayanan
$dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
if ($total_ulasan > 0) {
    $q_dist = mysqli_query($conn, "SELECT rating_pelayanan, COUNT(*) as count FROM rating_sewa GROUP BY rating_pelayanan");
    if ($q_dist) {
        while($r = mysqli_fetch_assoc($q_dist)) {
            $dist[(int)$r['rating_pelayanan']] = (int)$r['count'];
        }
    }
}
?>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Kepuasan Pelanggan</h1>
    <p class="text-slate-500 mt-1 font-medium italic">Analisis rating dan performa pelayanan rental.</p>
</div>

<div class="glass-card rounded-2xl p-8 hover-lift mb-10">
    <h5 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <i data-lucide="star" class="w-6 h-6 text-blue-600"></i> Ulasan & Rating Pelayanan
    </h5>
    
    <div class="flex flex-col md:flex-row gap-8 items-center">
        <!-- Kiri: Big Number -->
        <div class="flex flex-col items-center justify-center min-w-[150px]">
            <h1 class="text-6xl font-extrabold text-slate-800 tracking-tighter"><?= number_format($avg_pelayanan, 1) ?></h1>
            <div class="flex text-blue-500 my-2 text-xl">
                <?php 
                $full_stars = floor($avg_pelayanan);
                $half_star = ($avg_pelayanan - $full_stars) >= 0.5;
                for($i=1; $i<=5; $i++) {
                    if($i <= $full_stars) echo '<i class="bi bi-star-fill"></i>';
                    else if($i == $full_stars + 1 && $half_star) echo '<i class="bi bi-star-half"></i>';
                    else echo '<i class="bi bi-star"></i>';
                }
                ?>
            </div>
            <p class="text-sm font-medium text-slate-500"><?= $total_ulasan ?> ulasan</p>
        </div>
        
        <!-- Kanan: Bars -->
        <div class="flex-1 w-full flex flex-col gap-2">
            <?php for($i=5; $i>=1; $i--): 
                $count = $dist[$i];
                $pct = $total_ulasan > 0 ? ($count / $total_ulasan) * 100 : 0;
            ?>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-600 w-3"><?= $i ?></span>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: <?= $pct ?>%;"></div>
                </div>
                <span class="text-xs font-bold text-slate-400 w-8 text-right"><?= $count ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="glass-card rounded-2xl p-8 hover-lift mb-10">
    <h5 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
        <i data-lucide="message-square" class="w-6 h-6 text-blue-600"></i> Daftar Ulasan Lengkap
    </h5>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php
        $q_ulasan = mysqli_query($conn, "
            SELECT r.*, p.nama
            FROM rating_sewa r 
            JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
            ORDER BY r.tgl_rating DESC
        ");
        if($q_ulasan && mysqli_num_rows($q_ulasan) > 0) {
            while($u = mysqli_fetch_assoc($q_ulasan)) {
                ?>
                <div class="p-5 rounded-xl bg-slate-50 border border-blue-100/50 hover:border-blue-200 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <span class="font-bold text-slate-800 text-base block"><?= htmlspecialchars($u['nama'] ?? 'Pelanggan') ?></span>
                        <div class="flex text-blue-500 text-sm">
                            <?php for($i=0; $i<$u['rating_pelayanan']; $i++) echo '<i class="bi bi-star-fill"></i>'; ?>
                            <?php for($i=$u['rating_pelayanan']; $i<5; $i++) echo '<i class="bi bi-star text-slate-300"></i>'; ?>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 italic">"<?= !empty($u['ulasan']) ? htmlspecialchars($u['ulasan']) : 'Tidak ada ulasan tertulis.' ?>"</p>
                    <p class="text-[10px] text-slate-400 mt-3"><?= date('d M Y, H:i', strtotime($u['tgl_rating'])) ?></p>
                </div>
                <?php
            }
        } else {
        $q_ulasan = mysqli_query($conn, "SELECT r.*, p.nama, t.kode_mobil FROM rating_sewa r 
                                        JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                                        JOIN transaksi_sewa t ON r.id_transaksi = t.id_sewa
                                        ORDER BY r.tgl_rating DESC");
        if (mysqli_num_rows($q_ulasan) > 0) {
            while ($row = mysqli_fetch_assoc($q_ulasan)) {
                $bintang = (int)$row['rating_pelayanan'];
                $bg_color = $bintang >= 4 ? 'bg-green-50 border-green-200' : ($bintang == 3 ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200');
        ?>
        <div class="p-5 rounded-xl border <?= $bg_color ?> shadow-sm transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h6 class="font-bold text-slate-800 mb-1"><?= htmlspecialchars($row['nama']) ?> <span class="text-xs text-slate-500 font-normal ml-2">Transaksi #<?= $row['id_transaksi'] ?></span></h6>
                    <div class="flex text-yellow-500 text-sm">
                        <?php for($i=1; $i<=5; $i++) echo ($i <= $bintang) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                    </div>
                </div>
                <span class="text-xs text-slate-500"><?= date('d M Y, H:i', strtotime($row['tgl_rating'])) ?></span>
            </div>
            <p class="text-slate-700 italic mt-2">"<?= htmlspecialchars($row['ulasan']) ?>"</p>
            
            <?php if (!empty($row['jawaban_admin'])): ?>
                <div class="mt-4 bg-white/70 p-4 rounded-lg border border-slate-200/60 ml-4 relative">
                    <div class="absolute -top-3 left-4 bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold shadow-sm flex items-center gap-1">
                        <i class="bi bi-reply-fill"></i> Balasan Admin
                    </div>
                    <p class="text-sm text-slate-600 mb-0 mt-1"><?= nl2br(htmlspecialchars($row['jawaban_admin'])) ?></p>
                </div>
            <?php else: ?>
                <div class="mt-4 text-right">
                    <button class="btn btn-sm btn-indigo rounded-full px-4 font-medium shadow-sm hover:shadow-md transition-all" 
                            onclick="openReplyModal(<?= $row['id_rating'] ?>, <?= $bintang ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')">
                        <i class="bi bi-reply"></i> Balas Ulasan
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php } } else { ?>
            <div class="text-center py-8 text-slate-500">Belum ada ulasan dari pelanggan.</div>
        <?php } ?>
    </div>
</div>

<!-- Modal Balas Ulasan -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl rounded-2xl overflow-hidden">
            <div class="modal-header bg-indigo-600 text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-reply-all"></i> Balas Ulasan <span id="replyNamaPelanggan"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4 bg-slate-50">
                    <input type="hidden" name="id_rating" id="replyIdRating">
                    
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold text-slate-700 mb-0">Pesan Balasan</label>
                        <button type="button" class="btn btn-sm btn-outline-indigo rounded-pill py-0 px-3 text-xs fw-bold" onclick="generateAutoReply()">
                            <i class="bi bi-magic"></i> Auto-Reply
                        </button>
                    </div>
                    <textarea class="form-control rounded-xl border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" 
                              name="jawaban_admin" id="replyJawaban" rows="4" required placeholder="Ketik balasan Anda di sini..."></textarea>
                              
                    <input type="hidden" id="replyBintang">
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-xl fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="reply_rating" class="btn btn-indigo rounded-xl fw-medium shadow-md px-4">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
    
    function openReplyModal(idRating, bintang, nama) {
        document.getElementById('replyIdRating').value = idRating;
        document.getElementById('replyBintang').value = bintang;
        document.getElementById('replyNamaPelanggan').textContent = nama;
        document.getElementById('replyJawaban').value = '';
        new bootstrap.Modal(document.getElementById('replyModal')).show();
    }
    
    function generateAutoReply() {
        const bintang = parseInt(document.getElementById('replyBintang').value);
        const text = document.getElementById('replyJawaban');
        
        if (bintang < 4) {
            text.value = "Mohon maaf atas ketidaknyamanan yang Anda alami. Kami akan terus mengevaluasi dan memperbaiki layanan kami ke depannya. Terima kasih atas masukan Anda yang sangat berharga.";
        } else {
            text.value = "Terima kasih banyak atas ulasan positif Anda! Kami sangat senang dapat melayani Anda dan berharap dapat menyambut Anda kembali di perjalanan berikutnya.";
        }
    }
</script>
</body>
</html>