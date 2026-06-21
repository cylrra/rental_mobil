<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'navbar.php';
include 'koneksi.php';

// Proses Tambah Jadwal Pemeliharaan
if (isset($_POST['tambah_jadwal'])) {
    $kode_mobil = mysqli_real_escape_string($conn, trim($_POST['kode_mobil']));
    $tanggal = mysqli_real_escape_string($conn, trim($_POST['tanggal_pemeliharaan']));
    $jenis = mysqli_real_escape_string($conn, trim($_POST['jenis_pemeliharaan']));
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan']));

    // Insert ke tabel pemeliharaan (status default 'terjadwal')
    $query = "INSERT INTO pemeliharaan (kode_mobil, tanggal_pemeliharaan, jenis_pemeliharaan, biaya_pemeliharaan, keterangan, status) 
              VALUES ('$kode_mobil', '$tanggal', '$jenis', 0, '$keterangan', 'terjadwal')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Jadwal pemeliharaan berhasil ditambahkan!'); window.location='jadwal_service.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan jadwal: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}

// Proses Selesaikan Pemeliharaan & Posting Otomatis ke Laba Rugi
if (isset($_POST['selesaikan_pemeliharaan'])) {
    $id_pemeliharaan = (int)$_POST['id_pemeliharaan'];
    $biaya = floatval($_POST['biaya_aktual']);
    $kode_mobil = mysqli_real_escape_string($conn, trim($_POST['kode_mobil']));
    $jenis = mysqli_real_escape_string($conn, trim($_POST['jenis']));
    $tanggal_sekarang = date('Y-m-d');

    // Mulai database transaction untuk keamanan relasi & akuntansi
    mysqli_begin_transaction($conn);

    try {
        // 1. Update status di tabel pemeliharaan
        $update_query = "UPDATE pemeliharaan SET status = 'selesai', biaya_pemeliharaan = '$biaya' WHERE id_pemeliharaan = $id_pemeliharaan";
        if (!mysqli_query($conn, $update_query)) {
            throw new Exception("Gagal memperbarui status pemeliharaan.");
        }

        // Keterangan standar untuk log akuntansi
        $keterangan_jurnal = "Biaya Pemeliharaan Mobil: " . $kode_mobil . " (" . $jenis . ")";
        
        // 2. Insert DEBIT ke tabel jurnal (Beban Perawatan Akun 513 -> Masuk Laba Rugi)
        $q_debit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                    VALUES ('$tanggal_sekarang', '$keterangan_jurnal', '513', '$biaya', 0, '$id_pemeliharaan')";
        if (!mysqli_query($conn, $q_debit)) {
            throw new Exception("Gagal memposting sisi Debit (Beban Perawatan).");
        }

        // 3. Insert KREDIT ke tabel jurnal (Kas Akun 111 -> Berkurang di Neraca)
        $q_kredit = "INSERT INTO jurnal (tanggal, keterangan, kode_akun, Debit, Kredit, id_sumber) 
                     VALUES ('$tanggal_sekarang', '$keterangan_jurnal', '111', 0, '$biaya', '$id_pemeliharaan')";
        if (!mysqli_query($conn, $q_kredit)) {
            throw new Exception("Gagal memposting sisi Kredit (Kas).");
        }

        // Jika semua langkah sukses tanpa error, kunci ke database
        mysqli_commit($conn);
        echo "<script>alert('Pemeliharaan diselesaikan dan otomatis diposting ke Laporan Laba Rugi!'); window.location='riwayat_pemeliharaan.php';</script>";

    } catch (Exception $e) {
        // Jika ada salah satu langkah yang gagal, batalkan semua agar database tidak korup/unbalanced
        mysqli_rollback($conn);
        echo "<script>alert('Gagal memproses pembukuan: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

// Ambil data mobil untuk dropdown
$mobil_opsi = mysqli_query($conn, "SELECT kode_mobil, merk, nopol FROM mobil ORDER BY merk ASC");
// Ambil data jadwal (terjadwal)
$jadwal = mysqli_query($conn, "SELECT p.*, m.merk, m.nopol FROM pemeliharaan p JOIN mobil m ON p.kode_mobil = m.kode_mobil WHERE p.status = 'terjadwal' ORDER BY p.tanggal_pemeliharaan ASC");
?>

<div class="mb-4 p-4">
    <h3 class="fw-bold"><i class="bi bi-calendar-check text-[#06588c] me-2"></i> Jadwal Pemeliharaan</h3>
    <p class="text-muted">Kelola jadwal servis dan perawatan armada mobil Anda.</p>
</div>

<div class="row px-4">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-[#04345a] text-white py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal Baru</h5>
            </div>
            <div class="card-body p-4">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-[#050606]">Pilih Mobil</label>
                        <select name="kode_mobil" class="form-select" required>
                            <option value="">-- Pilih Mobil --</option>
                            <?php while($m = mysqli_fetch_assoc($mobil_opsi)) { ?>
                                <option value="<?= $m['kode_mobil']; ?>"><?= htmlspecialchars($m['merk']); ?> [<?= htmlspecialchars($m['nopol']); ?>]</option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-[#050606]">Rencana Tanggal Servis</label>
                        <input type="date" name="tanggal_pemeliharaan" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-[#050606]">Jenis Perawatan</label>
                        <select name="jenis_pemeliharaan" class="form-select" required>
                            <option value="Servis Rutin">Servis Rutin</option>
                            <option value="Perbaikan Kerusakan">Perbaikan Kerusakan</option>
                            <option value="Ganti Ban">Ganti Ban</option>
                            <option value="Ganti Oli">Ganti Oli</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-[#050606]">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Deskripsi perbaikan..."></textarea>
                    </div>
                    <button type="submit" name="tambah_jadwal" class="btn text-white w-100 py-2 mt-2 rounded-pill shadow-sm" style="background-color:#06588c;">
                        <i class="bi bi-calendar-plus me-1"></i> Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="mb-0 fw-bold text-[#04345a]"><i class="bi bi-list-task me-2"></i>Daftar Kendaraan Terjadwal</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No</th>
                                <th>Mobil</th>
                                <th>Tgl Jadwal</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1; 
                            if ($jadwal && mysqli_num_rows($jadwal) > 0) {
                                while($r = mysqli_fetch_assoc($jadwal)) { 
                            ?>
                            <tr>
                                <td class="ps-3 text-muted"><?= $no++; ?></td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($r['merk']); ?></span>
                                    <small class="text-muted d-block"><?= htmlspecialchars($r['nopol']); ?></small>
                                </td>
                                <td><?= date('d M Y', strtotime($r['tanggal_pemeliharaan'])); ?></td>
                                <td>
                                    <span class="badge rounded-pill text-dark" style="background-color:#c8c6c6;">
                                        <?= htmlspecialchars($r['jenis_pemeliharaan']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#modalSelesai<?= $r['id_pemeliharaan'] ?>">
                                        <i class="bi bi-check-circle me-1"></i> Selesaikan
                                    </button>

                                    <div class="modal fade" id="modalSelesai<?= $r['id_pemeliharaan'] ?>" tabindex="-1">
                                      <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                          <div class="modal-header border-bottom-0">
                                            <h5 class="modal-title fw-bold text-[#04345a]">Selesaikan Servis: <?= htmlspecialchars($r['merk']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                          </div>
                                          <form action="" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_pemeliharaan" value="<?= $r['id_pemeliharaan'] ?>">
                                                <input type="hidden" name="kode_mobil" value="<?= htmlspecialchars($r['kode_mobil']) ?>">
                                                <input type="hidden" name="jenis" value="<?= htmlspecialchars($r['jenis_pemeliharaan']) ?>">
                                                
                                                <div class="alert alert-info py-2" style="background-color:#04345a; color:white;">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    Memasukkan biaya akan otomatis tercatat ke <b>Laporan Laba Rugi (Akun 513)</b>.
                                                </div>

                                                <div class="mb-3 mt-3">
                                                    <label class="form-label fw-bold">Total Biaya Aktual (Rp)</label>
                                                    <input type="number" name="biaya_aktual" class="form-control form-control-lg" required min="0" placeholder="0">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="selesaikan_pemeliharaan" class="btn btn-success rounded-pill px-4">Simpan & Posting</button>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    </td>
                            </tr>
                            <?php 
                                } 
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada jadwal pemeliharaan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</main> 
</div> 
<script>lucide.createIcons();</script>
</body>
</html>