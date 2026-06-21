<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: login_admin.php"); exit(); }
include 'koneksi.php';
include 'navbar.php'; 

// Fetch active transactions (status_sewa = 'berjalan')
$query = "SELECT t.id_sewa, p.nama, p.alamat, p.no_telp, m.merk, m.nopol 
          FROM transaksi_sewa t
          JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
          JOIN mobil m ON t.kode_mobil = m.kode_mobil
          WHERE t.status_sewa = 'berjalan'";
$result = mysqli_query($conn, $query);

$active_rentals = [];
// Generate random coordinates around Jakarta for simulation
$center_lat = -6.200000;
$center_lng = 106.816666;

while ($row = mysqli_fetch_assoc($result)) {
    // Generate simulated coordinates
    $offset_lat = (rand(-50, 50) / 1000);
    $offset_lng = (rand(-50, 50) / 1000);
    
    $row['pickup_lat'] = $center_lat + $offset_lat;
    $row['pickup_lng'] = $center_lng + $offset_lng;
    
    // Car starts slightly away from pickup location
    $row['car_lat'] = $row['pickup_lat'] - (rand(1, 10) / 1000);
    $row['car_lng'] = $row['pickup_lng'] - (rand(1, 10) / 1000);
    
    $active_rentals[] = $row;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 600px; width: 100%; border-radius: 1rem; z-index: 1; }
    .tracking-card { transition: all 0.3s ease; }
    .tracking-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); border-color: #4f46e5; }
    .active-track { border: 2px solid #4f46e5 !important; background-color: #f5f3ff; }
</style>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Live Tracking Armada</h1>
    <p class="text-slate-500 mt-1 font-medium italic">Pantau posisi penjemputan dan pergerakan mobil secara *real-time* (Simulasi).</p>
</div>

<div class="row g-4">
    <!-- Map Section -->
    <div class="col-lg-8">
        <div class="glass-card p-2 rounded-2xl shadow-sm border border-slate-200">
            <div id="map"></div>
        </div>
    </div>
    
    <!-- Active Rentals List Section -->
    <div class="col-lg-4">
        <div class="glass-card p-6 rounded-2xl shadow-sm border border-slate-200 h-100 overflow-auto" style="max-height: 620px;">
            <h5 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i class="bi bi-broadcast text-indigo-600"></i> Armada Berjalan
            </h5>
            
            <?php if (count($active_rentals) > 0): ?>
                <div class="space-y-3">
                    <?php foreach ($active_rentals as $index => $rental): ?>
                        <div class="tracking-card bg-white p-4 rounded-xl border border-slate-200 cursor-pointer" onclick="focusOnMap(<?= $index ?>)" id="card-<?= $index ?>">
                            <div class="flex justify-between items-start mb-2">
                                <h6 class="font-bold text-slate-800 mb-0"><?= htmlspecialchars($rental['nopol']) ?></h6>
                                <span class="badge bg-indigo-100 text-indigo-700 rounded-full px-2 py-1 text-xs">TRX-<?= $rental['id_sewa'] ?></span>
                            </div>
                            <p class="text-sm text-slate-600 mb-1"><i class="bi bi-car-front"></i> <?= htmlspecialchars($rental['merk']) ?></p>
                            <p class="text-sm text-slate-600 mb-1"><i class="bi bi-person"></i> <?= htmlspecialchars($rental['nama']) ?></p>
                            <p class="text-xs text-slate-500 mt-2 border-t pt-2 line-clamp-2"><i class="bi bi-geo-alt"></i> Jemput: <?= htmlspecialchars($rental['alamat']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-10">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                        <i class="bi bi-slash-circle fs-2"></i>
                    </div>
                    <h6 class="text-slate-600 font-bold">Tidak ada armada berjalan</h6>
                    <p class="text-sm text-slate-500">Semua mobil sedang tersedia di garasi.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const rentalsData = <?= json_encode($active_rentals) ?>;
    
    // Initialize map
    const map = L.map('map').setView([-6.200000, 106.816666], 11);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);
    
    // Icons
    const carIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3204/3204121.png',
        iconSize: [35, 35],
        iconAnchor: [17, 17],
        popupAnchor: [0, -15]
    });
    
    const userIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3008/3008985.png',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    const markers = [];
    
    rentalsData.forEach((rental, index) => {
        // Customer Pickup Marker
        const pickupMarker = L.marker([rental.pickup_lat, rental.pickup_lng], {icon: userIcon}).addTo(map);
        pickupMarker.bindPopup(`<b>Titik Jemput Pelanggan</b><br>${rental.nama}<br><small>${rental.alamat}</small>`);
        
        // Car Marker
        const carMarker = L.marker([rental.car_lat, rental.car_lng], {icon: carIcon}).addTo(map);
        carMarker.bindPopup(`<b>${rental.merk} (${rental.nopol})</b><br>Menuju titik penjemputan...`);
        
        markers.push({ pickup: pickupMarker, car: carMarker, data: rental });
        
        // Simulate car movement
        setInterval(() => {
            const currentLat = carMarker.getLatLng().lat;
            const currentLng = carMarker.getLatLng().lng;
            
            // Move slightly towards pickup location
            const dLat = (rental.pickup_lat - currentLat) * 0.05;
            const dLng = (rental.pickup_lng - currentLng) * 0.05;
            
            // Add some random noise to simulate driving on roads
            const noiseLat = (Math.random() - 0.5) * 0.0001;
            const noiseLng = (Math.random() - 0.5) * 0.0001;
            
            const newLat = currentLat + dLat + noiseLat;
            const newLng = currentLng + dLng + noiseLng;
            
            carMarker.setLatLng([newLat, newLng]);
        }, 3000); // Update every 3 seconds
    });
    
    // Fit map bounds to show all markers if any exist
    if (markers.length > 0) {
        const group = new L.featureGroup(markers.map(m => m.pickup).concat(markers.map(m => m.car)));
        map.fitBounds(group.getBounds().pad(0.1));
    }
    
    function focusOnMap(index) {
        // Highlight card
        document.querySelectorAll('.tracking-card').forEach(c => c.classList.remove('active-track'));
        document.getElementById('card-' + index).classList.add('active-track');
        
        // Pan map
        const m = markers[index];
        map.flyTo(m.car.getLatLng(), 15, {
            animate: true,
            duration: 1.5
        });
        
        // Open popup
        setTimeout(() => {
            m.car.openPopup();
        }, 1500);
    }
</script>

</div> </main> </div>
</body>
</html>
