<?php
include 'koneksi.php';

// Fetch fleet for preview
$query_fleet = mysqli_query($conn, "SELECT * FROM mobil WHERE status_mobil = 'tersedia' LIMIT 3");

// Fetch ratings/testimonials dynamically from rating_sewa table (latest booking rating history)
$query_testimonials = mysqli_query($conn, "
    SELECT r.*, p.nama 
    FROM rating_sewa r 
    JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan 
    ORDER BY r.tgl_rating DESC 
    LIMIT 3
");

$testimonials = [];
if ($query_testimonials && mysqli_num_rows($query_testimonials) > 0) {
    while ($row = mysqli_fetch_assoc($query_testimonials)) {
        $testimonials[] = $row;
    }
}
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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
                            50: '#fff5f5',
                            100: '#ffe3e3',
                            500: '#c80000',
                            600: '#9e0000',
                            800: '#7a0000',
                            900: '#4a0000',
                        },
                        gold: '#fdc003'
                    }
                }
            }
        };
    </script>
    <style>
        .hero-bg {
            /* Now handled dynamically via absolute background layers inside section */
            background-color: #1a0000;
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
                <a href="#home" class="hover:text-bluebird-600 transition-colors">Home</a>
                <a href="#armada" class="hover:text-bluebird-600 transition-colors">Armada</a>
                <a href="#layanan" class="hover:text-bluebird-600 transition-colors">Layanan Kami</a>
                <a href="#testimoni" class="hover:text-bluebird-600 transition-colors">Testimoni</a>
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

    <!-- Hero Section with Fixed Height to Prevent Layout Shifts -->
    <section id="home" class="relative h-[80vh] min-h-[550px] md:h-[85vh] md:min-h-[700px] hero-bg flex items-center z-10">
        <!-- Hero Background Carousel -->
        <div class="absolute inset-0 z-0 select-none pointer-events-none overflow-hidden">
            <!-- Slide 1 -->
            <div class="hero-slide absolute inset-0 opacity-100 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home1.png?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Slide 2 -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home2.png?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Slide 3 -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home3.jpg?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Slide 4 -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home4.jpg?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Slide 5 -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home5.jpg?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Slide 6 -->
            <div class="hero-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out" style="background-image: linear-gradient(to right, rgba(74, 0, 0, 0.95) 0%, rgba(74, 0, 0, 0.8) 40%, rgba(74, 0, 0, 0) 100%), url('assets/img/home6.png?v=8'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
        </div>

        <!-- Carousel Indicators -->
        <div class="absolute bottom-16 right-6 md:right-12 flex gap-2.5 z-30 select-none">
            <button onclick="setSlide(0)" class="hero-dot w-6 h-2.5 rounded-full bg-white transition-all duration-300 shadow-sm" aria-label="Slide 1"></button>
            <button onclick="setSlide(1)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 shadow-sm" aria-label="Slide 2"></button>
            <button onclick="setSlide(2)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 shadow-sm" aria-label="Slide 3"></button>
            <button onclick="setSlide(3)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 shadow-sm" aria-label="Slide 4"></button>
            <button onclick="setSlide(4)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 shadow-sm" aria-label="Slide 5"></button>
            <button onclick="setSlide(5)" class="hero-dot w-2.5 h-2.5 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300 shadow-sm" aria-label="Slide 6"></button>
        </div>

        <div class="max-w-7xl mx-auto px-6 w-full relative z-10 pb-16">
            <div id="hero-text-container" class="max-w-2xl transition-opacity duration-300 ease-in-out">
                <span id="hero-badge" class="inline-block py-1.5 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-bluebird-50 text-xs font-bold uppercase tracking-wider mb-6">
                    🏆 #1 Layanan Rental Mobil Terpercaya
                </span>
                <h1 id="hero-title" class="font-heading text-5xl md:text-7xl font-black text-white mb-6 leading-[1.1] tracking-tight">
                    Perjalanan <span class="text-bluebird-500">Premium.</span><br>Kapan Saja.
                </h1>
                <p id="hero-desc" class="text-lg md:text-xl text-bluebird-50 mb-6 font-light leading-relaxed max-w-xl">
                    Sewa mobil mudah, aman, dan nyaman bersama INDOMAX. Didukung dengan armada terbaru dan supir profesional untuk setiap perjalanan Anda.
                </p>
            </div>
        </div>

        <!-- Floating Quick Booking Widget overlapping bottom border -->
        <div class="absolute bottom-0 left-0 w-full translate-y-1/2 z-20 px-6">
            <div class="max-w-4xl mx-auto bg-white p-5 md:p-6 rounded-3xl shadow-2xl border border-slate-100 grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <!-- Location Info -->
                <div class="border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:pr-6 flex items-center gap-3">
                    <div class="bg-bluebird-50 p-3 rounded-2xl text-bluebird-600 shadow-inner">
                        <i data-lucide="map-pin" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Lokasi Jemput</p>
                        <p class="text-sm font-extrabold text-slate-700">Semarang & Sekitarnya</p>
                    </div>
                </div>
                <!-- Service Operational Info -->
                <div class="border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:px-6 flex items-center gap-3">
                    <div class="bg-bluebird-50 p-3 rounded-2xl text-bluebird-600 shadow-inner">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Layanan Operasional</p>
                        <p class="text-sm font-extrabold text-slate-700">24 Jam Nonstop</p>
                    </div>
                </div>
                <!-- Action Button -->
                <div class="md:pl-6">
                    <a href="Pelanggan/login.php" class="w-full bg-bluebird-600 hover:bg-bluebird-700 text-white font-bold py-4 px-6 rounded-2xl flex items-center justify-center gap-2.5 transition-all hover:shadow-lg hover:shadow-bluebird-600/30 hover:scale-[1.02] active:scale-[0.98]">
                        <i data-lucide="search" class="w-5 h-5"></i> Cari & Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mengapa Memilih Kami -->
    <section id="layanan" class="pt-28 pb-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-heading text-3xl md:text-4xl font-black text-slate-900 mb-4">Layanan Unggulan Kami</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Kami berkomitmen memberikan kenyamanan dan keamanan terbaik dalam setiap kilometer perjalanan Anda.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Armada Prima</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Seluruh mobil kami dijamin bersih, prima, wangi, dan rutin servis demi kenyamanan & keamanan penuh Anda.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="user-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Supir Profesional</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Pengemudi berpengalaman yang sangat ramah, sopan, dan menguasai rute jalan untuk menjamin ketepatan waktu Anda.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="globe" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Layanan 24 Jam Online</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Pemesanan online praktis dan bantuan Customer Service kami selalu aktif siaga melayani Anda selama 24 jam nonstop.</p>
                </div>
                <!-- Feature 4 -->
                <div class="p-8 rounded-3xl bg-bluebird-50 border border-bluebird-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-bluebird-600 mb-6">
                        <i data-lucide="wallet" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-heading text-xl font-bold text-slate-900 mb-3">Harga Transparan</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Sistem harga yang jujur, transparan, bersahabat sejak awal tanpa tambahan tak terduga.</p>
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

    <!-- Testimonials Section (Ulasan & Rating Histori) -->
    <section id="testimoni" class="py-24 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="font-heading text-3xl md:text-4xl font-black text-slate-900 mb-4">Ulasan & Histori Rating</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Kepuasan Anda adalah prioritas kami. Berikut adalah rating dan ulasan asli dari pelanggan yang telah melakukan transaksi sewa mobil di INDOMAX.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $t): 
                    $pely = isset($t['rating_pelayanan']) ? intval($t['rating_pelayanan']) : 5;
                    $supir = isset($t['rating_supir']) ? intval($t['rating_supir']) : 5;
                    $mobil = isset($t['rating_mobil']) ? intval($t['rating_mobil']) : 5;
                    $stars = round(($pely + $supir + $mobil) / 3);
                    $initials = strtoupper(substr($t['nama'], 0, 2));
                ?>
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <!-- Stars -->
                        <div class="flex items-center gap-1 text-gold mb-6">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i data-lucide="star" class="w-5 h-5 <?= $i <= $stars ? 'fill-gold text-gold font-bold' : 'text-slate-300' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <!-- Review Text -->
                        <p class="text-slate-600 text-sm leading-relaxed italic mb-6">
                            "<?= htmlspecialchars($t['ulasan']) ?>"
                        </p>
                    </div>
                    <!-- Reviewer Info -->
                    <div class="flex items-center gap-3 pt-6 border-t border-slate-200/60">
                        <div class="w-10 h-10 rounded-full bg-bluebird-600 flex items-center justify-center text-white font-heading font-bold text-sm tracking-wider">
                            <?= $initials ?>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($t['nama']) ?></h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Pelanggan Terverifikasi</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 pt-16 pb-12 mt-auto border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-bluebird-600 flex items-center justify-center text-white shadow-lg shadow-bluebird-500/20">
                        <i data-lucide="car-front" class="w-5.5 h-5.5"></i>
                    </div>
                    <h2 class="font-heading font-black text-2xl text-white tracking-tight">INDOMAX</h2>
                </div>
                <p class="text-sm leading-relaxed max-w-sm mb-6 text-slate-400">
                    Solusi transportasi pintar, elegan, dan terpercaya. Kami berdedikasi untuk memberikan pengalaman berkendara kelas premium bagi setiap pelanggan.
                </p>
                <!-- Social Links for extra style -->
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/62881010715798" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:text-white hover:bg-bluebird-600 hover:border-bluebird-600 transition-all duration-300" title="WhatsApp">
                        <i class="bi bi-whatsapp text-base"></i>
                    </a>
                    <a href="mailto:indomax.rentcar@gmail.com" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:text-white hover:bg-bluebird-600 hover:border-bluebird-600 transition-all duration-300" title="Email">
                        <i class="bi bi-envelope-fill text-base"></i>
                    </a>
                    <a href="https://youtube.com/@INDOMAX-RENTCAR" target="_blank" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:text-white hover:bg-bluebird-600 hover:border-bluebird-600 transition-all duration-300" title="YouTube">
                        <i class="bi bi-youtube text-base"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:text-white hover:bg-bluebird-600 hover:border-bluebird-600 transition-all duration-300" title="Instagram">
                        <i class="bi bi-instagram text-base"></i>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500 hover:text-white hover:bg-bluebird-600 hover:border-bluebird-600 transition-all duration-300" title="Facebook">
                        <i class="bi bi-facebook text-base"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-heading font-bold text-white mb-5 tracking-wide text-sm uppercase">Navigasi</h4>
                <ul class="space-y-3.5 text-sm">
                    <li><a href="#home" class="hover:text-bluebird-400 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i> Home</a></li>
                    <li><a href="#armada" class="hover:text-bluebird-400 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i> Armada</a></li>
                    <li><a href="#layanan" class="hover:text-bluebird-400 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i> Layanan Kami</a></li>
                    <li><a href="#testimoni" class="hover:text-bluebird-400 transition-colors flex items-center gap-1.5"><i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-600"></i> Testimoni</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-heading font-bold text-white mb-5 tracking-wide text-sm uppercase">Hubungi Kami</h4>
                <ul class="space-y-3.5 text-sm">
                    <li class="flex items-center gap-2.5"><i data-lucide="map-pin" class="w-4 h-4 text-bluebird-500"></i> Semarang, Indonesia</li>
                    <li class="flex items-center gap-2.5"><i data-lucide="phone" class="w-4 h-4 text-bluebird-500"></i> +62 881-0107-15798</li>
                    <li class="flex items-center gap-2.5"><i data-lucide="mail" class="w-4 h-4 text-bluebird-500"></i> indomax.rentcar@gmail.com</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-16 pt-8 border-t border-slate-900 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 gap-4">
            <p>&copy; <?= date('Y') ?> PT INDOMAX RENTAL. Hak Cipta Dilindungi.</p>
            <!-- Tautan Rahasia Admin - Diperjelas sedikit & Elegan -->
            <a href="Admin/login_admin.php" class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-900 bg-slate-900/30 text-slate-600 hover:text-slate-400 hover:border-slate-800 transition-all duration-300" title="Portal Admin / Staff">
                <i class="bi bi-gear-fill text-sm"></i>
            </a>
        </div>
    </footer>

    <script>
        try {
            lucide.createIcons();
        } catch (e) {
            console.error("Lucide icons error: ", e);
        }

        // Hero Background Slider Logic
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        const totalSlides = slides.length;

        // Caption contents for each slide
        const captions = [
            {
                badge: "🏆 #1 Layanan Rental Mobil Terpercaya",
                title: "Perjalanan <span class='text-bluebird-500'>Premium.</span><br>Kapan Saja.",
                desc: "Sewa mobil mudah, aman, dan nyaman bersama INDOMAX. Didukung dengan armada terbaru dan supir profesional untuk setiap perjalanan Anda."
            },
            {
                badge: "🚗 Armada Terlengkap & Modern",
                title: "Pilihan Tepat<br>Untuk <span class='text-bluebird-500'>Keluarga Anda.</span>",
                desc: "Mulai dari Hatchback lincah, MPV keluarga luas, hingga SUV tangguh untuk perjalanan jarak jauh Anda. Semua dalam kondisi prima."
            },
            {
                badge: "🔑 Sewa Lepas Kunci Praktis",
                title: "Kebebasan Penuh<br>Menentukan <span class='text-bluebird-500'>Rute.</span>",
                desc: "Nikmati privasi berkendara maksimal bersama keluarga dengan pilihan sewa lepas kunci. Syarat mudah dan proses cepat."
            },
            {
                badge: "👨‍✈️ Sopir Profesional & Ramah",
                title: "Perjalanan Aman,<br>Bebas <span class='text-bluebird-500'>Lelah.</span>",
                desc: "Didampingi oleh pengemudi berpengalaman yang ramah, sopan, dan menguasai rute jalan untuk menjamin ketepatan waktu Anda."
            },
            {
                badge: "✨ Tarif Kompetitif & Jujur",
                title: "Harga Terbaik,<br>Tanpa <span class='text-bluebird-500'>Biaya Tak Terduga.</span>",
                desc: "Nikmati layanan sewa mobil kelas atas dengan harga sewa transparan, bersahabat, dan tanpa biaya tambahan tak terduga."
            },
            {
                badge: "💼 Layanan Eksekutif & Bisnis",
                title: "Kenyamanan Eksklusif<br>Kelas <span class='text-bluebird-500'>Premium.</span>",
                desc: "Tunjang aktivitas bisnis dan perjalanan dinas Anda dengan armada mewah terbaik. Kami menjamin prestise dan kenyamanan penuh."
            }
        ];

        const textContainer = document.getElementById('hero-text-container');
        const heroBadge = document.getElementById('hero-badge');
        const heroTitle = document.getElementById('hero-title');
        const heroDesc = document.getElementById('hero-desc');

        function changeSlide(index) {
            // Hide current slide and reset dot
            slides[currentSlide].classList.replace('opacity-100', 'opacity-0');
            dots[currentSlide].classList.replace('bg-white', 'bg-white/30');
            dots[currentSlide].classList.remove('w-6');
            dots[currentSlide].classList.add('w-2.5');

            currentSlide = index;

            // Show target slide and set dot active
            slides[currentSlide].classList.replace('opacity-0', 'opacity-100');
            dots[currentSlide].classList.replace('bg-white/30', 'bg-white');
            dots[currentSlide].classList.remove('w-2.5');
            dots[currentSlide].classList.add('w-6');

            // Fade out text container, swap text, and fade back in
            if (textContainer) {
                textContainer.classList.add('opacity-0');
                setTimeout(() => {
                    if (heroBadge) heroBadge.innerHTML = captions[index].badge;
                    if (heroTitle) heroTitle.innerHTML = captions[index].title;
                    if (heroDesc) heroDesc.innerHTML = captions[index].desc;
                    textContainer.classList.remove('opacity-0');
                }, 300);
            }
        }

        function nextSlide() {
            let nextIndex = (currentSlide + 1) % totalSlides;
            changeSlide(nextIndex);
        }

        let slideInterval = setInterval(nextSlide, 5000);

        function setSlide(index) {
            clearInterval(slideInterval);
            changeSlide(index);
            slideInterval = setInterval(nextSlide, 5000);
        }
        window.setSlide = setSlide;
    </script>

    <!-- Floating WhatsApp Widget -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end gap-4 font-sans">
        <!-- Chat Box Card (Tanya MINMAX) -->
        <div id="wa-chat-box" class="w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right">
            <!-- Card Header -->
            <div id="wa-chat-header" class="bg-bluebird-600 p-6 text-white relative select-none">
                <h3 class="font-heading font-bold text-lg mb-1">Info Tentang INDOMAX</h3>
                <p class="text-xs text-white/80 leading-relaxed">Silakan isi detail di bawah ini sebelum memulai percakapan chat dengan kami</p>
            </div>
            <!-- Card Body -->
            <div class="p-6 space-y-4">
                <div>
                    <label for="wa-name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Anda</label>
                    <div class="flex items-center gap-3 border border-slate-200 rounded-xl p-3 bg-slate-50 focus-within:border-bluebird-500 focus-within:bg-white transition-all">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                        <input type="text" id="wa-name" placeholder="Ketik nama Anda..." class="bg-transparent border-none outline-none text-sm w-full text-slate-700 placeholder-slate-400">
                    </div>
                </div>
                
                <button onclick="startWaChat()" class="w-full bg-bluebird-600 hover:bg-bluebird-700 text-white font-bold py-3.5 px-6 rounded-xl shadow-lg shadow-bluebird-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4"></i> Mulai Chatting
                </button>
                
                <div class="text-center text-[10px] text-slate-400 pt-1">
                    Didukung oleh Layanan Pelanggan INDOMAX
                </div>
            </div>
        </div>

        <!-- Toggle Button (Pill style like Tanya Bebi) -->
        <button id="wa-toggle-btn" onclick="toggleWaChat()" class="bg-bluebird-600 hover:bg-bluebird-700 text-white font-bold py-3 px-5 rounded-full shadow-2xl shadow-bluebird-600/30 transition-all hover:scale-105 active:scale-95 flex items-center gap-3">
            <span id="wa-btn-icon" class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-bluebird-600 shadow-sm transition-transform duration-300">
                <i class="bi bi-chat-text-fill text-base"></i>
            </span>
            <span id="wa-btn-text" class="text-sm tracking-wide font-semibold">TANYA MINMAX</span>
        </button>
    </div>

    <script>
        function toggleWaChat() {
            const chatBox = document.getElementById('wa-chat-box');
            const btnIcon = document.getElementById('wa-btn-icon');
            const btnText = document.getElementById('wa-btn-text');
            
            if (chatBox.classList.contains('hidden')) {
                // Open chat box
                chatBox.classList.remove('hidden');
                setTimeout(() => {
                    chatBox.classList.remove('scale-95', 'opacity-0');
                    chatBox.classList.add('scale-100', 'opacity-100');
                }, 10);
                
                // Change button to close state
                btnIcon.innerHTML = '<i class="bi bi-x-lg text-base"></i>';
                btnIcon.classList.add('rotate-90');
            } else {
                // Close chat box
                chatBox.classList.remove('scale-100', 'opacity-100');
                chatBox.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    chatBox.classList.add('hidden');
                }, 300);
                
                // Change button back to open state
                btnIcon.innerHTML = '<i class="bi bi-chat-text-fill text-base"></i>';
                btnIcon.classList.remove('rotate-90');
            }
        }

        function startWaChat() {
            const nameInput = document.getElementById('wa-name').value.trim();
            if (!nameInput) {
                alert('Silakan ketik nama Anda terlebih dahulu untuk memulai chat.');
                return;
            }
            const phoneNumber = '62881010715798'; // Number 62 881-0107-15798 in international format
            const text = encodeURIComponent(`Halo MinMax, saya ${nameInput} ingin bertanya tentang layanan rental mobil.`);
            const waUrl = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${text}`;
            window.open(waUrl, '_blank');
        }

    </script>
</body>
</html>
