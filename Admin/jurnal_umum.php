<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';

$db_connection = $conn ?? $koneksi ?? $db ?? $link;

if (!$db_connection) {
    die("<div style='color:red; padding:20px; border:2px solid red; background:#fff5f5; font-family:sans-serif;'>
            <h3>[ERROR KONEKSI]</h3>
            <p>Sistem tidak menemukan variabel koneksi database Anda.</p>
         </div>");
}

if (isset($_POST['simpan_jurnal'])) {
    $tanggal    = mysqli_real_escape_string($db_connection, trim($_POST['tanggal']));
    $keterangan = mysqli_real_escape_string($db_connection, trim($_POST['keterangan']));
    
    $akun_debit   = mysqli_real_escape_string($db_connection, trim($_POST['akun_debit']));
    $nominal_debit = floatval($_POST['nominal_debit']);
    
    $akun_kredit   = mysqli_real_escape_string($db_connection, trim($_POST['akun_kredit']));
    $nominal_kredit = floatval($_POST['nominal_kredit']);

    if ($nominal_debit <= 0 || $nominal_kredit <= 0) {
        echo "<script>alert('Error: Nominal input harus lebih besar dari 0!'); window.history.back();</script>";
        exit();
    }

    if ($nominal_debit !== $nominal_kredit) {
        echo "<script>alert('Error: Jurnal tidak seimbang! Nilai Debit & Kredit harus sama.'); window.history.back();</script>";
        exit();
    }

    mysqli_begin_transaction($db_connection);
    try {
        // Sisi Debit: Kolom debit diisi nominal, kolom kredit diisi 0
        $query_debit = "INSERT INTO jurnal (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                        VALUES ('$tanggal', '$akun_debit', '$nominal_debit', 0, '$keterangan', 1)";
        if (!mysqli_query($db_connection, $query_debit)) {
            throw new Exception(mysqli_error($db_connection));
        }

        // Sisi Kredit: Kolom debit diisi 0, kolom kredit diisi nominal
        $query_kredit = "INSERT INTO jurnal (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                         VALUES ('$tanggal', '$akun_kredit', 0, '$nominal_kredit', '$keterangan', 1)";
        if (!mysqli_query($db_connection, $query_kredit)) {
            throw new Exception(mysqli_error($db_connection));
        }

        mysqli_commit($db_connection);
        echo "<script>alert('Jurnal Umum Berhasil Dibukukan!'); window.location.href='riwayat_jurnal_umum.php';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($db_connection);
        echo "<script>alert('Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

$daftar_akun = [];
$query_coa = mysqli_query($db_connection, "SELECT kode_akun, nama_akun FROM nama_akun ORDER BY kode_akun ASC");
if ($query_coa) {
    while ($row = mysqli_fetch_assoc($query_coa)) {
        $daftar_akun[] = ['kode_akun' => $row['kode_akun'], 'nama_akun' => $row['nama_akun']];
    }
}
?>

<div class="p-8">
    <div class="mb-8 text-center max-w-2xl mx-auto">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Jurnal Umum</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Catat transaksi keuangan secara manual ke dalam buku besar akuntansi.</p>
    </div>

    <div class="flex justify-center">
        <div class="w-full lg:w-1/2">
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-800">Formulir Jurnal Baru</h4>
                        <p class="text-sm text-slate-500 font-medium">Pastikan sisi Debit dan Kredit bernilai sama</p>
                    </div>
                </div>
                
                <form action="" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-slate-50" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan Transaksi</label>
                            <input type="text" name="keterangan" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-slate-50" placeholder="Contoh: Pembelian bensin" required>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-xl border-l-4 border-l-emerald-500 border border-slate-200">
                        <label class="block text-sm font-bold text-emerald-600 mb-2">Akun Sisi DEBIT</label>
                        <select name="akun_debit" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white mb-4" required>
                            <option value="">-- Pilih Akun Debit --</option>
                            <?php foreach ($daftar_akun as $akun) { echo "<option value='{$akun['kode_akun']}'>{$akun['kode_akun']} - {$akun['nama_akun']}</option>"; } ?>
                        </select>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-emerald-600">Rp</span>
                            <input type="number" name="nominal_debit" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 bg-white font-bold text-emerald-700" placeholder="Masukkan Nominal Rp" min="1" required>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-xl border-l-4 border-l-rose-500 border border-slate-200">
                        <label class="block text-sm font-bold text-rose-600 mb-2">Akun Sisi KREDIT</label>
                        <select name="akun_kredit" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white mb-4" required>
                            <option value="">-- Pilih Akun Kredit --</option>
                            <?php foreach ($daftar_akun as $akun) { echo "<option value='{$akun['kode_akun']}'>{$akun['kode_akun']} - {$akun['nama_akun']}</option>"; } ?>
                        </select>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-rose-600">Rp</span>
                            <input type="number" name="nominal_kredit" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 bg-white font-bold text-rose-700" placeholder="Masukkan Nominal Rp" min="1" required>
                        </div>
                    </div>

                    <button type="submit" name="simpan_jurnal" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md hover:bg-blue-700 transition-colors flex justify-center items-center gap-2 mt-4">
                        <i data-lucide="check-circle" class="w-5 h-5"></i> Simpan Pembukuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>