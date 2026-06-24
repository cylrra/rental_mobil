<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Get list of accounts for dropdown
$query_coa = mysqli_query($conn, "SELECT kode_akun, nama_akun, saldo_awal FROM nama_akun ORDER BY kode_akun ASC");
$daftar_akun = [];
while ($row = mysqli_fetch_assoc($query_coa)) {
    $daftar_akun[] = $row;
}

$selected_akun = isset($_GET['akun']) ? mysqli_real_escape_string($conn, $_GET['akun']) : '111'; // Default to Kas (111)
$bulan = isset($_GET['bulan']) ? mysqli_real_escape_string($conn, $_GET['bulan']) : date('Y-m');

// Find selected account details
$akun_detail = null;
foreach ($daftar_akun as $akun) {
    if ($akun['kode_akun'] === $selected_akun) {
        $akun_detail = $akun;
        break;
    }
}

// Fetch general ledger entries for the selected account and month
$sql_jurnal = "SELECT * FROM jurnal 
               WHERE kode_akun = '$selected_akun' 
               AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan' 
               ORDER BY tanggal ASC, id_jurnal ASC";
$res_jurnal = mysqli_query($conn, $sql_jurnal);

// Get saldo awal bulan (balance up to the start of the selected month)
// This depends on the account type (Asset/Expense = normal debit, Liab/Equity/Revenue = normal credit)
$tipe_normal_debit = in_array(substr($selected_akun, 0, 1), ['1', '5']); // 1xx (Assets), 5xx (Expenses)

$sql_saldo_sebelumnya = "SELECT SUM(Debit) as total_debit, SUM(Kredit) as total_kredit 
                         FROM jurnal 
                         WHERE kode_akun = '$selected_akun' 
                         AND DATE_FORMAT(tanggal, '%Y-%m') < '$bulan'";
$res_saldo = mysqli_query($conn, $sql_saldo_sebelumnya);
$row_saldo = mysqli_fetch_assoc($res_saldo);

$saldo_awal = floatval($akun_detail['saldo_awal'] ?? 0);
if ($tipe_normal_debit) {
    $saldo_awal += floatval($row_saldo['total_debit']) - floatval($row_saldo['total_kredit']);
} else {
    $saldo_awal += floatval($row_saldo['total_kredit']) - floatval($row_saldo['total_debit']);
}

$saldo_berjalan = $saldo_awal;
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                <i data-lucide="book-marked" class="w-6 h-6"></i>
            </div>
            Buku Besar
        </h1>
        <p class="text-slate-500 font-medium mt-1">Lacak mutasi debit dan kredit secara mendetail untuk setiap akun.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
        <form method="GET" action="" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-grow">
                <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Akun COA</label>
                <select name="akun" class="w-full rounded-xl border-slate-200 px-4 py-2.5 bg-slate-50 focus:ring-indigo-500 focus:border-indigo-500">
                    <?php foreach ($daftar_akun as $akun): ?>
                        <option value="<?= $akun['kode_akun'] ?>" <?= ($akun['kode_akun'] === $selected_akun) ? 'selected' : '' ?>>
                            <?= $akun['kode_akun'] ?> - <?= $akun['nama_akun'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Bulan Periode</label>
                <input type="month" name="bulan" value="<?= $bulan ?>" class="w-full rounded-xl border-slate-200 px-4 py-2.5 bg-slate-50 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white font-bold py-2.5 px-6 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <?php if ($akun_detail): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h4 class="text-lg font-black text-slate-800 uppercase tracking-wide">
                    Buku Besar: <?= $akun_detail['nama_akun'] ?> (<?= $selected_akun ?>)
                </h4>
                <p class="text-sm font-medium text-slate-500 mt-1">Periode: <?= date('F Y', strtotime($bulan . '-01')) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Saldo Normal</p>
                <p class="text-sm font-bold text-slate-800"><?= $tipe_normal_debit ? 'DEBIT' : 'KREDIT' ?></p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200">
                        <th class="py-3 px-6 text-xs font-black text-slate-500 uppercase tracking-wider w-32">Tanggal</th>
                        <th class="py-3 px-6 text-xs font-black text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="py-3 px-6 text-xs font-black text-slate-500 uppercase tracking-wider text-right w-40">Debit</th>
                        <th class="py-3 px-6 text-xs font-black text-slate-500 uppercase tracking-wider text-right w-40">Kredit</th>
                        <th class="py-3 px-6 text-xs font-black text-slate-500 uppercase tracking-wider text-right w-48">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <!-- Saldo Awal -->
                    <tr class="bg-indigo-50/30">
                        <td class="py-4 px-6 text-slate-500 font-medium"><?= $bulan ?>-01</td>
                        <td class="py-4 px-6 font-bold text-slate-700 italic">Saldo Awal Bulan</td>
                        <td class="py-4 px-6 text-right text-slate-400">-</td>
                        <td class="py-4 px-6 text-right text-slate-400">-</td>
                        <td class="py-4 px-6 text-right font-black text-indigo-700">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></td>
                    </tr>
                    
                    <?php 
                    $total_mutasi_debit = 0;
                    $total_mutasi_kredit = 0;
                    
                    if (mysqli_num_rows($res_jurnal) > 0):
                        while ($row = mysqli_fetch_assoc($res_jurnal)): 
                            $debit = floatval($row['Debit']);
                            $kredit = floatval($row['Kredit']);
                            
                            $total_mutasi_debit += $debit;
                            $total_mutasi_kredit += $kredit;
                            
                            if ($tipe_normal_debit) {
                                $saldo_berjalan += ($debit - $kredit);
                            } else {
                                $saldo_berjalan += ($kredit - $debit);
                            }
                    ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6 text-slate-600 font-medium"><?= date('Y-m-d', strtotime($row['tanggal'])) ?></td>
                            <td class="py-4 px-6 text-slate-800 font-medium"><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td class="py-4 px-6 text-right font-semibold text-slate-700"><?= $debit > 0 ? 'Rp ' . number_format($debit, 0, ',', '.') : '-' ?></td>
                            <td class="py-4 px-6 text-right font-semibold text-slate-700"><?= $kredit > 0 ? 'Rp ' . number_format($kredit, 0, ',', '.') : '-' ?></td>
                            <td class="py-4 px-6 text-right font-bold text-slate-800">Rp <?= number_format($saldo_berjalan, 0, ',', '.') ?></td>
                        </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500 font-medium italic">Tidak ada mutasi jurnal pada bulan ini.</td>
                        </tr>
                    <?php endif; ?>
                    
                    <!-- Total Bawah -->
                    <tr class="bg-slate-50 border-t-2 border-slate-200">
                        <td colspan="2" class="py-4 px-6 text-right font-black text-slate-700 uppercase">Mutasi Bulan Ini</td>
                        <td class="py-4 px-6 text-right font-black text-emerald-600">Rp <?= number_format($total_mutasi_debit, 0, ',', '.') ?></td>
                        <td class="py-4 px-6 text-right font-black text-rose-600">Rp <?= number_format($total_mutasi_kredit, 0, ',', '.') ?></td>
                        <td class="py-4 px-6 text-right font-black text-indigo-700">Rp <?= number_format($saldo_berjalan, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>
