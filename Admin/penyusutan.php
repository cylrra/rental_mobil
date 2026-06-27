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

// Proses Simpan Penyusutan
if (isset($_POST['simpan_penyusutan'])) {
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    $nominal = floatval($_POST['nominal']);
    $keterangan = "Beban Penyusutan Kendaraan Bulan " . date('F Y', strtotime($bulan . '-01'));
    $tanggal_input = date('Y-m-t', strtotime($bulan . '-01')); // Akhir bulan
    
    // Validasi apakah bulan ini sudah ada penyusutan
    $cek = mysqli_query($conn, "SELECT id_jurnal FROM jurnal WHERE kode_akun = '514' AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Penyusutan untuk bulan ini sudah pernah dicatat!'); window.history.back();</script>";
        exit;
    }

    mysqli_begin_transaction($conn);
    try {
        // Debit: Beban Penyusutan Kendaraan (514)
        $q_debit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                    VALUES ('$tanggal_input', '$keterangan', '514', '$nominal', 0, 999)";
        if (!mysqli_query($conn, $q_debit)) throw new Exception("Gagal debit 514");

        // Kredit: Akumulasi Penyusutan Kendaraan (122)
        $q_kredit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                     VALUES ('$tanggal_input', '$keterangan', '122', 0, '$nominal', 999)";
        if (!mysqli_query($conn, $q_kredit)) throw new Exception("Gagal kredit 122");

        mysqli_commit($conn);
        echo "<script>alert('Beban Penyusutan berhasil dicatat ke Jurnal Umum!'); window.location.href='penyusutan.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Terjadi kesalahan: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}

// Ambil Riwayat Penyusutan
$riwayat = mysqli_query($conn, "SELECT tanggal, keterangan, Debit as nominal FROM jurnal WHERE kode_akun = '514' ORDER BY tanggal DESC");
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600">
                <i data-lucide="trending-down" class="w-6 h-6"></i>
            </div>
            Penyusutan Kendaraan
        </h1>
        <p class="text-slate-500 font-medium mt-1">Catat nilai depresiasi (penurunan nilai) armada mobil per bulan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Input -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h4 class="font-bold text-slate-800 mb-4 border-b pb-2 flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4 text-orange-500"></i> Form Catat Penyusutan
                </h4>
                <div class="bg-orange-50 border border-orange-100 text-orange-800 p-3 rounded-lg text-sm mb-4">
                    Pencatatan ini akan otomatis masuk ke <strong>Jurnal Umum</strong>: <br>
                    (D) 514 Beban Penyusutan<br>
                    (K) 122 Akum. Penyusutan
                </div>
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bulan & Tahun</label>
                        <input type="month" name="bulan" class="w-full rounded-xl border-slate-200 px-4 py-2.5 bg-slate-50 focus:ring-orange-500" value="<?= date('Y-m') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nominal Penyusutan (Rp)</label>
                        <input type="number" name="nominal" class="w-full rounded-xl border-slate-200 px-4 py-2.5 bg-slate-50 focus:ring-orange-500" placeholder="Contoh: 1500000" min="1" required>
                    </div>
                    <button type="submit" name="simpan_penyusutan" class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl shadow-md hover:bg-orange-700 transition-colors flex justify-center items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan ke Jurnal
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5 text-slate-500"></i>
                    <h4 class="font-bold text-slate-800">Riwayat Pencatatan Penyusutan</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-bold">
                                <th class="p-4">Tanggal Jurnal</th>
                                <th class="p-4">Keterangan</th>
                                <th class="p-4 text-right">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if(mysqli_num_rows($riwayat) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($riwayat)): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-4 font-medium text-slate-600"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="p-4 text-slate-700"><?= htmlspecialchars($row['keterangan']) ?></td>
                                    <td class="p-4 text-right font-bold text-orange-600">Rp <?= number_format($row['nominal'], 2, ',', '.') ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="p-8 text-center text-slate-400 italic">Belum ada riwayat pencatatan penyusutan kendaraan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</main></div>
<script>lucide.createIcons();</script>
</body>
</html>
