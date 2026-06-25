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

$error = '';

if (isset($_GET['kode'])) {
    $kode_mobil = mysqli_real_escape_string($conn, $_GET['kode']);
    $query = mysqli_query($conn, "SELECT * FROM mobil WHERE kode_mobil = '$kode_mobil'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('Data mobil tidak ditemukan!'); window.location.href='mobil.php';</script>";
        exit();
    }
} else {
    header("Location: mobil.php");
    exit();
}

if (isset($_POST['update'])) {
    $merk           = mysqli_real_escape_string($conn, trim($_POST['merk']));
    $jenis          = mysqli_real_escape_string($conn, trim($_POST['jenis']));
    $nopol          = mysqli_real_escape_string($conn, trim($_POST['nopol']));
    $tarif_12_dalam = mysqli_real_escape_string($conn, trim($_POST['tarif_12_dalam']));
    $tarif_12_luar  = mysqli_real_escape_string($conn, trim($_POST['tarif_12_luar']));
    $tarif_24_dalam = mysqli_real_escape_string($conn, trim($_POST['tarif_24_dalam']));
    $tarif_24_luar  = mysqli_real_escape_string($conn, trim($_POST['tarif_24_luar']));
    $tarif_per_hari = mysqli_real_escape_string($conn, trim($_POST['tarif_per_hari']));
    $Unit_Tersedia  = mysqli_real_escape_string($conn, trim($_POST['Unit_Tersedia']));
    $status_mobil   = mysqli_real_escape_string($conn, $_POST['status_mobil']);
    
    $nama_gambar_baru = $_FILES['gambar']['name'];
    $tmp_gambar       = $_FILES['gambar']['tmp_name'];
    
    if (!empty($nama_gambar_baru)) {
        $ext = pathinfo($nama_gambar_baru, PATHINFO_EXTENSION);
        $nama_gambar_siap = time() . '_edit_' . str_replace(' ', '_', $merk) . '.' . $ext;
        $path_simpan = "img/" . $nama_gambar_siap;

        if (!empty($data['Gambar']) && file_exists("img/" . $data['Gambar'])) {
            unlink("img/" . $data['Gambar']);
        }
        move_uploaded_file($tmp_gambar, $path_simpan);
    } else {
        $nama_gambar_siap = $data['Gambar'];
    }

    $query_update = "UPDATE mobil SET 
                        merk = '$merk', 
                        jenis = '$jenis', 
                        nopol = '$nopol', 
                        tarif_12_dalam = '$tarif_12_dalam',
                        tarif_12_luar = '$tarif_12_luar',
                        tarif_24_dalam = '$tarif_24_dalam',
                        tarif_24_luar = '$tarif_24_luar',
                        tarif_per_hari = '$tarif_per_hari',
                        Unit_Tersedia = '$Unit_Tersedia',
                        status_mobil = '$status_mobil', 
                        Gambar = '$nama_gambar_siap' 
                     WHERE kode_mobil = '$kode_mobil'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Data armada $merk berhasil diperbarui!'); window.location.href = 'mobil.php';</script>";
        exit();
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<div class="flex items-center justify-center py-10 px-4">
    <div class="glass-card w-full max-w-3xl rounded-3xl p-8 md:p-10 border border-slate-200 relative">
        
        <div class="mb-8">
            <a href="mobil.php" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-brand-500 transition-colors mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Armada
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500"><i data-lucide="edit-3" class="w-5 h-5"></i></div>
                Edit Data Armada
            </h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Perbarui informasi teknis, tarif, dan status armada kendaraan ini.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl text-sm font-medium mb-6"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Kode Mobil</label>
                    <input type="text" class="w-full bg-slate-100 border border-slate-200 text-slate-500 text-sm rounded-xl p-3 cursor-not-allowed" value="<?= htmlspecialchars($data['kode_mobil']); ?>" disabled>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Plat Nomor (Nopol)</label>
                    <input type="text" name="nopol" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['nopol']); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Merk / Nama Mobil</label>
                    <input type="text" name="merk" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['merk']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Jenis Kendaraan</label>
                    <input type="text" name="jenis" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['jenis']); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 12 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_12_dalam" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['tarif_12_dalam']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 12 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_12_luar" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['tarif_12_luar']); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 24 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_24_dalam" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['tarif_24_dalam']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 24 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_24_luar" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['tarif_24_luar']); ?>" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif Harian Lama (Opsional)</label>
                    <input type="number" name="tarif_per_hari" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['tarif_per_hari']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Total Unit / Stok</label>
                    <input type="number" name="Unit_Tersedia" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="<?= htmlspecialchars($data['Unit_Tersedia'] ?? 1); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Status Fisik</label>
                    <select name="status_mobil" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                        <option value="tersedia" <?= ($data['status_mobil'] === 'tersedia') ? 'selected' : ''; ?>>Aktif (Tersedia)</option>
                        <option value="tidak tersedia" <?= ($data['status_mobil'] === 'tidak tersedia') ? 'selected' : ''; ?>>Non-aktif (Rusak/Service)</option>
                    </select>
                </div>
            </div>

            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex flex-col md:flex-row items-center gap-6 mt-4">
                <div class="w-40 h-28 bg-white border border-slate-200 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <?php if (!empty($data['Gambar']) && file_exists("img/" . $data['Gambar'])): ?>
                        <img src="img/<?= htmlspecialchars($data['Gambar']); ?>" class="w-full h-full object-cover" id="imgView">
                    <?php else: ?>
                        <span class="text-xs text-slate-400 font-medium">Kosong</span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Ganti Foto Kendaraan</label>
                    <input type="file" name="gambar" id="imgInput" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 mb-2" accept="image/*">
                    <p class="text-[10px] text-slate-400 italic">*Biarkan kosong jika Anda tidak ingin merubah foto lama.</p>
                </div>
            </div>

            <button type="submit" name="update" class="w-full py-3.5 bg-brand-500 text-white font-bold text-sm text-center rounded-xl shadow-md shadow-brand-500/30 hover:bg-brand-600 transition-all mt-4">
                Simpan Perubahan Data
            </button>
        </form>
    </div>
</div>

        </div> </main>
</div>
<script>
    lucide.createIcons();
    // Preview Gambar Otomatis
    document.getElementById('imgInput').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            let imgView = document.getElementById('imgView');
            if(!imgView) {
                // Jika sebelumnya tidak ada gambar, kita buat elemen gambarnya
                const container = this.closest('.flex').querySelector('.w-40');
                container.innerHTML = '<img src="" class="w-full h-full object-cover" id="imgView">';
                imgView = document.getElementById('imgView');
            }
            imgView.src = URL.createObjectURL(file);
        }
    }
</script>
</body>
</html>