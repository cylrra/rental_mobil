<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Sertakan navbar dan koneksi
include 'navbar.php'; 
include 'koneksi.php'; 

// Helper fungsi untuk mengubah format nomor HP menjadi format standar WhatsApp (628xxx)
function formatWhatsAppNumber($phone) {
    // Hapus semua karakter non-digit (spasi, strip, plus, dll)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Jika nomor diawali dengan '0', ubah menjadi '62'
    if (strpos($phone, '0') === 0) {
        $phone = '62' . substr($phone, 1);
    }
    
    return $phone;
}

// Tangkap kode mobil dari URL (jika ada, misalnya dari halaman detail mobil)
$kode_selected = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script src="https://unpkg.com/lucide@latest"></script>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-4xl font-black text-[#800000] tracking-tight">Manajemen Transaksi</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Catat penyewaan baru dan pantau transaksi berjalan.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <div class="w-full lg:w-1/3">
            <div class="bg-white rounded-2xl p-6 border border-[#e2e2e2] shadow-sm hover-lift">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-[#800000]/10 text-[#800000] flex items-center justify-center">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    </div>
                    <h5 class="text-lg font-bold text-[#1a1c1c]">Input Transaksi Baru</h5>
                </div>
                <form action="proses_transaksi.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Pelanggan</label>
                            <select name="id_pelanggan" class="form-select select2-js" required>
                                <option value="">-- Cari Pelanggan --</option>
                                <?php
                                $pel = mysqli_query($conn, "SELECT id_pelanggan, nama FROM pelanggan");
                                while($p = mysqli_fetch_array($pel)) {
                                    echo "<option value='{$p['id_pelanggan']}'>{$p['id_pelanggan']} - {$p['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Mobil</label>
                            <select name="kode_mobil" class="form-select select2-js" required>
                                <option value="">-- Cari Mobil Tersedia --</option>
                                <?php
                                // Mengambil mobil yang stok real-time > 0
                                $mob = mysqli_query($conn, "SELECT m.*, (CAST(m.Unit_Tersedia AS SIGNED) - (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.kode_mobil = m.kode_mobil AND t.status_sewa = 'berjalan')) AS stok_realtime FROM mobil m");
                                while($m = mysqli_fetch_array($mob)) {
                                    if ((int)$m['stok_realtime'] > 0) {
                                        $selected = ($m['kode_mobil'] == $kode_selected) ? 'selected' : '';
                                        echo "<option value='{$m['kode_mobil']}' {$selected}>{$m['merk']} - {$m['nopol']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Gunakan Jasa Supir?</label>
                            <select name="pake_supir" id="pake_supir" class="form-select" onchange="toggleSupirBlock()" required>
                                <option value="Tidak">Tidak (Lepas Kunci)</option>
                                <option value="Ya">Ya (Menggunakan Supir)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="pilihan_supir_block" style="display: none;">
                            <label class="form-label fw-bold">Pilih Supir (Tersedia)</label>
                            <input type="hidden" name="id_supir" id="id_supir_hidden" value="">
                            <div class="row g-2 mt-1">
                                <?php
                                // Supir yang tidak sedang memiliki transaksi 'berjalan'
                                $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') = 0");
                                while($s = mysqli_fetch_array($supir_query)) {
                                ?>
                                <div class="col-6">
                                    <div class="card driver-card border cursor-pointer h-100" data-id="<?= $s['id_supir'] ?>" onclick="selectDriver(this)">
                                        <div class="card-body p-2 text-center">
                                            <div class="w-10 h-10 mx-auto bg-light rounded-circle flex items-center justify-center mb-2">
                                                <i class="bi bi-person-circle fs-4 text-secondary"></i>
                                            </div>
                                            <h6 class="mb-0 text-sm fw-bold text-dark"><?= $s['nama_supir'] ?></h6>
                                            <span class="badge bg-success rounded-pill mt-1" style="font-size: 0.65rem;">Rp <?= number_format($s['tarif_supir_per_hari'], 0, ',', '.') ?>/hr</span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <small class="text-danger mt-1 d-none" id="error_supir">Silakan pilih supir terlebih dahulu.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lama Sewa (Hari)</label>
                                <input type="number" name="lama_sewa" class="form-control" min="1" required>
                            </div>
                        </div>

                        <button type="submit" name="submit" class="w-full bg-[#d4af37] text-[#1a1c1c] font-bold py-3 rounded-xl shadow-md shadow-[#d4af37]/20 hover:bg-[#c49d2b] transition-colors mt-4 flex justify-center items-center gap-2">
                            <i data-lucide="save" class="w-5 h-5"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-2xl p-6 border border-[#e2e2e2] shadow-sm hover-lift">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#800000]/10 text-[#800000] flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <h5 class="text-lg font-bold text-[#1a1c1c]">Riwayat Transaksi</h5>
                    </div>
                    <div class="relative w-full sm:w-64">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari pelanggan/ID..." class="w-full pl-9 pr-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-[#800000] focus:ring-1 focus:ring-[#800000]">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-y border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-bold">
                                <th class="p-4 rounded-tl-xl">ID</th>
                                <th class="p-4">Pelanggan</th>
                                <th class="p-4">Mobil</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                            <tbody id="tableBody">
                                <?php
                                $filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
                                $where_clause = "";
                                if ($filter_status === 'pending') {
                                    $where_clause = "WHERE t.status_sewa = 'pending'";
                                }

                                $sql = "SELECT t.*, p.nama, p.no_telp, m.merk, s.nama_supir,
                                        IFNULL((SELECT SUM(jumlah_bayar) FROM pembayaran WHERE id_sewa = t.id_sewa), 0) AS uang_dibayar
                                        FROM transaksi_sewa t
                                        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                        JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                        LEFT JOIN supir s ON t.id_supir = s.id_supir
                                        $where_clause
                                        ORDER BY t.tanggal_sewa DESC";
                                $res = mysqli_query($conn, $sql);
                                
                                if($res && mysqli_num_rows($res) > 0){
                                    while($row = mysqli_fetch_array($res)) {
                                ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 font-bold text-slate-500">#<?php echo $row['id_sewa']; ?></td>
                                    <td class="p-4"><span class="font-bold text-slate-800"><?php echo htmlspecialchars($row['nama']); ?></span></td>
                                    <td class="p-4 text-slate-600"><?php echo htmlspecialchars($row['merk']); ?></td>
                                    <td class="p-4">
                                        <div class="flex flex-col gap-1 items-start">
                                            <?php if ($row['status_sewa'] == 'berjalan'): ?>
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                    Berjalan
                                                </span>
                                            <?php elseif ($row['status_sewa'] == 'diterima'): ?>
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-600 border border-blue-200">
                                                    Diterima
                                                </span>
                                            <?php elseif ($row['status_sewa'] == 'pending'): ?>
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-600 animate-pulse border border-amber-200">
                                                    Butuh ACC
                                                </span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                                    <?php echo ucfirst(htmlspecialchars($row['status_sewa'])); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php 
                                            // Payment Status Logic
                                            if ($row['uang_dibayar'] >= $row['total_biaya']) {
                                                echo '<span class="px-3 py-1 text-[10px] font-black rounded-md bg-green-600 text-white shadow-sm shadow-green-500/30 tracking-wider">LUNAS</span>';
                                                echo '<small class="text-green-600 mt-1 font-bold">Dibayar Rp ' . number_format($row['uang_dibayar'], 0, ',', '.') . '</small>';
                                            } elseif ($row['uang_dibayar'] > 0) {
                                                echo '<span class="px-3 py-1 text-[10px] font-black rounded-md bg-indigo-600 text-white shadow-sm shadow-indigo-500/30 tracking-wider">DP</span>';
                                                echo '<small class="text-indigo-600 mt-1 font-bold">Dibayar Rp ' . number_format($row['uang_dibayar'], 0, ',', '.') . '</small>';
                                            } else {
                                                echo '<span class="px-3 py-1 text-[10px] font-black rounded-md bg-rose-500 text-white shadow-sm shadow-rose-500/30 tracking-wider">BELUM BAYAR</span>';
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <?php if ($row['status_sewa'] == 'pending'): ?>
                                                <a href="acc_transaksi_supir.php?id_sewa=<?= $row['id_sewa'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors border border-blue-200" title="ACC Otomatis (Setujui)" onclick="return confirm('Apakah Anda yakin ingin menyetujui pesanan ini? Sistem akan otomatis mencarikan supir jika dibutuhkan.');">
                                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="edit_transaksi.php?id=<?php echo $row['id_sewa']; ?>" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-600 hover:text-white transition-colors" title="Edit Transaksi">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </a>
                                            <a href="pembayaran.php?id=<?php echo $row['id_sewa']; ?>" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" title="Pembayaran">
                                                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                                            </a>
                                            <?php 
                                                $wa_customer_phone = formatWhatsAppNumber($row['no_telp']);
                                            ?>
                                            <button type="button" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-colors" title="Bot Kirim Tagihan" onclick="openBotModal('<?= $row['id_sewa'] ?>', '<?= $wa_customer_phone ?>', '<?= htmlspecialchars(addslashes($row['nama'])) ?>', '<?= $row['total_biaya'] ?>', '<?= $row['tanggal_sewa'] ?>', '<?= $row['lama_sewa'] ?>', '<?= htmlspecialchars(addslashes($row['merk'])) ?>', '<?= $row['pake_supir'] ?>', '<?= htmlspecialchars(addslashes($row['nama_supir'] ?? '')) ?>')">
                                                <i data-lucide="bot" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php } } else { ?>
                                    <tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada transaksi.</td></tr>
                                <?php } ?>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .driver-card { transition: all 0.2s ease-in-out; border-radius: 0.5rem; }
    .driver-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.04); border-color: #800000 !important; }
    .driver-card.selected { border: 2px solid #800000 !important; background-color: #ffdad4; box-shadow: 0 4px 12px rgba(128, 0, 0, 0.15); }
    
    /* Make form inputs look like tailwind with 8px moderate rounding */
    .form-control, .form-select {
        border-radius: 0.5rem;
        border-color: #e2e2e2;
        padding: 0.6rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #800000;
        box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.15);
    }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem !important;
        border-color: #e2e2e2 !important;
        padding: 0.25rem 0.5rem !important;
        min-height: 42px !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 Search Box
        $('.select2-js').select2({ theme: "bootstrap-5", width: '100%' });

        // PENTING: Memicu render ulang Lucide Icons setelah dokumen HTML siap sepenuhnya
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function toggleSupirBlock() {
        const status = document.getElementById("pake_supir").value;
        const block = document.getElementById("pilihan_supir_block");
        
        block.style.display = (status === "Ya") ? "block" : "none";
        if(status === "Tidak") {
            document.getElementById("id_supir_hidden").value = "";
            document.querySelectorAll('.driver-card').forEach(c => c.classList.remove('selected'));
            document.getElementById("error_supir").classList.add('d-none');
        }
    }

    function selectDriver(element) {
        document.querySelectorAll('.driver-card').forEach(c => c.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById("id_supir_hidden").value = element.getAttribute('data-id');
        document.getElementById("error_supir").classList.add('d-none');
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const pakeSupir = document.getElementById("pake_supir").value;
        const idSupir = document.getElementById("id_supir_hidden").value;
        
        if(pakeSupir === "Ya" && !idSupir) {
            e.preventDefault();
            document.getElementById("error_supir").classList.remove('d-none');
        }
    });

    function openAccSupirModal(idSewa) {
        document.getElementById('accSewaId').value = idSewa;
        document.getElementById('accSupirIdHidden').value = '';
        document.querySelectorAll('.acc-driver-card').forEach(c => c.classList.remove('selected'));
        const modal = new bootstrap.Modal(document.getElementById('accSupirModal'));
        modal.show();
    }

    function selectAccDriver(element) {
        document.querySelectorAll('.acc-driver-card').forEach(c => c.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('accSupirIdHidden').value = element.getAttribute('data-id');
    }

    function submitAccSupir() {
        const supirId = document.getElementById('accSupirIdHidden').value;
        if (!supirId) {
            alert('Pilih supir terlebih dahulu!');
            return;
        }
        document.getElementById('formAccSupir').submit();
    }

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

<!-- MODAL ACC SUPIR -->
<div class="modal fade" id="accSupirModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-slate-800 text-white border-0 py-3">
                <h5 class="modal-title font-bold flex items-center gap-2">
                    <i data-lucide="check-square" class="w-5 h-5"></i> ACC Pesanan & Pilih Supir
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-6 bg-slate-50">
                <p class="text-slate-600 mb-4 font-medium">Pesanan ini membutuhkan jasa supir. Silakan tugaskan supir yang tersedia sebelum menyetujui transaksi.</p>
                
                <form id="formAccSupir" method="POST" action="acc_transaksi_supir.php">
                    <input type="hidden" name="id_sewa" id="accSewaId" value="">
                    <input type="hidden" name="id_supir" id="accSupirIdHidden" value="">
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <?php
                        $supir_query = mysqli_query($conn, "SELECT s.* FROM supir s WHERE (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') = 0");
                        while($s = mysqli_fetch_array($supir_query)) {
                        ?>
                        <div class="card acc-driver-card driver-card border cursor-pointer h-100 bg-white" data-id="<?= $s['id_supir'] ?>" onclick="selectAccDriver(this)">
                            <div class="card-body p-3 text-center">
                                <div class="w-12 h-12 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-2">
                                    <i class="bi bi-person-circle fs-3 text-slate-400"></i>
                                </div>
                                <h6 class="mb-0 text-sm font-bold text-slate-800"><?= $s['nama_supir'] ?></h6>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 bg-slate-100">
                <button type="button" class="px-4 py-2 bg-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-400" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700" onclick="submitAccSupir()">Setujui Transaksi</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="botModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold flex items-center gap-2">
                    <i class="bi bi-robot fs-4"></i> Indomax Rental Mobil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-50">
                <div id="botProcessing" class="text-center py-4">
                    <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="fw-bold text-slate-700">Bot sedang memproses tagihan...</h5>
                    <p class="text-slate-500 text-sm">Menghubungkan ke server WhatsApp...</p>
                </div>
                
                <div id="botSuccess" class="text-center py-4 d-none">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm">
                        <i class="bi bi-check-lg fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-slate-800">Terkirim!</h4>
                    <p class="text-slate-600 mb-0">Bot otomatis berhasil mengirim tagihan ke WhatsApp <strong id="botCustomerName"></strong> (<span id="botCustomerPhone"></span>).</p>
                    
                    <div class="mt-4 text-start bg-white p-3 rounded-lg border border-slate-200 shadow-sm relative">
                        <div class="absolute -top-3 left-4 bg-white px-2 text-xs font-bold text-green-600">Pesan Terkirim</div>
                        <p class="text-sm text-slate-700 font-mono mb-0 whitespace-pre-wrap" id="botMessageContent"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openBotModal(idSewa, phone, nama, totalBiaya, tglSewa, lamaSewa, merk, pakeSupir, namaSupir, isAcc = false) {
    if (!phone || phone.trim() === "") {
        alert("Gagal memproses: Nomor WhatsApp pelanggan tidak valid atau belum terdaftar!");
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('botModal'));
    
    document.getElementById('botProcessing').classList.remove('d-none');
    document.getElementById('botSuccess').classList.add('d-none');
    
    document.getElementById('botCustomerName').textContent = nama;
    document.getElementById('botCustomerPhone').textContent = '+' + phone;
    
    const formatter = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
    
    let supirText = (pakeSupir === 'Ya') ? `\n👨‍✈️ Supir: ${namaSupir}` : `\n👨‍✈️ Supir: Tidak (Lepas Kunci)`;
    let noteText = (pakeSupir === 'Ya') ? `\n*Catatan:* Biaya di atas belum termasuk pengisian BBM, tarif Tol, Parkir, dan Uang Makan Supir.` : `\n*Catatan:* Biaya di atas belum termasuk pengisian BBM, tarif Tol, dan Parkir.`;

    let message = '';
    if (isAcc) {
        message = `Halo Kak *${nama}*,\n\nKabar gembira! Pesanan Anda di *Indomax Rental Mobil* telah *DISETUJUI (ACC)*.\n\n*Detail Pesanan:*\n🔖 ID Transaksi: #${idSewa}\n📅 Tanggal Sewa: ${tglSewa}\n⏳ Lama Sewa: ${lamaSewa} Hari\n🚗 Mobil: ${merk}${supirText}\n\n💰 *Total Biaya: ${formatter.format(totalBiaya)}*\n\nMohon segera melengkapi pembayaran agar kendaraan dapat disiapkan.\n\nTerima kasih! 🙏`;
    } else {
        message = `Halo Kak *${nama}*,\n\nBerikut adalah rincian tagihan pesanan Anda di *Indomax Rental Mobil*:\n\n*Detail Pesanan:*\n🔖 ID Transaksi: #${idSewa}\n📅 Tanggal Sewa: ${tglSewa}\n⏳ Lama Sewa: ${lamaSewa} Hari\n🚗 Mobil: ${merk}${supirText}\n\n💰 *Total Tagihan: ${formatter.format(totalBiaya)}*\n${noteText}\n\nTerima kasih telah mempercayakan perjalanan Anda bersama Indomax Rental Mobil! 🙏`;
    }
    
    document.getElementById('botMessageContent').textContent = message;
    
    modal.show();
    
    setTimeout(() => {
        document.getElementById('botProcessing').classList.add('d-none');
        document.getElementById('botSuccess').classList.remove('d-none');
        
        window.open('https://wa.me/' + phone + '?text=' + encodeURIComponent(message), '_blank');
    }, 1500);
}
</script>

<?php
if (isset($_GET['acc_id'])) {
    $acc_id = mysqli_real_escape_string($conn, $_GET['acc_id']);
    $q_bot = mysqli_query($conn, "SELECT t.*, p.nama, p.no_telp, m.merk, s.nama_supir 
                                  FROM transaksi_sewa t
                                  JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                  JOIN mobil m ON t.kode_mobil = m.kode_mobil
                                  LEFT JOIN supir s ON t.id_supir = s.id_supir
                                  WHERE t.id_sewa = '$acc_id'");
    if ($row_bot = mysqli_fetch_assoc($q_bot)) {
        $phone = formatWhatsAppNumber($row_bot['no_telp']);
        $nama = addslashes($row_bot['nama']);
        $biaya = $row_bot['total_biaya'];
        $tgl = $row_bot['tanggal_sewa'];
        $lama = $row_bot['lama_sewa'];
        $merk = addslashes($row_bot['merk']);
        $pake_supir = $row_bot['pake_supir'];
        $nama_supir = addslashes($row_bot['nama_supir'] ?? '');
        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                openBotModal('$acc_id', '$phone', '$nama', '$biaya', '$tgl', '$lama', '$merk', '$pake_supir', '$nama_supir', true);
            }, 500);
        });
        </script>";
    }
}
?>
</body>
</html>