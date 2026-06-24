<?php
include 'koneksi.php';
$result = mysqli_query($conn, "DESCRIBE rating_sewa");
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data, JSON_PRETTY_PRINT);
?>
