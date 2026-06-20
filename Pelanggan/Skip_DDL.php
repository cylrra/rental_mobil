-- 1. Tabel Master Akun (Untuk Akuntansi)
CREATE TABLE akun (
    no_akun VARCHAR(10) PRIMARY KEY,
    nama_akun VARCHAR(50) NOT NULL,
    header_akun INT(1) -- Misal: 1 untuk Aktiva, 4 untuk Pendapatan
);

-- 2. Tabel Master Mobil
CREATE TABLE mobil (
    id_mobil INT AUTO_INCREMENT PRIMARY KEY,
    nopol VARCHAR(15) UNIQUE NOT NULL,
    merk VARCHAR(30),
    tipe VARCHAR(30),
    harga_sewa_perhari DECIMAL(12,2),
    status ENUM('Tersedia', 'Disewa', 'Servis') DEFAULT 'Tersedia'
);

-- 3. Tabel Master Pelanggan
CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100),
    alamat TEXT,
    no_telp VARCHAR(15),
    nik VARCHAR(20) UNIQUE
);

-- 4. Tabel Master Supir
CREATE TABLE supir (
    id_supir INT AUTO_INCREMENT PRIMARY KEY,
    nama_supir VARCHAR(100),
    no_telp VARCHAR(15),
    tarif_perhari DECIMAL(12,2),
    status ENUM('Tersedia', 'Tugas') DEFAULT 'Tersedia'
);

-- 5. Tabel Transaksi Sewa (Header)
CREATE TABLE transaksi_sewa (
    id_sewa INT AUTO_INCREMENT PRIMARY KEY,
    tgl_sewa DATE,
    tgl_kembali_rencana DATE,
    id_pelanggan INT,
    id_mobil INT,
    id_supir INT NULL, -- Bisa NULL jika lepas kunci
    total_biaya DECIMAL(15,2),
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    FOREIGN KEY (id_mobil) REFERENCES mobil(id_mobil),
    FOREIGN KEY (id_supir) REFERENCES supir(id_supir)
);

-- 6. Tabel Invoice
CREATE TABLE invoice (
    no_invoice VARCHAR(20) PRIMARY KEY,
    id_sewa INT,
    tgl_invoice DATE,
    status_bayar ENUM('Lunas', 'Belum Lunas'),
    FOREIGN KEY (id_sewa) REFERENCES transaksi_sewa(id_sewa)
);

-- 7. Tabel Pembayaran
CREATE TABLE pembayaran (
    id_bayar INT AUTO_INCREMENT PRIMARY KEY,
    no_invoice VARCHAR(20),
    tgl_bayar DATE,
    jumlah_bayar DECIMAL(15,2),
    metode_bayar VARCHAR(20), -- Tunai, Transfer
    FOREIGN KEY (no_invoice) REFERENCES invoice(no_invoice)
);

-- 8. Tabel Jurnal Umum
CREATE TABLE jurnal (
    id_jurnal INT AUTO_INCREMENT PRIMARY KEY,
    tgl_jurnal DATE,
    keterangan TEXT,
    no_referensi VARCHAR(20) -- Biasanya no_invoice atau id_bayar
);

-- 9. Detail Jurnal (Debit/Kredit)
CREATE TABLE jurnal_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_jurnal INT,
    no_akun VARCHAR(10),
    debit DECIMAL(15,2) DEFAULT 0,
    kredit DECIMAL(15,2) DEFAULT 0,
    FOREIGN KEY (id_jurnal) REFERENCES jurnal(id_jurnal),
    FOREIGN KEY (no_akun) REFERENCES akun(no_akun)
);