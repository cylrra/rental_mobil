-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Jun 2026 pada 06.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rental_mobil`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun`
--

CREATE TABLE `akun` (
  `kode_akun` int(11) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tipe_akun` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `coa2`
--

CREATE TABLE `coa2` (
  `nomor akun` varchar(20) NOT NULL,
  `name akun` varchar(100) NOT NULL,
  `class` varchar(10) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `induk` varchar(20) DEFAULT NULL,
  `levelAkun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `coa2`
--

INSERT INTO `coa2` (`nomor akun`, `name akun`, `class`, `type`, `induk`, `levelAkun`) VALUES
('1-0000', 'Aktiva', 'AS', 'Header', NULL, 1),
('1-1000', 'Aktiva Lancar', 'AS', 'Header', '1-0000', 2),
('1-2000', 'Aktiva Tetap', 'AS', 'Header', '1-0000', 2),
('111', 'Kas', 'AS', 'Detail', '1-1000', 3),
('112', 'Bank', 'AS', 'Detail', '1-1000', 3),
('113', 'Piutang Usaha', 'AS', 'Detail', '1-1000', 3),
('114', 'Persediaan Suku Cadang', 'AS', 'Detail', '1-1000', 3),
('121', 'Kendaraan Rental', 'AS', 'Detail', '1-2000', 3),
('122', 'Akumulasi Penyusutan Kendaraan', 'AS', 'Detail', '1-2000', 3),
('123', 'Peralatan Kantor', 'AS', 'Detail', '1-2000', 3),
('2-0000', 'Pasiva', 'LI', 'Header', NULL, 1),
('2-1000', 'Utang Jangka Pendek', 'LI', 'Header', '2-0000', 2),
('211', 'Utang Usaha', 'LI', 'Detail', '2-1000', 3),
('212', 'Utang Gaji', 'LI', 'Detail', '2-1000', 3),
('213', 'Utang Pajak', 'LI', 'Detail', '2-1000', 3),
('3-0000', 'Modal', 'EQ', 'Header', NULL, 1),
('311', 'Modal Pemilik', 'EQ', 'Detail', '3-0000', 2),
('312', 'Prive', 'EQ', 'Detail', '3-0000', 2),
('4-0000', 'Pendapatan', 'IN', 'Header', NULL, 1),
('411', 'Pendapatan Rental Mobil', 'IN', 'Detail', '4-0000', 2),
('412', 'Pendapatan Sopir', 'IN', 'Detail', '4-0000', 2),
('413', 'Pendapatan Denda Keterlambatan', 'IN', 'Detail', '4-0000', 2),
('5-0000', 'Beban', 'EX', 'Header', NULL, 1),
('511', 'Beban Gaji', 'EX', 'Detail', '5-0000', 2),
('512', 'Beban BBM', 'EX', 'Detail', '5-0000', 2),
('513', 'Beban Perawatan Kendaraan', 'EX', 'Detail', '5-0000', 2),
('514', 'Beban Penyusutan Kendaraan', 'EX', 'Detail', '5-0000', 2),
('515', 'Beban Administrasi', 'EX', 'Detail', '5-0000', 2),
('516', 'Beban Listrik dan Air', 'EX', 'Detail', '5-0000', 2),
('517', 'Beban Pajak Kendaraan', 'EX', 'Detail', '5-0000', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `invoice`
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
-- Struktur dari tabel `jurnal`
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
-- Dumping data untuk tabel `jurnal`
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
(12, NULL, '2026-05-19', '    Pembayaran Sewa Mobil ID: 260005', '401', 0.00, 400000.00, 20276),
(13, NULL, '2026-06-02', 'Pembayaran Sewa Mobil ID: 260006', '101', 275000.00, 0.00, 20277),
(14, NULL, '2026-06-02', '    Pembayaran Sewa Mobil ID: 260006', '401', 0.00, 275000.00, 20277);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_detail`
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
-- Struktur dari tabel `laporan_laba_rugi`
--

CREATE TABLE `laporan_laba_rugi` (
  `id` int(11) NOT NULL,
  `periode` date NOT NULL,
  `pendapatan_total` decimal(15,2) DEFAULT 0.00,
  `beban_total` decimal(15,2) DEFAULT 0.00,
  `laba_bersih` decimal(15,2) GENERATED ALWAYS AS (`pendapatan_total` - `beban_total`) STORED,
  `waktu_rekap` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `laporan_laba_rugi`
--

INSERT INTO `laporan_laba_rugi` (`id`, `periode`, `pendapatan_total`, `beban_total`, `waktu_rekap`) VALUES
(1, '2026-01-31', 500000000.00, 280000000.00, '2026-06-04 10:44:41'),
(2, '2026-02-28', 650000000.00, 310000000.00, '2026-06-04 10:44:41'),
(3, '2026-03-31', 720000000.00, 350000000.00, '2026-06-04 10:44:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mobil`
--

CREATE TABLE `mobil` (
  `kode_mobil` varchar(10) NOT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `nopol` varchar(15) DEFAULT NULL,
  `tarif_per_hari` decimal(10,2) DEFAULT NULL,
  `status_mobil` enum('tersedia','disewa') DEFAULT 'tersedia',
  `Gambar` varchar(255) NOT NULL,
  `Unit_Tersedia` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mobil`
--

INSERT INTO `mobil` (`kode_mobil`, `merk`, `jenis`, `nopol`, `tarif_per_hari`, `status_mobil`, `Gambar`, `Unit_Tersedia`) VALUES
('M0001', 'Hyundai', 'Palisade', 'H 0101 AB', 800000.00, 'tersedia', 'hyundai_Palisade.jpg', 1),
('M0002', 'Toyota', 'Avanza', 'H 0102 AC', 275000.00, 'tersedia', 'Toyota_Avanza.jpg', 3),
('M0003', 'Honda', 'Brio', 'H 0103 AD', 250000.00, 'tersedia', 'Honda_Brio.jpg', 3),
('M0004', 'Daihatsu', 'Xenia', 'H 0104 AE', 260000.00, 'tersedia', 'daihatsu_xenia.jpg', 2),
('M0005', 'Suzuki', 'Ertiga', 'H 0105 AF', 280000.00, 'tersedia', 'suzuki_ertiga.jpg', 2),
('M0006', 'Mitsubishi', 'Pajero Sport', 'H 0106 AG', 750000.00, 'tersedia', 'mitsubishi_pajero_sport.jpg', 1),
('M0007', 'Toyota', 'Fortuner', 'H 0107 AH', 800000.00, 'tersedia', 'Toyota_Fortuner.jpg', 1),
('M0008', 'Honda', 'CR-V', 'H 0108 AI', 600000.00, 'tersedia', 'Honda_CRV.jpg', 2),
('M0009', 'Nissan', 'Livina', 'H 0109 AJ', 270000.00, 'tersedia', 'Nissan_Livina.jpg', 3),
('M0010', 'Wuling', 'Almaz', 'H 0110 AK', 350000.00, 'tersedia', 'Wuling_Almaz.jpg', 2),
('M0011', 'Hyundai', 'Creta', 'H 0111 AL', 400000.00, 'tersedia', 'hyundai_creta.jpg', 2),
('M0012', 'Toyota', 'Innova', 'H 0112 AM', 450000.00, 'tersedia', 'Toyota_Innova.jpg', 2),
('M0013', 'Honda', 'Mobilio', 'H 0113 AN', 280000.00, 'tersedia', 'Honda_Mobilio.jpg', 3),
('M0014', 'Suzuki', 'XL7', 'H 0114 AO', 320000.00, 'tersedia', 'Suzuki_XL7.jpg', 2),
('M0015', 'Daihatsu', 'Terios', 'H 0115 AP', 300000.00, 'tersedia', 'Daihatsu_Terrios.jpg', 3),
('M0016', 'Mitsubishi', 'Xpander', 'H 0116 AQ', 330000.00, 'tersedia', 'Mitsubishi_Xpander.jpg', 3),
('M0017', 'Nissan', 'X-Trail', 'H 0117 AR', 650000.00, 'tersedia', 'nissan_x-trail.jpg', 1),
('M0018', 'Toyota', 'Rush', 'H 0118 AS', 310000.00, 'tersedia', 'Toyota_Rush.jpg', 2),
('M0019', 'Honda', 'HR-V', 'H 0119 AT', 450000.00, 'tersedia', 'Honda_HR-V.jpg', 2),
('M0020', 'Mazda', 'CX-5', 'H 0120 AU', 700000.00, 'tersedia', 'Mazda_CX-5.jpg', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nama_akun`
--

CREATE TABLE `nama_akun` (
  `kode_akun` varchar(20) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `saldo_awal` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `nama_akun`
--

INSERT INTO `nama_akun` (`kode_akun`, `nama_akun`, `saldo_awal`) VALUES
('111', 'Kas', 1200000000.00),
('112', 'Bank', 50000000.00),
('113', 'Piutang Usaha', 0.00),
('114', 'Persediaan Suku Cadang', 100000000.00),
('121', 'Kendaraan Rental', 6600000000.00),
('122', 'Akumulasi Penyusutan Kendaraan', 0.00),
('123', 'Peralatan Kantor', 50000000.00),
('211', 'Utang Usaha', 0.00),
('212', 'Utang Gaji', 0.00),
('213', 'Utang Pajak', 0.00),
('311', 'Modal Pemilik', 8000000000.00),
('312', 'Prive', 0.00),
('411', 'Pendapatan Rental Mobil', 0.00),
('412', 'Pendapatan Sopir', 0.00),
('413', 'Pendapatan Denda Keterlambatan', 0.00),
('511', 'Beban Gaji', 0.00),
('512', 'Beban BBM', 0.00),
('513', 'Beban Perawatan Kendaraan', 0.00),
('514', 'Beban Penyusutan Kendaraan', 0.00),
('515', 'Beban Administrasi', 0.00),
('516', 'Beban Listrik dan Air', 0.00),
('517', 'Beban Pajak Kendaraan', 0.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `no_ktp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `username`, `password`, `alamat`, `no_telp`, `no_ktp`) VALUES
(10001, 'Atarada Saputra', 'Putra', '$2y$10$X4eFhM5w/sqvkZWbOMs2fu7.SJqqaRBg5TW9Fjlc3QrmdQcUzkIzO', 'Jl. Pandanaran No. 45, Semarang Tengah, Kota Semarang', '081234567801', '3374010101010001'),
(10002, 'Sarada Santoso', '', '', 'Jl. Sultan Fatah No. 12, Demak Kota, Kabupaten Demak', '081234567802', '3322010202020002'),
(10003, 'Sheila Kharizza', '', '', 'Jl. Sunan Kudus No. 88, Kota Kudus, Kabupaten Kudus', '081234567803', '3319010303030003'),
(10004, 'Sheina Putri', '', '', 'Jl. Kartini No. 21, Jepara Kota, Kabupaten Jepara', '081234567804', '3321010404040004'),
(10005, 'Ron Pratama', '', '', 'Jl. Diponegoro No. 67, Sidorejo, Kota Salatiga', '081234567805', '3373010505050005'),
(10006, 'Naura Wulandari', '', '', 'Jl. Raya Kendal No. 10, Brangsong, Kabupaten Kendal', '081234567806', '3324010606060006'),
(10007, 'Gina Aurelia', '', '', 'Jl. MT Haryono No. 5, Ungaran Barat, Kabupaten Semarang', '081234567807', '3326010707070007'),
(10008, 'Hendra Wijaya', '', '', 'Jl. Gajahmada No. 99, Semarang Tengah, Kota Semarang', '081234567808', '3374010808080008'),
(10009, 'Karenovva', '', '', 'Jl. R. Suprapto No. 3, Purwodadi, Kabupaten Grobogan', '081234567809', '3315010909090009'),
(10010, 'Jeannetta', '', '', 'Jl. Jenderal Sudirman No. 50, Pati', '081234567810', '3318011010100010');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_sewa` int(11) DEFAULT NULL,
  `jenis_pembayaran` enum('dp','pelunasan') NOT NULL,
  `metode_pembayaran` enum('cash','transfer') NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `jumlah_bayar` decimal(12,2) DEFAULT NULL,
  `status_konfirmasi` enum('menunggu','diterima','ditolak') DEFAULT 'menunggu',
  `keterangan` text DEFAULT NULL,
  `tipe_pembayaran` enum('DP','Lunas') NOT NULL DEFAULT 'Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_sewa`, `jenis_pembayaran`, `metode_pembayaran`, `tanggal_bayar`, `jumlah_bayar`, `status_konfirmasi`, `keterangan`, `tipe_pembayaran`) VALUES
(20271, 260003, 'dp', '', '2026-05-07', 800000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260003', 'Lunas'),
(20272, 260002, 'dp', '', '2026-05-07', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260002', 'Lunas'),
(20273, 260004, 'dp', '', '2026-05-19', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260004', 'Lunas'),
(20274, 260002, 'dp', '', '2026-05-19', 280000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260002', 'Lunas'),
(20275, 260003, 'dp', '', '2026-05-19', 800000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260003', 'Lunas'),
(20276, 260005, 'dp', '', '2026-05-19', 400000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260005', 'Lunas'),
(20277, 260006, 'dp', 'transfer', '2026-06-02', 275000.00, 'menunggu', 'Pembayaran Sewa Mobil ID: 260006', 'Lunas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeliharaan`
--

CREATE TABLE `pemeliharaan` (
  `id_pemeliharaan` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `tanggal_pemeliharaan` date NOT NULL,
  `jenis_pemeliharaan` enum('Servis Rutin','Perbaikan Kerusakan','Ganti Ban','Ganti Oli','Lainnya') NOT NULL,
  `biaya_pemeliharaan` decimal(12,2) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rating_sewa`
--

CREATE TABLE `rating_sewa` (
  `id_rating` int(11) NOT NULL,
  `id_transaksi` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `rating_pelayanan` tinyint(4) NOT NULL,
  `rating_supir` tinyint(4) NOT NULL,
  `rating_mobil` tinyint(4) NOT NULL,
  `ulasan` text DEFAULT NULL,
  `tgl_rating` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `supir`
--

CREATE TABLE `supir` (
  `id_supir` int(11) NOT NULL,
  `nama_supir` varchar(100) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `tarif_supir_per_hari` decimal(10,2) DEFAULT NULL,
  `status_supir` enum('tersedia','bertugas') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supir`
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
(90001, 'Max Verstappen', '082134567809', 200000.00, 'tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tracking_mobil`
--

CREATE TABLE `tracking_mobil` (
  `id_tracking` int(11) NOT NULL,
  `id_sewa` int(11) NOT NULL,
  `lokasi_terkini` text NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `waktu_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tracking_mobil`
--

INSERT INTO `tracking_mobil` (`id_tracking`, `id_sewa`, `lokasi_terkini`, `latitude`, `longitude`, `waktu_update`) VALUES
(1, 260002, 'Jl. Tol Trans Jawa, KM 420 (Arah Jakarta)', '-7.0264', '110.4503', '2026-06-20 04:19:19'),
(2, 260003, 'Jl. Pemuda No.118, Sekayu, Kota Semarang', '-6.9825', '110.4138', '2026-06-20 04:19:19'),
(3, 260004, 'Jl. Pandanaran, Randusari, Kota Semarang', '-6.9897', '110.4121', '2026-06-20 04:19:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_sewa`
--

CREATE TABLE `transaksi_sewa` (
  `id_sewa` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `pake_supir` enum('Ya','Tidak') NOT NULL DEFAULT 'Tidak',
  `kode_mobil` varchar(10) NOT NULL,
  `id_supir` int(11) DEFAULT NULL,
  `biaya_supir` decimal(12,2) NOT NULL DEFAULT 0.00,
  `opsi_supir` enum('ya','tidak') DEFAULT 'tidak',
  `tanggal_sewa` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `lama_sewa` int(11) NOT NULL,
  `total_biaya` decimal(12,2) NOT NULL,
  `status_sewa` enum('berjalan','selesai') DEFAULT 'berjalan',
  `kode_akun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_sewa`
--

INSERT INTO `transaksi_sewa` (`id_sewa`, `id_pelanggan`, `pake_supir`, `kode_mobil`, `id_supir`, `biaya_supir`, `opsi_supir`, `tanggal_sewa`, `tanggal_kembali`, `lama_sewa`, `total_biaya`, `status_sewa`, `kode_akun`) VALUES
(260002, 10002, 'Tidak', 'M0005', NULL, 0.00, 'tidak', '2026-05-05', '0000-00-00', 3, 0.00, 'selesai', NULL),
(260003, 10005, 'Tidak', 'M0007', NULL, 0.00, 'tidak', '2026-05-07', '0000-00-00', 1, 0.00, 'selesai', NULL),
(260004, 10006, 'Tidak', 'M0009', NULL, 0.00, 'tidak', '2026-05-19', '0000-00-00', 2, 0.00, 'selesai', NULL),
(260005, 10009, 'Tidak', 'M0011', NULL, 0.00, 'tidak', '2026-05-19', '0000-00-00', 1, 0.00, 'selesai', NULL),
(260006, 10001, 'Tidak', 'M0002', NULL, 0.00, 'tidak', '2026-06-02', '0000-00-00', 1, 0.00, 'selesai', NULL),
(260007, 10008, 'Tidak', 'M0001', NULL, 0.00, 'tidak', '2026-06-02', '0000-00-00', 1, 0.00, 'berjalan', NULL),
(260008, 10001, 'Tidak', 'M0005', NULL, 0.00, 'tidak', '2026-06-02', '0000-00-00', 2, 0.00, 'berjalan', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasan`
--

CREATE TABLE `ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_sewa` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `nilai_rating` int(1) NOT NULL,
  `komentar` text DEFAULT NULL,
  `tanggal_ulasan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ulasan`
--

INSERT INTO `ulasan` (`id_ulasan`, `id_sewa`, `nama_pelanggan`, `nilai_rating`, `komentar`, `tanggal_ulasan`) VALUES
(1, 260002, 'Sarada Santoso', 5, 'Mobil sangat bersih dan mesin halus!', '2026-06-20 04:16:28'),
(2, 260003, 'Ron Pratama', 4, 'Bagus, tapi proses pengembalian sedikit lama.', '2026-06-20 04:16:28'),
(3, 260004, 'Naura Wulandari', 5, 'Pelayanan memuaskan, AC mobil sangat dingin.', '2026-06-20 04:16:28'),
(4, 260005, 'Karenovva', 3, 'Standar, suspensi mobil terasa agak keras.', '2026-06-20 04:16:28');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`kode_akun`);

--
-- Indeks untuk tabel `coa2`
--
ALTER TABLE `coa2`
  ADD PRIMARY KEY (`nomor akun`);

--
-- Indeks untuk tabel `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`no_invoice`),
  ADD KEY `fk_invoice_sewa` (`id_sewa`),
  ADD KEY `fk_invoice_pelanggan` (`id_pelanggan`);

--
-- Indeks untuk tabel `jurnal`
--
ALTER TABLE `jurnal`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `id_pembayaran` (`id_pembayaran`);

--
-- Indeks untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD PRIMARY KEY (`id_jurnal`),
  ADD KEY `kode_akun` (`kode_akun`);

--
-- Indeks untuk tabel `laporan_laba_rugi`
--
ALTER TABLE `laporan_laba_rugi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`kode_mobil`);

--
-- Indeks untuk tabel `nama_akun`
--
ALTER TABLE `nama_akun`
  ADD PRIMARY KEY (`kode_akun`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_sewa` (`id_sewa`);

--
-- Indeks untuk tabel `pemeliharaan`
--
ALTER TABLE `pemeliharaan`
  ADD PRIMARY KEY (`id_pemeliharaan`);

--
-- Indeks untuk tabel `rating_sewa`
--
ALTER TABLE `rating_sewa`
  ADD PRIMARY KEY (`id_rating`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indeks untuk tabel `supir`
--
ALTER TABLE `supir`
  ADD PRIMARY KEY (`id_supir`);

--
-- Indeks untuk tabel `tracking_mobil`
--
ALTER TABLE `tracking_mobil`
  ADD PRIMARY KEY (`id_tracking`),
  ADD KEY `id_sewa` (`id_sewa`);

--
-- Indeks untuk tabel `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD PRIMARY KEY (`id_sewa`),
  ADD KEY `fk_sewa_pelanggan` (`id_pelanggan`),
  ADD KEY `fk_sewa_mobil` (`kode_mobil`),
  ADD KEY `fk_sewa_akun` (`kode_akun`),
  ADD KEY `fk_transaksi_supir` (`id_supir`);

--
-- Indeks untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`id_ulasan`),
  ADD KEY `id_sewa` (`id_sewa`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jurnal`
--
ALTER TABLE `jurnal`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `laporan_laba_rugi`
--
ALTER TABLE `laporan_laba_rugi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10012;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20278;

--
-- AUTO_INCREMENT untuk tabel `pemeliharaan`
--
ALTER TABLE `pemeliharaan`
  MODIFY `id_pemeliharaan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rating_sewa`
--
ALTER TABLE `rating_sewa`
  MODIFY `id_rating` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `supir`
--
ALTER TABLE `supir`
  MODIFY `id_supir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90002;

--
-- AUTO_INCREMENT untuk tabel `tracking_mobil`
--
ALTER TABLE `tracking_mobil`
  MODIFY `id_tracking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  MODIFY `id_sewa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260009;

--
-- AUTO_INCREMENT untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk_invoice_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_invoice_sewa` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jurnal`
--
ALTER TABLE `jurnal`
  ADD CONSTRAINT `jurnal_ibfk_1` FOREIGN KEY (`id_pembayaran`) REFERENCES `pembayaran` (`id_pembayaran`);

--
-- Ketidakleluasaan untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD CONSTRAINT `fk_jurnal_akun` FOREIGN KEY (`kode_akun`) REFERENCES `akun` (`kode_akun`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`);

--
-- Ketidakleluasaan untuk tabel `rating_sewa`
--
ALTER TABLE `rating_sewa`
  ADD CONSTRAINT `rating_sewa_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tracking_mobil`
--
ALTER TABLE `tracking_mobil`
  ADD CONSTRAINT `tracking_mobil_ibfk_1` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi_sewa`
--
ALTER TABLE `transaksi_sewa`
  ADD CONSTRAINT `fk_sewa_akun` FOREIGN KEY (`kode_akun`) REFERENCES `akun` (`kode_akun`),
  ADD CONSTRAINT `fk_sewa_mobil` FOREIGN KEY (`kode_mobil`) REFERENCES `mobil` (`kode_mobil`),
  ADD CONSTRAINT `fk_sewa_pelanggan` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`),
  ADD CONSTRAINT `fk_sewa_supir` FOREIGN KEY (`id_supir`) REFERENCES `supir` (`id_supir`),
  ADD CONSTRAINT `fk_transaksi_supir` FOREIGN KEY (`id_supir`) REFERENCES `supir` (`id_supir`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`id_sewa`) REFERENCES `transaksi_sewa` (`id_sewa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
