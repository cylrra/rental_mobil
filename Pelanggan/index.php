<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pengguna dengan role 'pelanggan' yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    // Jika tidak valid atau diakses level lain, paksa alihkan ke login pelanggan
    header("Location: login_pelanggan.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Mengambil ID Pelanggan dari session login untuk personalisasi data
$id_pelanggan = $_SESSION['id_pelanggan'] ?? 0;

// Statistik Khusus Pelanggan yang Sedang Login
// 1. Total Armada yang tersedia di rental saat ini
$total_mobil = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM mobil WHERE status_mobil = 'tersedia'"));

// 2. Total Transaksi yang PERNAH dilakukan oleh pelanggan ini saja
$total_transaksi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE id_pelanggan = '$id_pelanggan'"));

// Karena ini halaman pelanggan, kunci status admin ke false
$is_admin = false;
$col_stats = 'col-md-6'; // Tampilan statistik membagi 2 kolom sama besar
$col_menu = 'col-md-4 col-sm-6'; // Layout grid menu agar presisi dan responsif
?>

<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12 text-center my-4">
            <h1 class="fw-bold">🚗 RENTAL MOBIL PT INDOMAX</h1>
            <p class="text-muted">Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['nama_pelanggan'] ?? 'Pelanggan'); ?></strong>! Yuk Jelajahi Armada Terbaik Kami.</p>
        </div>
    </div>

    <div class="row mb-4 justify-content-center">
        <div class="<?php echo $col_stats; ?> mb-3">
            <div class="card bg-primary text-white shadow-sm border-0 h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="opacity-75">Armada Siap Pakai</h5>
                    <h2 class="fw-bold m-0"><?php echo $total_mobil; ?> Mobil</h2>
                </div>
            </div>
        </div>
        <div class="<?php echo $col_stats; ?> mb-3">
            <div class="card bg-success text-white shadow-sm border-0 h-100 rounded-3">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <h5 class="opacity-75">Riwayat Sewa Anda</h5>
                    <h2 class="fw-bold m-0"><?php echo $total_transaksi; ?> Transaksi</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="row text-center justify-content-center g-4">
                        
                        <div class="<?php echo $col_menu; ?>">
                            <a href="katalog.php" class="text-decoration-none text-dark d-block h-100">
                                <div class="p-4 border rounded hover-shadow bg-white transition-all d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="bi bi-car-front fs-1 text-primary"></i>
                                    <h5 class="mt-3 fw-semibold fs-6">Katalog Mobil</h5>
                                </div>
                            </a>
                        </div>
                        
                        <div class="<?php echo $col_menu; ?>">
                            <a href="riwayat_pembayaran.php" class="text-decoration-none text-dark d-block h-100">
                                <div class="p-4 border rounded hover-shadow bg-white transition-all d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="bi bi-clock-history fs-1 text-success"></i>
                                    <h5 class="mt-3 fw-semibold fs-6">Riwayat & Bayar</h5>
                                </div>
                            </a>
                        </div>
                        
                        <div class="<?php echo $col_menu; ?>">
                            <a href="input_rating.php" class="text-decoration-none text-dark d-block h-100">
                                <div class="p-4 border rounded hover-shadow bg-white transition-all d-flex flex-column align-items-center justify-content-center h-100">
                                    <i class="bi bi-star-half fs-1 text-warning"></i>
                                    <h5 class="mt-3 fw-semibold fs-6">Berikan Rating</h5>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-all {
        transition: all 0.25s ease-in-out;
    }
    .hover-shadow:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        transform: translateY(-5px);
        background-color: #f8f9fa !important;
    }
    .hover-shadow {
        min-height: 140px;
    }
</style>

</div> </body>
</html>