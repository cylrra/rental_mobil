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

// Fungsi untuk menghitung saldo berjalan suatu akun
function getSaldoAkun($conn, $kode_akun, $normal_balance = 'debit') {
    // Ambil saldo awal
    $q_awal = mysqli_query($conn, "SELECT saldo_awal FROM nama_akun WHERE kode_akun = '$kode_akun'");
    $r_awal = mysqli_fetch_assoc($q_awal);
    $saldo_awal = floatval($r_awal['saldo_awal'] ?? 0);

    // Ambil mutasi dari jurnal
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

// Ambil semua akun ASET (1xx)
$aset = [];
$total_aset = 0;
$q_aset = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM nama_akun WHERE kode_akun LIKE '1%' ORDER BY kode_akun ASC");
while ($r = mysqli_fetch_assoc($q_aset)) {
    $saldo = getSaldoAkun($conn, $r['kode_akun'], 'debit');
    if ($saldo != 0) {
        $aset[] = ['kode' => $r['kode_akun'], 'nama' => $r['nama_akun'], 'saldo' => $saldo];
        $total_aset += $saldo;
    }
}

// Ambil semua akun KEWAJIBAN (2xx)
$kewajiban = [];
$total_kewajiban = 0;
$q_kewajiban = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM nama_akun WHERE kode_akun LIKE '2%' ORDER BY kode_akun ASC");
while ($r = mysqli_fetch_assoc($q_kewajiban)) {
    $saldo = getSaldoAkun($conn, $r['kode_akun'], 'kredit');
    if ($saldo != 0) {
        $kewajiban[] = ['kode' => $r['kode_akun'], 'nama' => $r['nama_akun'], 'saldo' => $saldo];
        $total_kewajiban += $saldo;
    }
}

// Ambil semua akun EKUITAS (3xx)
$ekuitas = [];
$total_ekuitas = 0;
$q_ekuitas = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM nama_akun WHERE kode_akun LIKE '3%' ORDER BY kode_akun ASC");
while ($r = mysqli_fetch_assoc($q_ekuitas)) {
    $saldo = getSaldoAkun($conn, $r['kode_akun'], 'kredit');
    // Khusus akun Prive (312), saldo normalnya debit, jadi nilainya mengurangi ekuitas
    if ($r['kode_akun'] == '312') {
        $saldo = getSaldoAkun($conn, $r['kode_akun'], 'debit');
        if ($saldo != 0) {
            $ekuitas[] = ['kode' => $r['kode_akun'], 'nama' => $r['nama_akun'], 'saldo' => -$saldo];
            $total_ekuitas -= $saldo;
        }
    } else {
        if ($saldo != 0) {
            $ekuitas[] = ['kode' => $r['kode_akun'], 'nama' => $r['nama_akun'], 'saldo' => $saldo];
            $total_ekuitas += $saldo;
        }
    }
}

// Hitung Laba Bersih (Pendapatan 4xx - Beban 5xx)
$total_pendapatan = 0;
$q_pendapatan = mysqli_query($conn, "SELECT kode_akun FROM nama_akun WHERE kode_akun LIKE '4%'");
while ($r = mysqli_fetch_assoc($q_pendapatan)) {
    $total_pendapatan += getSaldoAkun($conn, $r['kode_akun'], 'kredit');
}

$total_beban = 0;
$q_beban = mysqli_query($conn, "SELECT kode_akun FROM nama_akun WHERE kode_akun LIKE '5%'");
while ($r = mysqli_fetch_assoc($q_beban)) {
    $total_beban += getSaldoAkun($conn, $r['kode_akun'], 'debit');
}

$laba_bersih = $total_pendapatan - $total_beban;
$total_ekuitas += $laba_bersih;

$total_pasiva = $total_kewajiban + $total_ekuitas;
$is_balance = round($total_aset, 2) == round($total_pasiva, 2);
?>

<div class="p-8">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                    <i data-lucide="scale" class="w-6 h-6"></i>
                </div>
                Laporan Posisi Keuangan (Neraca)
            </h1>
            <p class="text-slate-500 font-medium mt-1">Laporan Aktiva, Kewajiban, dan Ekuitas secara real-time.</p>
        </div>
        <button onclick="window.print()" class="bg-slate-800 text-white font-bold py-2.5 px-5 rounded-xl hover:bg-slate-700 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
        </button>
    </div>

    <!-- Balance Status -->
    <div class="mb-8 p-4 rounded-2xl flex items-center justify-center gap-3 <?= $is_balance ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' ?>">
        <i data-lucide="<?= $is_balance ? 'check-circle' : 'alert-triangle' ?>" class="w-6 h-6"></i>
        <h3 class="text-lg font-black uppercase tracking-widest">
            Status: <?= $is_balance ? 'SEIMBANG (BALANCE)' : 'TIDAK SEIMBANG (UNBALANCED)' ?>
        </h3>
        <?php if (!$is_balance): ?>
            <p class="text-sm font-bold ml-4">Selisih: Rp <?= number_format(abs($total_aset - $total_pasiva), 2, ',', '.') ?></p>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- AKTIVA (ASET) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 bg-blue-50 border-b border-blue-100">
                <h3 class="text-lg font-black text-blue-800 uppercase tracking-wider text-center">Aktiva (Aset)</h3>
            </div>
            <div class="p-6">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                        <?php foreach($aset as $a): ?>
                        <tr>
                            <td class="py-3"><?= $a['kode'] ?> - <?= $a['nama'] ?></td>
                            <td class="py-3 text-right">Rp <?= number_format($a['saldo'], 2, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($aset)): ?>
                        <tr><td colspan="2" class="py-3 text-center text-slate-400 italic">Tidak ada data aset</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200">
                            <td class="py-4 font-black text-slate-800 uppercase text-right pr-4">Total Aktiva</td>
                            <td class="py-4 text-right font-black text-blue-700 text-lg">Rp <?= number_format($total_aset, 2, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- PASIVA (KEWAJIBAN & EKUITAS) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-4 bg-rose-50 border-b border-rose-100">
                    <h3 class="text-lg font-black text-rose-800 uppercase tracking-wider text-center">Pasiva (Kewajiban & Ekuitas)</h3>
                </div>
                <div class="p-6">
                    <h4 class="font-bold text-slate-500 uppercase tracking-wider text-sm mb-2 border-b pb-2">Kewajiban (Utang)</h4>
                    <table class="w-full text-left mb-6">
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            <?php foreach($kewajiban as $k): ?>
                            <tr>
                                <td class="py-2"><?= $k['kode'] ?> - <?= $k['nama'] ?></td>
                                <td class="py-2 text-right">Rp <?= number_format($k['saldo'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($kewajiban)): ?>
                            <tr><td colspan="2" class="py-2 text-center text-slate-400 italic">Tidak ada data utang</td></tr>
                            <?php endif; ?>
                            <tr>
                                <td class="py-2 font-bold text-right pr-4">Total Kewajiban</td>
                                <td class="py-2 text-right font-bold text-slate-800">Rp <?= number_format($total_kewajiban, 2, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <h4 class="font-bold text-slate-500 uppercase tracking-wider text-sm mb-2 border-b pb-2">Ekuitas (Modal)</h4>
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            <?php foreach($ekuitas as $e): ?>
                            <tr>
                                <td class="py-2"><?= $e['kode'] ?> - <?= $e['nama'] ?></td>
                                <td class="py-2 text-right">Rp <?= number_format($e['saldo'], 2, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <!-- Tambahan Laba Bersih Tahun Berjalan -->
                            <tr class="bg-emerald-50 text-emerald-800">
                                <td class="py-2 pl-2">Laba Bersih Berjalan</td>
                                <td class="py-2 pr-2 text-right">Rp <?= number_format($laba_bersih, 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="py-2 font-bold text-right pr-4">Total Ekuitas</td>
                                <td class="py-2 text-right font-bold text-slate-800">Rp <?= number_format($total_ekuitas, 2, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Total Pasiva Footer -->
            <div class="p-6 pt-0 mt-auto">
                <table class="w-full text-left">
                    <tfoot>
                        <tr class="border-t-2 border-slate-200">
                            <td class="py-4 font-black text-slate-800 uppercase text-right pr-4">Total Pasiva</td>
                            <td class="py-4 text-right font-black text-rose-700 text-lg">Rp <?= number_format($total_pasiva, 2, ',', '.') ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>
