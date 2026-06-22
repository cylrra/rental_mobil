<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PROTEKSI KETAT: Hanya pelanggan yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: login_pelanggan.php");
    exit();
}

include 'navbar.php'; 
include 'koneksi.php'; 

$id_pelanggan = $_SESSION['id_pelanggan'];

// =========================================================================
// 1. QUERY AGREGASI PERSONAL: Tren belanja pelanggan per bulan
// =========================================================================
$sql_grafik = "SELECT 
                DATE_FORMAT(t.tanggal_sewa, '%b %y') as bulan, 
                COUNT(p.id_pembayaran) as jumlah_transaksi,
                SUM(p.jumlah_bayar) as total_pendapatan
               FROM pembayaran p
               JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
               WHERE t.id_pelanggan = '$id_pelanggan'
               GROUP BY YEAR(t.tanggal_sewa), MONTH(t.tanggal_sewa)
               ORDER BY t.tanggal_sewa ASC";

$res_grafik = mysqli_query($conn, $sql_grafik);

$labels = [];
$counts = [];
$revenues = [];
$growth_pct = [];
$last_revenue = 0;

while ($row = mysqli_fetch_assoc($res_grafik)) {
    $labels[] = $row['bulan']; 
    $counts[] = (int)$row['jumlah_transaksi'];
    
    // Konversi nominal sewa (dibagi 100.000 agar seimbang di tinggi bar chart)
    $current_revenue = (float)$row['total_pendapatan'];
    $revenues[] = $current_revenue / 100000; 
    
    // Hitung persentase pertumbuhan bulanan
    if ($last_revenue > 0) {
        $pct = (($current_revenue - $last_revenue) / $last_revenue) * 100;
        $growth_pct[] = ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%';
    } else {
        $growth_pct[] = 'Awal';
    }
    $last_revenue = $current_revenue;
}

// Ubah ke format JSON
$json_labels = json_encode($labels);
$json_counts = json_encode($counts);
$json_revenues = json_encode($revenues);
$json_growth = json_encode($growth_pct);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-fluid px-4">
    <!-- Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;"><i class="bi bi-wallet2 me-2"></i> Riwayat Transaksi & Pembayaran</h3>
                        <p class="mb-0 opacity-75">Log transaksi dan status keuangan sewa Anda.</p>
                    </div>
                    <?php 
                    // Hitung total pengeluaran khusus pelanggan ini
                    $query_total = mysqli_query($conn, "SELECT SUM(p.jumlah_bayar) as total FROM pembayaran p JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa WHERE t.id_pelanggan = '$id_pelanggan'");
                    $res_total = mysqli_fetch_assoc($query_total);
                    $total_masuk = $res_total['total'] ?? 0;
                    ?>
                    <div class="text-md-end mt-3 mt-md-0">
                        <small class="d-block opacity-75 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Total Transaksi Anda</small>
                        <h2 class="fw-bold mb-0">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Spending Trend Chart -->
    <?php if (count($labels) > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-4">
                        <i class="bi bi-bar-chart-line-fill text-primary me-2"></i> Tren Pengeluaran Rental Bulanan Anda
                    </h6>
                    <div style="position: relative; height:240px; width:100%">
                        <canvas id="canvasTransaksi"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- History Table -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark" style="font-family: 'Outfit', sans-serif;">Daftar Pembayaran Saya</h5>
                <a href="pembayaran.php" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg"></i> Bayar Baru
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Resi</th>
                            <th>Detail Transaksi</th>
                            <th>Tanggal Bayar</th>
                            <th>Metode</th>
                            <th>Tipe</th>
                            <th>Jumlah Bayar</th>
                            <th class="text-center pe-4">Aksi / Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil riwayat pembayaran khusus pelanggan ini
                        $sql = "SELECT p.*, m.merk, t.id_sewa 
                                FROM pembayaran p
                                JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                                JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                WHERE t.id_pelanggan = '$id_pelanggan'
                                ORDER BY p.id_pembayaran DESC";
                        
                        $query = mysqli_query($conn, $sql);
                        
                        if ($query && mysqli_num_rows($query) > 0) {
                            while($d = mysqli_fetch_array($query)){
                                $metode = ucfirst($d['metode_pembayaran']);
                                $tipe_badge = ($d['jenis_pembayaran'] == 'dp') ? 'bg-info text-dark' : 'bg-success text-white';
                                $tipe_text = ($d['jenis_pembayaran'] == 'dp') ? 'DP 30%' : 'Pelunasan';
                        ?>
                        <tr>
                            <td class="ps-4 text-muted small">#PYM-<?php echo $d['id_pembayaran']; ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $d['merk']; ?></div>
                                <small class="text-muted">Order ID: #SRV-<?php echo $d['id_sewa']; ?></small>
                            </td>
                            <td><?php echo date('d M Y', strtotime($d['tanggal_bayar'])); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1.5 small text-capitalize">
                                    <?php echo $metode; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $tipe_badge; ?> rounded-pill px-3 py-1.5 small text-uppercase">
                                    <?php echo $tipe_text; ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    Rp <?php echo number_format($d['jumlah_bayar'], 0, ',', '.'); ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="Invoice.php?id=<?php echo $d['id_sewa']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3" target="_blank">
                                        <i class="bi bi-file-earmark-text"></i> Invoice
                                    </a>
                                    <a href="cetak_kwitansi.php?id=<?php echo $d['id_pembayaran']; ?>" class="btn btn-sm btn-primary rounded-pill px-3" target="_blank">
                                        <i class="bi bi-printer"></i> Kwitansi
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                                    <i class='bi bi-inbox fs-1 d-block mb-2'></i>
                                    Belum ada transaksi pembayaran.
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (count($labels) > 0): ?>
<script>
    const ctx = document.getElementById('canvasTransaksi').getContext('2d');
    
    const dataLabels = <?php echo $json_labels; ?>;
    const dataCounts = <?php echo $json_counts; ?>;
    const dataRevenues = <?php echo $json_revenues; ?>;
    const dataGrowth = <?php echo $json_growth; ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataLabels,
            datasets: [
                {
                    label: 'Jumlah Transaksi',
                    data: dataCounts,
                    backgroundColor: '#cbd5e1', // Light slate grey
                    borderColor: '#cbd5e1',
                    borderWidth: 1,
                    barPercentage: 0.6,
                    categoryPercentage: 0.5
                },
                {
                    label: 'Pengeluaran (Ratus Ribu Rp)',
                    data: dataRevenues,
                    backgroundColor: '#1e3a8a', // Deep accent blue
                    borderColor: '#1e3a8a',
                    borderWidth: 1,
                    barPercentage: 0.6,
                    categoryPercentage: 0.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { weight: '600', size: 11 }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: '#f1f5f9' },
                    ticks: { font: { size: 10 } }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
<?php endif; ?>

<!-- Footer component closes wrapper divs -->
</div> </body>
</html>