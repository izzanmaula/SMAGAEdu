-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 09:24 AM
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
-- Database: `smagaedu`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_history`
--

CREATE TABLE `ai_chat_history` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `respons` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `character_traits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`character_traits`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_chat_history`
--

INSERT INTO `ai_chat_history` (`id`, `user_id`, `session_id`, `pesan`, `respons`, `created_at`, `character_traits`) VALUES
(649, 'fadhilmanfa', 27, 'halo saga', 'Hai, Bapak Fadhil! 🙃 Lagi ngapain? Ada yang bisa gue bantu atau cuma pengen curhat soal hidup yang penuh plot twist ini? 🤔', '2025-02-19 17:39:16', NULL),
(650, 'fadhilmanfa', 27, 'plot wis apaan?', 'Bro, plot twist itu kayak hidup kita yang penuh kejutan, nggak tau apa yang bakal terjadi besok! 😂 kayak sinetron, tapi ini real life, bro! 🤯 jadi, harus siap untuk menghadapi apa aja yang datang, gitu. 🙃 fix, hidup itu kayak anime, tapi nggak ada episode terakhir, bro! 🤣', '2025-02-19 17:39:34', NULL),
(651, 'fadhilmanfa', 27, 'haha kamu lucu jirr', 'Makasih, Bapak Fadhil! 🙃 Gue coba membuat hidup lebih menyenangkan, walaupun lowkey hidup itu complicated, tapi gue bakal tetap mencoba membuatnya lebih fun, bro! 😄 Kalau gue berhasil membuat Bapak tertawa, itu fix, gue sudah berhasil melakukan tugas gue hari ini! 🤣', '2025-02-19 17:39:46', NULL),
(652, 'fadhilmanfa', 27, 'halo saga', 'Hai, Bapak Fadhil! 🙃 Lagi ngapain? Ada yang bisa gue bantu atau cuma pengen curhat soal hidup yang penuh plot twist ini? 🤔', '2025-02-19 17:41:37', NULL),
(653, 'fadhilmanfa', 27, 'hmm curhat apa jir bingung', 'Bro, fix! 🤣 Curhat aja, gue duduk manis, sedia mendengar. Apa yang bikin Bapak Fadhil bingung hari ini? Materi pelajaran yang susah, atau mungkin urusan lain? 🤔 Gue ada di sini buat bantu, atau paling tidak, buat nyimak curhatan Bapak 😊', '2025-02-19 17:41:47', NULL),
(654, 'fadhilmanfa', 27, 'hahaha, aman kok', 'Alhamdulillah, bro! 🙏 Jadi, Bapak Fadhil lagi santai aja, nggak ada masalah yang bikin pusing? Fix, itu yang terbaik! 😊 Tapi, kalau Bapak Fadhil pengen ngobrol tentang sesuatu, misalnya tentang strategi mengajar atau cara membuat materi pembelajaran lebih menarik, gue siap bantu, bro! 💡', '2025-02-19 17:41:56', NULL),
(655, 'fadhilmanfa', 27, 'hmm kamu tau lebih dulu telur sama ayam?', 'Bro, ini pertanyaan klasik! 🤣 Telur atau ayam, yang mana dulu? Gue rasa ini pertanyaan yang bikin kita semua pusing, kayak episode filler hidup kita, bro! 🙃 Tapi, kalau kita pikir logis, mungkin ayam dulu, karena ayam bisa bertelur, kan? 🐓🥚 Tapi, pertanyaan ini juga bisa dibalik, bro, karena telur juga bisa menetas jadi ayam, kan? 🤯 Jadi, gue rasa ini pertanyaan yang nggak ada jawabannya, bro, atau mungkin jawabannya adalah \\\"ayam telur\\\" stesso, bro! 😂', '2025-02-19 17:42:13', NULL),
(656, 'fadhilmanfa', 27, 'bjirrr, yang seris ada ga jawabannya?', 'Bro, kayaknya kamu nggak terima jawaban gue, ya? 🤣 Okay,okay, gue coba lagi. Serius, pertanyaan \\\"telur atau ayam\\\" ini adalah contoh klasik dari paradoks, bro. Paradoks adalah situasi di mana ada dua atau lebih statement yang saling bertentangan, tapi sama-sama benar, atau sama-sama salah. Jadi, nggak ada jawaban yang pasti, bro. Ini seperti.fix, tidak ada jawaban yang bisa memuaskan, bro! 🤯 Tapi, kalau kamu ingin tahu, beberapa ilmuwan berpendapat bahwa ayam dulu, karena mereka memiliki bukti fosil yang menunjukkan bahwa ayam modern berevolusi dari burung lain yang sudah ada sebelumnya. Tapi, ini juga masih diperdebatkan, bro! 🐓🔥', '2025-02-19 17:42:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ai_chat_sessions`
--

CREATE TABLE `ai_chat_sessions` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_chat_sessions`
--

INSERT INTO `ai_chat_sessions` (`id`, `user_id`, `title`, `created_at`, `closed_at`) VALUES
(6, '', 'Tentang Percakapan Singkat', '2025-01-26 17:29:11', NULL),
(14, 'fadhilmanfa', 'cek', '2025-02-03 08:02:30', NULL),
(27, 'fadhilmanfa', 'halo saga', '2025-02-19 17:39:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bank_soal`
--

CREATE TABLE `bank_soal` (
  `id` int(11) NOT NULL,
  `ujian_id` int(11) NOT NULL,
  `jenis_soal` enum('pilihan_ganda','uraian') NOT NULL,
  `pertanyaan` text NOT NULL,
  `gambar_soal` varchar(255) DEFAULT NULL,
  `jawaban_a` text DEFAULT NULL,
  `jawaban_b` text DEFAULT NULL,
  `jawaban_c` text DEFAULT NULL,
  `jawaban_d` text DEFAULT NULL,
  `jawaban_benar` varchar(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bank_soal`
--

INSERT INTO `bank_soal` (`id`, `ujian_id`, `jenis_soal`, `pertanyaan`, `gambar_soal`, `jawaban_a`, `jawaban_b`, `jawaban_c`, `jawaban_d`, `jawaban_benar`, `created_at`) VALUES
(369, 40, 'pilihan_ganda', 'Apa yang dimaksud dengan simbiosis?', NULL, 'Interaksi yang merugikan salah satu atau semua organisme yang terlibat', 'Interaksi yang tidak memiliki dampak terhadap organisme yang terlibat', 'Interaksi yang bermanfaat bagi semua organisme yang terlibat', 'Interaksi yang merugikan salah satu organisme, tetapi membantu organisme lain', 'C', '2025-02-16 12:54:16'),
(370, 40, 'pilihan_ganda', 'Simbiosis yang terjadi antara kuda dan jinjan adalah contoh dari simbiosis...', NULL, 'Parasitisme', 'Komensalisme', 'Mutualisme', 'Sintrofisme', 'C', '2025-02-16 12:54:16'),
(371, 40, 'pilihan_ganda', 'Apa yang dimaksud dengan parasitisme?', NULL, 'Interaksi yang merugikan salah satu organisme, tetapi membantu organisme lain', 'Interaksi yang bermanfaat bagi semua organisme yang terlibat', 'Interaksi yang tidak memiliki dampak terhadap organisme yang terlibat', 'Interaksi yang merugikan salah satu atau semua organisme yang terlibat', 'D', '2025-02-16 12:54:16'),
(372, 40, 'pilihan_ganda', 'Simbiosis yang terjadi antara cacing lumpur dan bakteri yang menghasilkan gas metana adalah contoh dari simbiosis...', NULL, 'Parasitisme', 'Komensalisme', 'Mutualisme', 'Sintrofisme', 'C', '2025-02-16 12:54:16'),
(373, 40, 'pilihan_ganda', 'Apa yang dimaksud dengan komensalisme?', NULL, 'Interaksi yang merugikan salah satu atau semua organisme yang terlibat', 'Interaksi yang bermanfaat bagi salah satu organisme, tetapi tidak merugikan organisme lain', 'Interaksi yang tidak memiliki dampak terhadap organisme yang terlibat', 'Interaksi yang merugikan salah satu organisme, tetapi membantu organisme lain', 'B', '2025-02-16 12:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `catatan_guru`
--

CREATE TABLE `catatan_guru` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` varchar(100) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text DEFAULT NULL,
  `file_lampiran` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `catatan_guru`
--

INSERT INTO `catatan_guru` (`id`, `kelas_id`, `guru_id`, `judul`, `konten`, `file_lampiran`, `created_at`) VALUES
(15, 52, 'fadhilmanfa', 'Catatan Pertama', 'Halo', NULL, '2025-02-23 07:59:13');

-- --------------------------------------------------------

--
-- Table structure for table `comment_reactions`
--

CREATE TABLE `comment_reactions` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `emoji` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_reactions`
--

INSERT INTO `comment_reactions` (`id`, `comment_id`, `user_id`, `emoji`, `created_at`) VALUES
(8, 57, 'fadhilmanfa', '😮', '2025-02-17 10:58:43'),
(9, 58, 'fadhilmanfa', '😂', '2025-02-23 07:59:47');

-- --------------------------------------------------------

--
-- Table structure for table `emoji_reactions`
--

CREATE TABLE `emoji_reactions` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `emoji` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emoji_reactions`
--

INSERT INTO `emoji_reactions` (`id`, `postingan_id`, `user_id`, `emoji`, `created_at`) VALUES
(30, 46, 'fadhilmanfa', '👍', '2025-02-22 03:12:06'),
(31, 49, 'agustina', '👍', '2025-02-23 07:05:53'),
(32, 50, 'fadhilmanfa', '👍', '2025-02-23 07:58:08'),
(33, 50, 'agustina', '👍', '2025-02-23 07:59:24'),
(34, 51, 'agustina', '👍', '2025-02-23 08:07:35'),
(35, 46, 'agustina', '👍', '2025-02-26 03:04:54'),
(36, 45, 'fadhilmanfa', '👍', '2025-02-27 07:49:51'),
(37, 48, 'agustina', '👍', '2025-02-27 08:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `file_soal`
--

CREATE TABLE `file_soal` (
  `id` int(11) NOT NULL,
  `ujian_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `status_processed` enum('pending','processed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `namaLengkap` varchar(100) NOT NULL,
  `namaSebutan` varchar(1000) NOT NULL,
  `jabatan` varchar(1000) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `foto_latarbelakang` varchar(255) DEFAULT NULL,
  `universitas` varchar(100) DEFAULT NULL,
  `sertifikasi1` text DEFAULT NULL,
  `sertifikasi2` text DEFAULT NULL,
  `sertifikasi3` text DEFAULT NULL,
  `publikasi1` text DEFAULT NULL,
  `publikasi2` text DEFAULT NULL,
  `publikasi3` text DEFAULT NULL,
  `proyek1` text DEFAULT NULL,
  `proyek2` text DEFAULT NULL,
  `proyek3` text DEFAULT NULL,
  `pendidikan_s1` varchar(255) DEFAULT NULL,
  `pendidikan_s2` varchar(255) DEFAULT NULL,
  `pendidikan_lainnya` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`id`, `username`, `password`, `namaLengkap`, `namaSebutan`, `jabatan`, `foto_profil`, `foto_latarbelakang`, `universitas`, `sertifikasi1`, `sertifikasi2`, `sertifikasi3`, `publikasi1`, `publikasi2`, `publikasi3`, `proyek1`, `proyek2`, `proyek3`, `pendidikan_s1`, `pendidikan_s2`, `pendidikan_lainnya`) VALUES
(1, 'fadhilmanfa', 'a', 'Muhammad Fadhil Manfa', 'fadhil', 'Staf TU', '67b219d99c9b6_1739725273.jpg', '67b17115c5bea_1739682069.jpg', NULL, 'sas', 'asa', 'asas', '', '', '', '', '', '', 'Universitas Muhammadiyah Surakarta', '', ''),
(2, 'fauzinugroho', 'a', 'Fauzi Nugroho', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'anugerah', 'a', 'Anugerah Mawarti', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'fikofiko', 'a', 'Fiko Novianto', 'fiko', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'joko', 'a', 'Joko Prasaja', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'sularsih', 'a', 'Sularsih', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'tutismp', 'a', 'Dwi Hastuti (SMP)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'agussalam', 'a', 'Agus Salam Hurriyanto', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'nanik', 'a', 'Nanik Sri Winarni', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'dewi', 'a', 'Herlina Dewi Afiati', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'nia', 'a', 'Nia Yuwana', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'intan', 'a', 'Intan Arvin Yunaeni', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'rosida', 'a', 'Rosida Insiyah Rahmah', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'sevi', 'a', 'Nur Seviana Mar\'atus Sholikhah', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'ira', 'a', 'Sri Sumirah Hartini', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'lenni', 'a', 'Lenni Sri Gustina', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'dani', 'a', 'Dani Sukmadewi', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'ituk', 'a', 'Tri Maryuni', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'jayus', 'a', 'Jayus Wijayanto', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'wardah', 'a', 'Wardah Hani Nabila', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'yudha', 'a', 'Aditya Damar Aji Yudha', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'ayundy', 'a', 'Ayundy Anditaningrum', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'annisanurul', 'a', 'Annisa Nurul Sholikah', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'nova', 'a', 'Nova Puspa Sari', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'annisakhoirina', 'a', 'Annisa Khoirina', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'nur', 'a', 'Nur Fariyati', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'tutisma', 'a', 'Dwi Hastuti (SMA)', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_ujian`
--

CREATE TABLE `jawaban_ujian` (
  `id` int(11) NOT NULL,
  `ujian_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `jawaban` varchar(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jawaban_ujian`
--

INSERT INTO `jawaban_ujian` (`id`, `ujian_id`, `siswa_id`, `soal_id`, `jawaban`, `created_at`) VALUES
(85, 40, 11, 369, 'd', '2025-02-16 12:55:54'),
(86, 40, 11, 370, 'c', '2025-02-16 12:55:54'),
(87, 40, 11, 371, 'b', '2025-02-16 12:55:54'),
(88, 40, 11, 372, 'c', '2025-02-16 12:55:54'),
(89, 40, 11, 373, 'c', '2025-02-16 12:55:54'),
(90, 40, 12, 369, 'b', '2025-02-16 15:56:06'),
(91, 40, 12, 370, 'd', '2025-02-16 15:56:06'),
(92, 40, 12, 371, 'b', '2025-02-16 15:56:06'),
(93, 40, 12, 372, 'c', '2025-02-16 15:56:06'),
(94, 40, 12, 373, 'a', '2025-02-16 15:56:06');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `kode_kelas` varchar(10) DEFAULT NULL,
  `nama_kelas` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `guru_id` varchar(50) DEFAULT NULL,
  `mata_pelajaran` varchar(100) DEFAULT NULL,
  `tingkat` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `background_image` varchar(1000) NOT NULL,
  `is_archived` tinyint(1) DEFAULT 0,
  `materi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `kode_kelas`, `nama_kelas`, `deskripsi`, `guru_id`, `mata_pelajaran`, `tingkat`, `created_at`, `background_image`, `is_archived`, `materi`) VALUES
(50, NULL, 'Ilmu Pengetahuan Alam Kelas F', '', 'fadhilmanfa', 'Ilmu Pengetahuan Alam', 'F', '2025-02-15 11:47:43', 'uploads/background/bg_67b2181302001_1739724819.jpg', 0, '[]'),
(51, NULL, 'Fikih Kelas F', '', 'fadhilmanfa', 'Fikih', 'F', '2025-02-15 11:48:09', '', 0, '[]'),
(52, NULL, 'Kemuhammadiyahan Kelas F', '', 'fadhilmanfa', 'Kemuhammadiyahan', 'F', '2025-02-23 07:54:47', 'uploads/background/bg_67bad49d0b75a_1740297373.jpg', 0, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_siswa`
--

CREATE TABLE `kelas_siswa` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `status` enum('pending','active') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas_siswa`
--

INSERT INTO `kelas_siswa` (`id`, `kelas_id`, `siswa_id`, `status`, `created_at`, `is_archived`) VALUES
(343, 50, 11, 'pending', '2025-02-15 11:47:43', 0),
(344, 50, 12, 'pending', '2025-02-15 11:47:43', 0),
(345, 50, 13, 'pending', '2025-02-15 11:47:43', 0),
(346, 50, 14, 'pending', '2025-02-15 11:47:43', 0),
(347, 50, 15, 'pending', '2025-02-15 11:47:43', 0),
(348, 50, 16, 'pending', '2025-02-15 11:47:43', 0),
(349, 50, 17, 'pending', '2025-02-15 11:47:43', 0),
(350, 50, 18, 'pending', '2025-02-15 11:47:43', 0),
(351, 50, 19, 'pending', '2025-02-15 11:47:43', 0),
(352, 50, 20, 'pending', '2025-02-15 11:47:43', 0),
(353, 50, 21, 'pending', '2025-02-15 11:47:43', 0),
(354, 50, 22, 'pending', '2025-02-15 11:47:43', 0),
(355, 50, 23, 'pending', '2025-02-15 11:47:43', 0),
(356, 50, 24, 'pending', '2025-02-15 11:47:43', 0),
(357, 50, 25, 'pending', '2025-02-15 11:47:43', 0),
(358, 50, 26, 'pending', '2025-02-15 11:47:43', 0),
(359, 50, 27, 'pending', '2025-02-15 11:47:43', 0),
(360, 50, 28, 'pending', '2025-02-15 11:47:43', 0),
(361, 50, 29, 'pending', '2025-02-15 11:47:43', 0),
(362, 50, 30, 'pending', '2025-02-15 11:47:43', 0),
(363, 50, 31, 'pending', '2025-02-15 11:47:43', 0),
(364, 50, 32, 'pending', '2025-02-15 11:47:43', 0),
(365, 50, 33, 'pending', '2025-02-15 11:47:43', 0),
(366, 50, 34, 'pending', '2025-02-15 11:47:43', 0),
(367, 50, 35, 'pending', '2025-02-15 11:47:43', 0),
(368, 50, 36, 'pending', '2025-02-15 11:47:43', 0),
(369, 50, 37, 'pending', '2025-02-15 11:47:43', 0),
(370, 50, 38, 'pending', '2025-02-15 11:47:43', 0),
(371, 50, 39, 'pending', '2025-02-15 11:47:44', 0),
(372, 51, 11, 'pending', '2025-02-15 11:48:09', 0),
(373, 51, 12, 'pending', '2025-02-15 11:48:09', 0),
(374, 51, 13, 'pending', '2025-02-15 11:48:09', 0),
(375, 51, 14, 'pending', '2025-02-15 11:48:09', 0),
(376, 51, 15, 'pending', '2025-02-15 11:48:09', 0),
(377, 51, 16, 'pending', '2025-02-15 11:48:09', 0),
(378, 51, 17, 'pending', '2025-02-15 11:48:09', 0),
(379, 51, 18, 'pending', '2025-02-15 11:48:09', 0),
(380, 51, 19, 'pending', '2025-02-15 11:48:09', 0),
(381, 51, 20, 'pending', '2025-02-15 11:48:09', 0),
(382, 51, 21, 'pending', '2025-02-15 11:48:09', 0),
(383, 51, 22, 'pending', '2025-02-15 11:48:09', 0),
(384, 51, 23, 'pending', '2025-02-15 11:48:09', 0),
(385, 51, 24, 'pending', '2025-02-15 11:48:09', 0),
(386, 51, 25, 'pending', '2025-02-15 11:48:09', 0),
(387, 51, 26, 'pending', '2025-02-15 11:48:09', 0),
(388, 51, 27, 'pending', '2025-02-15 11:48:09', 0),
(389, 51, 28, 'pending', '2025-02-15 11:48:09', 0),
(390, 51, 29, 'pending', '2025-02-15 11:48:09', 0),
(391, 51, 30, 'pending', '2025-02-15 11:48:09', 0),
(392, 51, 31, 'pending', '2025-02-15 11:48:09', 0),
(393, 51, 32, 'pending', '2025-02-15 11:48:09', 0),
(394, 51, 33, 'pending', '2025-02-15 11:48:09', 0),
(395, 51, 34, 'pending', '2025-02-15 11:48:09', 0),
(396, 51, 35, 'pending', '2025-02-15 11:48:09', 0),
(397, 51, 36, 'pending', '2025-02-15 11:48:09', 0),
(398, 51, 37, 'pending', '2025-02-15 11:48:09', 0),
(399, 51, 38, 'pending', '2025-02-15 11:48:09', 0),
(400, 51, 39, 'pending', '2025-02-15 11:48:09', 0),
(401, 52, 11, 'pending', '2025-02-23 07:54:47', 0),
(402, 52, 12, 'pending', '2025-02-23 07:54:47', 0),
(403, 52, 13, 'pending', '2025-02-23 07:54:47', 0),
(404, 52, 14, 'pending', '2025-02-23 07:54:47', 0),
(405, 52, 15, 'pending', '2025-02-23 07:54:47', 0),
(406, 52, 16, 'pending', '2025-02-23 07:54:47', 0),
(407, 52, 17, 'pending', '2025-02-23 07:54:47', 0),
(408, 52, 18, 'pending', '2025-02-23 07:54:47', 0),
(409, 52, 19, 'pending', '2025-02-23 07:54:47', 0),
(410, 52, 20, 'pending', '2025-02-23 07:54:47', 0),
(411, 52, 21, 'pending', '2025-02-23 07:54:47', 0),
(412, 52, 22, 'pending', '2025-02-23 07:54:47', 0),
(413, 52, 23, 'pending', '2025-02-23 07:54:47', 0),
(414, 52, 24, 'pending', '2025-02-23 07:54:47', 0),
(415, 52, 25, 'pending', '2025-02-23 07:54:47', 0),
(416, 52, 26, 'pending', '2025-02-23 07:54:47', 0),
(417, 52, 27, 'pending', '2025-02-23 07:54:47', 0),
(418, 52, 28, 'pending', '2025-02-23 07:54:47', 0),
(419, 52, 29, 'pending', '2025-02-23 07:54:47', 0),
(420, 52, 30, 'pending', '2025-02-23 07:54:47', 0),
(421, 52, 31, 'pending', '2025-02-23 07:54:47', 0),
(422, 52, 32, 'pending', '2025-02-23 07:54:47', 0),
(423, 52, 33, 'pending', '2025-02-23 07:54:47', 0),
(424, 52, 34, 'pending', '2025-02-23 07:54:47', 0),
(425, 52, 35, 'pending', '2025-02-23 07:54:47', 0),
(426, 52, 36, 'pending', '2025-02-23 07:54:47', 0),
(427, 52, 37, 'pending', '2025-02-23 07:54:47', 0),
(428, 52, 38, 'pending', '2025-02-23 07:54:47', 0),
(429, 52, 39, 'pending', '2025-02-23 07:54:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `komentar_postingan`
--

CREATE TABLE `komentar_postingan` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `konten` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentar_postingan`
--

INSERT INTO `komentar_postingan` (`id`, `postingan_id`, `user_id`, `konten`, `created_at`) VALUES
(57, 45, 'fadhilmanfa', 'Gass', '2025-02-16 16:39:26'),
(58, 50, 'agustina', 'Saya mau kalo tidur nanti mukanya di lelet balsem', '2025-02-23 07:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `komentar_replies`
--

CREATE TABLE `komentar_replies` (
  `id` int(11) NOT NULL,
  `komentar_id` int(11) DEFAULT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `konten` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_postingan`
--

CREATE TABLE `lampiran_postingan` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) DEFAULT NULL,
  `tipe_file` varchar(50) DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `path_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_tugas`
--

CREATE TABLE `lampiran_tugas` (
  `id` int(11) NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `tipe_file` varchar(100) NOT NULL,
  `ukuran_file` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lampiran_tugas`
--

INSERT INTO `lampiran_tugas` (`id`, `tugas_id`, `nama_file`, `path_file`, `tipe_file`, `ukuran_file`) VALUES
(1, 3, 'Instagram post - 90.png', 'uploads/tugas/tugas_3_67b958abe31c9_Instagram post - 90.png', 'image/png', 1012501);

-- --------------------------------------------------------

--
-- Table structure for table `likes_postingan`
--

CREATE TABLE `likes_postingan` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumpulan_tugas`
--

CREATE TABLE `pengumpulan_tugas` (
  `id` int(11) NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `siswa_id` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tipe_file` varchar(100) NOT NULL,
  `ukuran_file` int(11) NOT NULL,
  `waktu_pengumpulan` datetime NOT NULL,
  `pesan_siswa` text DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `komentar_guru` text DEFAULT NULL,
  `tanggal_penilaian` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumpulan_tugas`
--

INSERT INTO `pengumpulan_tugas` (`id`, `tugas_id`, `siswa_id`, `file_path`, `nama_file`, `tipe_file`, `ukuran_file`, `waktu_pengumpulan`, `pesan_siswa`, `nilai`, `komentar_guru`, `tanggal_penilaian`) VALUES
(1, 1, 'agustina', 'uploads/pengumpulan_tugas/tugas_1_siswa_agustina_67b833e0d9772.pdf', 'Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'application/pdf', 222275, '2025-02-21 15:05:52', NULL, NULL, NULL, NULL),
(2, 1, 'amara', 'uploads/pengumpulan_tugas/tugas_1_siswa_amara_67b83ffd55b3a_Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'application/pdf', 222275, '2025-02-21 15:57:33', NULL, NULL, NULL, NULL),
(3, 1, 'ainun', 'uploads/pengumpulan_tugas/tugas_1_siswa_ainun_67b8404967d20_lps_g000200185 (1).pdf', 'lps_g000200185 (1).pdf', 'application/pdf', 100923, '2025-02-21 15:58:49', NULL, NULL, NULL, NULL),
(4, 1, 'aulia', 'uploads/pengumpulan_tugas/tugas_1_siswa_aulia_67b940c951a38_krs sekarang.pdf', 'krs sekarang.pdf', 'application/pdf', 89384, '2025-02-22 10:13:13', NULL, NULL, NULL, NULL),
(5, 3, 'aisyah', 'uploads/pengumpulan_tugas/tugas_3_siswa_aisyah_67b96091c92c8_lps_g000200185 (1).pdf', 'lps_g000200185 (1).pdf', 'application/pdf', 100923, '2025-02-22 12:28:49', NULL, 90, 'mantap, terima kasih ya', NULL),
(6, 3, 'agustina', 'uploads/pengumpulan_tugas/tugas_3_siswa_agustina_67b9612815670_krs sekarang.pdf', 'krs sekarang.pdf', 'application/pdf', 89384, '2025-02-22 12:31:20', NULL, 10, 'tetap semangat ya nduk', NULL),
(7, 3, 'ainun', 'uploads/pengumpulan_tugas/tugas_3_siswa_ainun_67b9991662205_ancient-library-14298.png', 'ancient-library-14298.png', 'image/png', 3219890, '2025-02-22 16:29:58', NULL, NULL, NULL, NULL),
(8, 3, 'amara', 'uploads/pengumpulan_tugas/tugas_3_siswa_amara_67b99f5b32847_Draft SKPI_G000200185.docx', 'Draft SKPI_G000200185.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 33769, '2025-02-22 16:56:43', NULL, NULL, NULL, NULL),
(9, 3, 'aulia', 'uploads/pengumpulan_tugas/tugas_3_siswa_aulia_67b9a0b0c7599_67ae977ad669a_Surat-Pernyataan-Publikasi-02-03-2023.docx', '67ae977ad669a_Surat-Pernyataan-Publikasi-02-03-2023.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 95341, '2025-02-22 17:02:24', NULL, NULL, NULL, NULL),
(10, 3, 'bagus', 'uploads/pengumpulan_tugas/tugas_3_siswa_bagus_67b9a1a1e8d1c_Cek ini test untuk uji upload tugas.docx', 'Cek ini test untuk uji upload tugas.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 14621, '2025-02-22 17:06:25', NULL, NULL, NULL, NULL),
(11, 4, 'devi', 'uploads/pengumpulan_tugas/tugas_4_siswa_devi_67babfbaa0421_Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'application/pdf', 222275, '2025-02-23 13:27:06', 'terima kasih njih bu', NULL, NULL, NULL),
(12, 5, 'agustina', 'uploads/pengumpulan_tugas/tugas_5_siswa_agustina_67bad65a33e2e_Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'Gmail - Your Google Play Order Receipt from Dec 17, 2024.pdf', 'application/pdf', 222275, '2025-02-23 15:03:38', 'Terima kasih bu', 90, 'Terima kash nduk, tetap semangat belajar', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pg`
--

CREATE TABLE `pg` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `guru_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `semester` int(11) NOT NULL,
  `tahun_ajaran` varchar(9) NOT NULL,
  `nilai_akademik` int(11) DEFAULT NULL,
  `keaktifan` int(11) DEFAULT NULL,
  `pemahaman` int(11) DEFAULT NULL,
  `kehadiran_ibadah` int(11) DEFAULT NULL,
  `kualitas_ibadah` int(11) DEFAULT NULL,
  `pemahaman_agama` int(11) DEFAULT NULL,
  `minat_bakat` int(11) DEFAULT NULL,
  `prestasi` int(11) DEFAULT NULL,
  `keaktifan_ekskul` int(11) DEFAULT NULL,
  `partisipasi_sosial` int(11) DEFAULT NULL,
  `empati` int(11) DEFAULT NULL,
  `kerja_sama` int(11) DEFAULT NULL,
  `kebersihan_diri` int(11) DEFAULT NULL,
  `aktivitas_fisik` int(11) DEFAULT NULL,
  `pola_makan` int(11) DEFAULT NULL,
  `kejujuran` int(11) DEFAULT NULL,
  `tanggung_jawab` int(11) DEFAULT NULL,
  `kedisiplinan` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pg`
--

INSERT INTO `pg` (`id`, `siswa_id`, `guru_id`, `semester`, `tahun_ajaran`, `nilai_akademik`, `keaktifan`, `pemahaman`, `kehadiran_ibadah`, `kualitas_ibadah`, `pemahaman_agama`, `minat_bakat`, `prestasi`, `keaktifan_ekskul`, `partisipasi_sosial`, `empati`, `kerja_sama`, `kebersihan_diri`, `aktivitas_fisik`, `pola_makan`, `kejujuran`, `tanggung_jawab`, `kedisiplinan`, `created_at`) VALUES
(1, 51, 'fadhilmanfa', 1, '2024/2025', 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-24 03:57:46'),
(2, 11, 'fadhilmanfa', 1, '2024/2025', 100, NULL, NULL, NULL, 100, NULL, 100, NULL, NULL, NULL, 80, NULL, NULL, 50, NULL, NULL, 60, NULL, '2025-01-24 04:01:53'),
(5, 12, 'fadhilmanfa', 1, '2024/2025', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-01-24 04:16:27'),
(8, 43, 'fadhilmanfa', 1, '2024/2025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-24 06:34:58'),
(9, 36, 'fadhilmanfa', 1, '2024/2025', 100, 60, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-24 06:35:17'),
(10, 53, 'fadhilmanfa', 1, '2024/2025', 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-25 16:05:15'),
(14, 11, '0', 2, '2024/2025', 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-26 03:56:08'),
(24, 52, '0', 2, '2024/2025', 20, 40, 70, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-01-30 14:03:17');

-- --------------------------------------------------------

--
-- Table structure for table `pilihan_jawaban`
--

CREATE TABLE `pilihan_jawaban` (
  `id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `teks_pilihan` text NOT NULL,
  `is_benar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `postingan_kelas`
--

CREATE TABLE `postingan_kelas` (
  `id` int(11) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `konten` text DEFAULT NULL,
  `jenis_postingan` varchar(20) DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postingan_kelas`
--

INSERT INTO `postingan_kelas` (`id`, `kelas_id`, `user_id`, `konten`, `jenis_postingan`, `created_at`) VALUES
(45, 50, 'fadhilmanfa', 'Halo anak anak\r\nIni pertama kali kita menggunakan Smaga edu, semoga menyenangkan ya 😍', 'normal', '2025-02-16 16:36:23'),
(46, 50, 'fadhilmanfa', 'jangan lupa tugas liburannya', 'tugas', '2025-02-20 12:53:52'),
(48, 50, 'fadhilmanfa', 'sSasSsSasSassa', 'tugas', '2025-02-22 04:55:07'),
(49, 50, 'fadhilmanfa', 'Ini adalah tugas liburan UAS semester 3, tolong untuk di kerjakan sesuai dengan panduan kalian', 'tugas', '2025-02-23 06:20:08'),
(50, 52, 'fadhilmanfa', 'Halo adik-adik, ini adalah postingan pertama kelas F KEMUH. Sebelum masuk ke pembelajaran, kita buat kontrak belajar dulu ya, ada saran? 😅', 'normal', '2025-02-23 07:58:01'),
(51, 52, 'fadhilmanfa', 'Ini adalah tugas liburan semester 4, silahkan untuk mengerjakan tugas halaman 404 da, 405', 'tugas', '2025-02-23 08:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `project_name`, `description`, `created_at`) VALUES
(10, 'fadhilmanfa', 'Panduan Pembelajaran Matematika', 'Panduan mengajar matematika untuk siswa SMP kelas 7', '2025-01-25 04:26:18');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_knowledge`
--

CREATE TABLE `project_knowledge` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `content_type` enum('text','file') NOT NULL,
  `content` longtext DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_knowledge`
--

INSERT INTO `project_knowledge` (`id`, `project_id`, `content_type`, `content`, `file_path`, `file_type`, `created_at`) VALUES
(50, 10, 'text', '', NULL, NULL, '2025-01-25 05:26:55'),
(51, 10, 'text', '', NULL, NULL, '2025-01-25 05:26:55'),
(52, 10, 'text', '', NULL, NULL, '2025-01-25 05:26:55');

-- --------------------------------------------------------

--
-- Table structure for table `reactions`
--

CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `emoji` varchar(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saga_personality`
--

CREATE TABLE `saga_personality` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `current_job` text DEFAULT NULL,
  `traits` text NOT NULL,
  `additional_info` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saga_personality`
--

INSERT INTO `saga_personality` (`id`, `user_id`, `current_job`, `traits`, `additional_info`, `created_at`, `updated_at`) VALUES
(1, 'fadhilmanfa', '', '', '', '2025-02-19 04:47:18', '2025-02-27 14:47:43');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(255) NOT NULL,
  `nama` varchar(1000) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `tingkat` varchar(20) DEFAULT NULL,
  `foto_profil` varchar(1000) NOT NULL,
  `tahun_masuk` int(225) DEFAULT NULL,
  `no_hp` int(225) DEFAULT NULL,
  `alamat` varchar(1000) DEFAULT NULL,
  `nis` int(225) DEFAULT NULL,
  `photo_type` enum('upload','avatar') DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `nama`, `username`, `password`, `tingkat`, `foto_profil`, `tahun_masuk`, `no_hp`, `alamat`, `nis`, `photo_type`, `photo_url`) VALUES
(5, 'husein ali al fauzi', 'husein', 'a', 'E', '', 0, 0, '', NULL, NULL, NULL),
(6, 'irfan maulana akbar setyawan', 'irfan', 'a', 'E', '', 0, 0, '', NULL, NULL, NULL),
(7, 'muhamad ali nur said', 'muhamad ali', 'a', 'E', '', 0, 0, '', NULL, NULL, NULL),
(8, 'nuga asvianito marsya arindra', 'nuga', 'a', 'E', '', 0, 0, '', NULL, NULL, NULL),
(9, 'sidiq romadhon', 'sidiq', 'a', 'E', '', 0, 0, '', NULL, NULL, NULL),
(11, 'agustina reviyanti', 'agustina', 'a', 'F', '', 2022, 2147483647, 'Dukuh 2, Gatak, Sukoharjo', 10316, 'avatar', 'https://api.dicebear.com/7.x/lorelei/svg?seed=402nfy'),
(12, 'ainun wardiyah', 'ainun', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(13, 'aisyah nabila', 'aisyah', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(14, 'amara fauza', 'amara', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(15, 'aulia nazla rahayu fitrianingsih', 'aulia', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(16, 'bagus raka pratama', 'bagus', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(17, 'devi bintang maharani', 'devi', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(18, 'doni setyawan', 'doni', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(19, 'el fonda riski dwi saputra', 'el', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(20, 'hamidia aisya rachman', 'hamidia', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(21, 'ika purwita sari', 'ika', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(22, 'irza alfarizy', 'irza', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(23, 'keisya laila nurfatimah', 'keisya', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(24, 'krisna lestiya handoyo', 'krisna', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(25, 'lintang cahya lestari', 'lintang', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(26, 'marlindo tenafista', 'marlindo', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(27, 'melani sanjaya', 'melani', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(28, 'mus\'ab fairuz ziul haq', 'mus\'ab', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(29, 'naufal atha mubarok', 'naufal atha', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(30, 'naufal zaki muzaffar', 'naufal zaki', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(31, 'nisrina fairus mutia', 'nisrina', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(32, 'pradita guntur prasetyo', 'pradita', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(33, 'redwan satrio wibowo', 'redwan', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(34, 'reyhana assyfa', 'reyhana', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(35, 'septiana endah puji l', 'septiana endah', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(36, 'septiana rizki ramadhani', 'septiana rizki', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(37, 'septiani endah puji l', 'septiani', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(38, 'sharika rostriana elvira', 'sharika', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(39, 'umi nur hawa', 'umi', 'a', 'F', '', 0, 0, '', NULL, NULL, NULL),
(41, 'dwi noviyanti', 'dwi', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(42, 'galih al rasyd zakariya', 'galih', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(43, 'noor faizah', 'noor', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(44, 'putri dyah ayuningtyas', 'putri', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(45, 'reno rahmadandi', 'reno', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(46, 'reska dwi utomo', 'reska', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(47, 'reykhan ikhsanuddin kamil', 'reykhan', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(48, 'tomas candra mukti', 'tomas', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(49, 'dian putra ramadhan', 'dian', 'a', '12', '', 0, 0, '', NULL, NULL, NULL),
(50, '', '', '', '', '', 0, 0, '', NULL, NULL, NULL),
(51, 'adi putra pamungkas', 'adi', 'a', '7', '', 0, 0, '', NULL, NULL, NULL),
(52, 'dhio lintang winarto', 'dhio', 'a', '7', '', 0, 0, '', 0, NULL, NULL),
(53, 'hillan fajar saputra', 'hillan', 'a', '7', '1738293863_679c42670b08f.jpg', 0, 0, '', NULL, NULL, NULL),
(54, 'muhammad riski ramadhan', 'muhammad rizki', 'a', '7', '', 0, 0, '', NULL, NULL, NULL),
(56, 'audrey exa amiyudi azzahra', 'audrey', 'a', '8', '', 0, 0, '', NULL, NULL, NULL),
(57, 'velesia nabila permatasari', 'velesia', 'a', '8', '', 0, 0, '', NULL, NULL, NULL),
(59, 'aleksan julianto', 'aleksan', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(60, 'adi putra pamungkas', 'adi', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(61, 'angga fajrianto', 'angga', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(62, 'damar agustian dinata', 'damar', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(63, 'dhimas ariawan winarto', 'dhimas', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(64, 'erlangga ari saputra', 'erlangga', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(65, 'fabyan dafendra maulana', 'fabyan', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(66, 'galih andika prasetya', 'galih', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(67, 'muhammad reza praditya', 'muhammad reza', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(68, 'nagaswari rosma isya', 'nagaswari', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(69, 'naztwa marsyanti ramadhani', 'naztwa', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(70, 'safitri agustina sari', 'safitri', 'a', '9', '', 0, 0, '', NULL, NULL, NULL),
(71, 'zahratusyifa melandari', 'zahratusyifa', 'a', '9', '', 0, 0, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `soal_ujian`
--

CREATE TABLE `soal_ujian` (
  `id` int(11) NOT NULL,
  `ujian_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jenis_soal` enum('pilihan_ganda','essay') NOT NULL,
  `bobot` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tugas`
--

CREATE TABLE `tugas` (
  `id` int(11) NOT NULL,
  `postingan_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `batas_waktu` datetime NOT NULL,
  `poin_maksimal` int(11) NOT NULL DEFAULT 100,
  `created_at` datetime NOT NULL,
  `status` enum('active','closed') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas`
--

INSERT INTO `tugas` (`id`, `postingan_id`, `judul`, `deskripsi`, `batas_waktu`, `poin_maksimal`, `created_at`, `status`) VALUES
(1, 46, 'Tugas liburan', 'jangan lupa tugas liburannya', '2025-02-22 11:49:47', 0, '2025-02-20 19:53:52', 'closed'),
(3, 48, 'Tugas liburan 2', 'sSasSsSasSassa', '2025-02-22 12:00:00', 100, '2025-02-22 11:55:07', 'active'),
(4, 49, 'Tugas liburan 3', 'Ini adalah tugas liburan UAS semester 3, tolong untuk di kerjakan sesuai dengan panduan kalian', '2025-02-23 14:20:00', 100, '2025-02-23 13:20:08', 'active'),
(5, 51, 'Tugas Liburan Semester 4', 'Ini adalah tugas liburan semester 4, silahkan untuk mengerjakan tugas halaman 404 da, 405', '2025-03-05 15:01:00', 100, '2025-02-23 15:02:09', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `ujian`
--

CREATE TABLE `ujian` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `guru_id` varchar(100) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `mata_pelajaran` varchar(100) NOT NULL,
  `materi` text DEFAULT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `durasi` int(11) NOT NULL,
  `status` enum('draft','published','selesai') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ujian`
--

INSERT INTO `ujian` (`id`, `judul`, `deskripsi`, `guru_id`, `kelas_id`, `mata_pelajaran`, `materi`, `tanggal_mulai`, `tanggal_selesai`, `durasi`, `status`, `created_at`) VALUES
(40, 'test', 'test', 'fadhilmanfa', 50, 'Ilmu Pengetahuan Alam', '[\"Simbiosis\"]', '2025-02-16 19:53:00', '2025-02-17 07:00:00', 667, 'draft', '2025-02-16 12:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_character_analysis`
--

CREATE TABLE `user_character_analysis` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `analysis_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `kerjasama` float DEFAULT 0,
  `analitis` float DEFAULT 0,
  `detail` float DEFAULT 0,
  `inisiatif` float DEFAULT 0,
  `komunikatif` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_character_analysis`
--

INSERT INTO `user_character_analysis` (`id`, `user_id`, `analysis_date`, `kerjasama`, `analitis`, `detail`, `inisiatif`, `komunikatif`) VALUES
(1, 'fadhilmanfa', '2025-01-23 06:27:57', 0, 0, 0, 0, 0),
(2, 'fadhilmanfa', '2025-01-23 06:27:59', 0, 0, 0, 0, 0),
(3, 'fadhilmanfa', '2025-01-23 06:44:26', 0, 0, 0, 0, 0),
(4, 'fadhilmanfa', '2025-01-23 07:01:40', 0, 0, 0, 0, 0),
(5, 'fadhilmanfa', '2025-01-23 07:14:10', 0, 0, 0, 0, 0),
(6, 'fadhilmanfa', '2025-01-23 07:20:10', 0, 0, 0, 0, 0),
(7, 'fadhilmanfa', '2025-01-23 07:20:14', 0, 0, 0, 0, 0),
(8, 'fadhilmanfa', '2025-01-23 07:22:58', 0, 0, 0, 0, 0),
(9, 'fadhilmanfa', '2025-01-23 07:24:24', 0, 0, 0, 0, 0),
(10, 'fadhilmanfa', '2025-01-23 07:28:42', 0, 0, 0, 0, 0),
(11, 'fadhilmanfa', '2025-01-23 07:37:26', 0, 0, 0, 0, 0),
(12, 'fadhilmanfa', '2025-01-23 07:37:41', 0, 0, 0, 0, 0),
(13, 'fadhilmanfa', '2025-01-23 14:39:23', 0, 0, 0, 0, 0),
(14, 'fadhilmanfa', '2025-01-23 14:39:24', 0, 0, 0, 0, 0),
(15, 'fadhilmanfa', '2025-01-23 14:40:00', 0, 0, 0, 0, 0),
(16, 'fadhilmanfa', '2025-01-23 14:40:06', 0, 0, 0, 0, 0),
(17, 'fadhilmanfa', '2025-01-23 16:58:54', 0, 0, 0, 0, 0),
(18, 'fadhilmanfa', '2025-01-24 00:24:41', 0, 0, 0, 0, 0),
(19, 'fadhilmanfa', '2025-01-24 07:45:46', 0, 0, 0, 0, 0),
(20, 'fadhilmanfa', '2025-01-24 08:05:14', 0.1, 0, 0, 0, 0),
(21, 'fadhilmanfa', '2025-01-24 10:27:47', 0.1, 0, 0, 0, 0.1),
(22, 'fadhilmanfa', '2025-01-24 10:40:22', 0.1, 0.2, 0, 0, 0.3),
(23, 'fadhilmanfa', '2025-01-24 10:46:17', 0.1, 0.2, 0, 0, 0.5),
(24, 'fadhilmanfa', '2025-01-24 11:18:27', 0.1, 0.2, 0, 0, 0.5),
(25, 'fadhilmanfa', '2025-01-24 13:24:47', 0.2, 0.2, 0, 0, 0.6),
(26, 'fadhilmanfa', '2025-01-25 12:24:23', 0.4, 0, 0, 0, 1),
(27, 'fadhilmanfa', '2025-01-25 13:30:02', 0.4, 0, 0, 0, 1),
(28, 'fadhilmanfa', '2025-01-25 14:30:21', 0.4, 0, 0, 0, 1),
(29, 'fadhilmanfa', '2025-01-25 15:08:17', 0.4, 0, 0, 0, 1),
(30, 'fadhilmanfa', '2025-01-26 04:31:36', 0.4, 0, 0, 0, 1),
(31, 'fadhilmanfa', '2025-01-26 10:58:33', 0.4, 0, 0, 0, 1),
(32, 'fadhilmanfa', '2025-01-27 15:08:50', 0.4, 0, 0, 0, 1),
(33, 'fadhilmanfa', '2025-01-28 17:12:03', 0.3, 0, 0, 0, 1),
(34, 'fadhilmanfa', '2025-01-30 06:29:18', 0.3, 0, 0, 0, 1),
(35, 'fadhilmanfa', '2025-01-30 06:29:58', 0.3, 0, 0, 0, 1),
(36, 'fadhilmanfa', '2025-01-30 06:35:32', 0.3, 0, 0, 0, 1),
(37, 'fadhilmanfa', '2025-01-30 06:35:41', 0.3, 0, 0, 0, 1),
(38, 'fadhilmanfa', '2025-01-30 06:37:09', 0.3, 0, 0, 0, 1),
(39, 'fadhilmanfa', '2025-01-30 06:38:26', 0.3, 0, 0, 0, 1),
(40, 'fadhilmanfa', '2025-01-30 06:39:11', 0.3, 0, 0, 0, 1),
(41, 'fadhilmanfa', '2025-01-30 06:39:52', 0.3, 0, 0, 0, 1),
(42, 'fadhilmanfa', '2025-01-30 06:40:58', 0.3, 0, 0, 0, 1),
(43, 'fadhilmanfa', '2025-01-30 06:41:11', 0.3, 0, 0, 0, 1),
(44, 'fadhilmanfa', '2025-01-30 06:43:23', 0.3, 0, 0, 0, 1),
(45, 'fadhilmanfa', '2025-01-30 06:45:48', 0.3, 0, 0, 0, 1),
(46, 'fadhilmanfa', '2025-01-30 06:45:53', 0.3, 0, 0, 0, 1),
(47, 'fadhilmanfa', '2025-01-30 06:46:25', 0.3, 0, 0, 0, 1),
(48, 'fadhilmanfa', '2025-01-30 06:48:06', 0.3, 0, 0, 0, 1),
(49, 'fadhilmanfa', '2025-01-30 06:48:21', 0.3, 0, 0, 0, 1),
(50, 'fadhilmanfa', '2025-01-30 10:42:30', 0.3, 0, 0, 0, 1),
(51, 'fadhilmanfa', '2025-01-30 10:43:16', 0.3, 0, 0, 0, 1),
(52, 'fadhilmanfa', '2025-01-30 10:44:03', 0.3, 0, 0, 0, 1),
(53, 'fadhilmanfa', '2025-01-30 11:35:51', 0.1, 0, 0, 0, 1),
(54, 'fadhilmanfa', '2025-01-30 13:22:09', 0, 0, 0, 0, 1),
(55, 'fadhilmanfa', '2025-01-30 13:23:13', 0, 0, 0, 0, 1),
(56, 'fadhilmanfa', '2025-01-30 13:23:28', 0, 0, 0, 0, 1),
(57, 'fadhilmanfa', '2025-01-30 13:25:34', 0, 0, 0, 0, 1),
(58, 'fadhilmanfa', '2025-01-30 13:26:33', 0, 0, 0, 0, 1),
(59, 'fadhilmanfa', '2025-01-30 13:28:18', 0, 0, 0, 0, 1),
(60, 'fadhilmanfa', '2025-01-30 13:28:24', 0, 0, 0, 0, 1),
(61, 'fadhilmanfa', '2025-01-30 13:28:26', 0, 0, 0, 0, 1),
(62, 'fadhilmanfa', '2025-01-30 13:28:34', 0, 0, 0, 0, 1),
(63, 'fadhilmanfa', '2025-01-30 13:28:38', 0, 0, 0, 0, 1),
(64, 'fadhilmanfa', '2025-01-30 13:32:25', 0, 0, 0, 0, 1),
(65, 'fadhilmanfa', '2025-01-30 13:32:28', 0, 0, 0, 0, 1),
(66, 'fadhilmanfa', '2025-01-30 13:33:46', 0, 0, 0, 0, 1),
(67, 'fadhilmanfa', '2025-01-30 13:33:58', 0, 0, 0, 0, 1),
(68, 'fadhilmanfa', '2025-01-30 13:40:02', 0, 0, 0, 0, 1),
(69, 'fadhilmanfa', '2025-01-30 13:40:08', 0, 0, 0, 0, 1),
(70, 'fadhilmanfa', '2025-01-30 13:40:09', 0, 0, 0, 0, 1),
(71, 'fadhilmanfa', '2025-01-30 13:41:42', 0, 0, 0, 0, 1),
(72, 'fadhilmanfa', '2025-01-30 13:41:50', 0, 0, 0, 0, 1),
(73, 'fadhilmanfa', '2025-01-30 13:41:53', 0, 0, 0, 0, 1),
(74, 'fadhilmanfa', '2025-01-30 13:42:18', 0, 0, 0, 0, 1),
(75, 'fadhilmanfa', '2025-01-30 13:44:55', 0, 0, 0, 0, 1),
(76, 'fadhilmanfa', '2025-01-30 14:23:39', 0, 0, 0, 0, 1),
(77, 'fadhilmanfa', '2025-01-30 14:25:40', 0, 0, 0, 0, 1),
(78, 'fadhilmanfa', '2025-01-30 15:05:01', 0, 0, 0, 0, 1),
(79, 'fadhilmanfa', '2025-01-30 15:08:05', 0, 0, 0, 0, 1),
(80, 'fadhilmanfa', '2025-01-30 18:44:03', 0, 0, 0, 0, 1),
(81, 'fadhilmanfa', '2025-01-30 18:46:19', 0, 0, 0, 0, 1),
(82, 'fadhilmanfa', '2025-01-30 18:50:15', 0, 0, 0, 0, 1),
(83, 'fadhilmanfa', '2025-01-31 03:22:57', 0.1, 0, 0, 0, 1),
(84, 'fadhilmanfa', '2025-01-31 03:24:44', 0.1, 0, 0, 0, 1),
(85, 'fadhilmanfa', '2025-01-31 07:05:44', 0.1, 0.6, 0, 0, 1),
(86, 'fadhilmanfa', '2025-02-01 04:05:45', 0.4, 0, 0, 0, 1),
(87, 'fadhilmanfa', '2025-02-01 11:42:21', 0.4, 0, 0, 0, 1),
(88, 'fadhilmanfa', '2025-02-03 02:11:32', 0.4, 0, 0, 0, 1),
(89, 'fadhilmanfa', '2025-02-03 08:01:20', 0.4, 0, 0, 0, 1),
(90, 'fadhilmanfa', '2025-02-03 09:01:50', 0.4, 0.5, 0, 0, 1),
(91, 'fadhilmanfa', '2025-02-03 17:40:14', 0.6, 1, 0, 0, 1),
(92, 'fadhilmanfa', '2025-02-04 04:20:14', 0.6, 1, 0, 0, 1),
(93, 'fadhilmanfa', '2025-02-05 13:35:05', 0.6, 1, 0, 0, 1),
(94, 'fadhilmanfa', '2025-02-11 04:25:00', 0, 0, 0, 0, 0),
(95, 'fadhilmanfa', '2025-02-11 09:05:16', 0, 0, 0, 0, 0),
(96, 'fadhilmanfa', '2025-02-11 09:05:17', 0, 0, 0, 0, 0),
(97, 'fadhilmanfa', '2025-02-11 09:05:30', 0, 0, 0, 0, 0),
(98, 'fadhilmanfa', '2025-02-11 09:07:21', 0, 0, 0, 0, 0),
(99, 'fadhilmanfa', '2025-02-11 12:20:22', 0, 0, 0, 0, 0),
(100, 'fadhilmanfa', '2025-02-13 12:02:37', 0, 0, 0, 0, 0),
(101, 'fadhilmanfa', '2025-02-13 12:06:56', 0, 0, 0, 0, 0),
(102, 'fadhilmanfa', '2025-02-14 01:00:28', 0, 0, 0, 0, 0),
(103, 'fadhilmanfa', '2025-02-14 01:04:46', 0, 0, 0, 0, 0),
(104, 'fadhilmanfa', '2025-02-14 01:07:23', 0, 0, 0, 0, 0),
(105, 'fadhilmanfa', '2025-02-14 01:07:30', 0, 0, 0, 0, 0),
(106, 'fadhilmanfa', '2025-02-14 01:07:31', 0, 0, 0, 0, 0),
(107, 'fadhilmanfa', '2025-02-14 01:07:39', 0, 0, 0, 0, 0),
(108, 'fadhilmanfa', '2025-02-15 02:09:15', 0, 0, 0, 0, 0),
(109, 'fadhilmanfa', '2025-02-15 02:50:16', 0, 0, 0, 0, 0),
(110, 'fadhilmanfa', '2025-02-15 02:52:12', 0, 0, 0, 0, 0),
(111, 'fadhilmanfa', '2025-02-15 02:52:42', 0, 0, 0, 0, 0),
(112, 'fadhilmanfa', '2025-02-15 03:05:10', 0, 0, 0, 0, 0),
(113, 'fadhilmanfa', '2025-02-15 03:09:01', 0, 0, 0, 0, 0),
(114, 'fadhilmanfa', '2025-02-15 04:47:40', 0, 0, 0, 0, 0),
(115, 'fadhilmanfa', '2025-02-15 11:47:17', 0, 0, 0, 0, 0),
(116, 'fadhilmanfa', '2025-02-16 02:31:47', 0, 0, 0, 0, 0),
(117, 'fadhilmanfa', '2025-02-16 04:52:40', 0, 0, 0, 0, 0),
(118, 'fadhilmanfa', '2025-02-16 04:55:10', 0, 0, 0, 0, 0),
(119, 'fadhilmanfa', '2025-02-16 04:56:39', 0, 0, 0, 0, 0),
(120, 'fadhilmanfa', '2025-02-16 04:57:48', 0, 0, 0, 0, 0),
(121, 'fadhilmanfa', '2025-02-16 04:58:03', 0, 0, 0, 0, 0),
(122, 'fadhilmanfa', '2025-02-16 04:59:32', 0, 0, 0, 0, 0),
(123, 'fadhilmanfa', '2025-02-16 04:59:45', 0, 0, 0, 0, 0),
(124, 'fadhilmanfa', '2025-02-16 05:00:44', 0, 0, 0, 0, 0),
(125, 'fadhilmanfa', '2025-02-16 05:01:09', 0, 0, 0, 0, 0),
(126, 'fadhilmanfa', '2025-02-16 05:01:44', 0, 0, 0, 0, 0),
(127, 'fadhilmanfa', '2025-02-16 05:03:45', 0, 0, 0, 0, 0),
(128, 'fadhilmanfa', '2025-02-16 05:04:04', 0, 0, 0, 0, 0),
(129, 'fadhilmanfa', '2025-02-16 05:04:11', 0, 0, 0, 0, 0),
(130, 'fadhilmanfa', '2025-02-16 05:05:24', 0, 0, 0, 0, 0),
(131, 'fadhilmanfa', '2025-02-16 05:05:41', 0, 0, 0, 0, 0),
(132, 'fadhilmanfa', '2025-02-16 05:06:36', 0, 0, 0, 0, 0),
(133, 'fadhilmanfa', '2025-02-16 05:06:54', 0, 0, 0, 0, 0),
(134, 'fadhilmanfa', '2025-02-16 05:07:19', 0, 0, 0, 0, 0),
(135, 'fadhilmanfa', '2025-02-16 05:09:46', 0, 0, 0, 0, 0),
(136, 'fadhilmanfa', '2025-02-16 05:10:15', 0, 0, 0, 0, 0),
(137, 'fadhilmanfa', '2025-02-16 05:11:19', 0, 0, 0, 0, 0),
(138, 'fadhilmanfa', '2025-02-16 05:11:37', 0, 0, 0, 0, 0),
(139, 'fadhilmanfa', '2025-02-16 05:14:00', 0, 0, 0, 0, 0),
(140, 'fadhilmanfa', '2025-02-16 05:14:25', 0, 0, 0, 0, 0),
(141, 'fadhilmanfa', '2025-02-16 05:14:32', 0, 0, 0, 0, 0),
(142, 'fadhilmanfa', '2025-02-16 05:14:49', 0, 0, 0, 0, 0),
(143, 'fadhilmanfa', '2025-02-16 05:15:08', 0, 0, 0, 0, 0),
(144, 'fadhilmanfa', '2025-02-16 05:15:13', 0, 0, 0, 0, 0),
(145, 'fadhilmanfa', '2025-02-16 05:15:14', 0, 0, 0, 0, 0),
(146, 'fadhilmanfa', '2025-02-16 05:15:16', 0, 0, 0, 0, 0),
(147, 'fadhilmanfa', '2025-02-16 05:15:17', 0, 0, 0, 0, 0),
(148, 'fadhilmanfa', '2025-02-16 05:15:18', 0, 0, 0, 0, 0),
(150, 'fadhilmanfa', '2025-02-16 05:15:19', 0, 0, 0, 0, 0),
(151, 'fadhilmanfa', '2025-02-16 05:15:20', 0, 0, 0, 0, 0),
(152, 'fadhilmanfa', '2025-02-16 05:15:21', 0, 0, 0, 0, 0),
(153, 'fadhilmanfa', '2025-02-16 05:15:29', 0, 0, 0, 0, 0),
(154, 'fadhilmanfa', '2025-02-16 05:15:36', 0, 0, 0, 0, 0),
(155, 'fadhilmanfa', '2025-02-16 05:15:44', 0, 0, 0, 0, 0),
(156, 'fadhilmanfa', '2025-02-16 05:15:45', 0, 0, 0, 0, 0),
(157, 'fadhilmanfa', '2025-02-16 05:15:46', 0, 0, 0, 0, 0),
(158, 'fadhilmanfa', '2025-02-16 05:15:48', 0, 0, 0, 0, 0),
(159, 'fadhilmanfa', '2025-02-16 05:16:03', 0, 0, 0, 0, 0),
(160, 'fadhilmanfa', '2025-02-16 05:16:06', 0, 0, 0, 0, 0),
(161, 'fadhilmanfa', '2025-02-16 05:16:37', 0, 0, 0, 0, 0),
(162, 'fadhilmanfa', '2025-02-16 05:16:40', 0, 0, 0, 0, 0),
(163, 'fadhilmanfa', '2025-02-16 05:16:43', 0, 0, 0, 0, 0),
(164, 'fadhilmanfa', '2025-02-16 05:16:51', 0, 0, 0, 0, 0),
(165, 'fadhilmanfa', '2025-02-16 05:18:33', 0, 0, 0, 0, 0),
(166, 'fadhilmanfa', '2025-02-16 05:18:37', 0, 0, 0, 0, 0),
(167, 'fadhilmanfa', '2025-02-16 05:27:00', 0, 0, 0, 0, 0),
(168, 'fadhilmanfa', '2025-02-16 05:27:04', 0, 0, 0, 0, 0),
(169, 'fadhilmanfa', '2025-02-16 06:36:41', 0, 0, 0, 0, 0),
(170, 'fadhilmanfa', '2025-02-16 06:36:45', 0, 0, 0, 0, 0),
(171, 'fadhilmanfa', '2025-02-16 06:36:47', 0, 0, 0, 0, 0),
(172, 'fadhilmanfa', '2025-02-16 06:36:49', 0, 0, 0, 0, 0),
(173, 'fadhilmanfa', '2025-02-16 06:37:13', 0, 0, 0, 0, 0),
(174, 'fadhilmanfa', '2025-02-16 06:39:57', 0, 0, 0, 0, 0),
(175, 'fadhilmanfa', '2025-02-16 07:03:19', 0, 0, 0, 0, 0),
(176, 'fadhilmanfa', '2025-02-16 07:33:24', 0, 0, 0, 0, 0),
(177, 'fadhilmanfa', '2025-02-16 07:33:30', 0, 0, 0, 0, 0),
(178, 'fadhilmanfa', '2025-02-16 07:36:06', 0, 0, 0, 0, 0),
(179, 'fadhilmanfa', '2025-02-16 07:36:08', 0, 0, 0, 0, 0),
(180, 'fadhilmanfa', '2025-02-16 07:37:07', 0, 0, 0, 0, 0),
(181, 'fadhilmanfa', '2025-02-16 07:37:43', 0, 0, 0, 0, 0),
(182, 'fadhilmanfa', '2025-02-16 07:37:45', 0, 0, 0, 0, 0),
(183, 'fadhilmanfa', '2025-02-16 07:37:47', 0, 0, 0, 0, 0),
(184, 'fadhilmanfa', '2025-02-16 07:37:48', 0, 0, 0, 0, 0),
(185, 'fadhilmanfa', '2025-02-16 07:37:50', 0, 0, 0, 0, 0),
(186, 'fadhilmanfa', '2025-02-16 07:37:52', 0, 0, 0, 0, 0),
(187, 'fadhilmanfa', '2025-02-16 07:37:56', 0, 0, 0, 0, 0),
(188, 'fadhilmanfa', '2025-02-16 08:09:17', 0, 0, 0, 0, 0),
(189, 'fadhilmanfa', '2025-02-16 08:13:49', 0, 0, 0, 0, 0),
(190, 'fadhilmanfa', '2025-02-16 08:14:56', 0, 0, 0, 0, 0),
(191, 'fadhilmanfa', '2025-02-16 08:14:59', 0, 0, 0, 0, 0),
(192, 'fadhilmanfa', '2025-02-16 08:15:02', 0, 0, 0, 0, 0),
(193, 'fadhilmanfa', '2025-02-16 08:15:53', 0, 0, 0, 0, 0),
(194, 'fadhilmanfa', '2025-02-16 08:15:55', 0, 0, 0, 0, 0),
(195, 'fadhilmanfa', '2025-02-16 08:17:36', 0, 0, 0, 0, 0),
(196, 'fadhilmanfa', '2025-02-16 08:17:38', 0, 0, 0, 0, 0),
(197, 'fadhilmanfa', '2025-02-16 08:17:46', 0, 0, 0, 0, 0),
(198, 'fadhilmanfa', '2025-02-16 08:17:48', 0, 0, 0, 0, 0),
(199, 'fadhilmanfa', '2025-02-16 08:18:40', 0, 0, 0, 0, 0),
(200, 'fadhilmanfa', '2025-02-16 08:19:35', 0, 0, 0, 0, 0),
(201, 'fadhilmanfa', '2025-02-16 08:26:27', 0, 0, 0, 0, 0),
(202, 'fadhilmanfa', '2025-02-16 08:26:46', 0, 0, 0, 0, 0),
(203, 'fadhilmanfa', '2025-02-16 08:26:48', 0, 0, 0, 0, 0),
(204, 'fadhilmanfa', '2025-02-16 08:26:51', 0, 0, 0, 0, 0),
(205, 'fadhilmanfa', '2025-02-16 08:30:14', 0, 0, 0, 0, 0),
(206, 'fadhilmanfa', '2025-02-16 08:30:57', 0, 0, 0, 0, 0),
(207, 'joko', '2025-02-16 08:37:35', 0, 0, 0, 0, 0),
(208, 'joko', '2025-02-16 08:37:39', 0, 0, 0, 0, 0),
(209, 'joko', '2025-02-16 08:37:41', 0, 0, 0, 0, 0),
(210, 'fadhilmanfa', '2025-02-16 08:38:14', 0, 0, 0, 0, 0),
(211, 'fikofiko', '2025-02-16 08:38:38', 0, 0, 0, 0, 0),
(212, 'fikofiko', '2025-02-16 08:38:39', 0, 0, 0, 0, 0),
(213, 'fikofiko', '2025-02-16 08:38:41', 0, 0, 0, 0, 0),
(214, 'nia', '2025-02-16 08:39:14', 0, 0, 0, 0, 0),
(215, 'nia', '2025-02-16 08:39:45', 0, 0, 0, 0, 0),
(216, 'nia', '2025-02-16 08:39:50', 0, 0, 0, 0, 0),
(217, 'nia', '2025-02-16 08:40:03', 0, 0, 0, 0, 0),
(218, 'nia', '2025-02-16 08:40:06', 0, 0, 0, 0, 0),
(219, 'nia', '2025-02-16 08:40:14', 0, 0, 0, 0, 0),
(220, 'jayus', '2025-02-16 08:40:35', 0, 0, 0, 0, 0),
(221, 'jayus', '2025-02-16 08:40:38', 0, 0, 0, 0, 0),
(222, 'jayus', '2025-02-16 08:40:44', 0, 0, 0, 0, 0),
(223, 'jayus', '2025-02-16 08:40:48', 0, 0, 0, 0, 0),
(224, 'jayus', '2025-02-16 08:41:23', 0, 0, 0, 0, 0),
(225, 'jayus', '2025-02-16 08:41:26', 0, 0, 0, 0, 0),
(226, 'jayus', '2025-02-16 08:41:32', 0, 0, 0, 0, 0),
(227, 'jayus', '2025-02-16 08:41:38', 0, 0, 0, 0, 0),
(228, 'jayus', '2025-02-16 08:41:42', 0, 0, 0, 0, 0),
(229, 'jayus', '2025-02-16 08:41:46', 0, 0, 0, 0, 0),
(230, 'jayus', '2025-02-16 08:42:47', 0, 0, 0, 0, 0),
(231, 'jayus', '2025-02-16 08:42:49', 0, 0, 0, 0, 0),
(232, 'jayus', '2025-02-16 08:42:57', 0, 0, 0, 0, 0),
(233, 'jayus', '2025-02-16 08:42:58', 0, 0, 0, 0, 0),
(234, 'jayus', '2025-02-16 08:43:07', 0, 0, 0, 0, 0),
(235, 'jayus', '2025-02-16 08:45:02', 0, 0, 0, 0, 0),
(236, 'jayus', '2025-02-16 08:47:04', 0, 0, 0, 0, 0),
(237, 'jayus', '2025-02-16 08:47:11', 0, 0, 0, 0, 0),
(238, 'fadhilmanfa', '2025-02-16 08:47:25', 0, 0, 0, 0, 0),
(239, 'fadhilmanfa', '2025-02-16 08:47:31', 0, 0, 0, 0, 0),
(240, 'fadhilmanfa', '2025-02-16 08:47:34', 0, 0, 0, 0, 0),
(241, 'fadhilmanfa', '2025-02-16 08:50:03', 0, 0, 0, 0, 0),
(242, 'fadhilmanfa', '2025-02-16 08:50:09', 0, 0, 0, 0, 0),
(243, 'fadhilmanfa', '2025-02-16 08:50:40', 0, 0, 0, 0, 0),
(244, 'fadhilmanfa', '2025-02-16 08:50:43', 0, 0, 0, 0, 0),
(245, 'fadhilmanfa', '2025-02-16 08:50:46', 0, 0, 0, 0, 0),
(246, 'fadhilmanfa', '2025-02-16 08:50:47', 0, 0, 0, 0, 0),
(247, 'fadhilmanfa', '2025-02-16 08:51:16', 0, 0, 0, 0, 0),
(248, 'fadhilmanfa', '2025-02-16 08:51:18', 0, 0, 0, 0, 0),
(249, 'fadhilmanfa', '2025-02-16 08:51:21', 0, 0, 0, 0, 0),
(250, 'fadhilmanfa', '2025-02-16 08:51:26', 0, 0, 0, 0, 0),
(251, 'fadhilmanfa', '2025-02-16 08:51:31', 0, 0, 0, 0, 0),
(252, 'fadhilmanfa', '2025-02-16 08:51:45', 0, 0, 0, 0, 0),
(253, 'fadhilmanfa', '2025-02-16 08:52:19', 0, 0, 0, 0, 0),
(254, 'fadhilmanfa', '2025-02-16 08:52:22', 0, 0, 0, 0, 0),
(255, 'fadhilmanfa', '2025-02-16 08:52:27', 0, 0, 0, 0, 0),
(256, 'fadhilmanfa', '2025-02-16 08:52:54', 0, 0, 0, 0, 0),
(257, 'fadhilmanfa', '2025-02-16 08:52:56', 0, 0, 0, 0, 0),
(258, 'fadhilmanfa', '2025-02-16 08:53:05', 0, 0, 0, 0, 0),
(259, 'fadhilmanfa', '2025-02-16 08:53:07', 0, 0, 0, 0, 0),
(260, 'fadhilmanfa', '2025-02-16 08:53:24', 0, 0, 0, 0, 0),
(261, 'fadhilmanfa', '2025-02-16 08:53:57', 0, 0, 0, 0, 0),
(262, 'fadhilmanfa', '2025-02-16 08:54:00', 0, 0, 0, 0, 0),
(263, 'fadhilmanfa', '2025-02-16 08:54:05', 0, 0, 0, 0, 0),
(264, 'fadhilmanfa', '2025-02-16 08:54:53', 0, 0, 0, 0, 0),
(265, 'fadhilmanfa', '2025-02-16 08:54:57', 0, 0, 0, 0, 0),
(266, 'fadhilmanfa', '2025-02-16 08:55:05', 0, 0, 0, 0, 0),
(267, 'fadhilmanfa', '2025-02-16 08:55:56', 0, 0, 0, 0, 0),
(268, 'fadhilmanfa', '2025-02-16 08:55:58', 0, 0, 0, 0, 0),
(269, 'fadhilmanfa', '2025-02-16 08:56:01', 0, 0, 0, 0, 0),
(270, 'fadhilmanfa', '2025-02-16 08:56:04', 0, 0, 0, 0, 0),
(271, 'fadhilmanfa', '2025-02-16 08:59:30', 0, 0, 0, 0, 0),
(272, 'fadhilmanfa', '2025-02-16 08:59:34', 0, 0, 0, 0, 0),
(273, 'fadhilmanfa', '2025-02-16 09:18:11', 0, 0, 0, 0, 0),
(274, 'fadhilmanfa', '2025-02-16 11:37:54', 0, 0, 0, 0.2, 0.1),
(275, 'fadhilmanfa', '2025-02-16 11:37:56', 0, 0, 0, 0.2, 0.1),
(276, 'fadhilmanfa', '2025-02-16 11:37:59', 0, 0, 0, 0.2, 0.1),
(277, 'fadhilmanfa', '2025-02-16 11:38:45', 0, 0, 0, 0.2, 0.1),
(278, 'fadhilmanfa', '2025-02-16 11:38:48', 0, 0, 0, 0.2, 0.1),
(279, 'fadhilmanfa', '2025-02-16 11:39:59', 0, 0, 0, 0.2, 0.1),
(280, 'fadhilmanfa', '2025-02-16 11:40:32', 0, 0, 0, 0.2, 0.1),
(281, 'fadhilmanfa', '2025-02-16 11:45:00', 0, 0, 0, 0.2, 0.1),
(282, 'fadhilmanfa', '2025-02-16 11:47:04', 0, 0, 0, 0.2, 0.1),
(283, 'fadhilmanfa', '2025-02-16 11:50:01', 0, 0, 0, 0.2, 0.1),
(284, 'fadhilmanfa', '2025-02-16 11:55:55', 0, 0, 0, 0.2, 0.1),
(285, 'fadhilmanfa', '2025-02-16 11:55:59', 0, 0, 0, 0.2, 0.1),
(286, 'fadhilmanfa', '2025-02-16 11:56:46', 0, 0, 0, 0.2, 0.1),
(287, 'fadhilmanfa', '2025-02-16 12:09:14', 0, 0, 0, 0.2, 0.1),
(288, 'fadhilmanfa', '2025-02-16 12:31:25', 0, 0, 0, 0.2, 0.1),
(289, 'fadhilmanfa', '2025-02-16 12:31:35', 0, 0, 0, 0.2, 0.1),
(290, 'fadhilmanfa', '2025-02-16 12:56:10', 0, 0, 0, 0.2, 0.1),
(291, 'fadhilmanfa', '2025-02-16 15:35:55', 0, 0, 0, 0.2, 0.1),
(292, 'fadhilmanfa', '2025-02-16 16:18:04', 0, 0, 0, 0.2, 0.1),
(293, 'fadhilmanfa', '2025-02-16 16:21:04', 0, 0, 0, 0.2, 0.1),
(294, 'fadhilmanfa', '2025-02-16 16:21:28', 0, 0, 0, 0.2, 0.1),
(295, 'fadhilmanfa', '2025-02-16 16:21:29', 0, 0, 0, 0.2, 0.1),
(296, 'fadhilmanfa', '2025-02-16 16:21:49', 0, 0, 0, 0.2, 0.1),
(297, 'fadhilmanfa', '2025-02-16 16:22:51', 0, 0, 0, 0.2, 0.1),
(298, 'fadhilmanfa', '2025-02-16 16:33:00', 0, 0, 0, 0.2, 0.1),
(299, 'fadhilmanfa', '2025-02-16 16:36:54', 0, 0, 0, 0.2, 0.1),
(300, 'fadhilmanfa', '2025-02-16 16:37:50', 0, 0, 0, 0.2, 0.1),
(301, 'fadhilmanfa', '2025-02-16 16:37:58', 0, 0, 0, 0.2, 0.1),
(302, 'fadhilmanfa', '2025-02-16 16:38:35', 0, 0, 0, 0.2, 0.1),
(303, 'fadhilmanfa', '2025-02-16 16:50:09', 0, 0, 0, 0.2, 0.1),
(304, 'fadhilmanfa', '2025-02-16 16:50:26', 0, 0, 0, 0.2, 0.1),
(305, 'fadhilmanfa', '2025-02-16 16:53:57', 0, 0.1, 0, 0.3, 0.2),
(306, 'fadhilmanfa', '2025-02-16 16:54:04', 0, 0.1, 0, 0.3, 0.2),
(307, 'fadhilmanfa', '2025-02-16 16:54:57', 0, 0.1, 0, 0.3, 0.2),
(308, 'fadhilmanfa', '2025-02-16 16:55:59', 0, 0.1, 0, 0.3, 0.3),
(309, 'fadhilmanfa', '2025-02-16 17:01:13', 0, 0.1, 0, 0.3, 0.3),
(310, 'fadhilmanfa', '2025-02-17 10:55:24', 0, 0.1, 0, 0.3, 0.9),
(311, 'fadhilmanfa', '2025-02-17 10:56:39', 0, 0.1, 0, 0.3, 0.9),
(312, 'fadhilmanfa', '2025-02-17 10:57:56', 0, 0.1, 0, 0.3, 0.9),
(313, 'fadhilmanfa', '2025-02-17 10:57:59', 0, 0.1, 0, 0.3, 0.9),
(314, 'fadhilmanfa', '2025-02-17 10:58:13', 0, 0.1, 0, 0.3, 0.9),
(315, 'fadhilmanfa', '2025-02-17 10:58:17', 0, 0.1, 0, 0.3, 0.9),
(316, 'fadhilmanfa', '2025-02-17 10:59:05', 0, 0.1, 0, 0.3, 0.9),
(317, 'fadhilmanfa', '2025-02-18 00:13:27', 0, 0.1, 0, 0.3, 0.9),
(318, 'fadhilmanfa', '2025-02-18 00:14:35', 0, 0.1, 0, 0.3, 0.9),
(319, 'fadhilmanfa', '2025-02-18 00:28:53', 0, 0.1, 0, 0.3, 0.9),
(320, 'fadhilmanfa', '2025-02-18 00:29:03', 0, 0.1, 0, 0.3, 0.9),
(321, 'fadhilmanfa', '2025-02-18 00:30:40', 0, 0.1, 0, 0.3, 0.9),
(322, 'fadhilmanfa', '2025-02-18 00:30:55', 0, 0.1, 0, 0.3, 0.9),
(323, 'fadhilmanfa', '2025-02-18 00:30:57', 0, 0.1, 0, 0.3, 0.9),
(324, 'fadhilmanfa', '2025-02-18 00:31:04', 0, 0.1, 0, 0.3, 0.9),
(325, 'fadhilmanfa', '2025-02-18 00:34:26', 0, 0.1, 0, 0.3, 0.9),
(326, 'fadhilmanfa', '2025-02-18 01:57:18', 0, 0.1, 0, 0.3, 1),
(327, 'fadhilmanfa', '2025-02-18 02:13:31', 0, 0.1, 0, 0.3, 1),
(328, 'fadhilmanfa', '2025-02-18 21:59:45', 0, 0.1, 0, 0.3, 1),
(329, 'fadhilmanfa', '2025-02-19 14:09:54', 0, 0, 0, 0, 0),
(330, 'fadhilmanfa', '2025-02-19 14:09:56', 0, 0, 0, 0, 0),
(331, 'fadhilmanfa', '2025-02-19 14:10:08', 0, 0, 0, 0, 0),
(332, 'fadhilmanfa', '2025-02-19 14:10:24', 0, 0, 0, 0, 0),
(333, 'fadhilmanfa', '2025-02-19 14:10:25', 0, 0, 0, 0, 0),
(334, 'fadhilmanfa', '2025-02-19 14:11:52', 0, 0, 0, 0, 0),
(335, 'fadhilmanfa', '2025-02-19 14:13:13', 0, 0, 0, 0, 0),
(336, 'fadhilmanfa', '2025-02-19 14:13:16', 0, 0, 0, 0, 0),
(337, 'fadhilmanfa', '2025-02-19 14:13:42', 0, 0, 0, 0, 0),
(338, 'fadhilmanfa', '2025-02-19 14:14:39', 0, 0, 0, 0, 0),
(339, 'fadhilmanfa', '2025-02-19 14:15:05', 0, 0, 0, 0, 0),
(340, 'fadhilmanfa', '2025-02-19 14:15:23', 0, 0, 0, 0, 0),
(341, 'fadhilmanfa', '2025-02-19 14:15:26', 0, 0, 0, 0, 0),
(342, 'fadhilmanfa', '2025-02-19 14:16:23', 0, 0, 0, 0, 0),
(343, 'fadhilmanfa', '2025-02-19 17:22:45', 0, 0, 0, 0, 0),
(344, 'fadhilmanfa', '2025-02-19 17:22:51', 0, 0, 0, 0, 0),
(345, 'fadhilmanfa', '2025-02-19 17:22:55', 0, 0, 0, 0, 0),
(346, 'fadhilmanfa', '2025-02-19 17:31:55', 0, 0, 0, 0, 0.1),
(347, 'fadhilmanfa', '2025-02-19 17:38:28', 0, 0, 0, 0, 0),
(348, 'fadhilmanfa', '2025-02-20 11:36:55', 0, 0, 0, 0, 0.3),
(349, 'fadhilmanfa', '2025-02-20 11:39:01', 0, 0, 0, 0, 0.3),
(350, 'fadhilmanfa', '2025-02-20 11:55:18', 0, 0, 0, 0, 0.3),
(351, 'fadhilmanfa', '2025-02-20 11:55:24', 0, 0, 0, 0, 0.3),
(352, 'fadhilmanfa', '2025-02-20 11:55:59', 0, 0, 0, 0, 0.3),
(353, 'fadhilmanfa', '2025-02-21 08:58:17', 0, 0, 0, 0, 0.3),
(354, 'fadhilmanfa', '2025-02-26 00:25:24', 0, 0, 0, 0, 0.3),
(355, 'fadhilmanfa', '2025-02-26 03:02:48', 0, 0, 0, 0, 0.3),
(356, 'fadhilmanfa', '2025-02-26 03:03:35', 0, 0, 0, 0, 0.3),
(357, 'fadhilmanfa', '2025-02-26 03:03:45', 0, 0, 0, 0, 0.3),
(358, 'fadhilmanfa', '2025-02-26 03:04:02', 0, 0, 0, 0, 0.3),
(359, 'fadhilmanfa', '2025-02-27 07:46:25', 0, 0, 0, 0, 0.3),
(360, 'fadhilmanfa', '2025-02-27 07:47:48', 0, 0, 0, 0, 0.3),
(361, 'fadhilmanfa', '2025-02-27 07:47:53', 0, 0, 0, 0, 0.3),
(362, 'fadhilmanfa', '2025-02-27 07:47:54', 0, 0, 0, 0, 0.3),
(363, 'fadhilmanfa', '2025-02-27 07:49:58', 0, 0, 0, 0, 0.3);

-- --------------------------------------------------------

--
-- Table structure for table `user_topics`
--

CREATE TABLE `user_topics` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `topic` varchar(100) NOT NULL,
  `frequency` int(11) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_topics`
--

INSERT INTO `user_topics` (`id`, `user_id`, `topic`, `frequency`, `last_updated`) VALUES
(1, 'fadhilmanfa', 'halo', 1546, '2025-02-27 07:49:58'),
(3, 'fadhilmanfa', 'buatkan', 69, '2025-01-24 13:24:47'),
(4, 'fadhilmanfa', 'materi', 51, '2025-01-24 10:27:47'),
(5, 'fadhilmanfa', 'kultum', 14, '2025-01-24 00:24:41'),
(6, 'fadhilmanfa', 'ikhlas', 14, '2025-01-24 00:24:41'),
(7, 'fadhilmanfa', 'sebanyak', 14, '2025-01-24 00:24:41'),
(75, 'fadhilmanfa', 'berikan', 158, '2025-02-16 09:18:11'),
(76, 'fadhilmanfa', 'tips', 76, '2025-02-16 09:18:11'),
(77, 'fadhilmanfa', 'mengajar', 76, '2025-02-16 09:18:11'),
(80, 'fadhilmanfa', 'tolong', 9, '2025-01-25 15:08:17'),
(81, 'fadhilmanfa', 'rangkum', 1, '2025-01-24 08:05:14'),
(82, 'fadhilmanfa', 'jurnal', 1, '2025-01-24 08:05:14'),
(85, 'fadhilmanfa', 'haha,', 1, '2025-01-24 10:27:47'),
(86, 'fadhilmanfa', 'hanya', 1, '2025-01-24 10:27:47'),
(87, 'fadhilmanfa', 'ingin', 1, '2025-01-24 10:27:47'),
(88, 'fadhilmanfa', 'kamu', 579, '2025-02-27 07:49:58'),
(89, 'fadhilmanfa', 'user', 20, '2025-01-24 13:24:47'),
(91, 'fadhilmanfa', 'sampai', 3, '2025-01-24 10:40:22'),
(92, 'fadhilmanfa', 'tidak', 30, '2025-02-05 13:35:05'),
(95, 'fadhilmanfa', 'memahami', 8, '2025-01-24 11:18:27'),
(96, 'fadhilmanfa', 'kamu,', 12, '2025-01-24 13:24:47'),
(104, 'fadhilmanfa', 'ringkaskan', 7, '2025-01-24 13:24:47'),
(109, 'fadhilmanfa', 'dokumen', 233, '2025-02-03 09:01:50'),
(110, 'fadhilmanfa', 'jelaskan', 49, '2025-02-03 08:01:20'),
(111, 'fadhilmanfa', 'semua', 19, '2025-01-30 11:35:51'),
(112, 'fadhilmanfa', 'apa?', 20, '2025-01-26 10:58:33'),
(117, 'fadhilmanfa', 'hari', 5, '2025-01-25 13:30:02'),
(122, 'fadhilmanfa', 'coba', 113, '2025-02-03 08:01:20'),
(247, 'fadhilmanfa', 'siapa?', 127, '2025-01-31 03:24:44'),
(251, 'fadhilmanfa', 'haloo', 58, '2025-01-30 18:50:15'),
(256, 'fadhilmanfa', 'halo,', 81, '2025-02-13 12:06:56'),
(396, 'fadhilmanfa', 'merancang', 6, '2025-01-31 03:24:44'),
(397, 'fadhilmanfa', 'agar', 6, '2025-01-31 03:24:44'),
(404, 'fadhilmanfa', 'siswa', 46, '2025-02-05 13:35:05'),
(405, 'fadhilmanfa', 'bagaimana', 39, '2025-02-16 16:54:57'),
(407, 'fadhilmanfa', 'pembelajaran', 4, '2025-01-31 07:05:44'),
(429, 'fadhilmanfa', 'kelas', 7, '2025-02-03 09:01:50'),
(432, 'fadhilmanfa', 'tapi', 5, '2025-02-03 09:01:50'),
(433, 'fadhilmanfa', 'bisa', 33, '2025-02-05 13:35:05'),
(450, 'fadhilmanfa', 'selamat', 2, '2025-02-13 12:06:56'),
(451, 'fadhilmanfa', 'malam', 2, '2025-02-13 12:06:56'),
(459, 'fadhilmanfa', 'efektif', 75, '2025-02-16 09:18:11'),
(758, 'fadhilmanfa', 'saga', 224, '2025-02-27 07:49:58'),
(760, 'fadhilmanfa', 'masuk', 31, '2025-02-16 16:50:26'),
(761, 'fadhilmanfa', 'piket', 31, '2025-02-16 16:50:26'),
(914, 'fadhilmanfa', 'saran', 10, '2025-02-16 17:01:13'),
(916, 'fadhilmanfa', 'cara', 3, '2025-02-16 16:54:57'),
(930, 'fadhilmanfa', 'duluan', 2, '2025-02-16 17:01:13'),
(931, 'fadhilmanfa', 'telur', 2, '2025-02-16 17:01:13'),
(938, 'fadhilmanfa', 'indomi', 57, '2025-02-18 21:59:45'),
(940, 'fadhilmanfa', 'kuah?', 38, '2025-02-18 21:59:45'),
(941, 'fadhilmanfa', 'jawab', 36, '2025-02-18 02:13:31'),
(1030, 'fadhilmanfa', 'kabarmu?', 2, '2025-02-18 21:59:45'),
(1034, 'fadhilmanfa', 'kalo', 12, '2025-02-19 17:31:55'),
(1035, 'fadhilmanfa', 'belajar', 6, '2025-02-19 17:22:55'),
(1036, 'fadhilmanfa', 'gila', 6, '2025-02-19 17:22:55'),
(1050, 'fadhilmanfa', 'gimana', 2, '2025-02-19 17:31:55'),
(1051, 'fadhilmanfa', 'biar', 2, '2025-02-19 17:31:55'),
(1055, 'fadhilmanfa', 'bjirrr,', 16, '2025-02-27 07:49:58'),
(1056, 'fadhilmanfa', 'seris', 16, '2025-02-27 07:49:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`created_at`);

--
-- Indexes for table `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ujian_id` (`ujian_id`);

--
-- Indexes for table `catatan_guru`
--
ALTER TABLE `catatan_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `emoji_reactions`
--
ALTER TABLE `emoji_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postingan_id` (`postingan_id`);

--
-- Indexes for table `file_soal`
--
ALTER TABLE `file_soal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ujian_id` (`ujian_id`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `jawaban_ujian`
--
ALTER TABLE `jawaban_ujian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ujian_id` (`ujian_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_kelas` (`kode_kelas`);

--
-- Indexes for table `kelas_siswa`
--
ALTER TABLE `kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `komentar_postingan`
--
ALTER TABLE `komentar_postingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postingan_id` (`postingan_id`);

--
-- Indexes for table `komentar_replies`
--
ALTER TABLE `komentar_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `komentar_id` (`komentar_id`);

--
-- Indexes for table `lampiran_postingan`
--
ALTER TABLE `lampiran_postingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_postingan` (`postingan_id`);

--
-- Indexes for table `lampiran_tugas`
--
ALTER TABLE `lampiran_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`);

--
-- Indexes for table `likes_postingan`
--
ALTER TABLE `likes_postingan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`postingan_id`,`user_id`);

--
-- Indexes for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`);

--
-- Indexes for table `pg`
--
ALTER TABLE `pg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_period` (`siswa_id`,`semester`,`tahun_ajaran`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `pilihan_jawaban`
--
ALTER TABLE `pilihan_jawaban`
  ADD PRIMARY KEY (`id`),
  ADD KEY `soal_id` (`soal_id`);

--
-- Indexes for table `postingan_kelas`
--
ALTER TABLE `postingan_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kelas` (`kelas_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_knowledge`
--
ALTER TABLE `project_knowledge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`postingan_id`,`user_id`);

--
-- Indexes for table `saga_personality`
--
ALTER TABLE `saga_personality`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ujian_id` (`ujian_id`);

--
-- Indexes for table `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `postingan_id` (`postingan_id`);

--
-- Indexes for table `ujian`
--
ALTER TABLE `ujian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indexes for table `user_character_analysis`
--
ALTER TABLE `user_character_analysis`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_date` (`user_id`,`analysis_date`);

--
-- Indexes for table `user_topics`
--
ALTER TABLE `user_topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_topic` (`user_id`,`topic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=657;

--
-- AUTO_INCREMENT for table `ai_chat_sessions`
--
ALTER TABLE `ai_chat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `bank_soal`
--
ALTER TABLE `bank_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;

--
-- AUTO_INCREMENT for table `catatan_guru`
--
ALTER TABLE `catatan_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `emoji_reactions`
--
ALTER TABLE `emoji_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `file_soal`
--
ALTER TABLE `file_soal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `jawaban_ujian`
--
ALTER TABLE `jawaban_ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `kelas_siswa`
--
ALTER TABLE `kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430;

--
-- AUTO_INCREMENT for table `komentar_postingan`
--
ALTER TABLE `komentar_postingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `komentar_replies`
--
ALTER TABLE `komentar_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lampiran_postingan`
--
ALTER TABLE `lampiran_postingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `lampiran_tugas`
--
ALTER TABLE `lampiran_tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `likes_postingan`
--
ALTER TABLE `likes_postingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pg`
--
ALTER TABLE `pg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pilihan_jawaban`
--
ALTER TABLE `pilihan_jawaban`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postingan_kelas`
--
ALTER TABLE `postingan_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_knowledge`
--
ALTER TABLE `project_knowledge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saga_personality`
--
ALTER TABLE `saga_personality`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ujian`
--
ALTER TABLE `ujian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_character_analysis`
--
ALTER TABLE `user_character_analysis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT for table `user_topics`
--
ALTER TABLE `user_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1132;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  ADD CONSTRAINT `ai_chat_history_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `ai_chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bank_soal`
--
ALTER TABLE `bank_soal`
  ADD CONSTRAINT `bank_soal_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`);

--
-- Constraints for table `catatan_guru`
--
ALTER TABLE `catatan_guru`
  ADD CONSTRAINT `fk_catatan_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_catatan_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_reactions`
--
ALTER TABLE `comment_reactions`
  ADD CONSTRAINT `comment_reactions_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `komentar_postingan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emoji_reactions`
--
ALTER TABLE `emoji_reactions`
  ADD CONSTRAINT `emoji_reactions_ibfk_1` FOREIGN KEY (`postingan_id`) REFERENCES `postingan_kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `file_soal`
--
ALTER TABLE `file_soal`
  ADD CONSTRAINT `file_soal_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jawaban_ujian`
--
ALTER TABLE `jawaban_ujian`
  ADD CONSTRAINT `jawaban_ujian_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`),
  ADD CONSTRAINT `jawaban_ujian_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `kelas_siswa`
--
ALTER TABLE `kelas_siswa`
  ADD CONSTRAINT `kelas_siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  ADD CONSTRAINT `kelas_siswa_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `komentar_postingan`
--
ALTER TABLE `komentar_postingan`
  ADD CONSTRAINT `komentar_postingan_ibfk_1` FOREIGN KEY (`postingan_id`) REFERENCES `postingan_kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `komentar_replies`
--
ALTER TABLE `komentar_replies`
  ADD CONSTRAINT `komentar_replies_ibfk_1` FOREIGN KEY (`komentar_id`) REFERENCES `komentar_postingan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lampiran_postingan`
--
ALTER TABLE `lampiran_postingan`
  ADD CONSTRAINT `lampiran_postingan_ibfk_1` FOREIGN KEY (`postingan_id`) REFERENCES `postingan_kelas` (`id`);

--
-- Constraints for table `lampiran_tugas`
--
ALTER TABLE `lampiran_tugas`
  ADD CONSTRAINT `lampiran_tugas_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengumpulan_tugas`
--
ALTER TABLE `pengumpulan_tugas`
  ADD CONSTRAINT `pengumpulan_tugas_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pg`
--
ALTER TABLE `pg`
  ADD CONSTRAINT `fk_statistik_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`);

--
-- Constraints for table `pilihan_jawaban`
--
ALTER TABLE `pilihan_jawaban`
  ADD CONSTRAINT `fk_pilihan_soal` FOREIGN KEY (`soal_id`) REFERENCES `soal_ujian` (`id`);

--
-- Constraints for table `postingan_kelas`
--
ALTER TABLE `postingan_kelas`
  ADD CONSTRAINT `fk_postkelas_guru` FOREIGN KEY (`user_id`) REFERENCES `guru` (`username`),
  ADD CONSTRAINT `fk_postkelas_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);

--
-- Constraints for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD CONSTRAINT `project_documents_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_knowledge`
--
ALTER TABLE `project_knowledge`
  ADD CONSTRAINT `project_knowledge_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `soal_ujian`
--
ALTER TABLE `soal_ujian`
  ADD CONSTRAINT `fk_soal_ujian` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`);

--
-- Constraints for table `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`postingan_id`) REFERENCES `postingan_kelas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ujian`
--
ALTER TABLE `ujian`
  ADD CONSTRAINT `fk_ujian_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`username`),
  ADD CONSTRAINT `fk_ujian_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);

--
-- Constraints for table `user_character_analysis`
--
ALTER TABLE `user_character_analysis`
  ADD CONSTRAINT `user_character_analysis_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `guru` (`username`) ON DELETE CASCADE;

--
-- Constraints for table `user_topics`
--
ALTER TABLE `user_topics`
  ADD CONSTRAINT `user_topics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `guru` (`username`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;