<?php
include 'koneksi.php';

$query = "SELECT id_pelanggan, no_telp FROM pelanggan";
$result = mysqli_query($conn, $query);

$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id_pelanggan'];
    $no_telp = $row['no_telp'];
    $new_no_telp = $no_telp;

    // Remove any non-numeric characters like + or spaces
    $new_no_telp = preg_replace('/[^0-9]/', '', $new_no_telp);

    // Format if it starts with 0
    if (strpos($new_no_telp, '0') === 0) {
        $new_no_telp = '62' . substr($new_no_telp, 1);
    } 
    // Format if it directly starts with 8
    elseif (strpos($new_no_telp, '8') === 0) {
        $new_no_telp = '62' . $new_no_telp;
    }

    if ($new_no_telp !== $no_telp) {
        mysqli_query($conn, "UPDATE pelanggan SET no_telp = '$new_no_telp' WHERE id_pelanggan = '$id'");
        $count++;
    }
}
echo "Success! Updated $count phone numbers to Indonesian format.";
?>
