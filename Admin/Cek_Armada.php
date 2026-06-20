<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Cek Armada Tersedia - PT INDOMAX</title>
    <style>
        body { font-family: sans-serif; margin: 30px; }
        .container { display: flex; gap: 50px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #2c3e50; color: white; }
        .status-ready { color: green; font-weight: bold; }
        .card { flex: 1; }
    </style>
</head>
<body>

    <h1>Armada Siap Pesan (Ready)</h1>
    <p>Dibawah ini adalah daftar mobil dan supir yang berstatus <b>Tersedia</b>.</p>

    <div class="container">
        
        <div class="card">
            <h3>🚗 Mobil Tersedia</h3>
            <table>
                <tr>
                    <th>Merk</th>
                    <th>Nopol</th>
                    <th>Harga/Hari</th>
                </tr>
                <?php
                $sql_mobil = "SELECT * FROM mobil WHERE status_mobil = 'tersedia'";
                $query_mobil = mysqli_query($conn, $sql_mobil);
                while($m = mysqli_fetch_array($query_mobil)) {
                    echo "<tr>
                            <td>{$m['merk']}</td>
                            <td>{$m['nopol']}</td>
                            <td>Rp ".number_format($m['tarif_per_hari'], 0, ',', '.')."</td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h3>👨‍✈️ Supir Tersedia</h3>
            <table>
                <tr>
                    <th>Nama Supir</th>
                    <th>Telepon</th>
                    <th>Tarif/Hari</th>
                </tr>
                <?php
                $sql_supir = "SELECT * FROM supir WHERE status_supir = 'tersedia'";
                $query_supir = mysqli_query($conn, $sql_supir);
                while($s = mysqli_fetch_array($query_supir)) {
                    echo "<tr>
                            <td>{$s['nama_supir']}</td>
                            <td>{$s['no_telp']}</td>
                            <td>Rp ".number_format($s['tarif_supir_per_hari'], 0, ',', '.')."</td>
                          </tr>";
                }
                ?>
            </table>
        </div>

    </div>

    <br>
    <a href="index.php">⬅ Kembali ke Beranda</a>
    <a href="transaksi.php" style="margin-left:20px; color: blue; font-weight: bold;">Mulai Pesan Sekarang ⮕</a>

</body>
</html>