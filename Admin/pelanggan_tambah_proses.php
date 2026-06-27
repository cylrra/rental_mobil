<?php
include 'koneksi.php';

if (isset($_POST['btn_simpan'])) {
    $nama              = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email             = mysqli_real_escape_string($conn, trim($_POST['email']));
    $alamat            = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $no_telp           = mysqli_real_escape_string($conn, trim($_POST['no_telp']));
    $no_ktp            = mysqli_real_escape_string($conn, trim($_POST['no_ktp']));
    $username          = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password          = trim($_POST['password']);
    $status_verifikasi = mysqli_real_escape_string($conn, $_POST['status_verifikasi']);

    // Pastikan username tidak kosong
    if (empty($username) || empty($password)) {
        echo "<script>
                alert('Username dan Password wajib diisi!');
                window.history.back();
              </script>";
        exit();
    }

    // Cek apakah username sudah terdaftar
    $stmt_cek = mysqli_prepare($conn, "SELECT * FROM pelanggan WHERE username = ?");
    mysqli_stmt_bind_param($stmt_cek, "s", $username);
    mysqli_stmt_execute($stmt_cek);
    $cek_user = mysqli_stmt_get_result($stmt_cek);
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>
                alert('Username sudah digunakan, pilih username lain!');
                window.history.back();
              </script>";
        exit();
    }

    // Enkripsi password
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Pastikan nomor telepon berformat Indonesia (62...)
    $no_telp = preg_replace('/[^0-9]/', '', $no_telp);
    if (strpos($no_telp, '0') === 0) {
        $no_telp = '62' . substr($no_telp, 1);
    } elseif (strpos($no_telp, '8') === 0) {
        $no_telp = '62' . $no_telp;
    }

    // Query simpan ke tabel pelanggan
    $stmt = mysqli_prepare($conn, "INSERT INTO pelanggan (nama, email, username, password, alamat, no_telp, no_ktp, status_verifikasi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssss", $nama, $email, $username, $password_hashed, $alamat, $no_telp, $no_ktp, $status_verifikasi);
    
    $query = mysqli_stmt_execute($stmt);

    if ($query) {
        echo "<script>
                alert('Data pelanggan berhasil disimpan!');
                window.location.href = 'pelanggan.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
}
?>