<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
?>

<div class="flex items-center justify-center py-10 px-4">
    <div class="glass-card w-full max-w-2xl rounded-3xl p-8 md:p-10 border border-slate-200 relative overflow-hidden">
        
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="mb-8 relative z-10">
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center text-white"><i data-lucide="car-front" class="w-5 h-5"></i></div>
                Tambah Armada Baru
            </h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Lengkapi formulir di bawah ini untuk mendaftarkan kendaraan operasional ke dalam sistem.</p>
        </div>

        <form action="mobil_tambah_proses.php" method="POST" enctype="multipart/form-data" class="space-y-5 relative z-10">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Kode Mobil</label>
                    <input type="text" name="kode_mobil" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" placeholder="Contoh: M001" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Plat Nomor (Nopol)</label>
                    <input type="text" name="nopol" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" placeholder="Contoh: B 1234 ABC" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Merk / Model</label>
                    <input type="text" name="merk" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" placeholder="Contoh: Toyota Avanza" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Jenis Kendaraan</label>
                    <input type="text" name="jenis" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" placeholder="Contoh: MPV" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 12 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_12_dalam" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 12 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_12_luar" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 24 Jam (Dalam Kota)</label>
                    <input type="number" name="tarif_24_dalam" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif 24 Jam (Luar Kota)</label>
                    <input type="number" name="tarif_24_luar" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Tarif Per Hari (Lama/Opsional)</label>
                    <input type="number" name="tarif_per_hari" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" placeholder="Contoh: 300000" value="0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Jumlah Unit (Stok)</label>
                    <input type="number" name="Unit_Tersedia" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" value="1" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Foto Mobil</label>
                <div class="w-full bg-slate-50 border border-slate-200 border-dashed rounded-xl p-4 text-center hover:bg-slate-100 transition-colors">
                    <input type="file" name="gambar" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100" accept="image/*" required>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4 mt-6 border-t border-slate-100">
                <a href="mobil.php" class="flex-1 py-3 border border-slate-200 text-slate-600 font-bold text-sm text-center rounded-xl hover:bg-slate-50 transition-all">Batal</a>
                <button type="submit" name="simpan" class="flex-1 py-3 bg-brand-500 text-white font-bold text-sm text-center rounded-xl shadow-md shadow-brand-500/30 hover:bg-brand-600 transition-all">Simpan Armada</button>
            </div>
        </form>
    </div>
</div>

        </div> 
    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>