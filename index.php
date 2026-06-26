<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>INDOMAX RENTAL MOBIL - Portal Masuk</title>
    <!-- Google Fonts Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-[#1a1c1c] text-[#f9f9f9] min-h-screen flex flex-col justify-between overflow-x-hidden relative font-sans">
    
    <!-- Background accents -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-[#9e0000]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#fdc003]/5 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header / Brand -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-[#9e0000] shadow-md shadow-[#9e0000]/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125a1.125 1.125 0 0 0 1.125-1.125V9.75M8.25 9.75a3.75 3.75 0 0 1 7.5 0M8.25 9.75h7.5m-7.5 0H3.375A1.125 1.125 0 0 0 2.25 10.875v5.625M15.75 9.75h3.375c.621 0 1.125.504 1.125 1.125v5.625M12 18.75v-6" />
                </svg>
            </div>
            <div>
                <h1 class="font-extrabold text-lg tracking-tight leading-none">INDOMAX</h1>
                <p class="text-[9px] font-bold text-[#fdc003] uppercase tracking-widest mt-0.5">Rental System</p>
            </div>
        </div>
        <div class="text-xs font-semibold text-[#dadada]">
            v2.1 Premium
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-4xl mx-auto px-6 py-12 flex flex-col items-center text-center justify-center flex-grow z-10">
        <span class="inline-block bg-[#9e0000]/20 text-[#ffdad4] text-xs font-bold px-4 py-1.5 rounded-full mb-4 border border-[#9e0000]/30 tracking-wider uppercase">
            Sistem Kelola & Sewa Armada
        </span>
        <h2 class="text-4xl md:text-5xl font-black tracking-tight text-white mb-4 max-w-2xl leading-tight">
            PERFORMA PRIMA,<br><span class="text-[#fdc003]">KENDALI SEPENUHNYA.</span>
        </h2>
        <p class="text-[#dadada] text-sm md:text-base max-w-lg mb-8 leading-relaxed font-medium">
            Selamat datang di portal PT INDOMAX RENTAL. Silakan pilih portal masuk di bawah untuk menyewa armada atau mengelola sistem.
        </p>

        <!-- Jam Operasional -->
        <div class="mb-10 text-[#dadada] text-sm">
            <h4 class="font-bold text-[#fdc003] mb-3 uppercase tracking-widest text-xs">Jam Operasional Layanan</h4>
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <span class="bg-[#9e0000]/20 px-4 py-2 rounded-lg border border-[#9e0000]/30"><strong class="text-white">Senin - Jumat:</strong> 08:00 - 20:00 WIB</span>
                <span class="bg-[#fdc003]/10 px-4 py-2 rounded-lg border border-[#fdc003]/20"><strong class="text-white">Sabtu - Minggu:</strong> 09:00 - 21:00 WIB</span>
            </div>
        </div>

        <!-- Gateway Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl">
            <!-- Pelanggan Portal -->
            <div class="bg-[#2f3131] border border-[#4d4c4c] p-8 rounded-xl text-left hover:border-[#fdc003]/50 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-lg bg-[#fdc003]/10 text-[#fdc003] flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Portal Pelanggan</h3>
                    <p class="text-[#dadada] text-xs leading-relaxed mb-6 font-medium">
                        Cari mobil impian Anda, pesan cepat secara online, dan nikmati perjalanan tanpa hambatan.
                    </p>
                </div>
                <a href="Pelanggan/login.php" class="w-full bg-[#fdc003] text-[#1a1c1c] font-bold py-3 px-4 rounded-lg text-center hover:bg-[#fdc003]/90 transition-all duration-200 shadow-md shadow-[#fdc003]/10 block">
                    Masuk Pelanggan
                </a>
            </div>

            <!-- Admin Portal -->
            <div class="bg-[#2f3131] border border-[#4d4c4c] p-8 rounded-xl text-left hover:border-[#9e0000]/50 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-lg bg-[#9e0000]/20 text-[#ffb4a8] flex items-center justify-center mb-6 transition-transform group-hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Portal Admin</h3>
                    <p class="text-[#dadada] text-xs leading-relaxed mb-6 font-medium">
                        Kelola data armada, jadwal servis, verifikasi transaksi sewa, dan pantau pembukuan sistem.
                    </p>
                </div>
                <a href="Admin/login.php" class="w-full bg-[#9e0000] text-white font-bold py-3 px-4 rounded-lg text-center hover:bg-[#9e0000]/90 transition-all duration-200 shadow-md shadow-[#9e0000]/10 block">
                    Masuk Staff / Admin
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-6 text-xs text-[#dadada] border-t border-[#4d4c4c]/30 z-10">
        &copy; 2026 PT INDOMAX RENTAL. All Rights Reserved. Meticulously Crafted Dashboard.
    </footer>
</body>
</html>
