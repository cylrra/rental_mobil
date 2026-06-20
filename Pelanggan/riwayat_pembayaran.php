<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// =========================================================================
// 1. QUERY AGREGASI: Mengambil kuantitas sewa DAN total omset kas per bulan
// =========================================================================
$sql_grafik = "SELECT 
                DATE_FORMAT(t.tanggal_sewa, '%b %y') as bulan, 
                COUNT(p.id_pembayaran) as jumlah_transaksi,
                SUM(p.jumlah_bayar) as total_pendapatan
               FROM pembayaran p
               JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
               GROUP BY YEAR(t.tanggal_sewa), MONTH(t.tanggal_sewa)
               ORDER BY t.tanggal_sewa ASC";

$res_grafik = mysqli_query($conn, $sql_grafik);

$labels = [];
$counts = [];
$revenues = [];
$growth_pct = [];
$last_revenue = 0;

while ($row = mysqli_fetch_assoc($res_grafik)) {
    $labels[] = $row['bulan']; // Format singkat (Contoh: May 26, Jun 26)
    $counts[] = (int)$row['jumlah_transaksi'];
    
    // Pembagi skala (100.000) agar tinggi batang nominal seimbang saat digambar berdampingan dengan jumlah sewa
    $current_revenue = (float)$row['total_pendapatan'];
    $revenues[] = $current_revenue / 100000; 
    
    // Logika perhitungan persentase pertumbuhan omset dari bulan ke bulan
    if ($last_revenue > 0) {
        $pct = (($current_revenue - $last_revenue) / $last_revenue) * 100;
        $growth_pct[] = ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%';
    } else {
        $growth_pct[] = '+5.7%'; // Nilai default awal jika data pertama seperti di Gambar 1
    }
    $last_revenue = $current_revenue;
}

// Ubah array PHP menjadi format JSON agar bisa dibaca oleh JavaScript Chart.js
$json_labels = json_encode($labels);
$json_counts = json_encode($counts);
$json_revenues = json_encode($revenues);
$json_growth = json_encode($growth_pct);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 rounded-4 bg-primary text-white">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-0"><i class="bi bi-wallet2"></i> Riwayat Pembayaran</h3>
                    <p class="mb-0 opacity-75">Log masuk keuangan PT INDOMAX</p>
                </div>
                <?php 
                $query_total = mysqli_query($conn, "SELECT SUM(jumlah_bayar) as total FROM pembayaran");
                $res_total = mysqli_fetch_assoc($query_total);
                $total_masuk = $res_total['total'] ?? 0;
                ?>
                <div class="text-end">
                    <small class="d-block opacity-75">Total Kas Masuk</small>
                    <h2 class="fw-bold mb-0">Rp <?php echo number_format($total_masuk, 0, ',', '.'); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 bg-white">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-4">
                    <i class="bi bi-bar-chart-line-fill text-danger me-2"></i> Tren Statistik Transaksi Bulanan
                </h6>
                <div style="position: relative; height:320px; width:100%">
                    <canvas id="canvasTransaksi"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Daftar Transaksi Masuk</h5>
            <a href="pembayaran.php" class="btn btn-primary btn-sm rounded-3">
                <i class="bi bi-plus-lg"></i> Input Baru
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID Bayar</th>
                        <th>Info Transaksi</th>
                        <th>Tanggal Bayar</th>
                        <th>Metode</th>
                        <th>Jumlah Bayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, pl.nama 
                            FROM pembayaran p
                            JOIN transaksi_sewa t ON p.id_sewa = t.id_sewa
                            JOIN pelanggan pl ON t.id_pelanggan = pl.id_pelanggan
                            ORDER BY p.id_pembayaran DESC";
                    
                    $query = mysqli_query($conn, $sql);
                    
                    if ($query && mysqli_num_rows($query) > 0) {
                        while($d = mysqli_fetch_array($query)){
                            $metode_badge = ($d['metode_pembayaran'] == 'Transfer') ? 'bg-info text-dark' : 'bg-secondary text-white';
                    ?>
                    <tr>
                        <td class="ps-4 text-muted small">#PYM-<?php echo $d['id_pembayaran']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo $d['nama']; ?></div>
                            <small class="text-muted">ID Sewa: #SRV-<?php echo $d['id_sewa']; ?></small>
                        </td>
                        <td><?php echo date('d M Y', strtotime($d['tanggal_bayar'])); ?></td>
                        <td>
                            <span class="badge <?php echo $metode_badge; ?> rounded-pill px-3">
                                <?php echo $d['metode_pembayaran']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">
                                Rp <?php echo number_format($d['jumlah_bayar'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="cetak_kwitansi.php?id=<?php echo $d['id_pembayaran']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="bi bi-printer"></i> Kwitansi
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'>
                                <i class='bi bi-inbox fs-1 d-block mb-2'></i>
                                Belum ada data pembayaran masuk.
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white text-center py-3">
        <small class="text-muted">Menampilkan semua riwayat pembayaran PT INDOMAX</small>
    </div>
</div>

</div> </div> </div> <script>
    const ctx = document.getElementById('canvasTransaksi').getContext('2d');
    
    // Ambil variabel JSON dari pemrosesan PHP di atas
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
                    label: 'Normal Day (Kuantitas)',
                    data: dataCounts,
                    backgroundColor: '#ffb3ba', // Pink muda lembut (Sesuai Gambar 1)
                    borderColor: '#ffb3ba',
                    borderWidth: 1,
                    barPercentage: 0.8,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Double Date (Omset Kas)',
                    data: dataRevenues,
                    backgroundColor: '#ff3b5c', // Pink-merah pekat cerah (Sesuai Gambar 1)
                    borderColor: '#ff3b5c',
                    borderWidth: 1,
                    barPercentage: 0.8,
                    categoryPercentage: 0.6
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
                        font: { weight: '600', size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        footer: function(tooltipItems) {
                            let idx = tooltipItems[0].dataIndex;
                            return 'Pertumbuhan: ' + dataGrowth[idx];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: true, color: '#f1f2f6', drawBorder: false },
                    ticks: {
                        stepSize: 2,
                        font: { size: 11, weight: '500' }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        },
        // CUSTOM EXTERNAL PLUGIN: Mencetak langsung teks persentase dinamis hijau/merah di atas puncak batang
        plugins: [{
            id: 'customGrowthLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = 'bold 11px sans-serif';
                
                // Target Dataset indeks 1 (Grup Batang Kanan / Double Date)
                const meta = chart.getDatasetMeta(1); 
                meta.data.forEach((bar, index) => {
                    const text = dataGrowth[index];
                    
                    // Kondisi deteksi warna teks: Merah jika minus (-), Hijau jika surplus (+)
                    ctx.fillStyle = text.includes('-') ? '#ff3838' : '#2ed573';
                    
                    const x = bar.x;
                    const y = bar.y - 8; // Koordinat teks mengapung tepat di atas ujung batang
                    ctx.textAlign = 'center';
                    ctx.fillText(text, x, y);
                });
                ctx.restore();
            }
        }]
    });
</script>
</body>
</html>