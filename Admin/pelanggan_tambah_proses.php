<?php
include 'koneksi.php';

if (isset($_POST['btn_simpan'])) {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $no_ktp  = mysqli_real_escape_string($conn, $_POST['no_ktp']);

    // Query simpan ke tabel pelanggan
    $sql = "INSERT INTO pelanggan (nama, alamat, no_telp, no_ktp) 
            VALUES ('$nama', '$alamat', '$no_telp', '$no_ktp')";
    
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