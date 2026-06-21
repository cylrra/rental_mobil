<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = mysqli_connect('localhost', 'root', '', 'rental_mobil');
// Try to select transaction 1
$query = mysqli_query($conn, "SELECT t.*, p.nama, m.merk, m.tarif_per_hari FROM transaksi_sewa t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan JOIN mobil m ON t.kode_mobil = m.kode_mobil LIMIT 1");
if (!$query) {
    echo "SELECT ERROR: " . mysqli_error($conn) . "\n";
} else {
    $trx = mysqli_fetch_assoc($query);
    if (!$trx) {
        echo "No transaction exists at all.\n";
    } else {
        echo "Transaction found: ID " . $trx['id_sewa'] . "\n";
        $id_sewa = $trx['id_sewa'];
        
        $pake_supir_baru = 'Ya';
        $id_supir_db = 1; // Assuming 1 exists
        $biaya_supir_baru = 200000;
        $total_harga_baru = $trx['total_biaya'] + 200000;
        
        $update_query = "UPDATE transaksi_sewa SET 
                            pake_supir = '$pake_supir_baru', 
                            id_supir = $id_supir_db, 
                            biaya_supir = '$biaya_supir_baru', 
                            total_biaya = '$total_harga_baru' 
                         WHERE id_sewa = $id_sewa";
                         
        if (mysqli_query($conn, $update_query)) {
            echo "UPDATE OK\n";
        } else {
            echo "UPDATE ERROR: " . mysqli_error($conn) . "\n";
        }
    }
}
?>
