<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head><title>Transaksi Baru - PT INDOMAX</title></head>
<body>
    <h2>Input Transaksi Sewa Baru</h2>
    <form action="proses_simpan.php" method="POST">
        <label>Pilih Pelanggan:</label><br>
        <select name="id_pelanggan">
            <?php
            $pel = mysqli_query($conn, "SELECT * FROM pelanggan");
            while($p = mysqli_fetch_array($pel)) {
                echo "<option value='{$p['id_pelanggan']}'>{$p['nama']}</option>";
            }
            ?>
        </select><br><br>

        <label>Pilih Mobil (Hanya yang tersedia):</label><br>
        <select name="kode_mobil">
            <?php
            $mob = mysqli_query($conn, "SELECT * FROM mobil WHERE status_mobil='tersedia'");
            while($m = mysqli_fetch_array($mob)) {
                echo "<option value='{$m['kode_mobil']}'>{$m['merk']} - {$m['nopol']}</option>";
            }
            ?>
        </select><br><br>

        <label>Pilih Supir:</label><br>
        <select name="id_supir">
            <option value="">Tanpa Supir (Lepas Kunci)</option>
            <?php
            $sup = mysqli_query($conn, "SELECT * FROM supir WHERE status_supir='tersedia'");
            while($s = mysqli_fetch_array($sup)) {
                echo "<option value='{$s['id_supir']}'>{$s['nama_supir']}</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Simpan Transaksi</button>
    </form>
</body>
</html>