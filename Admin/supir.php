<?php
include 'koneksi.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Supir</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{
            background-color: #f4f6f9;
        }

        .card-custom{
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .table thead th{
            background: #f8f9fa;
            font-weight: 600;
        }

        .status-tersedia{
            background: #d1f7dc;
            color: #198754;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-bertugas{
            background: #ffe2e2;
            color: #dc3545;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-tambah{
            border-radius: 30px;
            padding: 8px 20px;
        }

        .table td{
            vertical-align: middle;
        }

        .icon-action{
            text-decoration: none;
            margin: 0 5px;
            font-size: 18px;
        }

        .edit{
            color: #0d6efd;
        }

        .hapus{
            color: #dc3545;
        }
    </style>
</head>

<body>

<div class="container mt-5">

    <div class="card card-custom p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="bi bi-person-badge-fill text-primary"></i>
                Data Supir
            </h3>

            <a href="tambah_supir.php" class="btn btn-primary btn-tambah">
                <i class="bi bi-person-plus-fill"></i>
                Tambah Supir
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Supir</th>
                        <th>No. Telepon</th>
                        <th>Tarif / Hari</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                // PERBAIKAN UTAMA: Query menggunakan LEFT JOIN ke transaksi_sewa untuk memeriksa status aktif supir secara real-time
                $sql = "SELECT s.*, 
                               IF(COUNT(t.id_sewa) > 0, 'bertugas', 'tersedia') AS status_realtime
                        FROM supir s
                        LEFT JOIN transaksi_sewa t ON s.id_id_supir = t.id_supir AND t.status_sewa = 'berjalan'
                        GROUP BY s.id_supir";

                // Catatan: Jika relasi kolom kunci supir Anda adalah 'id_supir', ganti 's.id_id_supir' di atas menjadi 's.id_supir'
                $query = mysqli_query($conn, "SELECT s.*, (SELECT COUNT(*) FROM transaksi_sewa t WHERE t.id_supir = s.id_supir AND t.status_sewa = 'berjalan') AS transaksi_aktif FROM supir s");

                while($data = mysqli_fetch_assoc($query)){
                    // Jika ada transaksi sewa yang masih 'berjalan', status otomatis 'bertugas'
                    $status_sekarang = ($data['transaksi_aktif'] > 0) ? 'bertugas' : 'tersedia';
                ?>

                <tr>
                    <td><?= $data['id_supir']; ?></td>

                    <td>
                        <i class="bi bi-person-circle text-secondary me-1"></i>
                        <?= $data['nama_supir']; ?>
                    </td>

                    <td>
                        <i class="bi bi-telephone-fill text-success me-1"></i>
                        <?= $data['no_telp']; ?>
                    </td>

                    <td>
                        Rp <?= number_format($data['tarif_supir_per_hari'],0,',','.'); ?>
                    </td>

                    <td>
                        <?php if($status_sekarang == 'tersedia'){ ?>
                            <span class="status-tersedia">
                                Tersedia
                            </span>
                        <?php } else { ?>
                            <span class="status-bertugas">
                                Bertugas
                            </span>
                        <?php } ?>
                    </td>

                    <td>
                        <a href="edit_supir.php?id=<?= $data['id_supir']; ?>" class="icon-action edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <a href="hapus_supir.php?id=<?= $data['id_supir']; ?>"
                           class="icon-action hapus"
                           onclick="return confirm('Yakin ingin menghapus data supir ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <?php } ?>

                </tbody>

            </table>
        </div>

    </div>

</div>

</div>
    </div>
</div>

</body>
</html>