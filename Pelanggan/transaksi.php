<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// Tangkap kode mobil dari URL (jika ada)
$kode_selected = isset($_GET['kode']) ? $_GET['kode'] : '';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Input Transaksi Baru</h5>
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
                                $mob = mysqli_query($conn, "SELECT m.*, (m.Unit_Tersedia - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m");
                                while($m = mysqli_fetch_array($mob)) {
                                    if ((int)$m['stok_realtime'] > 0) {
                                        $selected = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                        echo "<option value='{$m['kode_mobil']}' {$selected}>{$m['merk']} ({$m['nopol']})</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Layanan Supir</label>
                            <select name="id_supir" class="form-select select2-js">
                                <option value="">Tidak, Terima Kasih (Lepas Kunci / Tanpa Supir)</option>
                                <?php
                                // Mengambil data supir dari kami yang statusnya sedang 'tersedia' (tidak bertugas)
                                $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') = 0");
                                while($s = mysqli_fetch_array($supir_query)) {
                                    echo "<option value='{$s['id_supir']}'>Ya, Gunakan Jasa: {$s['nama_supir']} (+ Rp " . number_format($s['tarif_supir_per_hari'], 0, ',', '.') . "/Hari)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanggal Sewa</label>
                            <input type="date" name="tanggal_sewa" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lama Sewa (Hari)</label>
                            <input type="number" name="lama_sewa" class="form-control" placeholder="Contoh: 3" min="1" required>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-save"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary"></i> Riwayat Transaksi</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Transaksi</th>
                                    <th>Pelanggan</th>
                                    <th>Mobil</th>
                                    <th>Tgl Sewa</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
                                
                                if ($is_admin) {
                                    $sql = "SELECT t.*, p.nama, m.merk 
                                            FROM transaksi_sewa t
                                            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                            JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                            ORDER BY t.tanggal_sewa DESC";
                                } else {
                                    $id_user = '';
                                    if (isset($_SESSION['id_pelanggan'])) {
                                        $id_user = $_SESSION['id_pelanggan'];
                                    } elseif (isset($_SESSION['id_akun'])) {
                                        $id_user = $_SESSION['id_akun'];
                                    } elseif (isset($_SESSION['id_user'])) {
                                        $id_user = $_SESSION['id_user'];
                                    }
                                    
                                    $id_user_clean = mysqli_real_escape_string($conn, $id_user);
                                    
                                    $sql = "SELECT t.*, p.nama, m.merk 
                                            FROM transaksi_sewa t
                                            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                            JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                            WHERE t.id_pelanggan = '$id_user_clean'
                                            ORDER BY t.tanggal_sewa DESC";
                                }
                                
                                $res = mysqli_query($conn, $sql);
                                
                                if($res && mysqli_num_rows($res) > 0){
                                    while($row = mysqli_fetch_array($res)) {
                                        $display_id = $row['id_sewa'];
                                ?>
                                <tr>
                                    <td class="ps-3 small text-muted">#<?php echo $display_id; ?></td>
                                    <td><span class="fw-semibold"><?php echo $row['nama']; ?></span></td>
                                    <td><?php echo $row['merk']; ?></td>
                                    <td><small><?php echo date('d M Y', strtotime($row['tanggal_sewa'])); ?></small></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-warning text-dark"><?php echo $row['status_sewa']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="pembayaran.php?id=<?php echo $display_id; ?>" class="btn btn-sm btn-outline-success border-0">
                                            <i class="bi bi-cash"></i> Bayar
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada transaksi atau terjadi kesalahan query.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
        $('.select2-js').select2({
            theme: "bootstrap-5",
            width: '100%',
            placeholder: "-- Pilih --"
        });
    });
</script>