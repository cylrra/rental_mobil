<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';
?>

<div class="card shadow-sm border-0 rounded-4 bg-white p-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h3 class="mb-0 fw-bold">
            <i class="bi bi-person-badge-fill text-primary"></i>
            Data Supir
        </h3>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" onkeyup="liveSearch()" class="form-control rounded-pill border-0 bg-light" placeholder="Cari nama supir...">
            <a href="tambah_supir.php" class="btn btn-primary rounded-pill px-4 text-nowrap">
                <i class="bi bi-person-plus-fill me-1"></i>
                Tambah Supir
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Nama Supir</th>
                    <th>No. Telepon</th>
                    <th>Tarif / Hari</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
            // Query real-time status supir berdasarkan transaksi aktif
            $query = mysqli_query($conn, "SELECT s.*, 
                        (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') AS transaksi_aktif 
                     FROM supir s ORDER BY s.id_supir ASC");

            if ($query && mysqli_num_rows($query) > 0) {
                while($data = mysqli_fetch_assoc($query)){
                    $status_sekarang = ($data['transaksi_aktif'] > 0) ? 'bertugas' : 'tersedia';
            ?>
                <tr>
                    <td class="ps-3 text-muted"><?= $data['id_supir']; ?></td>
                    <td>
                        <!-- Menggunakan d-inline-flex agar gambar dan teks mengunci sejajar ke samping -->
                        <div class="d-inline-flex align-items-center">
                            <?php if (!empty($data['gambar']) && file_exists('img_supir/' . $data['gambar'])) { ?>
                                <img src="img_supir/<?= htmlspecialchars($data['gambar']); ?>" alt="Foto <?= htmlspecialchars($data['nama_supir']); ?>" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover; flex-shrink: 0;">
                            <?php } else { ?>
                                <i class="bi bi-person-circle text-secondary fs-5 me-2"></i>
                            <?php } ?>
                            <span class="fw-bold"><?= htmlspecialchars($data['nama_supir']); ?></span>
                        </div>
                    </td>
                    <td>
                        <i class="bi bi-telephone-fill text-success me-1"></i>
                        <?= htmlspecialchars($data['no_telp']); ?>
                    </td>
                    <td class="fw-bold">Rp <?= number_format($data['tarif_supir_per_hari'],0,',','.'); ?></td>
                    <td>
                        <?php if($status_sekarang == 'tersedia'){ ?>
                            <span class="badge rounded-pill bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Tersedia
                            </span>
                        <?php } else { ?>
                            <span class="badge rounded-pill bg-danger px-3 py-2">
                                <i class="bi bi-car-front me-1"></i>Bertugas
                            </span>
                        <?php } ?>
                    </td>
                    <td class="text-center">
                        <a href="edit_supir.php?id=<?= $data['id_supir']; ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="hapus_supir.php?id=<?= $data['id_supir']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Yakin ingin menghapus data supir ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada data supir.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</div> </main>
</div>
<script>
    lucide.createIcons();

    function liveSearch() {
        const input = document.getElementById("searchInput");
        const filter = input.value.toUpperCase();
        const tbody = document.getElementById("tableBody");
        const tr = tbody.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            if (tr[i].getElementsByTagName("td").length > 0) {
                const textContent = tr[i].textContent || tr[i].innerText;
                if (textContent.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>
</body>
</html>