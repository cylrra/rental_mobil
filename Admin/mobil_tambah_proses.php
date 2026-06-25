<?php
include 'koneksi.php'; 

if (isset($_POST['simpan'])) {
    // Sesuaikan variabel dengan input dari form baru
    $kode_mobil     = mysqli_real_escape_string($conn, $_POST['kode_mobil']);
    $nopol          = mysqli_real_escape_string($conn, $_POST['nopol']);
    $merk           = mysqli_real_escape_string($conn, $_POST['merk']);
    $jenis          = mysqli_real_escape_string($conn, $_POST['jenis']);
    $tarif_12_dalam = mysqli_real_escape_string($conn, $_POST['tarif_12_dalam']);
    $tarif_12_luar  = mysqli_real_escape_string($conn, $_POST['tarif_12_luar']);
    $tarif_24_dalam = mysqli_real_escape_string($conn, $_POST['tarif_24_dalam']);
    $tarif_24_luar  = mysqli_real_escape_string($conn, $_POST['tarif_24_luar']);
    $tarif_per_hari = mysqli_real_escape_string($conn, $_POST['tarif_per_hari']);
    $Unit_Tersedia  = mysqli_real_escape_string($conn, $_POST['Unit_Tersedia']);
    
    // Default Status (Otomatis tersedia karena baru diinput)
    $status_mobil   = 'tersedia'; 

    // Ambil data file gambar
    $gambar   = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    
    // Ciptakan nama unik agar gambar tidak tertimpa jika namanya sama
    $ext = pathinfo($gambar, PATHINFO_EXTENSION);
    $nama_gambar_unik = time() . '_' . str_replace(' ', '_', $merk) . '.' . $ext;
    
    $target_dir = "img/";
    $target_file = $target_dir . $nama_gambar_unik;

    // Proses unggah gambar
    if (move_uploaded_file($tmp_name, $target_file)) {
        
        // Eksekusi SQL Insert Data (Perhatikan nama kolom sudah disesuaikan!)
        $query = "INSERT INTO mobil (kode_mobil, nopol, merk, jenis, tarif_12_dalam, tarif_12_luar, tarif_24_dalam, tarif_24_luar, tarif_per_hari, status_mobil, Unit_Tersedia, Gambar) 
                  VALUES ('$kode_mobil', '$nopol', '$merk', '$jenis', '$tarif_12_dalam', '$tarif_12_luar', '$tarif_24_dalam', '$tarif_24_luar', '$tarif_per_hari', '$status_mobil', '$Unit_Tersedia', '$nama_gambar_unik')";
        
        $exec = mysqli_query($conn, $query);

        if ($exec) {
            echo "<script>
                    alert('Data mobil berhasil ditambahkan!');
                    window.location='mobil.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menyimpan data ke database: " . mysqli_error($conn) . "');
                    window.location='mobil_tambah.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Gagal mengunggah foto. Pastikan folder img/ tersedia dan memiliki izin akses.');
                window.location='mobil_tambah.php';
              </script>";
    }
}
?>