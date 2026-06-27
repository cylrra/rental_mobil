<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

if (isset($_POST['submit'])) {
    // Ambil data dari form dan bersihkan input
    $kode_akun = mysqli_real_escape_with_verify($conn, $_POST['kode_akun']); // Jika ada fungsi kustom, atau gunakan yang di bawah
    $kode_akun = mysqli_real_escape_string($conn, trim($_POST['kode_akun']));
    $nama_akun = mysqli_real_escape_string($conn, trim($_POST['nama_akun']));
    $saldo_awal = mysqli_real_escape_string($conn, $_POST['saldo_awal']);

    // Validasi: Cek apakah kode akun sudah digunakan sebelumnya
    $stmt_cek = mysqli_prepare($conn, "SELECT kode_akun FROM nama_akun WHERE kode_akun = ?");
    mysqli_stmt_bind_param($stmt_cek, "s", $kode_akun);
    mysqli_stmt_execute($stmt_cek);
    $cek_kode = mysqli_stmt_get_result($stmt_cek);
    
    if (mysqli_num_rows($cek_kode) > 0) {
        echo "<script>
                alert('Gagal! Kode akun $kode_akun sudah digunakan.');
                window.location.href = 'index.php'; // Sesuaikan dengan nama file utama COA Anda
              </script>";
    } else {
        // Query Insert Data
        $query_insert = "INSERT INTO nama_akun (kode_akun, nama_akun, saldo_awal) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $query_insert);
        mysqli_stmt_bind_param($stmt_insert, "ssd", $kode_akun, $nama_akun, $saldo_awal);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            echo "<script>
                    alert('Akun berhasil ditambahkan!');
                    window.location.href = 'index.php'; // Sesuaikan dengan nama file utama COA Anda
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menyimpan data ke database.');
                    window.location.href = 'index.php';
                  </script>";
        }
    }
} else {
    // Jika diakses langsung tanpa submit form, tendang kembali ke halaman utama
    header("Location: coa.php");
    exit();
}
?>