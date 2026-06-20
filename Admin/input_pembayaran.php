<?php 
include 'navbar.php'; 
include 'koneksi.php'; 

// 1. Ambil ID dari URL dan bersihkan (Gunakan trim agar tidak ada spasi yang mengganggu)
$id_pilihan = isset($_GET['id']) ? trim($_GET['id']) : '';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <h4 class="fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i> Input Pembayaran Baru</h4>
                    <p class="mb-0 opacity-75">Silakan isi detail pembayaran sewa mobil</p>
                </div>
                <div class="card-body p-4">
                    <form action="proses_bayar.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID Transaksi / Sewa</label>
                            <select name="id_transaksi" class="form-select" required>
                                <option value="">-- Pilih Transaksi --</option>
                                <?php 
                                // Ambil data transaksi yang statusnya 'Mulai'
                                $sql_t = mysqli_query($conn, "SELECT * FROM transaksi_sewa WHERE status_sewa = 'Mulai'");
                                
                                while($t = mysqli_fetch_array($sql_t)){
                                    // 2. LOGIKA PENCOCOKAN YANG LEBIH KUAT:
                                    // Bandingkan ID dari DB dan URL setelah di-trim
                                    $id_db = trim($t['id_sewa']);
                                    $selected = ($id_db == $id_pilihan) ? "selected" : "";
                                    
                                    echo "<option value='".$id_db."' $selected>";
                                    echo "#SRV-".$id_db." (Mobil: ".$t['kode_mobil'].")";
                                    echo "</option>";
                                }
                                ?>
                            </select>
                            <?php if($id_pilihan): ?>
                                <div class="form-text text-success">
                                    <i class="bi bi-check2-circle"></i> ID Transaksi #<?= htmlspecialchars($id_pilihan) ?> telah terpilih otomatis.
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
                            <input type="number" name="jumlah_bayar" class="form-control form-control-lg text-primary fw-bold" placeholder="Contoh: 500000" required>
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