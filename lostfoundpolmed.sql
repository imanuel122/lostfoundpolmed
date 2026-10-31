-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260810.843def3cd8
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2026 at 11:28 AM
-- Server version: 8.0.30
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lostfoundpolmed`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1793351402),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1793351402;', 1793351402);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nama`, `ikon`, `created_at`, `updated_at`) VALUES
(1, 'Dompet', 'wallet', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(2, 'HP / Gadget', 'smartphone', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(3, 'Kunci', 'key-round', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(4, 'KTM / Kartu', 'credit-card', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(5, 'Tas', 'backpack', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(6, 'Laptop / Elektronik', 'laptop', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(7, 'Botol / Tumbler', 'cup-soda', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(8, 'Alat Tulis / Buku', 'book-open', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(9, 'Pakaian / Jaket', 'shirt', '2026-10-26 00:19:18', '2026-10-26 00:44:50'),
(10, 'Lainnya', 'package', '2026-10-26 00:19:18', '2026-10-26 00:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `claimant_id` bigint UNSIGNED NOT NULL,
  `jenis_klaim` enum('klaim_pemilik','lapor_temuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi_barang_sekarang` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `catatan_admin` text COLLATE utf8mb4_unicode_ci,
  `reviewed_by` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `handed_over_at` timestamp NULL DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `dispute_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `item_id`, `claimant_id`, `jenis_klaim`, `bukti_deskripsi`, `lokasi_barang_sekarang`, `status`, `catatan_admin`, `reviewed_by`, `reviewed_at`, `handed_over_at`, `confirmed_at`, `dispute_note`, `created_at`, `updated_at`) VALUES
(1, 9, 2, 'klaim_pemilik', 'dompet warna hitam didalamnya ada ktp dan ktm atas nama saya yaitu Imanuel Reformata Hulu, dan ada 3 kartu atm.', NULL, 'ditolak', 'ciri cirinya masih berbeda coba berikan lebih detail lagi agar lebih meyakinkan', 3, '2026-10-27 23:19:59', NULL, NULL, NULL, '2026-10-27 03:58:59', '2026-10-27 23:19:59'),
(2, 9, 2, 'klaim_pemilik', 'dompet warna hitam didalamnya ada ktp dan ktm atas nama saya yaitu Imanuel Reformata Hulu, dan ada 3 kartu atm.', NULL, 'selesai', NULL, 3, '2026-10-27 23:24:15', '2026-10-28 02:32:07', '2026-10-28 02:33:24', NULL, '2026-10-27 23:21:52', '2026-10-28 02:33:24'),
(3, 7, 3, 'klaim_pemilik', 'Jaket nya jenis boomber berwarna hijau tua tebal', NULL, 'selesai', NULL, 2, '2026-10-28 03:59:56', '2026-10-23 04:02:36', '2026-10-28 04:05:46', NULL, '2026-10-28 02:14:30', '2026-10-28 04:05:46'),
(4, 6, 3, 'lapor_temuan', 'Saya menemukan jam tagan ini di kantin di dekat meja ujung', 'Saya bawa sendiri', 'ditolak', 'beda jam coba fotokan lebih jelas', 2, '2026-10-28 03:49:44', NULL, NULL, NULL, '2026-10-28 03:47:20', '2026-10-28 03:49:44'),
(5, 6, 3, 'lapor_temuan', 'Saya menemukan jam ini di kantin tadi saat makan siang', 'Saya bawa sendiri', 'selesai', NULL, 2, '2026-10-28 03:52:10', '2026-10-28 03:56:45', '2026-10-28 03:57:32', NULL, '2026-10-28 03:51:34', '2026-10-28 03:57:32'),
(6, 2, 2, 'lapor_temuan', 'saya menemukan barang ini di meja perpus', 'sama saya', 'disetujui', NULL, 1, '2026-10-28 04:10:00', NULL, NULL, NULL, '2026-10-28 04:07:20', '2026-10-28 04:10:00'),
(7, 10, 4, 'lapor_temuan', 'Saya melihatnya tadi di parkiran tergantung kunci kereta jadi saya simpan aja dulu, kunci kereta honda', 'saya bawa sendiri', 'ditolak', 'beda kunci', 2, '2026-10-29 00:56:06', NULL, NULL, NULL, '2026-10-29 00:53:26', '2026-10-29 00:56:06'),
(8, 10, 4, 'lapor_temuan', 'saya melihat kuncinya tadi kunci honda', 'sama saya', 'selesai', NULL, 2, '2026-10-29 00:57:55', '2026-10-29 01:01:38', '2026-10-29 01:02:19', NULL, '2026-10-29 00:57:18', '2026-10-29 01:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `claim_images`
--

CREATE TABLE `claim_images` (
  `id` bigint UNSIGNED NOT NULL,
  `claim_id` bigint UNSIGNED NOT NULL,
  `path_foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claim_images`
--

INSERT INTO `claim_images` (`id`, `claim_id`, `path_foto`, `keterangan`, `urutan`, `created_at`, `updated_at`) VALUES
(1, 1, 'claims/2026/10/klaim-dompet-hitam-1-01-kozsqo.jpg', NULL, 1, '2026-10-27 03:58:59', '2026-10-27 03:58:59'),
(2, 1, 'claims/2026/10/klaim-dompet-hitam-1-02-banyz5.jpg', NULL, 2, '2026-10-27 03:58:59', '2026-10-27 03:58:59'),
(3, 2, 'claims/2026/10/klaim-dompet-hitam-2-01-31yvxy.jpg', NULL, 1, '2026-10-27 23:21:52', '2026-10-27 23:21:52'),
(4, 2, 'claims/2026/10/klaim-dompet-hitam-2-02-js1ngl.jpg', NULL, 2, '2026-10-27 23:21:52', '2026-10-27 23:21:52'),
(5, 3, 'claims/2026/10/klaim-jaket-boomber-3-01-6pgbz2.jpg', NULL, 1, '2026-10-28 02:14:30', '2026-10-28 02:14:30'),
(6, 3, 'claims/2026/10/klaim-jaket-boomber-3-02-jak3tj.jpg', NULL, 2, '2026-10-28 02:14:30', '2026-10-28 02:14:30'),
(7, 4, 'claims/2026/10/klaim-jam-tangan-rolex-4-01-m5ryme.jpg', NULL, 1, '2026-10-28 03:47:20', '2026-10-28 03:47:20'),
(8, 4, 'claims/2026/10/klaim-jam-tangan-rolex-4-02-tnjvpy.jpg', NULL, 2, '2026-10-28 03:47:20', '2026-10-28 03:47:20'),
(9, 5, 'claims/2026/10/klaim-jam-tangan-rolex-5-01-lh9ewp.jpg', NULL, 1, '2026-10-28 03:51:34', '2026-10-28 03:51:34'),
(10, 5, 'claims/2026/10/klaim-jam-tangan-rolex-5-02-bvv8s6.jpg', NULL, 2, '2026-10-28 03:51:34', '2026-10-28 03:51:34'),
(11, 6, 'claims/2026/10/klaim-kunci-motor-honda-6-01-goj0oe.jpg', NULL, 1, '2026-10-28 04:07:21', '2026-10-28 04:07:21'),
(12, 7, 'claims/2026/10/klaim-kunci-motor-7-01-imbwl6.jpg', NULL, 1, '2026-10-29 00:53:26', '2026-10-29 00:53:26'),
(13, 8, 'claims/2026/10/klaim-kunci-motor-8-01-derozo.jpg', NULL, 1, '2026-10-29 00:57:18', '2026-10-29 00:57:18');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `handover_images`
--

CREATE TABLE `handover_images` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `claim_id` bigint UNSIGNED NOT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `path_foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `handover_images`
--

INSERT INTO `handover_images` (`id`, `item_id`, `claim_id`, `uploaded_by`, `path_foto`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 9, 2, 3, 'handovers/2026/10/serah-terima-dompet-hitam-2-01-ydhoqw.jpg', 'Diserahkan di kantin', '2026-10-28 02:26:50', '2026-10-28 02:26:50'),
(2, 9, 2, 3, 'handovers/2026/10/serah-terima-dompet-hitam-2-02-tbd7oi.jpg', 'Diserahkan di kantin', '2026-10-28 02:26:50', '2026-10-28 02:26:50'),
(3, 9, 2, 3, 'handovers/2026/10/serah-terima-dompet-hitam-2-03-ocs6ye.jpg', 'Diserahkan di kantin jam 2 siang tadi', '2026-10-28 02:32:07', '2026-10-28 02:32:07'),
(4, 6, 5, 2, 'handovers/2026/10/serah-terima-jam-tangan-rolex-5-01-hujkri.jpg', 'diserahkan', '2026-10-28 03:54:55', '2026-10-28 03:54:55'),
(5, 6, 5, 2, 'handovers/2026/10/serah-terima-jam-tangan-rolex-5-02-f7zqji.jpg', 'diserahkan', '2026-10-28 03:54:55', '2026-10-28 03:54:55'),
(6, 6, 5, 2, 'handovers/2026/10/serah-terima-jam-tangan-rolex-5-03-rqdzqp.jpg', 'Sudah dikembalikan barang yang benarnya', '2026-10-28 03:56:45', '2026-10-28 03:56:45'),
(7, 6, 5, 2, 'handovers/2026/10/serah-terima-jam-tangan-rolex-5-04-1lcydo.jpg', 'Sudah dikembalikan barang yang benarnya', '2026-10-28 03:56:45', '2026-10-28 03:56:45'),
(8, 7, 3, 2, 'handovers/2026/10/serah-terima-jaket-boomber-3-01-nnklnt.jpg', 'diserahkan', '2026-10-28 04:02:36', '2026-10-28 04:02:36'),
(9, 7, 3, 2, 'handovers/2026/10/serah-terima-jaket-boomber-3-02-rigsqk.jpg', 'diserahkan', '2026-10-28 04:02:36', '2026-10-28 04:02:36'),
(10, 10, 8, 2, 'handovers/2026/10/serah-terima-kunci-motor-8-01-usvas9.jpg', 'Diserahkan di kantin', '2026-10-29 00:58:47', '2026-10-29 00:58:47'),
(11, 10, 8, 2, 'handovers/2026/10/serah-terima-kunci-motor-8-02-0klilw.jpg', 'Diserahkan di kantin', '2026-10-29 00:58:47', '2026-10-29 00:58:47'),
(12, 10, 8, 2, 'handovers/2026/10/serah-terima-kunci-motor-8-03-jojgyh.jpg', 'sudah yaa', '2026-10-29 01:01:38', '2026-10-29 01:01:38'),
(13, 10, 8, 2, 'handovers/2026/10/serah-terima-kunci-motor-8-04-41s6ht.jpg', 'sudah yaa', '2026-10-29 01:01:38', '2026-10-29 01:01:38');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `jenis` enum('temuan','hilang') COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `detail_lokasi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_kejadian` date NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `ciri_rahasia` text COLLATE utf8mb4_unicode_ci,
  `lokasi_penyimpanan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','verifikasi','dikembalikan','kedaluwarsa','dihapus') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `expires_at` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_claim_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `user_id`, `jenis`, `judul`, `category_id`, `location_id`, `detail_lokasi`, `tanggal_kejadian`, `deskripsi`, `ciri_rahasia`, `lokasi_penyimpanan`, `status`, `expires_at`, `completed_at`, `completed_claim_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'temuan', 'Dompet Hitam', 1, 3, NULL, '2026-10-26', 'Dompet kulit warna hitam', NULL, NULL, 'aktif', NULL, NULL, NULL, '2026-10-26 00:27:28', '2026-10-26 00:27:28'),
(2, 1, 'hilang', 'Kunci Motor Honda', 3, 2, NULL, '2026-10-25', NULL, NULL, NULL, 'verifikasi', NULL, NULL, NULL, '2026-10-26 00:27:28', '2026-10-28 04:07:21'),
(3, 1, 'temuan', 'Tumbler Biru', 7, 1, NULL, '2026-10-24', NULL, NULL, NULL, 'dihapus', NULL, NULL, NULL, '2026-10-26 00:27:28', '2026-10-29 01:50:20'),
(4, 1, 'temuan', 'Jaket Hitam', 9, 4, NULL, '2026-10-21', NULL, NULL, NULL, 'dikembalikan', NULL, NULL, NULL, '2026-10-26 00:27:28', '2026-10-26 00:27:28'),
(6, 2, 'hilang', 'Jam Tangan Rolex', 6, 3, 'Sekitaran kantin pas jam istirahat makan siang jam 12.00', '2026-10-27', 'Jam bermerek Rolex, warnanya perak dan hitam', NULL, NULL, 'dikembalikan', '2026-12-26', '2026-10-28 03:57:32', 5, '2026-10-27 01:34:32', '2026-10-28 03:57:32'),
(7, 2, 'temuan', 'Jaket Boomber', 9, 12, 'Didapat di Gedung N kelas N-110', '2026-10-27', 'Jaket boomber berwarna hijau', 'Jaketnya berukuran L', 'Saya bawa sendiri', 'dikembalikan', '2026-12-26', '2026-10-28 04:05:46', 3, '2026-10-27 02:04:25', '2026-10-28 04:05:46'),
(8, 3, 'temuan', 'Jam Tangan', 6, 3, 'Di meja makan kantin', '2026-10-27', 'Jam tangan perak', 'Jam tangan berwarna perak, merek rolex', 'Saya bawa sendiri', 'kedaluwarsa', '2026-10-28', NULL, NULL, '2026-10-27 02:43:56', '2026-10-29 01:53:37'),
(9, 3, 'temuan', 'Dompet Hitam', 1, 2, 'Tertinggal di perpus meja baca', '2026-10-27', 'Dompet kulit berwarna hitam', 'isi dompetnya ada ktp, ktm, dan 3 kartu atm', 'Saya bawa sendiri', 'dikembalikan', '2026-12-26', '2026-10-28 02:33:24', 2, '2026-10-27 03:52:19', '2026-10-28 02:33:24'),
(10, 2, 'hilang', 'Kunci Motor', 3, 5, 'Tadi tertinggal di parkiran', '2026-10-29', 'kunci motor honda', 'kunci motor honda', NULL, 'dikembalikan', '2026-12-28', '2026-10-29 01:02:19', 8, '2026-10-29 00:49:47', '2026-10-29 01:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `item_images`
--

CREATE TABLE `item_images` (
  `id` bigint UNSIGNED NOT NULL,
  `item_id` bigint UNSIGNED NOT NULL,
  `path_foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_images`
--

INSERT INTO `item_images` (`id`, `item_id`, `path_foto`, `urutan`, `created_at`, `updated_at`) VALUES
(4, 6, 'items/2026/10/hilang-jam-tangan-rolex-6-01-ghrsqz.jpg', 1, '2026-10-27 01:34:32', '2026-10-27 01:34:32'),
(5, 6, 'items/2026/10/hilang-jam-tangan-rolex-6-02-9v9orw.jpg', 2, '2026-10-27 01:34:32', '2026-10-27 01:34:32'),
(6, 6, 'items/2026/10/hilang-jam-tangan-rolex-6-03-lwl9jm.jpg', 3, '2026-10-27 01:34:32', '2026-10-27 01:34:32'),
(7, 7, 'items/2026/10/temuan-jaket-boomber-7-01-irh105.jpg', 1, '2026-10-27 02:04:25', '2026-10-27 02:04:25'),
(8, 7, 'items/2026/10/temuan-jaket-boomber-7-02-4zstsr.jpg', 2, '2026-10-27 02:04:25', '2026-10-27 02:04:25'),
(9, 7, 'items/2026/10/temuan-jaket-boomber-7-03-nmg6b5.jpg', 3, '2026-10-27 02:04:25', '2026-10-27 02:04:25'),
(10, 8, 'items/2026/10/temuan-jam-tangan-8-01-azijro.jpg', 1, '2026-10-27 02:43:56', '2026-10-27 02:43:56'),
(11, 8, 'items/2026/10/temuan-jam-tangan-8-02-5nqkbx.jpg', 2, '2026-10-27 02:43:56', '2026-10-27 02:43:56'),
(12, 8, 'items/2026/10/temuan-jam-tangan-8-03-bfljbe.jpg', 3, '2026-10-27 02:43:56', '2026-10-27 02:43:56'),
(13, 9, 'items/2026/10/temuan-dompet-hitam-9-01-ghtfih.jpg', 1, '2026-10-27 03:52:19', '2026-10-27 03:52:19'),
(14, 9, 'items/2026/10/temuan-dompet-hitam-9-02-ppklg9.jpg', 2, '2026-10-27 03:52:19', '2026-10-27 03:52:19'),
(15, 9, 'items/2026/10/temuan-dompet-hitam-9-03-eyrj4f.jpg', 3, '2026-10-27 03:52:19', '2026-10-27 03:52:19'),
(16, 10, 'items/2026/10/hilang-kunci-motor-10-01-ju1p4w.jpg', 1, '2026-10-29 00:49:49', '2026-10-29 00:49:49'),
(17, 10, 'items/2026/10/hilang-kunci-motor-10-02-g8v0lc.jpg', 2, '2026-10-29 00:49:49', '2026-10-29 00:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Gedung Direktorat', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(2, 'Perpustakaan', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(3, 'Kantin', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(4, 'Masjid / Musholla', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(5, 'Parkiran', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(6, 'Lapangan / Area Olahraga', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(7, 'Jurusan Teknik Sipil', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(8, 'Jurusan Teknik Mesin', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(9, 'Jurusan Teknik Elektro', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(10, 'Jurusan Akuntansi', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(11, 'Jurusan Administrasi Niaga', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(12, 'Jurusan Teknik Komputer & Informatika', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(13, 'Laboratorium', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(14, 'Toilet', '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(15, 'Lainnya', '2026-10-26 00:19:18', '2026-10-26 00:19:18');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_26_065002_create_categories_table', 2),
(5, '2026_10_26_065003_create_locations_table', 2),
(6, '2026_10_26_065005_create_items_table', 2),
(7, '2026_10_26_065008_create_item_images_table', 2),
(8, '2026_10_26_065010_create_claims_table', 2),
(9, '2026_10_26_065012_add_completed_claim_fk_to_items_table', 2),
(10, '2026_10_26_065013_create_claim_images_table', 2),
(11, '2026_10_26_065015_create_handover_images_table', 2),
(12, '2026_10_26_065041_create_notifications_table', 2),
(13, '2026_10_26_065144_add_fields_to_users_table', 2),
(14, '2026_10_28_082441_add_handover_fields_to_claims_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('041e2752-57ad-4b58-89b4-82eae63750bd', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 1, '{\"judul\":\"Postingan dipulihkan\",\"pesan\":\"\\\"Tumbler Biru\\\" ditampilkan kembali.\",\"url\":\"\\/barang\\/3\",\"ikon\":\"rotate-ccw\",\"warna\":\"emerald\"}', '2026-10-29 01:49:25', '2026-10-29 01:49:18', '2026-10-29 01:49:25'),
('271679a7-b588-4e9c-8b50-3cbe669f9928', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 3, '{\"judul\":\"Peran admin dicabut\",\"pesan\":\"Akunmu kembali menjadi pengguna biasa.\",\"url\":\"\\/dashboard\",\"ikon\":\"shield-check\",\"warna\":\"slate\"}', NULL, '2026-10-29 01:52:44', '2026-10-29 01:52:44'),
('4ec73a6a-4de4-436b-8d6e-b0bab1fa581f', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 1, '{\"judul\":\"Postingan dihapus admin\",\"pesan\":\"\\\"Tumbler Biru\\\" dihapus. Alasan: sudah lama postingannya\",\"url\":\"\\/dashboard\",\"ikon\":\"trash-2\",\"warna\":\"rose\"}', '2026-10-29 01:48:34', '2026-10-29 01:48:22', '2026-10-29 01:48:34'),
('6ff61617-0c27-4703-9b4c-06832ce588b3', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 1, '{\"judul\":\"Postingan dihapus admin\",\"pesan\":\"\\\"Tumbler Biru\\\" dihapus. Alasan: hapus\",\"url\":\"\\/dashboard\",\"ikon\":\"trash-2\",\"warna\":\"rose\"}', NULL, '2026-10-29 01:50:21', '2026-10-29 01:50:21'),
('70985861-61e8-4348-937a-5c64a069c5a7', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 3, '{\"judul\":\"Kamu sekarang admin\",\"pesan\":\"Kamu bisa membuka Panel Admin dari menu akun.\",\"url\":\"\\/dashboard\",\"ikon\":\"shield-check\",\"warna\":\"emerald\"}', NULL, '2026-10-29 01:51:52', '2026-10-29 01:51:52'),
('8d572d79-7a86-4a32-b4ca-cd2ba594d787', 'App\\Notifications\\AppNotification', 'App\\Models\\User', 3, '{\"judul\":\"Postingan kedaluwarsa\",\"pesan\":\"\\\"Jam Tangan\\\" melewati batas 60 hari tanpa klaim.\",\"url\":\"\\/barang\\/8\",\"ikon\":\"clock\",\"warna\":\"slate\"}', '2026-10-29 01:53:58', '2026-10-29 01:53:37', '2026-10-29 01:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ZtSek4E275LKA3z2ikPBk5LYnPvN8yOv6Y4e5wj8', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI4V3d1d2FFTkVVTVE0V1ExdGR4djFKdG1BdTN2TXRxQk1DaW41OHVOIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9zdGZvdW5kcG9sbWVkLnRlc3QiLCJyb3V0ZSI6ImhvbWUifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsInBhc3N3b3JkX2hhc2hfd2ViIjoiZDA4YTQzZTNjMDU0YjNlZDkxNzZiZWJiYmI2ZjVmYWNkYjkwZGMwZmY5ODY0OTk4N2RkMzk4NDEyMTc5N2U2NiJ9', 1793354764);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_wa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('mahasiswa','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nim`, `no_wa`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin LostFound Polmed', 'admin@lostfoundpolmed.test', NULL, NULL, 'admin', NULL, '$2y$12$yNQJFwbkDVzwjzE1tlOTRODgvPKXLhh3VYWtOXI7Qs9IcdTmHTmTO', NULL, '2026-10-26 00:19:18', '2026-10-26 00:19:18'),
(2, 'Imanuel Reformata Hulu', 'imanuelhulu01@gmail.com', '2305181073', '6285275363756', 'mahasiswa', NULL, '$2y$12$.KW95ZHjuwyxGo/Afc1ub.acauSXG6p4gnpxieorPddhc7gurGL9O', 'e4JBidT7fsbNje4SUIyzmPPmNACZ3WUUb3i76k3Y0zlQxp8nzqCQIkuVdm2H', '2026-10-27 01:06:53', '2026-10-30 02:08:39'),
(3, 'Nuel Widodo', 'nuel@gmail.com', '2305181074', '6285275363756', 'mahasiswa', NULL, '$2y$12$XEBG/e37cPAta/vyPffg4ueRi92WQ8sy/3NxPpwamL4SXoE4BBk0G', NULL, '2026-10-27 02:39:43', '2026-10-29 01:52:44'),
(4, 'Danang Suherman', 'danang@gmail.com', '2305181075', '6285275363756', 'mahasiswa', NULL, '$2y$12$z/L8u.hXOExh1SdpvkgPCei0yI9imqc9nibNkrD5QPMVoYpDCDj3q', NULL, '2026-10-27 04:22:03', '2026-10-27 04:22:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claims_item_id_foreign` (`item_id`),
  ADD KEY `claims_claimant_id_foreign` (`claimant_id`),
  ADD KEY `claims_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `claims_status_index` (`status`);

--
-- Indexes for table `claim_images`
--
ALTER TABLE `claim_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_images_claim_id_foreign` (`claim_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `handover_images`
--
ALTER TABLE `handover_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `handover_images_item_id_foreign` (`item_id`),
  ADD KEY `handover_images_claim_id_foreign` (`claim_id`),
  ADD KEY `handover_images_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_user_id_foreign` (`user_id`),
  ADD KEY `items_category_id_foreign` (`category_id`),
  ADD KEY `items_location_id_foreign` (`location_id`),
  ADD KEY `items_jenis_index` (`jenis`),
  ADD KEY `items_status_index` (`status`),
  ADD KEY `items_created_at_index` (`created_at`),
  ADD KEY `items_completed_claim_id_foreign` (`completed_claim_id`);

--
-- Indexes for table `item_images`
--
ALTER TABLE `item_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_images_item_id_foreign` (`item_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nim_unique` (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `claim_images`
--
ALTER TABLE `claim_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `handover_images`
--
ALTER TABLE `handover_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `item_images`
--
ALTER TABLE `item_images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_claimant_id_foreign` FOREIGN KEY (`claimant_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `claims_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `claim_images`
--
ALTER TABLE `claim_images`
  ADD CONSTRAINT `claim_images_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `handover_images`
--
ALTER TABLE `handover_images`
  ADD CONSTRAINT `handover_images_claim_id_foreign` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `handover_images_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `handover_images_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `items_completed_claim_id_foreign` FOREIGN KEY (`completed_claim_id`) REFERENCES `claims` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `items_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `item_images`
--
ALTER TABLE `item_images`
  ADD CONSTRAINT `item_images_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
