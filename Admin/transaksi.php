<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Sertakan navbar dan koneksi (asumsi nama file sudah benar)
include 'navbar.php'; 
include 'koneksi.php'; 

// Tangkap kode mobil dari URL (jika ada, misalnya dari halaman detail mobil)
$kode_selected = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="container-fluid px-4 py-5">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Input Transaksi Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="proses_transaksi.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pelanggan</label>
                            <select name="id_pelanggan" class="form-select select2-js" required>
                                <option value="">-- Cari Pelanggan --</option>
                                <?php
                                $pel = mysqli_query($conn, "SELECT id_pelanggan, nama FROM pelanggan");
                                while($p = mysqli_fetch_array($pel)) {
                                    echo "<option value='{$p['id_pelanggan']}'>{$p['id_pelanggan']} - {$p['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Mobil</label>
                            <select name="kode_mobil" class="form-select select2-js" required>
                                <option value="">-- Cari Mobil Tersedia --</option>
                                <?php
                                // Mengambil mobil yang stok real-time > 0
                                $mob = mysqli_query($conn, "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m");
                                while($m = mysqli_fetch_array($mob)) {
                                    if ((int)$m['stok_realtime'] > 0) {
                                        $selected = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                        echo "<option value='{$m['kode_mobil']}' {$selected}>{$m['merk']} - {$m['nopol']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gunakan Jasa Supir?</label>
                            <select name="pake_supir" id="pake_supir" class="form-select" onchange="toggleSupirBlock()" required>
                                <option value="Tidak">Tidak (Lepas Kunci)</option>
                                <option value="Ya">Ya (Menggunakan Supir)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="pilihan_supir_block" style="display: none;">
                            <label class="form-label fw-bold">Pilih Supir</label>
                            <select name="id_supir" id="id_supir" class="form-select select2-js">
                                <option value="">-- Pilih Supir --</option>
                                <?php
                                // Supir yang tidak sedang memiliki transaksi 'berjalan'
                                $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') = 0");
                                while($s = mysqli_fetch_array($supir_query)) {
                                    echo "<option value='{$s['id_supir']}'>{$s['nama_supir']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lama Sewa (Hari)</label>
                                <input type="number" name="lama_sewa" class="form-control" min="1" required>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary w-100 py-2 mt-2 rounded-pill shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Riwayat Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Pelanggan</th>
                                    <th>Mobil</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT t.*, p.nama, m.merk 
                                        FROM transaksi_sewa t
                                        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                        JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                        ORDER BY t.tanggal_sewa DESC";
                                $res = mysqli_query($conn, $sql);
                                
                                if($res && mysqli_num_rows($res) > 0){
                                    while($row = mysqli_fetch_array($res)) {
                                ?>
                                <tr>
                                    <td class="ps-3 text-muted">#<?php echo $row['id_sewa']; ?></td>
                                    <td><span class="fw-bold"><?php echo htmlspecialchars($row['nama']); ?></span></td>
                                    <td><?php echo htmlspecialchars($row['merk']); ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?php echo ($row['status_sewa'] == 'berjalan') ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ucfirst($row['status_sewa']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="pembayaran.php?id=<?php echo $row['id_sewa']; ?>" class="btn btn-sm btn-outline-success border-0">
                                            <i class="bi bi-cash-stack"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                    <tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada transaksi.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-js').select2({ theme: "bootstrap-5", width: '100%' });
    });

    function toggleSupirBlock() {
        const status = document.getElementById("pake_supir").value;
        const block = document.getElementById("pilihan_supir_block");
        const select = document.getElementById("id_supir");
        
        block.style.display = (status === "Ya") ? "block" : "none";
        select.required = (status === "Ya");
    }
</script>