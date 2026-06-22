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
    $email    = trim($_POST['email']);
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
            // Enkripsi password
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert include email & status_verifikasi (default: belum_verifikasi)
            $query = "INSERT INTO pelanggan (nama, username, email, password, alamat, no_telp, no_ktp, status_verifikasi) 
                      VALUES ('$nama', '$username', '$email', '$password_hashed', '$alamat', '$no_telp', '$no_ktp', 'belum_verifikasi')";
            
            if (mysqli_query($conn, $query)) {
                // Redirect to OTP verification screen on login page
                header("Location: login_pelanggan.php?registered=true&user=" . urlencode($username));
                exit();
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #0f172a; /* Slate 900 */
            --accent-blue: #1e3a8a;  /* Deep blue */
            --gradient-blue: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            --grey-light: #f8fafc;
            --grey-hint: #e2e8f0;
            --font-display: 'Outfit', sans-serif;
            --font-sans: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background: radial-gradient(circle at 10% 20%, rgba(30, 58, 138, 0.05) 0%, transparent 80%),
                        linear-gradient(120deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 40px 0;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 15px;
            z-index: 1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        }

        .brand-header {
            font-family: var(--font-display);
            font-weight: 800;
            color: var(--primary-blue);
        }
        .brand-header span {
            color: #2563eb;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 14px;
            border: 1.5px solid rgba(15, 23, 42, 0.1);
            background-color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
        }

        .btn-submit {
            background: var(--gradient-blue);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(30, 58, 138, 0.3);
            color: white;
        }

        .brand-logo-circle {
            width: 70px;
            height: 70px;
            background: var(--gradient-blue);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 16px rgba(30, 58, 138, 0.2);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="brand-logo-circle">
                    <i class="bi bi-person-plus-fill text-white fs-2"></i>
                </div>
                <h3 class="brand-header fw-bold mb-1">REGISTRASI PELANGGAN</h3>
                <p class="text-muted small">Buat akun untuk mulai sewa mobil di INDOMAX</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap sesuai KTP" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Buat username unik" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Contoh: nama@domain.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">No. KTP</label>
                    <input type="text" name="no_ktp" class="form-control" placeholder="16 digit nomor induk KTP" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">No. Telepon / WhatsApp</label>
                    <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 0812345678" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat tinggal saat ini" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat password minimal 6 karakter" required>
                </div>

                <button type="submit" name="register" class="btn btn-submit w-100 mb-3">
                    Daftar Akun <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>

                <div class="text-center">
                    <p class="small text-muted mb-0">Sudah punya akun? <a href="login_pelanggan.php" class="text-primary fw-bold text-decoration-none">Login di Sini</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>