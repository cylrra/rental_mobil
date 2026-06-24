<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'navbar.php';
include 'koneksi.php';

// Ambil data riwayat pemeliharaan (status 'selesai')
$riwayat = mysqli_query($conn, "SELECT p.*, m.merk, m.nopol FROM pemeliharaan p JOIN mobil m ON p.kode_mobil = m.kode_mobil WHERE p.status = 'selesai' ORDER BY p.tanggal_pemeliharaan DESC");
?>

<div class="mb-6 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="text-4xl font-black text-[#800000] tracking-tight"><i class="bi bi-clock-history text-[#800000] me-2"></i> Riwayat Pemeliharaan</h3>
        <p class="text-slate-500 mt-1 font-medium italic">Catatan historis servis dan perbaikan armada mobil yang telah selesai.</p>
    </div>
    <a href="jadwal_service.php" class="btn text-[#1a1c1c] rounded-xl px-4 py-2.5 font-bold shadow-md shadow-[#d4af37]/20 hover:bg-[#c49d2b] transition-all flex items-center gap-2" style="background-color:#d4af37; border: none;">
        <i class="bi bi-calendar-check me-1"></i> Ke Jadwal Servis
    </a>
</div>

<div class="card border-0 shadow-sm rounded-xl overflow-hidden mb-10">
    <div class="card-header bg-white py-4 border-bottom border-slate-100 flex justify-between items-center">
        <h5 class="mb-0 fw-bold text-[#800000] flex items-center gap-2"><i class="bi bi-check-all me-1"></i> Daftar Pemeliharaan Selesai</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-left border-collapse w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="ps-6 py-4 w-16">No</th>
                        <th class="py-4"><i class="bi bi-car-front text-slate-400 me-1"></i> Mobil</th>
                        <th class="py-4"><i class="bi bi-calendar2-check text-slate-400 me-1"></i> Tanggal Selesai</th>
                        <th class="py-4"><i class="bi bi-wrench-adjustable text-slate-400 me-1"></i> Jenis</th>
                        <th class="py-4"><i class="bi bi-cash-stack text-slate-400 me-1"></i> Total Biaya</th>
                        <th class="py-4"><i class="bi bi-chat-left-text text-slate-400 me-1"></i> Keterangan</th>
                        <th class="pe-6 py-4 text-right"><i class="bi bi-shield-check text-slate-400 me-1"></i> Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php 
                    $no = 1; 
                    if ($riwayat && mysqli_num_rows($riwayat) > 0) {
                        while($r = mysqli_fetch_assoc($riwayat)) { 
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="ps-6 py-4 text-sm font-semibold text-slate-400">#<?= $no++; ?></td>
                        <td class="py-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="w-9 h-9 rounded-lg bg-[#800000]/10 text-[#800000] flex items-center justify-center shrink-0">
                                    <i class="bi bi-car-front-fill fs-6"></i>
                                </div>
                                <div>
                                    <span class="fw-bold text-[#1a1c1c] text-sm block leading-tight"><?= htmlspecialchars($r['merk']); ?></span>
                                    <small class="text-slate-400 text-[11px] font-bold block mt-0.5"><?= htmlspecialchars($r['nopol']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td class="py-4">
                            <span class="flex items-center gap-1.5 text-sm text-[#4d4c4c] font-semibold">
                                <i class="bi bi-calendar-event text-slate-400"></i> <?= date('d M Y', strtotime($r['tanggal_pemeliharaan'])); ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                <i class="bi bi-wrench-adjustable-circle text-slate-400"></i> <?= htmlspecialchars($r['jenis_pemeliharaan']); ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="fw-bold text-[#800000] text-sm flex items-center gap-1">
                                <i class="bi bi-wallet2 text-[#800000]"></i> Rp <?= number_format($r['biaya_pemeliharaan'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="py-4">
                            <span class="flex items-center gap-1.5 text-sm text-slate-500 font-medium max-w-xs truncate" title="<?= htmlspecialchars($r['keterangan']); ?>">
                                <i class="bi bi-file-earmark-text text-slate-400"></i> <?= htmlspecialchars($r['keterangan']); ?>
                            </span>
                        </td>
                        <td class="pe-6 py-4 text-right">
                            <span class="badge bg-[#10b981] text-white rounded-pill px-3 py-2 text-[11px] font-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-check-circle-fill"></i> Selesai
                            </span>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-10 text-slate-500 font-medium'>Belum ada riwayat pemeliharaan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
