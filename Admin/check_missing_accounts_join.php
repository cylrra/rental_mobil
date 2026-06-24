<?php
include 'koneksi.php';
$query_riwayat = "SELECT j.tanggal, j.kode_akun, a.nama_akun, j.keterangan, j.Debit, j.Kredit 
                  FROM jurnal j
                  LEFT JOIN nama_akun a ON j.kode_akun = a.kode_akun
                  WHERE a.nama_akun IS NULL
                  ORDER BY j.tanggal DESC, j.id_jurnal DESC";

$res = mysqli_query($conn, $query_riwayat);
while($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r)."\n";
}
?>
