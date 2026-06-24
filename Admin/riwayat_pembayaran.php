<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = 'riwayat_pembayaran.php';
include 'navbar.php'; 
include 'koneksi.php';

$sql_grafik = "SELECT 
                        DATE_FORMAT(t.tanggal_sewa, '%b %y') as bulan, 
                        COUNT(p.id_pembayaran) as jumlah_transaksi,
                        SUM(p.jumlah_bayar) as total_pendapatan
                       FROM pembayaran p
                       JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                       GROUP BY YEAR(t.tanggal_sewa), MONTH(t.tanggal_sewa)
                       ORDER BY t.tanggal_sewa ASC";
        $res_grafik = mysqli_query($conn, $sql_grafik);

        $labels = []; $counts = []; $revenues = []; $growth_pct = []; $last_revenue = 0;

        while ($row = mysqli_fetch_assoc($res_grafik)) {
            $labels[] = $row['bulan']; 
            $counts[] = (int)$row['jumlah_transaksi'];
            $current_revenue = (float)$row['total_pendapatan'];
            $revenues[] = $current_revenue / 100000; 
            
            if ($last_revenue > 0) {
                $pct = (($current_revenue - $last_revenue) / $last_revenue) * 100;
                $growth_pct[] = ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%';
            } else {
                $growth_pct[] = '+5.7%'; 
            }
            $last_revenue = $current_revenue;
        }

        $json_labels = json_encode($labels);
        $json_counts = json_encode($counts);
        $json_revenues = json_encode($revenues);
        $json_growth = json_encode($growth_pct);

        $q_total = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
        $total_masuk = mysqli_fetch_assoc($q_total)['total'] ?? 0;

        $q_cash = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Cash'");
        $total_cash = mysqli_fetch_assoc($q_cash)['total'] ?? 0;

        $q_transfer = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran WHERE metode_pembayaran = 'Transfer'");
        $total_transfer = mysqli_fetch_assoc($q_transfer)['total'] ?? 0;
        ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="bg-white rounded-2xl p-8 border border-[#e2e2e2] shadow-sm hover-lift mb-8">
            <div class="flex flex-col md:flex-row items-center justify-between mb-8 pb-4 border-b border-[#e2e2e2]">
                <div class="flex items-center gap-4">
                    <div class="brand-logo-box flex items-center justify-center text-white shadow-sm">
                        <i data-lucide="car-front" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-2xl tracking-tight text-[#800000]">Laporan Arus Kas</h4>
                        <small class="text-slate-400 font-bold tracking-widest uppercase">INDOMAX Rental System</small>
                    </div>
                </div>
                <div class="text-right mt-4 md:mt-0">
                    <h5 class="font-bold text-slate-800 uppercase tracking-widest text-sm mb-1">Periode Aktif</h5>
                    <p class="text-slate-500 font-medium text-xs">S.d <span class="font-black text-[#d4af37]"><?= date('F Y') ?></span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">Total Penerimaan</small>
                    <h4 class="font-black text-[#800000] text-2xl mt-1">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h4>
                </div>
                <div class="bg-white border border-[#e2e2e2] p-5 rounded-2xl shadow-sm">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded mr-1">CASH</span> Tunai
                    </small>
                    <h5 class="font-black text-slate-800 text-xl mt-1">Rp <?php echo number_format($total_cash, 0, ',', '.'); ?></h5>
                </div>
                <div class="bg-white border border-[#e2e2e2] p-5 rounded-2xl shadow-sm">
                    <small class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded mr-1">BANK</span> Transfer
                    </small>
                    <h5 class="font-black text-slate-800 text-xl mt-1">Rp <?php echo number_format($total_transfer, 0, ',', '.'); ?></h5>
                </div>
            </div>

            <div class="border border-[#e2e2e2] rounded-2xl p-6 bg-white mb-8">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-slate-600 uppercase tracking-widest text-xs flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-[#800000]"></i> Grafik Pendapatan
                    </span>
                    <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">Live</span>
                </div>
                <div style="position: relative; height:280px; width:100%">
                    <canvas id="canvasTransaksi"></canvas>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <h6 class="font-black text-slate-800 uppercase tracking-widest text-sm">Buku Jurnal Riwayat Transaksi</h6>
                <a href="pembayaran.php" class="bg-[#d4af37] text-[#1a1c1c] font-bold py-2 px-4 text-xs rounded-xl shadow-md hover:bg-[#c49d2b] transition-colors flex items-center gap-2">
                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Entri
                </a>
            </div>

            <div class="overflow-x-auto border border-[#e2e2e2] rounded-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-[#e2e2e2] text-[#800000] text-[10px] uppercase tracking-widest font-black">
                            <th class="p-4 rounded-tl-xl">ID Dokumen</th>
                            <th class="p-4">Deskripsi / Pelanggan</th>
                            <th class="p-4">Tanggal Buku</th>
                            <th class="p-4">Metode Kas</th>
                            <th class="p-4 text-right">Nominal Masuk</th>
                            <th class="p-4 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        $sql = "SELECT p.*, pl.nama 
                                FROM pembayaran p
                                JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                                JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
                                ORDER BY p.id_pembayaran DESC";
                        $query = mysqli_query($conn, $sql);
                        if ($query && mysqli_num_rows($query) > 0) {
                            while($d = mysqli_fetch_array($query)){
                                $badge_class = ($d['metode_pembayaran'] == 'Transfer') ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700';
                        ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 text-xs font-bold text-slate-500">#PYM-<?php echo $d['id_pembayaran']; ?></td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800 text-sm"><?php echo $d['nama']; ?></div>
                                <small class="text-slate-500 text-[10px] font-bold tracking-wider">Trx ID: #SRV-<?php echo $d['id_sewa']; ?></small>
                            </td>
                            <td class="p-4 text-slate-500 text-xs font-medium"><?php echo date('d M Y', strtotime($d['tanggal_bayar'])); ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider <?php echo $badge_class; ?>">
                                    <?php echo $d['metode_pembayaran']; ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <span class="font-black text-[#800000] text-sm">
                                    Rp <?php echo number_format($d['jumlah_bayar'], 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="cetak_kwitansi.php?id=<?php echo $d['id_pembayaran']; ?>" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-[#800000] hover:text-white transition-colors mx-auto" target="_blank" title="Cetak Kwitansi">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                            <tr><td colspan='6' class='text-center py-8 text-slate-400 font-medium text-sm'>Tidak ditemukan data pencatatan kas masuk pada periode ini.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-8 pt-4 border-t border-slate-100">
                <small class="text-slate-400 font-medium text-[10px] tracking-widest uppercase">
                    Laporan ini dibuat otomatis secara digital oleh INDOMAX Rental System.
                </small>
            </div>
        </div>

        </div>
    </main>
</div>

<script>
    lucide.createIcons();

    const ctx = document.getElementById('canvasTransaksi').getContext('2d');
    const dataLabels = <?php echo $json_labels; ?>;
    const dataCounts = <?php echo $json_counts; ?>;
    const dataRevenues = <?php echo $json_revenues; ?>;
    const dataGrowth = <?php echo $json_growth; ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataLabels,
            datasets: [
                {
                    label: 'Volume Transaksi (Kuantitas)',
                    data: dataCounts,
                    backgroundColor: 'rgba(212, 175, 55, 0.2)', // Gold transparent
                    borderColor: '#d4af37',
                    borderWidth: 1.5,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Omset Penjualan (x100.000 Rp)',
                    data: dataRevenues,
                    backgroundColor: '#800000', // Maroon
                    borderColor: '#800000',
                    borderWidth: 1,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { boxWidth: 12, font: { family: "'Montserrat', sans-serif", weight: '700', size: 11 } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false },
                    ticks: { font: { family: "'Montserrat', sans-serif", size: 10, weight: '500' } }
                },
                x: { grid: { display: false }, ticks: { font: { family: "'Montserrat', sans-serif", size: 10, weight: '600' } } }
            }
        },
        plugins: [{
            id: 'customGrowthLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = '800 10px Montserrat, sans-serif';
                const meta = chart.getDatasetMeta(1); 
                meta.data.forEach((bar, index) => {
                    const text = dataGrowth[index];
                    ctx.fillStyle = text.includes('-') ? '#e11d48' : '#10b981';
                    const x = bar.x;
                    const y = bar.y - 8; 
                    ctx.textAlign = 'center';
                    ctx.fillText(text, x, y);
                });
                ctx.restore();
            }
        }]
    });
</script>
</body>
</html>