<?php
include 'auth.php';
requireAdmin();
include 'koneksi.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Supir Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .card-custom { border-radius: 20px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom p-4">
                <h4 class="mb-4 fw-bold text-primary"><i class="bi bi-person-plus-fill"></i> Tambah Supir Baru</h4>
                
                <form action="proses_tambah_supir.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ID Supir (Primary Key)</label>
                        <input type="number" name="id_supir" class="form-control" placeholder="Contoh: 10090" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap Supir</label>
                        <input type="text" name="nama_supir" class="form-control" placeholder="Contoh: Lewis Hamilton" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Telepon / WA</label>
                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 082134567811" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tarif Kerja Per Hari (Rp)</label>
                        <input type="number" name="tarif_supir_per_hari" class="form-control" placeholder="Contoh: 200000" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Awal</label>
                        <select name="status_supir" class="form-select" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="bertugas">Bertugas</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 pt-2">
                        <a href="supir.php" class="btn btn-secondary rounded-pill px-4">Batal</a>
                        <button type="submit" name="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Data</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>