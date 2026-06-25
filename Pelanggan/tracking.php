<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pelanggan') { 
    header("Location: login.php"); 
    exit(); 
}
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['auto_complete']) && $_POST['auto_complete'] == 1) {
    $id_sewa_post = intval($_POST['id_sewa']);
    $q_mob = mysqli_query($conn, "SELECT kode_mobil FROM transaksi_sewa WHERE id_sewa = $id_sewa_post");
    if ($q_mob && mysqli_num_rows($q_mob) > 0) {
        $r_mob = mysqli_fetch_assoc($q_mob);
        $k_mob = $r_mob['kode_mobil'];
        mysqli_query($conn, "UPDATE transaksi_sewa SET status_sewa = 'selesai' WHERE id_sewa = $id_sewa_post");
        mysqli_query($conn, "UPDATE mobil SET status_mobil = 'tersedia' WHERE kode_mobil = '$k_mob'");
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error']);
    }
    exit();
}

$id_sewa = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_pelanggan = $_SESSION['id_pelanggan'];

// Cek kepemilikan transaksi dan status
$query = "SELECT t.*, p.nama, p.alamat, p.no_telp, m.merk, m.nopol, s.nama_supir, s.gambar as gambar_supir,
                 UNIX_TIMESTAMP(t.waktu_mulai_perjalanan) AS start_ts,
                 UNIX_TIMESTAMP(NOW()) AS current_ts
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

$start_ts = $rental['start_ts'] ?? 0;
$current_ts = $rental['current_ts'] ?? time();
$elapsed_seconds = ($start_ts > 0) ? ($current_ts - $start_ts) : -1;

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

        /* Progress Stepper Style */
        .tracking-stepper {
            position: relative;
            padding: 0 10px;
        }
        .progress-line-bg {
            position: absolute;
            top: 16px;
            left: 12%;
            right: 12%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
            border-radius: 2px;
        }
        .progress-line-active {
            position: absolute;
            top: 16px;
            left: 12%;
            width: 0%;
            height: 4px;
            background: #10b981;
            z-index: 2;
            border-radius: 2px;
            transition: width 0.5s ease;
        }
        .step-item {
            z-index: 3;
            width: 25%;
        }
        .step-icon {
            width: 32px;
            height: 32px;
            background: #ffffff;
            border: 2px solid #cbd5e1;
            color: #64748b;
            border-radius: 50%;
            font-weight: 800;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .step-icon.active {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
        }
        .step-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #64748b;
            margin-top: 8px;
            transition: color 0.3s ease;
        }
        .step-label.active {
            color: #10b981;
        }
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
    
    <!-- Gojek/Grab Stepper -->
    <div class="tracking-stepper mb-4 d-flex justify-content-between align-items-center position-relative">
        <div class="progress-line-bg"></div>
        <div class="progress-line-active" id="progress-line"></div>
        
        <div class="step-item text-center">
            <div id="step-icon-1" class="step-icon mx-auto">1</div>
            <div id="step-label-1" class="step-label">Persiapan</div>
        </div>
        <div class="step-item text-center">
            <div id="step-icon-2" class="step-icon mx-auto">2</div>
            <div id="step-label-2" class="step-label">Menuju Jemput</div>
        </div>
        <div class="step-item text-center">
            <div id="step-icon-3" class="step-icon mx-auto">3</div>
            <div id="step-label-3" class="step-label">Dalam Perjalanan</div>
        </div>
        <div class="step-item text-center">
            <div id="step-icon-4" class="step-icon mx-auto"><i class="bi bi-check-lg" style="display:none;"></i><span id="step-num-4">4</span></div>
            <div id="step-label-4" class="step-label">Tiba di Lokasi</div>
        </div>
    </div>
    
    <div class="driver-info">
        <div class="driver-avatar">
            <?php if(!empty($rental['gambar_supir'])): ?>
                <img src="../Admin/img_supir/<?= htmlspecialchars($rental['gambar_supir']) ?>" alt="Foto Supir" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
            <?php else: ?>
                <i class="bi bi-person-fill"></i>
            <?php endif; ?>
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
    const waktuMulaiPerjalanan = <?= json_encode($start_ts) ?>;
    const currentServerTime = <?= json_encode($current_ts) ?>;
    const elapsedSecondsInitial = <?= json_encode($elapsed_seconds) ?>;

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

    function updateStepper(progressFraction) {
        const progressLine = document.getElementById('progress-line');
        const step1Icon = document.getElementById('step-icon-1');
        const step1Label = document.getElementById('step-label-1');
        const step2Icon = document.getElementById('step-icon-2');
        const step2Label = document.getElementById('step-label-2');
        const step3Icon = document.getElementById('step-icon-3');
        const step3Label = document.getElementById('step-label-3');
        const step4Icon = document.getElementById('step-icon-4');
        const step4Label = document.getElementById('step-label-4');
        const step4Check = step4Icon.querySelector('.bi-check-lg');
        const step4Num = document.getElementById('step-num-4');

        // Reset classes
        step1Icon.classList.remove('active'); step1Label.classList.remove('active');
        step2Icon.classList.remove('active'); step2Label.classList.remove('active');
        step3Icon.classList.remove('active'); step3Label.classList.remove('active');
        step4Icon.classList.remove('active'); step4Label.classList.remove('active');
        step4Check.style.display = 'none';
        step4Num.style.display = 'inline';

        if (progressFraction < 0) {
            progressLine.style.width = '0%';
            step1Icon.classList.add('active'); step1Label.classList.add('active');
        } else if (progressFraction >= 1) {
            progressLine.style.width = '100%';
            step1Icon.classList.add('active'); step1Label.classList.add('active');
            step2Icon.classList.add('active'); step2Label.classList.add('active');
            step3Icon.classList.add('active'); step3Label.classList.add('active');
            step4Icon.classList.add('active'); step4Label.classList.add('active');
            step4Check.style.display = 'inline';
            step4Num.style.display = 'none';
        } else {
            step1Icon.classList.add('active'); step1Label.classList.add('active');
            
            let width = 0;
            if (progressFraction < 0.30) {
                // Persiapan -> Menuju Jemput (0% to 33% line)
                step2Icon.classList.add('active'); step2Label.classList.add('active');
                width = (progressFraction / 0.30) * 33;
            } else if (progressFraction < 0.90) {
                // Menuju Jemput -> Dalam Perjalanan (33% to 66% line)
                step2Icon.classList.add('active'); step2Label.classList.add('active');
                step3Icon.classList.add('active'); step3Label.classList.add('active');
                width = 33 + ((progressFraction - 0.30) / 0.60) * 33;
            } else {
                // Dalam Perjalanan -> Tiba (66% to 100% line)
                step2Icon.classList.add('active'); step2Label.classList.add('active');
                step3Icon.classList.add('active'); step3Label.classList.add('active');
                width = 66 + ((progressFraction - 0.90) / 0.10) * 34;
            }
            progressLine.style.width = width + '%';
        }
    }

    let coordinates = [];
    let totalTime = 0;
    let summary = null;
    let routeLoaded = false;
    let frameCount = 0;
    const pageLoadTime = Date.now();

    function animateRoute() {
        if (!coordinates || coordinates.length === 0) {
            requestAnimationFrame(animateRoute);
            return;
        }

        if (elapsedSecondsInitial < 0) {
            carMarker.setLatLng(origin);
            document.getElementById('eta-text').innerText = Math.round(totalTime / 60) + ' Menit';
            document.getElementById('dist-text').innerText = (summary.totalDistance / 1000).toFixed(1) + ' km';
            updateStepper(-1);
            
            const bounds = L.latLngBounds([origin, destination]);
            map.fitBounds(bounds, {padding: [50, 50]});
            return;
        }

        const elapsedMs = (Date.now() - pageLoadTime);
        const elapsedSeconds = elapsedSecondsInitial + (elapsedMs / 1000);
        
        if (elapsedSeconds >= totalTime) {
            carMarker.setLatLng(destination);
            document.getElementById('eta-text').innerText = 'Tiba di Lokasi';
            document.getElementById('dist-text').innerText = '0.0 km';
            updateStepper(1.0);
            map.setView(destination, 15);
            
            // Auto complete transaction in database and redirect to dashboard
            fetch('tracking.php?id=' + <?= $id_sewa ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'auto_complete=1&id_sewa=' + <?= $id_sewa ?>
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Mobil telah tiba! Transaksi Anda selesai.');
                    window.location = 'transaksi.php';
                }
            });
            return;
        }
        
        const progressFraction = elapsedSeconds / totalTime;
        const floatIndex = progressFraction * (coordinates.length - 1);
        const idx = Math.floor(floatIndex);
        const subProgress = floatIndex - idx;
        
        const currentCoord = coordinates[idx];
        const nextCoord = coordinates[idx + 1] || currentCoord;
        
        const lat = currentCoord.lat + (nextCoord.lat - currentCoord.lat) * subProgress;
        const lng = currentCoord.lng + (nextCoord.lng - currentCoord.lng) * subProgress;
        const currentPosition = L.latLng(lat, lng);
        
        carMarker.setLatLng(currentPosition);
        
        const dy = nextCoord.lat - currentCoord.lat;
        const dx = nextCoord.lng - currentCoord.lng;
        if (Math.abs(dy) > 0.000001 || Math.abs(dx) > 0.000001) {
            let angle = Math.atan2(dy, dx) * 180 / Math.PI;
            angle = 90 - angle; 
            carMarker.setRotationAngle(angle);
        }
        
        const remainingSeconds = Math.max(0, totalTime - elapsedSeconds);
        document.getElementById('eta-text').innerText = Math.round(remainingSeconds / 60) + ' Menit';
        
        const remainingFraction = 1 - progressFraction;
        const remainingDistance = (summary.totalDistance * remainingFraction) / 1000;
        document.getElementById('dist-text').innerText = Math.max(0.1, remainingDistance).toFixed(1) + ' km';
        
        updateStepper(progressFraction);
        
        if (frameCount % 60 === 0) {
            const bounds = L.latLngBounds([currentPosition, destination]);
            map.fitBounds(bounds, {padding: [50, 50], animate: true, duration: 1});
        }
        frameCount++;
        
        requestAnimationFrame(animateRoute);
    }

    control.on('routesfound', function(e) {
        const routes = e.routes;
        summary = routes[0].summary;
        totalTime = summary.totalTime;
        coordinates = routes[0].coordinates;
        
        if (!routeLoaded) {
            routeLoaded = true;
            animateRoute();
        }
    });
</script>
</body>
</html>
