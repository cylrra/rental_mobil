<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

include 'navbar.php';
include 'koneksi.php';

$id_sewa = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_sewa === 0) {
    echo "<script>alert('ID Transaksi tidak valid!'); window.location='transaksi.php';</script>";
    exit();
}

// Fetch transaction details
$query = mysqli_query($conn, "SELECT t.*, p.nama, m.merk, m.tarif_per_hari FROM transaksi_sewa t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan JOIN mobil m ON t.kode_mobil = m.kode_mobil WHERE t.id_sewa = $id_sewa");
if (!$query || mysqli_num_rows($query) === 0) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location='transaksi.php';</script>";
    exit();
}
$trx = mysqli_fetch_assoc($query);

// Handle Form Submission
if (isset($_POST['update'])) {
    $pake_supir_baru = mysqli_real_escape_string($conn, $_POST['pake_supir']);
    
    // Default to NULL driver
    $id_supir_db = "NULL";
    $biaya_supir_baru = 0;
    
    $lama_sewa = intval($trx['lama_sewa']);
    $tarif_mobil = floatval($trx['tarif_per_hari']);
    $total_biaya_mobil = $tarif_mobil * $lama_sewa;
    
    if ($pake_supir_baru === 'Ya' && !empty($_POST['id_supir'])) {
        $id_supir_val = intval($_POST['id_supir']);
        if ($id_supir_val > 0) {
            $id_supir_db = $id_supir_val;
            $tarif_supir = 200000; // Tagihan tambah 200 ribu per hari
            $biaya_supir_baru = $tarif_supir * $lama_sewa;
        } else {
            $pake_supir_baru = 'Tidak'; // Fallback if invalid ID
        }
    } else {
        $pake_supir_baru = 'Tidak'; // Force "Tidak" if no driver selected
    }
    
    $total_harga_baru = $total_biaya_mobil + $biaya_supir_baru;

    $update_query = "UPDATE transaksi_sewa SET 
                        pake_supir = '$pake_supir_baru', 
                        id_supir = $id_supir_db, 
                        biaya_supir = '$biaya_supir_baru', 
                        total_biaya = '$total_harga_baru' 
                     WHERE id_sewa = $id_sewa";
                     
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Transaksi berhasil diupdate!'); window.location='transaksi.php';</script>";
        exit();
    } else {
        $error_msg = mysqli_error($conn);
        echo "<script>alert('Gagal update transaksi: " . addslashes($error_msg) . "');</script>";
    }
    
    // Refresh data for view
    $query = mysqli_query($conn, "SELECT t.*, p.nama, m.merk, m.tarif_per_hari FROM transaksi_sewa t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan JOIN mobil m ON t.kode_mobil = m.kode_mobil WHERE t.id_sewa = $id_sewa");
    $trx = mysqli_fetch_assoc($query);
}

$current_page = 'transaksi.php';
?>

<div class="p-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Edit Transaksi #<?= $trx['id_sewa'] ?></h1>
            <p class="text-slate-500 mt-1 font-medium italic">Ubah detail layanan dan pilihan supir.</p>
        </div>
        <a href="transaksi.php" class="bg-slate-100 text-slate-600 font-bold py-2.5 px-5 rounded-xl hover:bg-slate-200 transition-colors flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <div class="flex justify-center">
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover-lift">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="block text-sm text-blue-500 font-bold mb-1">Pelanggan</span>
                            <span class="text-slate-800 font-bold"><?= htmlspecialchars($trx['nama']) ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-blue-500 font-bold mb-1">Mobil</span>
                            <span class="text-slate-800 font-bold"><?= htmlspecialchars($trx['merk']) ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-blue-500 font-bold mb-1">Lama Sewa</span>
                            <span class="text-slate-800 font-bold"><?= $trx['lama_sewa'] ?> Hari</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Gunakan Layanan Supir?</label>
                        <select name="pake_supir" id="pake_supir" onchange="toggleSupirBlock()" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-colors bg-slate-50">
                            <option value="Tidak" <?= ($trx['pake_supir'] == 'Tidak') ? 'selected' : '' ?>>Tidak (Lepas Kunci)</option>
                            <option value="Ya" <?= ($trx['pake_supir'] == 'Ya') ? 'selected' : '' ?>>Ya (Menggunakan Supir)</option>
                        </select>
                    </div>

                    <div id="pilihan_supir_block" style="display: <?= ($trx['pake_supir'] == 'Ya') ? 'block' : 'none' ?>;">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Supir (Tersedia)</label>
                        <input type="hidden" name="id_supir" id="id_supir_hidden" value="<?= $trx['id_supir'] ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <?php
                            // Supir yang tidak sedang memiliki transaksi 'berjalan' ATAU supir yang saat ini ditugaskan di transaksi ini
                            $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan' AND t.id_sewa != $id_sewa) = 0");
                            while($s = mysqli_fetch_array($supir_query)) {
                                $isSelected = ($trx['id_supir'] == $s['id_supir']) ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500 ring-opacity-50' : 'border-slate-200 bg-white hover:border-blue-300';
                            ?>
                            <div class="driver-card cursor-pointer border-2 rounded-xl p-4 transition-all duration-200 <?= $isSelected ?>" data-id="<?= $s['id_supir'] ?>" onclick="selectDriver(this)">
                                <div class="text-center">
                                    <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                        <i data-lucide="user" class="w-6 h-6 text-slate-500"></i>
                                    </div>
                                    <h6 class="mb-1 text-sm font-bold text-slate-800"><?= htmlspecialchars($s['nama_supir']) ?></h6>
                                    <span class="inline-block bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full text-xs">Rp 200.000/hari</span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <small class="text-rose-500 font-bold mt-2 hidden" id="error_supir">Silakan pilih supir terlebih dahulu.</small>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="update" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md shadow-blue-600/20 hover:bg-blue-700 transition-colors flex justify-center items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSupirBlock() {
        const status = document.getElementById("pake_supir").value;
        const block = document.getElementById("pilihan_supir_block");
        
        block.style.display = (status === "Ya") ? "block" : "none";
        if(status === "Tidak") {
            document.getElementById("id_supir_hidden").value = "";
            document.querySelectorAll('.driver-card').forEach(c => {
                c.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500', 'ring-opacity-50');
                c.classList.add('border-slate-200', 'bg-white');
            });
            document.getElementById("error_supir").classList.add('hidden');
        }
    }

    function selectDriver(element) {
        document.querySelectorAll('.driver-card').forEach(c => {
            c.classList.remove('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500', 'ring-opacity-50');
            c.classList.add('border-slate-200', 'bg-white');
        });
        element.classList.remove('border-slate-200', 'bg-white');
        element.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-500', 'ring-opacity-50');
        document.getElementById("id_supir_hidden").value = element.getAttribute('data-id');
        document.getElementById("error_supir").classList.add('hidden');
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const pakeSupir = document.getElementById("pake_supir").value;
        const idSupir = document.getElementById("id_supir_hidden").value;
        
        if(pakeSupir === "Ya" && !idSupir) {
            e.preventDefault();
            document.getElementById("error_supir").classList.remove('hidden');
        }
    });
</script>

    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
