<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if ($username !== '' && $password !== '') {
        $username = mysqli_real_escape_string($conn, $username);
        $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $query = mysqli_stmt_get_result($stmt);
        if ($query && mysqli_num_rows($query) === 1) {
            $row = mysqli_fetch_assoc($query);
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['role'] = 'admin';
                $_SESSION['id_admin'] = $row['id_admin'];
                $_SESSION['nama_user'] = $row['nama_lengkap'];
                
                header("Location: index.php");
                exit();
            } else {
                $error = "Password admin salah!";
            }
        } else {
            $error = "Username admin tidak ditemukan!";
        }
    } else {
        $error = "Username dan password admin wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - INDOMAX RENTAL MOBIL</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>

    <style>
        :root {
            /* Dynamic Energy Color Scheme */
            --primary: #800000;         /* Maroon */
            --secondary-container: #d4af37; /* Gold */
            --tertiary: #4d4c4c;        /* Deep Charcoal */
            --background: #1a1c1c;      /* Solid Dark background for professional feel */
            --surface: #2f3131;         /* Deep Gray surface */
            --text-light: #f9f9f9;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url('../assets/img/ferrari_bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            color: var(--text-light);
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(160deg, rgba(0,0,0,0.55) 0%, rgba(80,0,0,0.40) 100%);
            z-index: 0;
        }

        /* Ambient glow in background */
        .ambient-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background-color: var(--primary);
            filter: blur(120px);
            opacity: 0.12;
            top: 10%;
            left: 10%;
            pointer-events: none;
            z-index: 0;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            z-index: 1;
        }

        /* Glassmorphism card - high contrast dark solid */
        .login-card {
            background: rgba(15, 10, 10, 0.82);
            backdrop-filter: blur(20px) saturate(1.3);
            -webkit-backdrop-filter: blur(20px) saturate(1.3);
            border: 1px solid rgba(255,255,255,0.10);
            border-top: 1px solid rgba(255,100,100,0.20);
            border-radius: 20px;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(180,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.06);
            padding: 40px 32px;
        }

        .brand-logo-circle {
            width: 60px;
            height: 60px;
            background-color: var(--primary);
            border-radius: 8px; /* 8px rounding */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 4px 14px rgba(128, 0, 0, 0.3);
        }

        .brand-header {
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--text-light);
        }
        
        .brand-header span {
            color: var(--secondary-container);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #ffffff;
            margin-bottom: 8px;
        }

        /* Custom inputs for dark theme */
        .form-control {
            background: rgba(0,0,0,0.35);
            color: #ffffff;
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid rgba(255,255,255,0.18);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.55);
        }

        /* Focus: bright crimson border glow */
        .form-control:focus {
            background: rgba(0,0,0,0.50);
            color: #ffffff;
            border: 2px solid var(--primary);
            box-shadow: 0 0 0 3px rgba(200,0,0,0.15);
            outline: none;
        }

        .input-group-text {
            background: rgba(0,0,0,0.35);
            color: rgba(255,255,255,0.85);
            border-color: rgba(255,255,255,0.18);
        }

        /* Primary CTA uses Energetic Gold - high contrast */
        .btn-submit {
            background: linear-gradient(135deg, #e8c02a 0%, #d4af37 100%);
            color: #0f0f0f;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 800;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            transition: all 0.25s ease;
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4), inset 0 1px 0 rgba(255,255,255,0.25);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #f5d040 0%, #e8c02a 100%);
            color: #0f0f0f;
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(212, 175, 55, 0.5);
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="login-container" style="position: relative; z-index: 1;">
        <div class="login-card">
            
            <div class="text-center mb-5">
                <div class="brand-logo-circle">
                    <i class="bi bi-shield-lock-fill text-white fs-3"></i>
                </div>
                <h3 class="brand-header mb-1">INDOMAX<span>STAFF</span></h3>
                <p class="fw-semibold" style="letter-spacing: 0.06em; text-transform: uppercase; font-size: 0.7rem; color: rgba(255,255,255,0.70) !important;">Dashboard Login Administrator</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-3 mb-4 small d-flex align-items-center" style="background-color: #ffdad6; color: #ba1a1a;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <div class="fw-semibold"><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username Admin</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="bi bi-person text-secondary"></i>
                        </span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="Username" style="border-radius: 0 8px 8px 0;" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Password Admin</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="bi bi-key text-secondary"></i>
                        </span>
                        <input type="password" name="password" id="passwordField" class="form-control border-start-0 border-end-0" placeholder="Password" required>
                        <span class="input-group-text border-start-0" style="cursor: pointer; border-radius: 0 8px 8px 0;" onclick="togglePassword()">
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
                    Masuk Sebagai Admin <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>

                <div class="text-center d-grid gap-2">
                    <p class="small mb-0 fw-medium" style="color: rgba(255,255,255,0.65);">Kembali ke <a href="../index.php" class="fw-bold text-decoration-none" style="color: #d4af37;">Portal Utama</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
