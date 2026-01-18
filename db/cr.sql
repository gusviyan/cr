-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 18, 2026 at 10:55 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cr`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `role` enum('user','admin') NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 3, 'user', 'status', 'Status ticket #3 berubah menjadi In Progress', '/cr/tickets/detail.php?id=3', 1, '2026-01-17 18:51:33'),
(2, NULL, 'admin', 'comment', 'Komentar baru pada ticket #2', '/cr/tickets/detail.php?id=2', 1, '2026-01-17 20:21:31'),
(3, NULL, 'admin', 'ticket', 'Ticket baru dibuat', '/cr/admin/tickets.php', 1, '2026-01-17 20:50:40'),
(4, NULL, 'admin', 'ticket', 'Ticket baru dibuat', '/cr/admin/tickets.php', 1, '2026-01-17 20:51:52'),
(5, NULL, 'admin', 'ticket', 'Ticket baru dibuat', '/cr/admin/tickets.php', 1, '2026-01-17 20:55:14'),
(6, NULL, 'admin', 'ticket', 'Ticket baru dibuat', '/cr/admin/tickets.php', 1, '2026-01-17 21:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text,
  `status` varchar(50) DEFAULT 'New',
  `assigned_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `title`, `category`, `description`, `status`, `assigned_by`, `created_at`) VALUES
(13, 17, 'Penambahan report antibiotik', 'penambahan', 'Penambahan report antibiotik', 'New', NULL, NULL),
(14, 17, 'Penambahan report antibiotik 2', 'penambahan', 'Penambahan report antibiotik', 'New', NULL, '2026-01-18 14:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_attachments`
--

CREATE TABLE `ticket_attachments` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ticket_attachments`
--

INSERT INTO `ticket_attachments` (`id`, `ticket_id`, `filename`) VALUES
(1, 1, 'uploads/696af6620a0ad.jpg'),
(2, 2, 'uploads/696b026b56d33.png'),
(3, 3, 'uploads/696b02f18c80c.jpg'),
(4, 3, 'uploads/696b93b09ceb1.png'),
(5, 4, 'uploads/696b93f88d97a.png'),
(6, 5, 'uploads/696b94c2d2777.png'),
(7, 6, '/cr/uploads/696b98d0a9b46.png'),
(8, 9, '/cr/uploads/att_696b9b901ac9e.png'),
(9, 9, '/cr/uploads/att_696b9b901cf9e.jpg'),
(10, 13, '/cr/uploads/att_696c8d6844d04.png'),
(11, 14, '/cr/uploads/att_696c8dbba80af.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `comment` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ticket_comments`
--

INSERT INTO `ticket_comments` (`id`, `ticket_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 3, 'tes komen user', '2026-01-17 09:43:52'),
(2, 1, 2, 'tes komen admin', '2026-01-17 09:54:07'),
(3, 1, 3, 'adsfwe', '2026-01-17 11:54:23'),
(4, 2, 3, 'tes komen notif', '2026-01-17 20:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comment_attachments`
--

CREATE TABLE `ticket_comment_attachments` (
  `id` int NOT NULL,
  `comment_id` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_status_logs`
--

CREATE TABLE `ticket_status_logs` (
  `id` int NOT NULL,
  `ticket_id` int DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ticket_status_logs`
--

INSERT INTO `ticket_status_logs` (`id`, `ticket_id`, `old_status`, `new_status`, `changed_by`, `created_at`) VALUES
(1, 1, 'New', 'In Progress', 2, '2026-01-17 09:41:26'),
(2, 1, 'In Progress', 'Resolved', 2, '2026-01-17 11:13:16'),
(3, 1, 'Resolved', 'In Progress', 2, '2026-01-17 11:13:17'),
(4, 1, 'In Progress', 'New', 2, '2026-01-17 11:13:32'),
(5, 1, 'New', 'In Progress', 2, '2026-01-17 11:17:14'),
(6, 1, 'In Progress', 'Resolved', 2, '2026-01-17 11:37:45'),
(7, 1, 'Resolved', 'Closed', 2, '2026-01-17 11:53:44'),
(8, 2, 'New', 'In Progress', 2, '2026-01-17 12:03:41'),
(9, 3, 'New', 'In Progress', 2, '2026-01-17 18:51:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(2, 'Administrator', 'admin@cr.local', '$2y$10$DPrEr9BGVhn8vRDilAUt0.jNl4ucXumvrITFiJdIXtKr8sxShaHSK', 'admin'),
(3, 'tes', 'tes@rsperpam', '$2y$10$Z7MuOCi20tjjtFnRDFarL.Hi4SD63GdpJtV7XpQx48.OUC1PvHLVy', 'user'),
(4, 'Gusviyan', 'gusviyan@rsperpam', '$2y$10$WuDfqZvBw.GhWwPcK7SjA.CVWVdXf5dTiyxicxTQWNKHXaxzvj5Ni', 'admin'),
(5, 'Admission', 'adm@rsperpam', '$2y$10$oSsq1FF2pnPTZ7vxnbULN.Ga9y7HP.ecatli/du3/Wl9swc2kw6sm', 'user'),
(6, 'Marketing', 'marketing', '$2y$10$1gAPtPn/US/.gucqTeg/MuG76tjPx51DpsI6cgHm1DoVCODH7.mZ6', 'user'),
(7, 'Erlangga', 'erlangga', '$2y$10$RMNVaYp96ZkYRTJWFE0AVecuZ9K0OPxSXXbfKKzI1Fc3f.By88Jl.', 'admin'),
(8, 'Faried', 'faried', '$2y$10$McHMp1RplteqNrbRFx0TR.arabZYUAvbwEff7FNxCy7pf89xLv2Ly', 'admin'),
(9, 'Herry', 'herry', '$2y$10$PPlBfq3lYvJ1lTwSYVaeiOq5igi8SuPY/N3Qgqp62g7v1amptLfPy', 'admin'),
(10, 'Umum', 'umum', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(11, 'Gizi', 'gizi', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(12, 'Poliklinik', 'poli', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(13, 'Akunting', 'akunting', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(14, 'Berlian', 'berlian', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(15, 'Casemix', 'casemix', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(16, 'Emerald', 'emerald', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(17, 'Farmasi', 'farmasi', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(18, 'Fisioterapi', 'fisio', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(19, 'Gudang Farmasi', 'gudangfarmasi', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(20, 'HCU', 'hcu', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(21, 'Hemodialisa', 'hd', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(22, 'Komite Keperawatan', 'keperawatan', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(23, 'Emergency', 'igd', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(24, 'Komite Mutu', 'mutu', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(25, 'Laboratorium', 'lab', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(26, 'Logistik', 'logistik', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(27, 'Marketing', 'marketing', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(28, 'Mutiara', 'mutiara', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(29, 'NICU / PICU', 'nicu', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(30, 'Office', 'office', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(31, 'Radiologi', 'radiologi', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(32, 'Rekam Medis', 'rm', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(33, 'Ruang Operasi', 'ok', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(34, 'Ruby', 'ruby', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(35, 'Zamrud', 'zamrud', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(36, 'SDM', 'sdm', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user'),
(37, 'Purchasing', 'purchasing', '$2y$10$kfV8./OVdFKTFWy6eBzG..ZEhsjvvDWTkunuw.9YoNVLyAWyIJJJy', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_comment_attachments`
--
ALTER TABLE `ticket_comment_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_comment_attachments`
--
ALTER TABLE `ticket_comment_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ticket_comment_attachments`
--
ALTER TABLE `ticket_comment_attachments`
  ADD CONSTRAINT `ticket_comment_attachments_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `ticket_comments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
