<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
$tables = ['rating', 'ulasan', 'rating_pelanggan', 'transaksi_sewa'];
foreach($tables as $table) {
    echo "--- Table $table ---\n";
    $r = mysqli_query($conn, "DESCRIBE $table");
    if($r) {
        while($row = mysqli_fetch_array($r)) echo $row[0] . ' - ' . $row[1] . "\n";
    } else {
        echo "Not found\n";
    }
}
?>
