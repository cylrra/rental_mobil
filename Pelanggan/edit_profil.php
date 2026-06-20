<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['id_pelanggan'])) {
    header("Location: login_pelanggan.php");
    exit();
}

$id_pelanggan = $_SESSION['id_pelanggan'];

// Ambil data pelanggan saat ini
$query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id_pelanggan'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nohp   = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    $update = mysqli_query($koneksi, "UPDATE pelanggan SET nama='$nama', email='$email', nohp='$nohp', alamat='$alamat' WHERE id_pelanggan='$id_pelanggan'");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='edit_profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Profil - PT INDOMAX RENTAL</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container" style="padding: 20px; max-width: 600px; margin: auto;">
        <h2>Edit Profil Saya</h2>
        <form action="" method="POST">
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $data['nama']; ?>" required style="width: 100%; padding: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Email</label>
                <input type="email" name="email" value="<?= $data['email']; ?>" required style="width: 100%; padding: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>No. HP / WhatsApp</label>
                <input type="text" name="nohp" value="<?= $data['nohp']; ?>" required style="width: 100%; padding: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Alamat</label>
                <textarea name="alamat" required style="width: 100%; padding: 8px; height: 100px;"><?= $data['alamat']; ?></textarea>
            </div>
            <button type="submit" name="update" style="background: #e74c3c; color: white; padding: 10px 20px; border: none; cursor: pointer;">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>