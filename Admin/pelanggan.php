<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

// Mengambil total pelanggan
$query_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM pelanggan");
$data_total = mysqli_fetch_assoc($query_total);
$total_pelanggan = $data_total['total'] ?? 0;
?>

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Data Pelanggan</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Kelola daftar konsumen dan riwayat penyewa armada Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <span class="bg-blue-50 text-blue-600 px-4 py-2.5 rounded-xl text-sm font-bold border border-blue-100 flex items-center gap-2">
            <i data-lucide="users" class="w-4 h-4"></i> <?php echo $total_pelanggan; ?> Terdaftar
        </span>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md shadow-blue-600/20 hover:bg-blue-700 transition-all flex items-center gap-2 cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pelanggan
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm mb-10 hover-lift">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
        <h5 class="text-lg font-bold text-slate-800">Daftar Lengkap Pelanggan</h5>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider w-16">No</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Identitas Pelanggan</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Kontak (No. HP)</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</th>
                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                <?php
                // SESUAIKAN NAMA TABEL DAN KOLOM DENGAN DATABASE ANDA
                $sql_pelanggan = "SELECT * FROM pelanggan ORDER BY id_pelanggan DESC";
                $query = mysqli_query($conn, $sql_pelanggan);
                $no = 1;
                
                if (mysqli_num_rows($query) > 0) {
                    while($row = mysqli_fetch_array($query)) {
                ?>
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="py-4 px-6 text-sm font-medium text-slate-500"><?php echo $no++; ?></td>
                    <td class="py-4 px-6">
                        <p class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($row['nama']); ?></p>
                        <p class="text-[11px] font-medium text-slate-400 mt-0.5">KTP: <?php echo htmlspecialchars($row['no_ktp'] ?? '-'); ?></p>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex flex-col gap-2">
                            <span class="inline-flex items-center justify-between text-sm font-medium text-slate-600 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 group/wa">
                                <span class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> <?php echo htmlspecialchars($row['no_telp']); ?></span>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $row['no_telp']); ?>" target="_blank" class="text-emerald-500 hover:text-emerald-600 ml-2" title="Chat WhatsApp">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </span>
                            
                            <?php if(!empty($row['email'])): ?>
                            <span class="inline-flex items-center justify-between text-sm font-medium text-slate-600 bg-slate-100 px-3 py-1 rounded-lg border border-slate-200 group/mail">
                                <span class="flex items-center gap-1.5 truncate max-w-[150px]" title="<?= htmlspecialchars($row['email']) ?>"><i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i> <?= htmlspecialchars($row['email']) ?></span>
                                <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-rose-500 hover:text-rose-600 ml-2" title="Kirim Email">
                                    <i class="bi bi-envelope-fill"></i>
                                </a>
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm text-slate-600 truncate max-w-xs" title="<?php echo htmlspecialchars($row['alamat']); ?>">
                            <?php echo htmlspecialchars($row['alamat']); ?>
                        </p>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="pelanggan_edit.php?id=<?php echo $row['id_pelanggan']; ?>" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-colors" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <a href="pelanggan_hapus.php?id=<?php echo $row['id_pelanggan']; ?>" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors" title="Hapus" onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    } 
                } else {
                    echo '<tr><td colspan="5" class="py-10 text-center text-slate-500 font-medium">Belum ada data pelanggan yang terdaftar.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalTambah" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center transition-all">
    <div class="relative w-full max-w-lg mx-4">
        <form action="pelanggan_tambah_proses.php" method="POST" class="bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h5 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 text-brand-500"></i> Tambah Pelanggan
                </h5>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Email Address</label>
                        <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" rows="3" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">No. Telepon / WA</label>
                        <input type="number" name="no_telp" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">No. KTP</label>
                        <input type="number" name="no_ktp" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-brand-500 focus:border-brand-500 p-3" required>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" name="btn_simpan" class="px-5 py-2.5 text-sm font-bold text-white bg-brand-500 rounded-xl shadow-md shadow-brand-500/30 hover:bg-brand-600 transition-colors">
                    Simpan Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>

</div> 
    </main>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>