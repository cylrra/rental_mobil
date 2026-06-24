<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') { 
    header("Location: login.php"); 
    exit(); 
}
include 'koneksi.php';

$id_sewa = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_pelanggan = $_SESSION['id_pelanggan'];

// Cek kepemilikan transaksi dan status
$query = "SELECT t.*, p.nama, p.alamat, p.no_telp, m.merk, m.nopol, s.nama_supir 
          FROM transaksi_sewa t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          JOIN mobil m ON t.kode_mobil = m.kode_mobil
          LEFT JOIN supir s ON t.id_supir = s.id_supir
          WHERE t.id_sewa = $id_sewa AND t.id_pelanggan = $id_pelanggan";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Transaksi tidak ditemukan atau Anda tidak berhak melihatnya.'); window.location='transaksi.php';</script>";
    exit();
}
$rental = mysqli_fetch_assoc($result);

// Koordinat simulasi
$pickup_lat = -6.9932; 
$pickup_lng = 110.4203;

// Koordinat awal mobil sebelum routing
$start_lat = $pickup_lat - 0.05;
$start_lng = $pickup_lng - 0.05;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lacak Armada - PT INDOMAX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; overflow: hidden; background: #000; }
        #map { width: 100vw; height: 100vh; z-index: 1; }
        
        /* Modern Bottom Sheet */
        .bottom-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 24px 24px 0 0;
            z-index: 1000;
            box-shadow: 0 -10px 40px rgba(0,0,0,0.15);
            padding: 24px 24px 32px 24px;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }
        
        .handle-bar {
            width: 40px; height: 5px; background: #e2e2e2; border-radius: 10px;
            margin: 0 auto 20px auto;
        }

        .driver-info { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
        .driver-avatar { width: 56px; height: 56px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: #9ca3af; border: 2px solid #e2e2e2; }
        
        .driver-details h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1a1c1c; }
        .driver-details p { margin: 0; font-size: 0.85rem; font-weight: 600; color: #6b7280; }
        
        .car-plate { background: #f8fafc; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 8px; font-weight: 900; color: #1a1c1c; font-size: 0.9rem; letter-spacing: 1px; display: inline-block;}
        
        .eta-box { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 16px; border-radius: 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .eta-box .time { font-size: 1.5rem; font-weight: 900; margin: 0;}
        .eta-box .desc { font-size: 0.8rem; font-weight: 600; margin: 0; opacity: 0.9;}
        
        .back-btn { position: fixed; top: 20px; left: 20px; z-index: 1000; background: white; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); color: #1a1c1c; text-decoration: none; font-size: 20px; }
        
        /* Hide routing machine itinerary */
        .leaflet-routing-container { display: none !important; }
    </style>
</head>
<body>

<a href="transaksi.php" class="back-btn"><i class="bi bi-arrow-left"></i></a>

<div id="map"></div>

<div class="bottom-sheet">
    <div class="handle-bar"></div>
    
    <div class="eta-box shadow-sm">
        <div>
            <p class="desc">Estimasi Tiba (Jarak: <span id="dist-text">--</span>)</p>
            <h2 class="time" id="eta-text">-- Menit</h2>
        </div>
        <i class="bi bi-clock-history fs-1 opacity-50"></i>
    </div>
    
    <div class="driver-info">
        <div class="driver-avatar">
            <i class="bi bi-person-fill"></i>
        </div>
        <div class="driver-details flex-grow-1">
            <h3><?= htmlspecialchars($rental['nama_supir'] ?? 'Tanpa Supir (Lepas Kunci)') ?></h3>
            <p><i class="bi bi-star-fill text-warning"></i> 5.0 • <?= htmlspecialchars($rental['merk']) ?></p>
        </div>
        <div class="text-end">
            <div class="car-plate"><?= htmlspecialchars($rental['nopol']) ?></div>
        </div>
    </div>
    
    <div class="d-flex gap-3">
        <?php if(!empty($rental['id_supir'])): ?>
            <a href="tel:<?= $rental['no_telp'] ?>" class="btn btn-dark flex-grow-1 fw-bold py-3 rounded-4" style="background: #1a1c1c;"><i class="bi bi-telephone-fill me-2"></i>Hubungi Supir</a>
        <?php endif; ?>
        <button class="btn btn-light flex-grow-1 fw-bold py-3 rounded-4 border"><i class="bi bi-share-fill me-2"></i>Bagikan</button>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.min.js"></script>
<script>
    const map = L.map('map', { zoomControl: false }).setView([<?= $pickup_lat ?>, <?= $pickup_lng ?>], 13);
    
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const destination = L.latLng(<?= $pickup_lat ?>, <?= $pickup_lng ?>);
    const origin = L.latLng(<?= $start_lat ?>, <?= $start_lng ?>);

    // Custom Icons (Top-down car for rotation)
    const carIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/2985/2985040.png', 
        iconSize: [48, 48],
        iconAnchor: [24, 24],
        className: 'drop-shadow-lg'
    });
    
    const destIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png', 
        iconSize: [40, 40],
        iconAnchor: [20, 40],
    });

    L.marker(destination, {icon: destIcon}).addTo(map);
    const carMarker = L.marker(origin, {icon: carIcon, rotationAngle: 0}).addTo(map);

    // Initialize Routing
    const control = L.Routing.control({
        waypoints: [ origin, destination ],
        createMarker: function() { return null; }, // Hide default markers
        lineOptions: {
            styles: [{color: '#10b981', opacity: 0.8, weight: 6}]
        },
        fitSelectedRoutes: true,
        show: false,
        router: L.Routing.osrmv1({
            language: 'id',
            profile: 'driving'
        })
    }).addTo(map);

    control.on('routesfound', function(e) {
        const routes = e.routes;
        const summary = routes[0].summary;
        
        // Update ETA & Distance
        document.getElementById('eta-text').innerText = Math.round(summary.totalTime / 60) + ' Menit';
        document.getElementById('dist-text').innerText = (summary.totalDistance / 1000).toFixed(1) + ' km';
        
        // Animate marker along route
        const coordinates = routes[0].coordinates;
        let i = 0;
        
        function moveMarker() {
            if (i < coordinates.length - 1) {
                const nextCoord = coordinates[i+1];
                const currentCoord = coordinates[i];
                
                // Calculate angle for rotation
                const dy = nextCoord.lat - currentCoord.lat;
                const dx = nextCoord.lng - currentCoord.lng;
                let angle = Math.atan2(dy, dx) * 180 / Math.PI;
                angle = 90 - angle; 
                carMarker.setRotationAngle(angle);

                let step = 0;
                const maxSteps = 30;
                
                function animateStep() {
                    step++;
                    const lat = currentCoord.lat + ((nextCoord.lat - currentCoord.lat) * (step/maxSteps));
                    const lng = currentCoord.lng + ((nextCoord.lng - currentCoord.lng) * (step/maxSteps));
                    
                    carMarker.setLatLng([lat, lng]);
                    
                    if (step < maxSteps) {
                        requestAnimationFrame(animateStep);
                    } else {
                        // Adjust map bounds occasionally to keep car and dest in view
                        if (i % 20 === 0) {
                            const bounds = L.latLngBounds([coordinates[i], destination]);
                            map.fitBounds(bounds, {padding: [50, 50], animate: true, duration: 1});
                        }
                        
                        i++;
                        setTimeout(moveMarker, 50);
                    }
                }
                animateStep();
            } else {
                document.getElementById('eta-text').innerText = 'Tiba di Lokasi';
            }
        }
        
        setTimeout(moveMarker, 1000);
    });
</script>
</body>
</html>
