<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya admin yang boleh mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Ambil data Pendapatan (Akun 4xx) dan Beban (Akun 5xx) secara dinamis dari Jurnal Umum
$sql = "SELECT 
            DATE_FORMAT(tanggal, '%Y-%m') AS periode_bulan,
            SUM(CASE WHEN kode_akun LIKE '4%' THEN Kredit - Debit ELSE 0 END) AS pendapatan_total,
            SUM(CASE WHEN kode_akun LIKE '5%' THEN Debit - Kredit ELSE 0 END) AS beban_total
        FROM jurnal 
        WHERE kode_akun LIKE '4%' OR kode_akun LIKE '5%'
        GROUP BY periode_bulan
        ORDER BY periode_bulan DESC";
$res = mysqli_query($conn, $sql);

$total_pendapatan_kumulatif = 0;
$total_beban_kumulatif = 0;
$total_laba_kumulatif = 0;

$reports = [];
if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        // Hitung Laba Bersih
        $row['laba_bersih'] = $row['pendapatan_total'] - $row['beban_total'];

        $reports[] = $row;
        $total_pendapatan_kumulatif += $row['pendapatan_total'];
        $total_beban_kumulatif += $row['beban_total'];
        $total_laba_kumulatif += $row['laba_bersih'];
    }
}

$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', 
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', 
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<div class="p-8">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                    <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                </div>
                Laporan Laba Rugi
            </h1>
            <p class="text-slate-500 font-medium mt-1">Laporan pendapatan dan beban secara real-time dari Buku Besar.</p>
        </div>
        <button onclick="window.print()" class="bg-slate-800 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 shadow-lg shadow-emerald-500/20 text-white border border-emerald-400">
            <div class="flex items-center gap-3 mb-2 opacity-90">
                <i data-lucide="trending-up" class="w-5 h-5"></i>
                <span class="text-sm font-bold tracking-wider uppercase">Total Pendapatan (Kumulatif)</span>
            </div>
            <h3 class="text-3xl font-black mt-2">Rp <?= number_format($total_pendapatan_kumulatif, 0, ',', '.'); ?></h3>
        </div>
        
        <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-6 shadow-lg shadow-rose-500/20 text-white border border-rose-400">
            <div class="flex items-center gap-3 mb-2 opacity-90">
                <i data-lucide="trending-down" class="w-5 h-5"></i>
                <span class="text-sm font-bold tracking-wider uppercase">Total Beban Operasional</span>
            </div>
            <h3 class="text-3xl font-black mt-2">Rp <?= number_format($total_beban_kumulatif, 0, ',', '.'); ?></h3>
        </div>
        
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 shadow-lg shadow-blue-500/20 text-white border border-blue-500">
            <div class="flex items-center gap-3 mb-2 opacity-90">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <span class="text-sm font-bold tracking-wider uppercase">Laba Bersih Keseluruhan</span>
            </div>
            <h3 class="text-3xl font-black mt-2">Rp <?= number_format($total_laba_kumulatif, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-center w-1/4">Periode Bulan</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-right">Pendapatan (Kredit)</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-right">Beban (Debit)</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-right w-1/4">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                                    <i data-lucide="folder-search" class="w-8 h-8"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Belum ada data jurnal keuangan yang terekam.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $row): 
                            $thn_bln = explode('-', $row['periode_bulan']);
                            $nama_periode = $bulan_indo[$thn_bln[1]] . ' ' . $thn_bln[0];
                        ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-slate-100 text-slate-700">
                                        <?= $nama_periode ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-emerald-600">
                                    Rp <?= number_format($row['pendapatan_total'], 0, ',', '.'); ?>
                                </td>
                                <td class="py-4 px-6 text-right font-bold text-rose-600">
                                    Rp <?= number_format($row['beban_total'], 0, ',', '.'); ?>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <?php if ($row['laba_bersih'] >= 0): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 w-full justify-end">
                                            <i data-lucide="trending-up" class="w-4 h-4"></i> Rp <?= number_format($row['laba_bersih'], 0, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold bg-rose-50 text-rose-700 border border-rose-200 w-full justify-end">
                                            <i data-lucide="trending-down" class="w-4 h-4"></i> Rp <?= number_format($row['laba_bersih'], 0, ',', '.'); ?>
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
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>