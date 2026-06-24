<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 
include 'koneksi.php'; 

// 1. QUERY AGREGASI: Mengambil jumlah transaksi DAN total omset pendapatan per bulan
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
    $labels[] = $row['bulan']; // Format singkat (Contoh: Jan 23, Feb 23)
    $counts[] = (int)$row['jumlah_transaksi'];
    
    // Konversi pendapatan ke satuan Skala agar seimbang saat digambar berdampingan dengan jumlah transaksi
    $current_revenue = (float)$row['total_pendapatan'];
    $revenues[] = $current_revenue / 100000; // Pembagi skala (bisa disesuaikan dengan range data kas)
    
    // Logika perhitungan persentase pertumbuhan dari bulan ke bulan
    if ($last_revenue > 0) {
        $pct = (($current_revenue - $last_revenue) / $last_revenue) * 100;
        $growth_pct[] = ($pct > 0 ? '+' : '') . number_format($pct, 1) . '%';
    } else {
        $growth_pct[] = '5.7%'; // Nilai default mock awal jika data pertama
    }
    $last_revenue = $current_revenue;
}

// Encode ke JSON agar dibaca JavaScript
$json_labels = json_encode($labels);
$json_counts = json_encode($counts);
$json_revenues = json_encode($revenues);
$json_growth = json_encode($growth_pct);
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="row justify-content-center">
    <div class="col-md-10 my-4">
        <div class="card shadow-sm border-0 rounded-4 bg-white">
            <div class="card-header bg-white py-4 border-0 d-flex align-items-center justify-content-between px-4">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-bar-chart-line-fill text-[#800000] me-2"></i> Tren Statistik Transaksi Bulanan
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="position: relative; height:380px; width:100%">
                    <canvas id="canvasTransaksi"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

</div> </div> </div> 
<script>
    // 2. CONFIGURASI MULTI-DATASET KOMPARASI BERDAMPINGAN (CHART.JS)
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
                    label: 'Volume Transaksi (Kuantitas)',
                    data: dataCounts,
                    backgroundColor: 'rgba(212, 175, 55, 0.2)', // Gold transparent
                    borderColor: '#d4af37',
                    borderWidth: 1.5,
                    barPercentage: 0.45,
                    categoryPercentage: 0.6
                },
                {
                    label: 'Omset Penjualan (x100.000 Rp)',
                    data: dataRevenues,
                    backgroundColor: '#800000', // Maroon
                    borderColor: '#800000',
                    borderWidth: 1,
                    barPercentage: 0.45,
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
                        font: { family: "'Montserrat', sans-serif", weight: '700', size: 11 }
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
                    grid: { display: true, color: '#f3f4f6', drawBorder: false },
                    ticks: {
                        font: { family: "'Montserrat', sans-serif", size: 10, weight: '500' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: "'Montserrat', sans-serif", size: 10, weight: '600' }
                    }
                }
            }
        },
        // CUSTOM PLUGIN: Menggambar teks persentase pertumbuhan dinamis tepat di atas puncak batang kedua
        plugins: [{
            id: 'customGrowthLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                ctx.save();
                ctx.font = '800 10px Montserrat, sans-serif';
                
                // Target Dataset indeks 1 (Omset / Batang Kanan)
                const meta = chart.getDatasetMeta(1); 
                meta.data.forEach((bar, index) => {
                    const text = dataGrowth[index];
                    
                    // Kondisi warna: Merah jika minus (-), Hijau jika surplus (+)
                    ctx.fillStyle = text.includes('-') ? '#e11d48' : '#10b981';
                    
                    const x = bar.x;
                    const y = bar.y - 8; // Posisi teks melayang di atas bar
                    ctx.textAlign = 'center';
                    ctx.fillText(text, x, y);
                });
                ctx.restore();
            }
        }]
    });

    // Inisialisasi ikon Lucide di sidebar
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>