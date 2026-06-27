<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php'; // Sesuaikan jika letak koneksi.php Anda berbeda

$error = '';
$success = '';

if (isset($_POST['aktivasi'])) {
    $no_ktp   = trim($_POST['no_ktp']);
    $no_telp  = trim($_POST['no_telp']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($no_ktp) || empty($no_telp) || empty($username) || empty($password)) {
        $error = "Semua kolom wajib diisi untuk verifikasi!";
    } else {
        // 1. Cek apakah username baru sudah dipakai orang lain
        $stmt_cek = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE username = ?");
        mysqli_stmt_bind_param($stmt_cek, "s", $username);
        mysqli_stmt_execute($stmt_cek);
        $cek_username = mysqli_stmt_get_result($stmt_cek);
        if (mysqli_num_rows($cek_username) > 0) {
            $error = "Username sudah digunakan, silakan pilih username lain!";
        } else {
            // 2. Cari data pelanggan berdasarkan No KTP dan No Telp yang COCOK
            $stmt_pelanggan = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE no_ktp = ? AND no_telp = ?");
            mysqli_stmt_bind_param($stmt_pelanggan, "ss", $no_ktp, $no_telp);
            mysqli_stmt_execute($stmt_pelanggan);
            $result = mysqli_stmt_get_result($stmt_pelanggan);

            if (mysqli_num_rows($result) === 1) {
                $data = mysqli_fetch_assoc($result);

                // 3. Cek apakah pelanggan ini sebenarnya sudah punya akun (sudah pernah aktivasi)
                if (!empty($data['username']) || !empty($data['password'])) {
                    $error = "Akun Anda sudah aktif sebelumnya! Silakan langsung login.";
                } else {
                    // 4. Proses update data username dan password terenkripsi hash
                    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                    $id_pelanggan    = $data['id_pelanggan'];

                    $update_query = "UPDATE pelanggan SET username = ?, password = ? WHERE id_pelanggan = ?";
                    $stmt_update = mysqli_prepare($conn, $update_query);
                    mysqli_stmt_bind_param($stmt_update, "ssi", $username, $password_hashed, $id_pelanggan);

                    if (mysqli_stmt_execute($stmt_update)) {
                        $success = "Akun berhasil diaktifkan! Halo " . $data['nama'] . ", silakan login menggunakan username baru Anda.";
                    } else {
                        $error = "Gagal mengaktifkan akun: " . mysqli_error($conn);
                    }
                }
            } else {
                $error = "Data tidak cocok! Pastikan No. KTP dan No. Telepon sesuai dengan yang terdaftar di rental.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Pelanggan - INDOMAX RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #9e0000 0%, #fdc003 100%);
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(158, 0, 0, 0.15);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(120deg, #f9f9f9 0%, #eeeeee 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 40px 0;
        }
        .card-activation {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            width: 100%; max-width: 500px;
        }
        .brand-header {
            font-family: 'Outfit', sans-serif;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-control { border-radius: 12px; padding: 10px 14px; }
        .btn-submit {
            background: var(--primary-gradient); border: none; border-radius: 12px; padding: 12px; font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="container p-3 d-flex justify-content-center">
        <div class="card-activation p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-2"><i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i></div>
                <h3 class="brand-header fw-bold mb-1">AKTIVASI AKUN</h3>
                <p class="text-muted small">Khusus pelanggan lama Indomax yang sudah terdata</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success border-0 rounded-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= $success ?> <a href="login_pelanggan.php" class="alert-link fw-bold text-decoration-none">Login di sini</a></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="bg-light p-3 rounded-4 mb-4 border">
                    <span class="badge bg-primary mb-2">Langkah 1: Verifikasi Data Lama</span>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">No. KTP Terdaftar</label>
                        <input type="text" name="no_ktp" class="form-control" placeholder="Contoh: 3374010101010001" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081234567801" required>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-4 mb-4 border">
                    <span class="badge bg-info text-dark mb-2">Langkah 2: Buat Akses Login</span>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-secondary">Buat Username Baru</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: atarada99" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-secondary">Buat Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Password aman Anda" required>
                    </div>
                </div>

                <button type="submit" name="aktivasi" class="btn btn-primary btn-submit w-100 text-white mb-3">
                    Aktifkan Akun Saya <i class="bi bi-lightning-charge-fill ms-1"></i>
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Bukan pelanggan lama? <a href="register_pelanggan.php" class="text-success fw-bold text-decoration-none">Registrasi Akun Baru</a></p>
                </div>
            </form>
        </div>
    </div>

</body>
</html>