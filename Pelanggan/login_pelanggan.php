<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$error = '';
$show_otp = false;
$temp_username = '';

// Check if redirected after registration to show OTP verification
if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
    $show_otp = true;
    $temp_username = $_GET['user'] ?? '';
}

if (isset($_POST['verify_otp'])) {
    $otp_code = implode('', $_POST['otp']);
    if ($otp_code === '123456') { // Simulated correct OTP
        $success_msg = "Akun berhasil diaktifkan/diverifikasi! Silakan login.";
        $show_otp = false;
    } else {
        $error = "Kode OTP salah! Gunakan kode simulasi: 123456";
        $show_otp = true;
        $temp_username = $_POST['temp_username'];
    }
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $username = mysqli_real_escape_string($conn, $username);
        $query = "SELECT * FROM pelanggan WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $row['password'])) {
                // Check if account has KTP/SIM
                $_SESSION['role'] = 'pelanggan';
                $_SESSION['id_pelanggan'] = $row['id_pelanggan'];
                $_SESSION['nama_pelanggan'] = $row['nama'];
                
                header("Location: index.php"); 
                exit();
            } else {
                $error = "Password pelanggan salah!";
            }
        } else {
            $error = "Username tidak terdaftar!";
        }
    } else {
        $error = "Username dan password wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - INDOMAX RENTAL MOBIL</title>
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
            max-width: 450px;
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
            padding: 12px 16px;
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
            padding: 14px;
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

        /* OTP Input Boxes */
        .otp-input {
            width: 45px;
            height: 50px;
            font-size: 1.5rem;
            text-align: center;
            margin: 0 4px;
            border-radius: 10px;
            border: 1.5px solid var(--grey-hint);
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card p-4 p-md-5">
            
            <?php if ($show_otp): ?>
                <!-- OTP / VERIFICATION SCREEN -->
                <div class="text-center mb-4">
                    <div class="brand-logo-circle">
                        <i class="bi bi-shield-lock-fill text-white fs-2"></i>
                    </div>
                    <h3 class="brand-header fw-bold mb-1">Verifikasi OTP</h3>
                    <p class="text-muted small">Masukkan 6 digit kode yang dikirim ke nomor HP Anda</p>
                    <div class="badge bg-light text-dark border mt-1">Simulasi OTP: <strong>123456</strong></div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="temp_username" value="<?= htmlspecialchars($temp_username) ?>">
                    <div class="d-flex justify-content-center mb-4">
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 1)" id="otp1" required>
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 2)" id="otp2" required>
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 3)" id="otp3" required>
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 4)" id="otp4" required>
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 5)" id="otp5" required>
                        <input type="text" name="otp[]" class="otp-input form-control" maxlength="1" oninput="moveToNext(this, 6)" id="otp6" required>
                    </div>

                    <button type="submit" name="verify_otp" class="btn btn-submit w-100 mb-3">
                        Verifikasi Kode <i class="bi bi-shield-check ms-1 fs-5"></i>
                    </button>
                    
                    <div class="text-center">
                        <p class="small text-muted mb-0">Tidak menerima kode? <a href="#" class="text-primary fw-bold text-decoration-none">Kirim Ulang</a></p>
                    </div>
                </form>

                <script>
                    function moveToNext(field, index) {
                        if (field.value.length >= 1) {
                            if (index < 6) {
                                document.getElementById('otp' + (index + 1)).focus();
                            }
                        }
                    }
                    // Auto focus first input
                    window.onload = function() {
                        document.getElementById('otp1').focus();
                    }
                </script>

            <?php else: ?>
                <!-- LOGIN SCREEN -->
                <div class="text-center mb-4">
                    <div class="brand-logo-circle">
                        <i class="bi bi-person-fill text-white fs-2"></i>
                    </div>
                    <h3 class="brand-header fw-bold mb-1">INDOMAX<span>CLIENT</span></h3>
                    <p class="text-muted small">Area Portal Pelanggan</p>
                </div>

                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($success_msg) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username Anda</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: rgba(15, 23, 42, 0.1);">
                                <i class="bi bi-person text-secondary"></i>
                            </span>
                            <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" style="border-radius: 0 12px 12px 0;" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px; border-color: rgba(15, 23, 42, 0.1);">
                                <i class="bi bi-key text-secondary"></i>
                            </span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" style="border-radius: 0 12px 12px 0;" required>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn btn-submit w-100 mb-4">
                        Masuk Akun <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                    </button>

                    <div class="text-center d-grid gap-2">
                        <p class="small text-muted mb-0">Belum punya akun? <a href="register_pelanggan.php" class="text-primary fw-bold text-decoration-none">Daftar Sekarang</a></p>
                        <p class="small text-muted mb-0">Kembali ke <a href="landing.php" class="text-secondary fw-bold text-decoration-none">Halaman Utama</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>