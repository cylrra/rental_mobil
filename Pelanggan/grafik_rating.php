<?php
include 'koneksi.php';

// Ambil nilai rata-rata tiap indikator dari database
$query = mysqli_query($koneksi, "SELECT AVG(rating_pelayanan) as avg_pely, AVG(rating_supir) as avg_supr, AVG(rating_mobil) as avg_mobl FROM rating_sewa");
$row = mysqli_fetch_assoc($query);

$pely = round($row['avg_pely'], 2) ?? 0;
$supr = round($row['avg_supir'], 2) ?? 0;
$mobl = round($row['avg_mobl'], 2) ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Grafik Kepuasan Pelanggan</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div style="width: 50%; margin: auto; text-align: center; padding-top: 30px;">
        <h3>Grafik Kepuasan Layanan PT INDOMAX RENTAL</h3>
        <canvas id="ratingChart"></canvas>
    </div>

    <script>
    const ctx = document.getElementById('ratingChart').getContext('2d');
    const ratingChart = new Chart(ctx, {
        type: 'bar', // Tipe grafik batang
        data: {
            labels: ['Pelayanan', 'Supir', 'Kondisi Mobil'],
            datasets: [{
                label: 'Rata-rata Rating (Skala 1-5)',
                data: [<?= $pely; ?>, <?= $supr; ?>, <?= $mobl; ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
    </script>
</body>
</html>