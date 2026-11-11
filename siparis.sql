-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 01:23 AM
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
-- Database: `siparis`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `total_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `available_stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `condition` varchar(255) NOT NULL DEFAULT 'Baik',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventories`
--

INSERT INTO `inventories` (`id`, `code`, `name`, `category`, `description`, `total_stock`, `available_stock`, `condition`, `created_at`, `updated_at`, `image_path`) VALUES
(2, 'AST-002', 'Kamera Canon EOS', 'Multimedia', NULL, 2, 2, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/kamera-canon-eos.png'),
(4, 'AST-006', 'Laptop HP', 'Elektronik', NULL, 3, 2, 'Baik', '2026-10-20 22:48:23', '2026-11-10 04:56:56', 'inventories/laptop-hp.png'),
(5, 'AST-007', 'Flashdisk', 'Aksesori Komputer', NULL, 10, 10, 'Baik', '2026-10-20 22:48:23', '2026-11-10 06:34:55', 'inventories/flashdisk.png'),
(6, 'AST-008', 'Mouse', 'Aksesori Komputer', NULL, 20, 20, 'Baik', '2026-10-20 22:48:23', '2026-11-09 09:08:48', 'inventories/mouse.png'),
(7, 'AST-009', 'Keyboard', 'Aksesori Komputer', NULL, 10, 10, 'Baik', '2026-10-20 22:48:23', '2026-11-10 06:00:57', 'inventories/keyboard.png'),
(9, 'AST-011', 'Monitor', 'Elektronik', NULL, 3, 3, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/monitor.png'),
(10, 'AST-012', 'Obeng mini (1 set)', 'Perkakas', NULL, 1, 1, 'Baik', '2026-10-20 22:48:23', '2026-11-03 18:37:19', 'inventories/obeng-mini-set.png'),
(11, 'AST-013', 'Arduino Uno', 'Elektronik', NULL, 30, 28, 'Baik', '2026-10-20 22:48:23', '2026-11-10 05:19:13', 'inventories/arduino-uno.png'),
(12, 'AST-014', 'Solder', 'Perkakas', NULL, 1, 1, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/solder.png'),
(13, 'AST-015', 'Bor tembok', 'Perkakas', NULL, 1, 1, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/bor-tembok.png'),
(14, 'AST-016', 'Bor mini listrik', 'Perkakas', NULL, 1, 0, 'Baik', '2026-10-20 22:48:23', '2026-11-03 23:56:05', 'inventories/bor-mini-listrik.png'),
(15, 'AST-017', 'Speaker', 'Audio', NULL, 1, 1, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/speaker.png'),
(16, 'AST-018', 'Mic + sound', 'Audio', NULL, 1, 1, 'Baik', '2026-10-20 22:48:23', '2026-10-20 22:48:23', 'inventories/mic-sound.png'),
(18, 'AST-001', 'Proyektor Epson', 'Elektronik', NULL, 4, 4, 'Baik', '2026-11-09 08:41:44', '2026-11-09 09:05:42', 'inventories/proyektor-epson.png'),
(20, 'AST-0020', 'hp', 'Elektronik', NULL, 5, 5, 'Perlu perawatan', '2026-11-10 05:57:14', '2026-11-10 05:57:14', 'inventories/oI7NtYgQXyizzl9ScUvSNw4vs0spTXJeMFDLieep.jpg'),
(21, 'AST-0021', 'KABEL', 'Elektronik', NULL, 4, 4, 'Baik', '2026-11-10 06:31:36', '2026-11-10 06:31:36', 'inventories/50avJbBVzchUhOketUiYOFuVrT4CSFZtor7F0V2n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `borrowed_at` date NOT NULL,
  `due_at` date NOT NULL,
  `returned_at` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `borrower_phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `borrowing_evidence_path` varchar(255) DEFAULT NULL,
  `return_evidence_path` varchar(255) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `inventory_id`, `quantity`, `borrowed_at`, `due_at`, `returned_at`, `status`, `notes`, `borrower_phone`, `created_at`, `updated_at`, `borrowing_evidence_path`, `return_evidence_path`, `rejection_reason`) VALUES
(19, 13, 4, 1, '2026-11-10', '2026-11-11', NULL, 'approved', NULL, '+6288289015435', '2026-11-10 04:56:34', '2026-11-10 04:56:56', NULL, NULL, NULL),
(20, 18, 11, 2, '2026-11-10', '2026-11-11', NULL, 'approved', NULL, '+6288289015435', '2026-11-10 05:17:26', '2026-11-10 05:19:13', NULL, NULL, NULL),
(21, 24, 7, 3, '2026-11-10', '2026-11-11', '2026-11-10', 'returned', NULL, '088289014525', '2026-11-10 05:52:12', '2026-11-10 06:00:57', NULL, 'loan-evidence/return/7XmXO3IkbZ1CdKaY6pkIuHDeTNSMHOy2OaDehS3N.jpg', NULL),
(22, 26, 5, 2, '2026-11-10', '2026-11-12', '2026-11-10', 'returned', NULL, '+6288289015435', '2026-11-10 06:26:30', '2026-11-10 06:34:55', NULL, 'loan-evidence/return/aqby6fnICHrrXpKXrz6ugxAHO7pYOXT2miwVpZ2e.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_10_000003_add_role_to_users_table', 1),
(5, '2026_09_10_000004_create_inventories_table', 1),
(6, '2026_09_10_000005_create_loans_table', 1),
(7, '2026_10_21_080000_add_evidence_paths_to_loans_table', 1),
(8, '2026_11_04_080000_add_rejection_reason_to_loans_table', 2),
(9, '2026_11_09_000000_add_image_path_to_inventories_table', 3),
(10, '2026_11_09_000001_remove_discontinued_inventory_items', 4),
(11, '2026_11_09_000002_restore_proyektor_epson', 5),
(12, '2026_11_09_000003_prepare_real_school_data', 6),
(13, '2026_11_10_000000_add_class_to_users_table', 7),
(14, '2026_11_10_000001_add_borrower_phone_to_loans_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('abdul.aziz.nurrahman@sekolah.id', '$2y$12$kSwdPdgKiThlh4RoOoT2yumPLGrXYx1e/ZX75KaYxNz.xhRIX..9u', '2026-11-10 05:22:53'),
('rosyidah.alif.hakimah@sekolah.id', '$2y$12$jEpQFelf6ZV.gASP8Rf48ObAaM0oZf3Tjcd3ItlS/CX3ijYrb1iHO', '2026-11-10 05:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('g4YqaE1mJl8iMZq9jq45fLvz7prDf0Cd85xoX8LM', 26, '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiREtsc29MVTg4YTk1OFZXS1RlNzFFNDRsaUpQZkk4YTNmeGVNc2JBWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZW1pbmphbWFuIjtzOjU6InJvdXRlIjtzOjExOiJsb2Fucy5pbmRleCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI2O30=', 1794317643),
('l2BISjWKAhhSWX34eYvhkph4I23AXl7cRJrPzgqu', 13, '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQmVQNUY4THVlYXBpM09vV1ZuVHQzSjZvR2wxOW5EMW5CYUE5THllSCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEzO30=', 1794317992),
('rmmM72RuAyUWN4StmrYQE17yVU1xreNs3UVtTRpe', 1, '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUWtLcmN5RTRqYnVObTlpdlpNUG92NkhuZUZxUlN0dG4xYkVodnNYcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1794320978),
('XE1FJDtvr00SzYA6izFeHcDhIeB96rq6CstgoDWs', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic3NlUlpxODFVdkgyZnZJbHlQd0s4dXRmOWNBc3hkZjF1eEJuQ05DOCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1794356229);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'siswa',
  `class` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `class`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Sarana', 'admin@sekolah.id', 'admin', NULL, NULL, '$2y$12$09yKNGlW2d/4FsFoxT5Q.OEz9vHTsztmUsuWIWNcht764HiGdexJ.', 'H86Y4kHwmhllL4hcilmCkBEiQd5QPLSTEfC2Jh1l77kzZIc4qiujgzLjRws5', '2026-10-20 22:48:18', '2026-11-09 09:31:32'),
(13, 'Dwi Esti', 'dwiesti@sekolah.id', 'guru', NULL, NULL, '$2y$12$6m5F2jvXNA7We4hDU9Pf/ORerKN.pEGexZAX9HKWnu71LGxcwk8x.', '9xdJm5AxNaSa5eRW9Rd5T8aNJCccm0vY57XhGjzmUFyJ65pVKQaKyQaBzVbH', '2026-10-20 22:48:23', '2026-11-09 09:31:33'),
(14, 'Tsabitah Qurrataayun', 'tsabitahqurrataayun@gmail.com', 'siswa', NULL, NULL, '$2y$12$itv8VjAB9blRyu5k3mlttuZiqvh.oOgQsGP0GFUxwVo.lsxrtD2Z.', NULL, '2026-11-01 18:36:11', '2026-11-01 18:36:11'),
(18, 'ABDUL AZIZ NURRAHMAN', 'abdul.aziz.nurrahman@sekolah.id', 'siswa', 'XII RPL C', NULL, '$2y$12$feLrDgqHSrF3qK9oDG4dFe7bCEvoKAcxCYK7UJGnGdNpREau/XxKO', 'hGWX6v04NJyZJoyu3JcEW5UzECbxlnx9WPwUQmDJsxZHGX7Vjl7j8K7Denq2', '2026-11-10 04:43:58', '2026-11-10 04:43:58'),
(19, 'ABROR ABIYYI', 'abror.abiyyi@sekolah.id', 'siswa', 'XII RPL B', NULL, '$2y$12$i962EGJGJu.XJhnadqlDA.kpxJiLKRp6z79R9Jdk4ruex1tCB98fK', NULL, '2026-11-10 04:43:58', '2026-11-10 04:43:58'),
(20, 'AISTIA PUSPITANINGRUM', 'aistia.puspitaningrum@sekolah.id', 'siswa', 'XII RPL A', NULL, '$2y$12$v77qF2aRVb0.fpB86NVhT..CKILhaP5yhUvQ0s8riASMPh6jbAO/y', NULL, '2026-11-10 04:43:58', '2026-11-10 04:43:58'),
(21, 'Anjeli Afifah Darajati', 'anjeli.afifah.darajati@sekolah.id', 'siswa', 'XII RPL C', NULL, '$2y$12$FadZRUbIvwUc2BnEDUVGuOcCtovQ19LGzAQ6iBUM04MIa1ELE6swy', NULL, '2026-11-10 04:43:59', '2026-11-10 04:43:59'),
(22, 'ROSYIDAH ALIF HAKIMAH', 'rosyidah.alif.hakimah@sekolah.id', 'siswa', 'XII RPL B', NULL, '$2y$12$c5MqRyiO7TKQbmVsM9JrkeEHHBO/IPMKzhotz/vcJEN61bfQTOLVa', NULL, '2026-11-10 04:43:59', '2026-11-10 04:43:59'),
(23, 'MUHAMMAD FADHIL MUSYAFFA\'', 'muhammad.fadhil.musyaffa@sekolah.id', 'siswa', 'XII RPL A', NULL, '$2y$12$q39wH80c98GvBZmUBo8y6OIcPUIgzaAABQK.gux2AjD7vp1554Kuq', NULL, '2026-11-10 04:43:59', '2026-11-10 04:43:59'),
(24, 'Tsabitah Qurrata A\'yun', 'tsabitahqurrataayun@sekolah.id', 'siswa', NULL, NULL, '$2y$12$6kUVGpYVOtkjDad5ZwB8ruABd2Stk4fokCf3faVo75qUZ8dYSZPFm', NULL, '2026-11-10 05:48:10', '2026-11-10 05:48:10'),
(25, 'mikaila putri azarani', 'mikayla@sekolah.id', 'siswa', NULL, NULL, '$2y$12$VneBPFQ/9/VN.kNmpzBag.2sp/6tvGZ1K/dE4YU4pwYl/xY9wqGzW', NULL, '2026-11-10 06:19:38', '2026-11-10 06:19:38'),
(26, 'reisya kiara maharani', 'reisyamaharani@sekolah.id', 'siswa', NULL, NULL, '$2y$12$ghGF8d4jyqGdURhFvf.RJu9xLJiUGiVS7oG1dC/ODVSA5L8UVzqZG', NULL, '2026-11-10 06:23:53', '2026-11-10 06:23:53');

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
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventories_code_unique` (`code`);

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
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_user_id_foreign` (`user_id`),
  ADD KEY `loans_inventory_id_foreign` (`inventory_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

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
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
