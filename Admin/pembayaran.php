<?php 
include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Ambil ID dari URL (fitur otomatis dari tombol Bayar)
$id_pilihan = isset($_GET['id']) ? str_replace('#', '', trim($_GET['id'])) : '';

// 2. Hitung total biaya awal dari relasi tabel jika ID terpilih otomatis
$initial_tagihan = "";
if ($id_pilihan) {
    $id_pilihan_clean = mysqli_real_escape_string($conn, $id_pilihan);
    $res_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari 
                                  FROM transaksi_sewa t 
                                  JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                  LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                  WHERE t.id_sewa = '$id_pilihan_clean'");
    if ($res_t && $row_t = mysqli_fetch_assoc($res_t)) {
        if ($row_t['total_biaya'] > 0) {
            $initial_tagihan = $row_t['total_biaya'];
        } else {
            $tarif_mobil = $row_t['tarif_per_hari'];
            $tarif_supir = ($row_t['pake_supir'] == 'Ya' && !empty($row_t['tarif_supir_per_hari'])) ? $row_t['tarif_supir_per_hari'] : 0;
            $initial_tagihan = ($tarif_mobil + $tarif_supir) * $row_t['lama_sewa'];
        }
    }
}
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Manajemen Pembayaran</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Catat dan proses pembayaran sewa kendaraan dari pelanggan.</p>
    </div>

    <div class="flex justify-center">
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover-lift">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-[#800000]/10 text-[#800000] flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-800">Input Pembayaran Baru</h4>
                        <p class="text-sm text-slate-500 font-medium">Silakan isi detail pembayaran atau pilih transaksi lain</p>
                    </div>
                </div>
                <form action="proses_bayar.php" method="POST" class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">ID Transaksi / Sewa</label>
                            <select name="id_transaksi" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50" required>
                                <option value="">-- Klik untuk Pilih Transaksi --</option>
                                <?php 
                                // Ambil data transaksi berjalan dengan JOIN ke mobil & supir untuk menghitung total tagihan
                                $sql_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari,
                                                              (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayaran p WHERE p.id_sewa = t.id_sewa) as sudah_dibayar
                                                              FROM transaksi_sewa t 
                                                              JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                                              LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                                              WHERE t.status_sewa IN ('pending', 'diterima', 'berjalan') OR (t.status_sewa = 'selesai' AND t.jumlah_bayar < t.total_biaya)");
                                
                                while($t = mysqli_fetch_array($sql_t)){
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    
                                    // Tentukan total tagihan
                                    if ($t['total_biaya'] > 0) {
                                        $tagihan = $t['total_biaya'];
                                    } else {
                                        $tarif_mobil = $t['tarif_per_hari'];
                                        $tarif_supir = ($t['pake_supir'] == 'Ya' && !empty($t['tarif_supir_per_hari'])) ? $t['tarif_supir_per_hari'] : 0;
                                        $tagihan = ($tarif_mobil + $tarif_supir) * $t['lama_sewa'];
                                    }
                                    
                                    $sudah_dibayar = $t['sudah_dibayar'];
                                    
                                    echo "<option value='".$id_db."' data-tagihan='".$tagihan."' data-dibayar='".$sudah_dibayar."' $selected>";
                                    echo "#SRV-".$id_db." (Mobil: ".$t['kode_mobil'].")";
                                    echo "</option>";
                                }
                                ?>
                            </select>
                            <?php if($id_pilihan): ?>
                                <div class="mt-2 text-sm text-emerald-600 font-medium flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i> ID Transaksi #<?= htmlspecialchars($id_pilihan) ?> terpilih otomatis.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Bayar</label>
                                <input type="date" name="tgl_bayar" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                                <select name="metode_bayar" id="metode_bayar" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50" required onchange="toggleBank()">
                                    <option value="cash">Cash / Tunai</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="ewallet">E-Wallet (GoPay / OVO)</option>
                                </select>
                            </div>
                            <div id="bank_container" style="display: none;">
                                <label class="block text-sm font-bold text-slate-700 mb-2">Tujuan / Kas</label>
                                <select name="bank_tujuan" id="bank_tujuan" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50">
                                    <!-- Options will be populated by JS -->
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Pembayaran</label>
                                <select name="jenis_pembayaran" class="w-full rounded-xl border-slate-200 px-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50" required>
                                    <option value="dp">Uang Muka (DP)</option>
                                    <option value="pelunasan">Pelunasan / Lunas</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah Bayar (Rp)</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-4 font-bold text-[#800000]">Rp</span>
                                <!-- Input text visual untuk tampilan formal/baku dengan separator ribuan -->
                                <input type="text" id="jumlah_bayar_formatted" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 focus:border-[#800000] focus:ring focus:ring-[#800000]/20 transition-colors bg-slate-50 font-bold text-[#800000] text-lg" placeholder="Pilih transaksi terlebih dahulu" value="<?php echo !empty($initial_tagihan) ? number_format($initial_tagihan, 0, ',', '.') : ''; ?>" readonly required>
                                <!-- Input hidden raw untuk dikirimkan ke database agar query SQL tetap berjalan normal -->
                                <input type="hidden" name="jumlah_bayar" id="jumlah_bayar" value="<?php echo htmlspecialchars($initial_tagihan); ?>">
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 pt-2">
                            <button type="submit" name="simpan_pembayaran" class="w-full bg-[#d4af37] text-[#1a1c1c] font-bold py-3 rounded-xl shadow-md shadow-[#d4af37]/20 hover:bg-[#c49d2b] transition-colors flex justify-center items-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i> Simpan Pembayaran & Posting Jurnal
                            </button>
                            <a href="riwayat_pembayaran.php" class="w-full bg-[#800000] text-white font-bold py-3 rounded-xl hover:bg-[#600000] transition-colors flex justify-center items-center">
                                Lihat Riwayat
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectTransaksi = document.querySelector('select[name="id_transaksi"]');
    const selectJenisBayar = document.querySelector('select[name="jenis_pembayaran"]');
    const inputJumlahBayar = document.getElementById('jumlah_bayar');
    const inputJumlahBayarFormatted = document.getElementById('jumlah_bayar_formatted');
    
    window.toggleBank = function() {
        var metode = document.getElementById('metode_bayar').value;
        var bankContainer = document.getElementById('bank_container');
        var bankSelect = document.getElementById('bank_tujuan');
        var labelBank = document.querySelector('#bank_container label');
        
        if (metode === 'transfer') {
            bankContainer.style.display = 'block';
            labelBank.innerText = 'Bank Tujuan';
            bankSelect.innerHTML = '<option value="">-- Pilih Bank Tujuan --</option><option value="1121">Bank BCA (123456789)</option><option value="1122">Bank BNI (987654321)</option><option value="1123">Bank Mandiri (1122334455)</option>';
            bankSelect.required = true;
        } else if (metode === 'ewallet') {
            bankContainer.style.display = 'block';
            labelBank.innerText = 'Pilih E-Wallet';
            bankSelect.innerHTML = '<option value="">-- Pilih E-Wallet --</option><option value="gopay">GoPay</option><option value="ovo">OVO</option>';
            bankSelect.required = true;
        } else {
            bankContainer.style.display = 'none';
            bankSelect.required = false;
        }
    };
    toggleBank();
    
    function updateJumlahBayar() {
        if (selectTransaksi && inputJumlahBayar && inputJumlahBayarFormatted && selectJenisBayar) {
            const selectedOption = selectTransaksi.options[selectTransaksi.selectedIndex];
            if (selectedOption && selectedOption.value !== "") {
                const tagihan = parseFloat(selectedOption.getAttribute('data-tagihan'));
                const sudahDibayar = parseFloat(selectedOption.getAttribute('data-dibayar'));
                const jenis = selectJenisBayar.value;
                
                let nominalBayar = 0;
                
                if (jenis === 'dp') {
                    // DP adalah 50% dari total tagihan
                    nominalBayar = tagihan * 0.5;
                    // Jika sudah pernah bayar DP (sudah_dibayar > 0), DP tidak boleh lagi atau sesuaikan
                    if (sudahDibayar >= (tagihan * 0.5)) {
                        alert("Uang muka (DP) untuk transaksi ini sudah dibayar sebelumnya.");
                        nominalBayar = 0;
                        selectJenisBayar.value = 'pelunasan';
                        updateJumlahBayar();
                        return;
                    }
                } else if (jenis === 'pelunasan') {
                    // Pelunasan adalah sisa tagihan
                    nominalBayar = tagihan - sudahDibayar;
                    if (nominalBayar < 0) nominalBayar = 0;
                }
                
                if (nominalBayar >= 0) {
                    inputJumlahBayar.value = nominalBayar;
                    const formatted = new Intl.NumberFormat('id-ID').format(nominalBayar);
                    inputJumlahBayarFormatted.value = formatted;
                }
            } else {
                inputJumlahBayar.value = '';
                inputJumlahBayarFormatted.value = '';
            }
        }
    }
    
    if (selectTransaksi && inputJumlahBayar && selectJenisBayar) {
        updateJumlahBayar();
        selectTransaksi.addEventListener('change', updateJumlahBayar);
        selectJenisBayar.addEventListener('change', updateJumlahBayar);
    }
});
</script>

</div> </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>