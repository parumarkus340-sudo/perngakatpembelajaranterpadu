-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 07:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perangkat_pembelajaran`
--

-- --------------------------------------------------------

--
-- Table structure for table `album_kegiatan`
--

CREATE TABLE `album_kegiatan` (
  `id` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `catatan_harian`
--

CREATE TABLE `catatan_harian` (
  `id` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `judul` varchar(200) DEFAULT NULL,
  `catatan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_perangkat`
--

CREATE TABLE `dokumen_perangkat` (
  `id` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_mapel` int(11) DEFAULT NULL,
  `jenis` enum('cp','atp','prota','promes','jurnal','rpp','modul','penilaian','album','catatan','raport') NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `drive_link` varchar(500) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `semester` enum('1','2') DEFAULT NULL,
  `tahun_ajaran` varchar(9) DEFAULT NULL,
  `status` enum('draft','pending_kepsek','ditolak_kepsek','pending_pengawas','ditolak_pengawas','terverifikasi') DEFAULT 'draft',
  `ttd_kepsek` varchar(255) DEFAULT NULL,
  `ttd_kepsek_date` datetime DEFAULT NULL,
  `catatan_kepsek` text DEFAULT NULL,
  `ttd_pengawas` varchar(255) DEFAULT NULL,
  `ttd_pengawas_date` datetime DEFAULT NULL,
  `catatan_pengawas` text DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `downloads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokumen_perangkat`
--

INSERT INTO `dokumen_perangkat` (`id`, `id_guru`, `id_sekolah`, `id_kelas`, `id_mapel`, `jenis`, `judul`, `deskripsi`, `file_path`, `drive_link`, `file_type`, `file_size`, `semester`, `tahun_ajaran`, `status`, `ttd_kepsek`, `ttd_kepsek_date`, `catatan_kepsek`, `ttd_pengawas`, `ttd_pengawas_date`, `catatan_pengawas`, `views`, `downloads`, `created_at`, `updated_at`) VALUES
(3, 701, 520, 6, 3, 'cp', 'CP MAT', '', NULL, 'https://docs.google.com/spreadsheets/d/1pSAiRoROI1ODXeggyrBxQ8AaYwx8eGg1iTcgf7cjD_E/edit?pli=1&gid=1891298658#gid=1891298658', NULL, NULL, '1', '2025/2026', 'terverifikasi', NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, '2026-08-23 03:10:33', '2026-08-23 03:52:39');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_mengajar`
--

CREATE TABLE `jadwal_mengajar` (
  `id` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `id_mapel` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` varchar(10) NOT NULL,
  `jenjang` varchar(20) DEFAULT 'SMA'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `jenjang`) VALUES
(1, '1', 'SD'),
(2, '2', 'SD'),
(3, '3', 'SD'),
(4, '4', 'SD'),
(5, '5', 'SD'),
(6, '6', 'SD'),
(7, '7', 'SMP'),
(8, '8', 'SMP'),
(9, '9', 'SMP'),
(10, '10', 'SMA'),
(11, '11', 'SMA'),
(12, '12', 'SMA');

-- --------------------------------------------------------

--
-- Table structure for table `komentar`
--

CREATE TABLE `komentar` (
  `id` int(11) NOT NULL,
  `id_perangkat` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentar`
--

INSERT INTO `komentar` (`id`, `id_perangkat`, `id_user`, `komentar`, `created_at`) VALUES
(1, 1, 2, 'RPP ini sangat membantu untuk mengajar SPLTV!', '2026-08-21 08:49:17'),
(2, 1, NULL, 'RPP sudah sesuai dengan kurikulum merdeka.', '2026-08-21 08:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `log_unduhan`
--

CREATE TABLE `log_unduhan` (
  `id` int(11) NOT NULL,
  `id_perangkat` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `downloaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_unduhan`
--

INSERT INTO `log_unduhan` (`id`, `id_perangkat`, `id_user`, `ip_address`, `downloaded_at`) VALUES
(1, 1, 2, '192.168.1.1', '2026-08-21 08:49:17'),
(2, 1, 3, '192.168.1.2', '2026-08-21 08:49:17'),
(3, 2, 2, '192.168.1.1', '2026-08-21 08:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `mata_pelajaran`
--

CREATE TABLE `mata_pelajaran` (
  `id` int(11) NOT NULL,
  `nama_mapel` varchar(50) NOT NULL,
  `kode_mapel` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mata_pelajaran`
--

INSERT INTO `mata_pelajaran` (`id`, `nama_mapel`, `kode_mapel`) VALUES
(1, 'Matematika', 'MTK'),
(2, 'Bahasa Indonesia', 'BIN'),
(3, 'Bahasa Inggris', 'BIG'),
(4, 'IPA', 'IPA'),
(5, 'IPS', 'IPS'),
(6, 'Pendidikan Agama', 'PAI'),
(7, 'PKN', 'PKN'),
(8, 'Seni Budaya', 'SBK'),
(9, 'PJOK', 'PJOK'),
(10, 'Informatika', 'INF'),
(11, 'Pendidikan Agama Katolik', 'PAK');

-- --------------------------------------------------------

--
-- Table structure for table `pengawas_sekolah`
--

CREATE TABLE `pengawas_sekolah` (
  `id` int(11) NOT NULL,
  `pengawas_id` int(11) NOT NULL,
  `sekolah_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengawas_sekolah`
--

INSERT INTO `pengawas_sekolah` (`id`, `pengawas_id`, `sekolah_id`, `created_at`) VALUES
(1, 685, 2, '2026-08-22 05:23:10'),
(2, 689, 520, '2026-08-22 05:23:10'),
(4, 689, 447, '2026-08-22 05:29:43'),
(5, 689, 337, '2026-08-22 05:30:00'),
(6, 688, 472, '2026-08-25 01:08:25'),
(7, 688, 310, '2026-08-25 01:08:41'),
(8, 688, 448, '2026-08-25 01:08:55'),
(9, 688, 495, '2026-08-25 01:09:09'),
(10, 688, 355, '2026-08-25 01:09:24');

-- --------------------------------------------------------

--
-- Table structure for table `perangkat`
--

CREATE TABLE `perangkat` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jenis` enum('RPP','Modul','PPT','Video','Soal','Lainnya') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `semester` enum('1','2') NOT NULL,
  `tahun_ajaran` varchar(9) NOT NULL,
  `id_mapel` int(11) DEFAULT NULL,
  `id_kelas` int(11) DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `status` enum('draft','pending_kepsek','ditolak_kepsek','pending_pengawas','ditolak_pengawas','terverifikasi') DEFAULT 'draft',
  `ttd_kepsek` varchar(255) DEFAULT NULL,
  `ttd_kepsek_date` datetime DEFAULT NULL,
  `catatan_kepsek` text DEFAULT NULL,
  `ttd_pengawas` varchar(255) DEFAULT NULL,
  `ttd_pengawas_date` datetime DEFAULT NULL,
  `catatan_pengawas` text DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `downloads` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perangkat`
--

INSERT INTO `perangkat` (`id`, `judul`, `deskripsi`, `jenis`, `file_path`, `thumbnail`, `semester`, `tahun_ajaran`, `id_mapel`, `id_kelas`, `id_guru`, `status`, `ttd_kepsek`, `ttd_kepsek_date`, `catatan_kepsek`, `ttd_pengawas`, `ttd_pengawas_date`, `catatan_pengawas`, `views`, `downloads`, `created_at`, `updated_at`) VALUES
(1, 'RPP Matematika Kelas 10 - Sistem Persamaan Linear', 'RPP Matematika materi SPLTV dengan metode discovery learning', 'RPP', 'uploads/perangkat/RPP/rpp_mtk_10_spltv.pdf', NULL, '1', '2025/2026', 1, 10, 1, 'terverifikasi', 'ttd_kepsek_1.png', '2026-08-21 15:49:17', NULL, 'ttd_pengawas_1.png', '2026-08-21 15:49:17', NULL, 17, 8, '2026-08-21 08:49:17', '2026-08-21 11:34:50'),
(2, 'Modul IPA Kelas 11 - Gelombang Bunyi', 'Modul Fisika tentang gelombang bunyi dengan eksperimen', 'Modul', 'uploads/perangkat/Modul/modul_ipa_11_gelombang.pdf', NULL, '2', '2025/2026', 4, 11, 1, 'terverifikasi', 'ttd_kepsek_2.png', '2026-08-21 15:49:17', NULL, 'ttd_pengawas_2.png', '2026-08-21 15:49:17', NULL, 10, 5, '2026-08-21 08:49:17', '2026-08-21 08:49:17'),
(3, 'PPT Bahasa Inggris Kelas 12 - Analytical Exposition', 'Presentasi Powerpoint untuk materi Analytical Exposition', 'PPT', 'uploads/perangkat/PPT/ppt_big_12_analytical.pptx', NULL, '1', '2025/2026', 3, 12, 2, 'pending_kepsek', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-08-21 08:49:17', '2026-08-21 08:49:17'),
(4, 'Soal PAS Matematika Kelas 11 Semester 1', 'Soal PAS Matematika kelas 11 semester 1 (40 PG + 5 essay)', 'Soal', 'uploads/perangkat/Soal/soal_pas_mtk_11.pdf', NULL, '1', '2025/2026', 1, 11, 1, 'terverifikasi', 'ttd_kepsek_4.png', '2026-08-21 15:49:17', NULL, 'public/uploads/ttd/ttd_pengawas_4_20260821_120504.png', '2026-08-21 17:05:04', '', 0, 0, '2026-08-21 08:49:17', '2026-08-21 10:05:04'),
(5, 'Video Pembelajaran - Hukum Newton', 'Video animasi Fisika tentang Hukum Newton (15 menit)', 'Video', 'uploads/perangkat/Video/video_newton.mp4', NULL, '2', '2025/2026', 4, 10, 3, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2026-08-21 08:49:17', '2026-08-21 08:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_guru`
--

CREATE TABLE `presensi_guru` (
  `id` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('hadir','izin','sakit','alpa','cuti') DEFAULT 'hadir',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi_guru`
--

INSERT INTO `presensi_guru` (`id`, `id_sekolah`, `id_guru`, `tanggal`, `jam_masuk`, `jam_keluar`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-08-22', '07:30:00', '15:30:00', 'hadir', NULL, '2026-08-22 08:00:50', '2026-08-22 08:00:50'),
(2, 1, 2, '2026-08-22', '07:45:00', '15:30:00', 'hadir', NULL, '2026-08-22 08:00:50', '2026-08-22 08:00:50'),
(3, 2, 3, '2026-08-22', '08:00:00', NULL, 'izin', NULL, '2026-08-22 08:00:50', '2026-08-22 08:00:50'),
(4, 520, 701, '2026-08-22', '10:03:05', '10:03:09', 'hadir', NULL, '2026-08-22 08:03:05', '2026-08-22 08:03:09'),
(5, 1, 4, '2026-08-22', '07:30:00', '15:30:00', 'hadir', NULL, '2026-08-22 12:57:21', '2026-08-22 12:57:21'),
(6, 520, 508, '2026-08-22', '15:05:13', '15:05:17', 'hadir', NULL, '2026-08-22 13:05:13', '2026-08-22 13:05:17');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_siswa`
--

CREATE TABLE `presensi_siswa` (
  `id` int(11) NOT NULL,
  `id_sekolah` int(11) NOT NULL,
  `id_guru` int(11) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `total_siswa` int(11) DEFAULT 0,
  `hadir` int(11) DEFAULT 0,
  `sakit` int(11) DEFAULT 0,
  `izin` int(11) DEFAULT 0,
  `alpa` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi_siswa`
--

INSERT INTO `presensi_siswa` (`id`, `id_sekolah`, `id_guru`, `id_kelas`, `tanggal`, `total_siswa`, `hadir`, `sakit`, `izin`, `alpa`, `keterangan`, `created_at`) VALUES
(1, 1, 1, 1, '2026-08-22', 30, 28, 1, 1, 0, NULL, '2026-08-22 08:00:50'),
(2, 1, 1, 2, '2026-08-22', 32, 30, 1, 0, 1, NULL, '2026-08-22 08:00:50'),
(3, 2, 2, 3, '2026-08-22', 28, 25, 2, 1, 0, NULL, '2026-08-22 08:00:50'),
(4, 520, 701, 5, '2026-08-22', 10, 10, 0, 0, 0, '', '2026-08-22 08:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_status`
--

CREATE TABLE `riwayat_status` (
  `id` int(11) NOT NULL,
  `id_perangkat` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `status_awal` varchar(30) DEFAULT NULL,
  `status_akhir` varchar(30) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayat_status`
--

INSERT INTO `riwayat_status` (`id`, `id_perangkat`, `id_user`, `status_awal`, `status_akhir`, `catatan`, `created_at`) VALUES
(1, 1, 1, 'draft', 'pending_kepsek', 'Guru mengirim RPP ke Kepala Sekolah', '2026-08-21 08:49:17'),
(2, 1, 4, 'pending_kepsek', 'pending_pengawas', 'Kepala Sekolah menyetujui dan menandatangani', '2026-08-21 08:49:17'),
(3, 1, 11, 'pending_pengawas', 'terverifikasi', 'Pengawas menyetujui dan menandatangani', '2026-08-21 08:49:17'),
(4, 2, 1, 'draft', 'pending_kepsek', 'Guru mengirim modul ke Kepala Sekolah', '2026-08-21 08:49:17'),
(5, 2, 4, 'pending_kepsek', 'pending_pengawas', 'Kepala Sekolah menyetujui dan menandatangani', '2026-08-21 08:49:17'),
(6, 2, 11, 'pending_pengawas', 'terverifikasi', 'Pengawas menyetujui dan menandatangani', '2026-08-21 08:49:17'),
(7, 4, NULL, 'pending_pengawas', 'terverifikasi', 'Pengawas menyetujui dan menandatangani', '2026-08-21 10:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `sekolah`
--

CREATE TABLE `sekolah` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(100) NOT NULL,
  `npsn` varchar(10) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kabupaten` varchar(50) DEFAULT NULL,
  `provinsi` varchar(50) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `kepala_sekolah` int(11) DEFAULT NULL,
  `pengawas` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sekolah`
--

INSERT INTO `sekolah` (`id`, `nama_sekolah`, `npsn`, `alamat`, `kelurahan`, `kecamatan`, `kabupaten`, `provinsi`, `kode_pos`, `kepala_sekolah`, `pengawas`, `created_at`) VALUES
(1, 'KB ARARA', '70027792', 'RANDORAMA', 'RANDORAMA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 4, 11, '2026-08-21 08:49:17'),
(2, 'KB Arrahman Watubara', '70005156', 'Watubara - Mukusaki', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 5, 13, '2026-08-21 08:49:17'),
(3, 'KB FAJAR PAGI', '70014378', 'Pena RT.007 RW.013 - Dusun Neotonda', 'NEOTONDA', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 6, 12, '2026-08-21 08:49:17'),
(4, 'KB KELIWUMBU', '70011247', 'DUSUN WEWARIA RT 02 RW 01 WEWARIA', 'Keliwumbu', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 7, 12, '2026-08-21 08:49:17'),
(5, 'KB MARLOM', '70006252', 'Jl. Jurusan Ende - Maumere', 'Koanara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 8, 12, '2026-08-21 08:49:17'),
(6, 'KB MATABALE', '70002784', 'Jln. Udayana', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 9, 11, '2026-08-21 08:49:17'),
(7, 'KB MENTARI', '70047941', 'NIRASERA', 'Rapowawo', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 10, 12, '2026-08-21 08:49:17'),
(8, 'KB NUALISE', '69991307', 'Nualise - Wolowaru', 'Nualise', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 11, 12, '2026-08-21 08:49:17'),
(9, 'KB PERTIWI', '70026767', 'TIWEREA', 'Tiwe Rea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 12, 12, '2026-08-21 08:49:17'),
(10, 'KB PERWIRA', '69987093', 'Jl. Perwira', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 13, 11, '2026-08-21 08:49:17'),
(11, 'KB SANTO HENDRIKUS', '70002740', 'Nggumbelaka', 'Nggumbelaka', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 19, NULL, '2026-08-21 08:58:14'),
(12, 'KB SANTO PHILIPUS', '70042684', 'JL. TRANS UTARA', 'Liselande', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 20, NULL, '2026-08-21 08:58:14'),
(13, 'KB SINAR EMBUZOZO', '70027166', 'EMBUZOZO', 'EMBUZOZO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 21, NULL, '2026-08-21 08:58:14'),
(14, 'KB SINAR OTOLEKE', '70014379', 'Wologai Timur', 'Wologai Timur', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 22, NULL, '2026-08-21 08:58:14'),
(15, 'KB ST. PIUS', '70028687', 'WOLOLELE A', 'Wololele A', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 23, NULL, '2026-08-21 08:58:14'),
(16, 'KB STA. ELISABETH', '70043776', 'JL. TRANS UTARA', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(17, 'KB TERPADU KASIH BUNDA', '70033674', 'DESA EMBUTHERU', 'EMBUTHERU', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 24, NULL, '2026-08-21 08:58:14'),
(18, 'KB TERPADU RENATA', '70025717', 'ARAWEA', 'Kerirea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 25, NULL, '2026-08-21 08:58:14'),
(19, 'KB TERPADU ST. PAULUS KOTAKADHE', '70048520', 'JL. TRANS UTARA', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 26, NULL, '2026-08-21 08:58:14'),
(20, 'KB WAKA', '70036061', 'WAKA', 'WAKA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 27, NULL, '2026-08-21 08:58:14'),
(21, 'KB Watu Gamba', '70007552', 'Desa Tomberabu II', 'Tomberabu Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 28, NULL, '2026-08-21 08:58:14'),
(22, 'KB WONGA WEA NGGELA', '70038565', 'DESA NGGELA', 'Nggela', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 29, NULL, '2026-08-21 08:58:14'),
(23, 'KB WONGAWUJA', '69988337', 'Tendambongi', 'TENDAMBONGGI', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 30, NULL, '2026-08-21 08:58:14'),
(24, 'KB. AEDERO', '69974111', 'Jl. Ende-Detukeli', 'Nggesa', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 31, NULL, '2026-08-21 08:58:14'),
(25, 'KB. AEMAU NANGARIA', '69969048', 'Jl. Trans Utara', 'NGALUKOJA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 32, NULL, '2026-08-21 08:58:14'),
(26, 'KB. Anggrek', '69845132', 'Jl. Flores-Puutuga', 'Puutuga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 33, NULL, '2026-08-21 08:58:14'),
(27, 'KB. ANUGERAH', '69967735', 'Jl.Jurusan Ende-Bajawa', 'WAJAKEA JAYA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 34, NULL, '2026-08-21 08:58:14'),
(28, 'KB. Ar - Rahman', '69845105', 'Jl.Adisucipto-Kel.Tetandara-Kec.Ende Selatan-Ende', 'Kel. Tetandara', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 35, NULL, '2026-08-21 08:58:14'),
(29, 'KB. AREMA', '69972512', 'RT/RW.04/02 Dusun Rekko', 'RENGA MENGE', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 36, NULL, '2026-08-21 08:58:14'),
(30, 'KB. AZZAHRA', '69845124', 'Jl Imam Bonjol-Ende', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 37, NULL, '2026-08-21 08:58:14'),
(31, 'KB. Bengawan Jaya', '69845186', 'Jl.Ende-Maumere', 'Kel. Detusoko', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 38, NULL, '2026-08-21 08:58:14'),
(32, 'KB. BEWU SEA', '69962430', 'Mukureku', 'Mukureku', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 39, NULL, '2026-08-21 08:58:14'),
(33, 'KB. BHISU KOJA', '69984519', 'Dusun Watukibi I, RT.01/RW.01', 'Sipijena', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 40, NULL, '2026-08-21 08:58:14'),
(34, 'KB. BINA KASIH TURUNALU', '69979762', 'Jl. Ende-Detusoko', 'Turunalu', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 41, NULL, '2026-08-21 08:58:14'),
(35, 'KB. Borokanda', '69845126', 'Jl. Ende-Bajawa', 'Borokanda', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 42, NULL, '2026-08-21 08:58:14'),
(36, 'KB. Bunda Perubahan', '69845099', 'Jl.Teuku Umar', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 43, NULL, '2026-08-21 08:58:14'),
(37, 'KB. CINTA ANAK HOBATUWA', '69979544', 'Jl. Ende-Maumere', 'Hobatuwa', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 44, NULL, '2026-08-21 08:58:14'),
(38, 'KB. CINTA ANAK NUABOSI', '69845097', 'NUABOSI', 'Ndetundora Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 45, NULL, '2026-08-21 08:58:14'),
(39, 'KB. DAI MAU ENGA NANGA', '69969715', 'Jl. Trans Utara', 'Mausambi', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(40, 'KB. DETUNGGALI  LEWUMBANGGA', '69967737', 'Jl. Trans Utara', 'Fataatu Timur', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 46, NULL, '2026-08-21 08:58:14'),
(41, 'KB. DEWI LESTARI', '69845101', 'Jln. Teuku Umar-Kel.Paupanda-Kec.Ende Selatan-Ende', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 47, NULL, '2026-08-21 08:58:14'),
(42, 'KB. DHOA ANA', '69981583', 'Jl. Nangapanda-Rajawawo', 'UZU ZOZO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(43, 'KB. EMBURIA', '69965363', 'Jl.Ende-Nangapanda', 'Emburia', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 48, NULL, '2026-08-21 08:58:14'),
(44, 'KB. GADO RUA', '69969716', 'Jl. Trans Utara', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(45, 'KB. Harapan Bangsa', '69845098', 'dusun kojadhewa', 'Rukuramba', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 49, NULL, '2026-08-21 08:58:14'),
(46, 'KB. HARAPAN BUNDA', '69845108', 'Jln.Ikan paus RT.003/002 Tanjung', 'Kel. Tanjung', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 50, NULL, '2026-08-21 08:58:14'),
(47, 'KB. KALEMBALE', '69963671', 'Jl. JurusanEnde-Maumere', 'Wolosambi', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(48, 'KB. Kasih Ibu Mundinggasa', '69845089', 'Ende-Maukaro', 'Mundinggasa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 51, NULL, '2026-08-21 08:58:14'),
(49, 'KB. Kasih Ibu Wolomuku', '69845174', 'JL. Detukeli', 'Wolomuku', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 52, NULL, '2026-08-21 08:58:14'),
(50, 'KB. Marilonga', '69845180', 'JL. Detukeli', 'Watunggere Marilonga', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 53, NULL, '2026-08-21 08:58:14'),
(51, 'KB. MODA BENGE', '69969729', 'Jl. Trans Utara', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 54, NULL, '2026-08-21 08:58:14'),
(52, 'KB. Nanganesa', '69845129', 'jL. Jurusan Ndona - Ende', 'Nanganesa', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 55, NULL, '2026-08-21 08:58:14'),
(53, 'KB. NASARET BENGGE', '69970474', 'Jl. Trans Utara', 'Kebirangga Tengah', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 56, NULL, '2026-08-21 08:58:14'),
(54, 'KB. Ndoki Pati', '69845179', 'JL. Detukeli', 'Nggesa Biri', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 57, NULL, '2026-08-21 08:58:14'),
(55, 'KB. PANTURA AEWORA', '69969714', 'Jl. Trans Utara', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(56, 'KB. PATAS', '69845100', 'Jln. Teuku Umar-Kel.Paupanda-Kec.Ende Selatan-Ende', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 58, NULL, '2026-08-21 08:58:14'),
(57, 'KB. PEDI PASO', '69974822', 'RT/RW.01/01 Dusun Wolobalu', 'Maurole Selatan', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 59, NULL, '2026-08-21 08:58:14'),
(58, 'KB. PERA PAWE', '69845130', 'jL. Ende-Ndona', 'Kel. Onelako', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 60, NULL, '2026-08-21 08:58:14'),
(59, 'KB. Ratu Pencinta Balita', '69845121', 'Jl. Woloare B', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 61, NULL, '2026-08-21 08:58:14'),
(60, 'KB. SALIB SUCI NDETUNDOPO', '69964154', 'Dusun Ndetundopo', 'Kolikapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 62, NULL, '2026-08-21 08:58:14'),
(61, 'KB. SANTA AGUSTINA AELOGA', '69973379', 'Jl. Pentura', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 63, NULL, '2026-08-21 08:58:14'),
(62, 'KB. SANTA SISILIA', '69963096', 'Desa Kedebodu,Kec.Ende Timur', 'Kedebodu', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 64, NULL, '2026-08-21 08:58:14'),
(63, 'KB. SEHATI', '69845093', 'Jl.Ende-Maumere', 'Tomberabu I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 65, NULL, '2026-08-21 08:58:14'),
(64, 'KB. St. ANTONIUS PAUWAWA', '69962392', 'Jl.Ende-Nangapanda', 'Jegharangga', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 66, NULL, '2026-08-21 08:58:14'),
(65, 'KB. St. SOFIA EKOLEA', '69964153', 'JL. Trans Utara', 'EKOLEA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 67, NULL, '2026-08-21 08:58:14'),
(66, 'KB. TOJA MODA', '69965985', 'Jl. Ende-Ndikosapu', 'Mukureku Sa Ate', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:14'),
(67, 'KB. TUNAS BANGSA', '69966188', 'Jl. Gajah Mada', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 68, NULL, '2026-08-21 08:58:14'),
(68, 'KB. TUNAS BARU', '69970499', 'Dusun Mau Au Atas', 'KAZO KAPO', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 69, NULL, '2026-08-21 08:58:14'),
(69, 'KB. ULU DALA', '69845162', 'Jl. Pantura-Desa Uludala-Kec.Maurole-Kab.Ende', 'ULUDALA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 70, NULL, '2026-08-21 08:58:14'),
(70, 'KB. WOLO WEA', '69845181', 'Jl.Trans Utara Ende-Maurole', 'Ranga', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 71, NULL, '2026-08-21 08:58:14'),
(71, 'KB. WOLOLANU', '69970440', 'Jl. Jurusan Ende-Maumere', 'Niramesi', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 72, NULL, '2026-08-21 08:58:14'),
(72, 'KB. WOLOSOKO', '69967475', 'Jl. Jurusan Ende-Maumere', 'Wolosoko', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 73, NULL, '2026-08-21 08:58:14'),
(73, 'KB. WOROPAPA', '69966705', 'Jl. Ende-Woropapa', 'WORHOPAPA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 74, NULL, '2026-08-21 08:58:14'),
(74, 'KB.Bintang Timur', '69845167', 'Jl.Pantura', 'Tou', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 75, NULL, '2026-08-21 08:58:14'),
(75, 'KB.Bunga Mawar', '69845133', 'Jl. Flores', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 76, NULL, '2026-08-21 08:58:14'),
(76, 'KB.Cahaya Permata', '69845187', 'Jl.Ende-Maumere', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 77, NULL, '2026-08-21 08:58:14'),
(77, 'KB.Danau Ranoria', '69845182', 'Jl. Pantura', 'Golulada', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 78, NULL, '2026-08-21 08:58:14'),
(78, 'KB.Fajar Timur', '69845169', 'Jl.Pantura', 'Tou Timur', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 79, NULL, '2026-08-21 08:58:14'),
(79, 'KB.Ilham', '69845087', 'Jl.Pantura', 'Kamubheka', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 80, NULL, '2026-08-21 08:58:14'),
(80, 'KB.Ingin Maju', '69845082', 'Jl. Kakadupa', 'Kel. Ndorurea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 81, NULL, '2026-08-21 08:58:14'),
(81, 'KB.Kasih Ibu Mbiru', '69845143', 'JL.Nggela', 'Nuamulu', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 82, NULL, '2026-08-21 08:58:14'),
(82, 'KB.Lunggaria', '69845156', 'Jl. Jurusan Ende-Maumere', 'Maubasa', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 83, NULL, '2026-08-21 08:58:14'),
(83, 'KB.Ndori Sare', '69845157', 'Jl. Jurusan Ende-Maumer', 'Maubasa Timur', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 84, NULL, '2026-08-21 08:58:14'),
(84, 'KB.NUAGIU', '69965986', 'Jl. Jurusan Ende-Maumere', 'Detusoko Barat', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 85, NULL, '2026-08-21 08:58:14'),
(85, 'KB.Nusa Sura', '69845083', 'Jl.Aejeti', 'Aejeti', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 86, NULL, '2026-08-21 08:58:14'),
(86, 'KB.Permata Hati', '69845155', 'Jl. Ende-Maumere', 'Nduaria', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 87, NULL, '2026-08-21 08:58:14'),
(87, 'KB.Permata Puutara', '69845085', 'Jl.Puutara', 'Puutara', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 88, NULL, '2026-08-21 08:58:14'),
(88, 'KB.Peromboro', '69845115', 'Jl. Jurusan Ende-Maumere', 'Tiwutewa', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 89, NULL, '2026-08-21 08:58:14'),
(89, 'KB.Sehati Rateroru', '69845183', 'Jl.Ende-Maumere', 'Rateroru', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 90, NULL, '2026-08-21 08:58:14'),
(90, 'KB.SEKOSODO', '69845163', 'Jl. Pantura', 'Watukamba', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 91, NULL, '2026-08-21 08:58:15'),
(91, 'KB.Sinar Bahagia', '69845084', 'Jl.Aejeti', 'Aejeti', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 92, NULL, '2026-08-21 08:58:15'),
(92, 'KB.Sinar Harapan Dile', '69845184', 'Jl.Ende-Maumere', 'Dile', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 93, NULL, '2026-08-21 08:58:15'),
(93, 'KB.St. Faustina Anaranda', '69845190', 'Jl. Pantura', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 94, NULL, '2026-08-21 08:58:15'),
(94, 'KB.St.Antonius', '69845189', 'Jl.Pantura', 'Ratewati', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 95, NULL, '2026-08-21 08:58:15'),
(95, 'KB.Tabah', '69845110', 'Jl.Gatot Subroto', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 96, NULL, '2026-08-21 08:58:15'),
(96, 'KB.Try Warna', '69845151', 'Desa Woloara-Kec.Kelimutu-Kab.Ende', 'Woloara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 97, NULL, '2026-08-21 08:58:15'),
(97, 'KB.Tunas Harapan', '69845090', 'Jl.Nuabosi', 'Ndetundora I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 98, NULL, '2026-08-21 08:58:15'),
(98, 'KB.Wolokoli', '69845140', 'Jl.Ende-Maumere', 'Wolokoli', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 99, NULL, '2026-08-21 08:58:15'),
(99, 'KBA MURI SARE', '69992558', 'Hangalande', 'Hangalande', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 100, NULL, '2026-08-21 08:58:15'),
(100, 'KBA NAZARETH', '69996280', 'Jl. Anggrek BTN', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 101, NULL, '2026-08-21 08:58:15'),
(101, 'KBA RAJAWALI', '69991613', 'Ngajo - Nangapanda', 'Ndeturea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 102, NULL, '2026-08-21 08:58:15'),
(102, 'KOBER AISYIYAH', '69946721', 'JL. KOKOS III PERUMNAS, Rt/Rw: 13/07', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 103, NULL, '2026-08-21 08:58:15'),
(103, 'KOBER AMBUGAGA', '69959893', 'Jl. Pahlawan', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 104, NULL, '2026-08-21 08:58:15'),
(104, 'KOBER BINA KASIH DETUNGGALI', '69946064', 'FATAATU, KECAMATAN WEWARIA', 'Fataatu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 105, NULL, '2026-08-21 08:58:15'),
(105, 'KOBER BOAFEO', '69953609', 'Jl.Trans Nangaba - Maukaro', 'Boafeo', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 106, NULL, '2026-08-21 08:58:15'),
(106, 'KOBER BOTI FATE', '69845173', 'Jln.Pantura-Desa Rangalaka-Kotabaru', 'Rangalaka', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 107, NULL, '2026-08-21 08:58:15'),
(107, 'KOBER GAMA GENERATION', '69946066', 'JL. Slamet Riyadi', 'Kel. Mbongawani', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 108, NULL, '2026-08-21 08:58:15'),
(108, 'KOBER KASIH BUNDA WIWIPEMO', '69946056', 'WIWIPEMO, NUSA TENGGARA TIMUR, ENDE,WOLOJITA, WIWIPEMO', 'Wiwipemo', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 109, NULL, '2026-08-21 08:58:15'),
(109, 'KOBER KASIH SAYANG PAUBEWA', '69946065', 'JL. PAUBEWA, NUSA TENGGARA TIMUR, ENDE,LEPEMBUSU KELISOKE, TANALANGI', 'Tanalangi', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:15'),
(110, 'KOBER LANDO RANGA', '69845172', 'Jln.Pantura-Desa Rangalaka-Kotabaru', 'Rangalaka', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 110, NULL, '2026-08-21 08:58:15'),
(111, 'KOBER MAUMERI PERMAI', '69960599', 'Jl.Trans Utara', 'Wewaria', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 111, NULL, '2026-08-21 08:58:15'),
(112, 'KOBER MEKAR SARI', '69952708', 'Jl. Jurusan Ende-Ndona', 'Manulondo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 112, NULL, '2026-08-21 08:58:15'),
(113, 'KOBER MORI MAJA', '69946068', 'JL.ENDE-PUUTUGA, DESA KELIKIKU, KECAMATAN NDONA, KABUPATEN ENDE', 'Kelikiku', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:15'),
(114, 'KOBER NIRAMESI', '69845141', 'Desa Niramesi-Kec.Wolowaru-Kab.Ende', 'Niramesi', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 113, NULL, '2026-08-21 08:58:15'),
(115, 'KOBER ODA MBESI', '69946244', 'JL.PUUKUNGU-MAUKARO, DESA TIMBA ZIA, NANGAPANDA, ENDE', 'TIMBAZIA', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 114, NULL, '2026-08-21 08:58:15'),
(116, 'KOBER PUU KOJA NDITO', '69946027', 'DESA NDITO-KECAMATAN DETUSOKO-KAB.ENDE', 'Ndito', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 115, NULL, '2026-08-21 08:58:15'),
(117, 'KOBER RENDO SARE', '69952673', 'Dusun Kemo', 'Rendoraterua', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 116, NULL, '2026-08-21 08:58:15'),
(118, 'KOBER SADO GEDU', '69946020', 'JL. ENDE-NANGABA-DESA UZURAMBA BARAT-KECAMATAN ENDE-ENDE', 'UZURAMBA BARAT', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 117, NULL, '2026-08-21 08:58:15'),
(119, 'KOBER SEKOPADA', '69946063', 'MAUTENDA, NUSA TENGGARA TIMUR, ENDE,WEWARIA, MAUTENDA', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 118, NULL, '2026-08-21 08:58:15'),
(120, 'KOBER SOLIDARITAS BUNDA', '69946245', 'JL.PATIMURA, NUSA TENGGARA TIMUR, ENDE,ENDE TENGAH, KEL. POTULANDO', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 119, NULL, '2026-08-21 08:58:15'),
(121, 'KOBER ST. BONEFASIUS MBANI', '69960462', 'Desa Wologai Dua-Ende', 'WOLOGAI DUA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 120, NULL, '2026-08-21 08:58:15'),
(122, 'KOBER St. DANIEL', '69945437', 'NUMBA, KEC.WEWARIA-KAB.ENDE', 'Numba', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 121, NULL, '2026-08-21 08:58:15'),
(123, 'KOBER St. JOSEF FREINADEMETZ MAUTAPAGA', '69946014', 'JL GATOT SUBROTO KM.3-KELURAHAN MAUTAPAG-KECAMATAN ENDE TIMUR-KAB.ENDE', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 122, NULL, '2026-08-21 08:58:15'),
(124, 'KOBER ST. PETRUS WOLOGERU', '69946054', 'DUSUN WOLOGERU, DESA RANDORIA, KECAMATAN DETUSOKO, KABUPATEN ENDE', 'Randoria', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:15'),
(125, 'KOBER ST. SIMON PETRUS TIWUSORA', '69945986', 'TIWUSORA-KECAMATAN LEPEMBUSU KELISOKE-KAB.ENDE', 'Tanalangi', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 123, NULL, '2026-08-21 08:58:15'),
(126, 'KOBER STA. THERESIA', '70054903', 'JL. TRANS ENDE-BAJAWA RT 001 RW 001', 'Ndorurea I', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 124, NULL, '2026-08-21 08:58:15'),
(127, 'KOBER TANI WODA KARYA', '69845171', 'Jln.Pantura-Desa Tou Barat-Kotabaru', 'Tou Barat', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 125, NULL, '2026-08-21 08:58:15'),
(128, 'KOBER TENDA TEBHA', '69946052', 'JL.ENDE-MAUKARO, DESA KOLIKAPA, KECAMATAN MAUKARO, KABUPATEN ENDE', 'Kolikapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 126, NULL, '2026-08-21 08:58:15'),
(129, 'KOBER UNGGU JAYA', '69945989', 'DESA UNGGU-KEC.DETUKELI-KABUPATEN ENDE-NTT', 'Unggu', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 127, NULL, '2026-08-21 08:58:15'),
(130, 'KOBER WEE LOMBO', '69946053', 'JL.ENDE-DANAU KELIMUTU, DESA WATURAKA, KECAMATAN KELIMUTU, KABUPATEN ENDE', 'WATURAKA', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 128, NULL, '2026-08-21 08:58:15'),
(131, 'KOBER WOLOMONI', '69946058', 'JURUSAN ENDE-MAUMERE KM. 28, NUSA TENGGARA TIMUR, ENDE,DETUSOKO, NIOWULA', 'Niowula', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 129, NULL, '2026-08-21 08:58:15'),
(132, 'PAUD PELITA', '69991940', 'Jalan Patimura', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 130, NULL, '2026-08-21 08:58:15'),
(133, 'PAUD ST. ALEXANDRIA NGGEMO', '69990190', 'Jl. Pantura -  Magekapa - Nggemo', 'Magekapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 131, NULL, '2026-08-21 08:58:15'),
(134, 'PAUD TERPADU POSYANDU MANDIRI BASA PUURERE', '69946019', 'JL. ENDE-NANGAPANDA-DESA RAPORENDU-KECAMATAN NANGAPANDA-ENDE', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 132, NULL, '2026-08-21 08:58:15'),
(135, 'TAUD Saqu Al Misykah', '69990820', 'JL. Gatot Subroto, Rt/Rw 06/02, Kel. Rewarangga Selatan, Kec. Ende Timur', 'Kel. Rewarangga Selatan', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 133, NULL, '2026-08-21 08:58:15'),
(136, 'PKBM ALOKOJA SIA', 'P9997487', 'JL. TRANS UTARA RT 005 RW 003 DUSUN KELITEMBU', 'Kelitembu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 134, NULL, '2026-08-21 08:58:15'),
(137, 'PKBM ANNORA', 'P9945672', 'JL.ADI SUCIPTO-KELURAHAN TETANDARA-KEC.ENDE SELATAN-KAB. ENDE', 'Kel. Tetandara', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 135, NULL, '2026-08-21 08:58:15'),
(138, 'PKBM BUNGA MAWAR', 'P9945655', 'Jl. Flores, Kelurahan Lokoboko, Kecamatan Ndona Kabupaten Ende', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 136, NULL, '2026-08-21 08:58:15'),
(139, 'PKBM KAPO WALO', 'P9997476', 'JL. GATOT SUBROTO ENDE', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 137, NULL, '2026-08-21 08:58:15'),
(140, 'PKBM KEBHI DUA', 'P9908890', 'Jl. Ende-Maumere KM.36, Desa Dile, Kecamatan Detusoko, Kabupaten Ende', 'Dile', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 138, NULL, '2026-08-21 08:58:15'),
(141, 'PKBM MARILONGA', 'P2960007', 'Dusun Kurukota Desa Nggesa Biri Kecamatan Detukeli Kabupaten Ende', 'Nggesa Biri', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 08:58:15'),
(142, 'PKBM Sandi Kelana Ngalukoja', 'P9996516', 'Dusun 1 Ngalukoja', 'NGALUKOJA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 139, NULL, '2026-08-21 08:58:15'),
(143, 'PKBM SANTA ANGELA ENDE', 'P2970874', 'JL. SULTAN HASANUDIN', 'Kel. Rewarangga Selatan', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 140, NULL, '2026-08-21 08:58:15'),
(144, 'SD GMIT ENDE 4', '50305570', 'Jln. Sudirman No.5', 'Kel. Roworena', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 141, NULL, '2026-08-21 08:58:15'),
(145, 'SD INPRES AEDARI', '50302735', 'Aedari', 'Detukeli', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 142, NULL, '2026-08-21 08:58:15'),
(146, 'SD INPRES AEKORA', '50302736', 'Aekora', 'Maurole Selatan', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 143, NULL, '2026-08-21 08:58:15'),
(147, 'SD INPRES AEMAU', '50305621', 'Aemau', 'NGALUKOJA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 144, NULL, '2026-08-21 08:58:15'),
(148, 'SD INPRES AEREA', '50302737', 'Aerea', 'Kelisamba', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 145, NULL, '2026-08-21 08:58:15'),
(149, 'SD INPRES AETEKE', '50302754', 'Aeteke', 'Hobatuwa', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 146, NULL, '2026-08-21 08:58:15'),
(150, 'SD INPRES BARAI 1', '50305564', 'Puumbara', 'Borokanda', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 147, NULL, '2026-08-21 08:58:15'),
(151, 'SD INPRES BARAI 2', '50302753', 'Barai Wena 2', 'Borokanda', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 148, NULL, '2026-08-21 08:58:15'),
(152, 'SD INPRES BELANGGO', '50305697', 'Belanggo', 'Likanaka', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 150, NULL, '2026-08-21 09:00:06'),
(153, 'SD INPRES BHOANAWA 1', '50305540', 'Jln. R.W. MONGINSIDI', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 151, NULL, '2026-08-21 09:00:06'),
(154, 'SD INPRES BHOANAWA 2', '50302751', 'Jln. Ikan Duyung', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 152, NULL, '2026-08-21 09:00:06'),
(155, 'SMP NEGERI 1 DETUSOKO', '50302604', 'Jalan Desa Welamosa', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 153, NULL, '2026-08-21 09:00:06'),
(156, 'SMP NEGERI 1 ENDE', '50305410', 'Jln. Kelimutu', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 154, NULL, '2026-08-21 09:00:06'),
(157, 'SMP NEGERI 1 ENDE SELATAN', '50302603', 'Jln. Teuku  Umar', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 155, NULL, '2026-08-21 09:00:06'),
(158, 'SMP NEGERI 1 MAUROLE', '50302602', 'Maurole', 'Maurole', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 156, NULL, '2026-08-21 09:00:06'),
(159, 'SMP NEGERI 1 NANGAPANDA', '50302601', 'NDetuzea', 'Ndeturea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 157, NULL, '2026-08-21 09:00:06'),
(160, 'SMP NEGERI 1 NDONA', '50302600', 'Lokoboko', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 158, NULL, '2026-08-21 09:00:06'),
(161, 'SMP NEGERI 1 WOLOWARU', '50302599', 'Jln. Melati Wolowaru', 'Bokasape', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 159, NULL, '2026-08-21 09:00:06'),
(162, 'SMP NEGERI 2 DETUSOKO', '50302598', 'Peibenga', 'Nggumbelaka', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 160, NULL, '2026-08-21 09:00:06'),
(163, 'SMP KATOLIK FRATERAN NDAO ENDE', '50302664', 'Jln. Imam Bonjol No. 39 Ende Kode Pos 86311', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 161, NULL, '2026-08-21 09:00:06'),
(164, 'SMP KATOLIK MARIA GORETI ENDE', '50302665', 'Jalan Wirajaya Ende', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 162, NULL, '2026-08-21 09:00:06'),
(165, 'SMP KATOLIK NAZARETH ENDE', '70062969', 'JL. ANGGREK, BTN RT 04 RW 05', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 163, NULL, '2026-08-21 09:00:06'),
(166, 'SMP KATOLIK ST. GABRIEL NDONA', '50302609', 'Dusun Tana Gadi', 'Nanganesa', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 164, NULL, '2026-08-21 09:00:06'),
(167, 'SMP KATOLIK ST. THERESIA NGGELA', '50305427', 'Jalan Gai Gadjo Nggela', 'Nggela', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 165, NULL, '2026-08-21 09:00:06'),
(168, 'SMP KATOLIK SWADAYA MAUKARO', '50302608', 'Jln Trans Flores Utara', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 166, NULL, '2026-08-21 09:00:06'),
(169, 'SMP KATOLIK WAWONATO', '50302662', 'KOMBANDARU', 'Riaraja', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 167, NULL, '2026-08-21 09:00:06'),
(170, 'TK IDHATA WOLOWARU', '50305512', 'Jln. Melati', 'Bokasape', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 168, NULL, '2026-08-21 09:00:06'),
(171, 'TK NEGERI PEMBINA KOTA BARU', '50306095', 'KOTA BARU', 'Kotabaru', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 169, NULL, '2026-08-21 09:00:06'),
(172, 'TK RHERHEJA 1', '50305442', 'ROWOREKE', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 170, NULL, '2026-08-21 09:00:06'),
(173, 'TK TINA BANI', '50305446', 'SADONUWA', 'Tinabani', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 171, NULL, '2026-08-21 09:00:06'),
(174, 'TK.SATAP WELAMOSA', '69845079', 'Jl.Trans Utara-Welamosa-Kec.Wewaria-Kab.Ende', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 172, NULL, '2026-08-21 09:00:06'),
(175, 'TKN PEMBINA ENDE', '50305463', 'JL. GATOT SUBROTO KM.04 RT.32 RW.16', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 173, NULL, '2026-08-21 09:00:06'),
(176, 'TK AETEKE', '69991344', 'Lelu - Lio Timur', 'Hobatuwa', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 174, NULL, '2026-08-21 09:00:06'),
(177, 'TK AMI WALISANGA ENDE', '70055076', 'Lingkungan Rukun Lima Atas RT 003 RW 006', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:06'),
(178, 'TK ARNOLDUS JANSEN', '69977490', 'Jl.Ende-Wolowaru, Dusun Detupau', 'Likanaka', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 175, NULL, '2026-08-21 09:00:06'),
(179, 'TK BELUT SAKTI', '69974571', 'Jl. Ende-Detusoko', 'Wolotolo Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 176, NULL, '2026-08-21 09:00:06'),
(180, 'TK CINTA ABADI', '69966364', 'Jl. Ende-Wewaria', 'AE NDOKO', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 177, NULL, '2026-08-21 09:00:06'),
(181, 'TK CINTA ANAK', '69845092', 'Tiwurande,Rt 03 RW 02', 'Tomberabu I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 178, NULL, '2026-08-21 09:00:06'),
(182, 'TK DEYNICA', '70027352', 'DETURAU', 'Fatamari', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 179, NULL, '2026-08-21 09:00:06'),
(183, 'TK EMBU TURU', '70036062', 'WOLONIO', 'Roa', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 180, NULL, '2026-08-21 09:00:06'),
(184, 'TK Harapan Baru', '69845103', 'Jl. RW. Monginsidi', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 181, NULL, '2026-08-21 09:00:06'),
(185, 'TK HARAPAN BUNDA', '70013338', 'Jalan Ikan Paus - Ende', 'Kel. Tanjung', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 182, NULL, '2026-08-21 09:00:06'),
(186, 'TK HARAPAN REDODORI', '69952674', 'Dusun Paribajo RT/RW. 008/004', 'Redodori', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 183, NULL, '2026-08-21 09:00:06'),
(187, 'TK JEO DUA', '69946026', 'DESA JEO DUA-KECAMATAN DETUKELI-ENDE', 'JEO DUA', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 184, NULL, '2026-08-21 09:00:06'),
(188, 'TK KARTINI PUUPAU', '50307733', 'PUUPAU', 'Penggajawa', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 185, NULL, '2026-08-21 09:00:06'),
(189, 'TK KI HAJAR DEWANTARA NUAMURI', '50305479', 'NUAMURI', 'Nuamuri', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 186, NULL, '2026-08-21 09:00:06'),
(190, 'TK KURU KELI', '70060114', 'JL. PANTURA RT 01 RW 01 DESA KOBALEBA', 'Kobaleba', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 187, NULL, '2026-08-21 09:00:06'),
(191, 'TK LOKADHIO', '69946017', 'JL. ENDE-NDORI-DESA KELISAMBA-KECAMATAN NDORI-ENDE', 'Kelisamba', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 188, NULL, '2026-08-21 09:00:06'),
(192, 'TK MARIA FATIMA KOANARA', '50305704', 'KOANARA', 'Koanara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 189, NULL, '2026-08-21 09:00:06'),
(193, 'TK MARIA VIRGO 2', '50305436', 'JL. WOLOARE A', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 190, NULL, '2026-08-21 09:00:06'),
(194, 'TK MBETE KAKI', '70041107', 'LOKALANDE', 'Tou', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 191, NULL, '2026-08-21 09:00:06'),
(195, 'TK Melati', '69845062', 'Jln Sam Ratulangi', 'Kel. Rewarangga Selatan', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 192, NULL, '2026-08-21 09:00:06'),
(196, 'TK MUHAMADIYAH ENDE', '70051200', 'JL. IKAN PAUS', 'Kel. Tanjung', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 193, NULL, '2026-08-21 09:00:06'),
(197, 'TK MUTIARA KASIH', '70026740', 'DUSUN OTORAJO', 'Ratewati', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 194, NULL, '2026-08-21 09:00:06'),
(198, 'TK NAZARETH', '70000043', 'Jl. Anggrek BTN', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 195, NULL, '2026-08-21 09:00:06'),
(199, 'TK PELITA HATI EMBU NGENA', '70003507', 'Desa Embu Ngena', 'EMBU NGENA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 196, NULL, '2026-08-21 09:00:06'),
(200, 'TK PENA RIA', '70035852', 'PAUPANDA DESA WEWARIA', 'Wewaria', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 197, NULL, '2026-08-21 09:00:06'),
(201, 'TK PENABUR ST. DOMINIC KAMUBHEKA', '70059694', 'JL. JURUSAN PUUKUNGU RT 003 RW 002', 'Kamubheka', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:06'),
(202, 'TK PUUKUNGU', '69945438', 'PUUKUNGU-DESA ONDOREA-KEC.NANGAPANDA-ENDE', 'Ondorea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 198, NULL, '2026-08-21 09:00:06'),
(203, 'TK Raburia', '69845091', 'Jl.Ende-Maumere', 'Raburia', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 199, NULL, '2026-08-21 09:00:06'),
(204, 'TK RANORAMBA', '69946067', 'JURUSAN ENDE-NANGABA, DESA RANORAMBA, KECAMATAN ENDE, KABUPATEN ENDE', 'RANORAMBA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 200, NULL, '2026-08-21 09:00:06'),
(205, 'TK Rera Wete', '69845088', 'Jl.Pantura', 'Nabe', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 201, NULL, '2026-08-21 09:00:06'),
(206, 'TK SANTO ANTONIUS WOLOORA', '70024355', 'DUSUN WOLOORA RT 001 RW 002', 'Tonggopapa', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 202, NULL, '2026-08-21 09:00:06'),
(207, 'TK Sare Pawe', '69845194', 'Jl.Rambutan', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 203, NULL, '2026-08-21 09:00:06'),
(208, 'TK SATAP NUSANGGALA', '50307730', 'Mulawatu Baru, Jl.Trans Utara Ende', 'Tou Timur', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:06'),
(209, 'TK SATU ATAP NDETUKUNE', '69750497', 'NDETUKUNE', 'Jegharangga', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 204, NULL, '2026-08-21 09:00:06'),
(210, 'TK St. AGUSTINUS', '69946025', 'JL. ENDE-ROGA, DESA NGGUWA-KECAMATAN NDONA TIMUR-ENDE', 'Ngguwa', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 205, NULL, '2026-08-21 09:00:06'),
(211, 'TK ST. MARTHA SOKORIA', '50305488', 'SOKORIA', 'Ranokolo', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 206, NULL, '2026-08-21 09:00:06'),
(212, 'TK ST. PAULUS NANGAKEO', '69734395', 'BHERAMARI', 'Bheramari', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 207, NULL, '2026-08-21 09:00:06'),
(213, 'TK ST. THEODORUS', '50305481', 'WATUNESO', 'Kel. Watuneso', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:07'),
(214, 'TK St. Theresia', '69845138', 'Dusun TOBA RT.10, RW.10', 'Roga', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 208, NULL, '2026-08-21 09:00:07'),
(215, 'TK ST. VINCENTIUS RATESUBA', '50305483', 'RATESUBA', 'Kobaleba', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 209, NULL, '2026-08-21 09:00:07'),
(216, 'TK TAMBORA', '69960988', 'Jl.Ende-Nangapanda', 'TANAZOZO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 210, NULL, '2026-08-21 09:00:07'),
(217, 'TK TANA NUWA', '50305478', 'NDUARIA', 'Nuamuri', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 211, NULL, '2026-08-21 09:00:07'),
(218, 'TK TANAROGA', '70036914', 'TANAROGA', 'TANAROGA', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 212, NULL, '2026-08-21 09:00:07'),
(219, 'TK TEKAD', '69845094', 'Jl.Ende-Maumere', 'Tomberabu I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 213, NULL, '2026-08-21 09:00:07'),
(220, 'TK TERPADU WOLOOJA 2', '70051845', 'JL. TRANS UTARA', 'WOLOOJA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 214, NULL, '2026-08-21 09:00:07'),
(221, 'TK TRINITAS OJA', '70014377', 'Dusun I Oja, RT 001, RW 001', 'Tendambepa', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 215, NULL, '2026-08-21 09:00:07'),
(222, 'TK YAPERTIF', '50305467', 'Jalan Sam Ratulangi', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 216, NULL, '2026-08-21 09:00:07'),
(223, 'TK. Bata Laki Wologai', '69845077', 'Wologai', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:07'),
(224, 'TK. MALAWARU', '69945985', 'RT.003/RW.002, DUSUN MALAWAU II, DESA MALAWARU-KEC.NANGAPANDA-ENDE', 'MALAWARU', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 217, NULL, '2026-08-21 09:00:07'),
(225, 'TK. OSOSOMBO LOKOBOKO', '69845067', 'WATUTORO.LOKOBOKO/NDONA/ENDE', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 218, NULL, '2026-08-21 09:00:07'),
(226, 'TK. Pertiwi Cab. Ende', '69845060', 'Jalan Ikan Paus', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 219, NULL, '2026-08-21 09:00:07'),
(227, 'TK. PUUMBARA', '69952709', 'Jl. Jurusan Ende-Bajawa', 'RATERUA', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 220, NULL, '2026-08-21 09:00:07'),
(228, 'TK. ROMAREA', '69945441', 'JL.NANGAMBOA-WATUMITE, DESA ROMAREA-KEC.NANGAPANDA-ENDE', 'ROMAREA', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 221, NULL, '2026-08-21 09:00:07'),
(229, 'TK. Salib Suci Maurole', '69845071', 'Jl.Pantura-Maurole', 'Maurole', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 222, NULL, '2026-08-21 09:00:07'),
(230, 'TK. SANTO MATHEUS AEMURI', '69972374', 'Jl. Trans Utara', 'Aemuri', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 223, NULL, '2026-08-21 09:00:07'),
(231, 'TK. SANTO YOSEPH AEISA', '69963992', 'Aeisa RT.05/RW.02', 'KEL. ROWORENA BARAT', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:00:07'),
(232, 'TK. SATAP KARTINI KELITEMBU', '69845081', 'KELITEMBU', 'Kelitembu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 224, NULL, '2026-08-21 09:00:07'),
(233, 'TK. SIGARIA', '69965824', 'Jl. Ende-Wologai', 'Wologai', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 225, NULL, '2026-08-21 09:00:07'),
(234, 'TK. ST. GETRUDIS KEBIRANGGA', '69975421', 'Jl. Ende-Maukaro', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 226, NULL, '2026-08-21 09:00:07'),
(235, 'TK. St. MARIA SATAP DETUBELA', '69845080', 'DETUBELA', 'Detubela', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 227, NULL, '2026-08-21 09:00:07'),
(236, 'TK. ST. MAXIMILLIANUS MARIA KOLBE RANGGATALO', '70000110', 'Jl. Raya Lintas Ende - Maumere', 'RANGGATALO', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 228, NULL, '2026-08-21 09:00:07'),
(237, 'TK. ST. PETRUS PUUKOU', '69845057', 'Jurusan Ende-Bajawa', 'Tendarea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 229, NULL, '2026-08-21 09:00:07'),
(238, 'TK. St. YOHANES EKOAE', '69953878', 'JL.TRANS UTARA', 'Ekoae', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 230, NULL, '2026-08-21 09:00:07'),
(239, 'TK. TAUSIA NDANGAKAPA', '69945439', 'JL.JURUSAN ENDE-NANGAMBO, DESA TENDA ONDO-KEC.NANGAPANDA-ENDE', 'TENDA ONDO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 231, NULL, '2026-08-21 09:00:07'),
(240, 'TK.KARTIKA VII-8 ENDE', '69845065', 'JL.KARTINI NO. 02', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 232, NULL, '2026-08-21 09:00:07'),
(241, 'TK.Kusuma Udayana 2', '69845064', 'Jl.Wirajaya-Kompleks Kompi C', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 233, NULL, '2026-08-21 09:00:07'),
(242, 'TK.SATAP ADE IRMA', '69845070', 'JL. JURUSAN LIANUNU - MAUBASA', 'Serandori', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 234, NULL, '2026-08-21 09:00:07'),
(243, 'TK.SATAP NDETUNDORA I', '69845059', 'Nuabosi-Ndetundora III-Ende', 'Ndetundora Iii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 235, NULL, '2026-08-21 09:00:07'),
(244, 'TK.Satap Raaweka', '69845078', 'Desa Mautenda Barat-Kec.Wewaria-kab.Ende', 'Mautenda Barat', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 236, NULL, '2026-08-21 09:00:07'),
(245, 'TK.ST.Bernadetha Wolomage', '69845074', 'Wolomage', 'Wolomage', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 237, NULL, '2026-08-21 09:00:07'),
(246, 'TK.ST.FRANSISKUS ASSISI', '69845073', 'Jl.Pantura-Desa Loboniki-Kec.Kotabaru-Kab.Ende', 'Loboniki', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 238, NULL, '2026-08-21 09:00:07'),
(247, 'TK.ST.PAULUS VI', '69845076', 'JL. ENDE-MAUMERE', 'Kel. Detusoko', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 239, NULL, '2026-08-21 09:00:07'),
(248, 'TK.ST.THERESIA WOLOFEO', '69845075', 'Jl. Ende-Maumere KM.28', 'Wolofeo', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 240, NULL, '2026-08-21 09:00:07'),
(249, 'TK.Yohanes Pemandi', '69845072', 'Jl.Pantura-Aewora-Maurole', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 241, NULL, '2026-08-21 09:00:07'),
(250, 'SD INPRES DETUBELO', '50302750', 'DETUBELO', 'Woloaro', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 242, NULL, '2026-08-21 09:03:49'),
(251, 'SD INPRES DETUENA', '50302749', 'DETUENA', 'DETUENA', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 243, NULL, '2026-08-21 09:03:49'),
(252, 'SD INPRES DETUETE', '50305655', 'Detuete', 'Ratewati', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 244, NULL, '2026-08-21 09:03:49'),
(253, 'SD INPRES DETUSOKO', '50302747', 'Detusoko', 'Kel. Detusoko', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 245, NULL, '2026-08-21 09:03:49'),
(254, 'SD INPRES DETUWIRA', '50305577', 'Detuwira', 'Wolotolo Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 246, NULL, '2026-08-21 09:03:49'),
(255, 'SD INPRES EKOLEA', '50302746', 'Ekolea', 'EKOLEA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 247, NULL, '2026-08-21 09:03:49'),
(256, 'SD INPRES EKOTARU', '50305710', 'Ekotaru', 'Wewaria', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 248, NULL, '2026-08-21 09:03:49'),
(257, 'SD INPRES ENDE 10', '50305549', 'JL. DEWI SARTIKA', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 249, NULL, '2026-08-21 09:03:49'),
(258, 'SD INPRES ENDE 11', '50302744', 'Jl. Perwira', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 232, NULL, '2026-08-21 09:03:49'),
(259, 'SD INPRES ENDE 12', '50305565', 'Jalan Imam Bonjol ,  Belakang Terminal Ndao - Ende', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 250, NULL, '2026-08-21 09:03:49'),
(260, 'SD INPRES ENDE 13', '50302743', 'JL. PATIMURA', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 251, NULL, '2026-08-21 09:03:49'),
(261, 'SD INPRES ENDE 14', '50302742', 'Jln. Gatot Subroto', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 252, NULL, '2026-08-21 09:03:49'),
(262, 'SD INPRES ENDE 15', '50305566', 'Jalan Perwira', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 253, NULL, '2026-08-21 09:03:49'),
(263, 'SD INPRES ENDE 16', '50302741', 'Jln. Aster I', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 254, NULL, '2026-08-21 09:03:49'),
(264, 'SD INPRES ENDE 7', '50302740', 'Jln. Gatot Subrotyo Ende', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 255, NULL, '2026-08-21 09:03:49'),
(265, 'SD INPRES ENDE 9', '50302739', 'Jln. Masjid', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 256, NULL, '2026-08-21 09:03:49'),
(266, 'SD INPRES FEORIA', '50302738', 'Feoria', 'Kebesani', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 257, NULL, '2026-08-21 09:03:49'),
(267, 'SD INPRES HOBAKUA', '50302717', 'Hobakua', 'Maubasa Timur', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 258, NULL, '2026-08-21 09:03:49'),
(268, 'SD INPRES ILIWODO 1', '50305646', 'Iliwodo', 'Serandori', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 259, NULL, '2026-08-21 09:03:49'),
(269, 'SD INPRES ILIWODO 2', '50305647', 'Maubasa', 'MAUBASA BARAT', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 260, NULL, '2026-08-21 09:03:49'),
(270, 'SD INPRES JOPU 4', '50305680', 'Kompleks Nirmala Dusun D Rt.14/rw.08', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 261, NULL, '2026-08-21 09:03:49'),
(271, 'SD INPRES JOPU 5', '50302716', 'Ranggase', 'Wolokoli', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 262, NULL, '2026-08-21 09:03:49'),
(272, 'SD INPRES KEKAKEU', '50302695', 'Kekakeu', 'Zozozea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 263, NULL, '2026-08-21 09:03:49'),
(273, 'SD INPRES KEKAWII', '50305584', 'Puuperi', 'Ndetundora I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 264, NULL, '2026-08-21 09:03:49'),
(274, 'SD INPRES KELITEMBU', '50302694', 'Kelitembu', 'Kelitembu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 265, NULL, '2026-08-21 09:03:49'),
(275, 'SD INPRES KOAGATA', '50302693', 'Koagata', 'Kelikiku', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 266, NULL, '2026-08-21 09:03:49'),
(276, 'SD INPRES KOAWENA', '50302692', 'Koawena', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 267, NULL, '2026-08-21 09:03:49'),
(277, 'SD INPRES KOLIKAPA', '50302691', 'Kolikapa', 'Kolikapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 268, NULL, '2026-08-21 09:03:49'),
(278, 'SD INPRES KOTABARU', '50302690', 'Kotabaru', 'Kotabaru', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 269, NULL, '2026-08-21 09:03:49'),
(279, 'SD INPRES KURUMBORO', '50302689', 'Kurumboro', 'Tiwutewa', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 270, NULL, '2026-08-21 09:03:49'),
(280, 'SD INPRES LEWAGARE', '50302687', 'Lewagare', 'Unggu', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 271, NULL, '2026-08-21 09:03:49'),
(281, 'SD INPRES LIANGGERE', '50302686', 'TOMBERABU II', 'Tomberabu Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 272, NULL, '2026-08-21 09:03:49'),
(282, 'SD INPRES LIGALEJO', '50302685', 'Boto', 'Rangalaka', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 273, NULL, '2026-08-21 09:03:49'),
(283, 'SD INPRES LOKOBOKO', '50302769', 'Jln. Jurusan Ende Lokoboko', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 274, NULL, '2026-08-21 09:03:49'),
(284, 'SD INPRES LOWOKETO', '50305594', 'Lowoketo', 'Rangalaka', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 275, NULL, '2026-08-21 09:03:49'),
(285, 'SD INPRES LOWORONGGA', '50302684', 'Loworongga', 'Nila', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 276, NULL, '2026-08-21 09:03:49'),
(286, 'SD INPRES MALAWARU', '50305531', 'Malawaru', 'MALAWARU', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 277, NULL, '2026-08-21 09:03:49'),
(287, 'SD INPRES MAUAU', '50302683', 'MAUAU', 'KAZO KAPO', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 34, NULL, '2026-08-21 09:03:49'),
(288, 'SD INPRES MAUROLE', '50302682', 'Maurole', 'Maurole', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 278, NULL, '2026-08-21 09:03:49'),
(289, 'SD INPRES MAURONGGA', '50302681', 'Maurongga', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 279, NULL, '2026-08-21 09:03:50'),
(290, 'SD INPRES MAUTENDA', '50302680', 'Aegana', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 280, NULL, '2026-08-21 09:03:50'),
(291, 'SD INPRES MBONGAWANI', '50305541', 'JLN. SLAMET RIYADI', 'Kel. Mbongawani', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 281, NULL, '2026-08-21 09:03:50'),
(292, 'SD INPRES MBOTUJITA', '50302679', 'Mbotujita', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 282, NULL, '2026-08-21 09:03:50'),
(293, 'SD INPRES MBUJALOO', '50305661', 'Mbujaloo', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 283, NULL, '2026-08-21 09:03:50');
INSERT INTO `sekolah` (`id`, `nama_sekolah`, `npsn`, `alamat`, `kelurahan`, `kecamatan`, `kabupaten`, `provinsi`, `kode_pos`, `kepala_sekolah`, `pengawas`, `created_at`) VALUES
(294, 'SD INPRES MBULILOO', '50302696', 'Mbuliloo', 'Mbuliloo', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 284, NULL, '2026-08-21 09:03:50'),
(295, 'SD INPRES METINUMBA 1', '50302697', 'RT 12/RW 06 Dusun Metinumba 3', 'Ndoriwoy', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 285, NULL, '2026-08-21 09:03:50'),
(296, 'SD INPRES METINUMBA 2', '50302698', 'Paribajo', 'Redodori', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 286, NULL, '2026-08-21 09:03:50'),
(297, 'SD INPRES MUNDINGGASA', '50302715', 'Mundinggasa', 'Mundinggasa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 287, NULL, '2026-08-21 09:03:50'),
(298, 'SD INPRES NANGANIO', '50302714', 'Nanganio', 'Watukamba', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 288, NULL, '2026-08-21 09:03:50'),
(299, 'SD INPRES NANGAPANDA 2', '50302713', 'Ndorurea / Nangapanda', 'Kel. Ndorurea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 289, NULL, '2026-08-21 09:03:50'),
(300, 'SD INPRES NANGAPANDA 3', '50302712', 'Nangapanda', 'Kel. Ndorurea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 290, NULL, '2026-08-21 09:03:50'),
(301, 'SD INPRES NDETUFEO', '50302710', 'Ndetufeo', 'Sanggarhorho', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 291, NULL, '2026-08-21 09:03:50'),
(302, 'SD INPRES NDETUNDORA 1', '50305707', 'Koponio', 'Ndetundora Iii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 292, NULL, '2026-08-21 09:03:50'),
(303, 'SD INPRES NDETUNDORA 2', '50305585', 'Kopowoa', 'Ndetundora Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 293, NULL, '2026-08-21 09:03:50'),
(304, 'SD INPRES NDETUWARU', '50302709', 'wozojaru.dusun ndetukedho.desa uzuzozo', 'UZU ZOZO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 294, NULL, '2026-08-21 09:03:50'),
(305, 'SD INPRES NDITO', '50302708', 'Desa Ndito, kecamatan Detusoko, kabupaten Ende, Propinsi Nusa Tenggara Timur', 'Ndito', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 295, NULL, '2026-08-21 09:03:50'),
(306, 'SD INPRES NDONA 3', '50305627', 'Radawuwu', 'Kel. Onelako', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 296, NULL, '2026-08-21 09:03:50'),
(307, 'SD INPRES NDONA 4', '50302707', 'Ndona', 'Kel. Onelako', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 297, NULL, '2026-08-21 09:03:50'),
(308, 'SD INPRES NGALUPOLO', '50305628', 'Ngalupolo', 'Ngalupolo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 298, NULL, '2026-08-21 09:03:50'),
(309, 'SD INPRES NGALUROGA', '50305629', 'NGALUROGA', 'Ngaluroga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 299, NULL, '2026-08-21 09:03:50'),
(310, 'SD INPRES NGGELA 2', '50305662', 'Nggela Rt.04/ Rw. 04', 'Nggela', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 300, NULL, '2026-08-21 09:03:50'),
(311, 'SD INPRES NGGEMO', '50302706', 'Nggemo Jln. Pantura Flores', 'Magekapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 301, NULL, '2026-08-21 09:03:50'),
(312, 'SD INPRES NIONIBA', '50302705', 'Maukaro', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 302, NULL, '2026-08-21 09:03:50'),
(313, 'SD INPRES NIOSANGGO', '50302704', 'Lewumbangga', 'Fataatu Timur', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 303, NULL, '2026-08-21 09:03:50'),
(314, 'SD INPRES NIRANUSA', '50305622', 'Mauwaru', 'NIRANUSA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 304, NULL, '2026-08-21 09:03:50'),
(315, 'SD INPRES NUAJA', '50302703', 'Doka', 'NUAJA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 305, NULL, '2026-08-21 09:03:50'),
(316, 'SD INPRES NUAMURI 2', '50302702', 'Detubu', 'Nuamuri Barat', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 306, NULL, '2026-08-21 09:03:50'),
(317, 'SD INPRES NUANAGA', '50302701', 'Nuanaga', 'NUANAGA', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 307, NULL, '2026-08-21 09:03:50'),
(318, 'SD INPRES NUAPU', '50305644', 'Nuapu', 'Roga', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 308, NULL, '2026-08-21 09:03:50'),
(319, 'SD INPRES NUATU', '50302699', 'Nuatu', 'Niramesi', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 309, NULL, '2026-08-21 09:03:50'),
(320, 'SD INPRES NUMBA 1', '50302677', 'Numba', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 310, NULL, '2026-08-21 09:03:50'),
(321, 'SD INPRES NUMBA 2', '50302797', 'Numba', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 311, NULL, '2026-08-21 09:03:50'),
(322, 'SD INPRES ONEKORE 3', '50302814', 'Jln. Hayam Wuruk', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 312, NULL, '2026-08-21 09:03:50'),
(323, 'SD INPRES ONEKORE 4', '50305546', 'Jln. Woloare A', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 313, NULL, '2026-08-21 09:03:50'),
(324, 'SD INPRES ONEKORE 5', '50305547', 'JL. PROF. W.Z. YOHANES', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 314, NULL, '2026-08-21 09:03:50'),
(325, 'SD INPRES ONEKORE 6', '50305548', 'Jln. Udayana', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 315, NULL, '2026-08-21 09:03:50'),
(326, 'SD INPRES OTOMBAMBA', '50302813', 'Jln. Jurusan Ndona', 'Nanganesa', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 316, NULL, '2026-08-21 09:03:50'),
(327, 'SD INPRES PANALATO', '50305595', 'Panalato', 'Tou Barat', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 317, NULL, '2026-08-21 09:03:50'),
(328, 'SD INPRES PASADOO', '50302812', 'PASADOO', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 318, NULL, '2026-08-21 09:03:50'),
(329, 'SD INPRES PAUPANDA 1', '50302811', 'Jln. Ikan Paus', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 319, NULL, '2026-08-21 09:03:50'),
(330, 'SD INPRES PAUPANDA 2', '50302810', 'Jln. Ikan Paus', 'Kel. Tanjung', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 320, NULL, '2026-08-21 09:03:50'),
(331, 'SD INPRES PAUPANDA 3', '50302809', 'Jln. Gunung  Ia', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 321, NULL, '2026-08-21 09:03:50'),
(332, 'SD INPRES PUUDHOMBO', '50302808', 'Puudhombo', 'Riaraja', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 322, NULL, '2026-08-21 09:03:50'),
(333, 'SD INPRES PUUKUNGU', '50302807', 'Aukapa', 'Ondorea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 323, NULL, '2026-08-21 09:03:50'),
(334, 'SD INPRES PUUPAU', '50302806', 'Puupau', 'Ndeturea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 324, NULL, '2026-08-21 09:03:50'),
(335, 'SD INPRES RAAWEKA', '50302805', 'Raaweka', 'Mautenda Barat', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 325, NULL, '2026-08-21 09:03:50'),
(336, 'SD INPRES RABURIA', '50302804', 'Raburia', 'Raburia', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 326, NULL, '2026-08-21 09:03:50'),
(337, 'SD INPRES RANGGATALO', '50305606', 'Ranggatalo', 'RANGGATALO', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 327, NULL, '2026-08-21 09:03:50'),
(338, 'SD INPRES RATESUBA', '50302802', 'Ratesuba', 'Kebirangga Tengah', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 328, NULL, '2026-08-21 09:03:50'),
(339, 'SD INPRES REDA', '50305586', 'Reda', 'WORHOPAPA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 329, NULL, '2026-08-21 09:03:50'),
(340, 'SD INPRES RENDOMAUPANDI', '50302801', 'Maupandi', 'Rendoraterua', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 330, NULL, '2026-08-21 09:03:50'),
(341, 'SD INPRES ROA', '50302800', 'JL. RAYA ENDE - MAUMERE KM. 21', 'Roa', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 331, NULL, '2026-08-21 09:03:50'),
(342, 'SD INPRES ROJA 2', '50305709', 'Jln. Ikan Tongkol', 'Kel. Tanjung', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 332, NULL, '2026-08-21 09:03:50'),
(343, 'SD INPRES ROJABAI', '50305596', 'Rojabai', 'Ndondo', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 333, NULL, '2026-08-21 09:03:50'),
(344, 'SD INPRES ROPA', '50302799', 'JALAN TRANS ROPA', 'Keliwumbu', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 334, NULL, '2026-08-21 09:03:50'),
(345, 'SD INPRES ROWORENA 2', '50302798', 'Woloare Rt.11 Rw.03', 'Kel. Roworena', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 335, NULL, '2026-08-21 09:03:50'),
(346, 'SD INPRES SOKOLOO', '50302816', 'SOKOLOO', 'Wologai Timur', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 336, NULL, '2026-08-21 09:03:50'),
(347, 'SD INPRES SOKORIA', '50302817', 'Sokoria', 'Ranokolo Selatan', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 337, NULL, '2026-08-21 09:03:50'),
(348, 'SD INPRES TANARHI', '50302828', 'TANARHI', 'TANAZOZO', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 338, NULL, '2026-08-21 09:03:50'),
(349, 'SD INPRES TETANDARA', '50302829', 'Jln. Nangka Ende', 'Kel. Kelimutu', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 339, NULL, '2026-08-21 09:03:50'),
(350, 'SD INPRES TIWEREA', '50305532', 'Tiwerea', 'Tiwe Rea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 340, NULL, '2026-08-21 09:03:50'),
(351, 'SD INPRES WAKA', '50302830', 'Waka', 'WAKA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 341, NULL, '2026-08-21 09:03:50'),
(352, 'SD INPRES WATUBEWA', '50302832', 'Watubewa', 'TANA LOO', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 342, NULL, '2026-08-21 09:03:50'),
(353, 'SD INPRES WATUJARA', '50302833', 'Jln. Kokos VIII Perumnas Ende', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 343, NULL, '2026-08-21 09:03:50'),
(354, 'SD INPRES WATUMESI', '50302834', 'Mausambi', 'Mausambi', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 344, NULL, '2026-08-21 09:03:50'),
(355, 'SD INPRES WATUMOTO', '50305663', 'Nuamulu', 'Nuamulu', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 345, NULL, '2026-08-21 09:03:50'),
(356, 'SD INPRES WELAMOSA', '50305656', 'Welamosa', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 346, NULL, '2026-08-21 09:03:50'),
(357, 'SD INPRES WEWARIA', '50302835', 'Wewaria', 'Wewaria', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 155, NULL, '2026-08-21 09:03:50'),
(358, 'SD INPRES WOLOARA', '50302827', 'Woloara', 'Woloara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 347, NULL, '2026-08-21 09:03:50'),
(359, 'SD INPRES WOLOGAI', '50302826', 'Wologai', 'Wologai', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 348, NULL, '2026-08-21 09:03:50'),
(360, 'SD INPRES WOLOJITA', '50302818', 'Wolojita', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 349, NULL, '2026-08-21 09:03:50'),
(361, 'SD INPRES WOLOKOLI', '50302819', 'Wolokoli', 'Fataatu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 350, NULL, '2026-08-21 09:03:50'),
(362, 'SD INPRES WOLOLA', '50302820', 'Wolola', 'Lise Kuru', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 351, NULL, '2026-08-21 09:03:50'),
(363, 'SD INPRES WOLOMAGE', '50305657', 'WOLOMAGE', 'AE NDOKO', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 352, NULL, '2026-08-21 09:03:50'),
(364, 'SD INPRES WOLOOJA 1', '50305681', 'Wolooja Rt.006  Rw.003', 'Mbuliwaralau Utara', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 353, NULL, '2026-08-21 09:03:50'),
(365, 'SD INPRES WOLOOJA 3', '50302822', 'NUADEMU', 'Mbuliwaralau', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 354, NULL, '2026-08-21 09:03:50'),
(366, 'SD INPRES WOLOTOPO', '50302823', 'Dusun Lia Nggo', 'Wolotopo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 355, NULL, '2026-08-21 09:03:50'),
(367, 'SD INPRES WOLOWARU 4', '50302825', 'Wolofeo', 'Nualise', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 356, NULL, '2026-08-21 09:03:50'),
(368, 'SD INPRES WOLOWARU 5', '50302796', 'Bokasape, Rt 08  Rw. 01', 'Bokasape', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 357, NULL, '2026-08-21 09:03:50'),
(369, 'SD INPRES WOLOWONA 1', '50302773', 'Jln. Sultan Hasanudin', 'Kel. Rewarangga Selatan', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 358, NULL, '2026-08-21 09:03:50'),
(370, 'SD INPRES WOLOWONA 2', '50302772', 'Jln. Sultan Hasanudin Ende - Flores - NTT', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 359, NULL, '2026-08-21 09:03:50'),
(371, 'SD INPRES WONDA', '50305650', 'Mboku  Ndori', 'RATEMANGGA', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 360, NULL, '2026-08-21 09:03:50'),
(372, 'SD INPRES WOROJA', '50302771', 'Woroja', 'MBOMBA', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 361, NULL, '2026-08-21 09:03:50'),
(373, 'SD INPRES WOROPAPA', '50305587', 'Woropapa', 'Mbotutenda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 362, NULL, '2026-08-21 09:03:50'),
(374, 'SD INPRES WUKARIA', '50305658', 'Wukaria', 'Aelipo', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 363, NULL, '2026-08-21 09:03:50'),
(375, 'SD KATOLIK AEBARA', '50305648', 'Aebara', 'Wonda', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 364, NULL, '2026-08-21 09:03:50'),
(376, 'SD KATOLIK AEFEO', '50305591', 'Aefeo', 'Tomberabu I', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 365, NULL, '2026-08-21 09:03:50'),
(377, 'SD KATOLIK AEISA', '50305689', 'Jln. Woloare  B', 'KEL. ROWORENA BARAT', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 366, NULL, '2026-08-21 09:03:50'),
(378, 'SD KATOLIK AEKORO', '50302545', 'AEKORO', 'RANDORAMA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 367, NULL, '2026-08-21 09:03:50'),
(379, 'SD KATOLIK AEWORA', '50302767', 'Jalan Trans Utara Flores', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 368, NULL, '2026-08-21 09:03:50'),
(380, 'SD KATOLIK ANARANDA', '50305660', 'Anaranda', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 369, NULL, '2026-08-21 09:03:50'),
(381, 'SD KATOLIK ASE', '50305608', 'Ase, RT 10 RW 05', 'Kel. Watuneso', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 370, NULL, '2026-08-21 09:03:50'),
(382, 'SD KATOLIK BOAFEO', '50302765', 'Boafeo', 'Boafeo', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 371, NULL, '2026-08-21 09:03:50'),
(383, 'SD KATOLIK BUUBEI', '50302764', 'Buubei', 'Tinabani', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 372, NULL, '2026-08-21 09:03:50'),
(384, 'SD KATOLIK BUUNGENDA', '50302718', 'Buungenda', 'Golulada', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 373, NULL, '2026-08-21 09:03:50'),
(385, 'SD KATOLIK DEDU', '50302763', 'Dedu', 'Kelikiku', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 374, NULL, '2026-08-21 09:03:50'),
(386, 'SD KATOLIK DETUARA', '50305599', 'Detuara', 'Detuara', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 375, NULL, '2026-08-21 09:03:50'),
(387, 'SD KATOLIK DETUBELA 1', '50302762', 'Detubela', 'Detubela', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 376, NULL, '2026-08-21 09:03:50'),
(388, 'SD KATOLIK DETUDENU', '50305601', 'Tiwu Sora', 'Detuara', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 377, NULL, '2026-08-21 09:03:50'),
(389, 'SD KATOLIK DETUELU', '50305578', 'Detuelu', 'Mukureku', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 378, NULL, '2026-08-21 09:03:50'),
(390, 'SD KATOLIK DETUKOU', '50302761', 'Lokalande', 'Tou', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 379, NULL, '2026-08-21 09:03:50'),
(391, 'SD KATOLIK DETUMBAWA', '50302760', 'Jalan Jurusan Timur  Km.10 Ende - Maumere', 'Ndungga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 380, NULL, '2026-08-21 09:03:50'),
(392, 'SD KATOLIK DETUMBEWA', '50302759', 'Detumbewa', 'Detumbewa', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 381, NULL, '2026-08-21 09:03:50'),
(393, 'SD KATOLIK DETUPERA', '50305609', 'Detupera', 'Fatamari', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 382, NULL, '2026-08-21 09:03:50'),
(394, 'SD KATOLIK DETUWULU', '50302758', 'Detuwulu', 'Detuwulu', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 383, NULL, '2026-08-21 09:03:50'),
(395, 'SD KATOLIK DILE', '50302757', 'Dile', 'Dile', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 384, NULL, '2026-08-21 09:03:50'),
(396, 'SD KATOLIK EKOAE', '50305711', 'Jl. Trans Utara (Desa Ekoae)', 'Ekoae', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 385, NULL, '2026-08-21 09:03:50'),
(397, 'SD KATOLIK EKOLETA', '50305698', 'Ekoleta', 'Wologai', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 386, NULL, '2026-08-21 09:03:50'),
(398, 'SD KATOLIK ENDE 8', '50302795', 'Jln. Garuda', 'Kel. Kelimutu', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 387, NULL, '2026-08-21 09:03:50'),
(399, 'SD KATOLIK FENDO', '50305610', 'Lia Beke', 'LIABEKE', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 388, NULL, '2026-08-21 09:03:51'),
(400, 'SD KATOLIK FUNGAPANDA', '50302794', 'Fungapanda', 'JEO DUA', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 389, NULL, '2026-08-21 09:03:51'),
(401, 'SD KATOLIK GANA', '50305611', 'Ratelae - Gana', 'BU TANALAGU', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 390, NULL, '2026-08-21 09:03:51'),
(402, 'SD KATOLIK GHAIBHABHA', '50305575', 'GAIBHABHA', 'Kebesani', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 391, NULL, '2026-08-21 09:03:51'),
(403, 'SD KATOLIK HANGALANDE', '50302793', 'Hangalande', 'Hangalande', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 392, NULL, '2026-08-21 09:03:51'),
(404, 'SD KATOLIK JOGE', '50302792', 'WELAMBASA', 'WOLOAU', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 393, NULL, '2026-08-21 09:03:51'),
(405, 'SD KATOLIK JOPU 1', '50302791', 'Jopu', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 394, NULL, '2026-08-21 09:03:51'),
(406, 'SD KATOLIK JOPU 2', '50305683', 'Jopu', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 395, NULL, '2026-08-21 09:03:51'),
(407, 'SD KATOLIK JOPU 3', '50305684', 'Wolokoli', 'Wolokoli', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 396, NULL, '2026-08-21 09:03:51'),
(408, 'SD KATOLIK KAMUBHEKA', '50305618', 'Sdk  Kamubheka', 'Kamubheka', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 397, NULL, '2026-08-21 09:03:51'),
(409, 'SD KATOLIK KANGANARA', '50302790', 'Kanganara', 'Kanganara', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 398, NULL, '2026-08-21 09:03:51'),
(410, 'SD KATOLIK KEDO', '50302788', 'Kedo Mudetelo', 'Kuru', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 399, NULL, '2026-08-21 09:03:51'),
(411, 'SD KATOLIK KEDOGAJA', '50302787', 'Keriselo', 'Ndenggarongge', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 400, NULL, '2026-08-21 09:03:51'),
(412, 'SD KATOLIK KEKADORI', '50302786', 'Kekadori', 'Rapowawo', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 401, NULL, '2026-08-21 09:03:51'),
(413, 'SD KATOLIK KEKAJODHO', '50302785', 'KEKAJODHO', 'Uzuramba', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 402, NULL, '2026-08-21 09:03:51'),
(414, 'SD KATOLIK KEKANDERE 1', '50302784', 'Rapowawo', 'Rapowawo', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 403, NULL, '2026-08-21 09:03:51'),
(415, 'SD KATOLIK KEKANDERE 2', '50302783', 'Kekandere', 'KEKANDERE', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 404, NULL, '2026-08-21 09:03:51'),
(416, 'SD KATOLIK KEKASEWA', '50302782', 'Kekasewa', 'Kekasewa', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 405, NULL, '2026-08-21 09:03:51'),
(417, 'SD KATOLIK KEKAWII', '50302781', 'Kekawii', 'Randotonda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 406, NULL, '2026-08-21 09:03:51'),
(418, 'SD KATOLIK KOANARA', '50302780', 'Koanara', 'Koanara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 407, NULL, '2026-08-21 09:03:51'),
(419, 'SD KATOLIK KOMBANDARU', '50302779', 'Kombandaru', 'Riaraja', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 408, NULL, '2026-08-21 09:03:51'),
(420, 'SD KATOLIK KOMBO', '50302778', 'Tendaleo', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 409, NULL, '2026-08-21 09:03:51'),
(421, 'SD KATOLIK KURULIMBU', '50305645', 'Kurulimbu', 'Kurulimbu', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 410, NULL, '2026-08-21 09:03:51'),
(422, 'SD KATOLIK LAINILA', '50302756', 'Nila', 'Nila', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 411, NULL, '2026-08-21 09:03:51'),
(423, 'SD KATOLIK LANDOKURA', '50305637', 'Landokura', 'Kurulimbu Selatan', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 412, NULL, '2026-08-21 09:03:51'),
(424, 'SD KATOLIK LIAKAMBA', '50302676', 'Tendawawo', 'Tenda', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 413, NULL, '2026-08-21 09:03:51'),
(425, 'SD KATOLIK LIKANAKA', '50302596', 'Boa Likanaka', 'Likanaka', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 414, NULL, '2026-08-21 09:03:51'),
(426, 'SD KATOLIK LOBONIKI', '50305602', 'Loboniki', 'Loboniki', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 415, NULL, '2026-08-21 09:03:51'),
(427, 'SD KATOLIK LOKAOJA', '50305603', 'Lokaoja', 'Liselande', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 416, NULL, '2026-08-21 09:03:51'),
(428, 'SD KATOLIK LOKOBOKO', '50302575', 'Lokoboko', 'Kel. Lokoboko', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 417, NULL, '2026-08-21 09:03:51'),
(429, 'SD KATOLIK MAGEKOBA', '50302574', 'Detukeli', 'Detukeli', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 418, NULL, '2026-08-21 09:03:51'),
(430, 'SD KATOLIK MAGENGURA', '50302573', 'Magengura', 'EMBU NGENA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 419, NULL, '2026-08-21 09:03:51'),
(431, 'SD KATOLIK MARSUDIRINI', '50302572', 'Detusoko', 'Detusoko Barat', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 420, NULL, '2026-08-21 09:03:51'),
(432, 'SD KATOLIK MAUKARO', '50305619', 'Maukaro', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 421, NULL, '2026-08-21 09:03:51'),
(433, 'SD KATOLIK MBAKAONDO', '50302571', 'Mbakaondo', 'Kebirangga Selatan', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 422, NULL, '2026-08-21 09:03:51'),
(434, 'SD KATOLIK MBOMBA', '50302570', 'Mbomba', 'Gheoghoma', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 423, NULL, '2026-08-21 09:03:51'),
(435, 'SD KATOLIK MONDO', '50302569', 'Mondo', 'Ngaluroga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 424, NULL, '2026-08-21 09:03:51'),
(436, 'SD KATOLIK MUKUSAKI', '50302568', 'Mukusaki', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 425, NULL, '2026-08-21 09:03:51'),
(437, 'SD KATOLIK NABE', '50302647', 'Jln. Pantura', 'Nabe', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 426, NULL, '2026-08-21 09:03:51'),
(438, 'SD KATOLIK NANGAKEO', '50302565', 'Nangakeo', 'Bheramari', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 427, NULL, '2026-08-21 09:03:51'),
(439, 'SD KATOLIK NANGAMBOA', '50302564', 'Nangamboa', 'Ondorea Barat', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 428, NULL, '2026-08-21 09:03:51'),
(440, 'SD KATOLIK NANGAPANDA 1', '50302563', 'Nangapanda', 'Ndorurea I', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 429, NULL, '2026-08-21 09:03:51'),
(441, 'SD KATOLIK NAZARETH ENDE', '70005989', 'Jl. Anggrek, BTN Ende', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 430, NULL, '2026-08-21 09:03:51'),
(442, 'SD KATOLIK NDETUKUNE', '50302562', 'Ndetukune', 'Jegharangga', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 431, NULL, '2026-08-21 09:03:51'),
(443, 'SD KATOLIK NDONA 1', '50302561', 'Ndona', 'Nanganesa', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 432, NULL, '2026-08-21 09:03:51'),
(444, 'SD KATOLIK NDONA 2', '50302540', 'Jln. Jurusan Ende-ndona', 'Kel. Onelako', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 433, NULL, '2026-08-21 09:03:51'),
(445, 'SD KATOLIK NDUARIA', '50302560', 'Nduaria', 'Nduaria', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 434, NULL, '2026-08-21 09:03:51'),
(446, 'SD KATOLIK NGALUPOLO', '50305631', 'Ngalupolo', 'Ngalupolo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 435, NULL, '2026-08-21 09:03:51'),
(447, 'SD KATOLIK NGEBONDANA', '50305612', 'NGEBONDANA', 'NUALIMA', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 436, NULL, '2026-08-21 09:03:51'),
(448, 'SD KATOLIK NGGELA 1', '50302559', 'Nggela', 'Nggela', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 437, NULL, '2026-08-21 09:03:51'),
(449, 'SD KATOLIK NGGESADETU', '50302558', 'Nggesadetu', 'Nggesa', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 438, NULL, '2026-08-21 09:03:51'),
(450, 'SD KATOLIK NIDA', '50302576', 'NIDA', 'Watunggere', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 439, NULL, '2026-08-21 09:03:51'),
(451, 'SD KATOLIK NIOPANDA', '50305604', 'Niopanda', 'Niopanda', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 440, NULL, '2026-08-21 09:03:51'),
(452, 'SD KATOLIK NIRANANGA', '50302577', 'Nirananga', 'Zozozea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 441, NULL, '2026-08-21 09:03:51'),
(453, 'SD KATOLIK NUABOSI', '50302578', 'Ndetundora  II', 'Ndetundora Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 442, NULL, '2026-08-21 09:03:51'),
(454, 'SD KATOLIK NUAMULU', '50305665', 'Desa Nuamulu', 'Nuamulu', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 443, NULL, '2026-08-21 09:03:51'),
(455, 'SD KATOLIK NUAMURI 1', '50302595', 'Nuamuri', 'Nuamuri', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 444, NULL, '2026-08-21 09:03:51'),
(456, 'SD KATOLIK NUAULU', '50302594', 'Nuaulu', 'LISE PUU', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 445, NULL, '2026-08-21 09:03:51'),
(457, 'SD KATOLIK NUAWIKA', '50305605', 'Nuawika', 'Taniwoda', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 446, NULL, '2026-08-21 09:03:51'),
(458, 'SD KATOLIK NUMBA', '50305579', 'Numba', 'Numba', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 447, NULL, '2026-08-21 09:03:51'),
(459, 'SD KATOLIK OKA', '50302593', 'Oka', 'Niramesi', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 448, NULL, '2026-08-21 09:03:51'),
(460, 'SD KATOLIK ONEKORE 1', '50305560', 'JL. HAYAM WURUK', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 449, NULL, '2026-08-21 09:03:51'),
(461, 'SD KATOLIK ONEKORE 2', '50305561', 'Jln. Wirajaya No. 3', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 450, NULL, '2026-08-21 09:03:51'),
(462, 'SD KATOLIK PAAPINGGA', '50305638', 'RATEDANGA', 'DEMULAKA', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 451, NULL, '2026-08-21 09:03:51'),
(463, 'SD KATOLIK PANAMATA', '50305593', 'Panamata', 'Wolokaro', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 452, NULL, '2026-08-21 09:03:51'),
(464, 'SD KATOLIK PAUMERE', '50305536', 'Arawea', 'Kerirea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 453, NULL, '2026-08-21 09:03:51'),
(465, 'SD KATOLIK PAUPIRE', '50302592', 'Jln. Prof.dr.wz Yohanes', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 454, NULL, '2026-08-21 09:03:51'),
(466, 'SD KATOLIK PEIBENGA', '50302591', 'Peibenga', 'Nggumbelaka', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 455, NULL, '2026-08-21 09:03:51'),
(467, 'SD KATOLIK PEMO 1', '50302590', 'Pemo', 'Pemo', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 456, NULL, '2026-08-21 09:03:51'),
(468, 'SD KATOLIK PEMO 2', '50302589', 'Mbangga', 'Rindiwawo', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 457, NULL, '2026-08-21 09:03:51'),
(469, 'SD KATOLIK PISA TANAAU', '50305576', 'Pisa Tana Au', 'Ndikosapu', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 458, NULL, '2026-08-21 09:03:51'),
(470, 'SD KATOLIK PISE', '50302588', 'Pise', 'PISE', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 459, NULL, '2026-08-21 09:03:51'),
(471, 'SD KATOLIK PISOMBOPO', '50302587', 'Worhomboa', 'Sanggarhorho', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 460, NULL, '2026-08-21 09:03:51'),
(472, 'SD KATOLIK PORA', '50305667', 'Desa Pora, Kecamatan Wolojita, kabupaten Ende, NTT', 'Pora', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 461, NULL, '2026-08-21 09:03:51'),
(473, 'SD KATOLIK PUUBHETO', '50302586', 'Puubheto', 'Rukuramba', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 462, NULL, '2026-08-21 09:03:51'),
(474, 'SD KATOLIK PUUFEO', '50305571', 'Jln. Woloare B', 'Kel. Roworena', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 463, NULL, '2026-08-21 09:03:51'),
(475, 'SD KATOLIK PUUKOU', '50302585', 'Puukou', 'Tendarea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 464, NULL, '2026-08-21 09:03:51'),
(476, 'SD KATOLIK PUUTUGA', '50305632', 'Puutuga', 'Puutuga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 465, NULL, '2026-08-21 09:03:51'),
(477, 'SD KATOLIK RANGA', '50305580', 'Desa Ranga, Kec Detusoko, Kab. Ende', 'Ranga', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 466, NULL, '2026-08-21 09:03:51'),
(478, 'SD KATOLIK RANOKOLO', '50305623', 'Ranokolo', 'Ranokolo', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 467, NULL, '2026-08-21 09:03:51'),
(479, 'SD KATOLIK RATEMBUE', '50302581', 'Ratembue', 'Mbuliloo', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 468, NULL, '2026-08-21 09:03:51'),
(480, 'SD KATOLIK RATERORU', '50302580', 'RATERORU', 'Rateroru', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 469, NULL, '2026-08-21 09:03:51'),
(481, 'SD KATOLIK REKA', '50305633', 'Reka', 'Reka', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 470, NULL, '2026-08-21 09:03:51'),
(482, 'SD KATOLIK ROGA', '50305639', 'Roga', 'Roga', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 471, NULL, '2026-08-21 09:03:51'),
(483, 'SD KATOLIK ROWOREKE 1', '50302579', 'Jln. Sultan Hasanudin', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 472, NULL, '2026-08-21 09:03:51'),
(484, 'SD KATOLIK ROWOREKE 2', '50302557', 'Jln. Sultan Hasanudin - Ende - Flores - NTT', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 473, NULL, '2026-08-21 09:03:51'),
(485, 'SD KATOLIK SAGA', '50305581', 'Dusun Saga I', 'Saga', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 474, NULL, '2026-08-21 09:03:51'),
(486, 'SD KATOLIK SEULAKO', '50302675', 'Seulako', 'Ngguwa', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 475, NULL, '2026-08-21 09:03:51'),
(487, 'SD KATOLIK SOKORIA 1', '50305641', 'Detuboti', 'Sokoria', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 476, NULL, '2026-08-21 09:03:51'),
(488, 'SD KATOLIK SOKORIA 2', '50305642', 'Sokoria', 'Sokoria', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 477, NULL, '2026-08-21 09:03:51'),
(489, 'SD KATOLIK ST AMBROSIUS ENDE 6', '50305573', 'Jln. Perwira', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 478, NULL, '2026-08-21 09:03:51'),
(490, 'SD KATOLIK ST ANTONIUS ENDE 2', '50302775', 'JL. YOS SUDARSO', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 479, NULL, '2026-08-21 09:03:51'),
(491, 'SD KATOLIK ST THERESIA ENDE 3', '50302776', 'Jln. Kelimutu No. 12', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 480, NULL, '2026-08-21 09:03:51'),
(492, 'SD KATOLIK TANAJEA', '50302528', 'Marakoja', 'Tiwe Rea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 481, NULL, '2026-08-21 09:03:51'),
(493, 'SD KATOLIK TENDA', '50302529', 'Tenda', 'Tenda', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 482, NULL, '2026-08-21 09:03:51'),
(494, 'SD KATOLIK TOBA', '50305721', 'Toba', 'Roga', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 483, NULL, '2026-08-21 09:03:51'),
(495, 'SD KATOLIK WAGA', '50305668', 'Dusun Waga', 'Pora', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 406, NULL, '2026-08-21 09:03:51'),
(496, 'SD KATOLIK WAKA', '50302530', 'Aemuri', 'Aemuri', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 484, NULL, '2026-08-21 09:03:51'),
(497, 'SD KATOLIK WATUKAMBA', '50302531', 'Watukamba', 'Otogedu', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 485, NULL, '2026-08-21 09:03:51'),
(498, 'SD KATOLIK WATUMITE', '50302532', 'Watumite Rt 01 Rw 04', 'Watumite', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 486, NULL, '2026-08-21 09:03:51'),
(499, 'SD KATOLIK WATUNESO', '50305613', 'Watuneso Wena', 'Kel. Watuneso', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 487, NULL, '2026-08-21 09:03:52'),
(500, 'SD KATOLIK WATUNGGERE', '50302534', 'Watunggere', 'Watunggere Marilonga', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 488, NULL, '2026-08-21 09:03:52'),
(501, 'SD KATOLIK WATURAKA', '50302526', 'Waturaka', 'WATURAKA', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 489, NULL, '2026-08-21 09:03:52'),
(502, 'SD KATOLIK WATUSIPI', '50302525', 'Watusipi', 'Watusipi', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 490, NULL, '2026-08-21 09:03:52'),
(503, 'SD KATOLIK WELAMOSA', '50302517', 'Welamosa', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 491, NULL, '2026-08-21 09:03:52'),
(504, 'SD KATOLIK WOLOBHETO', '50305614', 'WOLOBHETO', 'MBEWAWORA', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 492, NULL, '2026-08-21 09:03:52'),
(505, 'SD KATOLIK WOLOFEO', '50302519', 'Wolofeo', 'Wolofeo', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 493, NULL, '2026-08-21 09:03:52'),
(506, 'SD KATOLIK WOLOGAI DETUSOKO', '50305716', 'Wologai', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 494, NULL, '2026-08-21 09:03:52'),
(507, 'SD KATOLIK WOLOGAI ENDE', '50305589', 'Mbani', 'WOLOGAI DUA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 495, NULL, '2026-08-21 09:03:52'),
(508, 'SD KATOLIK WOLOGERU', '50302523', 'Kuru', 'Randoria', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 496, NULL, '2026-08-21 09:03:52'),
(509, 'SD KATOLIK WOLOJITA', '50302524', 'Wolojita', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 497, NULL, '2026-08-21 09:03:52'),
(510, 'SD KATOLIK WOLOKOTA', '50305634', 'Wolokota', 'Wolokota', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 498, NULL, '2026-08-21 09:03:52'),
(511, 'SD KATOLIK WOLOLANU', '50305669', 'Wololanu', 'Nuamulu', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 499, NULL, '2026-08-21 09:03:52'),
(512, 'SD KATOLIK WOLOLELE A', '50305615', 'WOLOLELE A', 'Wololele A', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 500, NULL, '2026-08-21 09:03:52'),
(513, 'SD KATOLIK WOLOLELE B', '50302535', 'Wololele  B', 'Liselowobora', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 501, NULL, '2026-08-21 09:03:52'),
(514, 'SD KATOLIK WOLOMAGE', '50302536', 'Wolomage', 'Wolomage', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 502, NULL, '2026-08-21 09:03:52'),
(515, 'SD Katolik Wolomota', '50305616', 'Wolomota', 'TANAROGA', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 503, NULL, '2026-08-21 09:03:52'),
(516, 'SD KATOLIK WOLOMUKU', '50302554', 'Wolomuku', 'Wolomuku', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 504, NULL, '2026-08-21 09:03:52'),
(517, 'SD KATOLIK WOLONDOPO 1', '50302552', 'Detupau', 'Likanaka', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 505, NULL, '2026-08-21 09:03:52'),
(518, 'SD KATOLIK WOLONDOPO 2', '50302551', 'Wolojita', 'Nuaone', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 506, NULL, '2026-08-21 09:03:52'),
(519, 'SD KATOLIK WOLOORA', '50305592', 'Woloora', 'Tonggopapa', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 507, NULL, '2026-08-21 09:03:52'),
(520, 'SD KATOLIK WOLOSAMBI', '50305617', 'JITAPANDA', 'Wolosambi', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 508, NULL, '2026-08-21 09:03:52'),
(521, 'SD KATOLIK WOLOSOKO', '50302550', 'RT 003 / RW 003', 'Wolosoko', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 509, NULL, '2026-08-21 09:03:52'),
(522, 'SD KATOLIK WOLOTOLO', '50305582', 'Wolotolo', 'Wolotolo', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 510, NULL, '2026-08-21 09:03:52'),
(523, 'SD KATOLIK WOLOTOPO 1', '50302549', 'Dusun Wolosambi', 'Wolotopo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 511, NULL, '2026-08-21 09:03:52'),
(524, 'SD KATOLIK WOLOTOPO 2', '50302648', 'Wolotopo', 'Wolotopo Timur', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 512, NULL, '2026-08-21 09:03:52'),
(525, 'SD KATOLIK WOLOWARU 1', '50302548', 'Jln. Kelimutu Wolowaru', 'Bokasape', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 513, NULL, '2026-08-21 09:03:52'),
(526, 'SD KATOLIK WOLOWARU 2', '50302547', 'NAPUWAKA - LISEDETU - WOLOWARU', 'Lisedetu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 514, NULL, '2026-08-21 09:03:52'),
(527, 'SD KATOLIK WOLOWUSU', '50305635', 'Wolowusu', 'Nila', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 515, NULL, '2026-08-21 09:03:52'),
(528, 'SD KATOLIK WONDA', '50302546', 'Wonda', 'Wonda', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 516, NULL, '2026-08-21 09:03:52'),
(529, 'SD KATOLIK WOROMBERA', '50305590', 'Worombera', 'Nakuramba', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 517, NULL, '2026-08-21 09:03:52'),
(530, 'SD NEGERI ANAREWA', '50302646', 'AEJETI', 'Aejeti', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 518, NULL, '2026-08-21 09:03:52'),
(531, 'SD NEGERI DETUBELA 2', '50305597', 'Paubewa', 'Tanalangi', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 519, NULL, '2026-08-21 09:03:52'),
(532, 'SD NEGERI EKOREKO', '50302645', 'Ekoreko', 'Rorurangga', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 520, NULL, '2026-08-21 09:03:52'),
(533, 'SD NEGERI ENDE 1', '50305568', 'Jln. Mesjid', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 521, NULL, '2026-08-21 09:03:52'),
(534, 'SD NEGERI ENDE 5', '50302644', 'JL. DEWI SARTIKA', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 522, NULL, '2026-08-21 09:03:52'),
(535, 'SD NEGERI IPI', '50305543', 'Jln. Ih Doko', 'Kel. Tetandara', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 523, NULL, '2026-08-21 09:03:52'),
(536, 'SD NEGERI KEDEBODU', '69734390', 'KEDEBODU', 'Kedebodu', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 524, NULL, '2026-08-21 09:03:52'),
(537, 'SD NEGERI KEDOBORO', '50302643', 'KEDOBORO', 'Maurole', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 525, NULL, '2026-08-21 09:03:52'),
(538, 'SD NEGERI KOBALEBA', '50305620', 'Maukaro', 'Kobaleba', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 526, NULL, '2026-08-21 09:03:52'),
(539, 'SD NEGERI KURUPOKE', '50305574', 'Detukeli', 'Detukeli', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 527, NULL, '2026-08-21 09:03:52'),
(540, 'SD NEGERI LELU', '50302641', 'Hobatuwa', 'Hobatuwa', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 528, NULL, '2026-08-21 09:03:52'),
(541, 'SD NEGERI MALAARA', '50305533', 'Malaara', 'ROMAREA', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 529, NULL, '2026-08-21 09:03:52'),
(542, 'SD NEGERI MARANUA', '50305588', 'Maranua', 'Jamokeasa', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 530, NULL, '2026-08-21 09:03:52'),
(543, 'SD NEGERI MAUNGGORA', '50305722', 'MAUNGGORA', 'Nggorea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 531, NULL, '2026-08-21 09:03:52'),
(544, 'SD NEGERI MOKEASA', '50302640', 'Pemo', 'Mbotutenda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 532, NULL, '2026-08-21 09:03:52'),
(545, 'SD NEGERI MOLEKELISAMBA', '50305651', 'Molekelisamba', 'MOLE', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 533, NULL, '2026-08-21 09:03:52'),
(546, 'SD NEGERI MOLETEBOSAMA', '50302639', 'Wolomapa', 'Bokasape Timur', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 534, NULL, '2026-08-21 09:03:52'),
(547, 'SD NEGERI MOLUTANGGA', '50308517', 'MOLUTANGGA', 'RATEWATI SELATAN', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 535, NULL, '2026-08-21 09:03:52'),
(548, 'SD NEGERI NUSANGGALA', '50302638', 'Mulawatu Baru', 'Tou Timur', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 536, NULL, '2026-08-21 09:03:52'),
(549, 'SD NEGERI OJA', '50305534', 'OJA', 'Tendambepa', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 537, NULL, '2026-08-21 09:03:52'),
(550, 'SD NEGERI PUUTARA', '50305653', 'Puutara', 'Puutara', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 538, NULL, '2026-08-21 09:03:52'),
(551, 'SD NEGERI RATENGGOJI', '50305598', 'Ratenggoji', 'Taniwoda', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 539, NULL, '2026-08-21 09:03:52'),
(552, 'SD NEGERI ROJA 1', '50302637', 'Jln. Teuku Umar', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 540, NULL, '2026-08-21 09:03:52'),
(553, 'SD NEGERI ROJA 3', '50305544', 'ARUBARA', 'Kel. Tetandara', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 541, NULL, '2026-08-21 09:03:52'),
(554, 'SD NEGERI ROJA 6', '50302654', 'Jln. Teuku Umar', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 542, NULL, '2026-08-21 09:03:52'),
(555, 'SD NEGERI RUTU JEJA', '69965710', 'WOLOFAI', 'Rutu Jeja', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 543, NULL, '2026-08-21 09:03:52'),
(556, 'SD NEGERI SARELAKA', '50308518', 'KURUSARE', 'Kuru Sare', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 544, NULL, '2026-08-21 09:03:52'),
(557, 'SD NEGERI SOGOROGA', '50308519', 'SOGOROGA', 'WAWONATO', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 545, NULL, '2026-08-21 09:03:52'),
(558, 'SD NEGERI TURUNALU', '50305583', 'TURUNALU', 'Turunalu', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 546, NULL, '2026-08-21 09:03:52'),
(559, 'SD NEGERI UMANUBA', '50305535', 'Tendambhera', 'MBOBHENGA', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 547, NULL, '2026-08-21 09:03:52'),
(560, 'SD NEGERI WATUBARA', '50302831', 'Watubara', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 548, NULL, '2026-08-21 09:03:52'),
(561, 'SD NEGERI WIWIPEMO', '50305664', 'Wiwipemo', 'Wiwipemo', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 549, NULL, '2026-08-21 09:03:52'),
(562, 'SD NEGERI WOIMITE', '50302655', 'Woimite', 'Mbotulaka', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 550, NULL, '2026-08-21 09:03:52'),
(563, 'SD NEGERI WOLOARA', '50306096', 'KOPOBHOBHE', 'Woloara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 551, NULL, '2026-08-21 09:03:52'),
(564, 'SD NEGERI WOLOGAWI', '50302656', 'Wologawi', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 552, NULL, '2026-08-21 09:03:52'),
(565, 'SD NEGERI WOLOHEPO', '50305682', 'Wolohepo', 'Mbuliwaralau', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 553, NULL, '2026-08-21 09:03:52'),
(566, 'SD NEGERI WOLOMONI', '50302674', 'Wolomoni', 'Niowula', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 554, NULL, '2026-08-21 09:03:52'),
(567, 'SD NEGERI WOLONIO', '50305607', 'Wolonio', 'Fatamari', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 555, NULL, '2026-08-21 09:03:52'),
(568, 'SD NEGERI WOLOOJA 2', '50305659', 'Wolooja', 'WOLOOJA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 556, NULL, '2026-08-21 09:03:52'),
(569, 'SD NEGERI WOLOWARU 3', '50302673', 'Jln. Kesehatan Wolowaru', 'Bokasape', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 557, NULL, '2026-08-21 09:03:52'),
(570, 'SD SWASTA MUHAMMADYAH ENDE', '50302544', 'Jln. Woloare B', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 558, NULL, '2026-08-21 09:03:52'),
(571, 'SDN NAKAWARA', '69768280', 'NAKAWARA', 'RANORAMBA', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 559, NULL, '2026-08-21 09:03:52'),
(572, 'SDN ULU DALA', '70045708', 'Jl. Trans Utara Desa Ulu Dala', 'ULUDALA', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 560, NULL, '2026-08-21 09:03:52'),
(573, 'SKB KABUPATEN ENDE', 'P9970219', 'Jl.Rambutan', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 561, NULL, '2026-08-21 09:14:52'),
(574, 'SMP NEGERI 2 ENDE', '50305409', 'Jln. Kelimutu', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 562, NULL, '2026-08-21 09:14:53'),
(575, 'SMP NEGERI 2 ENDE SELATAN', '50302615', 'Jln. Woloare B', 'Kel. Roworena', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 563, NULL, '2026-08-21 09:14:53'),
(576, 'SMP NEGERI 2 MAUROLE', '50302616', 'Jl. Trans Utara Ende - Maumere', 'Kotabaru', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 564, NULL, '2026-08-21 09:14:53'),
(577, 'SMP NEGERI 2 NANGAPANDA', '50302617', 'Dusun Roo', 'PADERAPE', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 565, NULL, '2026-08-21 09:14:53'),
(578, 'SMP NEGERI 2 NDONA', '50305419', 'Ngalupolo', 'Ngalupolo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 566, NULL, '2026-08-21 09:14:53'),
(579, 'SMP NEGERI 2 WOLOWARU', '50302634', 'Jln. Raya Ende - Maumere Km. 64', 'Lisedetu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 567, NULL, '2026-08-21 09:14:53'),
(580, 'SMP NEGERI 3 ENDE', '50305408', 'Mokeasa', 'Jamokeasa', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 568, NULL, '2026-08-21 09:14:53'),
(581, 'SMP NEGERI 3 NANGAPANDA', '50302633', 'Jln. Jurusan Puukungu- Maukaro', 'Tendambepa', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 569, NULL, '2026-08-21 09:14:53'),
(582, 'SMP NEGERI 3 NDONA', '50305420', 'Puutuga', 'Puutuga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 570, NULL, '2026-08-21 09:14:53'),
(583, 'SMP NEGERI 3 WOLOWARU', '50302632', 'JL. RAYA ENDE - MAUMERE KM. 92 WATUNESO', 'Kel. Watuneso', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 571, NULL, '2026-08-21 09:14:53'),
(584, 'SMP NEGERI 4 NANGAPANDA', '50305418', 'Rajawawo', 'Rapowawo', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 572, NULL, '2026-08-21 09:14:53'),
(585, 'SMP NEGERI 4 WOLOWARU', '50302631', 'Wolojita', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 573, NULL, '2026-08-21 09:14:53'),
(586, 'SMP NEGERI 5 NANGAPANDA', '50305686', 'NANGAMBOA II', 'Ondorea Barat', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 574, NULL, '2026-08-21 09:14:53'),
(587, 'SMP NEGERI 5 WOLOWARU', '50302630', 'Maubasa', 'Maubasa', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 575, NULL, '2026-08-21 09:14:53'),
(588, 'SMP NEGERI 6 NANGAPANDA', '50305687', 'ORAKERI', 'Tendarea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 576, NULL, '2026-08-21 09:14:53'),
(589, 'SMP NEGERI 7 NANGAPANDA', '69734399', 'NANGAKEO', 'Bheramari', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 577, NULL, '2026-08-21 09:14:53'),
(590, 'SMP NEGERI 8 NANGAPANDA', '69734400', 'RAPORENDU', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 578, NULL, '2026-08-21 09:14:53'),
(591, 'SMP NEGERI AEWORA', '69725940', 'AEWORA', 'Aewora', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 579, NULL, '2026-08-21 09:14:53'),
(592, 'SMP NEGERI DETUKELI', '50305407', 'Jalan Ragho Riwu-Detuara', 'Maurole Selatan', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 580, NULL, '2026-08-21 09:14:53'),
(593, 'SMP NEGERI DETUNGGALI', '50305425', 'Lewumbangga', 'Fataatu Timur', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 581, NULL, '2026-08-21 09:14:53'),
(594, 'SMP NEGERI EKOAE', '69938844', 'JL. TRANS UTARA ENDE - MBAY', 'Ekoae', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 582, NULL, '2026-08-21 09:14:53'),
(595, 'SMP NEGERI INE PARE', '69786325', 'NIDA', 'Watunggere', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 583, NULL, '2026-08-21 09:14:53'),
(596, 'SMP NEGERI MAUKARO', '50305416', 'Jln.Pantura Jurusan Maukaro - Ende', 'Kebirangga', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 584, NULL, '2026-08-21 09:14:53'),
(597, 'SMP NEGERI MAUTENDA', '50305426', 'AEGANA', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 585, NULL, '2026-08-21 09:14:53'),
(598, 'SMP NEGERI PANCASILA PORA', '50302613', 'Dusun Pora', 'Pora', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 586, NULL, '2026-08-21 09:14:53'),
(599, 'SMP NEGERI SATU ATAP AEREA', '50305423', 'Aerea', 'Kelisamba', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 587, NULL, '2026-08-21 09:14:53'),
(600, 'SMP NEGERI SATU ATAP DETUBELO', '50309143', 'Detubelo', 'Woloaro', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 588, NULL, '2026-08-21 09:14:53'),
(601, 'SMP NEGERI SATU ATAP EKOREKO', '50306099', 'PULAU ENDE', 'Ndoriwoy', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 589, NULL, '2026-08-21 09:14:53');
INSERT INTO `sekolah` (`id`, `nama_sekolah`, `npsn`, `alamat`, `kelurahan`, `kecamatan`, `kabupaten`, `provinsi`, `kode_pos`, `kepala_sekolah`, `pengawas`, `created_at`) VALUES
(602, 'SMP NEGERI SATU ATAP KOAWENA', '50309142', 'Ndetumbera RT/RW : 014/004', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 590, NULL, '2026-08-21 09:14:53'),
(603, 'SMP NEGERI SATU ATAP LIGALEJO', '50305688', 'Boto, Jln. Trans Utara', 'Rangalaka', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 591, NULL, '2026-08-21 09:14:53'),
(604, 'SMP NEGERI SATU ATAP MUNDINGGASA', '50308520', 'MUNDINGGASA', 'Mundinggasa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 592, NULL, '2026-08-21 09:14:53'),
(605, 'SMP NEGERI SATU ATAP NGALUROGA', '69948610', 'NGALUROGA', 'Ngaluroga', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 593, NULL, '2026-08-21 09:14:53'),
(606, 'SMP NEGERI SATU ATAP NGGEMO', '50308810', 'Trans Utara magekapa Rt.003/Rw.002 Kec. Maukaro Kab. Ende', 'Magekapa', 'Maukaro', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 594, NULL, '2026-08-21 09:14:53'),
(607, 'SMP NEGERI SATU ATAP NUAMURI 2', '50306097', 'DETUBU', 'Nuamuri Barat', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 595, NULL, '2026-08-21 09:14:53'),
(608, 'SMP NEGERI SATU ATAP NUAPU', '50305432', 'NDONA TIMUR', 'Roga', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 596, NULL, '2026-08-21 09:14:53'),
(609, 'SMP NEGERI SATU ATAP PASADOO', '69734398', 'PASADOO', 'Wologai Tengah', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 597, NULL, '2026-08-21 09:14:53'),
(610, 'SMP NEGERI SATU ATAP RABURIA', '50305412', 'RABURIA', 'Raburia', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 598, NULL, '2026-08-21 09:14:53'),
(611, 'SMP NEGERI SATU ATAP RATENGGOJI', '50305431', 'RATENGGOJI', 'Taniwoda', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 599, NULL, '2026-08-21 09:14:53'),
(612, 'SMP NEGERI SATU ATAP SOKOLOO', '50305430', 'SOKOLOO', 'Wologai Timur', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 600, NULL, '2026-08-21 09:14:53'),
(613, 'SMP NEGERI SATU ATAP TURUNALU', '69906527', 'TURUNALU', 'Turunalu', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 601, NULL, '2026-08-21 09:14:53'),
(614, 'SMP NEGERI SATU ATAP WOLOARA', '50305414', 'Woloara', 'Woloara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 602, NULL, '2026-08-21 09:14:53'),
(615, 'SMP NEGERI SATU ATAP WOLOGAI', '50306098', 'WOLOGAI', 'Wologai', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 603, NULL, '2026-08-21 09:14:53'),
(616, 'SMP NEGERI SATU ATAP WOLOOJA 3', '50308522', 'NUADEMU', 'Mbuliwaralau', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 520, NULL, '2026-08-21 09:14:53'),
(617, 'SMP NEGERI SEKOLENGO', '50305422', 'Sokoria', 'Sokoria', 'Ndona Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 604, NULL, '2026-08-21 09:14:53'),
(618, 'SMP NEGERI SOKORIA', '50305692', 'Sokoria', 'Ranokolo Selatan', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 605, NULL, '2026-08-21 09:14:53'),
(619, 'SMP NEGERI TANADAKI', '69964886', 'Jalan Trans Ende - Maumere - Desa Tinabani', 'Tinabani', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 606, NULL, '2026-08-21 09:14:53'),
(620, 'SMP NEGERI TONDANDORA', '69906738', 'RANDOTONDA', 'Randotonda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 607, NULL, '2026-08-21 09:14:53'),
(621, 'SMP KRISTEN ENDE', '50302658', 'Jl. Onekore No. 5 Ende', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 608, NULL, '2026-08-21 09:14:53'),
(622, 'SMP MUHAMMADIYAH ENDE', '50302635', 'Jalan Woloare B', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 609, NULL, '2026-08-21 09:14:53'),
(623, 'SMP SWASTA ADHYAKSA', '50302672', 'Jln. Sam Ratu Langi', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 610, NULL, '2026-08-21 09:14:53'),
(624, 'SMP SWASTA DONA MART', '70012314', 'JL. TRANS WOLOLELE A POROS MALI DUA', 'Fatamari', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 611, NULL, '2026-08-21 09:14:53'),
(625, 'SMP SWASTA DONAMART 2 ST. VINSENSIUS MOKEOBO', '79394274', 'DUSUN MOKEOBO', 'Kuru Sare', 'Lepembusu Kelisoke', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:14:53'),
(626, 'SMP SWASTA ISLAM MUTHMAINNAH', '50302607', 'Jln. Teuku Umar No. 10', 'Kel. Rukunlima', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 612, NULL, '2026-08-21 09:14:53'),
(627, 'SMP SWASTA KATOLIK CHRISTOREGI', '50305411', 'Jln. Perwira Ende', 'Kel. Kotaraja', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 613, NULL, '2026-08-21 09:14:53'),
(628, 'SMP SWASTA KATOLIK DETUKELI', '50302668', 'Detukeli', 'Detukeli', 'Detukeli', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 614, NULL, '2026-08-21 09:14:53'),
(629, 'SMP SWASTA KATOLIK EMANUEL MAUTENDA', '50305424', 'Welamosa', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 615, NULL, '2026-08-21 09:14:53'),
(630, 'SMP SWASTA KATOLIK INEMETE', '50302667', 'Kuberu', 'Ndorurea I', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 616, NULL, '2026-08-21 09:14:53'),
(631, 'SMP SWASTA KATOLIK MARSUDIRINI', '50302657', 'Detusoko', 'Detusoko Barat', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 617, NULL, '2026-08-21 09:14:53'),
(632, 'SMP SWASTA KATOLIK MONI', '50302636', 'Jln. Jurusan Ende -Maumere', 'Koanara', 'Kelimutu', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 618, NULL, '2026-08-21 09:14:53'),
(633, 'SMP SWASTA KATOLIK NIRMALA JOPU', '50302663', 'Jopu', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 619, NULL, '2026-08-21 09:14:53'),
(634, 'SMP SWASTA KATOLIK SANTA URSULA ENDE', '50302606', 'Jln. Wirajaya No. 3', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 620, NULL, '2026-08-21 09:14:53'),
(635, 'SMP SWASTA KATOLIK ST ALOYSIUS WOLOTOPO', '50302610', 'Wolotopo', 'Wolotopo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 621, NULL, '2026-08-21 09:14:53'),
(636, 'SMP SWASTA KATOLIK WOLOJITA', '50302661', 'Wolojita Ende', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 622, NULL, '2026-08-21 09:14:53'),
(637, 'SMP SWASTA KATOLIK WOLOTOLO', '50302660', 'Jln. Jurusan Ende Maumere', 'Wolotolo', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 623, NULL, '2026-08-21 09:14:53'),
(638, 'SMP SWASTA KATOLIK WOLOWARU', '50302659', 'Napuwaka Rt. 01 Rw.01', 'Lisedetu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 624, NULL, '2026-08-21 09:14:53'),
(639, 'SMP SWASTA KATOLIK YOS SUDARSO', '50302669', 'JL. UDAYANA', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 625, NULL, '2026-08-21 09:14:53'),
(640, 'SMP SWASTA KELIMUTU', '50302605', 'Jln. Durian Ende', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 626, NULL, '2026-08-21 09:14:53'),
(641, 'SMP SWASTA MADANI NDONDO', '50305415', 'Desa Loboniki, Kec. Kotabaru, Kab. Ende', 'Loboniki', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 627, NULL, '2026-08-21 09:14:53'),
(642, 'SMP SWASTA NUSANTARA', '50302614', 'Nuabosi', 'Ndetundora Ii', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 628, NULL, '2026-08-21 09:14:54'),
(643, 'SMP SWASTA REWARANGGA', '50302612', 'Jln. Hassanudin Wolowona', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 629, NULL, '2026-08-21 09:14:54'),
(644, 'SMP SWASTA SINAR PELITA', '50302611', 'Mukusaki', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 630, NULL, '2026-08-21 09:14:54'),
(645, 'SMP SWASTA TARUNA DESA', '50302670', 'JL. RAYA ENDE - MAUMERE KM. 25', 'Dile', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 631, NULL, '2026-08-21 09:14:54'),
(646, 'SMP SWASTA TRI DHARMA', '50305413', 'Jln. Melati', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 632, NULL, '2026-08-21 09:14:54'),
(647, 'SMPK ST. ANTONIUS NDONA', '50305421', 'Jln. Wolowona Ndona', 'Kel. Onelako', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 633, NULL, '2026-08-21 09:14:54'),
(648, 'SMPN HANGALANDE', '69768279', 'HANGALANDE', 'Hangalande', 'Kota Baru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 634, NULL, '2026-08-21 09:14:54'),
(649, 'SMPN KELIWUMBU', '69768278', 'KELIWUMBU', 'Keliwumbu', 'Maurole', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 635, NULL, '2026-08-21 09:14:54'),
(650, 'SMPN SATAP WOLOOJA 2', '69768277', 'WOLOOJA', 'WOLOOJA', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 636, NULL, '2026-08-21 09:14:54'),
(651, 'TKS ANAK YESUS JOPU', '50305513', 'JOPU', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 637, NULL, '2026-08-21 09:14:54'),
(652, 'TKS BUNGA BANGSA', '50305468', 'JL. MELATI, KOMPLEKS KPN', 'Kel. Paupire', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 638, NULL, '2026-08-21 09:14:54'),
(653, 'TKS CHRISTOREGI', '50305474', 'JL. I.H. DOKO', 'Kel. Tetandara', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 639, NULL, '2026-08-21 09:14:54'),
(654, 'TKS DEWI SARTIKA PORA', '50305520', 'PORA', 'Pora', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 640, NULL, '2026-08-21 09:14:54'),
(655, 'TKS DHARMA WANITA ENDE', '50305440', 'JL. ELTARI', 'Kel. Mautapaga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 641, NULL, '2026-08-21 09:14:54'),
(656, 'TKS DHARMA WANITA MBULILOO', '50305516', 'MBULILOO', 'Jopu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 642, NULL, '2026-08-21 09:14:54'),
(657, 'TKS DHARMA WANITA NANGAPANDA', '50305492', 'WARUKASU', 'Kel. Ndorurea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 643, NULL, '2026-08-21 09:14:54'),
(658, 'TKS DHARMA WANITA TENDA', '50305521', 'TENDA', 'Tenda', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 644, NULL, '2026-08-21 09:14:54'),
(659, 'TKS DHARMA WANITA WOLOJITA', '50305522', 'PORA', 'Kel. Wolojita', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 645, NULL, '2026-08-21 09:14:54'),
(660, 'TKS ISLAM TARBIYAH', '50305437', 'JL. PERWIRA', 'Kel. Kotaratu', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 646, NULL, '2026-08-21 09:14:54'),
(661, 'TKS KAPOLANDO', '50305502', 'DUSUN LELERIA', 'Manulondo', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 647, NULL, '2026-08-21 09:14:54'),
(662, 'TKS KARTINI NGGELA', '50305519', 'JL. KESEHATAN', 'Nggela', 'Wolojita', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:14:54'),
(663, 'TKS LEPEMBUSU', '50305452', 'EKOLETA', 'Wologai', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 648, NULL, '2026-08-21 09:14:54'),
(664, 'TKS MANUBARA', '50305508', 'EKOREKO', 'Rorurangga', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 649, NULL, '2026-08-21 09:14:54'),
(665, 'TKS MANUSAMA WOLOHEPO', '50305511', 'WOLOHEPO', 'Mbuliwaralau', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 650, NULL, '2026-08-21 09:14:54'),
(666, 'TKS MARDATILLAH', '50305494', 'MAUNGGORA', 'Nggorea', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 651, NULL, '2026-08-21 09:14:54'),
(667, 'TKS MARIA VIRGO 1', '50305503', 'RADAWUWU', 'Reka', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 652, NULL, '2026-08-21 09:14:54'),
(668, 'TKS MAUBASA', '50305439', 'MAUBASA', 'MAUBASA BARAT', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 653, NULL, '2026-08-21 09:14:54'),
(669, 'TKS MUSLIMAT NU FATIMAH AZZAHRAH', '50305466', 'JL. IKAN PAUS', 'Kel. Paupanda', 'Ende Selatan', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 654, NULL, '2026-08-21 09:14:54'),
(670, 'TKS NIRMALA', '50305493', 'NDORUREA 1', 'Ndorurea I', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 655, NULL, '2026-08-21 09:14:54'),
(671, 'TKS PUTRA DUNGGA', '50305501', 'DETUMBAWA', 'Ndungga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 656, NULL, '2026-08-21 09:14:54'),
(672, 'TKS REDODORI', '50305506', 'METINUMBA', 'Ndoriwoy', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 657, NULL, '2026-08-21 09:14:54'),
(673, 'TKS RENDORATERUA', '50305507', 'KERIMANDO', 'PADERAPE', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 658, NULL, '2026-08-21 09:14:54'),
(674, 'TKS RHERHEJA 2', '50305443', 'JL. SULTAN HASANUDDIN', 'Kel. Rewarangga', 'Ende Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 659, NULL, '2026-08-21 09:14:54'),
(675, 'TKS RINDIWAWO', '50305518', 'JL.ENDE-MAUMERE', 'Rindiwawo', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 660, NULL, '2026-08-21 09:14:54'),
(676, 'TKS SANDHY PUTRA', '50305470', 'JL. SUDIRMAN ENDE', 'Kel. Potulando', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 661, NULL, '2026-08-21 09:14:54'),
(677, 'TKS SANTA AGNES', '50305471', 'JL. KELIMUTU', 'Kel. Kelimutu', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 662, NULL, '2026-08-21 09:14:54'),
(678, 'TKS SANTA HELEN WOLOWARU', '50305514', 'JL.ENDE-MAUMERE', 'Lisedetu', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 663, NULL, '2026-08-21 09:14:54'),
(679, 'TKS SANTA MARIA MAGDALENA SOFIA BARAT', '50305459', 'KOMBANDARU', 'Riaraja', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:14:54'),
(680, 'TKS SANTA YASINTHA', '50305510', 'WELAMOSA', 'Welamosa', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 664, NULL, '2026-08-21 09:14:54'),
(681, 'TKS SARE ORHA', '50305460', 'NANGABA', 'Rukuramba', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 665, NULL, '2026-08-21 09:14:54'),
(682, 'TKS SATAP SDN PUUTARA', '50307734', 'PUUTARA', 'Puutara', 'Pulau Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 666, NULL, '2026-08-21 09:14:54'),
(683, 'TKS SATAP ST. PAULUS MUKUSAKI', '50307729', 'MUKUSAKI', 'Mukusaki', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 667, NULL, '2026-08-21 09:14:54'),
(684, 'TKS SATAP WOLOKOLI', '50307736', 'WOLOKOLI', 'Fataatu', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 668, NULL, '2026-08-21 09:14:54'),
(685, 'TKS SATOJOTO', '50305455', 'MOKEASA', 'Mbotutenda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 669, NULL, '2026-08-21 09:14:54'),
(686, 'TKS SATU ATAP DETUBELO', '69734393', 'WOLOARO', 'Woloaro', 'Lio Timur', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 670, NULL, '2026-08-21 09:14:54'),
(687, 'TKS SATU ATAP KEKAWII', '69734392', 'KEKAWII', 'Randotonda', 'Ende', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 671, NULL, '2026-08-21 09:14:54'),
(688, 'TKS SATU ATAP KOMBO', '50307727', 'KOMBO', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 672, NULL, '2026-08-21 09:14:54'),
(689, 'TKS SATU ATAP NUMBA 1', '50305495', 'NUMBA', 'Raporendu', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 673, NULL, '2026-08-21 09:14:54'),
(690, 'TKS ST. ARNOLDUS YANSEN MBOMBA', '50305433', 'JL.ENDE-NANGABA', 'Gheoghoma', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 674, NULL, '2026-08-21 09:14:54'),
(691, 'TKS ST. FRANSISKUS XAVERIUS WOLOTOLO', '50305454', 'JL. RAYA ENDE - MAUMERE KM. 20', 'Wolotolo', 'Detusoko', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 675, NULL, '2026-08-21 09:14:54'),
(692, 'TKS ST. MARIA NANGAMBOA', '50305489', 'NANGAMBOA', 'Ondorea Barat', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 676, NULL, '2026-08-21 09:14:54'),
(693, 'TKS ST. MARIA WOLOARE', '50305445', 'JL. WOLOARE A', 'Kel. Roworena', 'Ende Utara', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, NULL, NULL, '2026-08-21 09:14:54'),
(694, 'TKS ST. MARTHA MOLUTANGGA', '50307722', 'MOLUTANGGA', 'RATEWATI SELATAN', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 677, NULL, '2026-08-21 09:14:54'),
(695, 'TKS ST. MARTINUS WATUMITE', '50305497', 'WATUMITE', 'Watumite', 'Nangapanda', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 678, NULL, '2026-08-21 09:14:54'),
(696, 'TKS ST. MIKAEL WOLOLELE B', '50305515', 'JL.ENDE-MAUMERE', 'Liselowobora', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 679, NULL, '2026-08-21 09:14:54'),
(697, 'TKS ST. SISILIA AEGANA', '69734397', 'AEGANA', 'Mautenda', 'Wewaria', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 680, NULL, '2026-08-21 09:14:54'),
(698, 'TKS ST. YANUARIUS WONDA', '50305447', 'WONDA', 'AEBARA', 'Ndori', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 681, NULL, '2026-08-21 09:14:54'),
(699, 'TKS ST.FRANSISKUS XAVERIUS WOLOTOPO', '50305504', 'WOLOTOPO TIMUR', 'Wolotopo Timur', 'Ndona', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 682, NULL, '2026-08-21 09:14:54'),
(700, 'TKS SYALOOM', '50305464', 'JL. MARILONGA', 'Kel. Onekore', 'Ende Tengah', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 683, NULL, '2026-08-21 09:14:54'),
(701, 'TKS WOLOOJA', '50305517', 'JL.ENDE-MBULI', 'Mbuliwaralau Utara', 'Wolowaru', 'Kabupaten Ende', 'Nusa Tenggara Timur', NULL, 684, NULL, '2026-08-21 09:14:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('guru','kepala_sekolah','pengawas','dinas','admin') NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `sekolah` varchar(100) DEFAULT NULL,
  `sekolah_id` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `nip`, `sekolah`, `sekolah_id`, `phone`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Guru Matematika', 'guru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'guru', NULL, 'SMA Negeri 1', NULL, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:04:59'),
(2, 'Guru Bahasa Indonesia', 'guru2@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'guru', NULL, 'SMA Negeri 1', NULL, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:04:59'),
(3, 'Guru IPA', 'guru3@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'guru', NULL, 'SMA Negeri 2', NULL, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:04:59'),
(4, 'YUSTINA TEFBANA', 'kepsek.kb.arara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB ARARA', 1, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:03'),
(5, 'ATI MALA', 'kepsek.kb.rahman@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB Arrahman Watubara', 2, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:00'),
(6, 'FIKLARIUS LEMBA', 'kepsek.kb.fajar@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB FAJAR PAGI', 3, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:00'),
(7, 'ELISABET KLAUDIA WANGGE', 'kepsek.kb.keliwumbu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB KELIWUMBU', 4, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:00'),
(8, 'ASHAYATI NGGALO', 'kepsek.kb.marlom@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB MARLOM', 5, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:00'),
(9, 'ROSFITA MOKTAR', 'kepsek.kb.matabale@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB MATABALE', 6, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:02'),
(10, 'MARIA ANGELINA TIRTA WEA', 'kepsek.kb.mentari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB MENTARI', 7, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:01'),
(11, 'Maria Salome Ludia Pare', 'kepsek.kb.nualise@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB NUALISE', 8, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:01'),
(12, 'Romana Bande', 'kepsek.kb.pertiwi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB PERTIWI', 9, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:02'),
(13, 'HABIBAH AROEBOESMAN', 'kepsek.kb.perwira@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB PERWIRA', 10, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:00'),
(17, 'Dinas Pendidikan', 'dinas@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'dinas', NULL, 'Dinas Pendidikan Kabupaten Ende', NULL, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:03'),
(18, 'Administrator', 'admin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'admin', NULL, 'Sistem', NULL, NULL, NULL, 1, '2026-08-21 08:49:17', '2026-08-25 01:05:03'),
(19, 'YUSTINA MBAGHO', 'yustina.mbagho@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB SANTO HENDRIKUS', 11, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:03'),
(20, 'MIKAEL WANGGE', 'mikael.wangge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB SANTO PHILIPUS', 12, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(21, 'MARIA KRISTINA MITE', 'maria.kristina.mite@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB SINAR EMBUZOZO', 13, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(22, 'TRIFONIA NANDA', 'trifonia.nanda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB SINAR OTOLEKE', 14, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(23, 'Tantina Gawul Panggur', 'tantina.gawul.panggur@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB ST. PIUS', 15, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(24, 'ELEONORA DONGI', 'eleonora.dongi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB TERPADU KASIH BUNDA', 17, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(25, 'YOVITA METE', 'yovita.mete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB TERPADU RENATA', 18, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(26, 'MARIA VINSENSIA LERO', 'maria.vinsensia.lero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB TERPADU ST. PAULUS KOTAKADHE', 19, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(27, 'MARIA NOFRIANTI MBEMBE', 'maria.nofrianti.mbembe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB WAKA', 20, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(28, 'VENANTIUS MINGGU', 'venantius.minggu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB Watu Gamba', 21, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(29, 'MARIA IRMA SUSANTI ODUNG', 'maria.irma.susanti.odung@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB WONGA WEA NGGELA', 22, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(30, 'EUFRASIA PRISKA WETI', 'eufrasia.priska.weti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB WONGAWUJA', 23, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(31, 'IPIVANIUS MBELE', 'ipivanius.mbele@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. AEDERO', 24, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(32, 'ROBERTUS RERO', 'robertus.rero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. AEMAU NANGARIA', 25, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(33, 'Gaudensia Patrina Pia', 'gaudensia.patrina.pia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Anggrek', 26, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(34, 'Santi Abdul Hamid, S.pd', 'santi.abdul.hamid.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. ANUGERAH', 27, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(35, 'INTAN PURNAMA S. NASRUN', 'intan.purnama.s..nasrun@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Ar - Rahman', 28, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(36, 'SITI SUMANTI ISKANDAR', 'siti.sumanti.iskandar@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. AREMA', 29, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(37, 'EVA HIKMAH', 'eva.hikmah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. AZZAHRA', 30, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(38, 'Mersiana We', 'mersiana.we@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Bengawan Jaya', 31, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(39, 'MARKUS TUJU', 'markus.tuju@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. BEWU SEA', 32, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(40, 'LUSIA ELVIONITA', 'lusia.elvionita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. BHISU KOJA', 33, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(41, 'Laurensius Jaru', 'laurensius.jaru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. BINA KASIH TURUNALU', 34, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(42, 'Mustafar Haji', 'mustafar.haji@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Borokanda', 35, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(43, 'SITTI HAJARUL HASTUTI, SE', 'sitti.hajarul.hastuti.se@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Bunda Perubahan', 36, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(44, 'DEWI PURWANTI DERO', 'dewi.purwanti.dero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. CINTA ANAK HOBATUWA', 37, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(45, 'PETRUS ROGA', 'petrus.roga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. CINTA ANAK NUABOSI', 38, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(46, 'ANGELINA SAMA', 'angelina.sama@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. DETUNGGALI  LEWUMBANGGA', 40, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(47, 'SUMARNI', 'sumarni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. DEWI LESTARI', 41, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(48, 'MARIANA ORI NGERI', 'mariana.ori.ngeri@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. EMBURIA', 43, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(49, 'MOSES MBAKE', 'moses.mbake@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Harapan Bangsa', 45, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(50, 'Arsad Ismail Dj', 'arsad.ismail.dj@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. HARAPAN BUNDA', 46, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(51, 'WILHELMUS DHOMBA', 'wilhelmus.dhomba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Kasih Ibu Mundinggasa', 48, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(52, 'YUSTINA TIWE', 'yustina.tiwe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Kasih Ibu Wolomuku', 49, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:03'),
(53, 'KRISTOFORUS ORO MARI', 'kristoforus.oro.mari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Marilonga', 50, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(54, 'MARIA TRISANTI WENI', 'maria.trisanti.weni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. MODA BENGE', 51, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(55, 'Oktavia Viviana Nggando', 'oktavia.viviana.nggando@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Nanganesa', 52, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(56, 'MARIA SUSANTI RUMBA', 'maria.susanti.rumba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. NASARET BENGGE', 53, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(57, 'MARIYATI SENDISNA WIWIN', 'mariyati.sendisna.wiwin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Ndoki Pati', 54, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(58, 'Abdul Hamid Abdurachman', 'abdul.hamid.abdurachman@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. PATAS', 56, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:04:59'),
(59, 'ANSELMUS MERE', 'anselmus.mere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. PEDI PASO', 57, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(60, 'ALOYSIUS RUTHU', 'aloysius.ruthu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. PERA PAWE', 58, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(61, 'MARIA ADVENTISIANA NDARA', 'maria.adventisiana.ndara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. Ratu Pencinta Balita', 59, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(62, 'Kornelius Naga', 'kornelius.naga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. SALIB SUCI NDETUNDOPO', 60, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(63, 'Mikael Di', 'mikael.di@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. SANTA AGUSTINA AELOGA', 61, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(64, 'Fransiskus Damianus Noe', 'fransiskus.damianus.noe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. SANTA SISILIA', 62, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(65, 'MOSES NOE', 'moses.noe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. SEHATI', 63, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(66, 'ELISABETH PURWATI', 'elisabeth.purwati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. St. ANTONIUS PAUWAWA', 64, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(67, 'STEFANIA SANTI NELU', 'stefania.santi.nelu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. St. SOFIA EKOLEA', 65, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(68, 'Nurbaiti', 'nurbaiti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. TUNAS BANGSA', 67, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(69, 'SALMAH YASIN', 'salmah.yasin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. TUNAS BARU', 68, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(70, 'Servasiana Anita Kapi', 'servasiana.anita.kapi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. ULU DALA', 69, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(71, 'MARIA HYAZINTA VITA', 'maria.hyazinta.vita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. WOLO WEA', 70, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(72, 'Maria Ura', 'maria.ura@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. WOLOLANU', 71, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(73, 'BERTIANA REI', 'bertiana.rei@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. WOLOSOKO', 72, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(74, 'YASINTA SIKA', 'yasinta.sika@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB. WOROPAPA', 73, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(75, 'WILHELMUS MBASA', 'wilhelmus.mbasa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Bintang Timur', 74, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(76, 'PRAXEDIS ODA ROSA', 'praxedis.oda.rosa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Bunga Mawar', 75, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(77, 'STEFANUS MBUSU', 'stefanus.mbusu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Cahaya Permata', 76, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(78, 'Yolenta Dhema', 'yolenta.dhema@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Danau Ranoria', 77, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(79, 'MOSES LERU', 'moses.leru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Fajar Timur', 78, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(80, 'Asmira Sere', 'asmira.sere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Ilham', 79, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(81, 'RAMLAN USMAN', 'ramlan.usman@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Ingin Maju', 80, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(82, 'Kristine Paskaline Timbu', 'kristine.paskaline.timbu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Kasih Ibu Mbiru', 81, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(83, 'Marlince Rosniwati Tanggela', 'marlince.rosniwati.tanggela@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Lunggaria', 82, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(84, 'HADIJAH ABDULLAH', 'hadijah.abdullah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Ndori Sare', 83, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(85, 'Yohanes Sari', 'yohanes.sari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.NUAGIU', 84, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:02'),
(86, 'Marwah Abubekar', 'marwah.abubekar@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Nusa Sura', 85, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(87, 'ANASTASIA WUNU', 'anastasia.wunu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Permata Hati', 86, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(88, 'CITRA DEWI', 'citra.dewi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Permata Puutara', 87, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(89, 'fransiskus seda', 'fransiskus.seda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Peromboro', 88, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:00'),
(90, 'Maria Theresia Ndaro', 'maria.theresia.ndaro@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Sehati Rateroru', 89, NULL, NULL, 1, '2026-08-21 08:58:14', '2026-08-25 01:05:01'),
(91, 'BARNABAS BEGO', 'barnabas.bego@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.SEKOSODO', 90, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(92, 'Abdullah Alwan Kea Benie', 'abdullah.alwan.kea.benie@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Sinar Bahagia', 91, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:04:59'),
(93, 'Maria Kres.Marg.Resi', 'maria.kres.marg.resi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Sinar Harapan Dile', 92, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(94, 'FLAVIANA VIANI TALI', 'flaviana.viani.tali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.St. Faustina Anaranda', 93, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(95, 'Andreas Tonda', 'andreas.tonda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.St.Antonius', 94, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(96, 'Purnama Sari Pora', 'purnama.sari.pora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Tabah', 95, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(97, 'ELISABETH LISNAWATI GOBA', 'elisabeth.lisnawati.goba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Try Warna', 96, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(98, 'MARIA ELSAN RARA', 'maria.elsan.rara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Tunas Harapan', 97, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(99, 'YOHANES DIANA EMBU LABA', 'yohanes.diana.embu.laba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KB.Wolokoli', 98, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(100, 'Geradus Friedrich Gani', 'geradus.friedrich.gani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KBA MURI SARE', 99, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(101, 'Adriana Susani Bayu', 'adriana.susani.bayu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KBA NAZARETH', 100, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(102, 'Nuraini Daeng', 'nuraini.daeng@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KBA RAJAWALI', 101, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(103, 'FEBRI CHUSWANTI ADOE', 'febri.chuswanti.adoe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER AISYIYAH', 102, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(104, 'NURMA MOH. NUR', 'nurma.moh..nur@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER AMBUGAGA', 103, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(105, 'VIRGINA GELO', 'virgina.gelo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER BINA KASIH DETUNGGALI', 104, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(106, 'Redemptus Teku, S. Pd', 'redemptus.teku.s..pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER BOAFEO', 105, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(107, 'Ignasius Piter', 'ignasius.piter@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER BOTI FATE', 106, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(108, 'Anita Sumby', 'anita.sumby@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER GAMA GENERATION', 107, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(109, 'Cyrillus Danu', 'cyrillus.danu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER KASIH BUNDA WIWIPEMO', 108, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(110, 'EMILIANUS NOPE', 'emilianus.nope@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER LANDO RANGA', 110, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(111, 'Valentiana Citra Woti', 'valentiana.citra.woti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER MAUMERI PERMAI', 111, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(112, 'PATERNUS BAGI', 'paternus.bagi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER MEKAR SARI', 112, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(113, 'ELISABETH  TEPI', 'elisabeth..tepi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER NIRAMESI', 114, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(114, 'Anastasia Nia', 'anastasia.nia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER ODA MBESI', 115, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(115, 'Adrianus Dapi', 'adrianus.dapi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER PUU KOJA NDITO', 116, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(116, 'NURSARI MUHAMAD SALEH', 'nursari.muhamad.saleh@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER RENDO SARE', 117, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(117, 'DOMINIKA BHENE', 'dominika.bhene@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER SADO GEDU', 118, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(118, 'ROSWITA ANGELA NDIMBU', 'roswita.angela.ndimbu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER SEKOPADA', 119, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(119, 'MARTHA D.WANGGE', 'martha.d.wangge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER SOLIDARITAS BUNDA', 120, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(120, 'YOHANES REA', 'yohanes.rea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER ST. BONEFASIUS MBANI', 121, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(121, 'LETSIANA SEKU', 'letsiana.seku@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER St. DANIEL', 122, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(122, 'MARIA JUS', 'maria.jus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER St. JOSEF FREINADEMETZ MAUTAPAGA', 123, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(123, 'Katarina Nimu Hokeng', 'katarina.nimu.hokeng@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER ST. SIMON PETRUS TIWUSORA', 125, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(124, 'FLORENSIANA MARIA ADEL PUTRI', 'florensiana.maria.adel.putri@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER STA. THERESIA', 126, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(125, 'YULIANA MBU', 'yuliana.mbu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER TANI WODA KARYA', 127, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(126, 'MARLINA LENI NDIKI', 'marlina.leni.ndiki@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER TENDA TEBHA', 128, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(127, 'HIRONIMUS RAPE', 'hironimus.rape@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER UNGGU JAYA', 129, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(128, 'Melania Bili Nara', 'melania.bili.nara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER WEE LOMBO', 130, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(129, 'SISILIA LINDA', 'sisilia.linda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'KOBER WOLOMONI', 131, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(130, 'Theresia Ewaldina Hayon', 'theresia.ewaldina.hayon@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PAUD PELITA', 132, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(131, 'Herman Jani', 'herman.jani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PAUD ST. ALEXANDRIA NGGEMO', 133, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(132, 'Mardan AB Djaba', 'mardan.ab.djaba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PAUD TERPADU POSYANDU MANDIRI BASA PUURERE', 134, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(133, 'SRY NUNUNG ABDUL GANI', 'sry.nunung.abdul.gani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TAUD Saqu Al Misykah', 135, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(134, 'YOHANES EWALDUS MARI', 'yohanes.ewaldus.mari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PKBM ALOKOJA SIA', 136, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(135, 'ROSDIYANA', 'rosdiyana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197601011976011001', 'PKBM ANNORA', 137, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(136, 'Fortunatus Towa, S.Pd', 'fortunatus.towa.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PKBM BUNGA MAWAR', 138, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(137, 'AGUSTINUS LAGA', 'agustinus.laga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198408011984081001', 'PKBM KAPO WALO', 139, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(138, 'MARIA ROSA MISTIKA NIO WALE, S.Pd', 'maria.rosa.mistika.nio.wale.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PKBM KEBHI DUA', 140, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(139, 'IBRAHIM NGGETE', 'ibrahim.nggete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'PKBM Sandi Kelana Ngalukoja', 142, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(140, 'Yosefina Mai', 'yosefina.mai@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196809011968091001', 'PKBM SANTA ANGELA ENDE', 143, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(141, 'Selfiana Theresia', 'selfiana.theresia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD GMIT ENDE 4', 144, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(142, 'Maria Fabiola Lami', 'maria.fabiola.lami@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197910121979102001', 'SD INPRES AEDARI', 145, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(143, 'Religius  Gawe', 'religius..gawe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196706101967061001', 'SD INPRES AEKORA', 146, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(144, 'Ambrosius Sari', 'ambrosius.sari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197008121970081001', 'SD INPRES AEMAU', 147, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:00'),
(145, 'Maria Goretty Kupa', 'maria.goretty.kupa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197002141970022001', 'SD INPRES AEREA', 148, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(146, 'Yulin L. Rerung', 'yulin.l..rerung@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197107231971071001', 'SD INPRES AETEKE', 149, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(147, 'Philipus Embu', 'philipus.embu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196704121967041001', 'SD INPRES BARAI 1', 150, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:02'),
(148, 'Johora', 'johora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SD INPRES BARAI 2', 151, NULL, NULL, 1, '2026-08-21 08:58:15', '2026-08-25 01:05:01'),
(150, 'Nikolaus Mbele', 'nikolaus.mbele@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196706101967061001', 'SD INPRES BELANGGO', 152, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(151, 'Maria Goreti Sri Kusumastuti', 'maria.goreti.sri.kusumastuti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197107101971071001', 'SD INPRES BHOANAWA 1', 153, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(152, 'Umar Kobe', 'umar.kobe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196609101966091001', 'SD INPRES BHOANAWA 2', 154, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(153, 'Yohanes Juang', 'yohanes.juang@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196809101968091001', 'SMP NEGERI 1 DETUSOKO', 155, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(154, 'Godefrida Rosa Da Lima Suria', 'godefrida.rosa.da.lima.suria@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197708101977081001', 'SMP NEGERI 1 ENDE', 156, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(155, 'SYAIFUDIN PUA RIWA', 'syaifudin.pua.riwa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SMP NEGERI 1 ENDE SELATAN', 157, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(156, 'Maria Ervina Paku', 'maria.ervina.paku@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198010101980101001', 'SMP NEGERI 1 MAUROLE', 158, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(157, 'Natalia Nata', 'natalia.nata@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197012101970121001', 'SMP NEGERI 1 NANGAPANDA', 159, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(158, 'Sofia Ere', 'sofia.ere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197411101974111001', 'SMP NEGERI 1 NDONA', 160, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(159, 'ARFAH OBA PEWA', 'arfah.oba.pewa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196908101969081001', 'SMP NEGERI 1 WOLOWARU', 161, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(160, 'Maria Margaretha Londa', 'maria.margaretha.londa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197212101972121001', 'SMP NEGERI 2 DETUSOKO', 162, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(161, 'Arianus Adam Raja Oja', 'arianus.adam.raja.oja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP KATOLIK FRATERAN NDAO ENDE', 163, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(162, 'Oktavianus Vikaris Rani', 'oktavianus.vikaris.rani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP KATOLIK MARIA GORETI ENDE', 164, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(163, 'Mega Sartika Radja Modjo', 'mega.sartika.radja.modjo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP KATOLIK NAZARETH ENDE', 165, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(164, 'Yohanes Tau', 'yohanes.tau@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197201011972011001', 'SMP KATOLIK ST. GABRIEL NDONA', 166, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(165, 'Maria Theresia Mbindi', 'maria.theresia.mbindi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SMP KATOLIK ST. THERESIA NGGELA', 167, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(166, 'Titus Hoke Rehi', 'titus.hoke.rehi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196911101969111001', 'SMP KATOLIK SWADAYA MAUKARO', 168, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(167, 'Yulita Angela Ine', 'yulita.angela.ine@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197401101974011001', 'SMP KATOLIK WAWONATO', 169, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(168, 'JAMIAH', 'jamiah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'TK IDHATA WOLOWARU', 170, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(169, 'Ferdinandus Dhae', 'ferdinandus.dhae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198210101982101001', 'TK NEGERI PEMBINA KOTA BARU', 171, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(170, 'Maria Rosalina Widiyanti Pama Gado', 'maria.rosalina.widiyanti.pama.gado@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197905101979051001', 'TK RHERHEJA 1', 172, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(171, 'Alberta Ratsiana Mara', 'alberta.ratsiana.mara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TINA BANI', 173, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(172, 'SABINA NITA', 'sabina.nita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'TK.SATAP WELAMOSA', 174, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(173, 'Marselina Albina Feni', 'marselina.albina.feni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196704101967041001', 'TKN PEMBINA ENDE', 175, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(174, 'VERONIKA DA OCA', 'veronika.da.oca@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK AETEKE', 176, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(175, 'MASLIKHA', 'maslikha@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK ARNOLDUS JANSEN', 178, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(176, 'HERMINA ENDRA', 'hermina.endra@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK BELUT SAKTI', 179, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(177, 'YATWILDA TERESIA LAWI', 'yatwilda.teresia.lawi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK CINTA ABADI', 180, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(178, 'MARIA YANTI', 'maria.yanti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK CINTA ANAK', 181, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(179, 'KATARINA PORA', 'katarina.pora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK DEYNICA', 182, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(180, 'Fransiana Aso', 'fransiana.aso@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK EMBU TURU', 183, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(181, 'Valentina Dhoka', 'valentina.dhoka@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK Harapan Baru', 184, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(182, 'Yanti Ishak', 'yanti.ishak@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK HARAPAN BUNDA', 185, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(183, 'NURSIA ARSYAD', 'nursia.arsyad@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK HARAPAN REDODORI', 186, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(184, 'KATARINA GAMBA', 'katarina.gamba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK JEO DUA', 187, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(185, 'Sri Yanti Yunus', 'sri.yanti.yunus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK KARTINI PUUPAU', 188, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(186, 'AGUSTINA BALE ANA', 'agustina.bale.ana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196908101969081001', 'TK KI HAJAR DEWANTARA NUAMURI', 189, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(187, 'MARIA SERAFIM SIMA', 'maria.serafim.sima@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK KURU KELI', 190, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(188, 'ROMANA UMI', 'romana.umi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK LOKADHIO', 191, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(189, 'FRANSISKA ANJIKA WAIN', 'fransiska.anjika.wain@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197012101970121001', 'TK MARIA FATIMA KOANARA', 192, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(190, 'YULIANA UJUT', 'yuliana.ujut@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK MARIA VIRGO 2', 193, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(191, 'MARIA OLGAN TUGA', 'maria.olgan.tuga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK MBETE KAKI', 194, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(192, 'Masah', 'masah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'TK Melati', 195, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(193, 'RADYATUL ADWIAH', 'radyatul.adwiah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK MUHAMADIYAH ENDE', 196, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(194, 'ROSALIA DENGU', 'rosalia.dengu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK MUTIARA KASIH', 197, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(195, 'Adel Berti Yunita', 'adel.berti.yunita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK NAZARETH', 198, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(196, 'ELISABETH ININO MITE', 'elisabeth.inino.mite@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK PELITA HATI EMBU NGENA', 199, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(197, 'APOLONIA DHANA', 'apolonia.dhana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK PENA RIA', 200, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(198, 'MARIA GAUDENTIA MI', 'maria.gaudentia.mi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196809101968091001', 'TK PUUKUNGU', 202, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(199, 'RASDIANA ERVIM ARI YANTI', 'rasdiana.ervim.ari.yanti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK Raburia', 203, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(200, 'MARIA BARA', 'maria.bara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK RANORAMBA', 204, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(201, 'MARIA EROSIANA RENDO', 'maria.erosiana.rendo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK Rera Wete', 205, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(202, 'DOMINIKA DIKI SADA', 'dominika.diki.sada@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK SANTO ANTONIUS WOLOORA', 206, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(203, 'Awaliah Rahman M. Tamrin', 'awaliah.rahman.m..tamrin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK Sare Pawe', 207, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:00'),
(204, 'MATILDE DHAE', 'matilde.dhae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK SATU ATAP NDETUKUNE', 209, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(205, 'Ludgardis Mi', 'ludgardis.mi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK St. AGUSTINUS', 210, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(206, 'YELITA NARSISIAS BEWU', 'yelita.narsisias.bewu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK ST. MARTHA SOKORIA', 211, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:02'),
(207, 'Maria Atika Rao', 'maria.atika.rao@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK ST. PAULUS NANGAKEO', 212, NULL, NULL, 1, '2026-08-21 09:00:06', '2026-08-25 01:05:01'),
(208, 'Stefanus Beda', 'stefanus.beda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK St. Theresia', 214, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(209, 'Emiliana Mindelti Jando', 'emiliana.mindelti.jando@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK ST. VINCENTIUS RATESUBA', 215, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(210, 'Helena Yasinta Dhato', 'helena.yasinta.dhato@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TAMBORA', 216, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(211, 'PAULINA WUNU', 'paulina.wunu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TANA NUWA', 217, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(212, 'Martina Wula', 'martina.wula@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TANAROGA', 218, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(213, 'DONATUS JAGO', 'donatus.jago@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TEKAD', 219, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(214, 'Bernadeta Lawo', 'bernadeta.lawo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TERPADU WOLOOJA 2', 220, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(215, 'EMILIANA DORCE', 'emiliana.dorce@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK TRINITAS OJA', 221, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(216, 'MARIA REGINA RONA PELA SEKE', 'maria.regina.rona.pela.seke@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK YAPERTIF', 222, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(217, 'DIDAKUS RENE PIJO BEKA', 'didakus.rene.pijo.beka@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. MALAWARU', 224, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `nip`, `sekolah`, `sekolah_id`, `phone`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(218, 'MARIA VALESTINA LETSIA NGO', 'maria.valestina.letsia.ngo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. OSOSOMBO LOKOBOKO', 225, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(219, 'HABIBA NOO TOMBU RODJA', 'habiba.noo.tombu.rodja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197301011973011001', 'TK. Pertiwi Cab. Ende', 226, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(220, 'MARIA MENSIANA UE', 'maria.mensiana.ue@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. PUUMBARA', 227, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(221, 'Florentina Owa', 'florentina.owa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. ROMAREA', 228, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(222, 'INVIOLATA DORCE', 'inviolata.dorce@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. Salib Suci Maurole', 229, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(223, 'Yohanes Nikolaus Duna', 'yohanes.nikolaus.duna@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. SANTO MATHEUS AEMURI', 230, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(224, 'RASDIANA SUPRYANTI', 'rasdiana.supryanti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. SATAP KARTINI KELITEMBU', 232, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(225, 'MARTINA WEA', 'martina.wea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. SIGARIA', 233, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(226, 'MAGDALENA KLARA BARI', 'magdalena.klara.bari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. ST. GETRUDIS KEBIRANGGA', 234, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(227, 'Bernadus M. Taji', 'bernadus.m..taji@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. St. MARIA SATAP DETUBELA', 235, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:00'),
(228, 'Yustina Nge', 'yustina.nge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. ST. MAXIMILLIANUS MARIA KOLBE RANGGATALO', 236, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:03'),
(229, 'Yohana Mariana Kuna Noe', 'yohana.mariana.kuna.noe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. ST. PETRUS PUUKOU', 237, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(230, 'Susana Pare', 'susana.pare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. St. YOHANES EKOAE', 238, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(231, 'Yulita Nobho', 'yulita.nobho@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK. TAUSIA NDANGAKAPA', 239, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:03'),
(232, 'NURHAYATI', 'nurhayati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197207101972071001', 'TK.KARTIKA VII-8 ENDE', 240, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(233, 'Wahidah Ratu', 'wahidah.ratu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197301011973011001', 'TK.Kusuma Udayana 2', 241, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(234, 'MARIA DESILVA USFINIT', 'maria.desilva.usfinit@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.SATAP ADE IRMA', 242, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(235, 'REGINA SAWO', 'regina.sawo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'TK.SATAP NDETUNDORA I', 243, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(236, 'XAVERIUS JIRA', 'xaverius.jira@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.Satap Raaweka', 244, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(237, 'MARIA SERAVIAM RERE BARA DAWI', 'maria.seraviam.rere.bara.dawi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.ST.Bernadetha Wolomage', 245, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(238, 'Martina Dae', 'martina.dae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.ST.FRANSISKUS ASSISI', 246, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(239, 'KLARA SAMA', 'klara.sama@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.ST.PAULUS VI', 247, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(240, 'Olifa Nggua', 'olifa.nggua@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196610101966101001', 'TK.ST.THERESIA WOLOFEO', 248, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:02'),
(241, 'KATARINA VIVIN BUBA', 'katarina.vivin.buba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TK.Yohanes Pemandi', 249, NULL, NULL, 1, '2026-08-21 09:00:07', '2026-08-25 01:05:01'),
(242, 'Tensi Ludwina', 'tensi.ludwina@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD INPRES DETUBELO', 250, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:02'),
(243, 'Fransiska Li Sare', 'fransiska.li.sare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197505101975051001', 'SD INPRES DETUENA', 251, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(244, 'Kristiana Seso', 'kristiana.seso@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197603101976031001', 'SD INPRES DETUETE', 252, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(245, 'Rafael Belawa', 'rafael.belawa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD INPRES DETUSOKO', 253, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:02'),
(246, 'Maximus Sambi', 'maximus.sambi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD INPRES DETUWIRA', 254, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(247, 'Laurensia Sarlin Adolfina', 'laurensia.sarlin.adolfina@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196808101968081001', 'SD INPRES EKOLEA', 255, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(248, 'Maria Ludburgis Pawe', 'maria.ludburgis.pawe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198410101984101001', 'SD INPRES EKOTARU', 256, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(249, 'Anastasia Sue', 'anastasia.sue@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196609101966091001', 'SD INPRES ENDE 10', 257, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(250, 'Nudiana', 'nudiana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196903101969031001', 'SD INPRES ENDE 12', 259, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(251, 'Anastasia Jona', 'anastasia.jona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197501101975011001', 'SD INPRES ENDE 13', 260, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(252, 'Maria Dima Mbere', 'maria.dima.mbere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197009101970091001', 'SD INPRES ENDE 14', 261, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(253, 'Maria Fabiola Sarlota', 'maria.fabiola.sarlota@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197605101976051001', 'SD INPRES ENDE 15', 262, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(254, 'Aisyah Yati', 'aisyah.yati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197808101978081001', 'SD INPRES ENDE 16', 263, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(255, 'Sisilia Ale', 'sisilia.ale@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'SD INPRES ENDE 7', 264, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:02'),
(256, 'NUR SUFITRIYANTI', 'nur.sufitriyanti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198406101984061001', 'SD INPRES ENDE 9', 265, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(257, 'Gasparina Seo', 'gasparina.seo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198105101981051001', 'SD INPRES FEORIA', 266, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(258, 'Zainul Ali', 'zainul.ali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197907101979071001', 'SD INPRES HOBAKUA', 267, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:03'),
(259, 'Muhamad Nur Azis', 'muhamad.nur.azis@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198901101989011001', 'SD INPRES ILIWODO 1', 268, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(260, 'Hildegardis Jawu, S.Pd.', 'hildegardis.jawu.s.pd.@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198509101985091001', 'SD INPRES ILIWODO 2', 269, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(261, 'Antonius Gati', 'antonius.gati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD INPRES JOPU 4', 270, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(262, 'Maria Teresia Sawo', 'maria.teresia.sawo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196910101969101001', 'SD INPRES JOPU 5', 271, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(263, 'Albino Lexyanus R. Tika', 'albino.lexyanus.r..tika@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198402101984021001', 'SD INPRES KEKAKEU', 272, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(264, 'MARSELINA IDA', 'marselina.ida@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197002101970021001', 'SD INPRES KEKAWII', 273, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(265, 'Maria Herlina Suba', 'maria.herlina.suba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198205101982051001', 'SD INPRES KELITEMBU', 274, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(266, 'Elisabeth Weli', 'elisabeth.weli@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197302101973021001', 'SD INPRES KOAGATA', 275, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(267, 'Hubertina Ndena', 'hubertina.ndena@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196711101967111001', 'SD INPRES KOAWENA', 276, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(268, 'Laurensia Sombo', 'laurensia.sombo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197508101975081001', 'SD INPRES KOLIKAPA', 277, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(269, 'Aloysius Wora', 'aloysius.wora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'SD INPRES KOTABARU', 278, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(270, 'Marinus Dari', 'marinus.dari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198502101985021001', 'SD INPRES KURUMBORO', 279, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(271, 'Mozes Orzim', 'mozes.orzim@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197902101979021001', 'SD INPRES LEWAGARE', 280, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(272, 'Yulius Nai', 'yulius.nai@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197210101972101001', 'SD INPRES LIANGGERE', 281, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:03'),
(273, 'Martinus Didakus Paru', 'martinus.didakus.paru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196711101967111001', 'SD INPRES LIGALEJO', 282, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(274, 'Maria Goreti Ice', 'maria.goreti.ice@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197802101978021001', 'SD INPRES LOKOBOKO', 283, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(275, 'Yoseph Rega', 'yoseph.rega@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198208101982081001', 'SD INPRES LOWOKETO', 284, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:02'),
(276, 'Yulius Laka', 'yulius.laka@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197612101976121001', 'SD INPRES LOWORONGGA', 285, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:03'),
(277, 'YULIUS KAJU', 'yulius.kaju@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198906101989061001', 'SD INPRES MALAWARU', 286, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:03'),
(278, 'Magdalena Pare', 'magdalena.pare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197812101978121001', 'SD INPRES MAUROLE', 288, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:01'),
(279, 'Amir', 'amir@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198206101982061001', 'SD INPRES MAURONGGA', 289, NULL, NULL, 1, '2026-08-21 09:03:49', '2026-08-25 01:05:00'),
(280, 'Donatus Sale', 'donatus.sale@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197807101978071001', 'SD INPRES MAUTENDA', 290, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(281, 'Adinona Muhamad', 'adinona.muhamad@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197403101974031001', 'SD INPRES MBONGAWANI', 291, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(282, 'Rosalia Deo', 'rosalia.deo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'SD INPRES MBOTUJITA', 292, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(283, 'Teresia Fifi', 'teresia.fifi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198406101984061001', 'SD INPRES MBUJALOO', 293, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(284, 'Arifin Rego', 'arifin.rego@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD INPRES MBULILOO', 294, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(285, 'Umni Pua', 'umni.pua@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197512101975121001', 'SD INPRES METINUMBA 1', 295, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(286, 'Sadikin', 'sadikin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197308101973081001', 'SD INPRES METINUMBA 2', 296, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(287, 'VINSENSIA ASINTA SEPA', 'vinsensia.asinta.sepa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '199207101992071001', 'SD INPRES MUNDINGGASA', 297, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(288, 'WEA HIRONIMUS', 'wea.hironimus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196701101967011001', 'SD INPRES NANGANIO', 298, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(289, 'BENEDIKTA SOFIA LAI BHIA TEI', 'benedikta.sofia.lai.bhia.tei@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD INPRES NANGAPANDA 2', 299, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(290, 'Egedius Sama Saja', 'egedius.sama.saja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197504101975041001', 'SD INPRES NANGAPANDA 3', 300, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(291, 'Oliva  Isa', 'oliva..isa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196801101968011001', 'SD INPRES NDETUFEO', 301, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(292, 'Maria Avila Sina', 'maria.avila.sina@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198601101986011001', 'SD INPRES NDETUNDORA 1', 302, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(293, 'Maria Anita Nona', 'maria.anita.nona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198609101986091001', 'SD INPRES NDETUNDORA 2', 303, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(294, 'Akharius Sawa', 'akharius.sawa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196704101967041001', 'SD INPRES NDETUWARU', 304, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(295, 'MARIA RATNA GULA MI', 'maria.ratna.gula.mi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197003101970031001', 'SD INPRES NDITO', 305, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(296, 'Maria Ana', 'maria.ana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196801101968011001', 'SD INPRES NDONA 3', 306, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(297, 'Fatimah Yeti', 'fatimah.yeti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196706101967061001', 'SD INPRES NDONA 4', 307, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(298, 'Pankrasia Natalia', 'pankrasia.natalia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196608101966081001', 'SD INPRES NGALUPOLO', 308, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(299, 'Hanifa Ali', 'hanifa.ali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD INPRES NGALUROGA', 309, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(300, 'Maria Herlina Lambo', 'maria.herlina.lambo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197302101973021001', 'SD INPRES NGGELA 2', 310, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(301, 'Anastasia  Mbere', 'anastasia..mbere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SD INPRES NGGEMO', 311, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(302, 'Siti Khadijah', 'siti.khadijah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198408101984081001', 'SD INPRES NIONIBA', 312, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(303, 'Gabrielis Penu', 'gabrielis.penu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197910101979101001', 'SD INPRES NIOSANGGO', 313, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(304, 'FELISTA MARIA YULITA', 'felista.maria.yulita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197901101979011001', 'SD INPRES NIRANUSA', 314, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(305, 'Veronika Fransiska Baru', 'veronika.fransiska.baru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198012101980121001', 'SD INPRES NUAJA', 315, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(306, 'INOSENSIA KOPA', 'inosensia.kopa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197106101971061001', 'SD INPRES NUAMURI 2', 316, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(307, 'Benediktus Meka', 'benediktus.meka@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198404101984041001', 'SD INPRES NUANAGA', 317, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(308, 'Amatus Berkhemans Keli', 'amatus.berkhemans.keli@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198003101980031001', 'SD INPRES NUAPU', 318, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(309, 'Mahmud Mau', 'mahmud.mau@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196708101967081001', 'SD INPRES NUATU', 319, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(310, 'Sumarni Ahmad', 'sumarni.ahmad@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197209101972091001', 'SD INPRES NUMBA 1', 320, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(311, 'Samia M. Numba', 'samia.m..numba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196611101966111001', 'SD INPRES NUMBA 2', 321, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(312, 'Regina Uwa', 'regina.uwa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196902101969021001', 'SD INPRES ONEKORE 3', 322, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(313, 'Maria Finsensia Adolfina Jaga', 'maria.finsensia.adolfina.jaga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197809101978091001', 'SD INPRES ONEKORE 4', 323, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(314, 'Mako  Bibiana', 'mako..bibiana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196711101967111001', 'SD INPRES ONEKORE 5', 324, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(315, 'Rahma Syafrudin', 'rahma.syafrudin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198310101983101001', 'SD INPRES ONEKORE 6', 325, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(316, 'Maksimus Papi', 'maksimus.papi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196706101967061001', 'SD INPRES OTOMBAMBA', 326, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(317, 'Reinandus Putra Mbete', 'reinandus.putra.mbete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '199012101990121001', 'SD INPRES PANALATO', 327, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(318, 'Agustinus Rela', 'agustinus.rela@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197912101979121001', 'SD INPRES PASADOO', 328, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(319, 'Asnat Hamid', 'asnat.hamid@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196908101969081001', 'SD INPRES PAUPANDA 1', 329, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(320, 'Abdullah Gasim', 'abdullah.gasim@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD INPRES PAUPANDA 2', 330, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:04:59'),
(321, 'Nurhayati Syahrir Noning', 'nurhayati.syahrir.noning@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197111101971111001', 'SD INPRES PAUPANDA 3', 331, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(322, 'Petronella  Ia', 'petronella..ia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196901101969011001', 'SD INPRES PUUDHOMBO', 332, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(323, 'Vincensius Fererius Eo', 'vincensius.fererius.eo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196704101967041001', 'SD INPRES PUUKUNGU', 333, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(324, 'Vivin Salfiah', 'vivin.salfiah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198601101986011001', 'SD INPRES PUUPAU', 334, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(325, 'Marius Oskar Moni Pao', 'marius.oskar.moni.pao@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198107101981071001', 'SD INPRES RAAWEKA', 335, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(326, 'Yulius Minggu', 'yulius.minggu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197812101978121001', 'SD INPRES RABURIA', 336, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:03'),
(327, 'Rufina Rawi', 'rufina.rawi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198302101983021001', 'SD INPRES RANGGATALO', 337, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(328, 'Ambrosius Bengu', 'ambrosius.bengu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196705101967051001', 'SD INPRES RATESUBA', 338, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(329, 'Maria Goreti Siti', 'maria.goreti.siti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197006101970061001', 'SD INPRES REDA', 339, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(330, 'Rugaiya  Husen', 'rugaiya..husen@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197209101972091001', 'SD INPRES RENDOMAUPANDI', 340, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(331, 'Robertus Roi', 'robertus.roi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197112101971121001', 'SD INPRES ROA', 341, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(332, 'Arni  Yusuf', 'arni..yusuf@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198108101981081001', 'SD INPRES ROJA 2', 342, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(333, 'EDELTRUDIS MBASI', 'edeltrudis.mbasi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '199406101994061001', 'SD INPRES ROJABAI', 343, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(334, 'Yovita Febronia Nona', 'yovita.febronia.nona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196802101968021001', 'SD INPRES ROPA', 344, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(335, 'Elisabeth Wea', 'elisabeth.wea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197003101970031001', 'SD INPRES ROWORENA 2', 345, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(336, 'Herminia Nderu', 'herminia.nderu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197303101973031001', 'SD INPRES SOKOLOO', 346, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(337, 'Vinsensa Gerosa Dadi', 'vinsensa.gerosa.dadi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198208101982081001', 'SD INPRES SOKORIA', 347, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(338, 'Ferdinandus Rengga Ja', 'ferdinandus.rengga.ja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198306101983061001', 'SD INPRES TANARHI', 348, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(339, 'Tersita Mei', 'tersita.mei@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197005101970051001', 'SD INPRES TETANDARA', 349, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(340, 'Petrus Ratu', 'petrus.ratu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198908101989081001', 'SD INPRES TIWEREA', 350, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(341, 'Polikarpus Biro', 'polikarpus.biro@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'SD INPRES WAKA', 351, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(342, 'Haryani  Ambumbipi', 'haryani..ambumbipi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197202101972021001', 'SD INPRES WATUBEWA', 352, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(343, 'Maria Afrida Prisilia Ero', 'maria.afrida.prisilia.ero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196806101968061001', 'SD INPRES WATUJARA', 353, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(344, 'Muhammad Yasin', 'muhammad.yasin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196808101968081001', 'SD INPRES WATUMESI', 354, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(345, 'Petronela Dama', 'petronela.dama@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197505101975051001', 'SD INPRES WATUMOTO', 355, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(346, 'Hermina Bay', 'hermina.bay@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196909101969091001', 'SD INPRES WELAMOSA', 356, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(347, 'Aufrida Yulita Senggo', 'aufrida.yulita.senggo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197811101978111001', 'SD INPRES WOLOARA', 358, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(348, 'Stefanus Gebo', 'stefanus.gebo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197007101970071001', 'SD INPRES WOLOGAI', 359, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(349, 'Maria Goreti Weti', 'maria.goreti.weti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196907101969071001', 'SD INPRES WOLOJITA', 360, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(350, 'Marselinda Emelia Ghea', 'marselinda.emelia.ghea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197507101975071001', 'SD INPRES WOLOKOLI', 361, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(351, 'Marius Alfridus Sudarma Sale Bae', 'marius.alfridus.sudarma.sale.bae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198005101980051001', 'SD INPRES WOLOLA', 362, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(352, 'Ester Gerita Ate', 'ester.gerita.ate@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197709101977091001', 'SD INPRES WOLOMAGE', 363, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(353, 'Fransiskus Yon Gunar', 'fransiskus.yon.gunar@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198408101984081001', 'SD INPRES WOLOOJA 1', 364, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(354, 'Kristina Priyanti Embu', 'kristina.priyanti.embu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197211101972111001', 'SD INPRES WOLOOJA 3', 365, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(355, 'Maria Yuliana Derita', 'maria.yuliana.derita@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197011101970111001', 'SD INPRES WOLOTOPO', 366, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(356, 'Blasius Woda', 'blasius.woda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197003101970031001', 'SD INPRES WOLOWARU 4', 367, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(357, 'Hasnah Wati', 'hasnah.wati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198109101981091001', 'SD INPRES WOLOWARU 5', 368, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(358, 'Hermina Triyanti', 'hermina.triyanti@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197811101978111001', 'SD INPRES WOLOWONA 1', 369, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(359, 'Maria L.Y. Ana Dema', 'maria.l.y..ana.dema@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197109101971091001', 'SD INPRES WOLOWONA 2', 370, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(360, 'Johra', 'johra@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'SD INPRES WONDA', 371, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(361, 'Ester Fonerande', 'ester.fonerande@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197201101972011001', 'SD INPRES WOROJA', 372, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(362, 'Yulitta Tuku', 'yulitta.tuku@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196909101969091001', 'SD INPRES WOROPAPA', 373, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:03'),
(363, 'Rosalia Nalu', 'rosalia.nalu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196807101968071001', 'SD INPRES WUKARIA', 374, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(364, 'Selviana Yulita Mbadhi', 'selviana.yulita.mbadhi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK AEBARA', 375, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(365, 'Gergorius Kema', 'gergorius.kema@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196611101966111001', 'SD KATOLIK AEFEO', 376, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(366, 'Josefina Wio', 'josefina.wio@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196708101967081001', 'SD KATOLIK AEISA', 377, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(367, 'Godehardus Rhigo', 'godehardus.rhigo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK AEKORO', 378, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(368, 'Dionisius Senja', 'dionisius.senja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK AEWORA', 379, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(369, 'Yakobus Dando', 'yakobus.dando@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK ANARANDA', 380, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(370, 'Maria Arnolda Daso', 'maria.arnolda.daso@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198008101980081001', 'SD KATOLIK ASE', 381, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(371, 'Frederikus Mari', 'frederikus.mari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198308101983081001', 'SD KATOLIK BOAFEO', 382, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(372, 'Dionisius Sema', 'dionisius.sema@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196702101967021001', 'SD KATOLIK BUUBEI', 383, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(373, 'Martha Ero', 'martha.ero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196901101969011001', 'SD KATOLIK BUUNGENDA', 384, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(374, 'Apollonia Ere', 'apollonia.ere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK DEDU', 385, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(375, 'Hendrikus Reta Pake', 'hendrikus.reta.pake@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK DETUARA', 386, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(376, 'Maria Goreti Jawa', 'maria.goreti.jawa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK DETUBELA 1', 387, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(377, 'Kletus Wangge', 'kletus.wangge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196609101966091001', 'SD KATOLIK DETUDENU', 388, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(378, 'Sisilia Gidha', 'sisilia.gidha@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD KATOLIK DETUELU', 389, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(379, 'Fransiskus Satu', 'fransiskus.satu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196803101968031001', 'SD KATOLIK DETUKOU', 390, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(380, 'THERESIA SA\'O, S.Ag.', 'theresia.sao.s.ag.@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196707101967071001', 'SD KATOLIK DETUMBAWA', 391, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(381, 'Yoseph Bhasa', 'yoseph.bhasa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197301101973011001', 'SD KATOLIK DETUMBEWA', 392, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(382, 'Frans Xaverius Seni', 'frans.xaverius.seni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196710101967101001', 'SD KATOLIK DETUPERA', 393, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(383, 'Konstantinus M. Ndu', 'konstantinus.m..ndu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK DETUWULU', 394, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(384, 'Simon Sare', 'simon.sare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196708101967081001', 'SD KATOLIK DILE', 395, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(385, 'ALBERTUS AVOLITUS NURAK', 'albertus.avolitus.nurak@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196909101969091001', 'SD KATOLIK EKOAE', 396, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(386, 'FLORENTINA RENYLDA DERE', 'florentina.renylda.dere@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197004101970041001', 'SD KATOLIK EKOLETA', 397, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:00'),
(387, 'Maria Regna Reni', 'maria.regna.reni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196710101967101001', 'SD KATOLIK ENDE 8', 398, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:01'),
(388, 'Yohanes Rana', 'yohanes.rana@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197006101970061001', 'SD KATOLIK FENDO', 399, NULL, NULL, 1, '2026-08-21 09:03:50', '2026-08-25 01:05:02'),
(389, 'Mikael Muda', 'mikael.muda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197001101970011001', 'SD KATOLIK FUNGAPANDA', 400, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(390, 'Bernabas Oryes Seda', 'bernabas.oryes.seda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK GANA', 401, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(391, 'Rikardus Paire Bani', 'rikardus.paire.bani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198606101986061001', 'SD KATOLIK GHAIBHABHA', 402, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(392, 'Romanus Repe', 'romanus.repe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196807101968071001', 'SD KATOLIK HANGALANDE', 403, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(393, 'Erlensius Sari', 'erlensius.sari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197803101978031001', 'SD KATOLIK JOGE', 404, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(394, 'Maria Lusia W W. San Putri', 'maria.lusia.w.w..san.putri@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK JOPU 1', 405, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(395, 'ANTONIUS ALFIRDE TENCE', 'antonius.alfirde.tence@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196805101968051001', 'SD KATOLIK JOPU 2', 406, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(396, 'Maria Hortensia Mida', 'maria.hortensia.mida@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'SD KATOLIK JOPU 3', 407, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(397, 'Helena  Wonga', 'helena..wonga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197006101970061001', 'SD KATOLIK KAMUBHEKA', 408, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(398, 'Anselmus Nove', 'anselmus.nove@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198211101982111001', 'SD KATOLIK KANGANARA', 409, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(399, 'Oktavia Anita Lero', 'oktavia.anita.lero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198307101983071001', 'SD KATOLIK KEDO', 410, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(400, 'Anastasia Pendega', 'anastasia.pendega@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK KEDOGAJA', 411, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(401, 'Ignasius Irawan', 'ignasius.irawan@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196702101967021001', 'SD KATOLIK KEKADORI', 412, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(402, 'Oswaldus Romanus Juma', 'oswaldus.romanus.juma@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK KEKAJODHO', 413, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(403, 'Maria Magdalena Bea', 'maria.magdalena.bea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197111101971111001', 'SD KATOLIK KEKANDERE 1', 414, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(404, 'Amandus Mbei', 'amandus.mbei@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196711101967111001', 'SD KATOLIK KEKANDERE 2', 415, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(405, 'Filarius Wangga', 'filarius.wangga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK KEKASEWA', 416, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(406, 'Yuliana Nona', 'yuliana.nona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK KEKAWII', 417, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(407, 'Florida Hartini Laru', 'florida.hartini.laru@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197603101976031001', 'SD KATOLIK KOANARA', 418, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(408, 'Anastasia Ie', 'anastasia.ie@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196704101967041001', 'SD KATOLIK KOMBANDARU', 419, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(409, 'Regina E.p. Resi', 'regina.e.p..resi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK KOMBO', 420, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(410, 'Yasintha Utha', 'yasintha.utha@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196710101967101001', 'SD KATOLIK KURULIMBU', 421, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(411, 'Firimina Bhanda', 'firimina.bhanda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196611101966111001', 'SD KATOLIK LAINILA', 422, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(412, 'Florentina  Malo', 'florentina..malo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197104101971041001', 'SD KATOLIK LANDOKURA', 423, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(413, 'Christina Maria Wensy', 'christina.maria.wensy@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197010101970101001', 'SD KATOLIK LIAKAMBA', 424, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(414, 'Kristina Adelheid Noe', 'kristina.adelheid.noe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK LIKANAKA', 425, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(415, 'Yovita Gaudensia Moi', 'yovita.gaudensia.moi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK LOBONIKI', 426, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(416, 'Yoseph  Oba', 'yoseph..oba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196608101966081001', 'SD KATOLIK LOKAOJA', 427, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(417, 'Magdalena Piri', 'magdalena.piri@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196711101967111001', 'SD KATOLIK LOKOBOKO', 428, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(418, 'Maria Esther Pala', 'maria.esther.pala@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197710101977101001', 'SD KATOLIK MAGEKOBA', 429, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(419, 'Rokus Ndena', 'rokus.ndena@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196708101967081001', 'SD KATOLIK MAGENGURA', 430, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `nip`, `sekolah`, `sekolah_id`, `phone`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(420, 'Sr. M Soviani-Katharina Kewa', 'sr..m.soviani-katharina.kewa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK MARSUDIRINI', 431, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(421, 'Bibiana Bima', 'bibiana.bima@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197111101971111001', 'SD KATOLIK MAUKARO', 432, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(422, 'Yeremias Radja', 'yeremias.radja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK MBAKAONDO', 433, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(423, 'Nikolaus Pani', 'nikolaus.pani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD KATOLIK MBOMBA', 434, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(424, 'Maria Antonia Rona', 'maria.antonia.rona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196908101969081001', 'SD KATOLIK MONDO', 435, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(425, 'Nikolaus Wake', 'nikolaus.wake@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196801101968011001', 'SD KATOLIK MUKUSAKI', 436, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(426, 'OKTAVIANUS MORI', 'oktavianus.mori@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NABE', 437, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(427, 'Sofia Irene', 'sofia.irene@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197405101974051001', 'SD KATOLIK NANGAKEO', 438, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(428, 'Kornelis Kasa', 'kornelis.kasa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197012101970121001', 'SD KATOLIK NANGAMBOA', 439, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(429, 'Paulina Seda', 'paulina.seda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197002101970021001', 'SD KATOLIK NANGAPANDA 1', 440, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(430, 'Mitang Yohana Uhung', 'mitang.yohana.uhung@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NAZARETH ENDE', 441, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(431, 'Miltidis Primitifa', 'miltidis.primitifa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NDETUKUNE', 442, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(432, 'SEBASTIANUS RAPA', 'sebastianus.rapa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NDONA 1', 443, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(433, 'Theodosia Ndole', 'theodosia.ndole@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197203101972031001', 'SD KATOLIK NDONA 2', 444, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(434, 'Maria Anastasia Mbadhi Derosari', 'maria.anastasia.mbadhi.derosari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198603101986031001', 'SD KATOLIK NDUARIA', 445, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(435, 'Maria Veneranda Wangu', 'maria.veneranda.wangu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SD KATOLIK NGALUPOLO', 446, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(436, 'Sarni', 'sarni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NGEBONDANA', 447, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(437, 'Yulia Ota', 'yulia.ota@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NGGELA 1', 448, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(438, 'Bernardus Paso', 'bernardus.paso@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197012101970121001', 'SD KATOLIK NGGESADETU', 449, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(439, 'Yuvensia Jilo, S.Pd', 'yuvensia.jilo.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197210101972101001', 'SD KATOLIK NIDA', 450, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:03'),
(440, 'Hyronimus Mewa', 'hyronimus.mewa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196801101968011001', 'SD KATOLIK NIOPANDA', 451, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(441, 'Alfonsa Gaudensia Mia', 'alfonsa.gaudensia.mia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK NIRANANGA', 452, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(442, 'Judius Gawe', 'judius.gawe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197112101971121001', 'SD KATOLIK NUABOSI', 453, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(443, 'Paulinus Foni', 'paulinus.foni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196706101967061001', 'SD KATOLIK NUAMULU', 454, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(444, 'Yustina Masem', 'yustina.masem@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196809101968091001', 'SD KATOLIK NUAMURI 1', 455, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:03'),
(445, 'Maria Sovia Lin', 'maria.sovia.lin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197312101973121001', 'SD KATOLIK NUAULU', 456, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(446, 'Kanisius Wangge', 'kanisius.wangge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197403101974031001', 'SD KATOLIK NUAWIKA', 457, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(447, 'Benediktus Deo', 'benediktus.deo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD KATOLIK NUMBA', 458, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(448, 'Daniel Se', 'daniel.se@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197006101970061001', 'SD KATOLIK OKA', 459, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(449, 'Fransiska Irma', 'fransiska.irma@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'SD KATOLIK ONEKORE 1', 460, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(450, 'MARIA GAUDENSIANA NABA KALOHU', 'maria.gaudensiana.naba.kalohu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK ONEKORE 2', 461, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(451, 'Bernardus Pude Kerans', 'bernardus.pude.kerans@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196710101967101001', 'SD KATOLIK PAAPINGGA', 462, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(452, 'Yustiana Mande Ombo', 'yustiana.mande.ombo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198505101985051001', 'SD KATOLIK PANAMATA', 463, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:03'),
(453, 'Kamilus Ino', 'kamilus.ino@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196909101969091001', 'SD KATOLIK PAUMERE', 464, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(454, 'Petronela Waha Piran', 'petronela.waha.piran@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196610101966101001', 'SD KATOLIK PAUPIRE', 465, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(455, 'Anastasia Bara', 'anastasia.bara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK PEIBENGA', 466, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(456, 'Maria Susanti Mara', 'maria.susanti.mara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK PEMO 1', 467, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(457, 'Kornelis Krestensius Da Silva Ivan', 'kornelis.krestensius.da.silva.ivan@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197106101971061001', 'SD KATOLIK PEMO 2', 468, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(458, 'Vinsensius Jira', 'vinsensius.jira@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK PISA TANAAU', 469, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(459, 'Marselus Sido', 'marselus.sido@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD KATOLIK PISE', 470, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(460, 'Petrus Rinto Randa', 'petrus.rinto.randa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK PISOMBOPO', 471, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(461, 'Juliana Djuli Kusi', 'juliana.djuli.kusi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196806101968061001', 'SD KATOLIK PORA', 472, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(462, 'Blasius Hide', 'blasius.hide@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197310101973101001', 'SD KATOLIK PUUBHETO', 473, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(463, 'Reinaldis Lena Mukin', 'reinaldis.lena.mukin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197003101970031001', 'SD KATOLIK PUUFEO', 474, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(464, 'Vitalis Lasa', 'vitalis.lasa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196904101969041001', 'SD KATOLIK PUUKOU', 475, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(465, 'Ferdinandus Dei', 'ferdinandus.dei@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK PUUTUGA', 476, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(466, 'Maria Lele', 'maria.lele@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198210101982101001', 'SD KATOLIK RANGA', 477, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(467, 'Adrianus Sedo', 'adrianus.sedo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198103101981031001', 'SD KATOLIK RANOKOLO', 478, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(468, 'Getrudis Subertiana Sama', 'getrudis.subertiana.sama@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196906101969061001', 'SD KATOLIK RATEMBUE', 479, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(469, 'Teresia Saja', 'teresia.saja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196710101967101001', 'SD KATOLIK RATERORU', 480, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(470, 'Hendrikus Toda', 'hendrikus.toda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD KATOLIK REKA', 481, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(471, 'Maria Sangposa Tiga', 'maria.sangposa.tiga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196701101967011001', 'SD KATOLIK ROGA', 482, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(472, 'Maria Angela Kartini', 'maria.angela.kartini@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197004101970041001', 'SD KATOLIK ROWOREKE 1', 483, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(473, 'Yustina Sifa', 'yustina.sifa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197801101978011001', 'SD KATOLIK ROWOREKE 2', 484, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:03'),
(474, 'Agustina Patrisia Anita Goma', 'agustina.patrisia.anita.goma@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK SAGA', 485, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(475, 'Vitalis Leta', 'vitalis.leta@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198212101982121001', 'SD KATOLIK SEULAKO', 486, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(476, 'Martinus Meta', 'martinus.meta@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196805101968051001', 'SD KATOLIK SOKORIA 1', 487, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(477, 'Herman Sara', 'herman.sara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK SOKORIA 2', 488, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(478, 'Margaretha Ardiana Dua Heret', 'margaretha.ardiana.dua.heret@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198106101981061001', 'SD KATOLIK ST AMBROSIUS ENDE 6', 489, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(479, 'Valentina Ke', 'valentina.ke@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD KATOLIK ST ANTONIUS ENDE 2', 490, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(480, 'VINSENSIUS BANGGO', 'vinsensius.banggo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197103101971031001', 'SD KATOLIK ST THERESIA ENDE 3', 491, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(481, 'Filomena Noi', 'filomena.noi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197311101973111001', 'SD KATOLIK TANAJEA', 492, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(482, 'Ludgardis Jie', 'ludgardis.jie@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK TENDA', 493, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:01'),
(483, 'Hendrika Rae', 'hendrika.rae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197502101975021001', 'SD KATOLIK TOBA', 494, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(484, 'Yoseph Simeon Male', 'yoseph.simeon.male@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WAKA', 496, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:02'),
(485, 'Yustina Mbulu', 'yustina.mbulu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198301101983011001', 'SD KATOLIK WATUKAMBA', 497, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:03'),
(486, 'Adrianus Muda', 'adrianus.muda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197010101970101001', 'SD KATOLIK WATUMITE', 498, NULL, NULL, 1, '2026-08-21 09:03:51', '2026-08-25 01:05:00'),
(487, 'Maria Margareta Alachok Masi', 'maria.margareta.alachok.masi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197110101971101001', 'SD KATOLIK WATUNESO', 499, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(488, 'YOHAKIM EDYSON SERA', 'yohakim.edyson.sera@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WATUNGGERE', 500, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(489, 'Katarina Mare', 'katarina.mare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196803101968031001', 'SD KATOLIK WATURAKA', 501, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(490, 'Katharina Mburhu', 'katharina.mburhu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196803101968031001', 'SD KATOLIK WATUSIPI', 502, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(491, 'Bajo L. Y. M. Darma', 'bajo.l..y..m..darma@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'SD KATOLIK WELAMOSA', 503, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(492, 'Petrus Nggiring', 'petrus.nggiring@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197707101977071001', 'SD KATOLIK WOLOBHETO', 504, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(493, 'Monika Mete', 'monika.mete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197004101970041001', 'SD KATOLIK WOLOFEO', 505, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(494, 'Silvester Lalu', 'silvester.lalu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SD KATOLIK WOLOGAI DETUSOKO', 506, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(495, 'Dionisius Rea', 'dionisius.rea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOGAI ENDE', 507, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(496, 'Gerardus Ghale', 'gerardus.ghale@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SD KATOLIK WOLOGERU', 508, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(497, 'Maria Edelsia Bara', 'maria.edelsia.bara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197010101970101001', 'SD KATOLIK WOLOJITA', 509, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(498, 'Bernardeta Delo', 'bernardeta.delo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOKOTA', 510, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(499, 'Yuventa Katarina Doa', 'yuventa.katarina.doa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197009101970091001', 'SD KATOLIK WOLOLANU', 511, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:03'),
(500, 'MARIA JACINTA DOA', 'maria.jacinta.doa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197007101970071001', 'SD KATOLIK WOLOLELE A', 512, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(501, 'Aurelius G. V. Dorelagu', 'aurelius.g..v..dorelagu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196901101969011001', 'SD KATOLIK WOLOLELE B', 513, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(502, 'Petronella Yanti Rilla', 'petronella.yanti.rilla@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197907101979071001', 'SD KATOLIK WOLOMAGE', 514, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(503, 'Wilhelmina Tido', 'wilhelmina.tido@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198304101983041001', 'SD Katolik Wolomota', 515, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(504, 'Erebertus Samban', 'erebertus.samban@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOMUKU', 516, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(505, 'VINSENSIUS ADRIANUS LAKA', 'vinsensius.adrianus.laka@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLONDOPO 1', 517, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(506, 'Elisabeth Filo', 'elisabeth.filo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198508101985081001', 'SD KATOLIK WOLONDOPO 2', 518, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(507, 'Leonardus Iwa Radja', 'leonardus.iwa.radja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196910101969101001', 'SD KATOLIK WOLOORA', 519, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(508, 'Aloysius Wuring Kelen', 'aloysius.wuring.kelen@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOSAMBI', 520, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(509, 'Thresia Maria Tungga', 'thresia.maria.tungga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197210101972101001', 'SD KATOLIK WOLOSOKO', 521, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(510, 'Paulus Ola', 'paulus.ola@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197008101970081001', 'SD KATOLIK WOLOTOLO', 522, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(511, 'Reni Apristina Lidi', 'reni.apristina.lidi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOTOPO 1', 523, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(512, 'Emilianus F. Henry Wasa', 'emilianus.f..henry.wasa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOTOPO 2', 524, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(513, 'Petrus Pedro Fernandes', 'petrus.pedro.fernandes@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196906101969061001', 'SD KATOLIK WOLOWARU 1', 525, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(514, 'Agustina Eddy Seja Rengga R.M', 'agustina.eddy.seja.rengga.r.m@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197708101977081001', 'SD KATOLIK WOLOWARU 2', 526, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(515, 'Yohana Mbulu', 'yohana.mbulu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SD KATOLIK WOLOWUSU', 527, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(516, 'Yustina Tonda', 'yustina.tonda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196911101969111001', 'SD KATOLIK WONDA', 528, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:03'),
(517, 'Stefanus Weo', 'stefanus.weo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196709101967091001', 'SD KATOLIK WOROMBERA', 529, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(518, 'Siti Sarifah Lukman', 'siti.sarifah.lukman@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198007101980071001', 'SD NEGERI ANAREWA', 530, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(519, 'Maria Leoni Bado', 'maria.leoni.bado@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198106101981061001', 'SD NEGERI DETUBELA 2', 531, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(520, 'Jamaludin', 'jamaludin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197906101979061001', 'SD NEGERI EKOREKO', 532, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(521, 'Ibrahim Sulaiman Wonga', 'ibrahim.sulaiman.wonga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD NEGERI ENDE 1', 533, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(522, 'Sisilia Sombo', 'sisilia.sombo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198108101981081001', 'SD NEGERI ENDE 5', 534, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(523, 'Martha Lede', 'martha.lede@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197006101970061001', 'SD NEGERI IPI', 535, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(524, 'Marianus  Sala', 'marianus..sala@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196904101969041001', 'SD NEGERI KEDEBODU', 536, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(525, 'Agustina Sia', 'agustina.sia@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198304101983041001', 'SD NEGERI KEDOBORO', 537, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(526, 'Emiliana Bara', 'emiliana.bara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197112101971121001', 'SD NEGERI KOBALEBA', 538, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(527, 'Matias Mite', 'matias.mite@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198706101987061001', 'SD NEGERI KURUPOKE', 539, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(528, 'Mahatir Tuto Gesak', 'mahatir.tuto.gesak@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198602101986021001', 'SD NEGERI LELU', 540, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(529, 'Sisilia Pano', 'sisilia.pano@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'SD NEGERI MALAARA', 541, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(530, 'Heribertus Siga', 'heribertus.siga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198507101985071001', 'SD NEGERI MARANUA', 542, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(531, 'Sauda Yaali', 'sauda.yaali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD NEGERI MAUNGGORA', 543, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(532, 'KORNELIS WANDA', 'kornelis.wanda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196911101969111001', 'SD NEGERI MOKEASA', 544, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(533, 'Mujibun Bata', 'mujibun.bata@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '199112101991121001', 'SD NEGERI MOLEKELISAMBA', 545, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(534, 'Juardin Jewa', 'juardin.jewa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198411101984111001', 'SD NEGERI MOLETEBOSAMA', 546, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(535, 'Arkadius Rua', 'arkadius.rua@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197009101970091001', 'SD NEGERI MOLUTANGGA', 547, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(536, 'Agustinus Wempi', 'agustinus.wempi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196608101966081001', 'SD NEGERI NUSANGGALA', 548, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(537, 'Paulus Pipa', 'paulus.pipa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197807101978071001', 'SD NEGERI OJA', 549, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(538, 'Ruslin', 'ruslin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196901101969011001', 'SD NEGERI PUUTARA', 550, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(539, 'Matias Tani', 'matias.tani@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'SD NEGERI RATENGGOJI', 551, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(540, 'Jamilah H. Ali', 'jamilah.h..ali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197202101972021001', 'SD NEGERI ROJA 1', 552, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(541, 'Abdullah Hasan', 'abdullah.hasan@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198110101981101001', 'SD NEGERI ROJA 3', 553, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:04:59'),
(542, 'PRAYAWATI', 'prayawati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197303101973031001', 'SD NEGERI ROJA 6', 554, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(543, 'Marselinus Sae', 'marselinus.sae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198202101982021001', 'SD NEGERI RUTU JEJA', 555, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(544, 'Yohana Moda', 'yohana.moda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197812101978121001', 'SD NEGERI SARELAKA', 556, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(545, 'Huronymus Rigo', 'huronymus.rigo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196802101968021001', 'SD NEGERI SOGOROGA', 557, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(546, 'Sophia Seno', 'sophia.seno@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197004101970041001', 'SD NEGERI TURUNALU', 558, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(547, 'Sofia Sohro', 'sofia.sohro@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198107101981071001', 'SD NEGERI UMANUBA', 559, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(548, 'Ifrisal Husen', 'ifrisal.husen@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198402101984021001', 'SD NEGERI WATUBARA', 560, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(549, 'Veronika Ese', 'veronika.ese@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD NEGERI WIWIPEMO', 561, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(550, 'Yohanes Dapi Tibo', 'yohanes.dapi.tibo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196809101968091001', 'SD NEGERI WOIMITE', 562, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(551, 'Anastasia Bunga', 'anastasia.bunga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197912101979121001', 'SD NEGERI WOLOARA', 563, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(552, 'Jamia Sa', 'jamia.sa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197106101971061001', 'SD NEGERI WOLOGAWI', 564, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(553, 'Rusminah Raya Bewu', 'rusminah.raya.bewu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196910101969101001', 'SD NEGERI WOLOHEPO', 565, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(554, 'Yohanes Dua', 'yohanes.dua@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198001101980011001', 'SD NEGERI WOLOMONI', 566, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(555, 'Yustina Renge', 'yustina.renge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198504101985041001', 'SD NEGERI WOLONIO', 567, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:03'),
(556, 'David Seu', 'david.seu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198007101980071001', 'SD NEGERI WOLOOJA 2', 568, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:00'),
(557, 'Kristina Yuliana Yanti Tea', 'kristina.yuliana.yanti.tea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198707101987071001', 'SD NEGERI WOLOWARU 3', 569, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(558, 'Nursa\'adah', 'nursaadah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SD SWASTA MUHAMMADYAH ENDE', 570, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(559, 'Magdalena Bhete', 'magdalena.bhete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'SDN NAKAWARA', 571, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:01'),
(560, 'YANUARIUS A. RENGGA', 'yanuarius.a..rengga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '199401101994011001', 'SDN ULU DALA', 572, NULL, NULL, 1, '2026-08-21 09:03:52', '2026-08-25 01:05:02'),
(561, 'SIMPLIANA BEDHA', 'simpliana.bedha@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197502101975021001', 'SKB KABUPATEN ENDE', 573, NULL, NULL, 1, '2026-08-21 09:14:52', '2026-08-25 01:05:02'),
(562, 'Marcellinus Ricardus Rango', 'marcellinus.ricardus.rango@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196807101968071001', 'SMP NEGERI 2 ENDE', 574, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(563, 'Dominikus Dhadjo', 'dominikus.dhadjo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197206101972061001', 'SMP NEGERI 2 ENDE SELATAN', 575, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(564, 'Stefanus Budiman', 'stefanus.budiman@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197107101971071001', 'SMP NEGERI 2 MAUROLE', 576, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(565, 'Kusbini', 'kusbini@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198502101985021001', 'SMP NEGERI 2 NANGAPANDA', 577, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(566, 'Amin Aserakal', 'amin.aserakal@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196908101969081001', 'SMP NEGERI 2 NDONA', 578, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(567, 'Maria Yasintha Yosefina Suku', 'maria.yasintha.yosefina.suku@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196707101967071001', 'SMP NEGERI 2 WOLOWARU', 579, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(568, 'Antonius Raja', 'antonius.raja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197301101973011001', 'SMP NEGERI 3 ENDE', 580, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(569, 'Melkiades Anse', 'melkiades.anse@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197901101979011001', 'SMP NEGERI 3 NANGAPANDA', 581, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(570, 'Yustina Ida Oba', 'yustina.ida.oba@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197512101975121001', 'SMP NEGERI 3 NDONA', 582, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:03'),
(571, 'Anianus Oda', 'anianus.oda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197805101978051001', 'SMP NEGERI 3 WOLOWARU', 583, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(572, 'Ignatius Loyola Nangge', 'ignatius.loyola.nangge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196707101967071001', 'SMP NEGERI 4 NANGAPANDA', 584, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(573, 'Yoseph Mbulu', 'yoseph.mbulu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196802101968021001', 'SMP NEGERI 4 WOLOWARU', 585, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(574, 'Frederikus Peja Jeloya', 'frederikus.peja.jeloya@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197110101971101001', 'SMP NEGERI 5 NANGAPANDA', 586, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(575, 'Kamarudin Pua Geno', 'kamarudin.pua.geno@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197205101972051001', 'SMP NEGERI 5 WOLOWARU', 587, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(576, 'Kristoforus Sale', 'kristoforus.sale@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197901101979011001', 'SMP NEGERI 6 NANGAPANDA', 588, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(577, 'Fandi Ibrahim Ali', 'fandi.ibrahim.ali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198709101987091001', 'SMP NEGERI 7 NANGAPANDA', 589, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(578, 'Samsul Bahrim', 'samsul.bahrim@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198008101980081001', 'SMP NEGERI 8 NANGAPANDA', 590, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(579, 'Andreas Dari', 'andreas.dari@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197412101974121001', 'SMP NEGERI AEWORA', 591, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(580, 'Sisilia Ladu,S.Pd', 'sisilia.ladu.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197306101973061001', 'SMP NEGERI DETUKELI', 592, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(581, 'Maria Margaretha Tea', 'maria.margaretha.tea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197610101976101001', 'SMP NEGERI DETUNGGALI', 593, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(582, 'Yuliana Ngatu', 'yuliana.ngatu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197307101973071001', 'SMP NEGERI EKOAE', 594, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(583, 'Damianus Modestus Du', 'damianus.modestus.du@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198207101982071001', 'SMP NEGERI INE PARE', 595, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(584, 'Agustina Dobe', 'agustina.dobe@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197605101976051001', 'SMP NEGERI MAUKARO', 596, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(585, 'Anastasia Mbare', 'anastasia.mbare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197304101973041001', 'SMP NEGERI MAUTENDA', 597, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(586, 'Yohanes Berhmans Laka Pati', 'yohanes.berhmans.laka.pati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197904101979041001', 'SMP NEGERI PANCASILA PORA', 598, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(587, 'Hermina Wonga', 'hermina.wonga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198112101981121001', 'SMP NEGERI SATU ATAP AEREA', 599, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(588, 'Laurensius Nggonggo', 'laurensius.nggonggo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197010101970101001', 'SMP NEGERI SATU ATAP DETUBELO', 600, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(589, 'Husein', 'husein@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198412101984121001', 'SMP NEGERI SATU ATAP EKOREKO', 601, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(590, 'Donatus Emanuel Kelly', 'donatus.emanuel.kelly@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198212101982121001', 'SMP NEGERI SATU ATAP KOAWENA', 602, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(591, 'Yohanes Robinson', 'yohanes.robinson@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197705101977051001', 'SMP NEGERI SATU ATAP LIGALEJO', 603, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(592, 'Xaferius Roy Ora', 'xaferius.roy.ora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198003101980031001', 'SMP NEGERI SATU ATAP MUNDINGGASA', 604, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(593, 'Angelus Donatus Wadhi', 'angelus.donatus.wadhi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197603101976031001', 'SMP NEGERI SATU ATAP NGALUROGA', 605, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(594, 'Paulina Bedhu', 'paulina.bedhu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197412101974121001', 'SMP NEGERI SATU ATAP NGGEMO', 606, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(595, 'Servasius Lefu', 'servasius.lefu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197501101975011001', 'SMP NEGERI SATU ATAP NUAMURI 2', 607, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(596, 'Fransiskus Pape', 'fransiskus.pape@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197201101972011001', 'SMP NEGERI SATU ATAP NUAPU', 608, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(597, 'Wilhelmus Mbiru Mali', 'wilhelmus.mbiru.mali@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SMP NEGERI SATU ATAP PASADOO', 609, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(598, 'Maria Elisabet Mete', 'maria.elisabet.mete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197208101972081001', 'SMP NEGERI SATU ATAP RABURIA', 610, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(599, 'Christoforus Yosep Leta Lengo', 'christoforus.yosep.leta.lengo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198503101985031001', 'SMP NEGERI SATU ATAP RATENGGOJI', 611, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(600, 'Tomas Nggae', 'tomas.nggae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196904101969041001', 'SMP NEGERI SATU ATAP SOKOLOO', 612, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(601, 'Yohanes Satu', 'yohanes.satu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196907101969071001', 'SMP NEGERI SATU ATAP TURUNALU', 613, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(602, 'Theresia Sara', 'theresia.sara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198501101985011001', 'SMP NEGERI SATU ATAP WOLOARA', 614, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(603, 'Silvester Sama', 'silvester.sama@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196910101969101001', 'SMP NEGERI SATU ATAP WOLOGAI', 615, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(604, 'Veronika Lero', 'veronika.lero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197808101978081001', 'SMP NEGERI SEKOLENGO', 617, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(605, 'Fransiskus Xaverius Karo', 'fransiskus.xaverius.karo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198009101980091001', 'SMP NEGERI SOKORIA', 618, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(606, 'Lambertus Delo', 'lambertus.delo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197409101974091001', 'SMP NEGERI TANADAKI', 619, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(607, 'Marta Gala', 'marta.gala@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197312101973121001', 'SMP NEGERI TONDANDORA', 620, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(608, 'Yaret Sanderia Nalle', 'yaret.sanderia.nalle@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197601101976011001', 'SMP KRISTEN ENDE', 621, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(609, 'Hasrul Azwar S.S Londra', 'hasrul.azwar.s.s.londra@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP MUHAMMADIYAH ENDE', 622, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(610, 'Siti Anisah Djenab', 'siti.anisah.djenab@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197005101970051001', 'SMP SWASTA ADHYAKSA', 623, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(611, 'MARKARIUS NARA MBETE', 'markarius.nara.mbete@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA DONA MART', 624, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(612, 'Chasiah Nur H. Hasan', 'chasiah.nur.h.hasan@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197002101970021001', 'SMP SWASTA ISLAM MUTHMAINNAH', 626, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(613, 'Bonefasius Dato', 'bonefasius.dato@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197404101974041001', 'SMP SWASTA KATOLIK CHRISTOREGI', 627, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(614, 'Fransiskus Seto', 'fransiskus.seto@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'SMP SWASTA KATOLIK DETUKELI', 628, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(615, 'Maria V. Y. Sayang', 'maria.v.y.sayang@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198509101985091001', 'SMP SWASTA KATOLIK EMANUEL MAUTENDA', 629, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:01'),
(616, 'Ferdinandus Bate', 'ferdinandus.bate@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196901101969011001', 'SMP SWASTA KATOLIK INEMETE', 630, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(617, 'Sr. M. Sylviana, OSF', 'sr.m.sylviana.osf@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK MARSUDIRINI', 631, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(618, 'Petrus Canisius Benge', 'petrus.canisius.benge@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197207101972071001', 'SMP SWASTA KATOLIK MONI', 632, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(619, 'Yoseline Lidia Ngeni', 'yoseline.lidia.ngeni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198103101981031001', 'SMP SWASTA KATOLIK NIRMALA JOPU', 633, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `nip`, `sekolah`, `sekolah_id`, `phone`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
(620, 'Herlina Helena Simanjorang', 'herlina.helena.simanjorang@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK SANTA URSULA ENDE', 634, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(621, 'Fabianus Siga', 'fabianus.siga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK ST ALOYSIUS WOLOTOPO', 635, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(622, 'Gregorius Benge Deo', 'gregorius.benge.deo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK WOLOJITA', 636, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(623, 'Petrus Yosef Juma', 'petrus.yosef.juma@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK WOLOTOLO', 637, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(624, 'Vinsensius Demong', 'vinsensius.demong@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197312101973121001', 'SMP SWASTA KATOLIK WOLOWARU', 638, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(625, 'Petronela Kando', 'petronela.kando@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KATOLIK YOS SUDARSO', 639, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(626, 'ELIAS HARIYANTO DEGOT', 'elias.hariyanto.degot@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA KELIMUTU', 640, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(627, 'Prudentiana Ngindang', 'prudentiana.ngindang@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA MADANI NDONDO', 641, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:02'),
(628, 'Bernadus Reda', 'bernadus.reda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA NUSANTARA', 642, NULL, NULL, 1, '2026-08-21 09:14:53', '2026-08-25 01:05:00'),
(629, 'Agustina Mbipa', 'agustina.mbipa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197007101970071001', 'SMP SWASTA REWARANGGA', 643, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(630, 'Yohanes Gesi', 'yohanes.gesi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'SMP SWASTA SINAR PELITA', 644, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(631, 'Grasiana Tepa', 'grasiana.tepa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197303101973031001', 'SMP SWASTA TARUNA DESA', 645, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(632, 'Advin Adrianus Illu', 'advin.adrianus.illu@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'SMP SWASTA TRI DHARMA', 646, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(633, 'Veronika Suriyani Didja', 'veronika.suriyani.didja@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196802101968021001', 'SMPK ST. ANTONIUS NDONA', 647, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(634, 'Benediktus Sengi', 'benediktus.sengi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197411101974111001', 'SMPN HANGALANDE', 648, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(635, 'Markus Aku', 'markus.aku@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197508101975081001', 'SMPN KELIWUMBU', 649, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(636, 'Darius Bae', 'darius.bae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196705101967051001', 'SMPN SATAP WOLOOJA 2', 650, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(637, 'EDELTRUDIS HIRMINA ERO', 'edeltrudis.hirmina.ero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'TKS ANAK YESUS JOPU', 651, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(638, 'SEBASTIANA SINA', 'sebastiana.sina@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'TKS BUNGA BANGSA', 652, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(639, 'Tati Haryati Tumalang', 'tati.haryati.tumalang@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198105101981051001', 'TKS CHRISTOREGI', 653, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(640, 'LIDIA GAA', 'lidia.gaa@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196804101968041001', 'TKS DEWI SARTIKA PORA', 654, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(641, 'Dorce Emilia Esy Dee', 'dorce.emilia.esy.dee@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196811101968111001', 'TKS DHARMA WANITA ENDE', 655, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(642, 'SUMARYANI NONA', 'sumaryani.nona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS DHARMA WANITA MBULILOO', 656, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(643, 'SUMARNI HASYIM', 'sumarni.hasyim@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS DHARMA WANITA NANGAPANDA', 657, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(644, 'SATO IGNASIUS', 'sato.ignasius@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196702101967021001', 'TKS DHARMA WANITA TENDA', 658, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(645, 'SEDU VALENTINUS', 'sedu.valentinus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196712101967121001', 'TKS DHARMA WANITA WOLOJITA', 659, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(646, 'SEHA ALWI', 'seha.alwi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ISLAM TARBIYAH', 660, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(647, 'MARSELINI MURNI', 'marselini.murni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196612101966121001', 'TKS KAPOLANDO', 661, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(648, 'Rosana Delvina Pora', 'rosana.delvina.pora@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS LEPEMBUSU', 663, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(649, 'HABIBA YASIN', 'habiba.yasin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196812101968121001', 'TKS MANUBARA', 664, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(650, 'PURSOFIA MADA', 'pursofia.mada@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS MANUSAMA WOLOHEPO', 665, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(651, 'MARDIANTI MANSUR', 'mardianti.mansur@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS MARDATILLAH', 666, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(652, 'MARTA NONA', 'marta.nona@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197105101971051001', 'TKS MARIA VIRGO 1', 667, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(653, 'nur sarina mandar', 'nur.sarina.mandar@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS MAUBASA', 668, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(654, 'Ngaya Hamsah', 'ngaya.hamsah@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197204101972041001', 'TKS MUSLIMAT NU FATIMAH AZZAHRAH', 669, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(655, 'LUSIA MANE, S.Pd', 'lusia.mane.s.pd@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS NIRMALA', 670, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(656, 'Fransiska Y. K. Pare', 'fransiska.y.k.pare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197806101978061001', 'TKS PUTRA DUNGGA', 671, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(657, 'NUR AINI ABDUL KADIR', 'nur.aini.abdul.kadir@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS REDODORI', 672, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(658, 'ABDULLAH IBRAHIM', 'abdullah.ibrahim@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197212101972121001', 'TKS RENDORATERUA', 673, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:04:59'),
(659, 'Sefriani Henderina Bara', 'sefriani.henderina.bara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198209101982091001', 'TKS RHERHEJA 2', 674, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(660, 'ANASTASIA NODA BELO', 'anastasia.noda.belo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197004101970041001', 'TKS RINDIWAWO', 675, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(661, 'SAVERINUS WILFRIDUS GADI', 'saverinus.wilfridus.gadi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SANDHY PUTRA', 676, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(662, 'MARTHA WINI MALO', 'martha.wini.malo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196810101968101001', 'TKS SANTA AGNES', 677, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(663, 'KATARINA NDOY', 'katarina.ndoy@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SANTA HELEN WOLOWARU', 678, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(664, 'EMILIANA EMI', 'emiliana.emi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'TKS SANTA YASINTHA', 680, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(665, 'ATINA SYAHRIR', 'atina.syahrir@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197312101973121001', 'TKS SARE ORHA', 681, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(666, 'NURIA PUA LALO', 'nuria.pua.lalo@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATAP SDN PUUTARA', 682, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(667, 'KLEMENSIA ARIFIN', 'klemensia.arifin@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATAP ST. PAULUS MUKUSAKI', 683, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(668, 'KRISTINA MINCE', 'kristina.mince@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATAP WOLOKOLI', 684, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(669, 'Christiana Bida', 'christiana.bida@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196912101969121001', 'TKS SATOJOTO', 685, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(670, 'Maria Delvina Tuga Bunga', 'maria.delvina.tuga.bunga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATU ATAP DETUBELO', 686, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(671, 'ERNESTA NDARO', 'ernesta.ndaro@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATU ATAP KEKAWII', 687, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(672, 'ELISABET LAWI', 'elisabet.lawi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SATU ATAP KOMBO', 688, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(673, 'Elsinta Sukmawati', 'elsinta.sukmawati@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '198409101984091001', 'TKS SATU ATAP NUMBA 1', 689, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(674, 'Rosadalima Wedhi', 'rosadalima.wedhi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. ARNOLDUS YANSEN MBOMBA', 690, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(675, 'Yulita Ester Jea', 'yulita.ester.jea@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. FRANSISKUS XAVERIUS WOLOTOLO', 691, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(676, 'Martina Anu Laga', 'martina.anu.laga@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196608101966081001', 'TKS ST. MARIA NANGAMBOA', 692, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(677, 'MARIANA HILDEGUNDA RAE', 'mariana.hildegunda.rae@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. MARTHA MOLUTANGGA', 694, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(678, 'MARIA MAGDALENA DA GOMES', 'maria.magdalena.da.gomes@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. MARTINUS WATUMITE', 695, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(679, 'MARLINA MITE', 'marlina.mite@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. MIKAEL WOLOLELE B', 696, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(680, 'HILDA PARE', 'hilda.pare@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. SISILIA AEGANA', 697, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:00'),
(681, 'TRESIA LERO', 'tresia.lero@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS ST. YANUARIUS WONDA', 698, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(682, 'ROFINUS PAULINUS FOLE', 'rofinus.paulinus.fole@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '197002101970021001', 'TKS ST.FRANSISKUS XAVERIUS WOLOTOPO', 699, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:02'),
(683, 'MERLIANA K. HUNGU RIHI', 'merliana.k.hungu.rihi@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', NULL, 'TKS SYALOOM', 700, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(684, 'MARTHA MARA', 'martha.mara@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'kepala_sekolah', '196904101969041001', 'TKS WOLOOJA', 701, NULL, NULL, 1, '2026-08-21 09:14:54', '2026-08-25 01:05:01'),
(685, 'Ahad Abdullah, S.Pd.AUD', 'pengawas.ahad@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196612161987031006', 'PAUD - Nangapanda, Ende, Kotabaru, Maurole, Ende Timur, Ende Tengah, Ende Selatan', 2, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(686, 'Donatus Tato, S.Pd.SD, MM', 'pengawas.donatus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197010091996061001', 'DIKDAS - Detukeli, Wewaria, Ende Tengah, Ende Selatan, Ende Utara', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(687, 'Vitalianus Pio, S.Pd', 'pengawas.vitalianus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196707101996061001', 'DIKDAS - Ende Utara, Ende Tengah', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(688, 'Getrudis Toja, S.Pd.SD', 'pengawas.getrudis@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196811161991042001', 'DIKDAS - Wolojita, Wewaria, Maurole', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(689, 'Seto Agustinus, S.Pd.SD', 'pengawas.seto@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '1968123111989081005', 'DIKDAS - Lio Timur, Kelimutu', 359, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(690, 'Maria Magdalena Tea, S.Pd', 'pengawas.maria.magdalena@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197001302007012010', 'DIKDAS - Wolowaru, Lepembusu, Kelisoke, Kotabaru', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(691, 'Muhammad Rusmin, S.Pd.I', 'pengawas.muhammad@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197810062011011005', 'DIKDAS - Nangapanda, Maukaro, Ende Selatan', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(692, 'Eni Sulistiowati, S.Pd.SD', 'pengawas.eni@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197602062003122007', 'DIKDAS - Ende, Wewaria', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(693, 'Yosefina Fransiska Dhana, S.Pd', 'pengawas.yosefina@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '198206282006042022', 'DIKDAS - Ndona, Wewaria, Detusoko, Kelimutu', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(694, 'Bernadus Gae Longa, M.Pd', 'pengawas.bernadus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196811301998011003', 'DIKDAS - Detusoko, Wewaria, Maukaro, Ende', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(695, 'Arnolda Yuliana Weti, S.Pd', 'pengawas.arnolda@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196907181997022002', 'DIKDAS - Kelimutu, Lepkes, Detusoko, Ende Timur, Ende Tengah', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(696, 'Maria Imakulata Bule, S.Pd', 'pengawas.maria.imakulata@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '196712091999032004', 'DIKDAS - Nangapanda, Ende Utara, Ende Tengah, Pulau Ende, Lepeks, Ndona', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(697, 'Antonius Singga, S.Pd', 'pengawas.antonius@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197206262006041020', 'DIKDAS - Wolojita, Wolowaru, Ndona, Ndona Timur, Ende, Ende Utara, Ende Timur', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(698, 'Martinianus Pegu, M.Pd', 'pengawas.martinianus@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '197411052005021001', 'DIKDAS - Maurole, Kotabaru, Detusoko, Ende Tengah, Ende Timur, Detukeli', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03'),
(699, 'Maria Skolastika Muni, S.Pd', 'pengawas.maria.skolastika@sekolah.com', '$2y$10$cND1JDENQjiniakCbXB6jOit/mPx7w984oilx9sBV2oyjuCxlwui.', 'pengawas', '19860221010012026', 'DIKDAS - Wolowaru, Lio Timur, Ndori, Ndona, Ende Tengah', NULL, NULL, NULL, 1, '2026-08-21 11:18:45', '2026-08-25 01:05:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `album_kegiatan`
--
ALTER TABLE `album_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_sekolah` (`id_sekolah`);

--
-- Indexes for table `catatan_harian`
--
ALTER TABLE `catatan_harian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_sekolah` (`id_sekolah`);

--
-- Indexes for table `dokumen_perangkat`
--
ALTER TABLE `dokumen_perangkat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_sekolah` (`id_sekolah`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `jadwal_mengajar`
--
ALTER TABLE `jadwal_mengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_mapel` (`id_mapel`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perangkat` (`id_perangkat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `log_unduhan`
--
ALTER TABLE `log_unduhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perangkat` (`id_perangkat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_mapel` (`kode_mapel`);

--
-- Indexes for table `pengawas_sekolah`
--
ALTER TABLE `pengawas_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_pair` (`pengawas_id`,`sekolah_id`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- Indexes for table `perangkat`
--
ALTER TABLE `perangkat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_mapel` (`id_mapel`),
  ADD KEY `id_kelas` (`id_kelas`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `presensi_guru`
--
ALTER TABLE `presensi_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sekolah` (`id_sekolah`),
  ADD KEY `id_guru` (`id_guru`);

--
-- Indexes for table `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sekolah` (`id_sekolah`),
  ADD KEY `id_guru` (`id_guru`),
  ADD KEY `id_kelas` (`id_kelas`);

--
-- Indexes for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perangkat` (`id_perangkat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `npsn` (`npsn`),
  ADD KEY `kepala_sekolah` (`kepala_sekolah`),
  ADD KEY `pengawas` (`pengawas`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `sekolah_id` (`sekolah_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `log_unduhan`
--
ALTER TABLE `log_unduhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mata_pelajaran`
--
ALTER TABLE `mata_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pengawas_sekolah`
--
ALTER TABLE `pengawas_sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `perangkat`
--
ALTER TABLE `perangkat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sekolah`
--
ALTER TABLE `sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=702;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=700;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `komentar`
--
ALTER TABLE `komentar`
  ADD CONSTRAINT `komentar_ibfk_1` FOREIGN KEY (`id_perangkat`) REFERENCES `perangkat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `komentar_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `log_unduhan`
--
ALTER TABLE `log_unduhan`
  ADD CONSTRAINT `log_unduhan_ibfk_1` FOREIGN KEY (`id_perangkat`) REFERENCES `perangkat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `log_unduhan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pengawas_sekolah`
--
ALTER TABLE `pengawas_sekolah`
  ADD CONSTRAINT `pengawas_sekolah_ibfk_1` FOREIGN KEY (`pengawas_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengawas_sekolah_ibfk_2` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perangkat`
--
ALTER TABLE `perangkat`
  ADD CONSTRAINT `perangkat_ibfk_1` FOREIGN KEY (`id_mapel`) REFERENCES `mata_pelajaran` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perangkat_ibfk_2` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perangkat_ibfk_3` FOREIGN KEY (`id_guru`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `riwayat_status`
--
ALTER TABLE `riwayat_status`
  ADD CONSTRAINT `riwayat_status_ibfk_1` FOREIGN KEY (`id_perangkat`) REFERENCES `perangkat` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_status_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sekolah`
--
ALTER TABLE `sekolah`
  ADD CONSTRAINT `sekolah_ibfk_1` FOREIGN KEY (`kepala_sekolah`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sekolah_ibfk_2` FOREIGN KEY (`pengawas`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
