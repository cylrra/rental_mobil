<?php
include 'koneksi.php';

$tables = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($tables)) {
    echo "Table: " . $row[0] . "\n";
    $columns = mysqli_query($conn, "SHOW COLUMNS FROM " . $row[0]);
    while ($col = mysqli_fetch_assoc($columns)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
?>
