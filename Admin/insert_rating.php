<?php
include 'koneksi.php';

$q = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM rating_sewa");
$r = mysqli_fetch_assoc($q);
if ($r['cnt'] == 0) {
    echo "Inserting dummy data into rating_sewa...\n";
    $query = "INSERT INTO `rating_sewa` (`id_transaksi`, `id_pelanggan`, `rating_pelayanan`, `rating_supir`, `rating_mobil`, `ulasan`, `tgl_rating`) VALUES
    (260002, 10002, 5, 5, 5, 'Sangat memuaskan, supir ramah', NOW()),
    (260003, 10005, 4, 3, 4, 'Supir datang agak telat tapi aman', NOW()),
    (260004, 10006, 5, 4, 5, 'Mobil bersih, supir bawa mobilnya enak', NOW()),
    (260005, 10009, 3, 5, 4, 'Supirnya the best, sangat membantu', NOW());";
    if(mysqli_query($conn, $query)){
        echo "Success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Data already exists.";
}
?>
