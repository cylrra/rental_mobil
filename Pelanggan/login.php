<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$error = '';
$success_msg = '';

// Check if redirected after registration
if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
    $success_msg = "Registrasi berhasil! Akun Anda sudah siap digunakan. Silakan login.";
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            $login_success = false;
            if (password_verify($password, $row['password'])) {
                $login_success = true;
            } elseif ($password === $row['password']) {
                // Fallback for legacy plain text passwords
                $login_success = true;
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $id_p = $row['id_pelanggan'];
                $stmt_up = mysqli_prepare($conn, "UPDATE pelanggan SET password = ? WHERE id_pelanggan = ?");
                mysqli_stmt_bind_param($stmt_up, "si", $new_hash, $id_p);
                mysqli_stmt_execute($stmt_up);
            }

            if ($login_success) {
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
            max-width: 440px;
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
        <div class="login-card">
            
            <div class="text-center mb-5">
                <div class="brand-logo-circle">
                    <i class="bi bi-person-fill text-white fs-3"></i>
                </div>
                <h3 class="brand-header mb-1">INDOMAX<span>CLIENT</span></h3>
                <p class="text-muted small fw-semibold" style="letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.7rem;">Area Portal Pelanggan</p>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success border-0 rounded-3 mb-4 small d-flex align-items-center" style="background-color: #d1fae5; color: #065f46;" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <div class="fw-semibold"><?= htmlspecialchars($success_msg) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-3 mb-4 small d-flex align-items-center" style="background-color: #ffdad6; color: #ba1a1a;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <div class="fw-semibold"><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px; border-color: #e2e2e2;">
                            <i class="bi bi-person text-secondary"></i>
                        </span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username" style="border-radius: 0 8px 8px 0;" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px; border-color: #e2e2e2;">
                            <i class="bi bi-key text-secondary"></i>
                        </span>
                        <input type="password" name="password" id="passwordField" class="form-control border-start-0 border-end-0" placeholder="Masukkan password" required>
                        <span class="input-group-text bg-light border-start-0" style="cursor: pointer; border-radius: 0 8px 8px 0; border-color: #e2e2e2;" onclick="togglePassword()">
                            <i class="bi bi-eye text-secondary" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <script>
                    function togglePassword() {
                        const passwordField = document.getElementById('passwordField');
                        const toggleIcon = document.getElementById('toggleIcon');
                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                            toggleIcon.classList.remove('bi-eye');
                            toggleIcon.classList.add('bi-eye-slash');
                        } else {
                            passwordField.type = 'password';
                            toggleIcon.classList.remove('bi-eye-slash');
                            toggleIcon.classList.add('bi-eye');
                        }
                    }
                </script>

                <button type="submit" name="login" class="btn btn-submit w-100 mb-4">
                    Masuk Akun <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>

                <div class="text-center d-grid gap-2">
                    <p class="small text-muted mb-0 fw-medium">Belum punya akun? <a href="register_pelanggan.php" class="text-danger fw-bold text-decoration-none" style="color: var(--primary) !important;">Daftar Sekarang</a></p>
                    <p class="small text-muted mb-0 fw-medium">Kembali ke <a href="../index.php" class="text-secondary fw-bold text-decoration-none">Portal Utama</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
