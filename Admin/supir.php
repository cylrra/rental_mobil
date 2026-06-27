<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';
?>

<style>
/* Supir page specific */
.supir-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}
.supir-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}
.supir-card {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(15,23,42,0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.supir-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    background: #E8ECF2;
    transition: background 0.3s;
}
.supir-card.available::before { background: linear-gradient(180deg, #16A34A, #4ade80); }
.supir-card.busy::before      { background: linear-gradient(180deg, #D97706, #fbbf24); }
.supir-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(15,23,42,0.1);
    border-color: #8B0000;
}
.supir-avatar {
    width: 60px; height: 60px;
    border-radius: 14px;
    object-fit: cover;
    flex-shrink: 0;
    border: 2px solid #E8ECF2;
}
.supir-avatar-placeholder {
    width: 60px; height: 60px;
    border-radius: 14px;
    background: linear-gradient(135deg, #8B0000, #c0392b);
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 1.4rem;
    font-weight: 800;
    flex-shrink: 0;
    letter-spacing: 0.5px;
}
.supir-name { font-size: 1rem; font-weight: 800; color: #0F172A; margin-bottom: 3px; }
.supir-meta { font-size: 0.78rem; color: #94A3B8; }
.supir-rate { font-size: 0.9rem; font-weight: 700; color: #8B0000; }
.supir-actions { display: flex; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid #F1F5F9; }

/* Table fallback */
.supir-table-wrap {
    background: #fff;
    border: 1px solid #E8ECF2;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15,23,42,0.04);
}
.view-toggle { display: flex; gap: 4px; background: #F1F5F9; border-radius: 8px; padding: 3px; }
.view-btn {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: #64748B;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    font-family: inherit;
}
.view-btn.active { background: #fff; color: #8B0000; box-shadow: 0 1px 4px rgba(0,0,0,0.1); font-weight: 700; }
</style>

<?php
$query = mysqli_query($conn, "SELECT s.*, 
    (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') AS transaksi_aktif 
 FROM supir s WHERE s.is_deleted = 0 ORDER BY s.id_supir ASC");
$total_supir = mysqli_num_rows($query);
$supir_list = [];
while ($d = mysqli_fetch_assoc($query)) { $supir_list[] = $d; }
$total_bertugas = count(array_filter($supir_list, fn($s) => $s['transaksi_aktif'] > 0));
$total_tersedia = $total_supir - $total_bertugas;

// Rewind for rendering
?>

<!-- Page Header -->
<div class="supir-header">
    <div>
        <h1 class="page-title">Manajemen Supir</h1>
        <p class="page-subtitle">Kelola data dan ketersediaan pengemudi armada.</p>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <!-- Stats mini -->
        <span class="badge-modern success" style="font-size:0.78rem;padding:7px 14px;">
            <i class="bi bi-person-check-fill"></i> <?= $total_tersedia ?> Tersedia
        </span>
        <span class="badge-modern warning" style="font-size:0.78rem;padding:7px 14px;">
            <i class="bi bi-car-front-fill"></i> <?= $total_bertugas ?> Bertugas
        </span>
        <!-- Search -->
        <div class="search-wrap">
            <i class="bi bi-search search-icon"></i>
            <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari supir...">
        </div>
        <!-- View toggle -->
        <div class="view-toggle">
            <button class="view-btn active" id="btnGrid" onclick="switchView('grid')">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </button>
            <button class="view-btn" id="btnList" onclick="switchView('list')">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        <a href="tambah_supir.php" class="btn-primary-modern">
            <i class="bi bi-person-plus-fill"></i> Tambah Supir
        </a>
    </div>
</div>

<!-- GRID VIEW -->
<div id="gridView" class="supir-card-grid mb-5">
<?php if (empty($supir_list)): ?>
<div class="empty-state" style="grid-column:1/-1;">
    <span class="empty-state-icon"><i class="bi bi-person-x"></i></span>
    <div class="empty-state-title">Belum ada data supir</div>
    <div class="empty-state-desc">Tambahkan supir pertama Anda sekarang.</div>
</div>
<?php else: foreach ($supir_list as $data):
    $status = $data['transaksi_aktif'] > 0 ? 'busy' : 'available';
    $init   = strtoupper(substr($data['nama_supir'], 0, 1));
    $has_img = !empty($data['gambar']) && file_exists('img_supir/' . $data['gambar']);
?>
<div class="supir-card <?= $status ?>" data-name="<?= strtolower(htmlspecialchars($data['nama_supir'])) ?>">
    <div class="d-flex align-items-start gap-3">
        <?php if ($has_img): ?>
        <img src="img_supir/<?= htmlspecialchars($data['gambar']) ?>" class="supir-avatar" alt="<?= htmlspecialchars($data['nama_supir']) ?>">
        <?php else: ?>
        <div class="supir-avatar-placeholder"><?= $init ?></div>
        <?php endif; ?>
        <div class="flex-1 min-w-0">
            <div class="supir-name"><?= htmlspecialchars($data['nama_supir']) ?></div>
            <div class="supir-meta mb-2">
                <i class="bi bi-telephone-fill text-success me-1"></i><?= htmlspecialchars($data['no_telp']) ?>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="supir-rate">Rp <?= number_format($data['tarif_supir_per_hari'], 0, ',', '.') ?><span class="text-muted fw-normal" style="font-size:0.7rem;">/hari</span></span>
            </div>
        </div>
        <div>
            <?php if ($status === 'available'): ?>
            <span class="badge-modern success"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Tersedia</span>
            <?php else: ?>
            <span class="badge-modern warning"><i class="bi bi-circle-fill" style="font-size:0.45rem;"></i> Bertugas</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="supir-actions">
        <a href="edit_supir.php?id=<?= $data['id_supir'] ?>" class="action-btn edit flex-1 d-flex align-items-center justify-content-center gap-2" style="width:auto;padding:8px 14px;font-size:0.78rem;font-weight:700;border-radius:8px;">
            <i class="bi bi-pencil-fill"></i> Edit
        </a>
        <a href="hapus_supir.php?id=<?= $data['id_supir'] ?>"
           class="action-btn del d-flex align-items-center justify-content-center gap-2" 
           style="width:auto;padding:8px 14px;font-size:0.78rem;font-weight:700;border-radius:8px;"
           onclick="return confirm('Yakin hapus supir <?= htmlspecialchars(addslashes($data['nama_supir'])) ?>?')">
            <i class="bi bi-trash-fill"></i> Hapus
        </a>
    </div>
</div>
<?php endforeach; endif; ?>
</div>

<!-- LIST VIEW (hidden by default) -->
<div id="listView" class="supir-table-wrap mb-5" style="display:none;">
    <div class="panel-header">
        <div class="panel-title">
            <div class="panel-title-icon"><i class="bi bi-person-badge-fill"></i></div>
            Daftar Supir
        </div>
        <span class="badge-modern crimson"><?= $total_supir ?> Supir</span>
    </div>
    <table class="table-modern w-100">
        <thead>
            <tr>
                <th style="width:50px;">ID</th>
                <th>Nama Supir</th>
                <th>No. Telepon</th>
                <th>Tarif / Hari</th>
                <th>Status</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody id="tableBody">
        <?php if (empty($supir_list)): ?>
        <tr><td colspan="6" class="empty-state">Belum ada data supir.</td></tr>
        <?php else: foreach ($supir_list as $data):
            $status_s = $data['transaksi_aktif'] > 0 ? 'bertugas' : 'tersedia';
            $has_img  = !empty($data['gambar']) && file_exists('img_supir/' . $data['gambar']);
        ?>
        <tr data-name="<?= strtolower(htmlspecialchars($data['nama_supir'])) ?>">
            <td><span class="text-muted" style="font-size:0.8rem;"><?= $data['id_supir'] ?></span></td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($has_img): ?>
                    <img src="img_supir/<?= htmlspecialchars($data['gambar']) ?>" style="width:34px;height:34px;border-radius:8px;object-fit:cover;">
                    <?php else: ?>
                    <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#8B0000,#c0392b);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.8rem;flex-shrink:0;">
                        <?= strtoupper(substr($data['nama_supir'],0,1)) ?>
                    </div>
                    <?php endif; ?>
                    <span style="font-weight:700;color:#0F172A;"><?= htmlspecialchars($data['nama_supir']) ?></span>
                </div>
            </td>
            <td style="font-size:0.85rem;"><i class="bi bi-telephone-fill text-success me-1"></i><?= htmlspecialchars($data['no_telp']) ?></td>
            <td style="font-weight:700;color:#8B0000;">Rp <?= number_format($data['tarif_supir_per_hari'],0,',','.') ?></td>
            <td>
                <?php if ($status_s === 'tersedia'): ?>
                <span class="badge-modern success"><i class="bi bi-check-circle-fill"></i> Tersedia</span>
                <?php else: ?>
                <span class="badge-modern warning"><i class="bi bi-car-front-fill"></i> Bertugas</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center;">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <a href="edit_supir.php?id=<?= $data['id_supir'] ?>" class="action-btn edit" title="Edit">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="hapus_supir.php?id=<?= $data['id_supir'] ?>" class="action-btn del" 
                       title="Hapus" onclick="return confirm('Yakin hapus supir ini?')">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </div>
            </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

</div> </main>
</div>

<script>
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // View toggle
    function switchView(view) {
        const grid = document.getElementById('gridView');
        const list = document.getElementById('listView');
        const btnG = document.getElementById('btnGrid');
        const btnL = document.getElementById('btnList');
        if (view === 'grid') {
            grid.style.display = ''; list.style.display = 'none';
            btnG.classList.add('active'); btnL.classList.remove('active');
        } else {
            grid.style.display = 'none'; list.style.display = '';
            btnG.classList.remove('active'); btnL.classList.add('active');
        }
        localStorage.setItem('supirView', view);
    }
    // Restore last view
    const savedView = localStorage.getItem('supirView');
    if (savedView) switchView(savedView);

    // Live search across both views
    function liveSearch() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        // Grid
        document.querySelectorAll('#gridView .supir-card').forEach(c => {
            c.style.display = c.dataset.name.includes(q) ? '' : 'none';
        });
        // List
        document.querySelectorAll('#tableBody tr[data-name]').forEach(r => {
            r.style.display = r.dataset.name.includes(q) ? '' : 'none';
        });
    }
</script>
</body>
</html>