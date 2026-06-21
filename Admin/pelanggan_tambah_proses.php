<?php
include 'koneksi.php';

if (isset($_POST['btn_simpan'])) {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $no_ktp  = mysqli_real_escape_string($conn, $_POST['no_ktp']);

    // Pastikan nomor telepon berformat Indonesia (62...)
    $no_telp = preg_replace('/[^0-9]/', '', $no_telp);
    if (strpos($no_telp, '0') === 0) {
        $no_telp = '62' . substr($no_telp, 1);
    } elseif (strpos($no_telp, '8') === 0) {
        $no_telp = '62' . $no_telp;
    }

    // Query simpan ke tabel pelanggan
    $sql = "INSERT INTO pelanggan (nama, email, alamat, no_telp, no_ktp) 
            VALUES ('$nama', '$email', '$alamat', '$no_telp', '$no_ktp')";
    
    $query = mysqli_query($conn, $sql);

    if ($query) {
        echo "<script>
                alert('Data berhasil disimpan!');
                window.location.href = 'pelanggan.php'; // Ganti dengan nama file utama Anda
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan data: " . mysqli_error($conn) . "');
                window.history.back();
              </script>";
    }
}
?>