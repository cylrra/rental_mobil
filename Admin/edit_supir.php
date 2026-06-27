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
$success = '';

if (isset($_GET['id'])) {
    $id_supir = mysqli_real_escape_string($conn, $_GET['id']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM supir WHERE id_supir = ?");
    mysqli_stmt_bind_param($stmt, "s", $id_supir);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('Data supir tidak ditemukan!'); window.location.href='supir.php';</script>";
        exit();
    }
} else {
    header("Location: supir.php");
    exit();
}

if (isset($_POST['update'])) {
    $nama_supir           = mysqli_real_escape_string($conn, trim($_POST['nama_supir']));
    $no_telp              = mysqli_real_escape_string($conn, trim($_POST['no_telp']));
    $tarif_12_dalam       = mysqli_real_escape_string($conn, trim($_POST['tarif_12_dalam']));
    $tarif_12_luar        = mysqli_real_escape_string($conn, trim($_POST['tarif_12_luar']));
    $tarif_24_dalam       = mysqli_real_escape_string($conn, trim($_POST['tarif_24_dalam']));
    $tarif_24_luar        = mysqli_real_escape_string($conn, trim($_POST['tarif_24_luar']));
    $tarif_supir_per_hari = mysqli_real_escape_string($conn, trim($_POST['tarif_supir_per_hari']));
    $status_supir         = mysqli_real_escape_string($conn, $_POST['status_supir']);
    
    $nama_gambar_baru = $_FILES['gambar']['name'];
    $tmp_gambar       = $_FILES['gambar']['tmp_name'];
    
    // Default gambar ke yang lama
    $nama_gambar_siap = $data['gambar'];

    // Update process
    $stmt_update = mysqli_prepare($conn, "UPDATE supir SET 
                        nama_supir = ?, no_telp = ?, tarif_12_dalam = ?, tarif_12_luar = ?, 
                        tarif_24_dalam = ?, tarif_24_luar = ?, tarif_supir_per_hari = ?, status_supir = ?
                     WHERE id_supir = ?");
    mysqli_stmt_bind_param($stmt_update, "ssdddddss", $nama_supir, $no_telp, $tarif_12_dalam, $tarif_12_luar, $tarif_24_dalam, $tarif_24_luar, $tarif_supir_per_hari, $status_supir, $id_supir);

    if (mysqli_stmt_execute($stmt_update)) {
        // Jika ada gambar baru diunggah
        if (!empty($nama_gambar_baru)) {
            $ext = strtolower(pathinfo($nama_gambar_baru, PATHINFO_EXTENSION));
            $valid_extensions = array("jpg", "jpeg", "png", "gif");
            
            // Check extension
            if (!in_array($ext, $valid_extensions)) {
                echo "<script>alert('Format file tidak didukung! Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.'); window.history.back();</script>";
                exit();
            }
            
            // Check MIME type using getimagesize
            $image_info = @getimagesize($tmp_gambar);
            if ($image_info === false) {
                echo "<script>alert('File bukan merupakan gambar yang valid!'); window.history.back();</script>";
                exit();
            }

            $nama_gambar_siap = time() . '_supir_' . str_replace(' ', '_', $nama_supir) . '.' . $ext;
            $path_simpan = "img_supir/" . $nama_gambar_siap;
    
            if (!empty($data['gambar']) && file_exists("img_supir/" . $data['gambar'])) {
                unlink("img_supir/" . $data['gambar']);
            }
            
            if (move_uploaded_file($tmp_gambar, $path_simpan)) {
                $stmt_img = mysqli_prepare($conn, "UPDATE supir SET gambar = ? WHERE id_supir = ?");
                mysqli_stmt_bind_param($stmt_img, "ss", $nama_gambar_siap, $id_supir);
                mysqli_stmt_execute($stmt_img);
            }
        }

        echo "<script>alert('Data supir $nama_supir berhasil diperbarui!'); window.location.href = 'supir.php';</script>";
        exit();
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<div class="flex items-center justify-center py-10 px-4">
    <div class="glass-card w-full max-w-3xl rounded-3xl p-8 md:p-10 border border-slate-200 relative shadow-sm bg-white">
        
        <div class="mb-8">
            <a href="supir.php" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-500 transition-colors mb-4 text-decoration-none">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Supir
            </a>
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3 mt-2">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 p-2 d-inline-flex"><i class="bi bi-pencil-square fs-5"></i></div>
                Edit Data Supir
            </h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Perbarui informasi personal, tarif, dan status supir ini.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger p-3 rounded-3 text-sm font-medium mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">ID Supir</label>
                    <input type="text" class="form-control bg-light border-0" value="<?= htmlspecialchars($data['id_supir']); ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Nama Lengkap</label>
                    <input type="text" name="nama_supir" class="form-control" value="<?= htmlspecialchars($data['nama_supir']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">No. Telepon / WA</label>
                    <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($data['no_telp']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Status Supir</label>
                    <select name="status_supir" class="form-select" required>
                        <option value="tersedia" <?= ($data['status_supir'] === 'tersedia') ? 'selected' : ''; ?>>Aktif (Tersedia)</option>
                        <option value="bertugas" <?= ($data['status_supir'] === 'bertugas') ? 'selected' : ''; ?>>Bertugas / Tidak Tersedia</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Tarif 12 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_12_dalam" class="form-control" value="<?= htmlspecialchars($data['tarif_12_dalam']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Tarif 12 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_12_luar" class="form-control" value="<?= htmlspecialchars($data['tarif_12_luar']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Tarif 24 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_24_dalam" class="form-control" value="<?= htmlspecialchars($data['tarif_24_dalam']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Tarif 24 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_24_luar" class="form-control" value="<?= htmlspecialchars($data['tarif_24_luar']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Tarif Harian Lama (Opsional)</label>
                    <input type="number" name="tarif_supir_per_hari" class="form-control" value="<?= htmlspecialchars($data['tarif_supir_per_hari']); ?>">
                </div>
            </div>

            <div class="bg-light p-4 rounded-3 border d-flex flex-column flex-md-row align-items-center gap-4 mt-4">
                <div class="bg-white border rounded-3 overflow-hidden d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; flex-shrink: 0;">
                    <?php if (!empty($data['gambar']) && file_exists("img_supir/" . $data['gambar'])): ?>
                        <img src="img_supir/<?= htmlspecialchars($data['gambar']); ?>" class="w-100 h-100 object-fit-cover" id="imgView">
                    <?php else: ?>
                        <span class="text-secondary small fw-medium" id="imgViewPlaceholder">Kosong</span>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1 w-100">
                    <label class="form-label text-xs fw-bold text-secondary text-uppercase mb-2">Ganti Foto Supir</label>
                    <input type="file" name="gambar" id="imgInput" class="form-control mb-2" accept="image/*">
                    <p class="text-muted small mb-0 fst-italic">*Biarkan kosong jika Anda tidak ingin merubah foto lama.</p>
                </div>
            </div>

            <button type="submit" name="update" class="btn btn-primary w-100 py-3 mt-4 fw-bold rounded-pill">
                <i class="bi bi-save me-2"></i>Simpan Perubahan Data
            </button>
        </form>
    </div>
</div>

</div> </main>
</div>
<script>
    // Preview Gambar Otomatis
    document.getElementById('imgInput').onchange = function (evt) {
        const [file] = this.files;
        if (file) {
            let imgView = document.getElementById('imgView');
            let imgPlaceholder = document.getElementById('imgViewPlaceholder');
            
            if(!imgView) {
                // Jika sebelumnya tidak ada gambar, kita buat elemen gambarnya
                const container = this.closest('.d-flex').querySelector('.bg-white');
                container.innerHTML = '<img src="" class="w-100 h-100 object-fit-cover" id="imgView">';
                imgView = document.getElementById('imgView');
            } else if (imgPlaceholder) {
                imgPlaceholder.style.display = 'none';
            }
            imgView.src = URL.createObjectURL(file);
        }
    }
</script>
</body>
</html>
