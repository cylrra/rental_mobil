<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: login_admin.php"); 
    exit(); 
}
include 'koneksi.php';
include 'navbar.php'; 

$query = "SELECT t.id_sewa, p.nama, p.alamat, p.no_telp, m.merk, m.nopol 
          FROM transaksi_sewa t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          JOIN mobil m ON t.kode_mobil = m.kode_mobil
          WHERE t.status_sewa = 'berjalan'";
$result = mysqli_query($conn, $query);

$active_rentals = [];

// Base coordinate for Semarang
$center_lat = -6.9932;
$center_lng = 110.4203;

while ($row = mysqli_fetch_assoc($result)) {
    // Generate distinct start and end points for routing
    $row['pickup_lat'] = $center_lat + (rand(-40, 40) / 1000);
    $row['pickup_lng'] = $center_lng + (rand(-40, 40) / 1000);
    
    $row['car_lat'] = $row['pickup_lat'] - (rand(15, 30) / 1000);
    $row['car_lng'] = $row['pickup_lng'] - (rand(15, 30) / 1000);
    
    $active_rentals[] = $row;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    #map { height: 600px; width: 100%; border-radius: 1rem; z-index: 1; }
    .tracking-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #e2e2e2; }
    .tracking-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -5px rgba(128, 0, 0, 0.15); border-color: #800000; }
    .active-track { border: 2px solid #800000 !important; background-color: #fffaf9; box-shadow: 0 4px 15px rgba(128,0,0,0.1); }
    
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e2e2e2; }
    .leaflet-popup-content { margin: 14px 18px; line-height: 1.5; }
    
    .pulse-ring { position: relative; display: inline-flex; }
    .pulse-ring::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 50%; background-color: #10b981; animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite; opacity: 0.7; }
    @keyframes ping { 75%, 100% { transform: scale(2); opacity: 0; } }
    
    /* Hide routing container (directions) */
    .leaflet-routing-container { display: none !important; }
</style>

<div class="p-8">
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="w-14 h-14 bg-gradient-to-br from-[#800000] to-[#b30000] rounded-2xl flex items-center justify-center shadow-lg shadow-red-900/20 border border-red-800">
                <i class="bi bi-radar text-white text-3xl animate-pulse"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-[#1a1c1c] tracking-tight">
                    Live <span class="text-[#800000]">Tracking</span>
                </h1>
                <p class="text-slate-500 font-medium italic mt-1">Pemantauan armada terintegrasi dengan pemetaan jalan raya nyata.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#e2e2e2] flex flex-col h-full" style="max-height: 650px;">
                <div class="border-b border-[#e2e2e2] pb-4 mb-4">
                    <h5 class="font-black text-[#1a1c1c] flex items-center gap-2">
                        <span class="pulse-ring w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span>
                        Status Armada Aktif
                    </h5>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Total: <?= count($active_rentals) ?> Unit Berjalan</p>
                </div>
                
                <?php if (count($active_rentals) > 0): ?>
                    <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbars" style="max-height: 520px;">
                        <?php foreach ($active_rentals as $index => $rental): ?>
                            <div class="tracking-card bg-white p-5 rounded-xl cursor-pointer relative overflow-hidden group" onclick="focusOnMap(<?= $index ?>)" id="card-<?= $index ?>">
                                <div class="absolute top-0 left-0 w-1 h-full bg-[#800000] scale-y-0 group-hover:scale-y-100 transition-transform origin-top"></div>
                                
                                <div class="flex justify-between items-start mb-3">
                                    <h6 class="font-black text-[#1a1c1c] text-sm flex items-center gap-2">
                                        <i class="bi bi-car-front-fill text-[#d4af37]"></i> <?= htmlspecialchars($rental['merk']) ?>
                                    </h6>
                                    <span class="bg-[#800000]/10 text-[#800000] font-black rounded-lg px-2 py-1 text-[10px] uppercase tracking-wider">
                                        #TRX-<?= $rental['id_sewa'] ?>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-slate-600 flex items-center gap-2"><i class="bi bi-person-badge text-slate-400"></i> <?= htmlspecialchars($rental['nama']) ?></p>
                                    <p class="text-xs font-bold text-slate-600 flex items-center gap-2"><i class="bi bi-123 text-slate-400"></i> <?= htmlspecialchars($rental['nopol']) ?></p>
                                    <p class="text-[11px] text-slate-500 mt-2 pt-2 border-t border-dashed border-[#e2e2e2] flex items-start gap-1.5"><i class="bi bi-geo-alt-fill text-rose-500 mt-0.5"></i> <span class="line-clamp-2 leading-tight"><?= htmlspecialchars($rental['alamat']) ?></span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-full text-center py-10 opacity-70">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4"><i data-lucide="shield-check" class="w-10 h-10 text-emerald-500"></i></div>
                        <h6 class="text-[#1a1c1c] font-black text-lg">Semua Aman</h6>
                        <p class="text-sm text-slate-500 font-medium px-4">Tidak ada armada yang sedang disewa saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-[#e2e2e2] relative">
                <div class="absolute top-6 right-6 z-[1000] bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl shadow-lg border border-[#e2e2e2] flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-xs font-black text-[#1a1c1c] uppercase tracking-wider">Live Route Sync</span>
                </div>
                <div id="map"></div>
            </div>
        </div>
        
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet-rotatedmarker@0.2.0/leaflet.rotatedMarker.min.js"></script>
<script>
    lucide.createIcons();

    const rentalsData = <?= json_encode($active_rentals) ?>;
    
    const map = L.map('map').setView([-6.9932, 110.4203], 13);
    // Modern map tiles
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);
    
    // Top-down car icon that can be rotated
    const carIcon = L.icon({ 
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/2985/2985040.png', 
        iconSize: [36, 36], 
        iconAnchor: [18, 18], 
        popupAnchor: [0, -15], 
        className: 'drop-shadow-md' 
    });
    const userIcon = L.icon({ 
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/149/149071.png', 
        iconSize: [36, 36], 
        iconAnchor: [18, 36], 
        popupAnchor: [0, -32], 
        className: 'drop-shadow-md' 
    });

    const trackingInstances = [];
    
    rentalsData.forEach((rental, index) => {
        const dest = L.latLng(rental.pickup_lat, rental.pickup_lng);
        const origin = L.latLng(rental.car_lat, rental.car_lng);
        
        const pickupMarker = L.marker(dest, {icon: userIcon}).addTo(map);
        const carMarker = L.marker(origin, {icon: carIcon, rotationAngle: 0}).addTo(map);
        
        carMarker.bindPopup(`
            <div style="font-family: 'Montserrat', sans-serif; min-width: 160px;">
                <b style="color: #d4af37; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;"><i class="bi bi-car-front-fill"></i> Armada Bergerak</b>
                <span style="font-size: 14px; font-weight: 900; color: #1a1c1c;">${rental.merk}</span><br>
                <span style="display: inline-block; background: #f3f3f3; color: #1a1c1c; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800; margin-top: 4px;">${rental.nopol}</span>
            </div>
        `);
        
        trackingInstances.push({ car: carMarker, dest: pickupMarker });

        L.Routing.control({
            waypoints: [origin, dest],
            createMarker: function() { return null; },
            lineOptions: { styles: [{color: '#10b981', opacity: 0.8, weight: 5}] },
            show: false,
            addWaypoints: false,
            routeWhileDragging: false,
            fitSelectedRoutes: false,
            router: L.Routing.osrmv1({
                language: 'id',
                profile: 'driving'
            })
        }).on('routesfound', function(e) {
            const routes = e.routes;
            const coordinates = routes[0].coordinates;
            
            let i = 0;
            // Smooth animation using requestAnimationFrame
            let startLat = coordinates[0].lat;
            let startLng = coordinates[0].lng;
            
            function move() {
                if (i < coordinates.length - 1) {
                    const nextCoord = coordinates[i+1];
                    const currentCoord = coordinates[i];
                    
                    // Calculate angle for rotation
                    const dy = nextCoord.lat - currentCoord.lat;
                    const dx = nextCoord.lng - currentCoord.lng;
                    let angle = Math.atan2(dy, dx) * 180 / Math.PI;
                    // Adjust angle because map coordinates are weird and icon points up
                    angle = 90 - angle; 
                    
                    carMarker.setRotationAngle(angle);

                    // Interpolate steps
                    let step = 0;
                    const maxSteps = 30; // higher = smoother but slower
                    
                    function animateStep() {
                        step++;
                        const lat = currentCoord.lat + ((nextCoord.lat - currentCoord.lat) * (step/maxSteps));
                        const lng = currentCoord.lng + ((nextCoord.lng - currentCoord.lng) * (step/maxSteps));
                        
                        carMarker.setLatLng([lat, lng]);
                        
                        if (step < maxSteps) {
                            requestAnimationFrame(animateStep);
                        } else {
                            i++;
                            setTimeout(move, 50); // wait before next segment
                        }
                    }
                    animateStep();
                }
            }
            setTimeout(move, 2000);
        }).addTo(map);
    });
    
    if (trackingInstances.length > 0) {
        const bounds = new L.featureGroup(trackingInstances.map(i => i.dest)).getBounds();
        map.fitBounds(bounds.pad(0.2));
    }
    
    function focusOnMap(index) {
        document.querySelectorAll('.tracking-card').forEach(c => c.classList.remove('active-track'));
        document.getElementById('card-' + index).classList.add('active-track');
        
        const instance = trackingInstances[index];
        map.flyTo(instance.car.getLatLng(), 16, { animate: true, duration: 1.5 });
        setTimeout(() => { instance.car.openPopup(); }, 1500);
    }
</script>
</div></main></div>
</body>
</html>