<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: login_admin.php"); 
    exit(); 
}
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

// Koordinat simulasi berpusat di Kota Semarang
$center_lat = -6.9932;
$center_lng = 110.4203;

while ($row = mysqli_fetch_assoc($result)) {
    // Jarak acak di sekitar Semarang
    $offset_lat = (rand(-30, 30) / 1000);
    $offset_lng = (rand(-30, 30) / 1000);
    
    $row['pickup_lat'] = $center_lat + $offset_lat;
    $row['pickup_lng'] = $center_lng + $offset_lng;
    
    $row['car_lat'] = $row['pickup_lat'] - (rand(5, 15) / 1000);
    $row['car_lng'] = $row['pickup_lng'] - (rand(5, 15) / 1000);
    
    $active_rentals[] = $row;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    #map { height: 580px; width: 100%; border-radius: 1.25rem; z-index: 1; }
    .tracking-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .tracking-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -5px rgba(79, 70, 229, 0.15); border-color: #4f46e5; }
    .active-track { border: 2px solid #4f46e5 !important; background-color: #f5f3ff; }
</style>

<div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight flex items-center gap-3">
        <i data-lucide="map-pin" class="w-8 h-8 text-indigo-600 animate-pulse"></i> Live Tracking Armada
    </h1>
    <p class="text-slate-500 mt-1 font-medium italic">Pantau posisi penjemputan dan pergerakan mobil secara *real-time* (Simulasi Geofencing Semarang).</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200">
            <div id="map"></div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between" style="max-height: 605px; min-height: 605px;">
            <div>
                <h5 class="font-bold text-slate-800 mb-4 flex items-center gap-2 border-b pb-3 border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <i class="bi bi-broadcast text-indigo-600 fs-5"></i> Armada Berjalan Saat Ini
                </h5>
                
                <?php if (count($active_rentals) > 0): ?>
                    <div class="space-y-3 overflow-y-auto pr-1" style="max-height: 480px;">
                        <?php foreach ($active_rentals as $index => $rental): ?>
                            <div class="tracking-card bg-white p-4 rounded-xl border border-slate-200 cursor-pointer" onclick="focusOnMap(<?= $index ?>)" id="card-<?= $index ?>">
                                <div class="flex justify-between items-start mb-2">
                                    <h6 class="font-bold text-slate-800 mb-0 flex items-center gap-2">
                                        <i class="bi bi-tag-fill text-slate-400 text-xs"></i> <?= htmlspecialchars($rental['nopol']) ?>
                                    </h6>
                                    <span class="badge bg-indigo-100 text-indigo-700 rounded-full px-2 py-1 text-xs font-semibold">TRX-<?= $rental['id_sewa'] ?></span>
                                </div>
                                <p class="text-sm text-slate-600 mb-1 flex items-center gap-2"><i class="bi bi-car-front-fill text-indigo-500"></i> <strong><?= htmlspecialchars($rental['merk']) ?></strong></p>
                                <p class="text-sm text-slate-600 mb-1 flex items-center gap-2"><i class="bi bi-person-circle text-slate-500"></i> <?= htmlspecialchars($rental['nama']) ?></p>
                                <p class="text-xs text-slate-500 mt-2 border-t pt-2 flex items-start gap-1"><i class="bi bi-geo-alt-fill text-rose-500 mt-0.5"></i> <span class="line-clamp-2">Jemput: <?= htmlspecialchars($rental['alamat']) ?></span></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-20">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                            <i class="bi bi-slash-circle fs-2"></i>
                        </div>
                        <h6 class="text-slate-600 font-bold">Tidak ada armada berjalan</h6>
                        <p class="text-sm text-slate-500 px-4">Semua mobil saat ini siap dan tersedia di dalam garasi pool utama.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    lucide.createIcons();

    const rentalsData = <?= json_encode($active_rentals) ?>;
    
    // PERBAIKAN DI SINI: Menggunakan kurung kurawal {s} agar Tile Layer berfungsi kembali
    const map = L.map('map').setView([-6.9932, 110.4203], 12);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);
    
    const carIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3204/3204121.png', 
        iconSize: [38, 38],
        iconAnchor: [19, 19],
        popupAnchor: [0, -15]
    });
    
    const userIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/819/819865.png', 
        iconSize: [34, 34],
        iconAnchor: [17, 34],
        popupAnchor: [0, -32]
    });

    const markers = [];
    
    rentalsData.forEach((rental, index) => {
        const pickupMarker = L.marker([rental.pickup_lat, rental.pickup_lng], {icon: userIcon}).addTo(map);
        pickupMarker.bindPopup(`
            <div style="font-family: sans-serif; padding: 2px;">
                <b style="color: #e11d48;"><i class="bi bi-geo-alt-fill"></i> Titik Jemput Pelanggan</b><br>
                <span style="font-size: 13px; font-weight: bold; color:#1e293b;">${rental.nama}</span><br>
                <small style="color: #64748b;">${rental.alamat}</small>
            </div>
        `);
        
        const carMarker = L.marker([rental.car_lat, rental.car_lng], {icon: carIcon}).addTo(map);
        carMarker.bindPopup(`
            <div style="font-family: sans-serif; padding: 2px;">
                <b style="color: #4f46e5;"><i class="bi bi-car-front-fill"></i> Armada Indomax</b><br>
                <span style="font-size: 13px; font-weight: bold; color:#1e293b;">${rental.merk} (${rental.nopol})</span><br>
                <span style="font-size: 11px; color:#10b981; font-weight:600;">Status: OTW Penjemputan...</span>
            </div>
        `);
        
        markers.push({ pickup: pickupMarker, car: carMarker, data: rental });
        
        setInterval(() => {
            const currentLat = carMarker.getLatLng().lat;
            const currentLng = carMarker.getLatLng().lng;
            
            const dLat = (rental.pickup_lat - currentLat) * 0.03;
            const dLng = (rental.pickup_lng - currentLng) * 0.03;
            
            const noiseLat = (Math.random() - 0.5) * 0.00015;
            const noiseLng = (Math.random() - 0.5) * 0.00015;
            
            const newLat = currentLat + dLat + noiseLat;
            const newLng = currentLng + dLng + noiseLng;
            
            carMarker.setLatLng([newLat, newLng]);
        }, 4000);
    });
    
    if (markers.length > 0) {
        const group = new L.featureGroup(markers.map(m => m.pickup).concat(markers.map(m => m.car)));
        map.fitBounds(group.getBounds().pad(0.15));
    }
    
    function focusOnMap(index) {
        document.querySelectorAll('.tracking-card').forEach(c => c.classList.remove('active-track'));
        document.getElementById('card-' + index).classList.add('active-track');
        
        const m = markers[index];
        map.flyTo(m.car.getLatLng(), 15, {
            animate: true,
            duration: 1.2
        });
        
        setTimeout(() => {
            m.car.openPopup();
        }, 1200);
    }
</script>