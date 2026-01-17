-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 17, 2026 at 11:34 AM
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
(1, 3, 'tes tiket', 'testiket', 'guysfgywefwefkjwgviuhwef', 'Closed', 2, '2026-01-17 09:39:30'),
(2, 3, 'tes 2', 'tes 2', 'wregergergegerger', 'In Progress', 2, '2026-01-17 10:30:51'),
(3, 3, 'tes 3', 'jherwdf', 'kjefvion', 'New', NULL, '2026-01-17 10:33:05'),
(4, 5, 'tyes adm', 'tes adm', 'segwefgwe', 'New', NULL, '2026-01-17 11:39:14');

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
(3, 3, 'uploads/696b02f18c80c.jpg');

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
(3, 1, 3, 'adsfwe', '2026-01-17 11:54:23');

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
(8, 2, 'New', 'In Progress', 2, '2026-01-17 12:03:41');

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
(6, 'Marketing', 'marketing', '$2y$10$1gAPtPn/US/.gucqTeg/MuG76tjPx51DpsI6cgHm1DoVCODH7.mZ6', 'user');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_attachments`
--
ALTER TABLE `ticket_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket_comment_attachments`
--
ALTER TABLE `ticket_comment_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_status_logs`
--
ALTER TABLE `ticket_status_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
