<?php
include 'koneksi.php';

// Fetch fleet for preview
$query_fleet = mysqli_query($conn, "SELECT * FROM mobil WHERE status_mobil = 'tersedia' LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>INDOMAX RENTAL MOBIL - The Premium Way to Drive</title>
    <!-- Google Fonts Outfit & Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        bluebird: {
                            50: '#f0f7ff',
                            100: '#e0f0fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        gold: '#F59E0B'
                    }
                }
            }
        };
    </script>
    <style>
        .hero-bg {
            background-image: linear-gradient(to right, rgba(12, 74, 110, 0.95) 0%, rgba(12, 74, 110, 0.8) 40%, rgba(12, 74, 110, 0) 100%), url('assets/img/hero_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans selection:bg-bluebird-500 selection:text-white flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-bluebird-600 flex items-center justify-center text-white shadow-lg shadow-bluebird-500/30">
                    <i data-lucide="car-front" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="font-heading font-black text-xl tracking-tight text-bluebird-900 leading-none">INDOMAX</h1>
                    <p class="text-[10px] font-bold text-gold uppercase tracking-widest mt-0.5">Premium Rental</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-slate-600">
                <a href="#layanan" class="hover:text-bluebird-600 transition-colors">Layanan Kami</a>
                <a href="#armada" class="hover:text-bluebird-600 transition-colors">Armada Pilihan</a>
                <a href="#cara-pesan" class="hover:text-bluebird-600 transition-colors">Cara Pesan</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="Pelanggan/login.php" class="hidden md:block font-bold text-sm text-slate-700 hover:text-bluebird-600 transition-colors">
                    Masuk
                </a>
                <a href="Pelanggan/login.php" class="bg-bluebird-600 hover:bg-bluebird-700 text-white text-sm font-bold py-2.5 px-6 rounded-full shadow-lg shadow-bluebird-600/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-4 h-4"></i> Booking
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-48 md:pb-32 hero-bg min-h-[85vh] flex items-center">
        <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
            <div class="max-w-2xl">
                <span class="inline-block py-1.5 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-bluebird-50 text-xs font-bold uppercase tracking-wider mb-6">
                    🏆 #1 Layanan Rental Mobil Terpercaya
                </span>
                <h1 class="font-heading text-5xl md:text-7xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                    Perjalanan <span class="text-bluebird-500">Premium.</span><br>Kapan Saja.
                </h1>
                <p class="text-lg md:text-xl text-bluebird-50 mb-10 font-light leading-relaxed max-w-xl">
                    Sewa mobil mudah, aman, dan nyaman bersama INDOMAX. Didukung dengan armada terbaru dan supir profesional untuk setiap perjalanan Anda.
                </p>
                
                <!-- Quick Action Box -->
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-2xl inline-block w-full max-w-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center gap-3">
                            <div class="bg-bluebird-50 p-2 rounded-lg text-bluebird-600">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Lokasi</p>
                                <p class="text-sm font-semibold text-slate-700">Semarang & Sekitarnya</p>
                            </div>
                        </div>
                        <div class="border border-slate-200 rounded-xl p-3 flex items-center gap-3">
                            <div class="bg-bluebird-50 p-2 rounded-lg text-bluebird-600">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Layanan</p>
                                <p class="text-sm font-semibold text-slate-700">24 Jam Operasional</p>
                            </div>
                        </div>
                    </div>
                    <a href="Pelanggan/login.php" class="mt-4 w-full bg-slate-900 text-white font-bold py-3.5 rounded-xl flex items-center justify-center gap-2 hover:bg-slate-800 transition-colors shadow-lg">
                        <i data-lucide="search" class="w-5 h-5"></i> Cari & Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mengapa Memilih Kami -->
    <section id="layanan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-heading text-3xl md:text-4xl font-black text-slate-900 mb-4">Layanan Unggulan Kami</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Kami berkomitmen memberikan kenyamanan dan keamanan terbaik dalam setiap kilometer perjalanan Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Armada Terawat</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Seluruh mobil kami melewati proses servis rutin dan pengecekan ketat sebelum disewakan kepada Anda.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="user-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Supir Profesional</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Tersedia opsi penyewaan dengan supir berpengalaman, ramah, dan sangat menguasai rute perjalanan.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="headphones" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Dukungan 24/7</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Tim customer service kami selalu siap sedia membantu Anda kapan pun Anda membutuhkan bantuan di jalan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Armada Pilihan -->
    <section id="armada" class="py-20 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="font-heading text-3xl md:text-4xl font-black text-slate-900 mb-3">Armada Favorit</h2>
                    <p class="text-slate-500">Pilihan mobil terbaik yang siap menemani perjalanan Anda.</p>
                </div>
                <a href="Pelanggan/login.php" class="hidden md:flex items-center gap-2 font-bold text-bluebird-600 hover:text-bluebird-800 transition-colors">
                    Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while($car = mysqli_fetch_assoc($query_fleet)): 
                    $img_src = (!empty($car['Gambar']) && file_exists('Pelanggan/img/'.$car['Gambar'])) ? 'Pelanggan/img/'.$car['Gambar'] : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&q=80';
                ?>
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden group hover:shadow-xl transition-all duration-300 flex flex-col">
                    <div class="relative h-48 overflow-hidden bg-slate-100">
                        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($car['merk'] . ' ' . $car['jenis']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-slate-700 shadow-sm">
                            <?= htmlspecialchars($car['merk']) ?>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-heading text-xl font-bold text-slate-900 mb-2"><?= htmlspecialchars($car['merk'] . ' ' . $car['jenis']) ?></h3>
                        <div class="flex items-center gap-4 text-sm text-slate-500 font-medium mb-6">
                            <div class="flex items-center gap-1.5"><i data-lucide="users" class="w-4 h-4"></i> 4-7 Kursi</div>
                            <div class="flex items-center gap-1.5"><i data-lucide="fuel" class="w-4 h-4"></i> Bensin</div>
                        </div>
                        <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Mulai dari</p>
                                <p class="font-heading text-xl font-black text-bluebird-600">Rp <?= number_format($car['tarif_per_hari'], 0, ',', '.') ?><span class="text-sm font-medium text-slate-500">/hari</span></p>
                            </div>
                            <a href="Pelanggan/login.php" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-bluebird-600 group-hover:text-white transition-colors">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <a href="Pelanggan/login.php" class="mt-8 md:hidden flex items-center justify-center gap-2 font-bold w-full py-4 rounded-xl bg-bluebird-50 text-bluebird-600">
                Lihat Semua Armada <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-bluebird-600 flex items-center justify-center text-white">
                        <i data-lucide="car-front" class="w-5 h-5"></i>
                    </div>
                    <h2 class="font-heading font-black text-xl text-white">INDOMAX</h2>
                </div>
                <p class="text-sm leading-relaxed max-w-sm mb-6">
                    Solusi transportasi pintar, elegan, dan terpercaya. Kami berdedikasi untuk memberikan pengalaman berkendara kelas premium bagi setiap pelanggan.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#layanan" class="hover:text-white transition-colors">Layanan Kami</a></li>
                    <li><a href="#armada" class="hover:text-white transition-colors">Armada Pilihan</a></li>
                    <li><a href="Pelanggan/login.php" class="hover:text-white transition-colors">Masuk / Daftar</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Kontak</h4>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-slate-500"></i> Semarang, Indonesia</li>
                    <li class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4 text-slate-500"></i> +62 812 3456 7890</li>
                    <li class="flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4 text-slate-500"></i> halo@indomax.id</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-12 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center text-xs">
            <p>&copy; <?= date('Y') ?> PT INDOMAX RENTAL. Hak Cipta Dilindungi.</p>
            <!-- Tautan Rahasia Admin -->
            <a href="Admin/login_admin.php" class="mt-4 md:mt-0 text-slate-700 hover:text-slate-500 transition-colors" title="Portal Staff">
                <i data-lucide="settings" class="w-4 h-4"></i>
            </a>
        </div>
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>
