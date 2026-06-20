<?php
// Admin/pemeliharaan.php
include 'config.php'; // Sesuaikan dengan file koneksi Anda
include 'navbar.php';
include 'sidebar.php';

// Proses Tambah Data
if (isset($_POST['tambah_pemeliharaan'])) {
    $id_mobil = $_POST['id_mobil'];
    $tanggal = $_POST['tanggal_pemeliharaan'];
    $jenis = $_POST['jenis_pemeliharaan'];
    $biaya = $_POST['biaya_pemeliharaan'];
    $keterangan = $_POST['keterangan'];

    // 1. Insert ke tabel pemeliharaan
    $query = "INSERT INTO pemeliharaan (id_mobil, tanggal_pemeliharaan, jenis_pemeliharaan, biaya_pemeliharaan, keterangan) 
              VALUES ('$id_mobil', '$tanggal', '$jenis', '$biaya', '$keterangan')";
    
    if (mysqli_query($koneksi, $query)) {
        // 2. OTOMATIS MASUK JURNAL UMUM (Debit: Beban Pemeliharaan, Kredit: Kas)
        // Sesuaikan kode akun (id_akun/id_coa) berdasarkan struktur tabel coa2 Anda
        $id_jurnal = time(); // Create dummy/unique reference
        mysqli_query($koneksi, "INSERT INTO jurnal (no_jurnal, tanggal_jurnal, keterangan) VALUES ('JR-$id_jurnal', '$tanggal', 'Biaya Pemeliharaan Mobil')");
        
        // PENTING: Sesuaikan nilai id_akun beban dan kas milik Anda
        // mysqli_query($koneksi, "INSERT INTO jurnal_detail (no_jurnal, id_akun, debit, kredit) VALUES ('JR-$id_jurnal', 'ID_AKUN_BEBAN', '$biaya', 0)");
        // mysqli_query($koneksi, "INSERT INTO jurnal_detail (no_jurnal, id_akun, debit, kredit) VALUES ('JR-$id_jurnal', 'ID_AKUN_KAS', 0, '$biaya')");

        echo "<script>alert('Data pemeliharaan berhasil disimpan dan dijurnal!'); window.location='pemeliharaan.php';</script>";
    }
}

// Ambil data mobil untuk dropdown
$mobil_opsi = mysqli_query($koneksi, "SELECT * FROM mobil");
// Ambil data riwayat pemeliharaan
$riwayat = mysqli_query($koneksi, "SELECT p.*, m.nama_mobil, m.plat_nomor FROM pemeliharaan p JOIN mobil m ON p.id_mobil = m.id_mobil ORDER BY p.tanggal_pemeliharaan DESC");
?>

<div class="content-wrapper" style="padding: 20px; margin-left: 250px;">
    <h3><i class="fa fa-wrench"></i> Tambah & Riwayat Pemeliharaan Mobil</h3>
    
    <div class="card" style="background: #f9f9f9; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h5>Input Pemeliharaan Baru</h5>
        <form action="" method="POST">
            <div style="margin-bottom: 10px;">
                <label>Pilih Mobil:</label>
                <select name="id_mobil" required style="width: 100%; padding: 8px;">
                    <?php while($m = mysqli_fetch_assoc($mobil_opsi)) { ?>
                        <option value="<?= $m['id_mobil']; ?>"><?= $m['nama_mobil']; ?> [<?= $m['plat_nomor']; ?>]</option>
                    <?php } ?>
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label>Tanggal Pemeliharaan:</label>
                <input type="date" name="tanggal_pemeliharaan" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Jenis Pemeliharaan:</label>
                <select name="jenis_pemeliharaan" style="width: 100%; padding: 8px;">
                    <option value="Servis Rutin">Servis Rutin</option>
                    <option value="Perbaikan Kerusakan">Perbaikan Kerusakan</option>
                    <option value="Ganti Ban">Ganti Ban</option>
                    <option value="Ganti Oli">Ganti Oli</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div style="margin-bottom: 10px;">
                <label>Biaya (Rp):</label>
                <input type="number" name="biaya_pemeliharaan" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 10px;">
                <label>Keterangan Tambahan:</label>
                <textarea name="keterangan" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <button type="submit" name="tambah_pemeliharaan" style="background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer;">Simpan Pemeliharaan</button>
        </form>
    </div>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead style="background: #343a40; color: white;">
            <tr>
                <th>No</th>
                <th>Mobil</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Biaya</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($r = mysqli_fetch_assoc($riwayat)) { ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $r['nama_mobil']; ?> (<?= $r['plat_nomor']; ?>)</td>
                <td><?= $r['tanggal_pemeliharaan']; ?></td>
                <td><?= $r['jenis_pemeliharaan']; ?></td>
                <td>Rp <?= number_format($r['biaya_pemeliharaan'], 0, ',', '.'); ?></td>
                <td><?= $r['keterangan']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>