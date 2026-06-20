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
        $tarif_mobil = $row_t['tarif_per_hari'];
        $tarif_supir = ($row_t['opsi_supir'] == 'ya' && !empty($row_t['tarif_supir_per_hari'])) ? $row_t['tarif_supir_per_hari'] : 0;
        $initial_tagihan = ($tarif_mobil + $tarif_supir) * $row_t['lama_sewa'];
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i> Input Pembayaran Baru</h4>
                    <p class="mb-0 opacity-75">Silakan isi detail pembayaran atau pilih transaksi lain</p>
                </div>
                <div class="card-body p-4">
                    <form action="proses_bayar.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Transaksi / Sewa</label>
                            <select name="id_transaksi" class="form-select form-select-lg" required>
                                <option value="">-- Klik untuk Pilih Transaksi --</option>
                                <?php 
                                // Ambil data transaksi berjalan dengan JOIN ke mobil & supir untuk menghitung total tagihan
                                $sql_t = mysqli_query($conn, "SELECT t.*, m.tarif_per_hari, s.tarif_supir_per_hari 
                                                              FROM transaksi_sewa t 
                                                              JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                                              LEFT JOIN supir s ON t.id_supir = s.id_supir 
                                                              WHERE t.status_sewa = 'berjalan'");
                                
                                while($t = mysqli_fetch_array($sql_t)){
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    
                                    // Hitung total tagihan: (tarif mobil + tarif supir jika sewa pakai supir) * lama sewa
                                    $tarif_mobil = $t['tarif_per_hari'];
                                    $tarif_supir = ($t['opsi_supir'] == 'ya' && !empty($t['tarif_supir_per_hari'])) ? $t['tarif_supir_per_hari'] : 0;
                                    $tagihan = ($tarif_mobil + $tarif_supir) * $t['lama_sewa'];
                                    
                                    echo "<option value='".$id_db."' data-tagihan='".$tagihan."' $selected>";
                                    echo "#SRV-".$id_db." (Mobil: ".$t['kode_mobil'].")";
                                    echo "</option>";
                                }
                                ?>
                            </select>
                            <?php if($id_pilihan): ?>
                                <div class="form-text text-success">
                                    <i class="bi bi-check2-circle"></i> ID Transaksi #<?= htmlspecialchars($id_pilihan) ?> terpilih otomatis.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Bayar</label>
                                <input type="date" name="tgl_bayar" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <select name="metode_bayar" class="form-select" required>
                                    <option value="Tunai">Tunai</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah Bayar (Rp)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light text-primary fw-bold">Rp</span>
                                <!-- Input text visual untuk tampilan formal/baku dengan separator ribuan -->
                                <input type="text" id="jumlah_bayar_formatted" class="form-control text-primary fw-bold" placeholder="Pilih transaksi terlebih dahulu" value="<?php echo !empty($initial_tagihan) ? number_format($initial_tagihan, 0, ',', '.') : ''; ?>" readonly required>
                                <!-- Input hidden raw untuk dikirimkan ke database agar query SQL tetap berjalan normal -->
                                <input type="hidden" name="jumlah_bayar" id="jumlah_bayar" value="<?php echo htmlspecialchars($initial_tagihan); ?>">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="simpan_pembayaran" class="btn btn-primary btn-lg rounded-3">
                                <i class="bi bi-save me-2"></i> Simpan Pembayaran & Posting Jurnal
                            </button>
                            <a href="riwayat_pembayaran.php" class="btn btn-light text-muted">Lihat Riwayat</a>
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
    const inputJumlahBayar = document.getElementById('jumlah_bayar');
    const inputJumlahBayarFormatted = document.getElementById('jumlah_bayar_formatted');
    
    function updateJumlahBayar() {
        if (selectTransaksi && inputJumlahBayar && inputJumlahBayarFormatted) {
            const selectedOption = selectTransaksi.options[selectTransaksi.selectedIndex];
            if (selectedOption) {
                const tagihan = selectedOption.getAttribute('data-tagihan');
                if (tagihan) {
                    // Isi nilai raw ke hidden input untuk database
                    inputJumlahBayar.value = tagihan;
                    
                    // Format ke rupiah baku (separator titik) untuk visual input text
                    const formatted = new Intl.NumberFormat('id-ID').format(tagihan);
                    inputJumlahBayarFormatted.value = formatted;
                } else {
                    inputJumlahBayar.value = '';
                    inputJumlahBayarFormatted.value = '';
                }
            }
        }
    }
    
    if (selectTransaksi && inputJumlahBayar) {
        // 1. Jalankan langsung saat halaman selesai dimuat (on load)
        updateJumlahBayar();
        
        // 2. Jalankan setiap kali pilihan dropdown berubah (on change)
        selectTransaksi.addEventListener('change', updateJumlahBayar);
    }
});
</script>