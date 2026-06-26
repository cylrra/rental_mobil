<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';

if (isset($_POST['simpan_gaji'])) {
    $tanggal      = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jenis_gaji   = mysqli_real_escape_string($conn, $_POST['jenis_gaji']);
    $nominal      = mysqli_real_escape_string($conn, $_POST['nominal']);
    $sumber_dana  = mysqli_real_escape_string($conn, $_POST['sumber_dana']);
    $keterangan   = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    $akun_debit = ($jenis_gaji === 'admin') ? '511' : '518';
    
    if ($jenis_gaji === 'supir') {
        $id_supir = mysqli_real_escape_string($conn, $_POST['id_supir']);
        
        // Kalkulasi ulang secara aman di server
        $unpaid_query = mysqli_query($conn, "SELECT SUM(lama_sewa) as total_hari FROM transaksi_sewa WHERE status_sewa = 'selesai' AND pake_supir = 'Ya' AND status_gaji_supir = 'belum' AND id_supir = '$id_supir'");
        $u_row = mysqli_fetch_assoc($unpaid_query);
        $total_hari = $u_row['total_hari'] ?? 0;
        
        $q_supir = mysqli_query($conn, "SELECT nama_supir, tarif_supir_per_hari FROM supir WHERE id_supir = '$id_supir'");
        $s = mysqli_fetch_assoc($q_supir);
        
        $nominal = ($total_hari * $s['tarif_supir_per_hari']) * 0.85; // Timpa input dari form dengan data aman (85%)
        
        if ($nominal <= 0) {
            echo "<script>alert('Gagal: Tidak ada tagihan gaji yang valid untuk supir ini.'); window.history.back();</script>";
            exit;
        }

        $nama_supir = $s['nama_supir'];
        $keterangan = "Gaji Supir: " . $nama_supir . " (" . $total_hari . " Hari)";
    } else {
        $keterangan = "Gaji Admin - " . $keterangan;
        // Nominal menggunakan input dari form agar bisa ditambah uang makan secara manual
    }

    mysqli_begin_transaction($conn);
    try {
        // Baris Debit: Beban Gaji bertambah
        $q_debit = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                    VALUES ('$tanggal', '$akun_debit', '$nominal', 0, '$keterangan', 0)";
        if (!mysqli_query($conn, $q_debit)) throw new Exception(mysqli_error($conn));
        
        // Baris Kredit: Kas / Bank berkurang
        $q_kredit = "INSERT INTO jurnal (tanggal, kode_akun, Debit, Kredit, keterangan, id_sumber) 
                     VALUES ('$tanggal', '$sumber_dana', 0, '$nominal', '$keterangan', 0)";
        if (!mysqli_query($conn, $q_kredit)) throw new Exception(mysqli_error($conn));
        
        if ($jenis_gaji === 'supir') {
            // Update transaksi jadi 'sudah dibayar' gajinya
            $q_update_trx = "UPDATE transaksi_sewa SET status_gaji_supir = 'sudah' WHERE status_sewa = 'selesai' AND pake_supir = 'Ya' AND status_gaji_supir = 'belum' AND id_supir = '$id_supir'";
            if (!mysqli_query($conn, $q_update_trx)) throw new Exception("Gagal update status transaksi: " . mysqli_error($conn));
        }
        
        mysqli_commit($conn);
        echo "<script>alert('Pembayaran Gaji Berhasil Diproses!'); window.location.href='riwayat_jurnal_umum.php';</script>";
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Gagal: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        exit;
    }
}
?>

<div class="p-8">
    <div class="mb-8 text-center max-w-2xl mx-auto">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Pembayaran Gaji</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Proses pencairan gaji Supir dan Staff Admin, terhubung langsung ke Buku Besar.</p>
    </div>

    <div class="flex justify-center">
        <div class="w-full lg:w-1/2">
            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-100">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i data-lucide="coins" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-slate-800">Form Penggajian Baru</h4>
                        <p class="text-sm text-slate-500 font-medium">Uang akan ditarik dari Kas / Bank pilihan Anda</p>
                    </div>
                </div>
                
                <form action="" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-slate-50" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Penerima Gaji</label>
                            <?php $default_jenis = isset($_GET['jenis']) && $_GET['jenis'] === 'admin' ? 'admin' : 'supir'; ?>
                            <select name="jenis_gaji" id="jenis_gaji" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white" required onchange="toggleSupir()">
                                <option value="supir" <?= $default_jenis === 'supir' ? 'selected' : '' ?>>Supir / Driver</option>
                                <option value="admin" <?= $default_jenis === 'admin' ? 'selected' : '' ?>>Staff Admin</option>
                            </select>
                        </div>
                    </div>

                    <div id="supir_container" class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Supir</label>
                        <select name="id_supir" id="id_supir" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white">
                            <option value="">-- Pilih Nama Supir --</option>
                            <?php 
                            // Load unpaid salary for each driver
                            $unpaid_query = mysqli_query($conn, "
                                SELECT id_supir, SUM(lama_sewa) as total_hari 
                                FROM transaksi_sewa 
                                WHERE status_sewa = 'selesai' AND pake_supir = 'Ya' AND status_gaji_supir = 'belum' 
                                GROUP BY id_supir
                            ");
                            $unpaid_data = [];
                            while($row = mysqli_fetch_assoc($unpaid_query)) {
                                $unpaid_data[$row['id_supir']] = $row['total_hari'];
                            }

                            $q = mysqli_query($conn, "SELECT * FROM supir");
                            while($s = mysqli_fetch_array($q)) {
                                $hari_belum_dibayar = isset($unpaid_data[$s['id_supir']]) ? $unpaid_data[$s['id_supir']] : 0;
                                $gaji_belum_dibayar = $hari_belum_dibayar * $s['tarif_supir_per_hari'] * 0.85;
                                
                                $label_gaji = ($gaji_belum_dibayar > 0) ? " (Belum dibayar: Rp ".number_format($gaji_belum_dibayar,0,',','.').")" : "";
                                echo "<option value='".$s['id_supir']."' data-gaji='".$gaji_belum_dibayar."'>".$s['nama_supir'].$label_gaji."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sumber Dana Pembayaran</label>
                        <select name="sumber_dana" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white" required>
                            <option value="111">Kas (Uang Tunai)</option>
                            <option value="1121">Bank BCA (1121)</option>
                            <option value="1122">Bank BNI (1122)</option>
                            <option value="1123">Bank Mandiri (1123)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nominal Gaji (Rp)</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-bold text-purple-600">Rp</span>
                            <input type="number" name="nominal" class="w-full rounded-xl border-slate-200 pl-12 pr-4 py-3 bg-white font-bold text-purple-700" placeholder="Masukkan Nominal Gaji" min="1" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan / Periode</label>
                        <input type="text" name="keterangan" class="w-full rounded-xl border-slate-200 px-4 py-3 bg-white" placeholder="Contoh: Gaji Bulan Juni 2026" required>
                    </div>

                    <button type="submit" name="simpan_gaji" class="w-full bg-purple-600 text-white font-bold py-3 rounded-xl shadow-md hover:bg-purple-700 transition-colors flex justify-center items-center gap-2 mt-4" onclick="return confirm('Apakah Anda yakin ingin melakukan pembayaran gaji ini? Saldo Kas/Bank akan berkurang.')">
                        <i data-lucide="check-circle" class="w-5 h-5"></i> Proses Pencairan Gaji
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSupir() {
        var jenis = document.getElementById('jenis_gaji').value;
        var supirContainer = document.getElementById('supir_container');
        var supirSelect = document.getElementById('id_supir');
        var nominalInput = document.querySelector('input[name="nominal"]');
        var keteranganInput = document.querySelector('input[name="keterangan"]');

        if (jenis === 'supir') {
            supirContainer.style.display = 'block';
            supirSelect.required = true;
            nominalInput.readOnly = true; // Auto-calculated
            
            // Trigger change to update nominal if a driver is already selected
            var evt = new Event('change');
            supirSelect.dispatchEvent(evt);
        } else if (jenis === 'admin') {
            supirContainer.style.display = 'none';
            supirSelect.required = false;
            supirSelect.value = '';
            nominalInput.readOnly = false; // Bisa diedit untuk ditambah uang makan
            nominalInput.value = '1000000'; // Default gaji pokok
            keteranganInput.value = '';
        }
    }

    document.getElementById('id_supir').addEventListener('change', function() {
        var jenis = document.getElementById('jenis_gaji').value;
        if(jenis !== 'supir') return;

        var selectedOpt = this.options[this.selectedIndex];
        if (selectedOpt && selectedOpt.value !== "") {
            var gaji = selectedOpt.getAttribute('data-gaji');
            document.querySelector('input[name="nominal"]').value = gaji;
            document.querySelector('input[name="keterangan"]').value = "Pencairan Gaji Otomatis";
        } else {
            document.querySelector('input[name="nominal"]').value = "";
            document.querySelector('input[name="keterangan"]').value = "";
        }
    });

    document.addEventListener('DOMContentLoaded', toggleSupir);
    lucide.createIcons();
</script>
</main></div>
</body>
</html>
