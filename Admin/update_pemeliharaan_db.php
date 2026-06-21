<?php
include 'koneksi.php';

// Add status column to pemeliharaan table
$sql = "ALTER TABLE pemeliharaan ADD COLUMN status enum('terjadwal', 'selesai') DEFAULT 'terjadwal' AFTER keterangan";

if(mysqli_query($conn, $sql)){
    echo "Column 'status' added successfully.\n";
} else {
    echo "Error adding column or it already exists: " . mysqli_error($conn) . "\n";
}

// Update existing records to 'selesai'
$sql_update = "UPDATE pemeliharaan SET status = 'selesai'";
mysqli_query($conn, $sql_update);
echo "Existing records marked as 'selesai'.\n";

?>
