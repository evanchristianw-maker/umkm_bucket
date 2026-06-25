-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 12:13 PM
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
-- Database: `db_buket`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(15) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `username`, `password`, `created_at`) VALUES
(1, 'Administrator', 'admin', 'admin123', '2026-06-25 16:39:03');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(15) NOT NULL,
  `id_pesanan` varchar(20) NOT NULL,
  `id_produk` int(10) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `warna_kertas` varchar(50) DEFAULT NULL,
  `jenis_isi` varchar(50) DEFAULT NULL,
  `isi_custom` text DEFAULT NULL,
  `ucapan` text DEFAULT NULL,
  `tambahan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `katalog`
--

CREATE TABLE `katalog` (
  `id_produk` int(10) NOT NULL,
  `id_admin` int(15) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `foto_produk` varchar(30) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `katalog`
--

INSERT INTO `katalog` (`id_produk`, `id_admin`, `nama_produk`, `kategori`, `deskripsi`, `harga`, `foto_produk`, `status`, `created_at`, `update_at`) VALUES
(3, 1, 'asdsad', 'asdsads', 'safsaqfa', 1242141, 'koceng.jpg', 'Aktif', '2026-06-25 17:11:28', '2026-06-25 17:11:28');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id_keuangan` int(15) NOT NULL,
  `id_admin` int(15) DEFAULT NULL,
  `id_pesanan` varchar(20) DEFAULT NULL,
  `tipe` varchar(20) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id_notifikasi` int(15) NOT NULL,
  `id_pesanan` varchar(20) DEFAULT NULL,
  `pesan` text DEFAULT NULL,
  `status_baca` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` varchar(20) NOT NULL,
  `id_admin` int(15) DEFAULT NULL,
  `id_pesanan` varchar(20) NOT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `file_bukti` varchar(200) DEFAULT NULL,
  `catatan_cod` text DEFAULT NULL,
  `jumlah_cod` int(11) DEFAULT NULL,
  `alasan_tolak` text DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `status_verifikasi` varchar(20) DEFAULT NULL,
  `tipe_bayar` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` varchar(20) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `no_telepon` varchar(15) NOT NULL,
  `tipe_pengambilan` text DEFAULT NULL,
  `tanggal_pesan` datetime DEFAULT NULL,
  `tanggal_ambil` datetime DEFAULT NULL,
  `total_harga` int(11) DEFAULT NULL,
  `status_pesanan` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_bahan`
--

CREATE TABLE `stok_bahan` (
  `id_bahan` int(15) NOT NULL,
  `id_admin` int(15) NOT NULL,
  `nama_bahan` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `satuan` varchar(30) DEFAULT NULL,
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `kode_bahan` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `katalog`
--
ALTER TABLE `katalog`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id_keuangan`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id_notifikasi`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD PRIMARY KEY (`id_bahan`),
  ADD KEY `id_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `katalog`
--
ALTER TABLE `katalog`
  MODIFY `id_produk` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id_keuangan` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id_notifikasi` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  MODIFY `id_bahan` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `katalog` (`id_produk`);

--
-- Constraints for table `katalog`
--
ALTER TABLE `katalog`
  ADD CONSTRAINT `katalog_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);

--
-- Constraints for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD CONSTRAINT `keuangan_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `keuangan_ibfk_2` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

--
-- Constraints for table `stok_bahan`
--
ALTER TABLE `stok_bahan`
  ADD CONSTRAINT `stok_bahan_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
