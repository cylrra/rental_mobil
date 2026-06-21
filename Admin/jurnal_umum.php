<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';

if (isset($_POST['simpan_jurnal'])) {
    // Menggunakan trim() untuk menghindari spasi tidak sengaja
    $tanggal    = mysqli_real_escape_string($conn, trim($_POST['tanggal']));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan']));
    
    $akun_debit   = mysqli_real_escape_string($conn, trim($_POST['akun_debit']));
    $nominal_debit = floatval($_POST['nominal_debit']);
    
    $akun_kredit   = mysqli_real_escape_string($conn, trim($_POST['akun_kredit']));
    $nominal_kredit = floatval($_POST['nominal_kredit']);

    if ($nominal_debit !== $nominal_kredit) {
        echo "<script>alert('Error: Jurnal tidak seimbang! Nilai Debit & Kredit harus sama.'); window.history.back();</script>";
        exit();
    }

    mysqli_begin_transaction($conn);
    try {
        // PERBAIKAN 1: Mengubah nama tabel insert ke 'jurnal' sesuai struktur gambar kedua kamu
        
        // 1. Catat Sisi Debit
        $query_debit = "INSERT INTO jurnal (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                        VALUES ('$tanggal', '$akun_debit', '$nominal_debit', 0, '$keterangan', 1)";
        if (!mysqli_query($conn, $query_debit)) {
            throw new Exception(mysqli_error($conn));
        }

        // 2. Catat Sisi Kredit
        $query_kredit = "INSERT INTO jurnal (tanggal, kode_akun, debit, kredit, keterangan, id_sumber) 
                         VALUES ('$tanggal', '$akun_kredit', 0, '$nominal_kredit', '$keterangan', 1)";
        if (!mysqli_query($conn, $query_kredit)) {
            throw new Exception(mysqli_error($conn));
        }

        mysqli_commit($conn);
        echo "<script>alert('Jurnal Umum Berhasil Dibukukan!'); window.location='jurnal_detail.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
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
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover-lift">
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
                            <input type="date" name="tanggal" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-slate-50" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan Transaksi</label>
                            <input type="text" name="keterangan" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-slate-50" placeholder="Contoh: Pembelian bensin" required>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-xl border-l-4 border-l-emerald-500 border border-y-slate-200 border-r-slate-200">
                        <label class="block text-sm font-bold text-emerald-600 mb-2">Akun Sisi DEBIT</label>
                        <select name="akun_debit" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-white mb-4" required>
                            <option value="">-- Pilih Akun Debit --</option>
                            <?php
                            // PERBAIKAN 2: Mengubah nama tabel master menjadi 'akun' sesuai gambar pertama kamu
                            $coas = mysqli_query($conn, "SELECT kode_akun, nama_akun FROM akun ORDER BY kode_akun ASC");
                            while($c = mysqli_fetch_assoc($coas)) {
                                echo "<option value='{$c['kode_akun']}'>{$c['kode_akun']} - {$c['nama_akun']}</option>";
                            }
                            ?>
                        </select>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-emerald-600">Rp</span>
                            <input type="number" name="nominal_debit" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-white font-bold text-emerald-700" placeholder="Masukkan Nominal Rp" min="1" required>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-xl border-l-4 border-l-rose-500 border border-y-slate-200 border-r-slate-200">
                        <label class="block text-sm font-bold text-rose-600 mb-2">Akun Sisi KREDIT</label>
                        <select name="akun_kredit" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-white mb-4" required>
                            <option value="">-- Pilih Akun Kredit --</option>
                            <?php
                            mysqli_data_seek($coas, 0);
                            while($c = mysqli_fetch_assoc($coas)) {
                                echo "<option value='{$c['kode_akun']}'>{$c['kode_akun']} - {$c['nama_akun']}</option>";
                            }
                            ?>
                        </select>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-rose-600">Rp</span>
                            <input type="number" name="nominal_kredit" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-white font-bold text-rose-700" placeholder="Masukkan Nominal Rp" min="1" required>
                        </div>
                    </div>

                    <button type="submit" name="simpan_jurnal" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md shadow-blue-600/20 hover:bg-blue-700 transition-colors flex justify-center items-center gap-2 mt-4">
                        <i data-lucide="check-circle" class="w-5 h-5"></i> Simpan Pembukuan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</main> 
</div> 
<script>lucide.createIcons();</script>
</body>
</html>