<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php';
include 'koneksi.php';

// Detektif Koneksi
$db_connection = null;
if (isset($conn)) { $db_connection = $conn; }
elseif (isset($koneksi)) { $db_connection = $koneksi; }
elseif (isset($db)) { $db_connection = $db; }
elseif (isset($link)) { $db_connection = $link; }

if (!$db_connection) {
    die("<div style='color:red; padding:20px; border:2px solid red; background:#fff5f5; font-family:sans-serif;'>
            <h3>[ERROR KONEKSI]</h3>
            <p>Sistem tidak menemukan variabel koneksi database Anda.</p>
         </div>");
}

// Query mengambil data dari tabel jurnal dan relasikan ke nama_akun jika diperlukan
// Catatan: Memperhatikan kolom 'Debit' (D kapital) dan 'kredit' (k kecil) sesuai gambar phpMyAdmin
$query_riwayat = "SELECT j.tanggal, j.kode_akun, a.nama_akun, j.keterangan, j.Debit, j.kredit 
                  FROM jurnal j
                  LEFT JOIN nama_akun a ON j.kode_akun = a.kode_akun
                  ORDER BY j.tanggal DESC, j.id_jurnal DESC";

$result = mysqli_query($db_connection, $query_riwayat);
?>

<div class="p-8">
    <div class="mb-8 text-center max-w-2xl mx-auto">
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Riwayat Buku Jurnal</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Daftar seluruh mutasi debit dan kredit yang telah dibukukan ke dalam sistem.</p>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-slate-800 font-sans">Log Transaksi Jurnal</h4>
                    <p class="text-xs text-slate-500 font-medium">Data diurutkan berdasarkan tanggal terbaru</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-sm font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="p-4 pl-6">Tanggal</th>
                            <th class="p-4">Kode Akun</th>
                            <th class="p-4">Nama Akun</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4 text-right">Debit</th>
                            <th class="p-4 text-right pr-6">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-sm">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Format angka menjadi Rupiah yang rapi
                                $debit_format  = $row['Debit'] > 0 ? "Rp " . number_format($row['Debit'], 0, ',', '.') : "-";
                                $kredit_format = $row['kredit'] > 0 ? "Rp " . number_format($row['kredit'], 0, ',', '.') : "-";
                                
                                // Teks nama akun cadangan jika join kosong
                                $nama_akun = !empty($row['nama_akun']) ? $row['nama_akun'] : 'Akun Tidak Ditemukan';
                                
                                echo "<tr class='hover:bg-slate-50/80 transition-colors'>";
                                echo "<td class='p-4 pl-6 font-medium text-slate-600'>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                echo "<td class='p-4'><span class='px-2 py-1 bg-slate-100 text-slate-800 rounded-md font-mono text-xs font-bold'>" . $row['kode_akun'] . "</span></td>";
                                echo "<td class='p-4 font-medium'>" . $nama_akun . "</td>";
                                echo "<td class='p-4 text-slate-500'>" . htmlspecialchars($row['keterangan']) . "</td>";
                                echo "<td class='p-4 text-right font-bold text-emerald-600'>" . $debit_format . "</td>";
                                echo "<td class='p-4 text-right font-bold text-rose-600 pr-6'>" . $kredit_format . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='p-8 text-center text-slate-400 italic'>Belum ada riwayat transaksi jurnal yang tercatat.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
</body>
</html>