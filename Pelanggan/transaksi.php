<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

$kode_selected = isset($_GET['kode']) ? $_GET['kode'] : '';
$id_pelanggan = $_SESSION['id_pelanggan'];
$stmt_user = mysqli_prepare($conn, "SELECT nama, status_verifikasi FROM pelanggan WHERE id_pelanggan = ?");
mysqli_stmt_bind_param($stmt_user, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_user);
$query_user = mysqli_stmt_get_result($stmt_user);

if ($query_user) {
    $user_data = mysqli_fetch_assoc($query_user);
    $status_verif = $user_data['status_verifikasi'] ?? 'belum_verifikasi';
    $nama_default = $user_data['nama'] ?? $_SESSION['nama_pelanggan'] ?? '';
} else {
    $status_verif = 'belum_verifikasi';
    $nama_default = $_SESSION['nama_pelanggan'] ?? 'Pelanggan';
}
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* ===== Premium Transaksi UI ===== */
.tx-wrap {
    max-width: 1200px;
    margin: 0 auto;
}
.tx-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}
.tx-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--on-surface);
    margin: 0 0 4px;
}
.tx-sub {
    color: var(--tertiary);
    font-size: 0.95rem;
    margin: 0;
}
.btn-catalog {
    background: #fff;
    border: 1px solid var(--primary);
    color: var(--primary);
    padding: 10px 24px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-catalog:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(158,0,0,0.2);
}
.card-modern {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(15,23,42,0.03);
    overflow: hidden;
}
.card-header-modern {
    background: transparent;
    padding: 24px 24px 0;
    border-bottom: none;
}
.card-header-modern h5 {
    font-weight: 800;
    color: var(--on-surface);
    margin: 0;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 10px;
}
.form-label {
    font-weight: 700;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    margin-bottom: 8px;
}
.form-control, .form-select {
    border-radius: 12px;
    padding: 12px 16px;
    border: 1px solid #E8ECF2;
    background: #F8FAFC;
    font-size: 0.95rem;
    font-weight: 500;
    color: #0F172A;
    transition: all 0.2s;
}
.form-control:focus, .form-select:focus {
    background: #fff;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(158,0,0,0.08);
}
.btn-submit {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px 24px;
    font-weight: 800;
    font-size: 1rem;
    width: 100%;
    transition: all 0.2s;
}
.btn-submit:hover {
    background: #7a0000;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(158,0,0,0.2);
}
.table-modern {
    margin: 0;
}
.table-modern th {
    background: #F8FAFC;
    color: #475569;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 16px;
    border-bottom: 1px solid #E8ECF2;
    font-weight: 800;
}
.table-modern td {
    padding: 16px;
    border-bottom: 1px solid #E8ECF2;
    vertical-align: middle;
}
.badge-status {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 800;
}
.badge-status.pending { background: #F1F5F9; color: #475569; }
.badge-status.diterima { background: rgba(158,0,0,0.1); color: var(--primary); }
.badge-status.dp { background: rgba(253,192,3,0.15); color: #b48600; }
.badge-status.berjalan { background: rgba(13,148,136,0.1); color: #0F766E; }
.badge-status.selesai { background: rgba(22,163,74,0.1); color: #16A34A; }
</style>

<div class="tx-wrap">
    <!-- Banner -->
    <div class="pay-header-banner mb-4">
        <div style="position:relative;z-index:2;" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div style="font-size:0.7rem;font-weight:800;color:#D4AF37;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;"><i class="bi bi-calendar-check-fill me-2"></i>Portal Sewa</div>
                <h1 style="font-size:1.5rem;font-weight:800;margin:0 0 6px;color:#fff;">Sewa Mobil</h1>
                <p style="font-size:0.85rem;color:rgba(255,255,255,0.65);margin:0;">Pesan kendaraan baru atau pantau status pesanan aktif Anda.</p>
            </div>
            <a href="katalog.php" class="btn btn-light px-4 rounded-pill shadow-sm fw-bold text-primary" style="color: var(--primary) !important;"><i class="bi bi-grid me-1"></i> Lihat Katalog</a>
        </div>
    </div>

    <?php if ($status_verif !== 'terverifikasi'): ?>
    <div class="alert d-flex align-items-start gap-3 mb-4 p-3" style="background: white; border-left: 4px solid #f59e0b; border-radius: 12px;">
        <i class="bi bi-shield-exclamation fs-4 text-warning"></i>
        <div>
            <strong style="color: var(--deep-navy);">Verifikasi Diperlukan</strong>
            <p class="mb-0 mt-1" style="font-size: 0.875rem; color: #555;">
                Akun belum terverifikasi. Untuk sewa <strong>Lepas Kunci</strong>, wajib unggah KTP & SIM di <a href="edit_profil.php" class="fw-bold text-decoration-none">Pengaturan Akun</a>.
            </p>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card-modern h-100">
                <div class="card-header-modern">
                    <h5><i class="bi bi-plus-circle text-primary"></i> Form Sewa</h5>
                </div>
                <div class="card-body p-4">
                    <form id="formRental"> <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Penyewa</label>
                            <input type="text" name="nama_penyewa" class="form-control" value="<?= htmlspecialchars($nama_default) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Mobil</label>
                            <select name="kode_mobil" id="kode_mobil_select" class="form-select" required onchange="hitungTotal()">
                                <option value="">— Pilih Armada —</option>
                                <?php
                                $mob = mysqli_query($conn, "SELECT * FROM mobil WHERE Unit_Tersedia > 0 AND is_deleted = 0");
                                while($m = mysqli_fetch_array($mob)) {
                                    $sel = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                    echo "<option value='{$m['kode_mobil']}' data-tarif='{$m['tarif_per_hari']}' $sel>{$m['merk']} {$m['jenis']} (" . number_format($m['tarif_per_hari'], 0, ',', '.') . "/hr)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Layanan Sopir</label>
                            <select name="id_supir" id="id_supir_select" class="form-select" onchange="hitungTotal(); toggleKeteranganSupir();">
                                <option value="">Tidak — Lepas Kunci (Tanpa Sopir)</option>
                                <option value="999">Ya — Pakai Jasa Sopir</option>
                            </select>
                            <div id="container_keterangan_sopir" style="display: none; margin-top: 5px;">
                                <small class="text-danger d-block" style="font-size: 0.75rem; font-style: italic;">
                                    <i class="bi bi-info-circle"></i> Biaya Supir: 250k (< 12 jam) atau 375k (12-24 jam).
                                </small>
                                <small class="text-danger d-block" style="font-size: 0.75rem; font-style: italic;">
                                    <i class="bi bi-info-circle"></i> *Harga belum termasuk bensin & makan supir.
                                </small>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Tgl & Jam Mulai</label>
                                <input type="datetime-local" name="tgl_mulai" id="tgl_mulai" class="form-control" onchange="hitungTotal()" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tgl & Jam Kembali</label>
                                <input type="datetime-local" name="tgl_kembali" id="tgl_kembali" class="form-control" onchange="hitungTotal()" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Area Pemakaian</label>
                            <select name="area_pemakaian" id="area_pemakaian" class="form-select" onchange="hitungTotal()">
                                <option value="Dalam Kota">Dalam Kota</option>
                                <option value="Luar Kota">Luar Kota (+100k)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi Jemput</label>
                            <select name="lokasi_jemput" id="lokasiSelect" class="form-select" onchange="toggleAlamatInput()">
                                <option value="Ambil di Kantor">Ambil Langsung di Kantor</option>
                                <option value="Antar ke Alamat lainnya">Antar ke Alamat Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inputAlamatCustom" style="display: none;">
                            <input type="text" name="alamat_detail" id="alamatDetail" class="form-control" placeholder="Masukkan alamat penjemputan lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi Pengembalian</label>
                            <select name="lokasi_kembali" id="lokasiKembaliSelect" class="form-select" onchange="toggleAlamatKembaliInput()">
                                <option value="Kembalikan ke Kantor">Kembalikan ke Kantor</option>
                                <option value="Jemput di Alamat lainnya">Jemput di Alamat Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inputAlamatKembaliCustom" style="display: none;">
                            <input type="text" name="alamat_kembali" id="alamatKembaliDetail" class="form-control" placeholder="Masukkan alamat pengembalian lengkap">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Jumlah Unit</label>
                            <input type="number" name="jumlah" id="input_jumlah" class="form-control" value="1" min="1" onchange="hitungTotal()">
                        </div>
                        
                        <div class="mb-4 p-3 rounded-3" style="background: rgba(158,0,0,0.05); border: 1px dashed rgba(158,0,0,0.2);">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold" style="color: #475569; font-size:0.9rem;">Estimasi Total</span>
                                <span class="fw-black text-primary" id="disp_total_biaya" style="font-size: 1.25rem; font-weight:900;">Rp 0</span>
                            </div>
                        </div>
                        <button type="button" id="btnAjukan" class="btn-submit"><i class="bi bi-check-circle me-2"></i> Ajukan Sewa</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-modern h-100">
                <div class="card-header-modern mb-3">
                    <h5><i class="bi bi-clock-history text-primary"></i> Pesanan Saya</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" id="tabelPesanan"> 
                        <table class="table table-modern align-middle">
                            <thead class="table-light text-nowrap">
                                <tr>
                                    <th class="ps-4">Order</th>
                                    <th>Mobil</th>
                                    <th>Jadwal (Ambil & Kembali)</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT t.id_sewa, t.tanggal_sewa, t.tanggal_kembali, t.status_sewa, t.total_bayar, t.jumlah_bayar, m.merk, m.nopol, m.Gambar, r.id_rating
                                        FROM transaksi_sewa t 
                                        LEFT JOIN mobil m ON t.kode_mobil = m.kode_mobil 
                                        LEFT JOIN rating_sewa r ON t.id_sewa = r.id_transaksi
                                        WHERE t.id_pelanggan = ? 
                                        ORDER BY t.id_sewa DESC";
                                $stmt = mysqli_prepare($conn, $sql);
                                mysqli_stmt_bind_param($stmt, "i", $id_pelanggan);
                                mysqli_stmt_execute($stmt);
                                $res = mysqli_stmt_get_result($stmt);
                                
                                if(mysqli_num_rows($res) > 0) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $st = !empty($row['status_sewa']) ? strtolower($row['status_sewa']) : 'pending';
                                        $merk = !empty($row['merk']) ? $row['merk'] : 'Mobil Dihapus';
                                        $gambar = !empty($row['Gambar']) && file_exists('../Admin/img/' . $row['Gambar']) ? '../Admin/img/' . $row['Gambar'] : 'https://placehold.co/100x60/f8f9fa/a3a3a3?text=No+Image';
                                        
                                        $badge_html = '';
                                        $instruction = '';
                                        if ($st == 'pending') {
                                            $badge_html = '<span class="badge-status pending">Menunggu Acc</span>';
                                            $instruction = '<small class="d-block mt-1 text-muted fw-bold" style="font-size:10px;">Tunggu Persetujuan</small>';
                                        } elseif ($st == 'diterima') {
                                            $badge_html = '<span class="badge-status diterima">Menunggu Bayar</span>';
                                            $instruction = '<a href="pembayaran.php?id='.$row['id_sewa'].'" class="btn btn-sm btn-danger rounded-pill mt-1 fw-bold text-nowrap d-inline-block shadow-sm" style="font-size:10px; padding: 4px 12px;">Bayar Sekarang</a>';
                                        } elseif ($st == 'dp') {
                                            $badge_html = '<span class="badge-status dp">DP Terbayar</span>';
                                            $instruction = '<a href="pembayaran.php?id='.$row['id_sewa'].'" class="btn btn-sm btn-outline-primary rounded-pill mt-1 fw-bold text-nowrap d-inline-block" style="font-size:10px; padding: 4px 12px;">Lunasi Kekurangan</a>';
                                        } elseif ($st == 'berjalan') {
                                            $badge_html = '<span class="badge-status berjalan">Sedang Disewa</span>';
                                            $instruction = '<small class="d-block mt-1 text-teal-700 fw-bold" style="font-size:10px; color:#0F766E;">Berkendara Hati-hati</small>';
                                        } elseif ($st == 'selesai') {
                                            $badge_html = '<span class="badge-status selesai">Selesai</span>';
                                            $instruction = '<small class="d-block mt-1 text-success fw-bold" style="font-size:10px;">Dikembalikan</small>';
                                        } else {
                                            $badge_html = '<span class="badge-status pending">'.$st.'</span>';
                                        }
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary align-middle" style="font-size:13px;">#<?= $row['id_sewa'] ?></td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= $gambar ?>" class="rounded shadow-sm" style="width: 60px; height: 40px; object-fit: cover; border: 1px solid #eee;" alt="<?= htmlspecialchars($merk) ?>">
                                            <div>
                                                <strong class="text-nowrap d-block" style="font-size:14px;"><?= htmlspecialchars($merk) ?></strong>
                                                <small class="text-muted text-nowrap" style="font-size:11px;"><?= htmlspecialchars($row['nopol'] ?? '-') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-nowrap" style="font-size:12px;">
                                        <div class="mb-1"><span class="fw-bold">Ambil:</span> <?= date('d M Y H:i', strtotime($row['tanggal_sewa'])) ?></div>
                                        <div><span class="fw-bold">Kembali:</span> <?= isset($row['tanggal_kembali']) && $row['tanggal_kembali'] ? date('d M Y H:i', strtotime($row['tanggal_kembali'])) : '-' ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?= $badge_html ?>
                                        <?= $instruction ?>
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <div class="d-flex justify-content-center align-items-center gap-2 flex-nowrap">
                                            <a href="riwayat_pembayaran.php" class="btn btn-sm btn-light border rounded-pill px-3 text-nowrap" style="font-size: 11px; font-weight: 600;">Detail</a>
                                            <?php if(in_array($st, ['diterima', 'dp', 'berjalan', 'selesai'])): ?>
                                                <a href="cetak_invoice.php?id=<?= $row['id_sewa'] ?>" target="_blank" class="btn btn-sm rounded-pill px-3 fw-semibold text-white shadow-sm text-nowrap" style="font-size: 11px; background: #800000; border: none;"><i class="bi bi-printer-fill me-1"></i>Invoice</a>
                                            <?php endif; ?>
                                            <?php if($st == 'berjalan'): ?>
                                                <a href="tracking.php?id=<?= $row['id_sewa'] ?>" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold text-white shadow-sm text-nowrap" style="font-size: 11px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;"><i class="bi bi-geo-alt-fill me-1"></i>Lacak</a>
                                            <?php endif; ?>
                                            <?php if($st == 'selesai'): ?>
                                                <?php if(empty($row['id_rating'])): ?>
                                                    <a href="ulasan_rating.php?id_sewa=<?= $row['id_sewa'] ?>" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-nowrap" style="font-size: 11px;"><i class="bi bi-star-fill me-1"></i>Rating</a>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 d-flex align-items-center text-nowrap" style="font-size: 11px;"><i class="bi bi-check2-circle me-1"></i>Dinilai</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php } } else { echo "<tr><td colspan='5' class='text-center py-5 text-muted'>Belum ada transaksi.</td></tr>"; } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function formatRupiah(angka) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka); }
    function toggleKeteranganSupir() {
        const idSupir = document.getElementById('id_supir_select').value;
        const container = document.getElementById('container_keterangan_sopir');
        container.style.display = (idSupir === '999') ? 'block' : 'none';
    }

    // ✅ FIX: hitungTotal sekarang pakai tarif sopir yang sinkron dengan keterangan UI (250k/<12jam, 375k/12-24jam)
    function hitungTotal() {
        const tglMulai = new Date(document.getElementById('tgl_mulai').value);
        const tglKembali = new Date(document.getElementById('tgl_kembali').value);
        const area = document.getElementById('area_pemakaian').value;
        const idSupir = document.getElementById('id_supir_select').value;
        const jumlah = parseInt(document.getElementById('input_jumlah').value) || 1;
        const mobSelect = document.getElementById('kode_mobil_select');
        const tarifDasarPerHari = parseFloat(mobSelect.options[mobSelect.selectedIndex].getAttribute('data-tarif')) || 0;
        
        let total = 0;
        
        if (tglMulai && tglKembali && tglKembali > tglMulai) {
            const diffTime = Math.abs(tglKembali - tglMulai);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const diffHours = diffTime / (1000 * 60 * 60);

            total = (tarifDasarPerHari * diffDays * jumlah);

            if (idSupir === '999') {
                // Hitung biaya sopir per sesi sesuai keterangan: <12 jam = 250k, 12-24 jam = 375k
                let biayaSopir = 0;
                let sisaJam = diffHours;
                while (sisaJam > 0) {
                    const sesiIni = Math.min(sisaJam, 24);
                    biayaSopir += (sesiIni <= 12) ? 250000 : 375000;
                    sisaJam -= 24;
                }
                total += biayaSopir;
            }

            if (area === 'Luar Kota') total += 100000;
        }
        
        document.getElementById('disp_total_biaya').innerText = formatRupiah(total);
    }
    function toggleAlamatInput() { document.getElementById("inputAlamatCustom").style.display = (document.getElementById("lokasiSelect").value === "Antar ke Alamat lainnya") ? "block" : "none"; }
    function toggleAlamatKembaliInput() { document.getElementById("inputAlamatKembaliCustom").style.display = (document.getElementById("lokasiKembaliSelect").value === "Jemput di Alamat lainnya") ? "block" : "none"; }

    document.addEventListener("DOMContentLoaded", function() { toggleKeteranganSupir(); });

    $('#btnAjukan').click(function() {
        var formData = $('#formRental').serialize();
        $.ajax({
            url: 'proses_transaksi.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var resArr = response.trim().split('|');
                if (resArr[0] === "sukses") {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pesanan telah diajukan. Silakan lakukan pembayaran.' }).then(() => {
                        window.location.href = 'pembayaran.php?id=' + resArr[1];
                    });
                } else {
                    Swal.fire('Error', response, 'error');
                }
            }
        });
    });
</script>