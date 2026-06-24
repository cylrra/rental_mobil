<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

$error = '';
$success = '';

// Ambil ID Pelanggan
if (isset($_GET['id'])) {
    $id_pelanggan = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = $id_pelanggan");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('Data pelanggan tidak ditemukan!'); window.location.href='pelanggan.php';</script>";
        exit();
    }
} else {
    header("Location: pelanggan.php");
    exit();
}

// Proses Update
if (isset($_POST['update'])) {
    $nama              = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email             = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username          = mysqli_real_escape_string($conn, trim($_POST['username']));
    $alamat            = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $no_telp           = mysqli_real_escape_string($conn, trim($_POST['no_telp']));
    $no_ktp            = mysqli_real_escape_string($conn, trim($_POST['no_ktp']));
    $password          = trim($_POST['password']);
    $status_verifikasi = mysqli_real_escape_string($conn, $_POST['status_verifikasi']);

    if (empty($nama) || empty($username)) {
        $error = "Nama Lengkap dan Username wajib diisi!";
    } else {
        // Cek jika username diubah dan duplikat dengan pelanggan lain
        $cek_username = mysqli_query($conn, "SELECT * FROM pelanggan WHERE username = '$username' AND id_pelanggan != $id_pelanggan");
        if (mysqli_num_rows($cek_username) > 0) {
            $error = "Username sudah digunakan oleh pelanggan lain, silakan pilih username lain!";
        } else {
            // Pastikan format nomor telepon (62...)
            $no_telp = preg_replace('/[^0-9]/', '', $no_telp);
            if (strpos($no_telp, '0') === 0) {
                $no_telp = '62' . substr($no_telp, 1);
            } elseif (strpos($no_telp, '8') === 0) {
                $no_telp = '62' . $no_telp;
            }

            // Update Query
            if (!empty($password)) {
                // Update dengan password baru
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $query_update = "UPDATE pelanggan SET 
                                    nama = '$nama', 
                                    email = '$email', 
                                    username = '$username', 
                                    password = '$password_hashed', 
                                    alamat = '$alamat', 
                                    no_telp = '$no_telp', 
                                    no_ktp = '$no_ktp', 
                                    status_verifikasi = '$status_verifikasi' 
                                 WHERE id_pelanggan = $id_pelanggan";
            } else {
                // Update tanpa mengubah password
                $query_update = "UPDATE pelanggan SET 
                                    nama = '$nama', 
                                    email = '$email', 
                                    username = '$username', 
                                    alamat = '$alamat', 
                                    no_telp = '$no_telp', 
                                    no_ktp = '$no_ktp', 
                                    status_verifikasi = '$status_verifikasi' 
                                 WHERE id_pelanggan = $id_pelanggan";
            }

            if (mysqli_query($conn, $query_update)) {
                echo "<script>alert('Data pelanggan $nama berhasil diperbarui!'); window.location.href = 'pelanggan.php';</script>";
                exit();
            } else {
                $error = "Gagal memperbarui data: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="flex items-center justify-center py-10 px-4">
    <div class="glass-card w-full max-w-3xl rounded-xl p-8 md:p-10 border border-slate-200 relative bg-white shadow-md">
        
        <div class="mb-8">
            <a href="pelanggan.php" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-[#800000] transition-colors mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Pelanggan
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-[#800000]/10 rounded-lg flex items-center justify-center text-[#800000]">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                Edit Data Pelanggan
            </h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Perbarui informasi profil, username, password, kontak, dan verifikasi pelanggan.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-rose-50 text-rose-600 p-4 rounded-[8px] text-sm font-semibold mb-6 border border-rose-200">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" value="<?= htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Alamat Email</label>
                    <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" value="<?= htmlspecialchars($data['email']); ?>">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Username Login</label>
                    <input type="text" name="username" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" value="<?= htmlspecialchars($data['username']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Ganti Password (Opsional)</label>
                    <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nomor KTP</label>
                    <input type="number" name="no_ktp" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" value="<?= htmlspecialchars($data['no_ktp']); ?>" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nomor HP / WhatsApp</label>
                    <input type="number" name="no_telp" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" value="<?= htmlspecialchars($data['no_telp']); ?>" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Alamat Tempat Tinggal</label>
                <textarea name="alamat" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" rows="3" required><?= htmlspecialchars($data['alamat']); ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Status Verifikasi</label>
                <select name="status_verifikasi" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-[8px] focus:ring-[#800000] focus:border-[#800000] p-3" required>
                    <option value="belum_verifikasi" <?= ($data['status_verifikasi'] === 'belum_verifikasi') ? 'selected' : ''; ?>>Belum Terverifikasi</option>
                    <option value="terverifikasi" <?= ($data['status_verifikasi'] === 'terverifikasi') ? 'selected' : ''; ?>>Terverifikasi</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="pelanggan.php" class="w-1/2 py-3.5 bg-white border border-slate-200 text-slate-600 font-bold text-sm text-center rounded-[8px] hover:bg-slate-100 transition-colors">
                    Batal
                </a>
                <button type="submit" name="update" class="w-1/2 py-3.5 bg-[#d4af37] text-[#1a1c1c] font-bold text-sm text-center rounded-[8px] shadow-sm hover:bg-[#c49d2b] transition-colors border-none">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

</div> </main>
</div>
<script>
    lucide.createIcons();
</script>
</body>
</html>
