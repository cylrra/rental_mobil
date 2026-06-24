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
        
        if ($username === 'admin' && $password === '12345') {
            $_SESSION['role'] = 'admin';
            $_SESSION['nama_user'] = 'Administrator Indomax';
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Kombinasi Username & Password Admin salah!";
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
            background-color: var(--background);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            color: var(--text-light);
            overflow-x: hidden;
            position: relative;
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

        /* 8px moderate rounded corners and low-contrast outline */
        .login-card {
            background-color: var(--surface);
            border: 1px solid var(--tertiary);
            border-radius: 8px; /* 8px moderate radius */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
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
            letter-spacing: 0.05em;
            color: #dadada;
            margin-bottom: 8px;
        }

        /* Custom inputs for dark theme */
        .form-control {
            background-color: #1a1c1c;
            color: var(--text-light);
            border-radius: 8px; /* 8px rounding */
            padding: 12px 16px;
            border: 1px solid var(--tertiary);
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: #656464;
        }

        /* Focus: border thickens to 2px and changes to primary crimson */
        .form-control:focus {
            background-color: #1a1c1c;
            color: var(--text-light);
            border: 2px solid var(--primary);
            box-shadow: none;
            outline: none;
        }

        .input-group-text {
            background-color: #1a1c1c;
            color: #dadada;
            border-color: var(--tertiary);
        }

        /* Primary CTA uses Energetic Gold with bold dark text */
        .btn-submit {
            background-color: var(--secondary-container);
            color: #1a1c1c;
            border: none;
            border-radius: 8px; /* 8px rounding */
            padding: 14px;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.2);
        }

        .btn-submit:hover {
            background-color: #c49d2b;
            color: #1a1c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(212, 175, 55, 0.3);
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="login-container">
        <div class="login-card">
            
            <div class="text-center mb-5">
                <div class="brand-logo-circle">
                    <i class="bi bi-shield-lock-fill text-white fs-3"></i>
                </div>
                <h3 class="brand-header mb-1">INDOMAX<span>STAFF</span></h3>
                <p class="text-muted small fw-semibold" style="letter-spacing: 0.05em; text-transform: uppercase; font-size: 0.7rem; color: #dadada !important;">Dashboard Login Administrator</p>
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
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Password" style="border-radius: 0 8px 8px 0;" required>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-submit w-100 mb-4">
                    Masuk Sebagai Admin <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>

                <div class="text-center d-grid gap-2">
                    <p class="small text-muted mb-0 fw-medium">Kembali ke <a href="../index.php" class="text-danger fw-bold text-decoration-none" style="color: var(--secondary-container) !important;">Portal Utama</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
