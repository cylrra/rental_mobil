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
            /* Dynamic Energy Palette */
            --primary: #9e0000;         /* Bold Crimson */
            --secondary-container: #fdc003; /* Energetic Amber */
            --tertiary: #4d4c4c;        /* Deep Charcoal */
            --background: #f9f9f9;
            --surface: #ffffff;
            --on-surface: #1a1c1c;
            --outline: #926e69;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #f3f3f3 0%, #eeeeee 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            color: var(--on-surface);
        }

        .login-container {
            width: 100%;
            max-width: 500px;
        }

        /* 8px moderate rounded corners and low-contrast outline */
        .login-card {
            background: var(--surface);
            border: 1px solid #e2e2e2;
            border-radius: 8px; /* 8px moderate radius */
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
            padding: 40px 32px;
        }

        .brand-logo-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, #cc0000 100%);
            border-radius: 8px; /* 8px rounding */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 12px rgba(158, 0, 0, 0.2);
        }

        .brand-header {
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--tertiary);
        }
        
        .brand-header span {
            color: var(--primary);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--tertiary);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px; /* 8px rounding */
            padding: 12px 16px;
            border: 1px solid #e2e2e2;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        /* Focus: border thickens to 2px and changes to primary crimson */
        .form-control:focus {
            border: 2px solid var(--primary);
            box-shadow: none;
            outline: none;
        }

        /* Primary CTA uses Energetic Amber with bold dark text */
        .btn-submit {
            background-color: var(--secondary-container);
            color: #1a1c1c;
            border: none;
            border-radius: 8px; /* 8px rounding */
            padding: 14px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(253, 192, 3, 0.15);
        }

        .btn-submit:hover {
            background-color: #e5ad02;
            color: #1a1c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(253, 192, 3, 0.25);
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
                    <p class="small text-muted mb-0 fw-medium">Sudah punya akun? <a href="login_pelanggan.php" class="text-danger fw-bold text-decoration-none" style="color: var(--primary) !important;">Login di Sini</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>