<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}
include 'navbar.php';
include 'koneksi.php';
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif; color: var(--deep-navy); font-size: 1.75rem;">
            <i class="bi bi-headset me-2" style="color: var(--clear-blue);"></i>Bantuan & Layanan CS
        </h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">Temukan jawaban atas pertanyaan Anda atau hubungi tim kami langsung.</p>
    </div>
</div>

<div class="row g-4">

    <!-- ═══ CONTACT CARDS ═══ -->
    <div class="col-12">
        <div class="row g-3 mb-4">
            <!-- WhatsApp -->
            <div class="col-md-4">
                <div class="card h-100 text-center p-4" style="border: 1px solid rgba(37,211,102,0.25); background: linear-gradient(135deg, rgba(37,211,102,0.04), rgba(37,211,102,0.08));">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background: rgba(37,211,102,0.15); border-radius: 16px;">
                        <i class="bi bi-whatsapp" style="font-size: 1.6rem; color: #25d366;"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color: var(--deep-navy);">Live Chat WhatsApp</h6>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">Hubungi CS kami langsung via WhatsApp. Respon cepat jam kerja.</p>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20INDOMAX%20RENTAL%2C%20saya%20ingin%20bertanya..." 
                       target="_blank"
                       class="btn fw-bold rounded-pill px-4"
                       style="background: #25d366; color: white; border: none; font-size: 0.85rem;">
                        <i class="bi bi-whatsapp me-1"></i> Chat Sekarang
                    </a>
                </div>
            </div>
            <!-- Email -->
            <div class="col-md-4">
                <div class="card h-100 text-center p-4" style="border: 1px solid rgba(158,0,0,0.25); background: linear-gradient(135deg, rgba(158,0,0,0.04), rgba(158,0,0,0.08));">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background: rgba(158,0,0,0.12); border-radius: 16px;">
                        <i class="bi bi-envelope-fill" style="font-size: 1.6rem; color: var(--clear-blue);"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color: var(--deep-navy);">Email Kami</h6>
                    <p class="text-muted mb-3" style="font-size: 0.82rem;">Kirim pertanyaan detail melalui email. Dibalas dalam 1x24 jam.</p>
                    <a href="mailto:cs@indomaxrental.co.id" 
                       class="btn fw-bold rounded-pill px-4"
                       style="background: linear-gradient(135deg, var(--clear-blue), var(--deep-navy)); color: white; border: none; font-size: 0.85rem;">
                        <i class="bi bi-envelope me-1"></i> Kirim Email
                    </a>
                </div>
            </div>
            <!-- Jam Operasional -->
            <div class="col-md-4">
                <div class="card h-100 text-center p-4" style="border: 1px solid rgba(184,170,180,0.3); background: linear-gradient(135deg, rgba(235,246,252,0.5), rgba(184,170,180,0.08));">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background: rgba(184,170,180,0.2); border-radius: 16px;">
                        <i class="bi bi-clock-fill" style="font-size: 1.6rem; color: var(--lilac-dust);"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color: var(--deep-navy);">Jam Operasional</h6>
                    <div style="font-size: 0.82rem; color: #555; line-height: 1.8;">
                        <div><strong>Senin – Jumat</strong><br>08.00 – 17.00 WIB</div>
                        <hr class="my-2" style="border-color: rgba(184,170,180,0.3);">
                        <div><strong>Sabtu</strong><br>08.00 – 13.00 WIB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ FAQ ACCORDION ═══ -->
    <div class="col-lg-8">
        <div class="card" style="border: 1px solid rgba(158,0,0,0.25);">
            <div class="card-header bg-white border-0 py-3 px-4"
                 style="border-bottom: 1px solid rgba(158,0,0,0.2) !important; border-radius: 16px 16px 0 0;">
                <h5 class="fw-bold m-0" style="color: var(--deep-navy); font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-patch-question me-2" style="color: var(--clear-blue);"></i>Pertanyaan yang Sering Diajukan
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="accordion accordion-flush" id="faqAccordion">

                    <?php
                    $faqs = [
                        [
                            "q" => "Bagaimana cara menyewa mobil di INDOMAX?",
                            "a" => "Pilih mobil di menu <strong>Katalog Armada</strong>, klik tombol <em>Sewa</em>, lalu isi formulir pemesanan (tanggal, durasi, pilihan sopir). Setelah submit, lakukan pembayaran DP atau lunas di menu <strong>Input Pembayaran</strong>."
                        ],
                        [
                            "q" => "Apa bedanya sewa Lepas Kunci dan Pakai Sopir?",
                            "a" => "<strong>Lepas Kunci</strong>: Anda mengendarai sendiri tanpa sopir. Memerlukan verifikasi KTP dan SIM A yang aktif. <strong>Pakai Sopir</strong>: Kami menyediakan sopir profesional dengan tarif tambahan Rp 200.000/hari. Bebas verifikasi dokumen."
                        ],
                        [
                            "q" => "Bagaimana cara verifikasi akun saya?",
                            "a" => "Pergi ke menu <strong>Pengaturan Akun → Verifikasi Identitas</strong>, lalu unggah foto KTP dan SIM A Anda. Tim kami akan mereview dalam 1–2 hari kerja dan mengubah status menjadi <em>Terverifikasi</em>."
                        ],
                        [
                            "q" => "Metode pembayaran apa saja yang tersedia?",
                            "a" => "Kami menerima pembayaran via <strong>Transfer Bank</strong>, <strong>E-Wallet</strong> (GoPay, OVO, Dana), dan <strong>Cash di Tempat</strong>. Tersedia opsi DP (uang muka) atau langsung Lunas."
                        ],
                        [
                            "q" => "Bagaimana jika saya ingin membatalkan pesanan?",
                            "a" => "Hubungi CS kami melalui WhatsApp atau email sebelum tanggal sewa dimulai. Kebijakan pembatalan: lebih dari 24 jam sebelum sewa = refund penuh, kurang dari 24 jam = refund 50%."
                        ],
                        [
                            "q" => "Apakah ada biaya denda keterlambatan pengembalian?",
                            "a" => "Ya, keterlambatan pengembalian dikenakan denda sebesar <strong>Rp 50.000/jam</strong> atau maksimal 1x tarif harian jika lebih dari 6 jam."
                        ],
                        [
                            "q" => "Bagaimana cara memberikan ulasan/rating?",
                            "a" => "Menu <strong>Ulasan & Rating</strong> akan aktif setelah status sewa Anda berubah menjadi <em>Selesai</em>. Anda bisa menilai pelayanan, sopir, dan kondisi mobil dengan bintang 1–5 beserta komentar."
                        ],
                        [
                            "q" => "Apakah saya bisa melacak posisi mobil yang saya sewa?",
                            "a" => "Fitur tracking tersedia untuk pemesanan dengan sopir. Koordinat real-time dapat dipantau oleh admin dan informasi lokasi dapat dikomunikasikan kepada pelanggan melalui CS kami."
                        ],
                    ];

                    foreach ($faqs as $i => $faq):
                    ?>
                    <div class="accordion-item border-0 mb-2" style="border-radius: 12px; overflow: hidden; background: var(--frost-veil); border: 1px solid rgba(158,0,0,0.2) !important;">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>"
                                    style="background: var(--frost-veil); color: var(--deep-navy); font-size: 0.9rem; border-radius: 12px; box-shadow: none;">
                                <i class="bi bi-question-circle me-2" style="color: var(--clear-blue);"></i>
                                <?= $faq['q'] ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body pt-0" style="font-size: 0.875rem; color: #555; line-height: 1.7; padding-left: 2.5rem;">
                                <?= $faq['a'] ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- ═══ KONTAK & INFO SIDEBAR ═══ -->
    <div class="col-lg-4">

        <!-- Info Kantor -->
        <div class="card mb-4" style="border: 1px solid rgba(158,0,0,0.25);">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: var(--deep-navy); font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-geo-alt-fill me-2" style="color: var(--clear-blue);"></i>Lokasi Kantor
                </h6>
                <div style="font-size: 0.85rem; color: #555; line-height: 1.8;">
                    <div class="d-flex gap-2 mb-2">
                        <i class="bi bi-building" style="color: var(--lilac-dust); margin-top: 2px;"></i>
                        <span>PT INDOMAX RENTAL MOBIL<br>Jl. Pemuda No. 123, Semarang Tengah,<br>Kota Semarang 50132</span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <i class="bi bi-telephone" style="color: var(--lilac-dust); margin-top: 2px;"></i>
                        <span>(024) 8812-3456</span>
                    </div>
                    <div class="d-flex gap-2">
                        <i class="bi bi-envelope" style="color: var(--lilac-dust); margin-top: 2px;"></i>
                        <span>cs@indomaxrental.co.id</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Tip Card -->
        <div class="card" style="background: linear-gradient(135deg, var(--primary), #600000); border: none; color: white;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: var(--light-blue);">
                    <i class="bi bi-lightbulb me-2"></i>Tips Sewa Lebih Hemat
                </h6>
                <ul class="list-unstyled mb-0" style="font-size: 0.82rem; line-height: 2; color: rgba(255,255,255,0.8);">
                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--light-blue);"></i>Sewa minimal 3 hari untuk harga terbaik</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--light-blue);"></i>Gunakan kode promo INDOMAXWEEKEND</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--light-blue);"></i>Verifikasi akun untuk opsi lepas kunci</li>
                    <li><i class="bi bi-check-circle-fill me-2" style="color: var(--light-blue);"></i>Booking H-1 untuk ketersediaan terjamin</li>
                </ul>
                <a href="katalog.php" class="btn btn-sm rounded-pill px-4 mt-3 fw-bold"
                   style="background: var(--light-blue); color: var(--deep-navy); border: none; font-size: 0.8rem;">
                    Cari Mobil Sekarang <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

    </div>
</div>

<style>
.accordion-button::after {
    filter: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%239e0000'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
.accordion-button:not(.collapsed) {
    background: rgba(158,0,0,0.08) !important;
    color: var(--clear-blue) !important;
}
</style>

<!-- Footer -->
</div> </div> </div> </body>
</html>
