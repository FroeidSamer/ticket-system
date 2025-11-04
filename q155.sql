-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 10:02 AM
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
-- Database: `q155`
--

-- --------------------------------------------------------

--
-- Table structure for table `file_uploads`
--

CREATE TABLE `file_uploads` (
  `id` int(30) NOT NULL,
  `file_path` text NOT NULL,
  `date_uploaded` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file_uploads`
--

INSERT INTO `file_uploads` (`id`, `file_path`, `date_uploaded`) VALUES
(21, '1727961900_1 (4).jpg', '2024-10-03 16:25:59'),
(22, '1727961900_1 (5).jpg', '2024-10-03 16:25:59'),
(23, '1727961900_1 (2).jpg', '2024-10-03 16:25:59'),
(24, '1727961900_1 (3).jpg', '2024-10-03 16:25:59'),
(25, '1727961900_1 (8).jpg', '2024-10-03 16:25:59'),
(26, '1727961900_1 (9).jpg', '2024-10-03 16:25:59'),
(27, '1727961900_1 (6).jpg', '2024-10-03 16:25:59'),
(28, '1727961900_1 (7).jpg', '2024-10-03 16:25:59'),
(29, '1727961960_1 (10).jpg', '2024-10-03 16:26:00'),
(30, '1727961960_1 (1).jpg', '2024-10-03 16:26:00'),
(31, '1755266520_file_example_MP4_480_1_5MG.mp4', '2025-08-15 17:02:06');

-- --------------------------------------------------------

--
-- Table structure for table `queue_list`
--

CREATE TABLE `queue_list` (
  `id` int(30) NOT NULL,
  `transaction_id` int(30) NOT NULL,
  `window_id` int(30) DEFAULT 0,
  `queue_no` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `type_id` int(2) DEFAULT NULL,
  `transfered` varchar(225) DEFAULT NULL,
  `recall` int(10) NOT NULL DEFAULT 0,
  `called_at` timestamp NULL DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_timestamp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue_list`
--

INSERT INTO `queue_list` (`id`, `transaction_id`, `window_id`, `queue_no`, `status`, `type_id`, `transfered`, `recall`, `called_at`, `date_created`, `created_timestamp`) VALUES
(789, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-17 18:05:55', '2025-10-17 18:05:55'),
(790, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-21 20:47:10', '2025-10-21 20:47:10'),
(791, 1, 1, '1001', 1, 1, NULL, 0, '2025-10-22 09:38:54', '2025-10-22 12:38:14', '2025-10-22 12:38:58'),
(792, 14, 0, '1001', 0, 1, 'عيادة الطبيب', 0, NULL, '2025-10-22 12:38:58', '2025-10-22 12:38:58'),
(793, 1, 0, '1002', 0, NULL, NULL, 0, NULL, '2025-10-22 12:49:05', '2025-10-22 12:49:05'),
(794, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-10-28 23:11:33', '2025-10-28 23:11:33'),
(795, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-11-01 20:06:18', '2025-11-01 20:06:18'),
(796, 1, 1, '1001', 1, 2, NULL, 0, '2025-11-02 15:53:55', '2025-11-02 12:34:33', '2025-11-02 16:54:24'),
(797, 1, 1, '1002', 1, 2, NULL, 0, '2025-11-02 15:54:34', '2025-11-02 15:16:03', '2025-11-02 20:20:28'),
(798, 14, 0, '1001', 0, 2, 'عيادة الطبيب', 0, NULL, '2025-11-02 16:54:24', '2025-11-02 16:54:24'),
(799, 14, 0, '1002', 0, 1, 'عيادة الطبيب', 0, NULL, '2025-11-02 20:20:00', '2025-11-02 20:20:00'),
(800, 14, 0, '1002', 0, 2, 'عيادة الطبيب', 0, NULL, '2025-11-02 20:20:28', '2025-11-02 20:20:28'),
(801, 1, 0, '1003', 0, NULL, NULL, 0, NULL, '2025-11-02 20:51:48', '2025-11-02 20:51:48'),
(802, 1, 0, '1004', 0, NULL, NULL, 0, NULL, '2025-11-02 22:29:54', '2025-11-02 22:29:54'),
(803, 1, 0, '1005', 0, NULL, NULL, 0, NULL, '2025-11-02 22:32:00', '2025-11-02 22:32:00'),
(804, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-11-02 23:49:46', '2025-11-02 23:49:46'),
(805, 1, 1, '1001', 1, NULL, NULL, 0, '2025-11-03 11:06:23', '2025-11-03 11:15:24', '2025-11-03 12:06:23'),
(806, 1, 1, '1002', 1, NULL, NULL, 0, '2025-11-03 11:08:05', '2025-11-03 11:15:51', '2025-11-03 12:08:05'),
(807, 1, 1, '1003', 1, 1, NULL, 0, '2025-11-03 11:09:26', '2025-11-03 11:16:10', '2025-11-03 18:20:48'),
(808, 14, 19, '1003', 1, 1, 'عيادة الطبيب', 0, '2025-11-03 17:28:18', '2025-11-03 18:20:49', '2025-11-03 18:28:18'),
(809, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-11-03 23:35:58', '2025-11-03 23:35:58'),
(810, 1, 0, '1001', 0, NULL, NULL, 0, NULL, '2025-11-03 23:54:04', '2025-11-03 23:54:04');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `password` varchar(225) NOT NULL,
  `name` varchar(225) NOT NULL,
  `image` varchar(225) NOT NULL,
  `ticket_company` enum('on','off') NOT NULL DEFAULT 'off',
  `ticket_logo` enum('on','off') NOT NULL DEFAULT 'off',
  `ticket_date` enum('on','off') NOT NULL DEFAULT 'off',
  `ticket_note` enum('on','off') NOT NULL DEFAULT 'off',
  `note` varchar(225) NOT NULL,
  `period` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `password`, `name`, `image`, `ticket_company`, `ticket_logo`, `ticket_date`, `ticket_note`, `note`, `period`) VALUES
(1, '$2y$10$seGQHo1zV69aHrMF5tMGye/8upyZnDQiIbERVrl2pj8yBtHW0D2Vi', ' ', '1750210560_98AA98AC998598B9-98B998B3998A98B1-98A7998498B598AD998A.webp', 'on', 'off', 'on', 'off', 'تفضل بالجلوس في قسم الإنتظار ,شاكرين صبرك ونتمنى لك الشفاء العاجل', 15);

-- --------------------------------------------------------

--
-- Table structure for table `staff_statistics`
--

CREATE TABLE `staff_statistics` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_statistics`
--

INSERT INTO `staff_statistics` (`id`, `staff_id`, `processed_at`) VALUES
(1, 3, '2024-07-26 21:56:34'),
(2, 3, '2024-07-26 21:57:17'),
(3, 3, '2024-07-27 20:49:37'),
(4, 3, '2024-07-27 20:49:53'),
(5, 3, '2024-07-27 20:49:59'),
(6, 3, '2024-07-27 20:50:07'),
(7, 4, '2024-10-05 15:19:16'),
(8, 8, '2024-10-05 15:36:57'),
(9, 4, '2024-10-05 20:59:46'),
(10, 4, '2024-10-05 21:01:01'),
(11, 4, '2024-10-05 21:07:26'),
(12, 4, '2024-10-05 21:07:49'),
(13, 4, '2024-10-05 21:07:55'),
(14, 4, '2025-06-15 06:20:47'),
(15, 7, '2025-06-15 06:22:08'),
(16, 7, '2025-06-15 06:23:12'),
(17, 7, '2025-06-15 06:23:21'),
(18, 8, '2025-06-15 06:23:57'),
(19, 8, '2025-06-15 06:24:19'),
(20, 8, '2025-06-15 06:24:25'),
(21, 8, '2025-06-15 06:24:31'),
(22, 4, '2025-06-15 06:32:20'),
(23, 8, '2025-06-15 06:33:09'),
(24, 8, '2025-06-15 06:33:16'),
(25, 8, '2025-06-15 06:33:25'),
(26, 8, '2025-06-16 08:30:10'),
(27, 8, '2025-06-18 01:37:28'),
(28, 7, '2025-06-18 01:37:45'),
(29, 6, '2025-06-18 01:38:00'),
(30, 5, '2025-06-18 01:38:23'),
(31, 3, '2025-06-18 01:38:42'),
(32, 4, '2025-06-18 01:39:03'),
(33, 4, '2025-06-18 01:42:55'),
(34, 4, '2025-06-18 01:42:57'),
(35, 4, '2025-06-18 01:42:59'),
(36, 4, '2025-06-18 01:43:00'),
(37, 4, '2025-06-18 01:43:01'),
(38, 4, '2025-06-18 01:43:02'),
(39, 4, '2025-06-18 01:43:02'),
(40, 4, '2025-06-18 01:43:05'),
(41, 4, '2025-06-18 01:43:06'),
(42, 8, '2025-06-18 01:43:57'),
(43, 8, '2025-06-18 01:44:00'),
(44, 4, '2025-06-27 01:50:08'),
(45, 8, '2025-07-28 21:43:41'),
(46, 4, '2025-07-28 21:45:08'),
(47, 4, '2025-08-06 12:29:51'),
(48, 4, '2025-08-06 12:32:49'),
(49, 4, '2025-08-06 12:39:54'),
(50, 4, '2025-08-06 12:48:58'),
(51, 4, '2025-08-06 15:19:21'),
(52, 6, '2025-08-06 15:40:08'),
(53, 4, '2025-08-06 15:51:18'),
(54, 6, '2025-08-06 15:51:37'),
(55, 4, '2025-08-15 19:44:26'),
(56, 5, '2025-08-15 19:46:47'),
(57, 4, '2025-08-15 19:53:07'),
(58, 5, '2025-08-15 19:53:37'),
(59, 4, '2025-08-15 19:54:30'),
(60, 4, '2025-08-15 19:54:41'),
(61, 5, '2025-08-15 19:55:01'),
(62, 4, '2025-08-15 19:56:27'),
(63, 4, '2025-08-15 20:03:08'),
(64, 4, '2025-08-15 20:03:56'),
(65, 4, '2025-08-15 20:06:01'),
(66, 4, '2025-08-15 20:06:15'),
(67, 5, '2025-08-15 20:10:31'),
(68, 4, '2025-08-16 20:01:56'),
(69, 4, '2025-08-16 20:02:08'),
(70, 4, '2025-08-16 20:04:41'),
(71, 4, '2025-08-16 20:04:47'),
(72, 5, '2025-08-16 20:04:57'),
(73, 5, '2025-08-16 20:05:07'),
(74, 4, '2025-08-16 20:25:04'),
(75, 4, '2025-08-16 20:25:41'),
(76, 4, '2025-08-16 20:28:18'),
(77, 4, '2025-08-16 20:28:26'),
(78, 4, '2025-08-16 20:30:33'),
(79, 5, '2025-08-16 20:33:07'),
(80, 5, '2025-08-16 20:33:17'),
(81, 4, '2025-08-18 18:22:42'),
(82, 4, '2025-08-18 18:24:14'),
(83, 4, '2025-08-18 18:29:25'),
(84, 4, '2025-08-18 18:31:41'),
(85, 4, '2025-08-18 18:32:16'),
(86, 4, '2025-08-18 18:34:49'),
(87, 5, '2025-08-18 19:44:51'),
(88, 5, '2025-08-18 19:45:09'),
(89, 5, '2025-08-18 19:45:35'),
(90, 7, '2025-08-18 19:50:29'),
(91, 7, '2025-08-18 19:51:48'),
(92, 4, '2025-08-18 19:56:12'),
(93, 4, '2025-08-18 19:56:30'),
(94, 4, '2025-08-18 19:57:34'),
(95, 5, '2025-08-18 19:58:22'),
(96, 5, '2025-08-18 19:58:48'),
(97, 5, '2025-08-18 20:01:55'),
(98, 7, '2025-08-18 20:05:44'),
(99, 5, '2025-08-18 20:06:11'),
(100, 7, '2025-08-18 20:08:34'),
(101, 5, '2025-08-18 20:09:18'),
(102, 4, '2025-08-18 20:10:47'),
(103, 4, '2025-08-18 20:11:06'),
(104, 4, '2025-08-18 20:11:25'),
(105, 5, '2025-08-18 20:13:10'),
(106, 5, '2025-08-18 20:13:34'),
(107, 5, '2025-08-18 20:14:01'),
(108, 7, '2025-08-18 20:14:30'),
(109, 7, '2025-08-18 20:14:47'),
(110, 4, '2025-08-18 20:41:18'),
(111, 5, '2025-08-18 20:42:39'),
(112, 7, '2025-08-18 20:43:40'),
(113, 4, '2025-08-18 20:45:24'),
(114, 4, '2025-08-18 20:45:55'),
(115, 5, '2025-08-18 20:46:33'),
(116, 5, '2025-08-18 20:46:50'),
(117, 4, '2025-08-19 15:23:47'),
(118, 4, '2025-08-19 15:34:47'),
(119, 4, '2025-08-19 15:35:05'),
(120, 4, '2025-08-19 15:35:26'),
(121, 5, '2025-08-19 15:36:01'),
(122, 5, '2025-08-19 15:36:50'),
(123, 4, '2025-08-31 12:32:12'),
(124, 4, '2025-08-31 12:32:31'),
(125, 4, '2025-08-31 12:33:17'),
(126, 4, '2025-08-31 12:33:34'),
(127, 4, '2025-08-31 12:34:00'),
(128, 4, '2025-08-31 12:34:11'),
(129, 4, '2025-08-31 12:34:19'),
(130, 5, '2025-08-31 12:35:13'),
(131, 5, '2025-08-31 12:36:59'),
(132, 9, '2025-08-31 12:38:18'),
(133, 5, '2025-08-31 12:38:37'),
(134, 10, '2025-08-31 12:40:55'),
(135, 10, '2025-08-31 12:45:38'),
(136, 9, '2025-08-31 12:47:25'),
(137, 9, '2025-08-31 12:47:41'),
(138, 9, '2025-08-31 12:48:00'),
(139, 10, '2025-08-31 12:48:17'),
(140, 10, '2025-08-31 12:48:29'),
(141, 10, '2025-08-31 12:49:34'),
(142, 9, '2025-08-31 12:49:45'),
(143, 9, '2025-08-31 12:50:01'),
(144, 9, '2025-08-31 12:50:17'),
(145, 10, '2025-08-31 12:50:32'),
(146, 9, '2025-08-31 12:51:50'),
(147, 9, '2025-08-31 12:51:59'),
(148, 9, '2025-08-31 12:52:44'),
(149, 9, '2025-08-31 12:53:11'),
(150, 9, '2025-08-31 12:53:43'),
(151, 9, '2025-08-31 12:54:24'),
(152, 10, '2025-08-31 12:55:42'),
(153, 10, '2025-08-31 12:56:09'),
(154, 10, '2025-08-31 12:56:15'),
(155, 10, '2025-08-31 12:56:22'),
(156, 10, '2025-08-31 12:56:29'),
(157, 10, '2025-08-31 12:56:34'),
(158, 4, '2025-08-31 13:00:38'),
(159, 4, '2025-08-31 13:00:54'),
(160, 4, '2025-08-31 13:01:14'),
(161, 4, '2025-08-31 13:01:30'),
(162, 4, '2025-08-31 13:02:16'),
(163, 4, '2025-08-31 13:02:40'),
(164, 4, '2025-08-31 13:06:01'),
(165, 4, '2025-08-31 13:17:40'),
(166, 4, '2025-08-31 13:17:57'),
(167, 4, '2025-08-31 13:18:12'),
(168, 4, '2025-08-31 13:18:32'),
(169, 5, '2025-08-31 13:19:01'),
(170, 5, '2025-08-31 13:19:19'),
(171, 5, '2025-08-31 13:21:28'),
(172, 5, '2025-08-31 13:21:49'),
(173, 4, '2025-09-01 16:09:36'),
(174, 4, '2025-09-01 16:13:55'),
(175, 4, '2025-10-16 17:43:24'),
(176, 4, '2025-10-22 09:38:54'),
(177, 4, '2025-11-02 14:53:55'),
(178, 4, '2025-11-02 14:54:34'),
(179, 4, '2025-11-03 10:06:23'),
(180, 4, '2025-11-03 10:08:05'),
(181, 4, '2025-11-03 10:09:26'),
(182, 5, '2025-11-03 16:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `color` varchar(50) NOT NULL,
  `ordering` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `type`, `color`, `ordering`) VALUES
(1, 'A', '#ff3300', '3'),
(2, 'B', '#00ff00', '4');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Inactive,=1 Active',
  `sorting` enum('on','off') NOT NULL DEFAULT 'off',
  `active` enum('on','off') DEFAULT 'on',
  `priority` enum('on','off') NOT NULL,
  `symbol` varchar(1) DEFAULT NULL,
  `numberFrom` int(11) NOT NULL DEFAULT 0,
  `numberTo` int(11) NOT NULL DEFAULT 0,
  `type` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `name`, `status`, `sorting`, `active`, `priority`, `symbol`, `numberFrom`, `numberTo`, `type`) VALUES
(1, 'عيادة الفرز', 1, 'off', 'on', 'off', 'A', 100, 199, 'sorting'),
(14, 'عيادة الطبيب', 1, 'off', 'off', 'off', 'B', 200, 299, 'doctor'),
(16, 'قسم الملاحظة', 1, 'off', 'off', 'off', '', 0, 0, 'notes');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_windows`
--

CREATE TABLE `transaction_windows` (
  `id` int(30) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transaction_ids` text DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(100) DEFAULT 1 COMMENT '0=Inactive,1=Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_windows`
--

INSERT INTO `transaction_windows` (`id`, `transaction_id`, `transaction_ids`, `name`, `status`) VALUES
(1, NULL, '1', 'عيادة الفرز 1', 1),
(19, NULL, '14', 'عيادة الطبيب 1', 1),
(21, NULL, '16', 'قسم الملاحظة 2', 1),
(25, NULL, '1', 'عيادة الفرز 2', 1),
(26, NULL, '14', 'عيادة الطبيب 2', 1),
(27, NULL, '16', '1 قسم الملاحظة ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `window_id` int(30) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 2 COMMENT '1 = Admin, 2= staff',
  `transfer` enum('yes','no') NOT NULL DEFAULT 'no',
  `username` varchar(100) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `window_id`, `type`, `transfer`, `username`, `password`) VALUES
(1, 'Administrator', 0, 1, 'yes', 'admin', '$2y$10$8Lm7szkoAI77iKISxQlAGu9yvPSPTUUUX62454uWU/QLSAKORvlNG'),
(4, 'عيادة الفرز 1', 1, 2, 'yes', 'ER1', '$2y$10$8Lm7szkoAI77iKISxQlAGu9yvPSPTUUUX62454uWU/QLSAKORvlNG'),
(5, 'عيادة الطبيب 1', 19, 2, 'yes', 'ER3', '$2y$10$chFK2xvRPpjCTmaSDCGY8.jlHv4b0Cdc7gkLMZM0f7uRydLHp7Rqy'),
(7, 'قسم الملاحظة A', 21, 2, 'no', 'ER5', '$2y$10$WY4TGY1lvnQ2w3DYKCai.OubI/lXXr2jd5Sr1M11Z02gBgnptjDMq'),
(9, 'عيادة الفرز 2', 25, 2, 'no', 'er2', '$2y$10$viU2wej0xMkZb6WF.m01H.CBzk6y03Lb4Smmq3KarjGx40NKtYJOi'),
(10, 'عيادة الطبيب 2', 26, 2, 'yes', 'er4', '$2y$10$vKkeX7kaSywNVRdH6FOgrOjctBQ3B3lg7Jybj19UM7/cGT9kQ8AX2'),
(11, 'الملاحظة 2', 27, 2, 'no', 'ER6', '$2y$10$WZ35WOn2AsMniynHSyPPOutCSqr1zxjIMEWDrBu.UTu4084XPKJ7e');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `transaction_id`) VALUES
(14, 4, 14),
(16, 5, 16),
(17, 10, 16);

-- --------------------------------------------------------

--
-- Table structure for table `waiting_stats`
--

CREATE TABLE `waiting_stats` (
  `id` int(11) NOT NULL,
  `queue_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  `arrival_time` datetime NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `waiting_duration` int(11) DEFAULT NULL COMMENT 'in seconds',
  `service_duration` int(11) DEFAULT NULL COMMENT 'in seconds'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `waiting_stats`
--

INSERT INTO `waiting_stats` (`id`, `queue_id`, `transaction_id`, `status_id`, `arrival_time`, `start_time`, `end_time`, `waiting_duration`, `service_duration`) VALUES
(40, 707, 1, NULL, '2025-08-19 18:21:56', '2025-08-19 18:23:47', NULL, NULL, NULL),
(41, 708, 1, NULL, '2025-08-19 18:22:01', '2025-08-19 18:34:47', '2025-08-19 18:34:53', 766, 6),
(42, 709, 1, NULL, '2025-08-19 18:22:06', '2025-08-19 18:35:05', '2025-08-19 18:35:11', 779, 6),
(43, 710, 1, NULL, '2025-08-19 18:34:08', '2025-08-19 18:35:26', '2025-08-19 18:35:36', 78, 10),
(44, 711, 1, NULL, '2025-08-19 18:34:17', NULL, NULL, NULL, NULL),
(45, 712, 14, NULL, '2025-08-19 18:34:53', '2025-08-19 18:36:01', '2025-08-19 18:36:43', 68, 42),
(46, 713, 14, NULL, '2025-08-19 18:35:11', '2025-08-19 18:36:50', NULL, NULL, NULL),
(47, 714, 14, NULL, '2025-08-19 18:35:36', NULL, NULL, NULL, NULL),
(48, 715, 16, NULL, '2025-08-19 18:36:43', NULL, NULL, NULL, NULL),
(49, 716, 1, NULL, '2025-08-21 22:59:08', NULL, NULL, NULL, NULL),
(50, 717, 1, NULL, '2025-08-21 23:00:42', NULL, NULL, NULL, NULL),
(51, 718, 1, NULL, '2025-08-31 15:29:11', NULL, '2025-08-31 15:32:09', NULL, NULL),
(52, 719, 1, NULL, '2025-08-31 15:29:19', '2025-08-31 15:32:12', '2025-08-31 15:32:21', 173, 9),
(53, 720, 1, NULL, '2025-08-31 15:29:24', '2025-08-31 15:32:31', '2025-08-31 15:32:35', 187, 4),
(54, 721, 1, NULL, '2025-08-31 15:29:28', NULL, NULL, NULL, NULL),
(55, 722, 1, NULL, '2025-08-31 15:29:33', '2025-08-31 15:33:34', '2025-08-31 15:34:00', 241, 26),
(56, 723, 1, NULL, '2025-08-31 15:29:38', '2025-08-31 15:34:19', '2025-08-31 15:34:24', 281, 5),
(57, 724, 1, NULL, '2025-08-31 15:29:43', NULL, NULL, NULL, NULL),
(58, 725, 1, NULL, '2025-08-31 15:31:10', '2025-08-31 15:33:17', NULL, NULL, NULL),
(59, 726, 1, NULL, '2025-08-31 15:31:17', '2025-08-31 15:34:11', NULL, NULL, NULL),
(60, 727, 1, NULL, '2025-08-31 15:31:50', '2025-08-31 15:38:18', '2025-08-31 15:38:24', 388, 6),
(61, 728, 1, NULL, '2025-08-31 15:31:58', '2025-08-31 15:34:00', NULL, NULL, NULL),
(62, 729, 14, NULL, '2025-08-31 15:32:09', '2025-08-31 15:35:13', NULL, NULL, NULL),
(63, 730, 14, NULL, '2025-08-31 15:32:21', '2025-08-31 15:38:37', NULL, NULL, NULL),
(64, 731, 14, NULL, '2025-08-31 15:32:35', '2025-08-31 15:40:55', NULL, NULL, NULL),
(65, 732, 1, NULL, '2025-08-31 15:32:40', '2025-08-31 15:47:25', '2025-08-31 15:47:27', 885, 2),
(66, 733, 1, NULL, '2025-08-31 15:33:39', '2025-08-31 15:47:41', '2025-08-31 15:47:46', 842, 5),
(67, 734, 1, NULL, '2025-08-31 15:33:45', '2025-08-31 15:48:00', '2025-08-31 15:48:08', 855, 8),
(68, 735, 14, NULL, '2025-08-31 15:34:24', '2025-08-31 15:36:59', NULL, NULL, NULL),
(69, 736, 1, NULL, '2025-08-31 15:37:18', '2025-08-31 15:49:45', '2025-08-31 15:49:49', 747, 4),
(70, 737, 1, NULL, '2025-08-31 15:37:25', '2025-08-31 15:50:01', '2025-08-31 15:50:08', 756, 7),
(71, 738, 14, NULL, '2025-08-31 15:38:24', '2025-08-31 15:45:38', NULL, NULL, NULL),
(72, 739, 1, NULL, '2025-08-31 15:46:13', '2025-08-31 15:50:17', '2025-08-31 15:50:19', 244, 2),
(73, 740, 1, NULL, '2025-08-31 15:46:28', '2025-08-31 15:51:50', '2025-08-31 15:51:53', 322, 3),
(74, 741, 1, NULL, '2025-08-31 15:46:37', '2025-08-31 15:51:59', NULL, NULL, NULL),
(75, 742, 1, NULL, '2025-08-31 15:46:43', '2025-08-31 15:52:44', '2025-08-31 15:52:47', 361, 3),
(76, 743, 1, NULL, '2025-08-31 15:46:50', '2025-08-31 15:53:11', '2025-08-31 15:53:40', 381, 29),
(77, 744, 14, NULL, '2025-08-31 15:47:27', '2025-08-31 15:48:17', '2025-08-31 15:48:29', 50, 12),
(78, 745, 14, NULL, '2025-08-31 15:47:46', '2025-08-31 15:48:29', NULL, NULL, NULL),
(79, 746, 14, NULL, '2025-08-31 15:48:08', '2025-08-31 15:49:34', '2025-08-31 15:50:32', 86, 58),
(80, 747, 14, NULL, '2025-08-31 15:49:49', '2025-08-31 15:50:32', '2025-08-31 15:55:42', 43, 310),
(81, 748, 14, NULL, '2025-08-31 15:50:08', '2025-08-31 15:55:42', NULL, NULL, NULL),
(82, 749, 14, NULL, '2025-08-31 15:50:19', '2025-08-31 15:56:16', '2025-08-31 15:56:22', 357, 6),
(83, 750, 1, NULL, '2025-08-31 15:50:43', '2025-08-31 15:53:43', '2025-08-31 15:54:10', 180, 27),
(84, 751, 1, NULL, '2025-08-31 15:50:51', '2025-08-31 15:54:24', NULL, NULL, NULL),
(85, 752, 1, NULL, '2025-08-31 15:50:57', '2025-08-31 16:00:38', '2025-08-31 16:00:45', 581, 7),
(86, 753, 1, NULL, '2025-08-31 15:51:02', '2025-08-31 16:00:54', '2025-08-31 16:00:57', 592, 3),
(87, 754, 1, NULL, '2025-08-31 15:51:09', '2025-08-31 16:01:14', '2025-08-31 16:01:19', 605, 5),
(88, 755, 1, NULL, '2025-08-31 15:51:14', '2025-08-31 16:01:30', '2025-08-31 16:01:35', 616, 5),
(89, 756, 1, NULL, '2025-08-31 15:51:18', '2025-08-31 16:02:16', '2025-08-31 16:02:20', 658, 4),
(90, 757, 1, NULL, '2025-08-31 15:51:25', '2025-08-31 16:06:01', '2025-08-31 16:06:20', 876, 19),
(91, 758, 1, NULL, '2025-08-31 15:51:33', '2025-08-31 16:02:40', NULL, NULL, NULL),
(92, 759, 14, NULL, '2025-08-31 15:51:53', '2025-08-31 15:56:09', NULL, NULL, NULL),
(93, 760, 14, NULL, '2025-08-31 15:52:47', '2025-08-31 15:56:22', NULL, NULL, NULL),
(94, 761, 14, NULL, '2025-08-31 15:53:40', '2025-08-31 15:56:29', NULL, NULL, NULL),
(95, 762, 14, NULL, '2025-08-31 15:54:10', '2025-08-31 15:56:34', '2025-08-31 16:00:08', 144, 214),
(96, 763, 14, NULL, '2025-08-31 16:00:45', '2025-08-31 16:19:01', '2025-08-31 16:19:19', 1096, 18),
(97, 764, 14, NULL, '2025-08-31 16:00:57', '2025-08-31 16:21:28', '2025-08-31 16:21:47', 1231, 19),
(98, 765, 14, NULL, '2025-08-31 16:01:19', '2025-08-31 16:21:49', NULL, NULL, NULL),
(99, 766, 14, NULL, '2025-08-31 16:01:35', '2025-08-31 16:19:19', NULL, NULL, NULL),
(100, 767, 14, NULL, '2025-08-31 16:02:20', NULL, NULL, NULL, NULL),
(101, 768, 14, NULL, '2025-08-31 16:06:20', NULL, NULL, NULL, NULL),
(102, 769, 1, NULL, '2025-08-31 16:06:34', '2025-08-31 16:17:40', '2025-08-31 16:17:49', 666, 9),
(103, 770, 1, NULL, '2025-08-31 16:06:41', '2025-08-31 16:17:57', '2025-08-31 16:17:59', 676, 2),
(104, 771, 1, NULL, '2025-08-31 16:06:47', '2025-08-31 16:18:12', '2025-08-31 16:18:20', 685, 8),
(105, 772, 1, NULL, '2025-08-31 16:06:53', '2025-08-31 16:18:32', '2025-08-31 16:18:35', 699, 3),
(106, 773, 14, NULL, '2025-08-31 16:17:49', NULL, NULL, NULL, NULL),
(107, 774, 14, NULL, '2025-08-31 16:17:59', NULL, NULL, NULL, NULL),
(108, 775, 14, NULL, '2025-08-31 16:18:20', NULL, NULL, NULL, NULL),
(109, 776, 14, NULL, '2025-08-31 16:18:35', NULL, NULL, NULL, NULL),
(110, 777, 1, NULL, '2025-08-31 16:19:52', NULL, NULL, NULL, NULL),
(111, 778, 1, NULL, '2025-08-31 16:20:01', NULL, NULL, NULL, NULL),
(112, 779, 1, NULL, '2025-08-31 16:20:07', NULL, NULL, NULL, NULL),
(113, 780, 1, NULL, '2025-08-31 16:20:15', NULL, NULL, NULL, NULL),
(114, 781, 16, NULL, '2025-08-31 16:21:47', NULL, NULL, NULL, NULL),
(115, 782, 1, NULL, '2025-09-01 19:07:21', '2025-09-01 19:09:36', NULL, NULL, NULL),
(116, 783, 1, NULL, '2025-09-01 19:09:55', '2025-09-01 19:13:55', NULL, NULL, NULL),
(117, 784, 1, NULL, '2025-09-01 19:10:10', NULL, NULL, NULL, NULL),
(118, 785, 1, NULL, '2025-09-01 19:35:29', NULL, NULL, NULL, NULL),
(119, 786, 1, NULL, '2025-10-16 20:42:48', '2025-10-16 20:43:24', NULL, NULL, NULL),
(120, 787, 1, NULL, '2025-10-16 20:51:07', NULL, NULL, NULL, NULL),
(121, 788, 1, NULL, '2025-10-16 20:56:20', NULL, NULL, NULL, NULL),
(122, 789, 1, NULL, '2025-10-17 18:05:55', NULL, NULL, NULL, NULL),
(123, 790, 1, NULL, '2025-10-21 20:47:10', NULL, NULL, NULL, NULL),
(124, 791, 1, NULL, '2025-10-22 12:38:14', '2025-10-22 12:38:54', '2025-10-22 12:38:58', 40, 4),
(125, 792, 14, NULL, '2025-10-22 12:38:58', NULL, NULL, NULL, NULL),
(126, 793, 1, NULL, '2025-10-22 12:49:05', NULL, NULL, NULL, NULL),
(127, 794, 1, NULL, '2025-10-28 23:11:33', NULL, NULL, NULL, NULL),
(128, 795, 1, NULL, '2025-11-01 20:06:18', NULL, NULL, NULL, NULL),
(129, 796, 1, NULL, '2025-11-02 12:34:33', '2025-11-02 16:53:55', '2025-11-02 16:54:24', 15562, 29),
(130, 797, 1, NULL, '2025-11-02 15:16:03', '2025-11-02 16:54:34', '2025-11-02 20:20:00', 5911, 12326),
(131, 798, 14, NULL, '2025-11-02 16:54:24', NULL, NULL, NULL, NULL),
(132, 799, 14, NULL, '2025-11-02 20:20:00', NULL, NULL, NULL, NULL),
(133, 800, 14, NULL, '2025-11-02 20:20:29', NULL, NULL, NULL, NULL),
(134, 801, 1, NULL, '2025-11-02 20:51:48', NULL, NULL, NULL, NULL),
(135, 802, 1, NULL, '2025-11-02 22:29:54', NULL, NULL, NULL, NULL),
(136, 803, 1, NULL, '2025-11-02 22:32:00', NULL, NULL, NULL, NULL),
(137, 804, 1, NULL, '2025-11-02 23:49:46', NULL, NULL, NULL, NULL),
(138, 805, 1, NULL, '2025-11-03 11:15:25', '2025-11-03 12:06:23', NULL, NULL, NULL),
(139, 806, 1, NULL, '2025-11-03 11:15:51', '2025-11-03 12:08:05', NULL, NULL, NULL),
(140, 807, 1, NULL, '2025-11-03 11:16:10', '2025-11-03 12:09:26', '2025-11-03 18:20:49', 3196, 22283),
(141, 808, 14, NULL, '2025-11-03 18:20:49', '2025-11-03 18:28:19', '2025-11-03 18:28:58', 450, 39),
(142, 809, 1, NULL, '2025-11-03 23:35:58', NULL, NULL, NULL, NULL),
(143, 810, 1, NULL, '2025-11-03 23:54:04', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue_list`
--
ALTER TABLE `queue_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_cons` (`type_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_statistics`
--
ALTER TABLE `staff_statistics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_windows`
--
ALTER TABLE `transaction_windows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cons` (`transaction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `con1` (`transaction_id`),
  ADD KEY `con2` (`user_id`);

--
-- Indexes for table `waiting_stats`
--
ALTER TABLE `waiting_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `queue_id` (`queue_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `status_id` (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file_uploads`
--
ALTER TABLE `file_uploads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `queue_list`
--
ALTER TABLE `queue_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=811;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_statistics`
--
ALTER TABLE `staff_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `transaction_windows`
--
ALTER TABLE `transaction_windows`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `waiting_stats`
--
ALTER TABLE `waiting_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `queue_list`
--
ALTER TABLE `queue_list`
  ADD CONSTRAINT `type_cons` FOREIGN KEY (`type_id`) REFERENCES `status` (`id`);

--
-- Constraints for table `transaction_windows`
--
ALTER TABLE `transaction_windows`
  ADD CONSTRAINT `fk_cons` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `con1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `con2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
