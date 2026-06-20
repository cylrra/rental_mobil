<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $alamat   = trim($_POST['alamat']);
    $no_telp  = trim($_POST['no_telp']);
    $no_ktp   = trim($_POST['no_ktp']);
    $password = trim($_POST['password']);

    if (empty($nama) || empty($username) || empty($password)) {
        $error = "Nama, Username, dan Password wajib diisi!";
    } else {
        // Cek apakah username sudah terdaftar
        $cek_user = mysqli_query($conn, "SELECT * FROM pelanggan WHERE username = '$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $error = "Username sudah digunakan, pilih username lain!";
        } else {
            // Enkripsi password aman
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Query disesuaikan dengan struktur tabel di gambar Anda
            $query = "INSERT INTO pelanggan (nama, username, password, alamat, no_telp, no_ktp) 
                      VALUES ('$nama', '$username', '$password_hashed', '$alamat', '$no_telp', '$no_ktp')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Registrasi Berhasil! Silakan login.";
            } else {
                $error = "Gagal melakukan registrasi: " . mysqli_error($conn);
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
    <title>Daftar Pelanggan - INDOMAX RENTAL MOBIL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #198754 0%, #20c997 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --font-display: 'Outfit', sans-serif;
            --font-sans: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background: radial-gradient(circle at 10% 20%, rgba(216, 241, 230, 0.46) 0.1%, rgba(233, 226, 226, 0.28) 90.1%),
                        linear-gradient(120deg, #fdfbf7 0%, #f0fcf7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
            padding: 40px 0;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: var(--primary-gradient);
            filter: blur(80px);
            opacity: 0.12;
            z-index: 0;
        }
        .bubble-1 { width: 350px; height: 350px; top: -10%; left: -10%; }
        .bubble-2 { width: 400px; height: 400px; bottom: -15%; right: -5%; }

        .login-container { z-index: 1; width: 100%; max-width: 500px; padding: 15px; }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }

        .brand-header {
            font-family: var(--font-display);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 14px;
            border: 1.5px solid rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.15);
        }

        .btn-submit {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(25, 135, 84, 0.25);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(25, 135, 84, 0.35);
        }

        .brand-logo-circle {
            width: 70px; height: 70px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            transform: rotate(-5deg);
        }
    </style>
</head>
<body>

    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>

    <div class="login-container">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-logo-circle">
                    <i class="bi bi-person-plus-fill text-white fs-1"></i>
                </div>
                <h3 class="brand-header fw-bold mb-1">REGISTRASI PELANGGAN</h3>
                <p class="text-muted small">Silakan buat akun Indomax Rental Anda</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= $success ?> <a href="login_pelanggan.php" class="alert-link">Login di sini</a></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Buat username untuk login" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">No. KTP</label>
                    <input type="text" name="no_ktp" class="form-control" placeholder="Masukkan 16 digit No. KTP">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                    <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 081234567xx">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat tempat tinggal saat ini"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat password aman" required>
                </div>

                <button type="submit" name="register" class="btn btn-success w-100 btn-submit text-white mb-3">
                    Daftar Sekarang <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Sudah punya akun? <a href="login_pelanggan.php" class="text-success fw-bold text-decoration-none">Login Pelanggan</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>