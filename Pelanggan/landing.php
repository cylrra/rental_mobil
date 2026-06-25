<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Redirect if already logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'pelanggan') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di PT INDOMAX RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Dynamic Energy Palette */
            --primary: #9e0000;         /* Bold Crimson */
            --primary-blue: #1a1c1c;    /* Deep Charcoal */
            --accent-blue: #9e0000;     /* Crimson */
            --gradient-blue: linear-gradient(135deg, #9e0000 0%, #cc0000 100%);
            --grey-light: #f8fafc;
            --grey-hint: #e2e8f0;
            --text-dark: #1a1c1c;
            --text-muted: #64748b;
            --font-display: 'Montserrat', sans-serif;
            --font-sans: 'Montserrat', sans-serif;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--grey-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Override Bootstrap Primary Text */
        .text-primary {
            color: var(--primary) !important;
        }

        /* Navbar Styling */
        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-dark) !important;
        }
        .navbar-brand span {
            color: var(--primary) !important;
        }
        .btn-nav-login {
            background-color: transparent;
            color: var(--accent-blue);
            border: 2px solid var(--accent-blue);
            border-radius: 50px;
            font-weight: 600;
            padding: 8px 24px;
            transition: all 0.3s ease;
        }
        .btn-nav-login:hover {
            background-color: var(--accent-blue);
            color: white;
        }
        .btn-nav-register {
            background: var(--gradient-blue);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: 10px 24px;
            box-shadow: 0 4px 12px rgba(158, 0, 0, 0.25);
            transition: all 0.3s ease;
        }
        .btn-nav-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(158, 0, 0, 0.35);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: radial-gradient(circle at 80% 20%, rgba(158, 0, 0, 0.05) 0%, transparent 50%), white;
            padding: 120px 0 80px;
            border-bottom: 1px solid var(--grey-hint);
        }
        .hero-title {
            font-family: var(--font-display);
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.1;
            color: var(--text-dark);
            margin-bottom: 20px;
        }
        .hero-title span {
            background: var(--gradient-blue);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 35px;
            max-width: 550px;
        }

        /* Features Section */
        .feature-card {
            background: white;
            border: 1px solid var(--grey-hint);
            border-radius: 20px;
            padding: 30px;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: rgba(158, 0, 0, 0.08);
            color: var(--primary);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        /* Car Preview Card */
        .car-card {
            background: white;
            border: 1px solid var(--grey-hint);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        }
        .car-img-wrapper {
            height: 200px;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .car-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .car-card:hover .car-img-wrapper img {
            transform: scale(1.05);
        }

        /* Promo Section */
        .promo-banner {
            background: var(--gradient-blue);
            color: white;
            border-radius: 24px;
            padding: 50px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(158, 0, 0, 0.15);
        }
        .promo-bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }
        .promo-bubble-1 { width: 300px; height: 300px; top: -50px; right: -50px; }
        .promo-bubble-2 { width: 150px; height: 150px; bottom: -30px; left: 10%; }

        /* Footer */
        footer {
            background-color: var(--primary-blue);
            color: white;
            padding: 60px 0 30px;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 border-bottom">
        <div class="container">
            <a class="navbar-brand fs-3" href="#">
                🚗 INDOMAX<span>RENT</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="d-flex gap-3 mt-3 mt-lg-0">
                    <a href="login_pelanggan.php" class="btn btn-nav-login d-flex align-items-center">Masuk</a>
                    <a href="register_pelanggan.php" class="btn btn-nav-register d-flex align-items-center">Daftar Akun</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="hero-title">
                        Sewa Mobil Premium <span>Mudah & Cepat</span>
                    </h1>
                    <p class="hero-subtitle">
                        Temukan armada kendaraan terbaik dari PT INDOMAX RENTAL. Kami menyediakan layanan lepas kunci maupun dengan sopir profesional untuk kenyamanan perjalanan Anda.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register_pelanggan.php" class="btn btn-nav-register btn-lg px-4 py-3">Mulai Rental Sekarang <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="#armada" class="btn btn-nav-login btn-lg px-4 py-3">Lihat Armada</a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=800" alt="Indomax Rental" class="img-fluid rounded-4 shadow-lg border" style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-white">
        <div class="container my-5">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 1.5px;">Mengapa Kami?</h6>
                <h2 class="fw-bold" style="font-family: var(--font-display);">Layanan Terbaik Untuk Perjalanan Anda</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h4 class="fw-bold">Lepas Kunci (Self-Drive)</h4>
                        <p class="text-muted">Nikmati kebebasan berkendara sendiri dengan proses verifikasi KTP & SIM yang mudah dan cepat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <h4 class="fw-bold">Dengan Jasa Sopir</h4>
                        <p class="text-muted">Ingin bersantai? Driver profesional dan berpengalaman kami siap mengantar Anda dengan aman sampai tujuan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h4 class="fw-bold">Pembayaran Fleksibel</h4>
                        <p class="text-muted">Bisa melakukan pembayaran DP 30% atau langsung lunas melalui Transfer Bank, E-Wallet, atau Tunai di tempat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Fleet Section -->
    <section id="armada" class="py-5">
        <div class="container my-5">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h6 class="text-primary fw-bold text-uppercase" style="letter-spacing: 1.5px;">Katalog Pilihan</h6>
                    <h2 class="fw-bold m-0" style="font-family: var(--font-display);">Pilihan Armada Terpopuler</h2>
                </div>
                <a href="login_pelanggan.php" class="btn btn-outline-dark rounded-pill px-4">Lihat Semua Mobil <i class="bi bi-chevron-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php
                // Display 3 featured cars
                $sql = "SELECT * FROM mobil LIMIT 3";
                $query = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_array($query)) {
                    $nama_file = $row['Gambar']; 
                    $path_gambar = "img/" . $nama_file;
                    if (empty($nama_file) || !file_exists($path_gambar)) {
                        $path_gambar = "https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&q=80&w=600";
                    }
                ?>
                <div class="col-md-4">
                    <div class="car-card h-100">
                        <div class="car-img-wrapper">
                            <img src="<?php echo $path_gambar; ?>" alt="<?php echo $row['merk']; ?>">
                        </div>
                        <div class="p-4">
                            <span class="badge bg-light text-dark border rounded-pill mb-2 px-3 py-1"><?php echo strtoupper($row['jenis']); ?></span>
                            <h4 class="fw-bold text-dark mb-1"><?php echo $row['merk']; ?></h4>
                            <p class="text-primary fw-bold fs-5 mb-3">
                                <span class="text-muted fw-normal fs-6">Mulai </span>Rp <?php echo number_format($row['tarif_12_dalam'], 0, ',', '.'); ?> <span class="text-muted fw-normal fs-6">/ 12 Jam</span>
                            </p>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="bi bi-snow2 me-1"></i> AC / Media</span>
                                <a href="login_pelanggan.php" class="btn btn-dark rounded-pill px-3 py-2 btn-sm fw-bold">Sewa Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Promo Banner Section -->
    <section class="py-5 bg-white">
        <div class="container my-5">
            <div class="promo-banner">
                <div class="promo-bubble promo-bubble-1"></div>
                <div class="promo-bubble promo-bubble-2"></div>
                <div class="row align-items-center z-1 position-relative">
                    <div class="col-lg-8">
                        <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-2 rounded-pill">SPESIAL BULAN INI</span>
                        <h2 class="display-5 fw-bold mb-3" style="font-family: var(--font-display);">Diskon 20% Untuk Liburan Akhir Pekan!</h2>
                        <p class="lead mb-0 opacity-75">Sewa mobil apa saja untuk minimal 3 hari dan nikmati diskon potongan harga langsung. Dapatkan juga gratis layanan antar jemput di Bandara Solo.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="register_pelanggan.php" class="btn btn-light text-dark btn-lg rounded-pill px-4 py-3 fw-bold shadow-lg">Daftar Akun Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-6">
                    <h3 class="fw-bold mb-3" style="font-family: var(--font-display);">🚗 INDOMAX RENTAL</h3>
                    <p class="text-white-50" style="max-width: 400px;">Penyedia jasa sewa mobil terbaik dan terpercaya di Jawa Tengah. Kami berkomitmen memberikan armada terawat dengan layanan profesional.</p>
                </div>
                <div class="col-lg-3">
                    <h5 class="fw-bold mb-3">Tautan Cepat</h5>
                    <ul class="list-unstyled text-white-50 d-grid gap-2">
                        <li><a href="login_pelanggan.php" class="text-white-50 text-decoration-none">Masuk Pelanggan</a></li>
                        <li><a href="register_pelanggan.php" class="text-white-50 text-decoration-none">Daftar Akun</a></li>
                        <li><a href="#armada" class="text-white-50 text-decoration-none">Pilihan Armada</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="fw-bold mb-3">Hubungi Kami</h5>
                    <p class="text-white-50 mb-1"><i class="bi bi-geo-alt-fill me-2"></i> Solo, Jawa Tengah</p>
                    <p class="text-white-50 mb-1"><i class="bi bi-telephone-fill me-2"></i> 0812-3456-7890</p>
                    <p class="text-white-50"><i class="bi bi-envelope-fill me-2"></i> support@indomaxrental.com</p>
                </div>
            </div>
            <hr class="text-white-50 opacity-25">
            <div class="text-center text-white-50 small mt-4">
                <p class="m-0">&copy; <?php echo date('Y'); ?> PT INDOMAX RENTAL MOBIL. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
