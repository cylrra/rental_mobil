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

// Fungsi untuk menghitung saldo berjalan suatu akun (Sama seperti neraca)
function getSaldoTotal($conn, $kode_akun, $normal_balance = 'debit') {
    $q_awal = mysqli_query($conn, "SELECT saldo_awal FROM nama_akun WHERE kode_akun = '$kode_akun'");
    $r_awal = mysqli_fetch_assoc($q_awal);
    $saldo_awal = floatval($r_awal['saldo_awal'] ?? 0);

    $q_mutasi = mysqli_query($conn, "SELECT SUM(debit) as tot_debit, SUM(kredit) as tot_kredit FROM jurnal WHERE kode_akun = '$kode_akun'");
    $r_mutasi = mysqli_fetch_assoc($q_mutasi);
    $tot_debit = floatval($r_mutasi['tot_debit'] ?? 0);
    $tot_kredit = floatval($r_mutasi['tot_kredit'] ?? 0);

    if ($normal_balance == 'debit') {
        return $saldo_awal + $tot_debit - $tot_kredit;
    } else {
        return $saldo_awal + $tot_kredit - $tot_debit;
    }
}

$neraca_saldo = [];
$total_debit = 0;
$total_kredit = 0;

$query_coa = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM nama_akun ORDER BY kode_akun ASC");
while ($row = mysqli_fetch_assoc($query_coa)) {
    $kode = $row['kode_akun'];
    $awalan = substr($kode, 0, 1);
    
    // Tentukan normal balance
    // 1xx Aset = Debit, 5xx Beban = Debit
    // 2xx Kewajiban = Kredit, 3xx Ekuitas = Kredit, 4xx Pendapatan = Kredit
    // Pengecualian Prive (312) = Debit
    $normal = 'kredit';
    if ($awalan == '1' || $awalan == '5' || $kode == '312') {
        $normal = 'debit';
    }

    $saldo = getSaldoTotal($conn, $kode, $normal);
    
    // Hanya masukkan akun yang ada saldonya (atau saldo 0 tetap masuk biar rapi)
    if ($saldo >= 0) {
        if ($normal == 'debit') {
            $neraca_saldo[] = ['kode' => $kode, 'nama' => $row['nama_akun'], 'debit' => $saldo, 'kredit' => 0];
            $total_debit += $saldo;
        } else {
            $neraca_saldo[] = ['kode' => $kode, 'nama' => $row['nama_akun'], 'debit' => 0, 'kredit' => $saldo];
            $total_kredit += $saldo;
        }
    } else {
        // Jika saldo minus (abnormal), letakkan di sisi sebaliknya
        if ($normal == 'debit') {
            $neraca_saldo[] = ['kode' => $kode, 'nama' => $row['nama_akun'], 'debit' => 0, 'kredit' => abs($saldo)];
            $total_kredit += abs($saldo);
        } else {
            $neraca_saldo[] = ['kode' => $kode, 'nama' => $row['nama_akun'], 'debit' => abs($saldo), 'kredit' => 0];
            $total_debit += abs($saldo);
        }
    }
}

$is_balance = round($total_debit, 2) == round($total_kredit, 2);
?>

<div class="p-8">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                    <i data-lucide="calculator" class="w-6 h-6"></i>
                </div>
                Neraca Saldo (Trial Balance)
            </h1>
            <p class="text-slate-500 font-medium mt-1">Laporan kesesuaian total sisi Debit dan Kredit dari semua akun.</p>
        </div>
        <button onclick="window.print()" class="bg-slate-800 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 flex items-center justify-center gap-3 <?= $is_balance ? 'bg-emerald-50 text-emerald-800 border-b border-emerald-100' : 'bg-rose-50 text-rose-800 border-b border-rose-100' ?>">
            <i data-lucide="<?= $is_balance ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
            <span class="font-bold uppercase tracking-wider">Status: <?= $is_balance ? 'SEIMBANG (BALANCE)' : 'TIDAK SEIMBANG (UNBALANCED)' ?></span>
            <?php if (!$is_balance): ?>
                <span class="ml-4 font-bold badge bg-rose-200 text-rose-900">Selisih: Rp <?= number_format(abs($total_debit - $total_kredit), 2, ',', '.') ?></span>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider">Kode Akun</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider">Nama Akun Pembukuan</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-right w-48">Debit (Rp)</th>
                        <th class="py-4 px-6 text-sm font-black text-slate-500 uppercase tracking-wider text-right w-48">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($neraca_saldo as $akun): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-6 font-mono text-slate-500"><?= $akun['kode'] ?></td>
                            <td class="py-3 px-6 font-bold text-slate-700"><?= $akun['nama'] ?></td>
                            <td class="py-3 px-6 text-right font-medium <?= $akun['debit'] > 0 ? 'text-indigo-600' : 'text-slate-300' ?>">
                                <?= $akun['debit'] > 0 ? number_format($akun['debit'], 2, ',', '.') : '-' ?>
                            </td>
                            <td class="py-3 px-6 text-right font-medium <?= $akun['kredit'] > 0 ? 'text-indigo-600' : 'text-slate-300' ?>">
                                <?= $akun['kredit'] > 0 ? number_format($akun['kredit'], 2, ',', '.') : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-slate-200">
                        <td colspan="2" class="py-4 px-6 font-black text-slate-800 uppercase text-right">Total Keseluruhan</td>
                        <td class="py-4 px-6 text-right font-black text-lg <?= $is_balance ? 'text-emerald-700' : 'text-rose-700' ?>">
                            Rp <?= number_format($total_debit, 2, ',', '.') ?>
                        </td>
                        <td class="py-4 px-6 text-right font-black text-lg <?= $is_balance ? 'text-emerald-700' : 'text-rose-700' ?>">
                            Rp <?= number_format($total_kredit, 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>
