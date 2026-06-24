<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protection: Only admin can view
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// --- PROCESS ADMIN REPLY ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    $id_rating = intval($_POST['id_rating']);
    $jawaban_admin = mysqli_real_escape_string($conn, $_POST['jawaban_admin']);
    
    $update_query = "UPDATE rating_sewa SET jawaban_admin = '$jawaban_admin' WHERE id_rating = $id_rating";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Balasan ulasan berhasil dikirim!'); window.location='grafik_rating.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal mengirim balasan: " . addslashes(mysqli_error($conn)) . "');</script>";
    }
}

// --- FETCH DATA ---
// 1. Calculate Average Statistics
$query_avg = mysqli_query($conn, "SELECT 
    AVG(rating_pelayanan) as avg_pely, 
    AVG(rating_supir) as avg_sup, 
    AVG(rating_mobil) as avg_mobl,
    COUNT(id_rating) as total_ulasan
    FROM rating_sewa");
$data_avg = mysqli_fetch_assoc($query_avg);

$total_avg = 0;
if (($data_avg['total_ulasan'] ?? 0) > 0) {
    $total_avg = (($data_avg['avg_pely'] ?? 0) + ($data_avg['avg_sup'] ?? 0) + ($data_avg['avg_mobl'] ?? 0)) / 3;
}

// 2. Fetch list of reviews with customer name and vehicle info
$query_ulasan = mysqli_query($conn, "SELECT r.*, p.nama as nama_pelanggan, m.merk as merk_mobil, m.nopol
                                     FROM rating_sewa r 
                                     JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
                                     JOIN transaksi_sewa t ON r.id_transaksi = t.id_sewa
                                     JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                     ORDER BY r.tgl_rating DESC");

include 'navbar.php';

// Helper function to render stars
function renderStars($score) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $score) {
            $stars .= '<i class="bi bi-star-fill text-[#d4af37] me-1"></i>';
        } else {
            $stars .= '<i class="bi bi-star text-slate-300 me-1"></i>';
        }
    }
    return $stars;
}
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-4xl font-black text-[#800000] tracking-tight">Rating & Ulasan Pelanggan</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Pantau feedback kepuasan pelanggan dan berikan tanggapan.</p>
    </div>

    <!-- Rating Summary Dashboard Card -->
    <div class="bg-white rounded-2xl p-6 border border-[#e2e2e2] shadow-sm mb-8 hover-lift">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
            <div class="text-center md:border-r border-[#e2e2e2] pr-6">
                <span class="text-sm font-bold text-slate-400 uppercase tracking-wider block mb-1">Rata-rata Rating</span>
                <h1 class="text-5xl font-black text-[#800000] tracking-tight"><?= number_format($total_avg, 1) ?></h1>
                <div class="flex justify-center mt-2 mb-1">
                    <?= renderStars(round($total_avg)) ?>
                </div>
                <p class="text-xs text-slate-500 font-semibold"><?= $data_avg['total_ulasan'] ?> Ulasan masuk</p>
            </div>
            
            <div class="col-span-3 pl-0 md:pl-6 space-y-4">
                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-slate-600">Pelayanan Kantor</span>
                        <span class="text-[#800000]"><?= number_format($data_avg['avg_pely'] ?? 0, 1) ?> / 5.0</span>
                    </div>
                    <div class="w-full bg-[#f3f3f3] h-2 rounded-full overflow-hidden">
                        <div class="bg-[#d4af37] h-full rounded-full" style="width: <?= (($data_avg['avg_pely'] ?? 0)/5)*100 ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-slate-600">Keramahan Sopir</span>
                        <span class="text-[#800000]"><?= number_format($data_avg['avg_sup'] ?? 0, 1) ?> / 5.0</span>
                    </div>
                    <div class="w-full bg-[#f3f3f3] h-2 rounded-full overflow-hidden">
                        <div class="bg-[#d4af37] h-full rounded-full" style="width: <?= (($data_avg['avg_sup'] ?? 0)/5)*100 ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-sm font-bold mb-1">
                        <span class="text-slate-600">Kondisi Armada Mobil</span>
                        <span class="text-[#800000]"><?= number_format($data_avg['avg_mobl'] ?? 0, 1) ?> / 5.0</span>
                    </div>
                    <div class="w-full bg-[#f3f3f3] h-2 rounded-full overflow-hidden">
                        <div class="bg-[#d4af37] h-full rounded-full" style="width: <?= (($data_avg['avg_mobl'] ?? 0)/5)*100 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Listing -->
    <h4 class="text-xl font-bold text-[#1a1c1c] mb-4">Daftar Review Pelanggan</h4>
    <div class="space-y-6">
        <?php 
        if ($query_ulasan && mysqli_num_rows($query_ulasan) > 0):
            while($row = mysqli_fetch_assoc($query_ulasan)): 
        ?>
            <div class="bg-white rounded-xl p-6 border border-[#e2e2e2] shadow-sm">
                <div class="flex flex-col md:flex-row justify-between md:items-center border-b border-[#f3f3f3] pb-4 mb-4 gap-2">
                    <div>
                        <h6 class="font-bold text-[#1a1c1c] text-lg"><?= htmlspecialchars($row['nama_pelanggan']) ?></h6>
                        <span class="text-xs text-slate-400 font-medium"><?= date('d M Y, H:i', strtotime($row['tgl_rating'])) ?></span>
                    </div>
                    <div class="flex flex-wrap gap-4 text-xs font-semibold">
                        <div class="bg-[#f3f3f3] px-3 py-1.5 rounded-lg text-slate-700">
                            🚗 Mobil: <span class="text-[#800000]"><?= htmlspecialchars($row['merk_mobil']) ?> (<?= htmlspecialchars($row['nopol']) ?>)</span>
                        </div>
                        <div class="bg-[#f3f3f3] px-3 py-1.5 rounded-lg text-slate-700">
                            🔖 Trx ID: <span class="text-[#800000]">#<?= $row['id_transaksi'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Individual Ratings Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 bg-[#f9f9f9] p-3 rounded-lg border border-[#e2e2e2]">
                    <div class="flex items-center justify-between px-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Pelayanan</span>
                        <div class="flex"><?= renderStars($row['rating_pelayanan']) ?></div>
                    </div>
                    <div class="flex items-center justify-between px-2 md:border-x border-[#e2e2e2]">
                        <span class="text-xs font-bold text-slate-500 uppercase">Sopir</span>
                        <div class="flex"><?= renderStars($row['rating_supir']) ?></div>
                    </div>
                    <div class="flex items-center justify-between px-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Mobil</span>
                        <div class="flex"><?= renderStars($row['rating_mobil']) ?></div>
                    </div>
                </div>

                <!-- Review Content -->
                <div class="mb-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Komentar Pelanggan</span>
                    <p class="text-[#1a1c1c] text-sm leading-relaxed font-medium bg-[#f9f9f9] p-3 rounded-lg border border-dashed border-[#e2e2e2]">
                        "<?= htmlspecialchars($row['ulasan'] ?? 'Tidak ada komentar tertulis.') ?>"
                    </p>
                </div>

                <!-- Reply Area -->
                <div class="border-t border-[#f3f3f3] pt-4">
                    <?php if (empty($row['jawaban_admin'])): ?>
                        <!-- Form to send reply -->
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Balas Ulasan Ini</span>
                        <form method="POST" action="">
                            <input type="hidden" name="id_rating" value="<?= $row['id_rating'] ?>">
                            <div class="flex gap-3">
                                <textarea name="jawaban_admin" class="form-control" rows="2" placeholder="Tulis tanggapan atau balasan admin..." required style="border-radius: 8px; border-color: #e2e2e2; resize: none; font-size: 0.85rem; font-weight: 500;"></textarea>
                                <button type="submit" name="submit_reply" class="bg-[#d4af37] text-[#1a1c1c] font-bold px-5 rounded-lg hover:bg-[#c49d2b] transition-colors shrink-0 text-sm flex items-center justify-center gap-1">
                                    <i class="bi bi-reply-fill fs-5"></i> Balas
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- Show reply -->
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Balasan Anda (Admin)</span>
                        <div class="bg-[#800000]/5 border-l-4 border-[#800000] p-3 rounded-r-lg">
                            <p class="text-sm font-semibold text-[#1a1c1c] mb-0 leading-relaxed">
                                <?= htmlspecialchars($row['jawaban_admin']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
        ?>
            <div class="bg-white rounded-xl p-8 border border-[#e2e2e2] text-center">
                <p class="text-slate-400 font-semibold mb-0">Belum ada rating atau ulasan masuk.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</main>
</div>
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>