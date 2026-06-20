-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 06:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental mobil`
--
CREATE DATABASE IF NOT EXISTS `rental_mobil_anakteknik` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `rental_mobil_anakteknik`;

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `kode_akun` int(11) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tipe_akun` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `no_invoice` varchar(20) NOT NULL,
  `id_sewa` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `potongan_diskon` decimal(12,2) DEFAULT 0.00,
  `total_akhir` decimal(12,2) NOT NULL,
  `status_pembayaran` enum('belum lunas','lunas','dibatalkan') DEFAULT 'belum lunas',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jurnal`
--

CREATE TABLE `jurnal` (
  `id_jurnal` int(11) NOT NULL,
  `id_pembayaran` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `kode_akun` varchar(20) DEFAULT NULL,
  `Debit` decimal(12,2) DEFAULT 0.00,
  `Kredit` decimal(12,2) DEFAULT 0.00,
  `id_sumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurnal`
--

INSERT INTO `jurnal` (`id_jurnal`, `id_pembayaran`, `tanggal`, `keterangan`, `kode_akun`, `Debit`, `Kredit`, `id_sumber`) VALUES
(1, NULL, '2026-05-07', 'Pembayaran Sewa Mobil ID: 260003', '101', 800000.00, 0.00, 20271),
(2, NULL, '2026-05-07', '    Pembayaran Sewa Mobil ID: 260003', '401', 0.00, 800000.00, 20271),
(3, NULL, '2026-05-07', 'Pembayaran Sewa Mobil ID: 260002', '101', 280000.00, 0.00, 20272),
(4, NULL, '2026-05-07', '    Pembayaran Sewa Mobil ID: 260002', '401', 0.00, 280000.00, 20272),
(5, NULL, '2026-05-19', 'Pembayaran Sewa Mobil ID: 260004', '101', 280000.00, 0.00, 20273),
(6, NULL, '2026-05-19', '    Pembayaran Sewa Mobil ID: 260004', '401', 0.00, 280000.00, 20273),
(7, NULL, '2026-05-19', 'Pembayaran Sewa Mobil ID: 260002', '101', 280000.00, 0.00, 20274),
(8, NULL, '2026-05-19', '    Pembayaran Sewa Mobil ID: 260002', '401', 0.00, 280000.00, 20274),
(9, NULL, '2026-05-19', 'Pembayaran Sewa Mobil ID: 260003', '101', 800000.00, 0.00, 20275),
(10, NULL, '2026-05-19', '    Pembayaran Sewa Mobil ID: 260003', '401', 0.00, 800000.00, 20275),
(11, NULL, '2026-05-19', 'Pembayaran Sewa Mobil ID: 260005', '101', 400000.00, 0.00, 20276),
(12, NULL, '2026-05-19', '    Pembayaran Sewa Mobil ID: 260005', '401', 0.00, 400000.00, 20276);

-- --------------------------------------------------------

--
-- Table structure for table `jurnal_detail`
--

CREATE TABLE `jurnal_detail` (
  `id_jurnal` int(11) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `kode_akun` int(11) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `id_sumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `kode_mobil` varchar(10) NOT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `nopol` varchar(15) DEFAULT NULL,
  `tarif_per_hari` decimal(10,2) DEFAULT NULL,
  `status_mobil` enum('tersedia','disewa') DEFAULT 'tersedia',
  `Gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`kode_mobil`, `merk`, `jenis`, `nopol`, `tarif_per_hari`, `status_mobil`, `Gambar`) VALUES
('M0001', 'Hyundai', 'Palisade', 'H 0101 AB', 900000.00, 'tersedia', 'hyundai_Palisade.jpg'),
('M0002', 'Toyota', 'Avanza', 'H 0102 AC', 275000.00, 'tersedia', 'Toyota_Avanza.jpg'),
('M0003', 'Honda', 'Brio', 'H 0103 AD', 250000.00, 'tersedia', 'Honda_Brio.jpg'),
('M0004', 'Daihatsu', 'Xenia', 'H 0104 AE', 260000.00, 'tersedia', 'daihatsu_xenia.jpg'),
('M0005', 'Suzuki', 'Ertiga', 'H 0105 AF', 280000.00, 'tersedia', 'suzuki_ertiga.jpg'),
('M0006', 'Mitsubishi', 'Pajero Sport', 'H 0106 AG', 750000.00, 'tersedia', 'mitsubishi_pajero_sport.jpg'),
('M0007', 'Toyota', 'Fortuner', 'H 0107 AH', 800000.00, 'tersedia', 'Toyota_Fortuner.jpg'),
('M0008', 'Honda', 'CR-V', 'H 0108 AI', 600000.00, 'tersedia', 'Honda_CRV.jpg'),
('M0009', 'Nissan', 'Livina', 'H 0109 AJ', 270000.00, 'tersedia', 'Nissan_Livina.jpg'),
('M0010', 'Wuling', 'Almaz', 'H 0110 AK', 350000.00, 'tersedia', 'Wuling_Almaz.jpg'),
('M0011', 'Hyundai', 'Creta', 'H 0111 AL', 400000.00, 'tersedia', 'hyundai_creta.jpg'),
('M0012', 'Toyota', 'Innova', 'H 0112 AM', 450000.00, 'tersedia', 'Toyota_Innova.jpg'),
('M0013', 'Honda', 'Mobilio', 'H 0113 AN', 280000.00, 'tersedia', 'Honda_Mobilio.jpg'),
('M0014', 'Suzuki', 'XL7', 'H 0114 AO', 320000.00, 'tersedia', 'Suzuki_XL7.jpg'),
('M0015', 'Daihatsu', 'Terios', 'H 0115 AP', 300000.00, 'tersedia', 'Daihatsu_Terrios.jpg'),
('M0016', 'Mitsubishi', 'Xpander', 'H 0116 AQ', 330000.00, 'tersedia', 'Mitsubishi_Xpander.jpg'),
('M0017', 'Nissan', 'X-Trail', 'H 0117 AR', 650000.00, 'tersedia', 'nissan_x-trail.jpg'),
('M0018', 'Toyota', 'Rush', 'H 0118 AS', 310000.00, 'tersedia', 'Toyota_Rush.jpg'),
('M0019', 'Honda', 'HR-V', 'H 0119 AT', 450000.00, 'tersedia', 'Honda_HR-V.jpg'),
('M0020', 'Mazda', 'CX-5', 'H 0120 AU', 700000.00, 'tersedia', 'Mazda_CX-5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `alamat`, `no_telp`, `no_ktp`) VALUES
(10001, 'Atarada Saputra', 'Jl. Pandanaran No. 45, Semarang Tengah, Kota Semarang', '081234567801', '3374010101010001'),
(10002, 'Sarada Santoso', 'Jl. Sultan Fatah No. 12, Demak Kota, Kabupaten Demak', '081234567802', '3322010202020002'),
(10003, 'Sheila Kharizza', 'Jl. Sunan Kudus No. 88, Kota Kudus, Kabupaten Kudus', '081234567803', '3319010303030003'),
(10004, 'Sheina Putri', 'Jl. Kartini No. 21, Jepara Kota, Kabupaten Jepara', '081234567804', '3321010404040004'),
(10005, 'Ron Pratama', 'Jl. Diponegoro No. 67, Sidorejo, Kota Salatiga', '081234567805', '3373010505050005'),
(10006, 'Naura Wulandari', 'Jl. Raya Kendal No. 10, Brangsong, Kabupaten Kendal', '081234567806', '3324010606060006'),
(10007, 'Gina Aurelia', 'Jl. MT Haryono No. 5, Ungaran Barat, Kabupaten Semarang', '081234567807', '3326010707070007'),
(10008, 'Hendra Wijaya', 'Jl. Gajahmada No. 99, Semarang Tengah, Kota Semarang', '081234567808', '3374010808080008'),
(10009, 'Karenovva', 'Jl. R. Suprapto No. 3, Purwodadi, Kabupaten Grobogan', '081234567809', '3315010909090009'),
(10010, 'Jeannetta', 'Jl. Jenderal Sudirman No. 50, Pati', '081234567810', '3318011010100010');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_sewa` int(11) DEFAULT NULL,
  `jenis_pembayaran` enum('dp','pelunasan') NOT NULL,
  `metode_pembayaran` enum('cash','transfer') NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) DEFAULT NULL,
  `status_konfirmasi` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_sewa`, `jenis_pembayaran`, `metode_pembayaran`, `tanggal_bayar`, `jumlah_bayar`, `status_konfirmasi`, `keterangan`) VALUES
(20271, 260003, 'dp', '', '2026-05-07', 800000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260003'),
(20272, 260002, 'dp', '', '2026-05-07', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260002'),
(20273, 260004, 'dp', '', '2026-05-19', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260004'),
(20274, 260002, 'dp', '', '2026-05-19', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260002'),
(20275, 260003, 'dp', '', '2026-05-19', 800000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260003'),
(20276, 260005, 'dp', '', '2026-05-19', 400000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260005');

-- --------------------------------------------------------

--
-- Table structure for table `supir`
--

CREATE TABLE `supir` (
  `id_supir` int(11) NOT NULL,
  `nama_supir` varchar(100) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `tarif_supir_per_hari` decimal(10,2) DEFAULT NULL,
  `status_supir` enum('tersedia','bertugas') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supir`
--

INSERT INTO `supir` (`id_supir`, `nama_supir`, `no_telp`, `tarif_supir_per_hari`, `status_supir`) VALUES
(10055, 'Carlos Sainz', '082134567801', 200000.00, 'tersedia'),
(10081, 'Oscar Piastri', '082134567810', 200000.00, 'tersedia'),
(20016, 'Charles Leclerc', '082134567802', 200000.00, 'tersedia'),
(30031, 'Esteban Ocon', '082134567803', 200000.00, 'tersedia'),
(40014, 'Fernando Alonso', '082134567804', 200000.00, 'tersedia'),
(50063, 'George Russell', '082134567805', 200000.00, 'tersedia'),
(60012, 'Kimi Antonelli', '082134567806', 200000.00, 'tersedia'),
(70004, 'Lando Norris', '082134567807', 200000.00, 'tersedia'),
(80044, 'Lewis Hamilton', '082134567808', 200000.00, 'tersedia'),
(90001, 'Max Verstappen', '082134567809', 200000.00, 'bertugas');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_sewa`
--

CREATE TABLE `transaksi_sewa` (
  `id_sewa` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `kode_mobil` varchar(10) NOT NULL,
  `id_supir` int(11) DEFAULT NULL,
  `opsi_supir` enum('ya','tidak') DEFAULT 'tidak',
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `lama_sewa` int(11) NOT NULL,
  `total_biaya` decimal(12,2) NOT NULL,
  `status_sewa` enum('berjalan','selesai') DEFAULT 'berjalan',
  `kode_akun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_sewa`
--

INSERT INTO `transaksi_sewa` (`id_sewa`, `id_pelanggan`, `kode_mobil`, `id_supir`, `opsi_supir`, `tanggal_sewa`, `tanggal_kembali`, `lama_sewa`, `total_biaya`, `status_sewa`, `kode_akun`) VALUES
(260002, 10002, 'M0005', NULL, 'tidak', '2026-05-05', '0000-00-00', 3, 0.00, 'selesai', NULL),
(260003, 10005, 'M0007', NULL, 'tidak', '2026-05-07', '0000-00-00', 1, 0.00, 'selesai', NULL),
(260004, 10006, 'M0009', NULL, 'tidak', '2026-05-19', '0000-00-00', 2, 0.00, 'selesai', NULL),
(260005, 10009, 'M0011', NULL, 'tidak', '2026-05-19', '0000-00-00', 1, 0.00, 'selesai', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`kode_akun`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`no_invoice`),
  ADD KEY `fk_invoice_sewa` (`id_sewa`),
  ADD KEY `fk_invoice_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `jurnal`
--
ALTER TABLE `jurnal`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indexes for table `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `kode_akun` (`kode_akun`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`kode_mobil`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_sewa` (`id_sewa`);

--
-- Indexes for table `supir`
--
ALTER TABLE `supir`
  ADD PRIMARY KEY (`id_supir`);

--
-- Indexes for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD PRIMARY KEY (`id_sewa`),
  ADD KEY `fk_sewa_pelanggan` (`id_pelanggan`),
  ADD KEY `fk_sewa_mobil` (`kode_mobil`),
  ADD KEY `fk_sewa_supir` (`id_supir`),
  ADD KEY `fk_sewa_akun` (`kode_akun`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jurnal`
--
ALTER TABLE `jurnal`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10011;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20277;

--
-- AUTO_INCREMENT for table `supir`
--
ALTER TABLE `supir`
  MODIFY `id_supir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90002;

--
-- AUTO_INCREMENT for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  MODIFY `id_sewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260006;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk_invoice_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invoice_sewa` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`) ON DELETE CASCADE;

--
-- Constraints for table `jurnal`
--
ALTER TABLE `jurnal`
  ADD CONSTRAINT `jurnal_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`);

--
-- Constraints for table `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD CONSTRAINT `fk_jurnal_akun` FOREIGN KEY (`kode_akun`) REFERENCES `akun` (`kode_akun`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`);

--
-- Constraints for table `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD CONSTRAINT `fk_sewa_akun` FOREIGN KEY (`kode_akun`) REFERENCES `akun` (`kode_akun`),
  ADD CONSTRAINT `fk_sewa_mobil` FOREIGN KEY (`kode_mobil`) REFERENCES `mobil` (`kode_mobil`),
  ADD CONSTRAINT `fk_sewa_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `fk_sewa_supir` FOREIGN KEY (`id_supir`) REFERENCES `supir` (`id_supir`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
