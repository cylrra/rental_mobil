<?php
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
if (!$conn) die("Connection failed");

$q = "ALTER TABLE rating_sewa ADD COLUMN jawaban_admin TEXT NULL AFTER ulasan";
if (mysqli_query($conn, $q)) {
    echo "Added jawaban_admin successfully.\n";
} else {
    // If it already exists, it will throw an error, which is fine
    echo "Error or already exists: " . mysqli_error($conn) . "\n";
}
?>
