<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php'; // Mengambil koneksi.php yang menggunakan $conn

$error = '';

// Proses Login Admin
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if ($username !== '' && $password !== '') {
        // Mengamankan input string dari risiko sql injection primitif
        $username = mysqli_real_escape_string($conn, $username);
        
        // Validasi kredensial admin secara statis sesuai ketentuan
        if ($username === 'admin' && $password === '12345') {
            // SINKRONISASI SESSION KETAT UNTUK ADMIN
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --font-display: 'Outfit', sans-serif;
            --font-sans: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background: radial-gradient(circle at 10% 20%, rgba(216, 241, 230, 0.46) 0.1%, rgba(233, 226, 226, 0.28) 90.1%),
                        linear-gradient(120deg, #fdfbf7 0%, #eef5fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative Bubbles */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: var(--primary-gradient);
            filter: blur(80px);
            opacity: 0.15;
            z-index: 0;
        }
        .bubble-1 { width: 350px; height: 350px; top: -10%; left: -10%; }
        .bubble-2 { width: 400px; height: 400px; bottom: -15%; right: -5%; }

        .login-container {
            z-index: 1;
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            box-shadow: 0 30px 60px rgba(13, 110, 253, 0.1);
        }

        .brand-header {
            font-family: var(--font-display);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1.5px solid rgba(0, 0, 0, 0.1);
            font-size: 0.95rem;
            transition: all 0.25s ease;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
            background-color: #fff;
        }

        .btn-submit {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.25);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.35);
            opacity: 0.95;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .brand-logo-circle {
            width: 70px;
            height: 70px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }

        .login-card:hover .brand-logo-circle {
            transform: rotate(5deg) scale(1.05);
        }
    </style>
</head>
<body>

    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>

    <div class="login-container">
        <div class="login-card p-5">
            <div class="text-center mb-4">
                <div class="brand-logo-circle">
                    <i class="bi bi-shield-lock-fill text-white fs-1"></i>
                </div>
                <h3 class="brand-header fw-bold mb-1">INDOMAX RENTAL</h3>
                <p class="text-muted small">Dashboard Login Administrator</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 small d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username Admin</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                            <i class="bi bi-person text-secondary"></i>
                        </span>
                        <input type="text" name="username" class="form-control border-start-0" placeholder="Masukkan username admin" style="border-radius: 0 12px 12px 0;" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Password Admin</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                            <i class="bi bi-key text-secondary"></i>
                        </span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="Masukkan password admin" style="border-radius: 0 12px 12px 0;" required>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 btn-submit text-white">
                    Masuk Sebagai Admin <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>