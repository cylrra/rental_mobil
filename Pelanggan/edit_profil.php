<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'koneksi.php';
include 'navbar.php';

$id_pelanggan = $_SESSION['id_pelanggan'];

// 1. Ambil data pelanggan saat ini
$stmt_get = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_get);
$query = mysqli_stmt_get_result($stmt_get);
$data = mysqli_fetch_assoc($query);

$success_msg = '';
$error_msg = '';
$active_tab = 'profile';

// 2. Logic Update Profil
if (isset($_POST['update_profile'])) {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $no_ktp  = mysqli_real_escape_string($conn, $_POST['no_ktp']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);

    $stmt_update = mysqli_prepare($conn, "UPDATE pelanggan SET nama=?, username=?, email=?, no_telp=?, no_ktp=?, alamat=? WHERE id_pelanggan=?");
    mysqli_stmt_bind_param($stmt_update, "ssssssi", $nama, $username, $email, $no_telp, $no_ktp, $alamat, $id_pelanggan);
    $update = mysqli_stmt_execute($stmt_update);

    if ($update) {
        $_SESSION['nama_pelanggan'] = $nama; // Update session name
        $success_msg = "Profil Anda berhasil diperbarui!";
        // Refresh data
        $stmt_get = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?");
        mysqli_stmt_bind_param($stmt_get, "i", $id_pelanggan);
        mysqli_stmt_execute($stmt_get);
        $query = mysqli_stmt_get_result($stmt_get);
        $data = mysqli_fetch_assoc($query);
    } else {
        $error_msg = "Gagal memperbarui profil: " . mysqli_error($conn);
    }
    $active_tab = 'profile';
}

// 3. Logic Ganti Password
if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_msg = "Konfirmasi password baru tidak cocok!";
    } else {
        if (password_verify($old_password, $data['password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_pw = mysqli_prepare($conn, "UPDATE pelanggan SET password=? WHERE id_pelanggan=?");
            mysqli_stmt_bind_param($stmt_pw, "si", $new_password_hash, $id_pelanggan);
            $update_pw = mysqli_stmt_execute($stmt_pw);
            if ($update_pw) {
                $success_msg = "Password Anda berhasil diubah!";
            } else {
                $error_msg = "Gagal mengubah password: " . mysqli_error($conn);
            }
        } else {
            $error_msg = "Password lama Anda salah!";
        }
    }
    $active_tab = 'password';
}

// 4. Logic Unggah Dokumen KTP/SIM
if (isset($_POST['upload_docs'])) {
    $upload_dir = 'img/uploads/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $foto_ktp_name = $data['foto_ktp'];
    $foto_sim_name = $data['foto_sim'];
    $uploaded = false;

    // Handle KTP Upload
    if (!empty($_FILES['foto_ktp']['name'])) {
        $ext = pathinfo($_FILES['foto_ktp']['name'], PATHINFO_EXTENSION);
        $foto_ktp_name = 'ktp_' . $id_pelanggan . '_' . time() . '.' . $ext;
        $target_file = $upload_dir . $foto_ktp_name;
        if (move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $target_file)) {
            $uploaded = true;
        }
    }

    // Handle SIM Upload
    if (!empty($_FILES['foto_sim']['name'])) {
        $ext = pathinfo($_FILES['foto_sim']['name'], PATHINFO_EXTENSION);
        $foto_sim_name = 'sim_' . $id_pelanggan . '_' . time() . '.' . $ext;
        $target_file = $upload_dir . $foto_sim_name;
        if (move_uploaded_file($_FILES['foto_sim']['tmp_name'], $target_file)) {
            $uploaded = true;
        }
    }

    if ($uploaded) {
        // Set status verifikasi ke 'dalam_proses' saat dokumen baru diunggah
        $status_ver = 'dalam_proses';
        $stmt_docs = mysqli_prepare($conn, "UPDATE pelanggan SET foto_ktp=?, foto_sim=?, status_verifikasi=? WHERE id_pelanggan=?");
        mysqli_stmt_bind_param($stmt_docs, "sssi", $foto_ktp_name, $foto_sim_name, $status_ver, $id_pelanggan);
        $update_docs = mysqli_stmt_execute($stmt_docs);
        if ($update_docs) {
            $success_msg = "Dokumen identitas berhasil diunggah! Status diubah menjadi dalam proses review.";
            // Refresh data
            $stmt_get = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?");
            mysqli_stmt_bind_param($stmt_get, "i", $id_pelanggan);
            mysqli_stmt_execute($stmt_get);
            $query = mysqli_stmt_get_result($stmt_get);
            $data = mysqli_fetch_assoc($query);
        } else {
            $error_msg = "Gagal memperbarui data dokumen di database: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Tidak ada dokumen baru yang diunggah.";
    }
    $active_tab = 'verification';
}
?>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12 my-2">
            <h1 class="fw-bold" style="font-family: 'Outfit', sans-serif; color: #0f172a;">Pengaturan Akun</h1>
            <p class="text-muted">Kelola informasi pribadi, ubah kata sandi, dan lakukan verifikasi dokumen identitas.</p>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($success_msg) ?></div>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div><?= htmlspecialchars($error_msg) ?></div>
        </div>
    <?php endif; ?>

    <!-- settings tab navigation -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                <div class="nav flex-column nav-pills gap-2" role="tablist">
                    <button class="nav-link text-start rounded-3 fw-semibold py-2-5 px-3 <?= $active_tab === 'profile' ? 'active bg-primary' : 'text-dark' ?>" 
                            data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                        <i class="bi bi-person-fill me-2"></i> Profil Saya
                    </button>
                    <button class="nav-link text-start rounded-3 fw-semibold py-2-5 px-3 <?= $active_tab === 'password' ? 'active bg-primary' : 'text-dark' ?>" 
                            data-bs-toggle="pill" data-bs-target="#tab-password" type="button" role="tab">
                        <i class="bi bi-key-fill me-2"></i> Ubah Password
                    </button>
                    <button class="nav-link text-start rounded-3 fw-semibold py-2-5 px-3 <?= $active_tab === 'verification' ? 'active bg-primary' : 'text-dark' ?>" 
                            data-bs-toggle="pill" data-bs-target="#tab-verification" type="button" role="tab">
                        <i class="bi bi-shield-lock-fill me-2"></i> Verifikasi Akun
                    </button>
                </div>
            </div>
        </div>

        <!-- tab content panels -->
        <div class="col-lg-9 col-md-8">
            <div class="tab-content">
                
                <!-- Tab Profile Info -->
                <div class="tab-pane fade <?= $active_tab === 'profile' ? 'show active' : '' ?>" id="tab-profile" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person me-2 text-primary"></i> Data Diri Anda</h5>
                        <hr class="my-3 text-muted opacity-25">
                        
                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control py-2-5" value="<?= htmlspecialchars($data['nama'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Username</label>
                                    <input type="text" name="username" class="form-control py-2-5" value="<?= htmlspecialchars($data['username'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                                    <input type="email" name="email" class="form-control py-2-5" value="<?= htmlspecialchars($data['email'] ?? ''); ?>" placeholder="nama@domain.com">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-secondary">No. HP / WhatsApp</label>
                                    <input type="text" name="no_telp" class="form-control py-2-5" value="<?= htmlspecialchars($data['no_telp'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">No. KTP</label>
                                <input type="text" name="no_ktp" class="form-control py-2-5" value="<?= htmlspecialchars($data['no_ktp'] ?? ''); ?>" placeholder="16 digit NIK" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Alamat Rumah Lengkap</label>
                                <textarea name="alamat" rows="3" class="form-control rounded-3" required><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-3 fw-bold px-4 py-2-5">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab Password -->
                <div class="tab-pane fade <?= $active_tab === 'password' ? 'show active' : '' ?>" id="tab-password" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-key me-2 text-primary"></i> Ganti Kata Sandi</h5>
                        <hr class="my-3 text-muted opacity-25">
                        
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Password Lama</label>
                                <input type="password" name="old_password" class="form-control py-2-5" placeholder="Masukkan password lama Anda" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Password Baru</label>
                                <input type="password" name="new_password" class="form-control py-2-5" placeholder="Buat password baru" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control py-2-5" placeholder="Ulangi password baru" required>
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary rounded-3 fw-bold px-4 py-2-5">
                                Ganti Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tab Document Verification -->
                <div class="tab-pane fade <?= $active_tab === 'verification' ? 'show active' : '' ?>" id="tab-verification" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark"><i class="bi bi-shield-check me-2 text-primary"></i> Verifikasi Identitas</h5>
                            <?php 
                            $status_ver = $data['status_verifikasi'] ?? 'belum_verifikasi';
                            if ($status_ver === 'terverifikasi') {
                                echo '<span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi</span>';
                            } elseif ($status_ver === 'dalam_proses') {
                                echo '<span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-clock-history me-1"></i> Dalam Review</span>';
                            } else {
                                echo '<span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Verifikasi</span>';
                            }
                            ?>
                        </div>
                        <p class="text-muted small mb-4">Unggah scan KTP dan SIM A Anda untuk membuka opsi rental **Lepas Kunci (Tanpa Sopir)**.</p>
                        <hr class="my-3 text-muted opacity-25">
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-secondary">Foto KTP Anda</label>
                                    <div class="border rounded-4 p-3 bg-light text-center">
                                        <?php if (!empty($data['foto_ktp']) && file_exists('img/uploads/' . $data['foto_ktp'])): ?>
                                            <img src="img/uploads/<?= htmlspecialchars($data['foto_ktp']) ?>" class="img-fluid rounded-3 mb-2 border shadow-sm" style="max-height: 120px; object-fit: contain;">
                                            <span class="d-block text-success small fw-semibold"><i class="bi bi-check-circle"></i> Sudah Terunggah</span>
                                        <?php else: ?>
                                            <i class="bi bi-card-image fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted small">KTP belum diunggah</span>
                                        <?php endif; ?>
                                        <input type="file" name="foto_ktp" class="form-control form-control-sm mt-3" accept="image/*">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label small fw-bold text-secondary">Foto SIM A Anda</label>
                                    <div class="border rounded-4 p-3 bg-light text-center">
                                        <?php if (!empty($data['foto_sim']) && file_exists('img/uploads/' . $data['foto_sim'])): ?>
                                            <img src="img/uploads/<?= htmlspecialchars($data['foto_sim']) ?>" class="img-fluid rounded-3 mb-2 border shadow-sm" style="max-height: 120px; object-fit: contain;">
                                            <span class="d-block text-success small fw-semibold"><i class="bi bi-check-circle"></i> Sudah Terunggah</span>
                                        <?php else: ?>
                                            <i class="bi bi-card-image fs-1 text-muted d-block mb-2"></i>
                                            <span class="text-muted small">SIM belum diunggah</span>
                                        <?php endif; ?>
                                        <input type="file" name="foto_sim" class="form-control form-control-sm mt-3" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="upload_docs" class="btn btn-primary rounded-3 fw-bold px-4 py-2-5">
                                Unggah & Ajukan Verifikasi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .py-2-5 { padding-top: 0.65rem; padding-bottom: 0.65rem; }
    .nav-pills .nav-link.active {
        background-color: #0f172a !important;
        color: white !important;
    }
</style>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>