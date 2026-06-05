-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 03:14 AM
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
-- Database: `cems_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `page` varchar(255) DEFAULT NULL,
  `section` varchar(255) DEFAULT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `description`, `page`, `section`, `changes`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'update', 'App\\Models\\Tool', 15, 'Updated Tool: PC VLV RMVR', NULL, NULL, '{\"name\":{\"old\":\"1 PC VLV RMVR\",\"new\":\"PC VLV RMVR\"},\"date_acquired\":{\"old\":\"2025-08-16T00:00:00.000000Z\",\"new\":\"2025-08-16\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 22:48:23', '2025-11-01 22:48:23'),
(2, 1, 'create', 'App\\Models\\Tool', 23, 'Created Tool: Payong', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 23:31:51', '2025-11-01 23:31:51'),
(3, 1, 'create', 'App\\Models\\Tool', 24, 'Created Tool: CALIPER', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 23:35:34', '2025-11-01 23:35:34'),
(4, 1, 'create', 'App\\Models\\Tool', 25, 'Created Tool: Payong', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-01 23:41:28', '2025-11-01 23:41:28'),
(5, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"purchase_price\":{\"old\":\"1200000.00\",\"new\":\"285000.00\"},\"purchase_date\":{\"old\":\"2025-10-25T00:00:00.000000Z\",\"new\":\"2025-10-25\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 07:09:46', '2025-11-02 07:09:46'),
(6, 1, 'delete', 'App\\Models\\ExpenseTransaction', 3, 'Deleted ExpenseTransaction: ID: 3', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 07:31:40', '2025-11-02 07:31:40'),
(7, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 07:59:51', '2025-11-02 07:59:51'),
(8, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 08:00:06', '2025-11-02 08:00:06'),
(9, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"300000.00\",\"new\":\"290000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-02 08:08:31', '2025-11-02 08:08:31'),
(10, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"posted_price\":{\"old\":\"300000.00\",\"new\":\"305000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:04:19', '2025-11-03 04:04:19'),
(11, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"290000.00\",\"new\":\"295000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:04:55', '2025-11-03 04:04:55'),
(12, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"295000.00\",\"new\":null}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:39:48', '2025-11-03 04:39:48'),
(13, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"posted_price\":{\"old\":\"305000.00\",\"new\":\"400000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:43:05', '2025-11-03 04:43:05'),
(14, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:43:29', '2025-11-03 04:43:29'),
(15, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"390000.00\",\"new\":null}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-03 04:45:40', '2025-11-03 04:45:40'),
(16, 1, 'create', 'App\\Models\\Vehicle', 126, 'Created Vehicle: ABC 123', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 19:41:36', '2025-11-07 19:41:36'),
(17, 1, 'create', 'App\\Models\\ExpenseTransaction', 5, 'Created ExpenseTransaction: ID: 5', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 19:46:43', '2025-11-07 19:46:43'),
(18, 1, 'delete', 'App\\Models\\ExpenseTransaction', 5, 'Deleted ExpenseTransaction: ID: 5', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 19:48:51', '2025-11-07 19:48:51'),
(19, 1, 'create', 'App\\Models\\ExpenseTransaction', 6, 'Created ExpenseTransaction: ID: 6', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 19:59:39', '2025-11-07 19:59:39'),
(20, 1, 'delete', 'App\\Models\\ExpenseTransaction', 6, 'Deleted ExpenseTransaction: ID: 6', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 20:08:37', '2025-11-07 20:08:37'),
(21, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 20:35:47', '2025-11-07 20:35:47'),
(22, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"400000.00\",\"new\":\"450000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 20:36:00', '2025-11-07 20:36:00'),
(23, 1, 'update', 'App\\Models\\Vehicle', 124, 'Updated Vehicle: nbz12313', NULL, NULL, '{\"sold_price\":{\"old\":\"450000.00\",\"new\":null}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-07 20:36:29', '2025-11-07 20:36:29'),
(24, 1, 'create', 'App\\Models\\ExpenseTransaction', 7, 'Created ExpenseTransaction: ID: 7', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 00:15:35', '2025-11-08 00:15:35'),
(25, 1, 'create', 'App\\Models\\CashAddition', 10, 'Created CashAddition: ₱1,000.00', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:39:26', '2025-11-08 08:39:26'),
(26, 1, 'create', 'App\\Models\\CashAddition', 10, 'Added cash: ₱1,000.00 to payment method ID: 1 on 2025-11-08', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:39:26', '2025-11-08 08:39:26'),
(27, 1, 'update', 'App\\Models\\CashAddition', 9, 'Updated CashAddition: ₱4,990.00', NULL, NULL, '{\"amount\":{\"old\":\"5000.00\",\"new\":4990}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:40:46', '2025-11-08 08:40:46'),
(28, 1, 'update', 'App\\Models\\CashAddition', 9, 'Updated cash addition: ₱4,990.00', NULL, NULL, '{\"amount\":{\"old\":\"5000.00\",\"new\":4990},\"addition_date\":{\"old\":\"2025-11-08T00:00:00.000000Z\",\"new\":\"2025-11-08\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:40:46', '2025-11-08 08:40:46'),
(29, 1, 'delete', 'App\\Models\\CashAddition', 10, 'Deleted cash addition: ₱1,000.00 from payment method ID: 1', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:41:25', '2025-11-08 08:41:25'),
(30, 1, 'delete', 'App\\Models\\CashAddition', 10, 'Deleted CashAddition: ₱1,000.00', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:41:25', '2025-11-08 08:41:25'),
(31, 1, 'delete', 'App\\Models\\CashAddition', 9, 'Deleted cash addition: ₱4,990.00 from payment method ID: 1', NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:43:32', '2025-11-08 08:43:32'),
(32, 1, 'delete', 'App\\Models\\Vehicle', 126, 'Deleted Vehicle: ABC 123', 'Vehicles Destroy', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:47:57', '2025-11-08 08:47:57'),
(33, 1, 'create', 'App\\Models\\Employee', 25, 'Created Employee: ID: 25', 'Employees Store', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:48:59', '2025-11-08 08:48:59'),
(34, 1, 'update', 'App\\Models\\Employee', 25, 'Updated Employee: ID: 25', 'Employees Update', 'Employee Management', '{\"contract_start\":{\"old\":\"2025-11-09T00:00:00.000000Z\",\"new\":\"2025-11-09T00:00:00.000000Z\"},\"birthdate\":{\"old\":\"2025-11-13T00:00:00.000000Z\",\"new\":\"2025-11-13T00:00:00.000000Z\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:54:34', '2025-11-08 08:54:34'),
(35, 1, 'update', 'App\\Models\\Employee', 25, 'Updated Employee: ID: 25', 'Employees Update', 'Employee Management', '{\"contract_start\":{\"old\":\"2025-11-09T00:00:00.000000Z\",\"new\":\"2025-11-09T00:00:00.000000Z\"},\"birthdate\":{\"old\":\"2025-11-13T00:00:00.000000Z\",\"new\":\"2025-11-13T00:00:00.000000Z\"},\"primary_photo\":{\"old\":\"employees\\/photos\\/40d8a1ad-29c9-4c51-83ef-2297d41752f2.jpg\",\"new\":\"employees\\/photos\\/06f757f3-f029-445a-8b07-b8653a9bf81f.jpg\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 08:55:01', '2025-11-08 08:55:01'),
(36, 1, 'update', 'App\\Models\\Vehicle', 125, 'Updated Vehicle: adfa44', 'Vehicles Posted Price Update', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:01:31', '2025-11-08 09:01:31'),
(37, 1, 'update', 'App\\Models\\ExpenseItem', 122, 'Updated expense item: Postage', 'Expenses Items Update', 'Expense Items', '{\"expense_date\":{\"old\":\"2025-11-11T00:00:00.000000Z\",\"new\":\"2025-11-11\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-08 09:20:42', '2025-11-08 09:20:42'),
(38, 1, 'create', 'App\\Models\\Vehicle', 127, 'Created Vehicle: 234234', 'Vehicles Store', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 21:19:51', '2025-11-19 21:19:51'),
(39, 1, 'delete', 'App\\Models\\Vehicle', 127, 'Deleted Vehicle: 234234', 'Vehicles Destroy', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 21:20:09', '2025-11-19 21:20:09'),
(40, 1, 'create', 'App\\Models\\Vehicle', 128, 'Created Vehicle: nbz123132', 'Vehicles Store', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-20 06:48:06', '2025-11-20 06:48:06'),
(41, 1, 'create', 'App\\Models\\Vehicle', 129, 'Created Vehicle: 123ABC', 'Vehicles Store', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 16:22:02', '2025-11-24 16:22:02'),
(42, 1, 'update', 'App\\Models\\Vehicle', 129, 'Updated Vehicle: 123ABC', 'Vehicles Update', NULL, '{\"body_type\":{\"old\":\"Sedan\",\"new\":\"SUV\"},\"purchase_date\":{\"old\":\"2025-11-25T00:00:00.000000Z\",\"new\":\"2025-11-25\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 16:22:41', '2025-11-24 16:22:41'),
(43, 1, 'delete', 'App\\Models\\Vehicle', 129, 'Deleted Vehicle: 123ABC', 'Vehicles Destroy', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 21:35:26', '2025-11-24 21:35:26'),
(44, 1, 'create', 'App\\Models\\ExpenseTransaction', 109, 'Created ExpenseTransaction: ID: 109', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:05', '2025-11-24 23:09:05'),
(45, 1, 'create', 'App\\Models\\ExpenseItem', 219, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:05', '2025-11-24 23:09:05'),
(46, 1, 'create', 'App\\Models\\ExpenseTransaction', 110, 'Created ExpenseTransaction: ID: 110', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:26', '2025-11-24 23:09:26'),
(47, 1, 'create', 'App\\Models\\ExpenseItem', 220, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:26', '2025-11-24 23:09:26'),
(48, 1, 'create', 'App\\Models\\ExpenseTransaction', 111, 'Created ExpenseTransaction: ID: 111', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:30', '2025-11-24 23:09:30'),
(49, 1, 'create', 'App\\Models\\ExpenseItem', 221, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:09:30', '2025-11-24 23:09:30'),
(50, 1, 'create', 'App\\Models\\ExpenseTransaction', 112, 'Created ExpenseTransaction: ID: 112', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:10:13', '2025-11-24 23:10:13'),
(51, 1, 'create', 'App\\Models\\ExpenseItem', 222, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:10:13', '2025-11-24 23:10:13'),
(52, 1, 'create', 'App\\Models\\ExpenseTransaction', 113, 'Created ExpenseTransaction: ID: 113', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:17:38', '2025-11-24 23:17:38'),
(53, 1, 'create', 'App\\Models\\ExpenseItem', 223, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:17:38', '2025-11-24 23:17:38'),
(54, 1, 'create', 'App\\Models\\ExpenseTransaction', 114, 'Created ExpenseTransaction: ID: 114', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:17:42', '2025-11-24 23:17:42'),
(55, 1, 'create', 'App\\Models\\ExpenseItem', 224, 'Created expense item: Test (₱50,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:17:42', '2025-11-24 23:17:42'),
(56, 1, 'delete', 'App\\Models\\ExpenseItem', 224, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:22:32', '2025-11-24 23:22:32'),
(57, 1, 'delete', 'App\\Models\\ExpenseItem', 223, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:23:04', '2025-11-24 23:23:04'),
(58, 1, 'delete', 'App\\Models\\ExpenseItem', 219, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:23:18', '2025-11-24 23:23:18'),
(59, 1, 'delete', 'App\\Models\\ExpenseItem', 220, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:23:29', '2025-11-24 23:23:29'),
(60, 1, 'delete', 'App\\Models\\ExpenseItem', 221, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:23:50', '2025-11-24 23:23:50'),
(61, 1, 'delete', 'App\\Models\\ExpenseItem', 222, 'Deleted expense item: Test', 'Expenses Items Destroy', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 23:24:04', '2025-11-24 23:24:04'),
(62, 1, 'create', 'App\\Models\\ExpenseTransaction', 115, 'Created ExpenseTransaction: ID: 115', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 05:30:57', '2025-11-29 05:30:57'),
(63, 1, 'create', 'App\\Models\\ExpenseItem', 225, 'Created expense item: Aircon (₱5,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 05:30:57', '2025-11-29 05:30:57'),
(64, 1, 'create', 'App\\Models\\ExpenseTransaction', 116, 'Created ExpenseTransaction: ID: 116', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 05:34:43', '2025-11-29 05:34:43'),
(65, 1, 'create', 'App\\Models\\ExpenseItem', 226, 'Created expense item: Aircon (₱1,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 05:34:43', '2025-11-29 05:34:43'),
(66, 1, 'update', 'App\\Models\\Vehicle', 123, 'Updated Vehicle: 12313A', 'Vehicles Update', NULL, '{\"purchase_price\":{\"old\":\"1.00\",\"new\":\"500000.00\"},\"purchase_date\":{\"old\":\"2025-10-25T00:00:00.000000Z\",\"new\":\"2025-10-25\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 05:46:31', '2025-11-29 05:46:31'),
(67, 1, 'create', 'App\\Models\\Vehicle', 130, 'Created Vehicle: nbz9090', 'Vehicles Store', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 06:05:48', '2025-11-29 06:05:48'),
(68, 1, 'update', 'App\\Models\\Vehicle', 130, 'Updated Vehicle: nbz9090', 'Vehicles Update', NULL, '{\"purchase_date\":{\"old\":\"2025-01-01T00:00:00.000000Z\",\"new\":\"2025-01-01\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 06:08:39', '2025-11-29 06:08:39'),
(69, 1, 'update', 'App\\Models\\Vehicle', 130, 'Updated Vehicle: nbz9090', 'Vehicles Update', NULL, '{\"purchase_date\":{\"old\":\"2025-01-01T00:00:00.000000Z\",\"new\":\"2025-01-01\"},\"selling_price\":{\"old\":\"700000.00\",\"new\":\"750000.00\"},\"posted_price\":{\"old\":\"700000.00\",\"new\":\"750000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 16:22:43', '2025-11-29 16:22:43'),
(70, 1, 'update', 'App\\Models\\Vehicle', 130, 'Updated Vehicle: nbz9090', 'Vehicles Posted Price Update', NULL, '{\"posted_price\":{\"old\":\"750000.00\",\"new\":\"753000.00\"},\"selling_price\":{\"old\":\"750000.00\",\"new\":\"753000.00\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 16:23:13', '2025-11-29 16:23:13'),
(71, 1, 'create', 'App\\Models\\ExpenseTransaction', 117, 'Created ExpenseTransaction: ID: 117', 'Expenses Store', 'Expense Transactions', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 17:11:17', '2025-11-29 17:11:17'),
(72, 1, 'create', 'App\\Models\\ExpenseItem', 227, 'Created expense item: Aircon (₱3,000.00)', 'Expenses Store', 'Expense Items', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 17:11:17', '2025-11-29 17:11:17'),
(73, 1, 'update', 'App\\Models\\ExpenseItem', 227, 'Updated expense item: Aircon, Battery', 'Expenses Items Update', 'Expense Items', '{\"expense_date\":{\"old\":\"2025-11-30T00:00:00.000000Z\",\"new\":\"2025-11-30\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.1.39 Chrome/138.0.7204.251 Electron/37.7.0 Safari/537.36', '2025-11-29 17:44:50', '2025-11-29 17:44:50'),
(74, 1, 'create', 'App\\Models\\Vehicle', 131, 'Created Vehicle: ABC 123', 'Vehicles Store', NULL, NULL, '180.191.156.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-03 09:53:24', '2025-12-03 09:53:24'),
(75, 1, 'update', 'App\\Models\\ExpenseItem', 226, 'Updated expense item: Aircon', 'Expenses Items Update', 'Expense Items', '{\"expense_date\":{\"old\":\"2025-11-29T00:00:00.000000Z\",\"new\":\"2025-11-29\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 09:02:22', '2026-02-24 09:02:22'),
(76, 1, 'update', 'App\\Models\\Vehicle', 130, 'Updated Vehicle: nbz9090', 'Vehicles Update', NULL, '{\"status\":{\"old\":\"Forfeited\",\"new\":\"Available\"},\"purchase_date\":{\"old\":\"2025-01-01T00:00:00.000000Z\",\"new\":\"2025-01-01\"}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 20:01:54', '2026-02-27 20:01:54');

-- --------------------------------------------------------

--
-- Table structure for table `agent_bolo_agents`
--

CREATE TABLE `agent_bolo_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `sales_executive` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `facebook_profile_link` varchar(500) DEFAULT NULL,
  `facebook_page_link` varchar(500) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `signed_bolo` varchar(255) DEFAULT NULL,
  `one_valid_id` varchar(255) DEFAULT NULL,
  `joined_sales_associate_gc` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agent_bolo_agents`
--

INSERT INTO `agent_bolo_agents` (`id`, `name`, `sales_executive`, `contact_number`, `facebook_profile_link`, `facebook_page_link`, `email`, `signed_bolo`, `one_valid_id`, `joined_sales_associate_gc`, `notes`, `created_at`, `updated_at`) VALUES
(4, 'Agent 1', 'MARK', '8989898', 'https://facebook.com', 'https://facebook.com', 'agent@gmail.com', 'yes', NULL, '2026-02-27', NULL, '2026-02-27 18:59:14', '2026-02-27 18:59:14');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date_added_to_schedule` date DEFAULT NULL,
  `added_by` varchar(100) DEFAULT NULL,
  `customer_first_name` varchar(255) NOT NULL,
  `customer_last_name` varchar(255) NOT NULL,
  `customer_phone_number` varchar(50) DEFAULT NULL,
  `showroom` varchar(100) DEFAULT NULL,
  `date_of_visit` date DEFAULT NULL,
  `preferred_unit` varchar(500) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `sales_exec_who_assisted` varchar(100) DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `notes_of_visit` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buffing_records`
--

CREATE TABLE `buffing_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `buffing_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `buffing_records`
--

INSERT INTO `buffing_records` (`id`, `vehicle_id`, `employee_id`, `buffing_date`, `status`, `notes`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 130, 23, '2026-02-28', 'Completed', 'test', '2026-02-27 16:05:12', '2026-02-27 16:05:12', '2026-02-27 16:05:12');

-- --------------------------------------------------------

--
-- Table structure for table `car_financing_settings`
--

CREATE TABLE `car_financing_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `financing_scheme_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `year_model_range` varchar(50) NOT NULL COMMENT 'e.g. 2026-2022, 2021-2020',
  `chattel_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `chattel_fee_percent` decimal(5,2) DEFAULT NULL COMMENT '% of Amount Financed (AF)',
  `insurance_initial` decimal(12,2) NOT NULL DEFAULT 0.00,
  `no_pdc_charge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `term_pct_12` decimal(8,4) NOT NULL DEFAULT 0.1530 COMMENT '15.30% as 0.153',
  `term_pct_24` decimal(8,4) NOT NULL DEFAULT 0.3060,
  `term_pct_36` decimal(8,4) NOT NULL DEFAULT 0.4590,
  `term_pct_48` decimal(8,4) NOT NULL DEFAULT 0.6120,
  `term_pct_60` decimal(8,4) NOT NULL DEFAULT 0.7200,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_financing_settings`
--

INSERT INTO `car_financing_settings` (`id`, `financing_scheme_id`, `year_model_range`, `chattel_fee`, `chattel_fee_percent`, `insurance_initial`, `no_pdc_charge`, `term_pct_12`, `term_pct_24`, `term_pct_36`, `term_pct_48`, `term_pct_60`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-2022', 0.00, 8.00, 10000.00, 1500.00, 0.1530, 0.3060, 0.4590, 0.6120, 0.7200, '2026-02-27 21:16:34', '2026-02-27 21:16:34'),
(2, 1, '2014-2001', 0.00, 5.00, 10000.00, 1500.00, 0.1530, 0.3060, 0.4590, 0.6120, 0.7200, '2026-02-27 21:19:03', '2026-02-27 22:17:12'),
(3, 1, '2023-2025', 0.00, 8.00, 10000.00, 1500.00, 0.1530, 0.3060, 0.4590, 0.6120, 0.7200, '2026-02-27 22:16:19', '2026-02-27 22:25:22'),
(4, 2, '2026-2022', 0.00, 8.00, 10000.00, 1500.00, 0.1530, 0.3060, 0.4590, 0.6120, 0.7200, '2026-02-27 22:39:28', '2026-02-27 22:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `cash_additions`
--

CREATE TABLE `cash_additions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED NOT NULL,
  `addition_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_follow_up_list`
--

CREATE TABLE `client_follow_up_list` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date_of_first_inquiry` date DEFAULT NULL,
  `application` varchar(50) DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `unit_inquired` varchar(255) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `about_what` varchar(255) DEFAULT NULL,
  `sales_exec_1` varchar(100) DEFAULT NULL,
  `date_followed_up_1` date DEFAULT NULL,
  `outcome_1` varchar(255) DEFAULT NULL,
  `notes_1` text DEFAULT NULL,
  `sales_exec_2` varchar(100) DEFAULT NULL,
  `date_followed_up_2` date DEFAULT NULL,
  `outcome_2` varchar(255) DEFAULT NULL,
  `notes_2` text DEFAULT NULL,
  `sales_exec_3` varchar(100) DEFAULT NULL,
  `date_followed_up_3` date DEFAULT NULL,
  `outcome_3` varchar(255) DEFAULT NULL,
  `notes_3` text DEFAULT NULL,
  `sales_exec_4` varchar(100) DEFAULT NULL,
  `date_followed_up_4` date DEFAULT NULL,
  `outcome_4` varchar(255) DEFAULT NULL,
  `notes_4` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_follow_up_list`
--

INSERT INTO `client_follow_up_list` (`id`, `date_of_first_inquiry`, `application`, `client_name`, `contact_number`, `email`, `unit_inquired`, `vehicle_id`, `follow_up_date`, `status`, `notes`, `about_what`, `sales_exec_1`, `date_followed_up_1`, `outcome_1`, `notes_1`, `sales_exec_2`, `date_followed_up_2`, `outcome_2`, `notes_2`, `sales_exec_3`, `date_followed_up_3`, `outcome_3`, `notes_3`, `sales_exec_4`, `date_followed_up_4`, `outcome_4`, `notes_4`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'John Balmaceda', '23232332', NULL, NULL, 131, '2026-02-28', 'In Progress', 'follow up palang', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-27 08:07:12', '2026-02-27 08:07:12'),
(2, NULL, NULL, 'john b', '23232', NULL, NULL, 130, '2026-02-28', 'Pending', 'may dp na', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-27 08:07:48', '2026-02-27 08:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `company_documents`
--

CREATE TABLE `company_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `agent_bolo_agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `link_url` varchar(1000) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `contract_type` varchar(255) NOT NULL DEFAULT 'Other',
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `party_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_sections`
--

CREATE TABLE `custom_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_section_fields`
--

CREATE TABLE `custom_section_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `custom_section_id` bigint(20) UNSIGNED NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','textarea','number','date','email','url','select','checkbox','radio') NOT NULL,
  `field_value` text DEFAULT NULL,
  `field_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_budgets`
--

CREATE TABLE `daily_budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED NOT NULL,
  `budget_date` date NOT NULL,
  `starting_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `added_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_budgets`
--

INSERT INTO `daily_budgets` (`id`, `payment_method_id`, `budget_date`, `starting_balance`, `added_cash`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-11-08', 100000.00, 0.00, NULL, '2025-11-08 06:50:27', '2025-11-08 06:50:27'),
(2, 1, '2025-11-07', 100000.00, 50.00, NULL, '2025-11-08 07:00:58', '2025-11-08 07:00:58'),
(3, 1, '2025-11-06', 30000.00, 0.00, NULL, '2025-11-08 07:03:47', '2025-11-08 07:03:47');

-- --------------------------------------------------------

--
-- Table structure for table `document_form_templates`
--

CREATE TABLE `document_form_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `form_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_fields`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_form_templates`
--

INSERT INTO `document_form_templates` (`id`, `name`, `document_type`, `form_fields`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'sample custom form', 'PROMISSORY', '[{\"label\":\"Name\",\"type\":\"text\",\"name\":\"name\",\"value\":\"10\",\"options\":[],\"required\":true,\"placeholder\":\"Name\"},{\"label\":\"Address\",\"type\":\"text\",\"name\":\"address\",\"value\":\"address\",\"options\":[],\"required\":true,\"placeholder\":\"address\"}]', 1, '2025-11-20 00:13:04', '2025-11-20 00:13:04'),
(2, 'sample form', 'AR', '[{\"name\":\"name\",\"label\":\"name\",\"type\":\"text\"}]', 1, '2025-11-27 04:41:50', '2025-11-27 04:41:50'),
(4, 'sample template again', 'GENERAL', '[{\"name\":\"checkbox\",\"label\":\"checkbox\",\"type\":\"checkbox\",\"options\":[\"Yes\",\"No\"]},{\"name\":\"name\",\"label\":\"Name\",\"type\":\"text\"},{\"name\":\"number\",\"label\":\"number\",\"type\":\"number\"},{\"name\":\"date_of_birth\",\"label\":\"date of birth\",\"type\":\"date\"}]', 1, '2025-11-28 18:43:26', '2025-11-28 18:43:26');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `contract_start` date DEFAULT NULL,
  `contract_type` enum('PROBATIONARY','REGULAR') DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `sss` varchar(255) DEFAULT NULL,
  `philhealth` varchar(255) DEFAULT NULL,
  `pagibig` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `primary_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `middle_name`, `last_name`, `contract_start`, `contract_type`, `role`, `location`, `sss`, `philhealth`, `pagibig`, `birthdate`, `status`, `notes`, `primary_photo`, `created_at`, `updated_at`) VALUES
(1, 'MARJORIE MAE', 'VILLAFUENTE', 'ALISOSO', '2025-03-03', 'PROBATIONARY', 'ADMIN', 'MAIN', '35-2770266-8', '16-253233815-8', '1213-28380-847', '2000-08-14', 'active', 'New admin staff member', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(2, 'GEOFFREY', 'QUILATAN', 'TABLADA', '2024-09-16', 'REGULAR', 'ADMIN SALES', 'MAIN', '34-8505570-3', '01-026515310-6', '1212-486718-09', '1999-01-27', 'active', 'Experienced admin sales staff', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(3, 'KRISTOFFER JOHN IAN', 'N/A', 'BOLAGAO', '2025-06-25', 'PROBATIONARY', 'BUFFER - GEN. STAFF', 'EXTENSION', NULL, NULL, NULL, '1985-01-10', 'active', 'General staff member at extension branch', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(4, 'MARIA', 'SANTOS', 'CRUZ', '2024-11-15', 'REGULAR', 'DATA ENCODER', 'MAIN', '35-1234567-8', '16-123456789-0', '1213-123456-78', '1995-05-20', 'active', 'Data entry specialist', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(5, 'JUAN', 'DELA', 'CRUZ', '2024-08-01', 'REGULAR', 'DRIVER', 'WAREHOUSE', '35-2345678-9', '16-234567890-1', '1213-234567-89', '1988-12-03', 'active', 'Delivery driver for warehouse operations', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(6, 'PEDRO', 'GARCIA', 'MARTINEZ', '2024-07-10', 'REGULAR', 'MECHANIC', 'ANNEX', '35-3456789-0', '16-345678901-2', '1213-345678-90', '1990-03-15', 'active', 'Automotive mechanic at annex location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(7, 'ANA', 'RODRIGUEZ', 'LOPEZ', '2025-01-15', 'PROBATIONARY', 'SALES ASST. - GEN. STAFF', 'PREMIUM', NULL, NULL, NULL, '1992-07-22', 'active', 'Sales assistant at premium location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(8, 'CARLOS', 'MIGUEL', 'SANTOS', '2024-12-01', 'REGULAR', 'ADMIN', 'MAIN', '35-4567890-1', '16-456789012-3', '1213-456789-01', '1987-09-08', 'active', 'Senior admin staff', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(9, 'SOPHIA', 'ISABEL', 'GONZALES', '2025-02-10', 'PROBATIONARY', 'DATA ENCODER', 'EXTENSION', NULL, NULL, NULL, '1996-11-30', 'active', 'New data encoder at extension', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(10, 'MIGUEL', 'ANGEL', 'TORRES', '2024-10-20', 'REGULAR', 'DRIVER', 'MAIN', '35-5678901-2', '16-567890123-4', '1213-567890-12', '1989-04-12', 'active', 'Main branch driver', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(11, 'ISABELLA', 'ROSE', 'RAMIREZ', '2024-06-05', 'REGULAR', 'ADMIN SALES', 'PREMIUM', '35-6789012-3', '16-678901234-5', '1213-678901-23', '1991-02-18', 'active', 'Admin sales at premium location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(12, 'ANTONIO', 'JOSE', 'HERNANDEZ', '2025-04-01', 'PROBATIONARY', 'MECHANIC', 'WAREHOUSE', NULL, NULL, NULL, '1993-08-25', 'active', 'Warehouse mechanic', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(13, 'VALENTINA', 'MARIA', 'FLORES', '2024-09-30', 'REGULAR', 'SALES ASST. - GEN. STAFF', 'ANNEX', '35-7890123-4', '16-789012345-6', '1213-789012-34', '1994-06-14', 'active', 'Sales assistant at annex', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(14, 'DIEGO', 'ALEJANDRO', 'MORALES', '2024-11-20', 'REGULAR', 'BUFFER - GEN. STAFF', 'MAIN', '35-8901234-5', '16-890123456-7', '1213-890123-45', '1986-10-07', 'active', 'General staff at main office', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(15, 'CAMILA', 'SOFIA', 'VARGAS', '2025-03-15', 'PROBATIONARY', 'DATA ENCODER', 'PREMIUM', NULL, NULL, NULL, '1997-01-28', 'active', 'Data encoder at premium location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(16, 'SANTIAGO', 'CARLOS', 'JIMENEZ', '2024-08-15', 'REGULAR', 'DRIVER', 'EXTENSION', '35-9012345-6', '16-901234567-8', '1213-901234-56', '1990-12-11', 'active', 'Extension branch driver', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(17, 'VALERIA', 'ELENA', 'CASTRO', '2024-07-25', 'REGULAR', 'ADMIN', 'ANNEX', '35-0123456-7', '16-012345678-9', '1213-012345-67', '1988-05-03', 'active', 'Admin at annex location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(18, 'MATEO', 'GABRIEL', 'RUIZ', '2025-01-05', 'PROBATIONARY', 'MECHANIC', 'MAIN', NULL, NULL, NULL, '1995-09-16', 'active', 'Mechanic trainee at main location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(19, 'LUCIA', 'FERNANDA', 'DIAZ', '2024-12-10', 'REGULAR', 'SALES ASST. - GEN. STAFF', 'WAREHOUSE', '35-1234567-8', '16-123456789-0', '1213-123456-78', '1992-03-24', 'active', 'Sales assistant at warehouse', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(20, 'NICOLAS', 'SEBASTIAN', 'HERRERA', '2024-10-05', 'REGULAR', 'ADMIN SALES', 'EXTENSION', '35-2345678-9', '16-234567890-1', '1213-234567-89', '1987-07-19', 'active', 'Admin sales at extension', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(21, 'EMILIA', 'VICTORIA', 'MORENO', '2025-02-20', 'PROBATIONARY', 'BUFFER - GEN. STAFF', 'PREMIUM', NULL, NULL, NULL, '1996-11-02', 'active', 'General staff at premium location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(22, 'LEONARDO', 'ANTONIO', 'GUTIERREZ', '2024-09-10', 'REGULAR', 'DATA ENCODER', 'MAIN', '35-3456789-0', '16-345678901-2', '1213-345678-90', '1989-04-27', 'active', 'Senior data encoder', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(23, 'ADRIANA', 'ISABEL', 'RAMOS', '2024-11-25', 'REGULAR', 'DRIVER', 'ANNEX', '35-4567890-1', '16-456789012-3', '1213-456789-01', '1991-08-13', 'active', 'Driver at annex location', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(24, 'GABRIEL', 'ALEXANDER', 'MENDOZA', '2025-05-01', 'PROBATIONARY', 'MECHANIC', 'EXTENSION', NULL, NULL, NULL, '1994-12-05', 'inactive', 'Former mechanic - resigned', NULL, '2025-10-28 09:19:30', '2025-10-28 09:19:30'),
(25, 'John', 'b', 'balmaceda', '2025-11-09', 'REGULAR', 'DATA ENCODER', 'MAIN', '123123', '123123', '123123', '2025-11-13', 'active', NULL, NULL, '2025-11-08 08:48:59', '2025-11-08 08:55:01');

-- --------------------------------------------------------

--
-- Table structure for table `expense_items`
--

CREATE TABLE `expense_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_transaction_id` bigint(20) UNSIGNED NOT NULL,
  `expense_date` date DEFAULT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `description_details` text DEFAULT NULL,
  `care_of` varchar(255) DEFAULT NULL,
  `requested_by` varchar(255) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `store_shop` varchar(255) DEFAULT NULL,
  `receipt_checked` tinyint(1) NOT NULL DEFAULT 0,
  `receipt_checker` varchar(255) DEFAULT NULL,
  `receipt_check_date` date DEFAULT NULL,
  `cost` decimal(12,2) NOT NULL,
  `payment_tag` enum('Operating','Vehicle','Customer Request') DEFAULT 'Operating',
  `expense_category` varchar(255) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_items`
--

INSERT INTO `expense_items` (`id`, `expense_transaction_id`, `expense_date`, `payment_method_id`, `description`, `description_details`, `care_of`, `requested_by`, `approved_by`, `store_shop`, `receipt_checked`, `receipt_checker`, `receipt_check_date`, `cost`, `payment_tag`, `expense_category`, `vehicle_id`, `created_at`, `updated_at`) VALUES
(14, 4, '2025-11-08', 1, 'Paint', NULL, 'JAY', 'aiko', 'aiko', 'aa', 0, NULL, NULL, 5000.00, 'Vehicle', NULL, 124, '2025-11-02 07:31:15', '2025-11-08 00:38:25'),
(15, 4, '2025-11-08', 2, 'Cluster', NULL, 'jhong', NULL, NULL, NULL, 0, NULL, NULL, 1500.00, 'Vehicle', NULL, 124, '2025-11-02 07:32:49', '2025-11-08 00:38:39'),
(16, 4, '2025-11-08', 4, 'Paper', '9/12/25-REAR DOOR LEFT/ REAR DOOR RIGHT/ TRUNK LID/ REAR BUMPER - JAY', 'Ltms', NULL, NULL, NULL, 0, NULL, NULL, 200.00, 'Vehicle', NULL, 124, '2025-11-02 07:33:33', '2025-11-08 00:38:52'),
(17, 4, '2025-11-08', 3, 'Tyers', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 700.00, 'Vehicle', NULL, 124, '2025-11-02 07:34:00', '2025-11-08 00:39:04'),
(20, 7, '2025-11-08', 1, 'Battery', NULL, 'John', 'John', 'John', 'Somwhere', 1, 'john', '2025-11-08', 2000.00, 'Vehicle', NULL, NULL, '2025-11-08 00:15:35', '2025-11-08 00:15:35'),
(21, 9, '2025-11-10', 4, 'Paper', 'Additional notes for Paper', NULL, 'John', 'Ltms', 'Service Center', 1, 'David', '2025-11-11', 4005.00, 'Vehicle', NULL, 77, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(22, 9, '2025-11-10', 9, 'Software License', 'Additional notes for Software License', NULL, 'Mike', 'Mike', NULL, 1, 'David', NULL, 6388.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(23, 9, '2025-11-10', 5, 'Suspension', NULL, 'John', NULL, 'Sarah', NULL, 1, NULL, '2025-11-13', 1182.00, 'Vehicle', NULL, 100, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(24, 10, '2025-10-31', 2, 'Exhaust System', NULL, NULL, 'Jane', 'John', 'Dealership', 0, NULL, NULL, 4693.00, 'Vehicle', NULL, 10, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(25, 10, '2025-11-02', 8, 'Starter Motor', NULL, NULL, 'Sarah', 'JAY', 'Online Shop', 0, 'David', NULL, 8303.00, 'Vehicle', NULL, 78, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(26, 10, '2025-11-02', 4, 'Oil Change', 'Additional notes for Oil Change', 'John', NULL, NULL, 'Main Shop', 0, 'James', '2025-11-06', 1097.00, 'Vehicle', NULL, 36, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(27, 11, '2025-11-09', 3, 'Brake Pads', NULL, NULL, 'Mike', NULL, NULL, 0, 'JAY', NULL, 8895.00, 'Vehicle', NULL, 99, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(28, 11, '2025-11-09', 7, 'Software License', 'Additional notes for Software License', NULL, 'David', NULL, NULL, 1, 'James', NULL, 1257.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(29, 11, '2025-11-10', 5, 'Rent', 'Additional notes for Rent', 'Emily', 'Emily', NULL, 'Warehouse', 1, NULL, '2025-11-12', 1751.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(30, 12, '2025-11-05', 2, 'Battery', NULL, 'Ltms', NULL, NULL, 'Online Shop', 1, NULL, NULL, 6452.00, 'Vehicle', NULL, 125, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(31, 12, '2025-11-07', 4, 'Office Furniture', NULL, NULL, 'JAY', 'Patricia', NULL, 1, 'Chris', '2025-11-08', 4588.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(32, 12, '2025-11-08', 3, 'Paper', NULL, 'JAY', NULL, 'jhong', 'Office Depot', 1, 'Maria', NULL, 2186.00, 'Vehicle', NULL, 78, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(33, 13, '2025-11-08', 8, 'Travel Expenses', NULL, NULL, NULL, 'James', NULL, 1, 'JAY', NULL, 8350.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(34, 13, '2025-11-05', 2, 'Accounting Services', 'Additional notes for Accounting Services', 'JAY', NULL, NULL, NULL, 0, 'Patricia', '2025-11-06', 5215.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(35, 14, '2025-11-01', 2, 'AC Repair', NULL, NULL, 'Chris', 'Patricia', NULL, 0, 'JAY', '2025-11-02', 617.00, 'Vehicle', NULL, 55, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(36, 14, '2025-11-03', 1, 'Phone Bill', 'Additional notes for Phone Bill', NULL, NULL, 'JAY', NULL, 0, NULL, NULL, 3754.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(37, 15, '2025-11-02', 5, 'Marketing', 'Additional notes for Marketing', NULL, NULL, 'Patricia', NULL, 1, 'Patricia', NULL, 8519.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(38, 15, '2025-11-01', 2, 'Meals', NULL, NULL, NULL, 'Chris', 'Main Shop', 1, 'Lisa', NULL, 6379.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(39, 15, '2025-10-31', 7, 'Stationery', NULL, NULL, 'David', NULL, NULL, 0, 'Ltms', NULL, 9491.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(40, 16, '2025-11-03', 7, 'Printing', NULL, 'David', 'David', 'James', 'Dealership', 1, NULL, NULL, 3500.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(41, 17, '2025-11-08', 8, 'Legal Fees', 'Additional notes for Legal Fees', NULL, NULL, 'James', 'Dealership', 0, 'Maria', NULL, 1799.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(42, 17, '2025-11-06', 2, 'Tyers', NULL, NULL, 'Jane', NULL, NULL, 1, NULL, NULL, 9127.00, 'Vehicle', NULL, 52, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(43, 18, '2025-11-10', 4, 'Transmission Service', 'Additional notes for Transmission Service', 'JAY', 'Mike', 'John', 'Main Shop', 0, NULL, '2025-11-12', 6007.00, 'Vehicle', NULL, 28, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(44, 18, '2025-11-10', 7, 'Travel Expenses', NULL, NULL, 'Maria', NULL, 'Online Shop', 0, NULL, '2025-11-13', 8364.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(45, 18, '2025-11-09', 8, 'Tyers', NULL, 'Lisa', 'Chris', 'Mike', 'Online Shop', 1, 'Robert', '2025-11-14', 8621.00, 'Vehicle', NULL, 13, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(46, 19, '2025-11-01', 6, 'Stationery', NULL, NULL, 'David', 'Sarah', NULL, 1, 'David', '2025-11-04', 1517.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(47, 20, '2025-11-07', 9, 'Advertising', 'Additional notes for Advertising', NULL, NULL, 'James', 'Office Depot', 1, 'David', NULL, 5085.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(48, 20, '2025-11-07', 7, 'AC Repair', NULL, 'Chris', 'Jane', NULL, 'Retail Outlet', 0, NULL, NULL, 5157.00, 'Vehicle', NULL, 21, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(49, 20, '2025-11-05', 4, 'Travel Expenses', 'Additional notes for Travel Expenses', NULL, 'James', NULL, NULL, 1, 'JAY', '2025-11-10', 1876.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(50, 21, '2025-11-06', 5, 'Battery', NULL, 'Lisa', NULL, NULL, NULL, 0, NULL, NULL, 1352.00, 'Vehicle', NULL, 104, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(51, 22, '2025-11-08', 4, 'Insurance', NULL, 'James', 'David', NULL, 'Online Shop', 1, NULL, NULL, 5538.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(52, 22, '2025-11-07', 5, 'Equipment Maintenance', NULL, 'Patricia', NULL, NULL, NULL, 1, NULL, NULL, 9065.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(53, 23, '2025-10-31', 1, 'Rent', 'Additional notes for Rent', 'Lisa', 'John', 'James', 'Main Shop', 0, NULL, NULL, 9973.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(54, 23, '2025-11-01', 2, 'Office Furniture', NULL, NULL, 'James', 'James', NULL, 1, NULL, '2025-11-02', 5975.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(55, 23, '2025-11-03', 5, 'Wheel Alignment', NULL, 'Ltms', 'Sarah', 'Maria', 'Dealership', 1, 'Ltms', NULL, 1031.00, 'Vehicle', NULL, 102, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(56, 24, '2025-11-01', 6, 'Fuel Pump', 'Additional notes for Fuel Pump', 'JAY', NULL, NULL, 'Auto Parts Store', 1, NULL, '2025-11-06', 7569.00, 'Vehicle', NULL, 94, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(57, 24, '2025-11-03', 4, 'Travel Expenses', NULL, 'JAY', NULL, NULL, 'Retail Outlet', 0, 'Sarah', '2025-11-07', 8584.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(58, 25, '2025-11-02', 8, 'Spark Plugs', NULL, NULL, NULL, NULL, 'Somewhere', 1, NULL, NULL, 6006.00, 'Vehicle', NULL, 94, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(59, 25, '2025-10-31', 6, 'Postage', NULL, 'Lisa', NULL, 'Maria', NULL, 1, NULL, NULL, 6003.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(60, 26, '2025-11-06', 6, 'Security Services', NULL, NULL, NULL, NULL, 'Hardware Store', 1, 'Emily', NULL, 7734.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(61, 27, '2025-11-01', 2, 'Marketing', NULL, NULL, 'James', NULL, NULL, 1, 'JAY', NULL, 3762.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(62, 28, '2025-11-03', 6, 'Alternator', 'Additional notes for Alternator', NULL, 'Lisa', NULL, NULL, 1, 'Chris', NULL, 6682.00, 'Vehicle', NULL, 84, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(63, 28, '2025-11-01', 2, 'Tires', 'Additional notes for Tires', 'Sarah', NULL, 'David', 'Dealership', 1, 'JAY', '2025-11-03', 8675.00, 'Vehicle', NULL, 48, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(64, 28, '2025-10-31', 7, 'AC Repair', NULL, 'jhong', NULL, NULL, NULL, 0, 'John', NULL, 8408.00, 'Vehicle', NULL, 55, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(65, 29, '2025-10-31', 7, 'Internet Bill', 'Additional notes for Internet Bill', 'Patricia', NULL, NULL, 'Main Shop', 1, NULL, NULL, 6627.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(66, 29, '2025-11-03', 8, 'Exhaust System', 'Additional notes for Exhaust System', 'JAY', 'Jane', NULL, NULL, 1, NULL, NULL, 8482.00, 'Vehicle', NULL, 62, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(67, 30, '2025-11-07', 7, 'Radiator', 'Additional notes for Radiator', 'John', NULL, NULL, NULL, 1, NULL, NULL, 8475.00, 'Vehicle', NULL, 91, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(68, 30, '2025-11-05', 4, 'Battery', NULL, 'Jane', 'David', NULL, NULL, 0, 'Ltms', '2025-11-10', 2487.00, 'Vehicle', NULL, 12, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(69, 31, '2025-11-08', 7, 'Fuel Pump', 'Additional notes for Fuel Pump', 'Patricia', 'Sarah', 'JAY', NULL, 0, NULL, NULL, 7951.00, 'Vehicle', NULL, 108, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(70, 31, '2025-11-06', 4, 'Postage', 'Additional notes for Postage', 'James', NULL, 'David', 'Dealership', 0, 'Chris', NULL, 6810.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(71, 31, '2025-11-05', 9, 'Marketing', NULL, NULL, NULL, 'Robert', 'Somewhere', 1, 'James', NULL, 9230.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(72, 32, '2025-11-05', 9, 'Security Services', NULL, NULL, 'JAY', 'Jane', 'Retail Outlet', 1, NULL, '2025-11-08', 8028.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(73, 32, '2025-11-08', 3, 'Spark Plugs', NULL, 'JAY', NULL, NULL, 'Auto Parts Store', 0, NULL, '2025-11-09', 1269.00, 'Vehicle', NULL, 11, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(74, 32, '2025-11-08', 8, 'AC Repair', NULL, 'JAY', NULL, NULL, 'Service Center', 1, 'Maria', '2025-11-09', 7036.00, 'Vehicle', NULL, 24, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(75, 33, '2025-11-07', 5, 'Utilities', 'Additional notes for Utilities', NULL, 'Patricia', NULL, NULL, 0, NULL, NULL, 7841.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(76, 34, '2025-11-11', 1, 'Suspension', 'Additional notes for Suspension', NULL, 'Lisa', 'JAY', 'Service Center', 1, 'John', '2025-11-16', 562.00, 'Vehicle', NULL, 90, '2025-11-08 00:41:17', '2025-11-08 07:47:28'),
(77, 34, '2025-11-09', 8, 'Printing', 'Additional notes for Printing', NULL, 'Lisa', 'Ltms', 'Branch Store', 0, NULL, NULL, 5777.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(78, 35, '2025-11-07', 3, 'Marketing', NULL, NULL, 'John', NULL, NULL, 0, NULL, NULL, 6155.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(79, 35, '2025-11-08', 1, 'Equipment Maintenance', NULL, 'John', 'Maria', 'James', NULL, 0, NULL, '2025-11-09', 243.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(80, 36, '2025-11-01', 6, 'Air Filter', 'Additional notes for Air Filter', NULL, NULL, 'Patricia', NULL, 1, 'Ltms', '2025-11-05', 2133.00, 'Vehicle', NULL, 1, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(81, 37, '2025-11-03', 9, 'Travel Expenses', NULL, NULL, NULL, NULL, 'Somewhere', 1, NULL, '2025-11-06', 6229.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(82, 37, '2025-11-01', 2, 'Engine Repair', 'Additional notes for Engine Repair', 'JAY', 'Sarah', 'John', 'Warehouse', 0, 'Chris', '2025-11-02', 1849.00, 'Vehicle', NULL, 68, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(83, 37, '2025-11-01', 1, 'Legal Fees', NULL, NULL, NULL, 'David', 'Main Shop', 1, NULL, NULL, 3476.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(84, 38, '2025-11-06', 7, 'Oil Change', NULL, 'Jane', NULL, 'David', NULL, 0, 'Maria', '2025-11-08', 6827.00, 'Vehicle', NULL, 115, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(85, 39, '2025-11-08', 4, 'Exhaust System', NULL, NULL, NULL, 'Ltms', NULL, 0, 'Sarah', NULL, 8889.00, 'Vehicle', NULL, 57, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(86, 40, '2025-11-03', 9, 'Advertising', NULL, NULL, NULL, 'Sarah', NULL, 0, 'Robert', NULL, 9034.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(87, 41, '2025-11-06', 4, 'Meals', 'Additional notes for Meals', 'Chris', NULL, 'Emily', 'Online Shop', 1, 'Patricia', '2025-11-07', 1119.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(88, 41, '2025-11-05', 5, 'Spark Plugs', NULL, 'jhong', NULL, 'Emily', 'Warehouse', 1, NULL, '2025-11-08', 4102.00, 'Vehicle', NULL, 52, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(89, 42, '2025-11-06', 4, 'Meals', 'Additional notes for Meals', NULL, 'Mike', 'Chris', NULL, 1, NULL, '2025-11-10', 9907.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(90, 42, '2025-11-08', 3, 'Office Supplies', NULL, 'Jane', 'Emily', NULL, NULL, 0, 'Maria', NULL, 1642.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(91, 42, '2025-11-07', 9, 'Printing', 'Additional notes for Printing', NULL, 'Robert', 'Mike', 'Somewhere', 0, NULL, NULL, 6104.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(92, 43, '2025-11-10', 5, 'Printing', NULL, NULL, 'JAY', 'Maria', NULL, 0, 'Lisa', NULL, 9988.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(93, 43, '2025-11-10', 3, 'Internet Bill', NULL, 'Patricia', 'JAY', NULL, 'Online Shop', 0, 'JAY', NULL, 7793.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(94, 43, '2025-11-08', 2, 'Printing', NULL, NULL, 'John', 'James', NULL, 0, NULL, NULL, 9214.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(95, 44, '2025-11-08', 5, 'AC Repair', NULL, 'Emily', 'John', NULL, 'Auto Parts Store', 1, NULL, '2025-11-11', 3513.00, 'Vehicle', NULL, 85, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(96, 44, '2025-11-05', 3, 'Internet Bill', NULL, NULL, NULL, 'Sarah', NULL, 1, NULL, NULL, 8114.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(97, 44, '2025-11-08', 6, 'Paper', 'Additional notes for Paper', 'Chris', NULL, NULL, 'Main Shop', 1, NULL, NULL, 3064.00, 'Vehicle', NULL, 58, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(98, 45, '2025-11-01', 3, 'Marketing', 'Additional notes for Marketing', 'Patricia', NULL, NULL, NULL, 1, 'Patricia', '2025-11-05', 6191.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(99, 45, '2025-11-01', 8, 'Radiator', 'Additional notes for Radiator', 'Ltms', 'John', NULL, NULL, 1, NULL, NULL, 3706.00, 'Vehicle', NULL, 29, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(100, 45, '2025-10-31', 2, 'Advertising', 'Additional notes for Advertising', NULL, 'Chris', NULL, NULL, 1, 'Robert', NULL, 8215.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(101, 46, '2025-11-01', 7, 'Transmission Service', NULL, NULL, 'Ltms', 'Robert', NULL, 0, NULL, NULL, 5590.00, 'Vehicle', NULL, 21, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(102, 46, '2025-11-01', 3, 'Utilities', 'Additional notes for Utilities', 'Jane', NULL, 'Jane', 'Online Shop', 1, NULL, '2025-11-06', 6805.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(103, 47, '2025-11-01', 5, 'Security Services', NULL, 'jhong', 'Maria', 'Maria', 'Main Shop', 1, 'Sarah', '2025-11-05', 9769.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(104, 47, '2025-11-01', 8, 'Spark Plugs', 'Additional notes for Spark Plugs', 'Jane', NULL, NULL, NULL, 0, NULL, '2025-11-02', 9643.00, 'Vehicle', NULL, 54, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(105, 47, '2025-11-03', 7, 'Tyers', NULL, 'Ltms', NULL, NULL, NULL, 0, NULL, '2025-11-05', 5054.00, 'Vehicle', NULL, 86, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(106, 48, '2025-11-08', 6, 'Meals', NULL, NULL, NULL, NULL, 'Online Shop', 0, 'John', NULL, 5968.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(107, 48, '2025-11-10', 8, 'Exhaust System', 'Additional notes for Exhaust System', 'Jane', 'jhong', 'Robert', 'Retail Outlet', 0, NULL, NULL, 7240.00, 'Vehicle', NULL, 60, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(108, 48, '2025-11-10', 3, 'Spark Plugs', NULL, 'jhong', NULL, NULL, 'Auto Parts Store', 1, 'Robert', '2025-11-11', 1120.00, 'Vehicle', NULL, 94, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(109, 49, '2025-11-07', 4, 'Security Services', 'Additional notes for Security Services', NULL, 'Patricia', 'Robert', NULL, 0, NULL, '2025-11-12', 6716.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(110, 50, '2025-11-10', 1, 'Wheel Alignment', NULL, NULL, 'JAY', NULL, NULL, 0, NULL, '2025-11-13', 2062.00, 'Vehicle', NULL, 90, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(111, 51, '2025-11-05', 7, 'Paint', NULL, NULL, 'David', 'James', NULL, 0, NULL, '2025-11-10', 9088.00, 'Vehicle', NULL, 67, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(112, 52, '2025-11-06', 5, 'Travel Expenses', NULL, 'jhong', NULL, 'Mike', NULL, 0, NULL, '2025-11-08', 6106.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(113, 52, '2025-11-06', 9, 'Suspension', 'Additional notes for Suspension', 'Patricia', 'Lisa', 'John', 'Dealership', 1, 'Chris', NULL, 1894.00, 'Vehicle', NULL, 24, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(114, 52, '2025-11-08', 1, 'Transmission Service', 'Additional notes for Transmission Service', 'Chris', NULL, NULL, 'Retail Outlet', 0, 'Mike', NULL, 6334.00, 'Vehicle', NULL, 78, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(115, 53, '2025-11-06', 1, 'Exhaust System', NULL, NULL, NULL, NULL, 'Warehouse', 0, NULL, NULL, 6015.00, 'Vehicle', NULL, 62, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(116, 54, '2025-11-02', 1, 'Paint', 'Additional notes for Paint', 'Ltms', NULL, 'Maria', 'Auto Parts Store', 0, NULL, '2025-11-03', 1938.00, 'Vehicle', NULL, 116, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(117, 54, '2025-11-02', 9, 'Oil Change', NULL, 'Robert', NULL, 'Sarah', 'Retail Outlet', 0, 'Maria', NULL, 8552.00, 'Vehicle', NULL, 16, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(118, 54, '2025-11-01', 9, 'Engine Repair', 'Additional notes for Engine Repair', 'Chris', NULL, NULL, 'Retail Outlet', 1, 'David', NULL, 2012.00, 'Vehicle', NULL, 18, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(119, 55, '2025-11-01', 6, 'Tires', 'Additional notes for Tires', 'James', NULL, NULL, 'Warehouse', 0, 'Lisa', NULL, 1087.00, 'Vehicle', NULL, 77, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(120, 55, '2025-11-02', 1, 'Engine Repair', NULL, NULL, 'Lisa', 'James', NULL, 0, 'Patricia', '2025-11-05', 1739.00, 'Vehicle', NULL, 101, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(121, 55, '2025-11-01', 9, 'Legal Fees', 'Additional notes for Legal Fees', 'Jane', 'Ltms', 'Chris', 'Online Shop', 1, 'jhong', NULL, 4422.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(122, 56, '2025-11-11', 1, 'Postage', NULL, 'David', 'Johnny', 'Bravo', 'Retail Outlet', 1, 'Ltms', '2025-11-13', 1223.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 09:20:42'),
(123, 56, '2025-11-11', 3, 'Wheel Alignment', NULL, NULL, 'jhong', 'Emily', NULL, 0, 'Mike', '2025-11-12', 2174.00, 'Vehicle', NULL, 49, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(124, 57, '2025-11-10', 5, 'Spark Plugs', 'Additional notes for Spark Plugs', NULL, NULL, 'Ltms', 'Service Center', 0, 'JAY', '2025-11-12', 7746.00, 'Vehicle', NULL, 10, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(125, 57, '2025-11-11', 7, 'Office Furniture', NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-11-13', 4118.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(126, 57, '2025-11-08', 1, 'Suspension', NULL, NULL, 'Ltms', NULL, NULL, 0, NULL, NULL, 5607.00, 'Vehicle', NULL, 94, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(127, 58, '2025-10-31', 3, 'Spark Plugs', 'Additional notes for Spark Plugs', 'Sarah', NULL, 'Lisa', NULL, 0, NULL, '2025-11-04', 8253.00, 'Vehicle', NULL, 30, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(128, 58, '2025-11-02', 8, 'Security Services', NULL, NULL, NULL, NULL, 'Online Shop', 1, 'Lisa', NULL, 8304.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(129, 59, '2025-11-10', 5, 'Transmission Service', 'Additional notes for Transmission Service', NULL, 'James', NULL, 'Service Center', 1, 'Patricia', '2025-11-11', 6748.00, 'Vehicle', NULL, 71, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(130, 60, '2025-11-08', 4, 'Battery', 'Additional notes for Battery', 'Ltms', 'Ltms', 'JAY', NULL, 0, 'Chris', NULL, 7115.00, 'Vehicle', NULL, 96, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(131, 60, '2025-11-11', 5, 'Paint', NULL, NULL, NULL, 'Chris', NULL, 1, 'Jane', '2025-11-13', 7115.00, 'Vehicle', NULL, 71, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(132, 61, '2025-11-03', 3, 'Brake Pads', NULL, NULL, 'JAY', 'John', 'Dealership', 0, 'Emily', NULL, 1211.00, 'Vehicle', NULL, 12, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(133, 62, '2025-11-06', 2, 'Exhaust System', 'Additional notes for Exhaust System', NULL, NULL, 'JAY', 'Office Depot', 0, NULL, '2025-11-08', 2880.00, 'Vehicle', NULL, 114, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(134, 63, '2025-11-10', 1, 'Postage', 'Additional notes for Postage', NULL, NULL, 'Chris', NULL, 1, NULL, '2025-11-12', 1117.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(135, 63, '2025-11-09', 2, 'Tyers', NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-11-12', 7432.00, 'Vehicle', NULL, 37, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(136, 64, '2025-11-08', 9, 'AC Repair', 'Additional notes for AC Repair', NULL, 'Patricia', NULL, 'Service Center', 1, 'Emily', NULL, 3355.00, 'Vehicle', NULL, 36, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(137, 64, '2025-11-08', 8, 'AC Repair', NULL, 'Chris', 'Sarah', NULL, NULL, 0, NULL, '2025-11-12', 5438.00, 'Vehicle', NULL, 8, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(138, 65, '2025-11-02', 4, 'Accounting Services', NULL, NULL, NULL, NULL, NULL, 0, 'Jane', NULL, 6072.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(139, 65, '2025-11-03', 2, 'Security Services', NULL, NULL, 'Ltms', NULL, NULL, 1, NULL, NULL, 472.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(140, 66, '2025-11-01', 5, 'Utilities', NULL, NULL, 'Robert', 'Sarah', NULL, 0, 'Patricia', '2025-11-06', 7607.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(141, 66, '2025-11-03', 3, 'Advertising', 'Additional notes for Advertising', NULL, 'Emily', 'Emily', 'Hardware Store', 0, 'Robert', '2025-11-05', 9858.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(142, 67, '2025-11-08', 5, 'Spark Plugs', NULL, NULL, NULL, 'Maria', 'Main Shop', 0, 'JAY', NULL, 7839.00, 'Vehicle', NULL, 75, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(143, 68, '2025-10-31', 6, 'Brake Pads', NULL, 'Emily', NULL, 'jhong', 'Branch Store', 1, 'Mike', NULL, 4036.00, 'Vehicle', NULL, 44, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(144, 68, '2025-11-03', 4, 'Brake Pads', 'Additional notes for Brake Pads', NULL, NULL, 'Ltms', 'Warehouse', 0, NULL, NULL, 2456.00, 'Vehicle', NULL, 97, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(145, 68, '2025-11-02', 3, 'Security Services', 'Additional notes for Security Services', 'Jane', 'Sarah', NULL, NULL, 0, 'Lisa', '2025-11-04', 924.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(146, 69, '2025-11-06', 4, 'Fuel Pump', 'Additional notes for Fuel Pump', 'Maria', NULL, NULL, 'Hardware Store', 0, NULL, '2025-11-11', 6815.00, 'Vehicle', NULL, 41, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(147, 69, '2025-11-05', 8, 'Battery', NULL, 'Emily', 'JAY', 'Robert', NULL, 1, 'Emily', NULL, 8872.00, 'Vehicle', NULL, 81, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(148, 69, '2025-11-07', 2, 'Security Services', NULL, NULL, NULL, 'JAY', 'Hardware Store', 1, NULL, '2025-11-11', 9471.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(149, 70, '2025-11-06', 1, 'Stationery', 'Additional notes for Stationery', NULL, NULL, NULL, NULL, 0, NULL, '2025-11-08', 6906.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(150, 71, '2025-11-08', 9, 'Starter Motor', NULL, NULL, NULL, 'David', 'Dealership', 0, 'jhong', '2025-11-13', 5762.00, 'Vehicle', NULL, 12, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(151, 71, '2025-11-07', 5, 'Accounting Services', 'Additional notes for Accounting Services', 'Emily', 'Maria', 'Lisa', NULL, 1, 'jhong', NULL, 6328.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(152, 72, '2025-11-07', 3, 'Phone Bill', 'Additional notes for Phone Bill', NULL, NULL, 'David', NULL, 1, 'David', NULL, 4370.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(153, 73, '2025-11-01', 9, 'Spark Plugs', 'Additional notes for Spark Plugs', NULL, 'jhong', 'James', NULL, 0, 'Ltms', NULL, 8635.00, 'Vehicle', NULL, 21, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(154, 73, '2025-11-01', 8, 'Meals', NULL, 'David', NULL, NULL, 'Auto Parts Store', 0, NULL, NULL, 918.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(155, 73, '2025-11-03', 1, 'Security Services', 'Additional notes for Security Services', 'Jane', NULL, 'JAY', NULL, 0, NULL, '2025-11-05', 9183.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(156, 74, '2025-11-06', 1, 'Accounting Services', 'Additional notes for Accounting Services', NULL, NULL, 'Mike', NULL, 1, 'Mike', NULL, 8043.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(157, 74, '2025-11-08', 5, 'Marketing', 'Additional notes for Marketing', NULL, NULL, 'Jane', NULL, 0, 'Chris', '2025-11-13', 4582.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(158, 74, '2025-11-08', 7, 'Office Furniture', 'Additional notes for Office Furniture', 'David', NULL, 'Ltms', NULL, 0, 'JAY', NULL, 8778.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(159, 75, '2025-11-08', 3, 'Air Filter', NULL, NULL, NULL, 'Chris', 'Main Shop', 1, 'Ltms', NULL, 1633.00, 'Vehicle', NULL, 13, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(160, 76, '2025-11-09', 9, 'Oil Change', NULL, 'jhong', NULL, 'Chris', NULL, 1, 'Lisa', NULL, 7870.00, 'Vehicle', NULL, 30, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(161, 77, '2025-11-02', 9, 'Cleaning Services', 'Additional notes for Cleaning Services', 'jhong', 'John', NULL, 'Auto Parts Store', 0, NULL, '2025-11-07', 2897.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(162, 78, '2025-11-10', 2, 'Phone Bill', NULL, NULL, 'Mike', NULL, 'Branch Store', 0, 'Ltms', NULL, 1615.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(163, 78, '2025-11-08', 1, 'Suspension', 'Additional notes for Suspension', NULL, 'Jane', 'Emily', NULL, 1, NULL, NULL, 7664.00, 'Vehicle', NULL, 106, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(164, 79, '2025-11-07', 4, 'Battery', 'Additional notes for Battery', 'John', NULL, NULL, NULL, 0, 'Lisa', '2025-11-09', 8694.00, 'Vehicle', NULL, 33, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(165, 80, '2025-11-08', 6, 'Advertising', 'Additional notes for Advertising', NULL, NULL, 'JAY', NULL, 1, NULL, NULL, 8557.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(166, 80, '2025-11-10', 7, 'Cluster', 'Additional notes for Cluster', 'James', 'James', 'jhong', 'Warehouse', 0, 'Patricia', NULL, 3064.00, 'Vehicle', NULL, 114, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(167, 80, '2025-11-08', 9, 'Marketing', NULL, NULL, NULL, 'Jane', 'Somewhere', 0, 'Jane', NULL, 8114.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(168, 81, '2025-11-07', 7, 'Tyers', 'Additional notes for Tyers', 'John', NULL, 'jhong', 'Service Center', 1, 'Maria', '2025-11-10', 4150.00, 'Vehicle', NULL, 13, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(169, 81, '2025-11-05', 1, 'Alternator', 'Additional notes for Alternator', 'Maria', 'jhong', NULL, NULL, 0, NULL, '2025-11-10', 5874.00, 'Vehicle', NULL, 23, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(170, 82, '2025-11-11', 4, 'Postage', NULL, NULL, NULL, 'John', 'Hardware Store', 0, NULL, NULL, 336.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(171, 83, '2025-11-01', 3, 'Alternator', 'Additional notes for Alternator', 'Mike', NULL, 'Lisa', NULL, 0, 'Emily', '2025-11-02', 7639.00, 'Vehicle', NULL, 16, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(172, 83, '2025-10-31', 6, 'Battery', NULL, 'jhong', 'JAY', 'Mike', NULL, 0, 'Lisa', '2025-11-05', 7674.00, 'Vehicle', NULL, 60, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(173, 84, '2025-11-08', 4, 'Engine Repair', 'Additional notes for Engine Repair', 'John', 'Lisa', NULL, 'Retail Outlet', 0, 'jhong', '2025-11-12', 2059.00, 'Vehicle', NULL, 118, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(174, 84, '2025-11-09', 5, 'Printing', NULL, 'James', 'David', 'Lisa', NULL, 1, NULL, '2025-11-14', 7254.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(175, 84, '2025-11-08', 2, 'Spark Plugs', 'Additional notes for Spark Plugs', 'Lisa', NULL, NULL, NULL, 0, NULL, NULL, 8759.00, 'Vehicle', NULL, 35, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(176, 85, '2025-11-10', 8, 'AC Repair', NULL, 'Patricia', 'Robert', NULL, 'Retail Outlet', 0, 'Mike', NULL, 955.00, 'Vehicle', NULL, 72, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(177, 85, '2025-11-11', 4, 'Meals', 'Additional notes for Meals', NULL, 'jhong', NULL, 'Warehouse', 1, NULL, '2025-11-13', 7420.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(178, 86, '2025-10-31', 2, 'Battery', 'Additional notes for Battery', NULL, NULL, NULL, 'Office Depot', 0, 'Robert', NULL, 1439.00, 'Vehicle', NULL, 83, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(179, 87, '2025-10-31', 9, 'Meals', NULL, NULL, NULL, 'Lisa', NULL, 1, 'Emily', '2025-11-05', 3271.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(180, 88, '2025-11-08', 7, 'Paint', 'Additional notes for Paint', NULL, 'James', 'Mike', NULL, 0, NULL, '2025-11-12', 5446.00, 'Vehicle', NULL, 48, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(181, 88, '2025-11-09', 3, 'Air Filter', 'Additional notes for Air Filter', 'Robert', 'Jane', NULL, 'Retail Outlet', 1, 'Mike', NULL, 7078.00, 'Vehicle', NULL, 38, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(182, 88, '2025-11-08', 5, 'Paper', NULL, 'Robert', NULL, NULL, 'Warehouse', 1, NULL, '2025-11-12', 5736.00, 'Vehicle', NULL, 118, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(183, 89, '2025-11-11', 6, 'Printing', NULL, 'Jane', NULL, NULL, NULL, 1, 'Patricia', NULL, 900.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(184, 90, '2025-11-03', 3, 'Fuel Pump', NULL, NULL, 'Sarah', NULL, 'Branch Store', 0, 'Chris', NULL, 2700.00, 'Vehicle', NULL, 55, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(185, 90, '2025-11-02', 8, 'Battery', NULL, 'David', NULL, NULL, NULL, 0, NULL, '2025-11-03', 5101.00, 'Vehicle', NULL, 100, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(186, 90, '2025-10-31', 7, 'Paint', NULL, 'John', 'jhong', NULL, NULL, 0, NULL, NULL, 8302.00, 'Vehicle', NULL, 65, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(187, 91, '2025-11-05', 7, 'Oil Change', NULL, 'David', NULL, NULL, 'Online Shop', 0, NULL, NULL, 2033.00, 'Vehicle', NULL, 115, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(188, 91, '2025-11-05', 1, 'Security Services', 'Additional notes for Security Services', NULL, NULL, 'John', NULL, 1, 'Lisa', NULL, 270.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(189, 92, '2025-11-01', 3, 'Advertising', NULL, NULL, NULL, 'Ltms', 'Somewhere', 0, 'Emily', '2025-11-02', 4062.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(190, 92, '2025-11-02', 4, 'Travel Expenses', 'Additional notes for Travel Expenses', NULL, NULL, NULL, NULL, 1, NULL, '2025-11-07', 7940.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(191, 92, '2025-11-03', 6, 'Marketing', NULL, 'Robert', 'Jane', NULL, NULL, 0, NULL, '2025-11-04', 8531.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(192, 93, '2025-11-10', 9, 'Cleaning Services', 'Additional notes for Cleaning Services', 'David', 'Lisa', NULL, NULL, 0, 'JAY', NULL, 878.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(193, 94, '2025-11-09', 6, 'Alternator', 'Additional notes for Alternator', 'Patricia', 'Chris', NULL, NULL, 1, NULL, NULL, 5031.00, 'Vehicle', NULL, 88, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(194, 95, '2025-11-08', 1, 'Engine Repair', NULL, 'Ltms', 'Sarah', 'Robert', 'Hardware Store', 1, NULL, NULL, 3907.00, 'Vehicle', NULL, 74, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(195, 96, '2025-11-10', 2, 'Spark Plugs', 'Additional notes for Spark Plugs', 'Robert', NULL, 'John', 'Auto Parts Store', 1, 'Mike', '2025-11-12', 7025.00, 'Vehicle', NULL, 41, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(196, 97, '2025-11-10', 6, 'Phone Bill', NULL, NULL, NULL, 'Mike', NULL, 1, 'John', NULL, 431.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(197, 97, '2025-11-08', 8, 'Air Filter', 'Additional notes for Air Filter', NULL, NULL, 'Emily', 'Retail Outlet', 1, 'Sarah', NULL, 7073.00, 'Vehicle', NULL, 99, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(198, 97, '2025-11-09', 7, 'Paper', 'Additional notes for Paper', 'Jane', 'Mike', NULL, NULL, 1, 'JAY', NULL, 8465.00, 'Vehicle', NULL, 124, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(199, 98, '2025-11-09', 6, 'Paint', 'Additional notes for Paint', 'Maria', NULL, 'Patricia', NULL, 1, 'Maria', NULL, 1735.00, 'Vehicle', NULL, 33, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(200, 98, '2025-11-09', 3, 'Tires', NULL, NULL, NULL, 'Chris', 'Service Center', 0, NULL, NULL, 5716.00, 'Vehicle', NULL, 109, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(201, 99, '2025-10-31', 3, 'Starter Motor', NULL, 'Maria', NULL, NULL, 'Dealership', 1, 'Patricia', '2025-11-02', 6372.00, 'Vehicle', NULL, 120, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(202, 99, '2025-10-31', 3, 'Suspension', NULL, 'Sarah', 'Lisa', NULL, NULL, 0, NULL, '2025-11-01', 4922.00, 'Vehicle', NULL, 1, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(203, 100, '2025-11-03', 5, 'Engine Repair', NULL, 'Sarah', 'Robert', 'Patricia', 'Warehouse', 0, 'Mike', NULL, 1805.00, 'Vehicle', NULL, 103, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(204, 100, '2025-10-31', 9, 'Transmission Service', NULL, NULL, NULL, 'James', NULL, 1, NULL, '2025-11-04', 318.00, 'Vehicle', NULL, 41, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(205, 101, '2025-11-02', 4, 'Suspension', NULL, 'JAY', NULL, 'John', NULL, 0, NULL, NULL, 2374.00, 'Vehicle', NULL, 79, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(206, 101, '2025-11-03', 8, 'Equipment Maintenance', NULL, 'JAY', NULL, NULL, NULL, 0, NULL, NULL, 8970.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(207, 102, '2025-11-06', 8, 'Tyers', NULL, NULL, 'David', NULL, 'Online Shop', 0, NULL, NULL, 516.00, 'Vehicle', NULL, 24, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(208, 102, '2025-11-08', 9, 'Cluster', 'Additional notes for Cluster', NULL, 'jhong', 'Maria', NULL, 1, NULL, '2025-11-11', 9374.00, 'Vehicle', NULL, 16, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(209, 102, '2025-11-08', 6, 'Software License', 'Additional notes for Software License', NULL, 'Emily', 'Jane', 'Main Shop', 0, 'Chris', NULL, 4194.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(210, 103, '2025-11-08', 2, 'Wheel Alignment', NULL, NULL, NULL, NULL, 'Warehouse', 1, NULL, '2025-11-13', 8234.00, 'Vehicle', NULL, 5, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(211, 104, '2025-10-31', 5, 'Legal Fees', NULL, 'Chris', 'JAY', NULL, NULL, 1, 'jhong', NULL, 7074.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(212, 104, '2025-11-03', 2, 'Internet Bill', 'Additional notes for Internet Bill', 'Sarah', NULL, NULL, NULL, 0, NULL, '2025-11-08', 321.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(213, 105, '2025-11-08', 4, 'Cleaning Services', NULL, 'Sarah', 'Lisa', NULL, NULL, 1, 'Robert', NULL, 6974.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(214, 106, '2025-11-08', 3, 'Suspension', 'Additional notes for Suspension', 'Ltms', NULL, NULL, 'Main Shop', 0, NULL, NULL, 767.00, 'Vehicle', NULL, 73, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(215, 107, '2025-11-01', 9, 'Tires', 'Additional notes for Tires', 'Jane', NULL, 'Lisa', NULL, 1, 'James', '2025-11-06', 9550.00, 'Vehicle', NULL, 112, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(216, 108, '2025-11-08', 8, 'Legal Fees', NULL, NULL, NULL, NULL, NULL, 1, 'Robert', '2025-11-13', 5880.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(217, 108, '2025-11-08', 2, 'Software License', 'Additional notes for Software License', 'Emily', NULL, 'Chris', 'Warehouse', 0, NULL, '2025-11-13', 3991.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(218, 108, '2025-11-05', 4, 'Postage', 'Additional notes for Postage', 'Ltms', 'JAY', NULL, 'Warehouse', 0, NULL, '2025-11-09', 1749.00, 'Operating', NULL, NULL, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(225, 115, '2025-11-29', 1, 'Aircon', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 5000.00, 'Customer Request', NULL, 128, '2025-11-29 05:30:57', '2025-11-29 05:30:57'),
(226, 116, '2025-11-29', 1, 'Aircon', 'v', 'b', 'b', 'b', 'b', 1, NULL, '2000-10-20', 1000.00, 'Vehicle', NULL, 128, '2025-11-29 05:34:43', '2025-11-29 05:34:43'),
(227, 117, '2025-11-30', 1, 'Aircon, Battery', 'adfa', 'john', 'john', 'john', 'john', 1, 'vj', '1999-10-01', 3000.00, 'Vehicle', NULL, 130, '2025-11-29 17:11:17', '2025-11-29 17:44:49'),
(228, 118, '2026-02-04', 4, 'Paint', 'just paint', NULL, 'john', 'john', 'idol shop', 0, NULL, NULL, 6000.00, 'Vehicle', 'Post Reservation', 124, '2026-02-04 05:29:35', '2026-02-04 05:29:35'),
(229, 119, '2026-02-04', 4, 'Wiper', 'back wiper', NULL, 'none', 'none', 'none', 0, NULL, NULL, 2000.00, 'Vehicle', 'Post Reservation', 124, '2026-02-04 05:38:42', '2026-02-04 05:38:42'),
(230, 120, '2026-02-04', 4, 'roof pain', 'roof pain', NULL, 'none', 'none', 'none', 0, NULL, NULL, 3000.00, 'Vehicle', 'Post Release', 130, '2026-02-04 05:45:25', '2026-02-04 05:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `expense_item_receipts`
--

CREATE TABLE `expense_item_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_item_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_item_receipts`
--

INSERT INTO `expense_item_receipts` (`id`, `expense_item_id`, `image_path`, `original_name`, `mime_type`, `file_size`, `sort_order`, `created_at`, `updated_at`) VALUES
(4, 20, 'expenses/receipts/902b7fe1-e78e-4a0d-82b0-30f82c4da1b2.jpeg', 'cutekittens.jpeg', 'image/jpeg', 6048, 0, '2025-11-08 00:15:39', '2025-11-08 00:15:39'),
(5, 227, 'expenses/receipts/8d3cb611-5211-462c-aecf-14d2c159f64d.png', '1.png', 'image/png', 582992, 0, '2025-11-29 17:11:17', '2025-11-29 17:11:17');

-- --------------------------------------------------------

--
-- Table structure for table `expense_transactions`
--

CREATE TABLE `expense_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_date` date NOT NULL,
  `starting_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `added_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_cash` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_expense` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_remaining` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_transactions`
--

INSERT INTO `expense_transactions` (`id`, `transaction_date`, `starting_cash`, `added_cash`, `total_cash`, `total_expense`, `cash_remaining`, `created_at`, `updated_at`) VALUES
(4, '2025-11-02', 100000.00, 0.00, 100000.00, 7400.00, 92600.00, '2025-11-01 19:45:12', '2025-11-07 19:47:35'),
(7, '2025-11-08', 0.00, 0.00, 0.00, 2000.00, -2000.00, '2025-11-08 00:15:35', '2025-11-08 00:15:35'),
(8, '2025-11-11', 99045.00, 7226.00, 106271.00, 0.00, 106271.00, '2025-11-08 00:40:59', '2025-11-08 00:40:59'),
(9, '2025-11-11', 53885.00, 43955.00, 97840.00, 11575.00, 86265.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(10, '2025-11-03', 31825.00, 3193.00, 35018.00, 14093.00, 20925.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(11, '2025-11-11', 28348.00, 39195.00, 67543.00, 11903.00, 55640.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(12, '2025-11-08', 51327.00, 25291.00, 76618.00, 13226.00, 63392.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(13, '2025-11-08', 92608.00, 30714.00, 123322.00, 13565.00, 109757.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(14, '2025-11-03', 19713.00, 3767.00, 23480.00, 4371.00, 19109.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(15, '2025-11-03', 73161.00, 36127.00, 109288.00, 24389.00, 84899.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(16, '2025-11-03', 13882.00, 6488.00, 20370.00, 3500.00, 16870.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(17, '2025-11-08', 90284.00, 13357.00, 103641.00, 10926.00, 92715.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(18, '2025-11-11', 63598.00, 33427.00, 97025.00, 22992.00, 74033.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(19, '2025-11-03', 25004.00, 43771.00, 68775.00, 1517.00, 67258.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(20, '2025-11-08', 29636.00, 31786.00, 61422.00, 12118.00, 49304.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(21, '2025-11-08', 31982.00, 35600.00, 67582.00, 1352.00, 66230.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(22, '2025-11-08', 30297.00, 40312.00, 70609.00, 14603.00, 56006.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(23, '2025-11-03', 34234.00, 34851.00, 69085.00, 16979.00, 52106.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(24, '2025-11-03', 60220.00, 7945.00, 68165.00, 16153.00, 52012.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(25, '2025-11-03', 24620.00, 18629.00, 43249.00, 12009.00, 31240.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(26, '2025-11-08', 80240.00, 19501.00, 99741.00, 7734.00, 92007.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(27, '2025-11-03', 21177.00, 34148.00, 55325.00, 3762.00, 51563.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(28, '2025-11-03', 29153.00, 40721.00, 69874.00, 23765.00, 46109.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(29, '2025-11-03', 68771.00, 16822.00, 85593.00, 15109.00, 70484.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(30, '2025-11-08', 13703.00, 18403.00, 32106.00, 10962.00, 21144.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(31, '2025-11-08', 29958.00, 38041.00, 67999.00, 23991.00, 44008.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(32, '2025-11-08', 27160.00, 33068.00, 60228.00, 16333.00, 43895.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(33, '2025-11-08', 43527.00, 40653.00, 84180.00, 7841.00, 76339.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(34, '2025-11-11', 38291.00, 26778.00, 65069.00, 6339.00, 58730.00, '2025-11-08 00:41:17', '2025-11-08 07:47:28'),
(35, '2025-11-08', 21126.00, 30849.00, 51975.00, 6398.00, 45577.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(36, '2025-11-03', 68682.00, 25557.00, 94239.00, 2133.00, 92106.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(37, '2025-11-03', 49296.00, 19165.00, 68461.00, 11554.00, 56907.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(38, '2025-11-08', 36479.00, 22060.00, 58539.00, 6827.00, 51712.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(39, '2025-11-08', 57240.00, 2183.00, 59423.00, 8889.00, 50534.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(40, '2025-11-03', 73776.00, 27708.00, 101484.00, 9034.00, 92450.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(41, '2025-11-08', 78425.00, 42444.00, 120869.00, 5221.00, 115648.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(42, '2025-11-08', 33364.00, 9514.00, 42878.00, 17653.00, 25225.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(43, '2025-11-11', 76170.00, 40572.00, 116742.00, 26995.00, 89747.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(44, '2025-11-08', 54066.00, 40282.00, 94348.00, 14691.00, 79657.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(45, '2025-11-03', 80088.00, 26886.00, 106974.00, 18112.00, 88862.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(46, '2025-11-03', 79926.00, 23229.00, 103155.00, 12395.00, 90760.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(47, '2025-11-03', 81081.00, 19311.00, 100392.00, 24466.00, 75926.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(48, '2025-11-11', 60983.00, 11479.00, 72462.00, 14328.00, 58134.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(49, '2025-11-08', 48609.00, 16331.00, 64940.00, 6716.00, 58224.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(50, '2025-11-11', 93082.00, 27531.00, 120613.00, 2062.00, 118551.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(51, '2025-11-08', 96313.00, 30438.00, 126751.00, 9088.00, 117663.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(52, '2025-11-08', 64273.00, 37984.00, 102257.00, 14334.00, 87923.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(53, '2025-11-08', 67329.00, 47713.00, 115042.00, 6015.00, 109027.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(54, '2025-11-03', 51293.00, 35787.00, 87080.00, 12502.00, 74578.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(55, '2025-11-03', 87134.00, 39301.00, 126435.00, 7248.00, 119187.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(56, '2025-11-11', 54940.00, 37732.00, 92672.00, 3397.00, 89275.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(57, '2025-11-11', 95024.00, 29897.00, 124921.00, 17471.00, 107450.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(58, '2025-11-03', 93950.00, 35972.00, 129922.00, 16557.00, 113365.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(59, '2025-11-11', 61073.00, 18171.00, 79244.00, 6748.00, 72496.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(60, '2025-11-11', 39634.00, 33829.00, 73463.00, 14230.00, 59233.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(61, '2025-11-03', 81836.00, 42714.00, 124550.00, 1211.00, 123339.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(62, '2025-11-08', 25775.00, 46142.00, 71917.00, 2880.00, 69037.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(63, '2025-11-11', 49166.00, 4330.00, 53496.00, 8549.00, 44947.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(64, '2025-11-08', 23769.00, 22921.00, 46690.00, 8793.00, 37897.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(65, '2025-11-03', 48107.00, 22689.00, 70796.00, 6544.00, 64252.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(66, '2025-11-03', 44934.00, 42800.00, 87734.00, 17465.00, 70269.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(67, '2025-11-11', 47242.00, 19535.00, 66777.00, 7839.00, 58938.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(68, '2025-11-03', 78846.00, 6474.00, 85320.00, 7416.00, 77904.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(69, '2025-11-08', 85021.00, 28515.00, 113536.00, 25158.00, 88378.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(70, '2025-11-08', 49446.00, 33078.00, 82524.00, 6906.00, 75618.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(71, '2025-11-08', 11658.00, 39628.00, 51286.00, 12090.00, 39196.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(72, '2025-11-08', 14950.00, 3057.00, 18007.00, 4370.00, 13637.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(73, '2025-11-03', 90307.00, 7648.00, 97955.00, 18736.00, 79219.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(74, '2025-11-08', 35084.00, 13303.00, 48387.00, 21403.00, 26984.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(75, '2025-11-11', 97590.00, 39269.00, 136859.00, 1633.00, 135226.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(76, '2025-11-11', 28018.00, 887.00, 28905.00, 7870.00, 21035.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(77, '2025-11-03', 12888.00, 14220.00, 27108.00, 2897.00, 24211.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(78, '2025-11-11', 58789.00, 23713.00, 82502.00, 9279.00, 73223.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(79, '2025-11-08', 90755.00, 40867.00, 131622.00, 8694.00, 122928.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(80, '2025-11-11', 48310.00, 1539.00, 49849.00, 19735.00, 30114.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(81, '2025-11-08', 13242.00, 27439.00, 40681.00, 10024.00, 30657.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(82, '2025-11-11', 79911.00, 8614.00, 88525.00, 336.00, 88189.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(83, '2025-11-03', 47932.00, 4115.00, 52047.00, 15313.00, 36734.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(84, '2025-11-11', 90415.00, 28002.00, 118417.00, 18072.00, 100345.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(85, '2025-11-11', 56958.00, 46450.00, 103408.00, 8375.00, 95033.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(86, '2025-11-03', 45527.00, 44699.00, 90226.00, 1439.00, 88787.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(87, '2025-11-03', 12596.00, 8639.00, 21235.00, 3271.00, 17964.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(88, '2025-11-11', 27162.00, 31043.00, 58205.00, 18260.00, 39945.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(89, '2025-11-11', 86262.00, 18927.00, 105189.00, 900.00, 104289.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(90, '2025-11-03', 47051.00, 13062.00, 60113.00, 16103.00, 44010.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(91, '2025-11-08', 79597.00, 44336.00, 123933.00, 2303.00, 121630.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(92, '2025-11-03', 72424.00, 4769.00, 77193.00, 20533.00, 56660.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(93, '2025-11-11', 14833.00, 18611.00, 33444.00, 878.00, 32566.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(94, '2025-11-11', 76499.00, 36097.00, 112596.00, 5031.00, 107565.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(95, '2025-11-11', 52526.00, 2034.00, 54560.00, 3907.00, 50653.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(96, '2025-11-11', 22697.00, 17891.00, 40588.00, 7025.00, 33563.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(97, '2025-11-11', 22253.00, 24123.00, 46376.00, 15969.00, 30407.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(98, '2025-11-11', 21677.00, 16671.00, 38348.00, 7451.00, 30897.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(99, '2025-11-03', 73309.00, 10918.00, 84227.00, 11294.00, 72933.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(100, '2025-11-03', 97296.00, 3698.00, 100994.00, 2123.00, 98871.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(101, '2025-11-03', 97802.00, 47160.00, 144962.00, 11344.00, 133618.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(102, '2025-11-08', 56473.00, 7432.00, 63905.00, 14084.00, 49821.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(103, '2025-11-08', 78910.00, 41638.00, 120548.00, 8234.00, 112314.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(104, '2025-11-03', 58427.00, 3409.00, 61836.00, 7395.00, 54441.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(105, '2025-11-11', 62529.00, 34768.00, 97297.00, 6974.00, 90323.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(106, '2025-11-08', 74079.00, 34036.00, 108115.00, 767.00, 107348.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(107, '2025-11-03', 21057.00, 46996.00, 68053.00, 9550.00, 58503.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(108, '2025-11-08', 74788.00, 43707.00, 118495.00, 11620.00, 106875.00, '2025-11-08 00:41:17', '2025-11-08 00:41:17'),
(109, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:09:05', '2025-11-24 23:23:18'),
(110, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:09:26', '2025-11-24 23:23:29'),
(111, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:09:30', '2025-11-24 23:23:50'),
(112, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:10:13', '2025-11-24 23:24:04'),
(113, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:17:38', '2025-11-24 23:23:04'),
(114, '2025-11-25', 0.00, 0.00, 0.00, 0.00, 0.00, '2025-11-24 23:17:42', '2025-11-24 23:22:32'),
(115, '2025-11-29', 0.00, 0.00, 0.00, 5000.00, -5000.00, '2025-11-29 05:30:57', '2025-11-29 05:30:57'),
(116, '2025-11-29', 0.00, 0.00, 0.00, 1000.00, -1000.00, '2025-11-29 05:34:43', '2025-11-29 05:34:43'),
(117, '2025-11-30', 0.00, 0.00, 0.00, 3000.00, -3000.00, '2025-11-29 17:11:17', '2025-11-29 17:11:17'),
(118, '2026-02-04', 0.00, 0.00, 0.00, 6000.00, -6000.00, '2026-02-04 05:29:35', '2026-02-04 05:29:35'),
(119, '2026-02-04', 0.00, 0.00, 0.00, 2000.00, -2000.00, '2026-02-04 05:38:42', '2026-02-04 05:38:42'),
(120, '2026-02-04', 0.00, 0.00, 0.00, 3000.00, -3000.00, '2026-02-04 05:45:25', '2026-02-04 05:45:25');

-- --------------------------------------------------------

--
-- Table structure for table `financing_schemes`
--

CREATE TABLE `financing_schemes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financing_schemes`
--

INSERT INTO `financing_schemes` (`id`, `name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'ASIALINK', 1, '2026-02-27 22:07:16', '2026-02-27 22:45:12'),
(2, 'JACCS', 2, '2026-02-27 22:07:16', '2026-02-27 22:07:16'),
(5, 'ORICO', 3, '2026-02-27 22:44:53', '2026-02-27 22:44:53'),
(6, 'BERJAYA', 4, '2026-02-27 22:45:04', '2026-02-27 22:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `follow_up_documents`
--

CREATE TABLE `follow_up_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `priority` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `follow_up_documents`
--

INSERT INTO `follow_up_documents` (`id`, `title`, `description`, `vehicle_id`, `due_date`, `status`, `priority`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'ORCR', 'test', 130, '2026-02-27', 'Pending', 'High', 'test', '2026-02-27 07:49:33', '2026-02-27 07:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `gas_expenses`
--

CREATE TABLE `gas_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `driver` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `gas_amount` decimal(10,2) NOT NULL,
  `expense_sent_by` varchar(255) NOT NULL,
  `has_photo_video_in_groupchat` tinyint(1) NOT NULL DEFAULT 0,
  `photo_fuel_gauge_before` tinyint(1) NOT NULL DEFAULT 0,
  `photo_fuel_gauge_after` tinyint(1) NOT NULL DEFAULT 0,
  `photo_car_license_plate_gas_boy` tinyint(1) NOT NULL DEFAULT 0,
  `photo_receipt_next_to_gas_pump` tinyint(1) NOT NULL DEFAULT 0,
  `checked_by` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gas_expenses`
--

INSERT INTO `gas_expenses` (`id`, `date`, `driver`, `model`, `plate_number`, `gas_amount`, `expense_sent_by`, `has_photo_video_in_groupchat`, `photo_fuel_gauge_before`, `photo_fuel_gauge_after`, `photo_car_license_plate_gas_boy`, `photo_receipt_next_to_gas_pump`, `checked_by`, `created_at`, `updated_at`) VALUES
(2, '2025-05-24', 'JIM T', 'FORD TERRITORY', 'XYZ-5333', 499.00, 'MERLIN', 1, 1, 1, 1, 1, 'MARJ', '2025-10-25 17:04:43', '2025-10-25 17:04:43'),
(3, '2025-05-25', 'OLIVER', 'TOYOTA RUSH', 'JHK-3354', 495.00, 'MERLIN', 1, 1, 1, 1, 1, 'MARJ', '2025-10-25 17:04:43', '2025-10-25 17:04:43'),
(4, '2025-05-25', 'MIKE C', 'HONDA BRIO', 'ABC-2217', 496.00, 'MERLIN', 1, 1, 1, 1, 1, 'MARJ', '2025-10-25 17:04:43', '2025-10-25 17:04:43'),
(5, '2025-05-25', 'MARIANO', '17 SUZUKI SWIFT', 'TUV-5181', 998.00, 'MERLIN', 1, 1, 1, 1, 1, 'MARJ', '2025-10-25 17:04:43', '2025-10-25 17:04:43'),
(8, '2026-01-30', 'VJ', '2017 Toyota Wigo G', 'nbz9090', 500.00, 'MERLIN', 1, 0, 1, 0, 0, 'jona', '2026-01-30 07:00:42', '2026-01-30 07:00:42');

-- --------------------------------------------------------

--
-- Table structure for table `insurance_tracker`
--

CREATE TABLE `insurance_tracker` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `showroom` varchar(100) DEFAULT NULL,
  `sales` varchar(100) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `make` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction` varchar(50) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `insurance_tracker`
--

INSERT INTO `insurance_tracker` (`id`, `showroom`, `sales`, `year`, `make`, `model`, `number`, `vehicle_id`, `transaction`, `source`, `reservation_date`, `release_date`, `amount`, `created_at`, `updated_at`) VALUES
(1, 'test', 'test', '2017', 'Toyota', 'Wigo', 'nbz9090', 130, 'Cash', 'test', '2026-02-09', '2026-02-28', 5000.00, '2026-02-27 15:18:04', '2026-02-27 15:18:04');

-- --------------------------------------------------------

--
-- Table structure for table `makes`
--

CREATE TABLE `makes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `makes`
--

INSERT INTO `makes` (`id`, `name`, `logo`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Toyota', NULL, 'Japanese automotive manufacturer known for reliability and quality.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(2, 'Honda', NULL, 'Japanese multinational corporation known for automobiles and motorcycles.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(3, 'Nissan', NULL, 'Japanese multinational automobile manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(4, 'Mitsubishi', NULL, 'Japanese multinational automotive manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(5, 'Suzuki', NULL, 'Japanese multinational corporation specializing in automobiles and motorcycles.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(6, 'Mazda', NULL, 'Japanese multinational automaker based in Hiroshima.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(7, 'Hyundai', NULL, 'South Korean multinational automotive manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(8, 'Kia', NULL, 'South Korean multinational automotive manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(9, 'Ford', NULL, 'American multinational automobile manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(10, 'Chevrolet', NULL, 'American automobile division of General Motors.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(11, 'BMW', NULL, 'German multinational corporation which produces automobiles and motorcycles.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(12, 'Mercedes-Benz', NULL, 'German luxury automotive brand.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(13, 'Audi', NULL, 'German luxury automobile manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(14, 'Volkswagen', NULL, 'German automotive manufacturer.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49'),
(15, 'Lexus', NULL, 'Luxury vehicle division of Toyota.', 1, '2025-10-25 05:28:49', '2025-10-25 05:28:49');

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(3, '2025_10_25_130328_create_vehicles_table', 2),
(4, '2025_10_25_132650_create_makes_table', 3),
(5, '2025_10_25_132656_create_models_table', 3),
(6, '2025_10_25_132712_update_vehicles_table_add_make_model_foreign_keys', 3),
(7, '2025_10_25_140534_create_vehicle_images_table', 4),
(8, '2025_10_25_142906_create_vehicle_expenses_table', 5),
(9, '2025_10_25_144408_update_vehicle_status_enum', 6),
(10, '2025_10_25_145621_create_vehicle_status_details_table', 7),
(11, '2025_10_26_010155_create_gas_expenses_table', 8),
(12, '2025_10_26_011201_fix_gas_expenses_foreign_key', 9),
(13, '2025_10_28_143814_create_custom_sections_table', 10),
(14, '2025_10_28_143816_create_custom_section_fields_table', 10),
(15, '2025_10_28_151322_create_vehicle_custom_fields_table', 11),
(16, '2025_10_28_154958_add_forfeited_status_to_vehicles_table', 12),
(17, '2025_10_28_155015_add_forfeited_status_to_vehicle_status_details_table', 13),
(18, '2025_10_28_160838_create_sales_agents_table', 14),
(19, '2025_10_28_162629_rename_employee_id_to_sales_agent_id_in_sales_agents_table', 15),
(20, '2025_10_28_171006_create_employees_table', 16),
(21, '2025_10_31_155702_create_expense_transactions_table', 17),
(22, '2025_10_31_163206_create_expense_item_receipts_table', 18),
(23, '2025_01_15_000000_create_vehicle_expense_categories_table', 19),
(24, '2025_11_02_032837_update_vehicle_expense_categories', 20),
(25, '2025_11_02_052852_create_tools_inventory_table', 21),
(26, '2025_11_02_061539_create_activity_logs_table', 22),
(27, '2025_11_02_151611_add_description_details_to_expense_items_table', 23),
(30, '2025_11_02_153656_add_sales_price_to_vehicles_table', 24),
(31, '2025_11_02_155039_add_sold_price_to_vehicles_table', 24),
(32, '2025_11_02_155048_rename_sales_price_to_posted_price_in_vehicles_table', 24),
(33, '2025_11_03_122120_create_showrooms_table', 25),
(34, '2025_11_08_071521_create_payment_methods_table', 26),
(35, '2025_11_08_071526_add_expense_date_and_payment_method_to_expense_items_table', 26),
(36, '2025_11_08_072921_add_requested_approved_paid_by_fields_to_expense_items_table', 27),
(37, '2025_11_08_144049_create_daily_budgets_table', 28),
(38, '2025_11_08_144516_add_added_cash_to_daily_budgets_table', 29),
(39, '2025_11_08_151307_create_cash_additions_table', 30),
(40, '2025_11_08_164445_add_page_and_section_to_activity_logs_table', 31),
(42, '2025_11_08_165117_add_primary_photo_to_employees_table', 32),
(43, '2025_11_08_171827_change_office_to_operating_in_expense_items', 32),
(44, '2025_11_20_052718_create_vehicle_documents_table', 33),
(45, '2025_11_20_080413_create_document_form_templates_table', 34),
(46, '2025_11_20_110918_add_is_completed_to_vehicle_documents_table', 35),
(47, '2025_11_20_114707_create_vehicle_document_files_table', 36),
(48, '2025_11_20_120042_add_customer_information_to_vehicle_status_details_table', 37),
(49, '2025_11_20_123300_add_consent_form_to_vehicle_documents_table', 38),
(50, '2025_11_24_235114_add_body_type_to_vehicles_table', 39),
(51, '2025_11_25_002529_add_check_date_and_checked_by_to_vehicle_documents_table', 40),
(52, '2025_11_29_025333_create_spot_cash_details_table', 41),
(53, '2025_11_29_122052_add_customer_request_to_payment_tag_in_expense_items', 42),
(54, '2025_11_29_122453_add_customer_request_to_payment_tag_enum_in_expense_items', 42),
(55, '2025_11_29_134925_add_selling_price_to_vehicles_table', 43),
(56, '2025_11_29_140358_update_fuel_type_enum_in_vehicles_table', 44),
(58, '2025_11_29_235905_sync_selling_price_with_posted_price', 45),
(59, '2025_11_30_010116_add_pending_customer_information_details_status_to_vehicles_table', 46),
(60, '2026_02_04_124559_add_insurance_trade_in_fields_to_vehicle_status_details_table', 47),
(61, '2026_02_04_132317_add_expense_category_to_expense_items_table', 48),
(62, '2026_02_04_135541_create_vehicle_ads_table', 49),
(63, '2026_02_24_000000_create_vehicle_forfeit_details_table', 50),
(64, '2026_02_27_000000_create_buffing_records_table', 51),
(65, '2026_02_27_100000_create_purchase_orders_table', 52),
(66, '2026_02_27_120000_create_source_screenshots_table', 53),
(67, '2026_02_27_140000_create_video_posting_records_table', 54),
(68, '2026_02_27_150000_create_follow_up_documents_table', 55),
(69, '2026_02_27_160000_create_client_follow_up_list_table', 56),
(70, '2026_02_27_170000_create_contracts_table', 57),
(71, '2026_02_27_180000_add_vehicle_and_employee_to_contracts_table', 58),
(72, '2026_02_27_190000_create_transfer_orcr_table', 59),
(73, '2026_02_27_200000_create_sales_agent_commissions_table', 60),
(74, '2026_02_27_180000_add_client_follow_up_spreadsheet_columns', 61),
(75, '2026_02_27_190000_create_appointments_table', 62),
(76, '2026_02_27_200000_create_insurance_tracker_table', 63),
(77, '2026_02_27_100000_create_recommendation_trackers_table', 64),
(78, '2026_02_28_100000_create_recommendation_tracker_images_table', 65),
(79, '2026_02_28_120000_create_company_documents_table', 66),
(80, '2026_02_28_130000_create_agent_bolo_agents_table', 67),
(81, '2026_02_28_130001_add_agent_bolo_agent_id_to_company_documents_table', 67),
(82, '2026_02_28_140000_add_spreadsheet_columns_to_agent_bolo_agents_table', 68),
(83, '2026_02_27_200000_add_pricelist_financing_to_vehicles_table', 69),
(84, '2026_02_27_210000_create_vehicle_incentives_table', 70),
(85, '2026_02_28_200000_create_car_financing_settings_table', 71),
(86, '2026_02_28_210000_add_chattel_fee_percent_to_car_financing_settings', 72),
(87, '2026_02_24_100000_create_financing_schemes_table', 73),
(88, '2026_02_24_100001_add_financing_scheme_id_to_car_financing_settings', 74);

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE `models` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `make_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `models`
--

INSERT INTO `models` (`id`, `make_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Camry', 'Toyota Camry model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(2, 1, 'Corolla', 'Toyota Corolla model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(3, 1, 'RAV4', 'Toyota RAV4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(4, 1, 'Highlander', 'Toyota Highlander model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(5, 1, 'Prius', 'Toyota Prius model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(6, 1, 'Avalon', 'Toyota Avalon model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(7, 1, 'Sienna', 'Toyota Sienna model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(8, 1, 'Tacoma', 'Toyota Tacoma model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(9, 1, 'Tundra', 'Toyota Tundra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(10, 1, '4Runner', 'Toyota 4Runner model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(11, 1, 'Sequoia', 'Toyota Sequoia model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(12, 1, 'Land Cruiser', 'Toyota Land Cruiser model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(13, 1, 'Yaris', 'Toyota Yaris model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(14, 1, 'C-HR', 'Toyota C-HR model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(15, 1, 'Venza', 'Toyota Venza model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(16, 1, 'Innova', 'Toyota Innova model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(17, 1, 'Fortuner', 'Toyota Fortuner model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(18, 1, 'Hilux', 'Toyota Hilux model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(19, 1, 'Alphard', 'Toyota Alphard model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(20, 1, 'Vios', 'Toyota Vios model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(21, 2, 'Civic', 'Honda Civic model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(22, 2, 'Accord', 'Honda Accord model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(23, 2, 'CR-V', 'Honda CR-V model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(24, 2, 'Pilot', 'Honda Pilot model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(25, 2, 'HR-V', 'Honda HR-V model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(26, 2, 'Passport', 'Honda Passport model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(27, 2, 'Ridgeline', 'Honda Ridgeline model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(28, 2, 'Insight', 'Honda Insight model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(29, 2, 'Fit', 'Honda Fit model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(30, 2, 'Odyssey', 'Honda Odyssey model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(31, 2, 'Element', 'Honda Element model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(32, 2, 'S2000', 'Honda S2000 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(33, 2, 'NSX', 'Honda NSX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(34, 2, 'Prelude', 'Honda Prelude model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(35, 2, 'Integra', 'Honda Integra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(36, 2, 'City', 'Honda City model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(37, 2, 'BR-V', 'Honda BR-V model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(38, 2, 'Mobilio', 'Honda Mobilio model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(39, 2, 'Brio', 'Honda Brio model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(40, 2, 'Freed', 'Honda Freed model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(41, 3, 'Altima', 'Nissan Altima model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(42, 3, 'Sentra', 'Nissan Sentra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(43, 3, 'Rogue', 'Nissan Rogue model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(44, 3, 'Murano', 'Nissan Murano model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(45, 3, 'Pathfinder', 'Nissan Pathfinder model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(46, 3, 'Armada', 'Nissan Armada model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(47, 3, 'Frontier', 'Nissan Frontier model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(48, 3, 'Titan', 'Nissan Titan model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(49, 3, '370Z', 'Nissan 370Z model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(50, 3, 'GT-R', 'Nissan GT-R model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(51, 3, 'Leaf', 'Nissan Leaf model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(52, 3, 'Versa', 'Nissan Versa model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(53, 3, 'Maxima', 'Nissan Maxima model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(54, 3, 'Juke', 'Nissan Juke model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(55, 3, 'Cube', 'Nissan Cube model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(56, 3, 'Navara', 'Nissan Navara model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(57, 3, 'Terra', 'Nissan Terra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(58, 3, 'Almera', 'Nissan Almera model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(59, 3, 'Livina', 'Nissan Livina model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(60, 3, 'Grand Livina', 'Nissan Grand Livina model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(61, 4, 'Outlander', 'Mitsubishi Outlander model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(62, 4, 'Eclipse Cross', 'Mitsubishi Eclipse Cross model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(63, 4, 'Mirage', 'Mitsubishi Mirage model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(64, 4, 'Lancer', 'Mitsubishi Lancer model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(65, 4, 'Galant', 'Mitsubishi Galant model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(66, 4, 'Diamante', 'Mitsubishi Diamante model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(67, 4, 'Montero', 'Mitsubishi Montero model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(68, 4, 'Endeavor', 'Mitsubishi Endeavor model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(69, 4, 'Eclipse', 'Mitsubishi Eclipse model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(70, 4, '3000GT', 'Mitsubishi 3000GT model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(71, 4, 'Starion', 'Mitsubishi Starion model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(72, 4, 'Cordia', 'Mitsubishi Cordia model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(73, 4, 'Tredia', 'Mitsubishi Tredia model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(74, 4, 'Sigma', 'Mitsubishi Sigma model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(75, 4, 'Debonair', 'Mitsubishi Debonair model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(76, 4, 'Strada', 'Mitsubishi Strada model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(77, 4, 'Adventure', 'Mitsubishi Adventure model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(78, 4, 'Xpander', 'Mitsubishi Xpander model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(79, 4, 'Mirage G4', 'Mitsubishi Mirage G4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(80, 4, 'ASX', 'Mitsubishi ASX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(81, 5, 'Swift', 'Suzuki Swift model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(82, 5, 'SX4', 'Suzuki SX4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(83, 5, 'Grand Vitara', 'Suzuki Grand Vitara model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(84, 5, 'Kizashi', 'Suzuki Kizashi model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(85, 5, 'Equator', 'Suzuki Equator model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(86, 5, 'XL7', 'Suzuki XL7 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(87, 5, 'Aerio', 'Suzuki Aerio model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(88, 5, 'Esteem', 'Suzuki Esteem model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(89, 5, 'Forenza', 'Suzuki Forenza model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(90, 5, 'Reno', 'Suzuki Reno model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(91, 5, 'Verona', 'Suzuki Verona model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(92, 5, 'X-90', 'Suzuki X-90 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(93, 5, 'Samurai', 'Suzuki Samurai model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(94, 5, 'Sidekick', 'Suzuki Sidekick model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(95, 5, 'Vitara', 'Suzuki Vitara model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(96, 5, 'Ertiga', 'Suzuki Ertiga model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(97, 5, 'Celerio', 'Suzuki Celerio model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(98, 5, 'Jimny', 'Suzuki Jimny model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(99, 5, 'Baleno', 'Suzuki Baleno model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(100, 5, 'Dzire', 'Suzuki Dzire model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(101, 6, 'Mazda3', 'Mazda Mazda3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(102, 6, 'Mazda6', 'Mazda Mazda6 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(103, 6, 'CX-5', 'Mazda CX-5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(104, 6, 'CX-9', 'Mazda CX-9 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(105, 6, 'MX-5', 'Mazda MX-5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(106, 6, 'RX-8', 'Mazda RX-8 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(107, 6, 'Tribute', 'Mazda Tribute model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(108, 6, 'B-Series', 'Mazda B-Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(109, 6, 'MPV', 'Mazda MPV model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(110, 6, 'Protege', 'Mazda Protege model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(111, 6, 'Millenia', 'Mazda Millenia model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(112, 6, '929', 'Mazda 929 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(113, 6, 'RX-7', 'Mazda RX-7 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(114, 6, 'Cosmo', 'Mazda Cosmo model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(115, 6, 'Eunos', 'Mazda Eunos model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(116, 6, 'CX-3', 'Mazda CX-3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(117, 6, 'CX-30', 'Mazda CX-30 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(118, 6, 'MX-30', 'Mazda MX-30 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(119, 6, 'BT-50', 'Mazda BT-50 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(120, 6, 'Mazda2', 'Mazda Mazda2 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(121, 7, 'Elantra', 'Hyundai Elantra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(122, 7, 'Sonata', 'Hyundai Sonata model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(123, 7, 'Tucson', 'Hyundai Tucson model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(124, 7, 'Santa Fe', 'Hyundai Santa Fe model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(125, 7, 'Palisade', 'Hyundai Palisade model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(126, 7, 'Veloster', 'Hyundai Veloster model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(127, 7, 'Genesis', 'Hyundai Genesis model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(128, 7, 'Accent', 'Hyundai Accent model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(129, 7, 'Azera', 'Hyundai Azera model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(130, 7, 'Entourage', 'Hyundai Entourage model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(131, 7, 'Tiburon', 'Hyundai Tiburon model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(132, 7, 'XG300', 'Hyundai XG300 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(133, 7, 'XG350', 'Hyundai XG350 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(134, 7, 'Scoupe', 'Hyundai Scoupe model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(135, 7, 'Excel', 'Hyundai Excel model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(136, 7, 'Creta', 'Hyundai Creta model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(137, 7, 'Venue', 'Hyundai Venue model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(138, 7, 'Kona', 'Hyundai Kona model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(139, 7, 'i10', 'Hyundai i10 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(140, 7, 'i20', 'Hyundai i20 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(141, 8, 'Forte', 'Kia Forte model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(142, 8, 'Optima', 'Kia Optima model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(143, 8, 'Sportage', 'Kia Sportage model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(144, 8, 'Sorento', 'Kia Sorento model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(145, 8, 'Telluride', 'Kia Telluride model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(146, 8, 'Soul', 'Kia Soul model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(147, 8, 'Stinger', 'Kia Stinger model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(148, 8, 'Cadenza', 'Kia Cadenza model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(149, 8, 'Sedona', 'Kia Sedona model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(150, 8, 'Spectra', 'Kia Spectra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(151, 8, 'Amanti', 'Kia Amanti model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(152, 8, 'Rondo', 'Kia Rondo model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(153, 8, 'Borrego', 'Kia Borrego model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(154, 8, 'Rio', 'Kia Rio model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(155, 8, 'Niro', 'Kia Niro model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(156, 8, 'Picanto', 'Kia Picanto model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(157, 8, 'Carens', 'Kia Carens model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(158, 8, 'Carnival', 'Kia Carnival model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(159, 8, 'Seltos', 'Kia Seltos model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(160, 8, 'Stonic', 'Kia Stonic model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(161, 9, 'Focus', 'Ford Focus model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(162, 9, 'Fusion', 'Ford Fusion model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(163, 9, 'Escape', 'Ford Escape model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(164, 9, 'Explorer', 'Ford Explorer model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(165, 9, 'Expedition', 'Ford Expedition model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(166, 9, 'F-150', 'Ford F-150 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(167, 9, 'Ranger', 'Ford Ranger model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(168, 9, 'Mustang', 'Ford Mustang model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(169, 9, 'Edge', 'Ford Edge model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(170, 9, 'Flex', 'Ford Flex model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(171, 9, 'Taurus', 'Ford Taurus model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(172, 9, 'Crown Victoria', 'Ford Crown Victoria model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(173, 9, 'Thunderbird', 'Ford Thunderbird model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(174, 9, 'GT', 'Ford GT model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(175, 9, 'Bronco', 'Ford Bronco model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(176, 9, 'Everest', 'Ford Everest model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(177, 9, 'Territory', 'Ford Territory model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(178, 9, 'EcoSport', 'Ford EcoSport model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(179, 9, 'Fiesta', 'Ford Fiesta model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(180, 9, 'Mondeo', 'Ford Mondeo model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(181, 10, 'Cruze', 'Chevrolet Cruze model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(182, 10, 'Malibu', 'Chevrolet Malibu model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(183, 10, 'Equinox', 'Chevrolet Equinox model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(184, 10, 'Traverse', 'Chevrolet Traverse model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(185, 10, 'Tahoe', 'Chevrolet Tahoe model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(186, 10, 'Silverado', 'Chevrolet Silverado model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(187, 10, 'Colorado', 'Chevrolet Colorado model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(188, 10, 'Camaro', 'Chevrolet Camaro model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(189, 10, 'Corvette', 'Chevrolet Corvette model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(190, 10, 'Impala', 'Chevrolet Impala model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(191, 10, 'Avalanche', 'Chevrolet Avalanche model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(192, 10, 'Suburban', 'Chevrolet Suburban model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(193, 10, 'Blazer', 'Chevrolet Blazer model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(194, 10, 'Tracker', 'Chevrolet Tracker model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(195, 10, 'Cavalier', 'Chevrolet Cavalier model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(196, 10, 'Sonic', 'Chevrolet Sonic model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(197, 10, 'Spark', 'Chevrolet Spark model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(198, 10, 'Trax', 'Chevrolet Trax model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(199, 10, 'Trailblazer', 'Chevrolet Trailblazer model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(200, 10, 'Optra', 'Chevrolet Optra model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(201, 11, '3 Series', 'BMW 3 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(202, 11, '5 Series', 'BMW 5 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(203, 11, '7 Series', 'BMW 7 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(204, 11, 'X1', 'BMW X1 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(205, 11, 'X3', 'BMW X3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(206, 11, 'X5', 'BMW X5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(207, 11, 'X7', 'BMW X7 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(208, 11, 'Z4', 'BMW Z4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(209, 11, 'i3', 'BMW i3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(210, 11, 'i8', 'BMW i8 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(211, 11, 'M3', 'BMW M3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(212, 11, 'M5', 'BMW M5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(213, 11, 'X6', 'BMW X6 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(214, 11, '2 Series', 'BMW 2 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(215, 11, '4 Series', 'BMW 4 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(216, 11, '6 Series', 'BMW 6 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(217, 11, '8 Series', 'BMW 8 Series model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(218, 11, 'iX', 'BMW iX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(219, 11, 'X2', 'BMW X2 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(220, 11, 'X4', 'BMW X4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(221, 12, 'C-Class', 'Mercedes-Benz C-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(222, 12, 'E-Class', 'Mercedes-Benz E-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(223, 12, 'S-Class', 'Mercedes-Benz S-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(224, 12, 'A-Class', 'Mercedes-Benz A-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(225, 12, 'B-Class', 'Mercedes-Benz B-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(226, 12, 'GLA', 'Mercedes-Benz GLA model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(227, 12, 'GLC', 'Mercedes-Benz GLC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(228, 12, 'GLE', 'Mercedes-Benz GLE model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(229, 12, 'GLS', 'Mercedes-Benz GLS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(230, 12, 'G-Class', 'Mercedes-Benz G-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(231, 12, 'CLS', 'Mercedes-Benz CLS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(232, 12, 'SL', 'Mercedes-Benz SL model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(233, 12, 'AMG GT', 'Mercedes-Benz AMG GT model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(234, 12, 'Sprinter', 'Mercedes-Benz Sprinter model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(235, 12, 'V-Class', 'Mercedes-Benz V-Class model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(236, 12, 'CLA', 'Mercedes-Benz CLA model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(237, 12, 'GLB', 'Mercedes-Benz GLB model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(238, 12, 'EQC', 'Mercedes-Benz EQC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(239, 12, 'A-Class Sedan', 'Mercedes-Benz A-Class Sedan model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(240, 12, 'GLE Coupe', 'Mercedes-Benz GLE Coupe model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(241, 13, 'A3', 'Audi A3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(242, 13, 'A4', 'Audi A4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(243, 13, 'A6', 'Audi A6 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(244, 13, 'A8', 'Audi A8 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(245, 13, 'Q3', 'Audi Q3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(246, 13, 'Q5', 'Audi Q5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(247, 13, 'Q7', 'Audi Q7 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(248, 13, 'Q8', 'Audi Q8 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(249, 13, 'TT', 'Audi TT model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(250, 13, 'R8', 'Audi R8 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(251, 13, 'e-tron', 'Audi e-tron model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(252, 13, 'A1', 'Audi A1 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(253, 13, 'A5', 'Audi A5 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(254, 13, 'A7', 'Audi A7 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(255, 13, 'Q2', 'Audi Q2 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(256, 13, 'RS3', 'Audi RS3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(257, 13, 'RS4', 'Audi RS4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(258, 13, 'RS6', 'Audi RS6 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(259, 13, 'S3', 'Audi S3 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(260, 13, 'S4', 'Audi S4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(261, 14, 'Golf', 'Volkswagen Golf model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(262, 14, 'Jetta', 'Volkswagen Jetta model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(263, 14, 'Passat', 'Volkswagen Passat model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(264, 14, 'Tiguan', 'Volkswagen Tiguan model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(265, 14, 'Atlas', 'Volkswagen Atlas model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(266, 14, 'Beetle', 'Volkswagen Beetle model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(267, 14, 'CC', 'Volkswagen CC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(268, 14, 'Touareg', 'Volkswagen Touareg model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(269, 14, 'Polo', 'Volkswagen Polo model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(270, 14, 'Vento', 'Volkswagen Vento model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(271, 14, 'Virtus', 'Volkswagen Virtus model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(272, 14, 'Taos', 'Volkswagen Taos model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(273, 14, 'ID.4', 'Volkswagen ID.4 model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(274, 14, 'Arteon', 'Volkswagen Arteon model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(275, 14, 'T-Cross', 'Volkswagen T-Cross model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(276, 14, 'T-Roc', 'Volkswagen T-Roc model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(277, 14, 'Caddy', 'Volkswagen Caddy model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(278, 14, 'Transporter', 'Volkswagen Transporter model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(279, 14, 'Crafter', 'Volkswagen Crafter model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(280, 14, 'Amarok', 'Volkswagen Amarok model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(281, 15, 'ES', 'Lexus ES model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(282, 15, 'IS', 'Lexus IS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(283, 15, 'GS', 'Lexus GS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(284, 15, 'LS', 'Lexus LS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(285, 15, 'RX', 'Lexus RX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(286, 15, 'GX', 'Lexus GX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(287, 15, 'LX', 'Lexus LX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(288, 15, 'NX', 'Lexus NX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(289, 15, 'UX', 'Lexus UX model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(290, 15, 'LC', 'Lexus LC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(291, 15, 'RC', 'Lexus RC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(292, 15, 'CT', 'Lexus CT model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(293, 15, 'HS', 'Lexus HS model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(294, 15, 'SC', 'Lexus SC model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(295, 15, 'LFA', 'Lexus LFA model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(296, 15, 'ES Hybrid', 'Lexus ES Hybrid model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(297, 15, 'RX Hybrid', 'Lexus RX Hybrid model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(298, 15, 'NX Hybrid', 'Lexus NX Hybrid model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(299, 15, 'UX Hybrid', 'Lexus UX Hybrid model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(300, 15, 'LS Hybrid', 'Lexus LS Hybrid model', 1, '2025-10-25 05:28:57', '2025-10-25 05:28:57'),
(302, 1, 'Wigo', NULL, 1, '2025-11-29 05:57:52', '2025-11-29 05:57:52'),
(303, 1, 'avanza', NULL, 1, '2025-12-03 09:52:33', '2025-12-03 09:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'COST CENTER (FLAGSHIP BUDGET)', 1, 1, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(2, 'COST CENTER (WAREHOUSE BUDGET)', 1, 2, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(3, 'COST CENTER (ANNEX BUDGET)', 1, 3, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(4, 'GCASH', 1, 4, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(5, 'CREDIT CARD #1', 1, 5, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(6, 'CREDIT CARD #2', 1, 6, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(7, 'CREDIT CARD #3', 1, 7, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(8, 'CREDIT CARD #4', 1, 8, '2025-11-07 23:23:15', '2025-11-07 23:23:15'),
(9, 'CREDIT CARD #5', 1, 9, '2025-11-07 23:23:15', '2025-11-07 23:23:15');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(255) DEFAULT NULL,
  `po_date` date NOT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recommendation_trackers`
--

CREATE TABLE `recommendation_trackers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `year` varchar(20) DEFAULT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `paint` varchar(255) DEFAULT NULL,
  `hood` tinyint(1) NOT NULL DEFAULT 0,
  `front_bumper` tinyint(1) NOT NULL DEFAULT 0,
  `grille` tinyint(1) NOT NULL DEFAULT 0,
  `fender_right` tinyint(1) NOT NULL DEFAULT 0,
  `fender_left` tinyint(1) NOT NULL DEFAULT 0,
  `driver_passenger_door` tinyint(1) NOT NULL DEFAULT 0,
  `driver_side_door` tinyint(1) NOT NULL DEFAULT 0,
  `step_board_left` tinyint(1) NOT NULL DEFAULT 0,
  `step_board_right` tinyint(1) NOT NULL DEFAULT 0,
  `trunk_lid` tinyint(1) NOT NULL DEFAULT 0,
  `quarter_panels_left` tinyint(1) NOT NULL DEFAULT 0,
  `rear_bumper` tinyint(1) NOT NULL DEFAULT 0,
  `quarter_panel_right` tinyint(1) NOT NULL DEFAULT 0,
  `passenger_door_right_rear` tinyint(1) NOT NULL DEFAULT 0,
  `passenger_door_right_front` tinyint(1) NOT NULL DEFAULT 0,
  `roof` tinyint(1) NOT NULL DEFAULT 0,
  `spoiler` tinyint(1) NOT NULL DEFAULT 0,
  `tire_1` tinyint(1) NOT NULL DEFAULT 0,
  `tire_2` tinyint(1) NOT NULL DEFAULT 0,
  `tire_3` tinyint(1) NOT NULL DEFAULT 0,
  `tire_4` tinyint(1) NOT NULL DEFAULT 0,
  `rims_1` tinyint(1) NOT NULL DEFAULT 0,
  `rims_2` tinyint(1) NOT NULL DEFAULT 0,
  `rims_3` tinyint(1) NOT NULL DEFAULT 0,
  `rims_4` tinyint(1) NOT NULL DEFAULT 0,
  `front_headlight_1` tinyint(1) NOT NULL DEFAULT 0,
  `front_headlight_2` tinyint(1) NOT NULL DEFAULT 0,
  `inner_rear_taillight_1` tinyint(1) NOT NULL DEFAULT 0,
  `inner_rear_taillight_2` tinyint(1) NOT NULL DEFAULT 0,
  `taillight_1` tinyint(1) NOT NULL DEFAULT 0,
  `taillight_2` tinyint(1) NOT NULL DEFAULT 0,
  `side_mirror_left` tinyint(1) NOT NULL DEFAULT 0,
  `side_mirror_right` tinyint(1) NOT NULL DEFAULT 0,
  `mud_guard` tinyint(1) NOT NULL DEFAULT 0,
  `windshield_front` tinyint(1) NOT NULL DEFAULT 0,
  `windshield_rear` tinyint(1) NOT NULL DEFAULT 0,
  `with_spare_key` tinyint(1) NOT NULL DEFAULT 0,
  `with_spare_tire` tinyint(1) NOT NULL DEFAULT 0,
  `with_tools` tinyint(1) NOT NULL DEFAULT 0,
  `with_matting_complete` tinyint(1) NOT NULL DEFAULT 0,
  `row_2nd` tinyint(1) NOT NULL DEFAULT 0,
  `row_3rd` tinyint(1) NOT NULL DEFAULT 0,
  `row_1st` tinyint(1) NOT NULL DEFAULT 0,
  `dash_cam` tinyint(1) NOT NULL DEFAULT 0,
  `odometers` varchar(100) DEFAULT NULL,
  `authorized_drivers` varchar(255) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recommendation_trackers`
--

INSERT INTO `recommendation_trackers` (`id`, `date`, `year`, `customer`, `make`, `model`, `paint`, `hood`, `front_bumper`, `grille`, `fender_right`, `fender_left`, `driver_passenger_door`, `driver_side_door`, `step_board_left`, `step_board_right`, `trunk_lid`, `quarter_panels_left`, `rear_bumper`, `quarter_panel_right`, `passenger_door_right_rear`, `passenger_door_right_front`, `roof`, `spoiler`, `tire_1`, `tire_2`, `tire_3`, `tire_4`, `rims_1`, `rims_2`, `rims_3`, `rims_4`, `front_headlight_1`, `front_headlight_2`, `inner_rear_taillight_1`, `inner_rear_taillight_2`, `taillight_1`, `taillight_2`, `side_mirror_left`, `side_mirror_right`, `mud_guard`, `windshield_front`, `windshield_rear`, `with_spare_key`, `with_spare_tire`, `with_tools`, `with_matting_complete`, `row_2nd`, `row_3rd`, `row_1st`, `dash_cam`, `odometers`, `authorized_drivers`, `vehicle_id`, `created_at`, `updated_at`) VALUES
(1, '2026-02-28', '2017', 'john b', 'Toyota', 'Wigo', NULL, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, 130, '2026-02-27 17:54:10', '2026-02-27 17:54:10');

-- --------------------------------------------------------

--
-- Table structure for table `recommendation_tracker_images`
--

CREATE TABLE `recommendation_tracker_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recommendation_tracker_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recommendation_tracker_images`
--

INSERT INTO `recommendation_tracker_images` (`id`, `recommendation_tracker_id`, `file_path`, `original_name`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'recommendation-tracker/images/3c93799b-050c-4b49-83e7-742375c11628.png', 'senior-banner-1.png', 0, '2026-02-27 17:54:13', '2026-02-27 17:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `sales_agents`
--

CREATE TABLE `sales_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `sales_agent_id` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `base_salary` decimal(10,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_agents`
--

INSERT INTO `sales_agents` (`id`, `name`, `email`, `phone`, `sales_agent_id`, `department`, `position`, `hire_date`, `commission_rate`, `base_salary`, `address`, `emergency_contact_name`, `emergency_contact_phone`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'John Michael Santos', 'john.santos@carempire.com', '+63 917 123 4567', 'SA001', 'Sales', 'Senior Sales Agent', '2022-01-15', 5.50, 25000.00, '123 Main Street, Quezon City, Metro Manila', 'Maria Santos', '+63 917 987 6543', 'active', 'Top performer for 3 consecutive quarters. Specializes in luxury vehicles.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(2, 'Sarah Jane Rodriguez', 'sarah.rodriguez@carempire.com', '+63 918 234 5678', 'SA002', 'Sales', 'Sales Agent', '2022-03-20', 4.00, 22000.00, '456 Oak Avenue, Makati City, Metro Manila', 'Carlos Rodriguez', '+63 918 876 5432', 'active', 'Excellent customer service skills. Focuses on family vehicles.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(3, 'Michael David Chen', 'michael.chen@carempire.com', '+63 919 345 6789', 'SA003', 'Sales', 'Sales Manager', '2021-08-10', 6.00, 35000.00, '789 Pine Street, Taguig City, Metro Manila', 'Lisa Chen', '+63 919 765 4321', 'active', 'Team leader with 5+ years experience. Manages 8 sales agents.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(4, 'Maria Elena Cruz', 'maria.cruz@carempire.com', '+63 920 456 7890', 'SA004', 'Sales', 'Sales Agent', '2022-06-05', 4.50, 23000.00, '321 Elm Street, Pasig City, Metro Manila', 'Roberto Cruz', '+63 920 654 3210', 'active', 'New to the team but showing great potential. Specializes in compact cars.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(5, 'James Patrick Wilson', 'james.wilson@carempire.com', '+63 921 567 8901', 'SA005', 'Sales', 'Senior Sales Agent', '2021-11-30', 5.00, 28000.00, '654 Maple Drive, Mandaluyong City, Metro Manila', 'Jennifer Wilson', '+63 921 543 2109', 'active', 'Experienced in commercial vehicle sales. Strong negotiation skills.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(6, 'Ana Marie Garcia', 'ana.garcia@carempire.com', '+63 922 678 9012', 'SA006', 'Sales', 'Sales Agent', '2023-01-15', 3.50, 20000.00, '987 Cedar Lane, San Juan City, Metro Manila', 'Miguel Garcia', '+63 922 432 1098', 'active', 'Fresh graduate with excellent communication skills. Learning quickly.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(7, 'Robert Anthony Lee', 'robert.lee@carempire.com', '+63 923 789 0123', 'SA007', 'Sales', 'Sales Agent', '2022-09-12', 4.20, 22500.00, '147 Birch Street, Marikina City, Metro Manila', 'Grace Lee', '+63 923 321 0987', 'active', 'Strong technical knowledge of vehicles. Good with detailed explanations.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(8, 'Catherine Rose Martinez', 'catherine.martinez@carempire.com', '+63 924 890 1234', 'SA008', 'Sales', 'Senior Sales Agent', '2021-05-18', 5.80, 30000.00, '258 Spruce Avenue, Las Piñas City, Metro Manila', 'Antonio Martinez', '+63 924 210 9876', 'active', 'Consistent top performer. Excellent at closing deals. Mentor to junior agents.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(9, 'David Christopher Kim', 'david.kim@carempire.com', '+63 925 901 2345', 'SA009', 'Sales', 'Sales Agent', '2022-12-01', 3.80, 21000.00, '369 Walnut Street, Parañaque City, Metro Manila', 'Sofia Kim', '+63 925 109 8765', 'active', 'Bilingual (English and Korean). Good with international clients.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(10, 'Lisa Marie Thompson', 'lisa.thompson@carempire.com', '+63 926 012 3456', 'SA010', 'Sales', 'Sales Agent', '2023-03-08', 3.20, 19500.00, '741 Cherry Lane, Muntinlupa City, Metro Manila', 'William Thompson', '+63 926 098 7654', 'active', 'Recent hire. Enthusiastic and eager to learn. Good with social media marketing.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(11, 'Mark Anthony Flores', 'mark.flores@carempire.com', '+63 927 123 4567', 'SA011', 'Sales', 'Sales Agent', '2022-07-25', 4.30, 23500.00, '852 Poplar Drive, Caloocan City, Metro Manila', 'Carmen Flores', '+63 927 987 6543', 'active', 'Good relationship builder. Strong in follow-up and customer retention.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(12, 'Jennifer Lynn Anderson', 'jennifer.anderson@carempire.com', '+63 928 234 5678', 'SA012', 'Sales', 'Senior Sales Agent', '2021-12-03', 5.20, 27000.00, '963 Sycamore Street, Valenzuela City, Metro Manila', 'Robert Anderson', '+63 928 876 5432', 'active', 'Specializes in premium vehicles. Excellent product knowledge.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(13, 'Christopher Paul Reyes', 'christopher.reyes@carempire.com', '+63 929 345 6789', 'SA013', 'Sales', 'Sales Agent', '2023-05-20', 3.60, 20500.00, '159 Hickory Lane, Malabon City, Metro Manila', 'Patricia Reyes', '+63 929 765 4321', 'active', 'Tech-savvy. Good with digital tools and CRM systems.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(14, 'Michelle Anne Torres', 'michelle.torres@carempire.com', '+63 930 456 7890', 'SA014', 'Sales', 'Sales Agent', '2022-04-14', 4.10, 22800.00, '357 Magnolia Avenue, Navotas City, Metro Manila', 'Jose Torres', '+63 930 654 3210', 'active', 'Strong analytical skills. Good at identifying customer needs.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(15, 'Daniel Joseph Ramos', 'daniel.ramos@carempire.com', '+63 931 567 8901', 'SA015', 'Sales', 'Sales Agent', '2021-10-22', 4.70, 24500.00, '468 Dogwood Street, Pateros, Metro Manila', 'Elena Ramos', '+63 931 543 2109', 'active', 'Experienced in fleet sales. Good with corporate clients.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(16, 'Stephanie Joy Hernandez', 'stephanie.hernandez@carempire.com', '+63 932 678 9012', 'SA016', 'Sales', 'Sales Agent', '2022-11-30', 3.90, 21800.00, '579 Redwood Drive, Manila City, Metro Manila', 'Manuel Hernandez', '+63 932 432 1098', 'active', 'Good with first-time buyers. Patient and thorough in explanations.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(17, 'Kevin Michael Lopez', 'kevin.lopez@carempire.com', '+63 933 789 0123', 'SA017', 'Sales', 'Sales Agent', '2023-02-15', 3.40, 20200.00, '680 Sequoia Lane, Pasay City, Metro Manila', 'Rosa Lopez', '+63 933 321 0987', 'active', 'Quick learner. Good at adapting to different customer personalities.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(18, 'Amanda Grace White', 'amanda.white@carempire.com', '+63 934 890 1234', 'SA018', 'Sales', 'Senior Sales Agent', '2021-07-08', 5.30, 26500.00, '791 Ash Street, Makati City, Metro Manila', 'Thomas White', '+63 934 210 9876', 'active', 'Strong leadership qualities. Often assists in training new agents.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(19, 'Ryan Patrick Brown', 'ryan.brown@carempire.com', '+63 935 901 2345', 'SA019', 'Sales', 'Sales Agent', '2022-08-18', 4.40, 23200.00, '802 Elm Avenue, Quezon City, Metro Manila', 'Linda Brown', '+63 935 109 8765', 'active', 'Good with financing options. Helps customers with loan applications.', '2025-10-28 08:18:49', '2025-10-28 08:28:53'),
(20, 'Nicole Marie Davis', 'nicole.davis@carempire.com', '+63 936 012 3456', 'SA020', 'Sales', 'Sales Agent', '2023-01-10', 3.70, 20800.00, '913 Oak Drive, Taguig City, Metro Manila', 'Michael Davis', '+63 936 098 7654', 'inactive', 'Recently resigned. Was a promising agent with good potential.', '2025-10-28 08:18:49', '2025-10-28 08:28:53');

-- --------------------------------------------------------

--
-- Table structure for table `sales_agent_commissions`
--

CREATE TABLE `sales_agent_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agent_name` varchar(255) NOT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL DEFAULT 'CASH',
  `release_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `coms_paid_via` varchar(100) DEFAULT NULL,
  `date_sent` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_agent_commissions`
--

INSERT INTO `sales_agent_commissions` (`id`, `agent_name`, `client_name`, `unit`, `vehicle_id`, `plate_number`, `transaction_type`, `release_date`, `amount`, `coms_paid_via`, `date_sent`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'John B', NULL, '2017 Toyota Wigo G', 130, 'nbz9090', 'CASH', '2026-02-28', 5000.00, 'BPI', '2026-02-28', 'na', '2026-02-27 09:51:55', '2026-02-27 09:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `showrooms`
--

CREATE TABLE `showrooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showrooms`
--

INSERT INTO `showrooms` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'FLAGSHIP', 'Flagship Showroom', 1, '2025-11-03 04:22:02', '2025-11-03 04:22:02'),
(2, 'PREMIUM', 'Premium Showroom', 1, '2025-11-03 04:22:02', '2025-11-03 04:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `source_screenshots`
--

CREATE TABLE `source_screenshots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `screenshot_date` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spot_cash_details`
--

CREATE TABLE `spot_cash_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `down_payment` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `check_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_notes` text DEFAULT NULL,
  `customer_first_name` varchar(255) DEFAULT NULL,
  `customer_last_name` varchar(255) DEFAULT NULL,
  `customer_middle_name` varchar(255) DEFAULT NULL,
  `customer_contact_number` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_date_of_birth` date DEFAULT NULL,
  `customer_gender` varchar(255) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `customer_city` varchar(255) DEFAULT NULL,
  `customer_province` varchar(255) DEFAULT NULL,
  `customer_postal_code` varchar(255) DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spot_cash_details`
--

INSERT INTO `spot_cash_details` (`id`, `vehicle_id`, `payment_method`, `total_amount`, `down_payment`, `balance`, `payment_date`, `check_number`, `bank_name`, `reference_number`, `payment_notes`, `customer_first_name`, `customer_last_name`, `customer_middle_name`, `customer_contact_number`, `customer_email`, `customer_date_of_birth`, `customer_gender`, `customer_address`, `customer_city`, `customer_province`, `customer_postal_code`, `customer_notes`, `created_at`, `updated_at`) VALUES
(1, 128, 'Cash', 500000.00, NULL, 500000.00, '2025-11-29', NULL, NULL, NULL, NULL, 'aaa', 'ccc', 'bbb', '23423', 'john@gmail.com', '1989-01-10', 'Male', 'adfadf', 'adfad', 'adsf', '234234', 'adfasdf', '2025-11-28 19:15:17', '2025-11-28 19:15:17');

-- --------------------------------------------------------

--
-- Table structure for table `tools_inventory`
--

CREATE TABLE `tools_inventory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(10,2) NOT NULL,
  `date_acquired` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tools_inventory`
--

INSERT INTO `tools_inventory` (`id`, `name`, `quantity`, `amount`, `date_acquired`, `created_at`, `updated_at`) VALUES
(1, 'HYDRAULIC FLOOR STAND', 1, 12000.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(2, 'OFF SET RING', 1, 2220.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(3, 'CORDLESS IMPACT WRENCH', 1, 8625.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(4, 'CURVED JAW LOCKING PLIER', 1, 450.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(5, 'ADJUSTABLE WRENCH', 1, 420.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(6, '1 SET SCREW DRIVER', 1, 700.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(7, '1 SET PLIERS', 1, 1250.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(8, 'JACK STAND', 2, 3900.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(9, 'RUBBER AND PLASTIC HAMMER', 1, 360.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(10, 'BALLPEEN HAMMER', 1, 315.00, '2025-08-02', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(11, 'PIPE WRENCH 10', 1, 2575.00, '2025-08-14', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(12, 'PIPE WRENCH 12', 1, 2745.00, '2025-08-14', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(13, 'GRINDER', 1, 1344.00, '2025-08-15', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(14, 'MANIFOLD GAUGE', 1, 3300.00, '2025-08-16', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(15, 'PC VLV RMVR', 1, 40.00, '2025-08-16', '2025-11-01 21:32:02', '2025-11-01 22:48:23'),
(16, 'CFLR HIGH SIDE CONNECTOR AND LOW (ROTARY TRADING)', 2, 700.00, '2025-08-16', '2025-11-01 21:32:02', '2025-11-01 21:32:02'),
(17, 'SOLDERING IRON', 1, 480.00, '2025-08-17', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(18, 'EXTENSION WIRE', 1, 610.00, '2025-08-17', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(19, 'CALIPER', 1, 765.00, '2025-08-22', '2025-11-01 21:32:02', '2025-11-01 22:54:01'),
(20, 'SCANNER (ANNEX)', 0, 0.00, '2025-11-02', '2025-11-01 22:54:01', '2025-11-01 22:54:01'),
(21, 'VACUUM PUMP', 0, 0.00, '2025-11-02', '2025-11-01 22:54:01', '2025-11-01 22:54:01'),
(22, 'YABE TUBO', 0, 0.00, '2025-11-02', '2025-11-01 22:54:01', '2025-11-01 22:54:01'),
(23, 'Payong', 2, 200.00, '2025-11-02', '2025-11-01 23:31:51', '2025-11-01 23:31:51'),
(24, 'CALIPER', 2, 800.00, '2025-11-02', '2025-11-01 23:35:34', '2025-11-01 23:35:34'),
(25, 'Payong', 1, 200.00, '2025-11-01', '2025-11-01 23:41:28', '2025-11-01 23:41:28');

-- --------------------------------------------------------

--
-- Table structure for table `transfer_orcr`
--

CREATE TABLE `transfer_orcr` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` varchar(50) NOT NULL DEFAULT 'ORCR',
  `release_date` date DEFAULT NULL,
  `lto_file_no` varchar(100) DEFAULT NULL,
  `transfer_sop` decimal(12,2) NOT NULL DEFAULT 0.00,
  `transfer_or` decimal(12,2) NOT NULL DEFAULT 0.00,
  `others` decimal(12,2) DEFAULT NULL,
  `notary` decimal(12,2) DEFAULT NULL,
  `pnp_clearance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `confirmation` decimal(12,2) DEFAULT NULL,
  `rd` varchar(100) DEFAULT NULL,
  `rd_sop` decimal(12,2) DEFAULT NULL,
  `rd_or` decimal(12,2) DEFAULT NULL,
  `renewal_reg_or` decimal(12,2) DEFAULT NULL,
  `renewal_sop` decimal(12,2) DEFAULT NULL,
  `smoke_na` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `transfer_sop_paid` tinyint(1) NOT NULL DEFAULT 0,
  `transfer_or_paid` tinyint(1) NOT NULL DEFAULT 0,
  `pnp_clearance_paid` tinyint(1) NOT NULL DEFAULT 0,
  `rd_sop_paid` tinyint(1) NOT NULL DEFAULT 0,
  `rd_or_paid` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transfer_orcr`
--

INSERT INTO `transfer_orcr` (`id`, `date`, `vehicle_id`, `transaction_type`, `release_date`, `lto_file_no`, `transfer_sop`, `transfer_or`, `others`, `notary`, `pnp_clearance`, `confirmation`, `rd`, `rd_sop`, `rd_or`, `renewal_reg_or`, `renewal_sop`, `smoke_na`, `remarks`, `status`, `transfer_sop_paid`, `transfer_or_paid`, `pnp_clearance_paid`, `rd_sop_paid`, `rd_or_paid`, `created_at`, `updated_at`) VALUES
(1, '2026-02-27', 130, 'ORCR', '2026-02-28', 'test', 5000.00, 5000.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DONE', 1, 1, 0, 0, 0, '2026-02-27 15:26:45', '2026-02-27 15:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@carempire.com', '2025-10-25 04:40:09', '$2y$12$qNWc1iSK7RR67BtV4ALg.Oq6X7m8TORB4zcYC.rFeOiHnPysuC1U6', 'admin', 'kl9MU0VdpLRiQSyDhEiAOQ4TB5bVMFMNumRbM10vHjsb4BWwjfEvHLFwV4J4', '2025-10-25 04:40:09', '2025-10-25 04:40:09'),
(2, 'John Developer', 'john@carempire.com', '2025-10-25 04:40:10', '$2y$12$AK1/sqwRytVTWu10VACYgOVt9ZqAMd736YEXub/lIIuqpUPyulMgu', 'user', NULL, '2025-10-25 04:40:10', '2025-10-25 04:40:10');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `make_id` bigint(20) UNSIGNED DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `make` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `variant` varchar(255) DEFAULT NULL,
  `body_type` varchar(255) DEFAULT NULL,
  `transmission` enum('Manual','Automatic') NOT NULL,
  `fuel_type` enum('Diesel','Gasoline','Hybrid','Electric') NOT NULL,
  `kilometers` bigint(20) NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `colour` varchar(255) NOT NULL,
  `with_tools` tinyint(1) NOT NULL DEFAULT 0,
  `with_matting` tinyint(1) NOT NULL DEFAULT 0,
  `with_spare_tire` tinyint(1) NOT NULL DEFAULT 0,
  `purchase_price` decimal(12,2) NOT NULL,
  `posted_price` decimal(10,2) DEFAULT NULL,
  `sold_price` decimal(10,2) DEFAULT NULL,
  `option1_cash_out` decimal(12,2) DEFAULT NULL,
  `option1_12mos` decimal(12,2) DEFAULT NULL,
  `option1_24mos` decimal(12,2) DEFAULT NULL,
  `option1_36mos` decimal(12,2) DEFAULT NULL,
  `option1_48mos` decimal(12,2) DEFAULT NULL,
  `option2_cash_out` decimal(12,2) DEFAULT NULL,
  `option2_12mos` decimal(12,2) DEFAULT NULL,
  `option2_24mos` decimal(12,2) DEFAULT NULL,
  `option2_36mos` decimal(12,2) DEFAULT NULL,
  `option2_48mos` decimal(12,2) DEFAULT NULL,
  `purchased_from` varchar(255) NOT NULL,
  `selling_price` decimal(12,2) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `spare_key` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `status` enum('Available','Under Maintenance','Reserved','Released','Forfeited','Pending Customer Information Details') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `year`, `make_id`, `model_id`, `make`, `model`, `variant`, `body_type`, `transmission`, `fuel_type`, `kilometers`, `plate_number`, `colour`, `with_tools`, `with_matting`, `with_spare_tire`, `purchase_price`, `posted_price`, `sold_price`, `option1_cash_out`, `option1_12mos`, `option1_24mos`, `option1_36mos`, `option1_48mos`, `option2_cash_out`, `option2_12mos`, `option2_24mos`, `option2_36mos`, `option2_48mos`, `purchased_from`, `selling_price`, `purchase_date`, `spare_key`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 2016, NULL, NULL, 'Toyota', 'Innova', 'GLX', NULL, 'Manual', 'Diesel', 154339, 'XYZ-7141', 'Black', 1, 1, 1, 2087363.00, 2092363.00, NULL, 513667.12, 160832.97, 91087.54, 67839.06, 56214.82, 911216.09, 120624.73, 68315.65, 50879.29, 42161.11, 'Leo Garcia', NULL, '2022-03-25', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(2, 2012, NULL, NULL, 'Kia', 'Sorento', 'Limited', NULL, 'Automatic', 'Diesel', 96048, 'XYZ-5333', 'Blue', 0, 1, 1, 327859.00, 332859.00, NULL, 91386.16, 25585.76, 14490.46, 10792.03, 8942.81, 154629.37, 19189.32, 10867.85, 8094.02, 6707.11, 'Maria Santos', NULL, '2021-08-23', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(3, 2014, NULL, NULL, 'Nissan', 'Navara', 'Sport', NULL, 'Manual', 'Diesel', 107485, 'JHK-3354', 'Red', 1, 0, 1, 950940.00, 955940.00, NULL, 240925.60, 73479.92, 41615.25, 30993.70, 25682.92, 422554.20, 55109.94, 31211.44, 23245.27, 19262.19, 'John Dela Cruz', NULL, '2017-02-21', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(4, 2010, NULL, NULL, 'Suzuki', 'Vitara', 'GL', NULL, 'Manual', 'Gasoline', 93406, 'ABC-2217', 'White', 0, 1, 0, 1683256.00, 1688256.00, NULL, 416681.44, 129770.61, 73495.41, 54737.01, 45357.81, 737450.08, 97327.96, 55121.56, 41052.76, 34018.36, 'Joseph Lim', NULL, '2020-04-29', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(5, 2018, NULL, NULL, 'Mitsubishi', 'Mirage', 'Premium', NULL, 'Automatic', 'Gasoline', 174433, 'TUV-5181', 'Silver', 1, 1, 1, 2102790.00, 2107790.00, NULL, 517369.60, 162018.79, 91759.12, 68339.24, 56629.29, 917849.70, 121514.09, 68819.34, 51254.43, 42471.97, 'Mark Villanueva', NULL, '2021-03-15', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(6, 2019, NULL, NULL, 'Ford', 'EcoSport', 'Base', NULL, 'Manual', 'Gasoline', 54526, 'ABC-9702', 'Maroon', 0, 0, 1, 1861062.00, 1866062.00, NULL, 459354.88, 143437.97, 81235.90, 60501.88, 50134.87, 813906.66, 107578.47, 60926.92, 45376.41, 37601.15, 'Kristine Lopez', NULL, '2023-04-21', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(7, 2024, NULL, NULL, 'Hyundai', 'Accent', 'GLX', NULL, 'Automatic', 'Gasoline', 139442, 'ABC-9454', 'Black', 1, 1, 0, 2371088.00, 2376088.00, NULL, 638787.23, 182641.96, 103439.03, 77038.05, 63837.56, 1075987.42, 136981.47, 77579.27, 57778.54, 47878.17, 'Anna Mendoza', NULL, '2023-08-26', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(8, 2015, NULL, NULL, 'Mitsubishi', 'Strada', 'Sport', NULL, 'Manual', 'Diesel', 192304, 'JHK-7384', 'Blue', 0, 1, 1, 1699349.00, 1704349.00, NULL, 420543.76, 131007.63, 74195.99, 55258.78, 45790.18, 744370.07, 98255.72, 55646.99, 41444.09, 34342.63, 'Leo Garcia', NULL, '2018-05-18', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(9, 2020, NULL, NULL, 'Ford', 'Mustang', 'Premium', NULL, 'Automatic', 'Gasoline', 140645, 'XYZ-3340', 'Red', 1, 0, 1, 2008264.00, 2013264.00, NULL, 494683.36, 154752.89, 87644.09, 65274.49, 54089.69, 877203.52, 116064.67, 65733.07, 48955.87, 40567.27, 'Maria Santos', NULL, '2020-04-30', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(10, 2013, NULL, NULL, 'Nissan', 'Terra', 'GL', NULL, 'Manual', 'Diesel', 26455, 'XYZ-3749', 'White', 0, 1, 0, 2191170.00, 2196170.00, NULL, 538580.80, 168812.27, 95606.60, 71204.71, 59003.77, 955853.10, 126609.20, 71704.95, 53403.53, 44252.83, 'John Dela Cruz', NULL, '2016-11-30', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(11, 2017, NULL, NULL, 'Mitsubishi', 'Montero', 'Sport', NULL, 'Automatic', 'Diesel', 71889, 'NMP-8776', 'Silver', 1, 1, 1, 2314460.00, 2319460.00, NULL, 568170.40, 178289.16, 100973.83, 75202.05, 62316.16, 1008867.80, 133716.87, 75730.37, 56401.54, 46737.12, 'Joseph Lim', NULL, '2022-02-03', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(12, 2015, NULL, NULL, 'Chevrolet', 'Colorado', 'Base', NULL, 'Manual', 'Diesel', 102721, 'XYZ-5924', 'Maroon', 0, 0, 1, 1979831.00, 1984831.00, NULL, 487859.44, 152567.34, 86406.31, 64352.63, 53325.79, 864977.33, 114425.51, 64804.73, 48264.47, 39994.34, 'Mark Villanueva', NULL, '2015-04-02', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(13, 2024, NULL, NULL, 'Mazda', 'CX-9', 'Premium', NULL, 'Automatic', 'Gasoline', 38288, 'JHK-9065', 'Black', 1, 1, 1, 2183557.00, 2188557.00, NULL, 589279.05, 168227.08, 95275.18, 70957.88, 58799.23, 991973.54, 126170.31, 71456.39, 53218.41, 44099.42, 'Kristine Lopez', NULL, '2023-11-28', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(14, 2019, NULL, NULL, 'Suzuki', 'Swift', 'GLX', NULL, 'Manual', 'Gasoline', 34989, 'XYZ-2927', 'Blue', 0, 1, 0, 1788085.00, 1793085.00, NULL, 441840.40, 137828.47, 78058.97, 58135.80, 48174.22, 782526.55, 103371.35, 58544.23, 43601.85, 36130.66, 'Anna Mendoza', NULL, '2024-12-09', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(15, 2021, NULL, NULL, 'Toyota', 'Fortuner', 'Sport', NULL, 'Automatic', 'Diesel', 124830, 'XYZ-5316', 'Red', 1, 0, 1, 1415056.00, 1420056.00, NULL, 352313.44, 109154.97, 61819.77, 46041.37, 38152.17, 622124.08, 81866.23, 46364.83, 34531.03, 28614.13, 'Leo Garcia', NULL, '2021-03-09', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(16, 2015, NULL, NULL, 'Nissan', 'Navara', 'GL', NULL, 'Manual', 'Diesel', 146323, 'ABC-6375', 'White', 0, 1, 1, 1615183.00, 1620183.00, NULL, 400343.92, 124538.07, 70531.97, 52529.93, 43528.92, 708178.69, 93403.55, 52898.97, 39397.45, 32646.69, 'Maria Santos', NULL, '2015-12-16', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(17, 2016, NULL, NULL, 'Kia', 'Sorento', 'Limited', NULL, 'Automatic', 'Diesel', 33426, 'ABC-7246', 'Silver', 1, 0, 0, 958866.00, 963866.00, NULL, 242827.84, 74089.17, 41960.30, 31250.68, 25895.87, 425962.38, 55566.87, 31470.22, 23438.01, 19421.90, 'John Dela Cruz', NULL, '2016-06-20', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(18, 2021, NULL, NULL, 'Mitsubishi', 'Montero', 'Premium', NULL, 'Automatic', 'Diesel', 177121, 'NMP-5991', 'Maroon', 0, 1, 1, 2044008.00, 2049008.00, NULL, 503261.92, 157500.41, 89200.15, 66433.39, 55050.01, 892573.44, 118125.31, 66900.11, 49825.04, 41287.51, 'Joseph Lim', NULL, '2021-07-25', 0, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(19, 2020, NULL, NULL, 'Toyota', 'Innova', 'GLX', NULL, 'Manual', 'Diesel', 48754, 'XYZ-2044', 'Black', 1, 1, 1, 1463909.00, 1468909.00, NULL, 364038.16, 112910.14, 63946.51, 47625.29, 39464.69, 643130.87, 84682.60, 47959.88, 35718.97, 29598.52, 'Mark Villanueva', NULL, '2020-11-15', 1, NULL, 'Available', '2025-10-25 05:10:23', '2026-02-27 22:30:52'),
(21, 2014, NULL, NULL, 'Kia', 'Cadenza', 'LE', NULL, 'Manual', 'Gasoline', 50232, 'TUV-4946', 'Maroon', 0, 0, 1, 4468427.00, 4473427.00, NULL, 1085122.48, 343857.42, 194743.19, 145038.44, 120186.07, 1935073.61, 257893.07, 146057.39, 108778.83, 90139.55, 'Tyler Hernandez', NULL, '2025-02-14', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:44', '2026-02-27 22:30:52'),
(22, 2021, NULL, NULL, 'Kia', 'Niro', 'Premium', NULL, 'Manual', 'Gasoline', 180666, 'NOP-2564', 'Yellow', 0, 0, 1, 4451671.00, 4456671.00, NULL, 1081101.04, 342569.44, 194013.74, 144495.18, 119735.89, 1927868.53, 256927.08, 145510.31, 108371.38, 89801.92, 'Tyler Hernandez', NULL, '2023-11-14', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(23, 2010, NULL, NULL, 'Nissan', 'Cube', 'GL', NULL, 'Automatic', 'Diesel', 195565, 'VWX-7962', 'Maroon', 1, 1, 0, 1678580.00, 1683580.00, NULL, 415559.20, 129411.18, 73291.85, 54585.40, 45232.18, 735439.40, 97058.39, 54968.89, 40939.05, 33924.14, 'Connor Stewart', NULL, '2025-01-24', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(24, 2018, NULL, NULL, 'Suzuki', 'Swift', 'Limited', NULL, 'Automatic', 'Gasoline', 68963, 'PQR-5149', 'Yellow', 0, 0, 1, 4794282.00, 4799282.00, NULL, 1163327.68, 368904.81, 208928.74, 155603.39, 128940.71, 2075191.26, 276678.61, 156696.56, 116702.54, 96705.53, 'Caleb Baker', NULL, '2023-09-28', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(25, 2018, NULL, NULL, 'Chevrolet', 'Suburban', 'Touring', NULL, 'Automatic', 'Gasoline', 110275, 'MNO-2702', 'Gold', 0, 1, 0, 3140296.00, 3145296.00, NULL, 766371.04, 241768.42, 136925.22, 101977.49, 84503.62, 1363977.28, 181326.31, 102693.91, 76483.11, 63377.71, 'David Martinez', NULL, '2021-07-22', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(26, 2019, NULL, NULL, 'Chevrolet', 'Blazer', 'GL', NULL, 'Manual', 'Gasoline', 262707, 'GHI-7024', 'Purple', 0, 1, 1, 1835220.00, 1840220.00, NULL, 453152.80, 141451.58, 80110.91, 59664.02, 49440.58, 802794.60, 106088.68, 60083.18, 44748.02, 37080.43, 'Brandon Walker', NULL, '2022-04-13', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(27, 2017, NULL, NULL, 'Hyundai', 'Veloster', 'Base', NULL, 'Manual', 'Diesel', 142849, 'MNO-5915', 'Yellow', 1, 0, 1, 1079238.00, 1084238.00, NULL, 271717.12, 83341.76, 47200.49, 35153.41, 29129.86, 477722.34, 62506.32, 35400.37, 26365.05, 21847.40, 'Mason Phillips', NULL, '2024-06-26', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(28, 2023, NULL, NULL, 'Nissan', 'Leaf', 'DX', NULL, 'Manual', 'Gasoline', 34960, 'VWX-3613', 'Brown', 0, 0, 1, 3637102.00, 3642102.00, NULL, 973014.93, 279956.24, 158552.84, 118085.04, 97851.14, 1643161.70, 209967.18, 118914.63, 88563.78, 73388.36, 'Jessica Garcia', NULL, '2023-06-15', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(29, 2018, NULL, NULL, 'Mitsubishi', 'Lancer', 'Limited', NULL, 'Manual', 'Gasoline', 278644, 'TUV-1950', 'Brown', 1, 0, 1, 2184310.00, 2189310.00, NULL, 536934.40, 168284.96, 95307.96, 70982.30, 58819.46, 952903.30, 126213.72, 71480.97, 53236.72, 44114.60, 'Jessica Garcia', NULL, '2025-08-26', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(30, 2024, NULL, NULL, 'Kia', 'Rondo', 'Type R', NULL, 'Manual', 'Gasoline', 228592, 'HIJ-7858', 'Orange', 0, 0, 0, 4558734.00, 4563734.00, NULL, 1216325.78, 350799.02, 198674.55, 147966.40, 122612.32, 2056052.83, 263099.27, 149005.92, 110974.80, 91959.24, 'Ryan Lewis', NULL, '2024-07-14', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(31, 2023, NULL, NULL, 'Suzuki', 'Forenza', 'LX', NULL, 'Automatic', 'Diesel', 162531, 'KLM-3826', 'Yellow', 0, 1, 0, 2685082.00, 2690082.00, NULL, 721681.65, 206777.64, 117108.24, 87218.44, 72273.54, 1216656.74, 155083.23, 87831.18, 65413.83, 54205.15, 'Amanda Thomas', NULL, '2024-09-10', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(32, 2016, NULL, NULL, 'Kia', 'Telluride', 'SE', NULL, 'Manual', 'Gasoline', 207918, 'HIJ-9593', 'Gold', 1, 0, 0, 1131088.00, 1136088.00, NULL, 284161.12, 87327.30, 49457.70, 36834.50, 30522.90, 500017.84, 65495.47, 37093.27, 27625.87, 22892.17, 'Taylor Collins', NULL, '2024-09-09', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(33, 2010, NULL, NULL, 'Chevrolet', 'Traverse', 'Premium', NULL, 'Automatic', 'Gasoline', 34904, 'HIJ-6204', 'Green', 0, 1, 0, 2275114.00, 2280114.00, NULL, 558727.36, 175264.76, 99260.96, 73926.36, 61259.06, 991949.02, 131448.57, 74445.72, 55444.77, 45944.30, 'Nicole Rodriguez', NULL, '2021-05-08', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(34, 2015, NULL, NULL, 'Kia', 'Stinger', 'Type R', NULL, 'Manual', 'Diesel', 97041, 'EFG-4565', 'Purple', 0, 1, 0, 3340556.00, 3345556.00, NULL, 814433.44, 257161.74, 145643.20, 108470.36, 89883.94, 1450089.08, 192871.30, 109232.40, 81352.77, 67412.95, 'Megan Hall', NULL, '2022-08-28', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(35, 2010, NULL, NULL, 'Nissan', 'Murano', 'DX', NULL, 'Manual', 'Gasoline', 293487, 'BCD-5947', 'Blue', 0, 1, 1, 1215173.00, 1220173.00, NULL, 304341.52, 93790.63, 53118.20, 39560.72, 32781.98, 536174.39, 70342.97, 39838.65, 29670.54, 24586.49, 'Brittany Scott', NULL, '2024-09-16', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(36, 2022, NULL, NULL, 'Toyota', 'Sequoia', 'LE', NULL, 'Manual', 'Diesel', 285584, 'BCD-8909', 'Blue', 1, 1, 1, 2124349.00, 2129349.00, NULL, 573648.14, 163675.96, 92697.66, 69038.23, 57208.51, 965448.35, 122756.97, 69523.24, 51778.67, 42906.38, 'Robert Johnson', NULL, '2023-07-23', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(37, 2015, NULL, NULL, 'Suzuki', 'XL7', 'GL', NULL, 'Manual', 'Gasoline', 130397, 'PQR-2369', 'Silver', 1, 1, 0, 3465013.00, 3470013.00, NULL, 844303.12, 266728.33, 151061.23, 112505.53, 93227.68, 1503605.59, 200046.25, 113295.92, 84379.15, 69920.76, 'Michelle White', NULL, '2022-07-04', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(38, 2023, NULL, NULL, 'Nissan', 'Titan', 'Type R', NULL, 'Manual', 'Diesel', 91788, 'VWX-4370', 'Yellow', 0, 1, 1, 3260364.00, 3265364.00, NULL, 873556.10, 250997.65, 142152.18, 105870.36, 87729.45, 1474383.07, 188248.23, 106614.13, 79402.77, 65797.08, 'Destiny Turner', NULL, '2022-11-23', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(39, 2018, NULL, NULL, 'Hyundai', 'XG300', 'Type R', NULL, 'Manual', 'Diesel', 52810, 'PQR-5978', 'Brown', 1, 1, 1, 4856775.00, 4861775.00, NULL, 1178326.00, 373708.44, 211649.27, 157629.55, 130619.69, 2102063.25, 280281.33, 158736.95, 118222.16, 97964.77, 'Maria Garcia', NULL, '2022-02-01', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(40, 2013, NULL, NULL, 'Suzuki', 'X-90', 'SE', NULL, 'Automatic', 'Diesel', 245139, 'MNO-4707', 'White', 1, 0, 0, 2769651.00, 2774651.00, NULL, 677416.24, 213278.17, 120789.81, 89960.35, 74545.62, 1204599.93, 159958.63, 90592.36, 67470.26, 55909.22, 'John Smith', NULL, '2021-08-06', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(41, 2014, NULL, NULL, 'Kia', 'Sedona', 'Touring', NULL, 'Manual', 'Gasoline', 130669, 'YZA-5195', 'Blue', 1, 0, 1, 1975626.00, 1980626.00, NULL, 486850.24, 152244.12, 86223.25, 64216.30, 53212.82, 863169.18, 114183.09, 64667.44, 48162.22, 39909.61, 'Sarah Wilson', NULL, '2025-10-15', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(42, 2010, NULL, NULL, 'Mitsubishi', 'Montero', 'Sport', NULL, 'Manual', 'Gasoline', 240035, 'HIJ-5678', 'Green', 1, 1, 0, 1890320.00, 1895320.00, NULL, 466376.80, 145686.93, 82509.60, 61450.49, 50920.93, 826487.60, 109265.20, 61882.20, 46087.86, 38190.70, 'Jacob Wright', NULL, '2024-07-05', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(43, 2023, NULL, NULL, 'Honda', 'Fit', 'Type R', NULL, 'Automatic', 'Diesel', 193749, 'YZA-1258', 'Maroon', 1, 1, 0, 4905528.00, 4910528.00, NULL, 1307879.39, 377455.92, 213771.65, 159210.23, 131929.52, 2211416.54, 283091.94, 160328.74, 119407.67, 98947.14, 'Ryan Lewis', NULL, '2021-01-15', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(44, 2022, NULL, NULL, 'Chevrolet', 'Corvette', 'GL', NULL, 'Manual', 'Gasoline', 39487, 'HIJ-6944', 'Gray', 1, 1, 0, 1601846.00, 1606846.00, NULL, 435707.34, 123512.90, 69951.36, 52097.52, 43170.60, 731367.01, 92634.67, 52463.52, 39073.14, 32377.95, 'Stephanie Robinson', NULL, '2024-04-29', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(45, 2017, NULL, NULL, 'Suzuki', 'Esteem', 'GLX', NULL, 'Manual', 'Diesel', 15589, 'KLM-4580', 'Gray', 1, 1, 1, 568856.00, 573856.00, NULL, 149225.44, 44110.40, 24981.86, 18605.69, 15417.60, 258258.08, 33082.80, 18736.40, 13954.27, 11563.20, 'Amber Carter', NULL, '2023-03-18', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(46, 2024, NULL, NULL, 'Mitsubishi', 'Galant', 'LE', NULL, 'Automatic', 'Diesel', 22029, 'PQR-6678', 'Silver', 1, 0, 1, 1032058.00, 1037058.00, NULL, 285283.31, 79715.19, 45146.59, 33623.72, 27862.29, 476101.98, 59786.39, 33859.94, 25217.79, 20896.72, 'Logan Roberts', NULL, '2022-12-07', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(47, 2021, NULL, NULL, 'Toyota', 'C-HR', 'GLX', NULL, 'Manual', 'Diesel', 174416, 'BCD-1520', 'Brown', 0, 1, 1, 3602681.00, 3607681.00, NULL, 877343.44, 277310.41, 157054.38, 116969.04, 96926.36, 1562802.83, 207982.81, 117790.78, 87726.78, 72694.77, 'Logan Roberts', NULL, '2022-11-08', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(48, 2013, NULL, NULL, 'Honda', 'Integra', 'SE', NULL, 'Automatic', 'Gasoline', 284364, 'YZA-5978', 'Beige', 0, 0, 1, 3932938.00, 3937938.00, NULL, 956605.12, 302696.17, 171431.57, 127676.70, 105799.27, 1704813.34, 227022.13, 128573.68, 95757.53, 79349.45, 'Lauren King', NULL, '2023-06-21', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(49, 2015, NULL, NULL, 'Toyota', 'Sequoia', 'SE', NULL, 'Manual', 'Diesel', 25162, 'VWX-6333', 'Yellow', 1, 0, 0, 1589271.00, 1594271.00, NULL, 394125.04, 122546.30, 69403.93, 51689.81, 42832.75, 697036.53, 91909.72, 52052.95, 38767.36, 32124.56, 'Danielle Adams', NULL, '2024-09-11', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(50, 2017, NULL, NULL, 'Toyota', 'Highlander', 'Base', NULL, 'Manual', 'Gasoline', 225908, 'YZA-4023', 'Maroon', 0, 1, 1, 2148026.00, 2153026.00, NULL, 528226.24, 165495.93, 93728.40, 69805.89, 57844.63, 937301.18, 124121.95, 70296.30, 52354.42, 43383.47, 'Jessica Garcia', NULL, '2022-04-19', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(51, 2024, NULL, NULL, 'Honda', 'Civic', 'SE', NULL, 'Manual', 'Gasoline', 252598, 'GHI-5921', 'Silver', 0, 0, 1, 3676616.00, 3681616.00, NULL, 983446.62, 282993.55, 160273.02, 119366.17, 98912.75, 1660863.97, 212245.16, 120204.76, 89524.63, 74184.56, 'Michelle White', NULL, '2022-02-21', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(52, 2018, NULL, NULL, 'Nissan', '370Z', 'EX', NULL, 'Manual', 'Gasoline', 13026, 'EFG-4388', 'White', 0, 1, 1, 2368921.00, 2373921.00, NULL, 581241.04, 182475.39, 103344.69, 76967.79, 63779.34, 1032286.03, 136856.55, 77508.52, 57725.85, 47834.51, 'Lauren King', NULL, '2021-12-06', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(53, 2018, NULL, NULL, 'Honda', 'Passport', 'LE', NULL, 'Manual', 'Diesel', 201142, 'VWX-1944', 'Red', 0, 1, 0, 1349489.00, 1354489.00, NULL, 336577.36, 104115.05, 58965.42, 43915.54, 36390.60, 593930.27, 78086.29, 44224.07, 32936.66, 27292.95, 'Victoria Gonzalez', NULL, '2024-04-19', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(54, 2022, NULL, NULL, 'Suzuki', 'XL7', 'Sport', NULL, 'Manual', 'Diesel', 90868, 'BCD-2875', 'Yellow', 0, 0, 0, 2707482.00, 2712482.00, NULL, 727595.25, 208499.45, 118083.38, 87944.69, 72875.35, 1226691.94, 156374.59, 88562.54, 65958.52, 54656.51, 'Caleb Baker', NULL, '2021-06-27', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(55, 2023, NULL, NULL, 'Mitsubishi', 'Sigma', 'DX', NULL, 'Manual', 'Gasoline', 236019, 'BCD-2731', 'Beige', 1, 0, 1, 819183.00, 824183.00, NULL, 229084.31, 63352.20, 35879.43, 26721.84, 22143.05, 380733.98, 47514.15, 26909.57, 20041.38, 16607.29, 'Morgan Sanchez', NULL, '2025-04-12', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(56, 2013, NULL, NULL, 'Mitsubishi', 'Montero', 'SE', NULL, 'Manual', 'Gasoline', 185467, 'YZA-2089', 'Silver', 0, 0, 0, 2202984.00, 2207984.00, NULL, 541416.16, 169720.37, 96120.90, 71587.75, 59321.17, 960933.12, 127290.28, 72090.68, 53690.81, 44490.88, 'Joshua Clark', NULL, '2021-03-02', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(57, 2018, NULL, NULL, 'Nissan', 'Armada', 'Type R', NULL, 'Manual', 'Diesel', 263375, 'EFG-9330', 'Maroon', 1, 0, 0, 1335641.00, 1340641.00, NULL, 333253.84, 103050.60, 58362.57, 43466.56, 36018.55, 587975.63, 77287.95, 43771.93, 32599.92, 27013.92, 'Christopher Taylor', NULL, '2021-04-17', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(58, 2022, NULL, NULL, 'Suzuki', 'Reno', 'LE', NULL, 'Automatic', 'Gasoline', 299142, 'TUV-6215', 'Yellow', 0, 1, 0, 2008409.00, 2013409.00, NULL, 543039.98, 154764.04, 87650.41, 65279.19, 54093.59, 913507.23, 116073.03, 65737.80, 48959.40, 40570.19, 'Nicole Rodriguez', NULL, '2024-09-13', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(59, 2022, NULL, NULL, 'Mazda', 'B-Series', 'Touring', NULL, 'Manual', 'Diesel', 266248, 'MNO-4398', 'Green', 1, 0, 1, 889629.00, 894629.00, NULL, 247682.06, 68767.15, 38946.18, 29005.86, 24035.70, 412293.79, 51575.36, 29209.64, 21754.40, 18026.77, 'Michelle White', NULL, '2020-12-02', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(60, 2023, NULL, NULL, 'Honda', 'Accord', 'Touring', NULL, 'Automatic', 'Gasoline', 251721, 'JKL-4471', 'Purple', 0, 1, 1, 1327544.00, 1332544.00, NULL, 363291.62, 102428.22, 58010.08, 43204.04, 35801.02, 608479.71, 76821.16, 43507.56, 32403.03, 26850.76, 'Logan Roberts', NULL, '2020-11-23', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(61, 2016, NULL, NULL, 'Hyundai', 'Scoupe', 'GLX', NULL, 'Manual', 'Gasoline', 138137, 'YZA-8075', 'Yellow', 0, 1, 1, 2364849.00, 2369849.00, NULL, 580263.76, 182162.39, 103167.43, 76835.77, 63669.94, 1030535.07, 136621.79, 77375.57, 57626.83, 47752.46, 'Sarah Wilson', NULL, '2021-01-15', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(62, 2024, NULL, NULL, 'Hyundai', 'Tucson', 'Premium', NULL, 'Automatic', 'Diesel', 156930, 'PQR-3399', 'Gold', 1, 1, 1, 1269442.00, 1274442.00, NULL, 347952.69, 97962.11, 55480.71, 41320.24, 34240.01, 582450.02, 73471.58, 41610.53, 30990.18, 25680.01, 'Brandon Walker', NULL, '2022-11-10', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(63, 2021, NULL, NULL, 'Mazda', 'CX-5', 'XLE', NULL, 'Automatic', 'Gasoline', 279661, 'MNO-9219', 'Pink', 0, 0, 1, 3933866.00, 3938866.00, NULL, 956827.84, 302767.50, 171471.97, 127706.79, 105824.20, 1705212.38, 227075.62, 128603.97, 95780.09, 79368.15, 'Mason Phillips', NULL, '2022-08-14', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(64, 2011, NULL, NULL, 'Nissan', 'Frontier', 'Premium', NULL, 'Manual', 'Gasoline', 10173, 'DEF-1441', 'White', 0, 0, 0, 4406409.00, 4411409.00, NULL, 1070238.16, 339090.31, 192043.34, 143027.68, 118519.86, 1908405.87, 254317.73, 144032.50, 107270.76, 88889.89, 'Amanda Thomas', NULL, '2025-04-18', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(65, 2015, NULL, NULL, 'Mazda', 'CX-5', 'Limited', NULL, 'Manual', 'Diesel', 99595, 'DEF-7052', 'Black', 0, 1, 0, 1055562.00, 1060562.00, NULL, 266034.88, 81521.87, 46169.80, 34385.78, 28493.77, 467541.66, 61141.40, 34627.35, 25789.33, 21370.32, 'Alexis Evans', NULL, '2024-05-07', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(66, 2015, NULL, NULL, 'Toyota', 'Prius', 'Premium', NULL, 'Manual', 'Gasoline', 136128, 'DEF-3909', 'Black', 0, 0, 1, 4688018.00, 4693018.00, NULL, 1137824.32, 360736.65, 204302.72, 152158.07, 126085.75, 2029497.74, 270552.49, 153227.04, 114118.55, 94564.31, 'Samantha Lee', NULL, '2024-03-12', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(67, 2022, NULL, NULL, 'Nissan', 'Versa', 'GLX', NULL, 'Manual', 'Diesel', 88252, 'STU-4677', 'Silver', 1, 0, 0, 1118842.00, 1123842.00, NULL, 308194.29, 86385.99, 48924.59, 36437.46, 30193.89, 514981.22, 64789.49, 36693.44, 27328.09, 22645.42, 'Jasmine Campbell', NULL, '2023-11-23', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(68, 2012, NULL, NULL, 'Kia', 'Soul', 'XLE', NULL, 'Manual', 'Gasoline', 166547, 'KLM-2605', 'Maroon', 0, 1, 0, 4835277.00, 4840277.00, NULL, 1173166.48, 372055.96, 210713.39, 156932.54, 130042.11, 2092819.11, 279041.97, 158035.04, 117699.40, 97531.58, 'Stephanie Robinson', NULL, '2024-07-31', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(69, 2014, NULL, NULL, 'Nissan', 'Pathfinder', 'GLX', NULL, 'Manual', 'Gasoline', 82526, 'HIJ-2778', 'Purple', 1, 0, 0, 3414764.00, 3419764.00, NULL, 832243.36, 262865.86, 148873.73, 110876.35, 91877.66, 1481998.52, 197149.39, 111655.29, 83157.26, 68908.24, 'Connor Stewart', NULL, '2023-08-08', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(70, 2024, NULL, NULL, 'Ford', 'Expedition', 'Sport', NULL, 'Manual', 'Gasoline', 18454, 'VWX-6619', 'Orange', 1, 1, 1, 2111550.00, 2116550.00, NULL, 570269.20, 162692.14, 92140.48, 68623.25, 56864.64, 959714.40, 122019.11, 69105.36, 51467.44, 42648.48, 'Zachary Green', NULL, '2025-01-20', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(71, 2019, NULL, NULL, 'Honda', 'Integra', 'LX', NULL, 'Manual', 'Diesel', 75800, 'MNO-5606', 'Blue', 0, 0, 0, 4455202.00, 4460202.00, NULL, 1081948.48, 342840.86, 194167.46, 144609.66, 119830.76, 1929386.86, 257130.65, 145625.60, 108457.25, 89873.07, 'James Jackson', NULL, '2024-03-23', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(72, 2020, NULL, NULL, 'Honda', 'Fit', 'EX', NULL, 'Manual', 'Gasoline', 56527, 'HIJ-9411', 'Black', 1, 1, 0, 3701368.00, 3706368.00, NULL, 901028.32, 284896.15, 161350.55, 120168.69, 99577.75, 1605238.24, 213672.12, 121012.92, 90126.52, 74683.32, 'Nicole Rodriguez', NULL, '2025-04-05', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(73, 2020, NULL, NULL, 'Hyundai', 'Azera', 'GLX', NULL, 'Automatic', 'Gasoline', 97073, 'MNO-9537', 'Purple', 0, 0, 1, 4128918.00, 4133918.00, NULL, 1003640.32, 317760.50, 179963.23, 134030.81, 111064.60, 1789084.74, 238320.37, 134972.42, 100523.11, 83298.45, 'Jessica Garcia', NULL, '2022-04-21', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(74, 2022, NULL, NULL, 'Ford', 'GT', 'LE', NULL, 'Manual', 'Gasoline', 250440, 'JKL-7684', 'Black', 0, 0, 1, 1392361.00, 1397361.00, NULL, 380403.30, 107410.48, 60831.78, 45305.55, 37542.43, 637517.73, 80557.86, 45623.84, 33979.16, 28156.82, 'Brittany Scott', NULL, '2021-01-15', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(75, 2013, NULL, NULL, 'Hyundai', 'XG300', 'EX', NULL, 'Manual', 'Gasoline', 131263, 'VWX-8713', 'White', 1, 0, 0, 1288444.00, 1293444.00, NULL, 321926.56, 99422.73, 56307.93, 41936.33, 34750.53, 567680.92, 74567.05, 42230.95, 31452.25, 26062.90, 'Daniel Harris', NULL, '2022-08-14', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(76, 2011, NULL, NULL, 'Honda', 'Integra', 'EX', NULL, 'Automatic', 'Diesel', 277974, 'BCD-9772', 'Black', 1, 1, 0, 2203418.00, 2208418.00, NULL, 541520.32, 169753.73, 96139.80, 71601.82, 59332.83, 961119.74, 127315.30, 72104.85, 53701.36, 44499.62, 'Kayla Lopez', NULL, '2023-01-27', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(77, 2018, NULL, NULL, 'Hyundai', 'Excel', 'Touring', NULL, 'Automatic', 'Gasoline', 94863, 'KLM-9936', 'Brown', 1, 1, 1, 3812649.00, 3817649.00, NULL, 927735.76, 293449.95, 166194.99, 123776.66, 102567.50, 1653089.07, 220087.46, 124646.24, 92832.50, 76925.63, 'Morgan Sanchez', NULL, '2024-07-11', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:52'),
(78, 2022, NULL, NULL, 'Mazda', 'Eunos', 'LE', NULL, 'Manual', 'Gasoline', 218038, 'TUV-3713', 'Yellow', 1, 0, 0, 2208528.00, 2213528.00, NULL, 595871.39, 170146.52, 96362.25, 71767.50, 59470.12, 1003160.54, 127609.89, 72271.69, 53825.62, 44602.59, 'Morgan Sanchez', NULL, '2021-06-05', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(79, 2016, NULL, NULL, 'Chevrolet', 'Silverado', 'GLX', NULL, 'Manual', 'Gasoline', 254018, 'PQR-1550', 'Green', 1, 1, 0, 2506813.00, 2511813.00, NULL, 614335.12, 193074.69, 109347.59, 81438.56, 67484.04, 1091579.59, 144806.02, 82010.69, 61078.92, 50613.03, 'Michael Davis', NULL, '2023-05-06', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(80, 2014, NULL, NULL, 'Hyundai', 'Azera', 'SE', NULL, 'Manual', 'Gasoline', 173656, 'EFG-5262', 'White', 0, 0, 1, 4717932.00, 4722932.00, NULL, 1145003.68, 363036.04, 205604.97, 153127.95, 126889.44, 2042360.76, 272277.03, 154203.73, 114845.96, 95167.08, 'Megan Hall', NULL, '2021-07-19', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(81, 2019, NULL, NULL, 'Mitsubishi', 'Diamante', 'SE', NULL, 'Manual', 'Gasoline', 270556, 'VWX-7682', 'White', 1, 0, 1, 3334283.00, 3339283.00, NULL, 812927.92, 256679.55, 145370.12, 108266.98, 89715.40, 1447391.69, 192509.66, 109027.59, 81200.23, 67286.55, 'Sarah Wilson', NULL, '2024-03-16', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(82, 2011, NULL, NULL, 'Honda', 'Integra', 'Type R', NULL, 'Manual', 'Gasoline', 78044, 'GHI-5876', 'Gold', 0, 0, 1, 3346335.00, 3351335.00, NULL, 815820.40, 257605.95, 145894.78, 108657.73, 90039.20, 1452574.05, 193204.46, 109421.09, 81493.30, 67529.40, 'Brittany Scott', NULL, '2022-01-27', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(83, 2016, NULL, NULL, 'Toyota', 'Prius', 'Base', NULL, 'Automatic', 'Gasoline', 31162, 'VWX-7830', 'White', 0, 0, 0, 597292.00, 602292.00, NULL, 156050.08, 46296.18, 26219.78, 19527.65, 16181.58, 270485.56, 34722.13, 19664.83, 14645.73, 12136.18, 'Andrew Martinez', NULL, '2021-05-08', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(84, 2023, NULL, NULL, 'Mitsubishi', 'Eclipse', 'GL', NULL, 'Manual', 'Gasoline', 170441, 'GHI-4630', 'Blue', 0, 1, 0, 2718706.00, 2723706.00, NULL, 730558.38, 209362.20, 118572.00, 88308.60, 73176.90, 1231720.29, 157021.65, 88929.00, 66231.45, 54882.68, 'Robert Johnson', NULL, '2022-11-15', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(85, 2019, NULL, NULL, 'Nissan', 'Murano', 'DX', NULL, 'Manual', 'Gasoline', 179077, 'GHI-2835', 'Blue', 1, 1, 0, 4402640.00, 4407640.00, NULL, 1069333.60, 338800.59, 191879.26, 142905.48, 118418.59, 1906785.20, 254100.45, 143909.45, 107179.11, 88813.95, 'Danielle Adams', NULL, '2022-08-05', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(86, 2010, NULL, NULL, 'Honda', 'NSX', 'EX-L', NULL, 'Automatic', 'Gasoline', 36800, 'QRS-8714', 'Green', 1, 1, 0, 2571519.00, 2576519.00, NULL, 629864.56, 198048.43, 112164.46, 83536.47, 69222.48, 1119403.17, 148536.32, 84123.35, 62652.35, 51916.86, 'John Smith', NULL, '2022-06-18', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(87, 2018, NULL, NULL, 'Toyota', 'Highlander', 'Sport', NULL, 'Automatic', 'Gasoline', 229089, 'TUV-7524', 'Gray', 0, 0, 1, 4575578.00, 4580578.00, NULL, 1110838.72, 352093.76, 199407.83, 148512.52, 123064.86, 1981148.54, 264070.32, 149555.87, 111384.39, 92298.65, 'Connor Stewart', NULL, '2024-12-02', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(88, 2022, NULL, NULL, 'Toyota', 'RAV4', 'LX', NULL, 'Manual', 'Gasoline', 269447, 'YZA-3303', 'Orange', 1, 1, 1, 4780018.00, 4785018.00, NULL, 1274744.75, 367808.38, 208307.78, 155140.92, 128557.48, 2155188.06, 275856.29, 156230.84, 116355.69, 96418.11, 'Lucas Parker', NULL, '2021-10-31', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(89, 2012, NULL, NULL, 'Chevrolet', 'Impala', 'GL', NULL, 'Automatic', 'Diesel', 231203, 'BCD-4796', 'Purple', 0, 1, 0, 786271.00, 791271.00, NULL, 201405.04, 60822.36, 34446.66, 25654.76, 21258.81, 351746.53, 45616.77, 25835.00, 19241.07, 15944.11, 'Robert Johnson', NULL, '2025-09-06', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(90, 2011, NULL, NULL, 'Kia', 'Stinger', 'Touring', NULL, 'Manual', 'Diesel', 73398, 'BCD-3217', 'Red', 1, 0, 1, 3516349.00, 3521349.00, NULL, 856623.76, 270674.36, 153296.06, 114169.96, 94606.91, 1525680.07, 203005.77, 114972.04, 85627.47, 70955.18, 'Samantha Lee', NULL, '2021-12-18', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(91, 2023, NULL, NULL, 'Chevrolet', 'Blazer', 'DX', NULL, 'Automatic', 'Gasoline', 258498, 'STU-6281', 'Green', 1, 1, 0, 2003641.00, 2008641.00, NULL, 541781.22, 154397.54, 87442.84, 65124.60, 53965.49, 911371.17, 115798.15, 65582.13, 48843.45, 40474.12, 'Owen Edwards', NULL, '2021-05-13', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(92, 2020, NULL, NULL, 'Nissan', 'Altima', 'EX-L', NULL, 'Manual', 'Gasoline', 240732, 'STU-9362', 'Yellow', 1, 0, 0, 934085.00, 939085.00, NULL, 236880.40, 72184.33, 40881.50, 30447.22, 25230.08, 415306.55, 54138.25, 30661.13, 22835.42, 18922.56, 'Samantha Lee', NULL, '2023-09-10', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(93, 2024, NULL, NULL, 'Toyota', 'Camry', 'GL', NULL, 'Manual', 'Gasoline', 115484, 'TUV-2385', 'Pink', 1, 1, 0, 612878.00, 617878.00, NULL, 174619.79, 47494.22, 26898.29, 20032.98, 16600.32, 288309.34, 35620.67, 20173.72, 15024.73, 12450.24, 'Robert Johnson', NULL, '2020-11-23', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(94, 2017, NULL, NULL, 'Honda', 'Pilot', 'XLE', NULL, 'Manual', 'Gasoline', 141167, 'JKL-4101', 'Black', 1, 0, 1, 1553585.00, 1558585.00, NULL, 385560.40, 119803.23, 67850.40, 50532.79, 41873.98, 681691.55, 89852.43, 50887.80, 37899.59, 31405.49, 'Logan Roberts', NULL, '2024-09-27', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(95, 2016, NULL, NULL, 'Hyundai', 'Azera', 'GLX', NULL, 'Automatic', 'Gasoline', 220926, 'DEF-2086', 'Pink', 0, 0, 1, 959430.00, 964430.00, NULL, 242963.20, 74132.52, 41984.85, 31268.96, 25911.02, 426204.90, 55599.39, 31488.64, 23451.72, 19433.26, 'Michelle White', NULL, '2023-10-11', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(96, 2013, NULL, NULL, 'Toyota', 'Yaris', 'LX', NULL, 'Manual', 'Gasoline', 234050, 'PQR-3740', 'Gold', 1, 1, 1, 2777549.00, 2782549.00, NULL, 679311.76, 213885.27, 121133.63, 90216.42, 74757.82, 1207996.07, 160413.95, 90850.22, 67662.32, 56068.36, 'Ryan Lewis', NULL, '2024-11-19', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(97, 2012, NULL, NULL, 'Mazda', 'CX-9', 'Sport', NULL, 'Automatic', 'Gasoline', 164923, 'GHI-1173', 'Green', 0, 1, 0, 4942872.00, 4947872.00, NULL, 1198989.28, 380326.43, 215397.36, 160421.01, 132932.83, 2139084.96, 285244.82, 161548.02, 120315.75, 99699.62, 'Sierra Perez', NULL, '2022-11-15', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(98, 2015, NULL, NULL, 'Toyota', 'Land Cruiser', 'Base', NULL, 'Automatic', 'Diesel', 31404, 'HIJ-3581', 'Blue', 0, 0, 0, 2950614.00, 2955614.00, NULL, 720847.36, 227188.20, 128667.73, 95827.57, 79407.50, 1282414.02, 170391.15, 96500.80, 71870.68, 59555.62, 'Jasmine Campbell', NULL, '2024-08-22', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(99, 2013, NULL, NULL, 'Nissan', 'GT-R', 'GL', NULL, 'Automatic', 'Gasoline', 272730, 'BCD-9889', 'Orange', 1, 0, 0, 1943751.00, 1948751.00, NULL, 479200.24, 149793.99, 84835.63, 63182.84, 52356.44, 849462.93, 112345.50, 63626.72, 47387.13, 39267.33, 'Amanda Thomas', NULL, '2023-10-30', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(100, 2010, NULL, NULL, 'Honda', 'Element', 'LX', NULL, 'Manual', 'Diesel', 108786, 'BCD-8551', 'Black', 0, 0, 0, 592595.00, 597595.00, NULL, 154922.80, 45935.14, 26015.30, 19375.36, 16055.39, 268465.85, 34451.35, 19511.48, 14531.52, 12041.54, 'Lisa Brown', NULL, '2024-05-15', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(101, 2017, NULL, NULL, 'Mitsubishi', 'Endeavor', 'Type R', NULL, 'Manual', 'Gasoline', 288809, 'KLM-1157', 'Maroon', 0, 0, 0, 4867569.00, 4872569.00, NULL, 1180916.56, 374538.14, 212119.17, 157979.51, 130909.69, 2106704.67, 280903.60, 159089.38, 118484.64, 98182.27, 'Rachel Young', NULL, '2021-05-02', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(102, 2017, NULL, NULL, 'Suzuki', 'Forenza', 'GL', NULL, 'Automatic', 'Diesel', 16353, 'NOP-6876', 'White', 0, 0, 1, 3688267.00, 3693267.00, NULL, 897884.08, 283889.12, 160780.22, 119743.92, 99225.77, 1599604.81, 212916.84, 120585.17, 89807.94, 74419.33, 'Ryan Lewis', NULL, '2022-01-20', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(103, 2015, NULL, NULL, 'Suzuki', 'Sidekick', 'GL', NULL, 'Manual', 'Diesel', 108167, 'HIJ-8081', 'Gold', 1, 0, 0, 3053753.00, 3058753.00, NULL, 745600.72, 235116.15, 133157.71, 99171.57, 82178.50, 1326763.79, 176337.11, 99868.29, 74378.68, 61633.87, 'Sarah Wilson', NULL, '2022-05-19', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(104, 2011, NULL, NULL, 'Chevrolet', 'Colorado', 'LE', NULL, 'Manual', 'Diesel', 144339, 'PQR-3895', 'Maroon', 0, 1, 1, 3769898.00, 3774898.00, NULL, 917475.52, 290163.83, 164333.89, 122390.58, 101418.93, 1634706.14, 217622.87, 123250.42, 91792.94, 76064.19, 'Zachary Green', NULL, '2021-03-06', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(105, 2017, NULL, NULL, 'Honda', 'S2000', 'Touring', NULL, 'Manual', 'Diesel', 287005, 'BCD-5519', 'Gray', 1, 0, 1, 2660095.00, 2665095.00, NULL, 651122.80, 204856.97, 116020.47, 86408.30, 71602.22, 1157490.85, 153642.73, 87015.35, 64806.23, 53701.66, 'James Jackson', NULL, '2025-01-03', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(106, 2022, NULL, NULL, 'Kia', 'Sedona', 'EX', NULL, 'Automatic', 'Gasoline', 94364, 'VWX-7546', 'Orange', 1, 1, 1, 3765371.00, 3770371.00, NULL, 1006877.94, 289815.85, 164136.82, 122243.81, 101297.30, 1700626.21, 217361.89, 123102.61, 91682.85, 75972.98, 'Megan Hall', NULL, '2024-10-21', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(107, 2012, NULL, NULL, 'Suzuki', 'Grand Vitara', 'EX', NULL, 'Manual', 'Gasoline', 33868, 'MNO-2732', 'Maroon', 0, 1, 0, 1591875.00, 1596875.00, NULL, 394750.00, 122746.46, 69517.29, 51774.24, 42902.71, 698156.25, 92059.84, 52137.97, 38830.68, 32177.03, 'Jessica Garcia', NULL, '2025-08-05', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(108, 2019, NULL, NULL, 'Hyundai', 'Sonata', 'Type R', NULL, 'Manual', 'Gasoline', 148303, 'TUV-6270', 'Blue', 0, 1, 1, 1072298.00, 1077298.00, NULL, 270051.52, 82808.31, 46898.37, 34928.40, 28943.41, 474738.14, 62106.23, 35173.78, 26196.30, 21707.55, 'Ashley Martin', NULL, '2021-11-20', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(109, 2023, NULL, NULL, 'Mitsubishi', '3000GT', 'LX', NULL, 'Automatic', 'Diesel', 132591, 'STU-8721', 'Brown', 0, 1, 0, 1176828.00, 1181828.00, NULL, 323502.59, 90843.18, 51448.91, 38317.49, 31751.78, 540958.94, 68132.38, 38586.68, 28738.12, 23813.83, 'Ethan Nelson', NULL, '2020-12-17', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(110, 2015, NULL, NULL, 'Toyota', 'Avalon', 'SE', NULL, 'Manual', 'Gasoline', 239036, 'NOP-7189', 'Orange', 1, 1, 0, 3729467.00, 3734467.00, NULL, 907772.08, 287056.03, 162573.80, 121079.72, 100332.68, 1617320.81, 215292.02, 121930.35, 90809.79, 75249.51, 'Maria Garcia', NULL, '2021-11-21', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(111, 2018, NULL, NULL, 'Suzuki', 'Swift', 'Touring', NULL, 'Manual', 'Diesel', 141217, 'PQR-8227', 'Brown', 0, 0, 1, 1816781.00, 1821781.00, NULL, 448727.44, 140034.23, 79308.20, 59066.19, 48945.18, 794865.83, 105025.67, 59481.15, 44299.64, 36708.89, 'Sarah Wilson', NULL, '2023-04-02', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(112, 2012, NULL, NULL, 'Ford', 'GT', 'SE', NULL, 'Automatic', 'Gasoline', 40554, 'MNO-4932', 'Yellow', 0, 1, 0, 4511058.00, 4516058.00, NULL, 1095353.92, 347134.32, 196599.06, 146420.64, 121331.42, 1953404.94, 260350.74, 147449.29, 109815.48, 90998.57, 'Noah Mitchell', NULL, '2021-12-24', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(113, 2018, NULL, NULL, 'Hyundai', 'Veloster', 'Limited', NULL, 'Automatic', 'Gasoline', 87164, 'NOP-1853', 'Yellow', 1, 1, 1, 604565.00, 609565.00, NULL, 157795.60, 46855.23, 26536.40, 19763.45, 16376.98, 273612.95, 35141.42, 19902.30, 14822.59, 12282.73, 'Caleb Baker', NULL, '2023-07-18', 1, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(114, 2021, NULL, NULL, 'Nissan', 'Cube', 'Premium', NULL, 'Automatic', 'Gasoline', 204470, 'BCD-9515', 'Gray', 1, 0, 1, 3284221.00, 3289221.00, NULL, 800913.04, 252831.45, 143190.75, 106643.85, 88370.40, 1425865.03, 189623.59, 107393.07, 79982.89, 66277.80, 'Lucas Parker', NULL, '2022-10-28', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(115, 2012, NULL, NULL, 'Suzuki', 'Swift', 'Type R', NULL, 'Automatic', 'Diesel', 162693, 'TUV-5374', 'Beige', 1, 1, 0, 4012290.00, 4017290.00, NULL, 975649.60, 308795.69, 174886.02, 130249.47, 107931.19, 1738934.70, 231596.77, 131164.52, 97687.10, 80948.39, 'John Smith', NULL, '2024-10-02', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(116, 2021, NULL, NULL, 'Chevrolet', 'Suburban', 'EX', NULL, 'Automatic', 'Diesel', 169423, 'DEF-9038', 'Yellow', 1, 1, 0, 3669364.00, 3674364.00, NULL, 893347.36, 282436.11, 159957.31, 119131.05, 98717.91, 1591476.52, 211827.08, 119967.98, 89348.28, 74038.43, 'Samantha Lee', NULL, '2022-08-23', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(117, 2023, NULL, NULL, 'Mazda', 'Millenia', 'Touring', NULL, 'Automatic', 'Diesel', 257946, 'HIJ-3906', 'Blue', 1, 1, 0, 802351.00, 807351.00, NULL, 224640.66, 62058.38, 35146.68, 26176.11, 21690.83, 373193.25, 46543.79, 26360.01, 19632.09, 16268.12, 'Lucas Parker', NULL, '2021-04-16', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(118, 2022, NULL, NULL, 'Honda', 'Fit', 'Sport', NULL, 'Automatic', 'Diesel', 40525, 'NOP-7033', 'Brown', 1, 0, 0, 3731752.00, 3736752.00, NULL, 998002.53, 287231.67, 162673.27, 121153.80, 100394.07, 1685564.90, 215423.75, 122004.95, 90865.35, 75295.55, 'Ryan Lewis', NULL, '2025-01-18', 0, NULL, 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(119, 2014, NULL, NULL, 'Hyundai', 'Elantra', 'Touring', NULL, 'Automatic', 'Gasoline', 153199, 'STU-6979', 'Maroon', 1, 0, 0, 4611940.00, 4616940.00, NULL, 1119565.60, 354888.79, 200990.79, 149691.45, 124041.79, 1996784.20, 266166.59, 150743.09, 112268.59, 93031.34, 'Noah Mitchell', NULL, '2023-03-01', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(120, 2013, NULL, NULL, 'Suzuki', 'Vitara', 'Premium', NULL, 'Manual', 'Gasoline', 266577, 'BCD-9809', 'Black', 0, 0, 0, 2808646.00, 2813646.00, NULL, 686775.04, 216275.59, 122487.39, 91224.66, 75593.29, 1221367.78, 162206.69, 91865.54, 68418.49, 56694.97, 'Danielle Adams', NULL, '2025-09-20', 0, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(121, 2012, NULL, NULL, 'Nissan', 'Juke', 'Premium', NULL, 'Automatic', 'Gasoline', 248102, 'TUV-6931', 'Beige', 0, 1, 1, 4065233.00, 4070233.00, NULL, 988355.92, 312865.24, 177190.81, 131966.00, 109353.59, 1761700.19, 234648.93, 132893.11, 98974.50, 82015.19, 'Amanda Thomas', NULL, '2023-01-15', 1, 'Well maintained vehicle', 'Available', '2025-10-25 05:17:55', '2026-02-27 22:30:53'),
(123, 1999, 1, 1, 'Toyota', 'Camry', NULL, NULL, 'Manual', 'Diesel', 1, '12313A', 'Blue', 1, 1, 1, 500000.00, 505000.00, NULL, 132700.00, 38817.67, 21984.33, 16373.22, 13567.67, 228650.00, 29113.25, 16488.25, 12279.92, 10175.75, 'asdfadsf', NULL, '2025-10-25', 1, 'na', 'Available', '2025-10-25 05:51:07', '2026-02-27 22:30:53'),
(124, 2000, 1, 19, 'Toyota', 'Alphard', NULL, NULL, 'Manual', 'Diesel', 234, 'nbz12313', 'Blue', 1, 1, 0, 285000.00, 290000.00, NULL, 81100.00, 22291.33, 12624.67, 9402.44, 7791.33, 136200.00, 16718.50, 9468.50, 7051.83, 5843.50, 'somewhere', 400000.00, '2025-10-25', 0, 'adfadf', 'Reserved', '2025-10-25 05:58:51', '2026-02-27 22:30:53'),
(125, 2000, 1, 17, 'Toyota', 'Fortuner', 'E', NULL, 'Manual', 'Diesel', 22, 'adfa44', 'red', 1, 1, 0, 10000.00, 15000.00, NULL, 15100.00, 1153.00, 653.00, 486.33, 403.00, 17950.00, 864.75, 489.75, 364.75, 302.25, 'ferdie', 500000.00, '2025-10-30', 0, 'none', 'Released', '2025-10-29 19:20:54', '2026-02-27 22:30:53'),
(128, 2015, 10, 191, 'Chevrolet', 'Avalanche', 'E', NULL, 'Manual', 'Diesel', 1000, 'nbz123132', 'Blue', 1, 0, 1, 1200000.00, 1205000.00, NULL, 300700.00, 92624.33, 52457.67, 39068.78, 32374.33, 529650.00, 69468.25, 39343.25, 29301.58, 24280.75, 'somewhere', NULL, '2025-11-20', 0, NULL, 'Released', '2025-11-20 06:48:06', '2026-02-27 22:30:53'),
(130, 2017, 1, 302, 'Toyota', 'Wigo', 'G', 'Hatchback', 'Manual', 'Electric', 100000, 'nbz9090', 'Grey', 1, 1, 1, 500000.00, 505000.00, NULL, 132700.00, 38817.67, 21984.33, 16373.22, 13567.67, 228650.00, 29113.25, 16488.25, 12279.92, 10175.75, 'VJ', 753000.00, '2025-01-01', 1, 'this is a note', 'Forfeited', '2025-11-29 06:05:48', '2026-02-27 22:30:53'),
(131, 2025, 1, 303, 'Toyota', 'avanza', 'E', 'Sedan', 'Automatic', 'Diesel', 30000, 'ABC 123', 'RED', 1, 1, 1, 500000.00, 505000.00, NULL, 144820.00, 38817.67, 21984.33, 16373.22, 13567.67, 237740.00, 29113.25, 16488.25, 12279.92, 10175.75, 'Laspinas', 100000.00, '2025-12-03', 1, NULL, 'Available', '2025-12-03 09:53:24', '2026-02-27 22:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_ads`
--

CREATE TABLE `vehicle_ads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `posted_date` date NOT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `social_media_post_link` varchar(255) DEFAULT NULL,
  `ads_boost_link` varchar(255) DEFAULT NULL,
  `campaign_id` varchar(255) DEFAULT NULL,
  `ad_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_ads`
--

INSERT INTO `vehicle_ads` (`id`, `vehicle_id`, `posted_date`, `video_link`, `social_media_post_link`, `ads_boost_link`, `campaign_id`, `ad_id`, `created_at`, `updated_at`) VALUES
(1, 130, '2026-02-04', 'https://google.com', 'https://google.com', 'https://google.com', 'qw', 'qweqwe', '2026-02-04 06:00:16', '2026-02-04 06:00:16');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_custom_fields`
--

CREATE TABLE `vehicle_custom_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','textarea','number','date','email','url','select','checkbox','radio') NOT NULL,
  `field_value` text DEFAULT NULL,
  `field_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`field_options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_custom_fields`
--

INSERT INTO `vehicle_custom_fields` (`id`, `vehicle_id`, `section_name`, `field_name`, `field_label`, `field_type`, `field_value`, `field_options`, `is_required`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(8, 124, 'vehicle_information', 'website link', 'website link', 'url', 'https://web.facebook.com/share/v/1AGHQ2V5e9/', NULL, 0, 1, 1, '2025-11-02 07:46:39', '2025-11-02 07:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_documents`
--

CREATE TABLE `vehicle_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` enum('OR','CR','AR','IDS','PROMISSORY','CHATTEL','REGISTRY_OF_DEEDS','SEC_CERT','DEED_OF_SALE','VOLUNTARY_SURRENDER','SHERRIF_LETTER','DEED_OF_SALE_BANK','CONSENT_FORM') DEFAULT NULL,
  `process_type` enum('ACQUISITION','RESERVATION','RELEASE') NOT NULL DEFAULT 'ACQUISITION',
  `storage_type` enum('file','link','form') NOT NULL DEFAULT 'form',
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_link` text DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_data`)),
  `notes` text DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `checked_by` varchar(255) DEFAULT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_documents`
--

INSERT INTO `vehicle_documents` (`id`, `vehicle_id`, `document_type`, `process_type`, `storage_type`, `file_path`, `file_name`, `file_link`, `form_data`, `notes`, `check_date`, `checked_by`, `is_completed`, `created_at`, `updated_at`) VALUES
(1, 125, 'OR', 'ACQUISITION', 'file', 'vehicles/documents/1763625615_or-and-cr-philippines-60d2fd50a9337jpg', 'or-and-cr-philippines-60d2fd50a9337.jpg', NULL, NULL, NULL, NULL, NULL, 0, '2025-11-20 00:00:15', '2025-11-20 00:00:15'),
(2, 125, 'AR', 'ACQUISITION', 'file', 'vehicles/documents/1763625652_right-absolutepng', 'right_absolute.png', NULL, NULL, NULL, NULL, NULL, 0, '2025-11-20 00:00:52', '2025-11-20 03:26:09'),
(3, 125, 'PROMISSORY', 'ACQUISITION', 'form', NULL, NULL, NULL, '{\"name\":\"10\",\"address\":\"address\",\"_form_structure\":[{\"label\":\"Name\",\"type\":\"text\",\"name\":\"name\",\"value\":\"10\",\"options\":[],\"required\":true,\"placeholder\":\"Name\"},{\"label\":\"Address\",\"type\":\"text\",\"name\":\"address\",\"value\":\"address\",\"options\":[],\"required\":true,\"placeholder\":\"address\"}]}', NULL, NULL, NULL, 0, '2025-11-20 00:13:16', '2025-11-20 03:37:32'),
(4, 125, 'SHERRIF_LETTER', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', 'this is just a note', NULL, NULL, 1, '2025-11-20 03:40:14', '2025-11-20 03:40:28'),
(5, 125, 'IDS', 'RESERVATION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 0, '2025-11-20 03:42:09', '2025-11-20 03:45:20'),
(6, 125, 'DEED_OF_SALE', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 0, '2025-11-20 04:59:14', '2025-11-20 04:59:18'),
(17, 128, 'OR', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 1, '2025-11-28 17:03:47', '2025-11-28 17:03:47'),
(18, 128, 'SHERRIF_LETTER', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 1, '2025-11-28 17:12:06', '2025-11-28 17:12:06'),
(19, 128, 'AR', 'ACQUISITION', 'form', NULL, NULL, NULL, '{\"name\":\"jOHN A\",\"_form_structure\":[{\"name\":\"name\",\"label\":\"name\",\"type\":\"text\",\"value\":\"jOHN A\"}]}', NULL, '2025-11-29', 'Admin User', 0, '2025-11-28 17:12:31', '2025-11-28 17:56:16'),
(20, 128, 'PROMISSORY', 'ACQUISITION', 'form', NULL, NULL, NULL, '{\"name\":\"John\",\"address\":\"Balmaceda\",\"_form_structure\":[{\"name\":\"name\",\"label\":\"Name\",\"type\":\"text\",\"value\":\"John\"},{\"name\":\"address\",\"label\":\"Address\",\"type\":\"text\",\"value\":\"Balmaceda\"}]}', NULL, '2025-11-29', 'Admin User', 0, '2025-11-28 18:14:37', '2025-11-28 18:14:37'),
(22, 130, 'OR', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 1, '2025-12-03 09:48:02', '2025-12-03 09:51:53'),
(24, 131, 'OR', 'ACQUISITION', 'form', NULL, NULL, NULL, '[]', NULL, NULL, NULL, 1, '2025-12-05 03:39:28', '2025-12-05 03:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_document_files`
--

CREATE TABLE `vehicle_document_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_document_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('file','link') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_link` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_expenses`
--

CREATE TABLE `vehicle_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `paint_items` text DEFAULT NULL,
  `paint_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mechanical_electrical_items` text DEFAULT NULL,
  `mechanical_electrical_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cluster_items` text DEFAULT NULL,
  `cluster_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `aircon_items` text DEFAULT NULL,
  `aircon_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `interior_items` text DEFAULT NULL,
  `interior_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `papers_items` text DEFAULT NULL,
  `papers_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tyres_battery_items` text DEFAULT NULL,
  `tyres_battery_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `misc_items` text DEFAULT NULL,
  `misc_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_repair_items` text DEFAULT NULL,
  `total_repair_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `post_reservation_repairs` text DEFAULT NULL,
  `post_reservation_repairs_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_capital_repair_capital_posted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_expenses`
--

INSERT INTO `vehicle_expenses` (`id`, `plate_number`, `paint_items`, `paint_costs`, `mechanical_electrical_items`, `mechanical_electrical_costs`, `cluster_items`, `cluster_costs`, `aircon_items`, `aircon_cost`, `interior_items`, `interior_costs`, `papers_items`, `papers_costs`, `tyres_battery_items`, `tyres_battery_cost`, `misc_items`, `misc_costs`, `total_repair_items`, `total_repair_cost`, `post_reservation_repairs`, `post_reservation_repairs_cost`, `total_capital_repair_capital_posted`, `price`, `created_at`, `updated_at`) VALUES
(1, 'nbz12313', 'adsf', 12.00, 'fasdf', 2123.00, '234', 123.00, '234', 2.00, '234', 3.00, 'wer', 4.00, 'wer', 5.00, NULL, 6.00, 'rew', 7.00, NULL, 8.00, 100.00, 9.00, '2025-10-25 06:33:29', '2025-10-25 06:33:29');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_expense_categories`
--

CREATE TABLE `vehicle_expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_expense_categories`
--

INSERT INTO `vehicle_expense_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Paint', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(2, 'Mechanical / Electrical', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(3, 'Cluster', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(4, 'Aircon', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(5, 'Interior', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(6, 'Paper', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(7, 'Tyers', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(8, 'Battery', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(9, 'Miscellaneous', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(10, 'Repair', '2025-11-01 19:28:51', '2025-11-01 19:28:51'),
(11, 'Post Reservation Repairs', '2025-11-01 19:28:51', '2025-11-01 19:28:51');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_forfeit_details`
--

CREATE TABLE `vehicle_forfeit_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `previous_forfeit_date` date DEFAULT NULL,
  `forfeit_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `forfeit_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_forfeit_details`
--

INSERT INTO `vehicle_forfeit_details` (`id`, `vehicle_id`, `previous_forfeit_date`, `forfeit_amount`, `forfeit_date`, `created_at`, `updated_at`) VALUES
(1, 130, '2026-02-03', 5000.00, '2026-02-25', '2026-02-24 08:02:32', '2026-02-24 08:02:32');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_images`
--

CREATE TABLE `vehicle_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_images`
--

INSERT INTO `vehicle_images` (`id`, `vehicle_id`, `image_path`, `original_name`, `mime_type`, `file_size`, `is_primary`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 124, 'vehicles/images/c66d96ac-180e-46c8-98b9-d54a2010b56d.webp', '20230621_01_30_s.webp', 'image/webp', 82468, 1, 0, '2025-10-25 06:19:02', '2025-10-25 06:19:02'),
(2, 124, 'vehicles/images/85cdae02-c9c2-4d91-a593-66d1dc850b9c.jpg', 'images (2).jpg', 'image/jpeg', 9013, 0, 1, '2025-10-25 06:19:11', '2025-10-25 06:19:11'),
(3, 124, 'vehicles/images/2f82a148-d324-445f-b94c-039ac9690234.jpg', 'images (1).jpg', 'image/jpeg', 13159, 0, 2, '2025-10-25 06:19:11', '2025-10-25 06:19:11'),
(6, 24, 'vehicles/images/24d67bd0-be84-4dd9-9aa8-5f0336d0cdea.jpg', 'images (3).jpg', 'image/jpeg', 9643, 1, 0, '2025-10-25 07:41:22', '2025-10-25 07:41:22'),
(11, 128, 'vehicles/images/b438485d-8f3e-4ab2-8be5-8d54866c99c0.png', 'login-banner-1_v2.png', 'image/png', 186279, 1, 0, '2025-11-29 05:35:35', '2025-11-29 05:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_incentives`
--

CREATE TABLE `vehicle_incentives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vehicle_id` bigint(20) UNSIGNED NOT NULL,
  `sa_origin` varchar(255) DEFAULT NULL,
  `sa_origin_link` varchar(255) DEFAULT NULL,
  `sa_origin_file_path` varchar(255) DEFAULT NULL,
  `reserved_by` varchar(255) DEFAULT NULL,
  `no_look` tinyint(1) NOT NULL DEFAULT 0,
  `no_look_link` varchar(255) DEFAULT NULL,
  `no_look_file_path` varchar(255) DEFAULT NULL,
  `insurance` tinyint(1) NOT NULL DEFAULT 0,
  `insurance_link` varchar(255) DEFAULT NULL,
  `insurance_file_path` varchar(255) DEFAULT NULL,
  `testimonial` tinyint(1) NOT NULL DEFAULT 0,
  `testimonial_link` varchar(255) DEFAULT NULL,
  `testimonial_file_path` varchar(255) DEFAULT NULL,
  `review` tinyint(1) NOT NULL DEFAULT 0,
  `review_link` varchar(255) DEFAULT NULL,
  `review_file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_status_details`
--

CREATE TABLE `vehicle_status_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plate_number` varchar(255) NOT NULL,
  `showroom` varchar(255) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `sales_price` decimal(12,2) DEFAULT NULL,
  `sale_reservation_amount` decimal(12,2) DEFAULT NULL,
  `sales_person_reserved` varchar(255) DEFAULT NULL,
  `sales_person_release` varchar(255) DEFAULT NULL,
  `good_sales_review` tinyint(1) DEFAULT NULL,
  `cash_financing` enum('Cash','Financing') DEFAULT NULL,
  `sale_origin` varchar(255) DEFAULT NULL,
  `agent_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `finance_revenue_1` decimal(12,2) DEFAULT NULL,
  `finance_revenue_2` decimal(12,2) DEFAULT NULL,
  `sale_status` enum('Available','Under Maintenance','Reserved','Released','Forfeited','Pending Customer Information Details') NOT NULL DEFAULT 'Available',
  `transfer_cost` decimal(12,2) DEFAULT NULL,
  `has_insurance` tinyint(1) DEFAULT NULL,
  `insurance_value` decimal(12,2) DEFAULT NULL,
  `has_trade_in` tinyint(1) DEFAULT NULL,
  `trade_in_value` decimal(12,2) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `days_from_reservation_to_release` int(11) DEFAULT NULL,
  `days_from_acquisition_to_reservation` int(11) DEFAULT NULL,
  `customer_first_name` varchar(255) DEFAULT NULL,
  `customer_last_name` varchar(255) DEFAULT NULL,
  `customer_middle_name` varchar(255) DEFAULT NULL,
  `customer_contact_number` varchar(255) DEFAULT NULL,
  `customer_date_of_birth` date DEFAULT NULL,
  `customer_gender` enum('Male','Female','Other') DEFAULT NULL,
  `customer_location` varchar(255) DEFAULT NULL,
  `customer_purpose` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_status_details`
--

INSERT INTO `vehicle_status_details` (`id`, `plate_number`, `showroom`, `sale_date`, `sales_price`, `sale_reservation_amount`, `sales_person_reserved`, `sales_person_release`, `good_sales_review`, `cash_financing`, `sale_origin`, `agent_cost`, `finance_revenue_1`, `finance_revenue_2`, `sale_status`, `transfer_cost`, `has_insurance`, `insurance_value`, `has_trade_in`, `trade_in_value`, `release_date`, `days_from_reservation_to_release`, `days_from_acquisition_to_reservation`, `customer_first_name`, `customer_last_name`, `customer_middle_name`, `customer_contact_number`, `customer_date_of_birth`, `customer_gender`, `customer_location`, `customer_purpose`, `created_at`, `updated_at`) VALUES
(6, 'nbz12313', 'FLAGSHIP', '2025-11-08', 4500000.00, 50000.00, 'JOHN', 'JOHN', 1, 'Financing', 'NA', 2000.00, 0.00, 0.00, 'Reserved', 6000.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-03 04:35:09', '2025-11-07 20:39:26'),
(11, 'adfa44', 'FLAGSHIP', '2025-11-01', 10000.00, 100.00, 'john', 'john', 1, 'Cash', 'adfad', 1000.00, 22.00, 12.00, 'Released', 2342.00, NULL, NULL, NULL, NULL, '2025-11-20', 19, NULL, 'jamaica', 'balmaceda', 'V', '090909', '2025-11-20', 'Female', 'antipolo', 'wala lang', '2025-11-08 09:01:08', '2025-11-20 05:07:11'),
(13, 'nbz123132', NULL, NULL, NULL, NULL, NULL, 'johnny', 1, NULL, NULL, 0.00, NULL, NULL, 'Released', NULL, NULL, NULL, NULL, NULL, '2025-11-29', NULL, NULL, 'aaa', 'ccc', 'bbb', '23423', '1989-01-10', 'Male', 'adfad, adsf', NULL, '2025-11-20 06:48:06', '2025-11-29 05:36:07'),
(15, '12313A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-29 05:46:06', '2025-11-29 05:46:06'),
(17, 'nbz9090', 'FLAGSHIP', '2025-11-30', 753000.00, 50000.00, 'bj', 'jj', 1, 'Cash', '7676', 0.00, 555.00, 66.00, 'Forfeited', 12.00, NULL, NULL, NULL, NULL, '2025-11-30', 0, NULL, 'john', 'b', 'bb', '56565', '2000-09-09', 'Female', 'fhgffdgdgfd', 'vcvcvc', '2025-11-29 16:56:59', '2026-02-27 20:01:58'),
(18, 'ABC 123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-03 09:53:35', '2025-12-03 09:53:35'),
(19, 'NOP-2564', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL, 'Available', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-27 22:23:34', '2026-02-27 22:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `video_posting_records`
--

CREATE TABLE `video_posting_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `record_date` date NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Video',
  `platform` varchar(255) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `vehicle_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Posted',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `video_posting_records`
--

INSERT INTO `video_posting_records` (`id`, `title`, `record_date`, `type`, `platform`, `link_url`, `vehicle_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'facebook', '2026-02-27', 'Video', 'adfadfadf', NULL, 130, 'Posted', 'test', '2026-02-27 10:11:36', '2026-02-27 10:11:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `agent_bolo_agents`
--
ALTER TABLE `agent_bolo_agents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `buffing_records`
--
ALTER TABLE `buffing_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buffing_records_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `buffing_records_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `car_financing_settings`
--
ALTER TABLE `car_financing_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `car_fin_scheme_year_unique` (`financing_scheme_id`,`year_model_range`);

--
-- Indexes for table `cash_additions`
--
ALTER TABLE `cash_additions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_additions_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `client_follow_up_list`
--
ALTER TABLE `client_follow_up_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_follow_up_list_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `company_documents`
--
ALTER TABLE `company_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_documents_agent_bolo_agent_id_foreign` (`agent_bolo_agent_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contracts_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `contracts_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `custom_sections`
--
ALTER TABLE `custom_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_sections_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `custom_section_fields`
--
ALTER TABLE `custom_section_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_section_fields_custom_section_id_foreign` (`custom_section_id`);

--
-- Indexes for table `daily_budgets`
--
ALTER TABLE `daily_budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `daily_budgets_payment_method_id_budget_date_unique` (`payment_method_id`,`budget_date`);

--
-- Indexes for table `document_form_templates`
--
ALTER TABLE `document_form_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_form_templates_document_type_is_active_index` (`document_type`,`is_active`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_items_expense_transaction_id_foreign` (`expense_transaction_id`),
  ADD KEY `expense_items_vehicle_id_foreign` (`vehicle_id`),
  ADD KEY `expense_items_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `expense_item_receipts`
--
ALTER TABLE `expense_item_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_item_receipts_expense_item_id_foreign` (`expense_item_id`);

--
-- Indexes for table `expense_transactions`
--
ALTER TABLE `expense_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financing_schemes`
--
ALTER TABLE `financing_schemes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follow_up_documents`
--
ALTER TABLE `follow_up_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follow_up_documents_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `gas_expenses`
--
ALTER TABLE `gas_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gas_expenses_plate_number_index` (`plate_number`),
  ADD KEY `gas_expenses_date_index` (`date`);

--
-- Indexes for table `insurance_tracker`
--
ALTER TABLE `insurance_tracker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insurance_tracker_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `makes`
--
ALTER TABLE `makes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `makes_name_unique` (`name`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `models`
--
ALTER TABLE `models`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `models_make_id_name_unique` (`make_id`,`name`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recommendation_trackers`
--
ALTER TABLE `recommendation_trackers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recommendation_trackers_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `recommendation_tracker_images`
--
ALTER TABLE `recommendation_tracker_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recommendation_tracker_images_recommendation_tracker_id_foreign` (`recommendation_tracker_id`);

--
-- Indexes for table `sales_agents`
--
ALTER TABLE `sales_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_agents_email_unique` (`email`),
  ADD UNIQUE KEY `sales_agents_employee_id_unique` (`sales_agent_id`);

--
-- Indexes for table `sales_agent_commissions`
--
ALTER TABLE `sales_agent_commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_agent_commissions_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `showrooms`
--
ALTER TABLE `showrooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `showrooms_name_unique` (`name`);

--
-- Indexes for table `source_screenshots`
--
ALTER TABLE `source_screenshots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spot_cash_details`
--
ALTER TABLE `spot_cash_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spot_cash_details_vehicle_id_index` (`vehicle_id`);

--
-- Indexes for table `tools_inventory`
--
ALTER TABLE `tools_inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfer_orcr`
--
ALTER TABLE `transfer_orcr`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_orcr_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicles_plate_number_unique` (`plate_number`),
  ADD KEY `vehicles_make_id_foreign` (`make_id`),
  ADD KEY `vehicles_model_id_foreign` (`model_id`);

--
-- Indexes for table `vehicle_ads`
--
ALTER TABLE `vehicle_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_ads_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `vehicle_custom_fields`
--
ALTER TABLE `vehicle_custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_custom_fields_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vehicle_document` (`vehicle_id`,`document_type`,`process_type`);

--
-- Indexes for table `vehicle_document_files`
--
ALTER TABLE `vehicle_document_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_document_files_vehicle_document_id_type_index` (`vehicle_document_id`,`type`);

--
-- Indexes for table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_expenses_plate_number_index` (`plate_number`);

--
-- Indexes for table `vehicle_expense_categories`
--
ALTER TABLE `vehicle_expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_expense_categories_name_unique` (`name`);

--
-- Indexes for table `vehicle_forfeit_details`
--
ALTER TABLE `vehicle_forfeit_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_forfeit_details_vehicle_id_foreign` (`vehicle_id`);

--
-- Indexes for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_images_vehicle_id_is_primary_index` (`vehicle_id`,`is_primary`),
  ADD KEY `vehicle_images_vehicle_id_sort_order_index` (`vehicle_id`,`sort_order`);

--
-- Indexes for table `vehicle_incentives`
--
ALTER TABLE `vehicle_incentives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_incentives_vehicle_id_unique` (`vehicle_id`);

--
-- Indexes for table `vehicle_status_details`
--
ALTER TABLE `vehicle_status_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_status_details_plate_number_index` (`plate_number`),
  ADD KEY `vehicle_status_details_sale_status_index` (`sale_status`);

--
-- Indexes for table `video_posting_records`
--
ALTER TABLE `video_posting_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `video_posting_records_vehicle_id_foreign` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `agent_bolo_agents`
--
ALTER TABLE `agent_bolo_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buffing_records`
--
ALTER TABLE `buffing_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `car_financing_settings`
--
ALTER TABLE `car_financing_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cash_additions`
--
ALTER TABLE `cash_additions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `client_follow_up_list`
--
ALTER TABLE `client_follow_up_list`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_documents`
--
ALTER TABLE `company_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_sections`
--
ALTER TABLE `custom_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_section_fields`
--
ALTER TABLE `custom_section_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `daily_budgets`
--
ALTER TABLE `daily_budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_form_templates`
--
ALTER TABLE `document_form_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `expense_items`
--
ALTER TABLE `expense_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;

--
-- AUTO_INCREMENT for table `expense_item_receipts`
--
ALTER TABLE `expense_item_receipts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expense_transactions`
--
ALTER TABLE `expense_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `financing_schemes`
--
ALTER TABLE `financing_schemes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `follow_up_documents`
--
ALTER TABLE `follow_up_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gas_expenses`
--
ALTER TABLE `gas_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `insurance_tracker`
--
ALTER TABLE `insurance_tracker`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `makes`
--
ALTER TABLE `makes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `models`
--
ALTER TABLE `models`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=304;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recommendation_trackers`
--
ALTER TABLE `recommendation_trackers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recommendation_tracker_images`
--
ALTER TABLE `recommendation_tracker_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_agents`
--
ALTER TABLE `sales_agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sales_agent_commissions`
--
ALTER TABLE `sales_agent_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `showrooms`
--
ALTER TABLE `showrooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `source_screenshots`
--
ALTER TABLE `source_screenshots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spot_cash_details`
--
ALTER TABLE `spot_cash_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tools_inventory`
--
ALTER TABLE `tools_inventory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transfer_orcr`
--
ALTER TABLE `transfer_orcr`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `vehicle_ads`
--
ALTER TABLE `vehicle_ads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicle_custom_fields`
--
ALTER TABLE `vehicle_custom_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vehicle_document_files`
--
ALTER TABLE `vehicle_document_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicle_expense_categories`
--
ALTER TABLE `vehicle_expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle_forfeit_details`
--
ALTER TABLE `vehicle_forfeit_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle_incentives`
--
ALTER TABLE `vehicle_incentives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_status_details`
--
ALTER TABLE `vehicle_status_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `video_posting_records`
--
ALTER TABLE `video_posting_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `buffing_records`
--
ALTER TABLE `buffing_records`
  ADD CONSTRAINT `buffing_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `buffing_records_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `car_financing_settings`
--
ALTER TABLE `car_financing_settings`
  ADD CONSTRAINT `car_financing_settings_financing_scheme_id_foreign` FOREIGN KEY (`financing_scheme_id`) REFERENCES `financing_schemes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_additions`
--
ALTER TABLE `cash_additions`
  ADD CONSTRAINT `cash_additions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_follow_up_list`
--
ALTER TABLE `client_follow_up_list`
  ADD CONSTRAINT `client_follow_up_list_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `company_documents`
--
ALTER TABLE `company_documents`
  ADD CONSTRAINT `company_documents_agent_bolo_agent_id_foreign` FOREIGN KEY (`agent_bolo_agent_id`) REFERENCES `agent_bolo_agents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contracts_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `custom_sections`
--
ALTER TABLE `custom_sections`
  ADD CONSTRAINT `custom_sections_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_section_fields`
--
ALTER TABLE `custom_section_fields`
  ADD CONSTRAINT `custom_section_fields_custom_section_id_foreign` FOREIGN KEY (`custom_section_id`) REFERENCES `custom_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_budgets`
--
ALTER TABLE `daily_budgets`
  ADD CONSTRAINT `daily_budgets_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD CONSTRAINT `expense_items_expense_transaction_id_foreign` FOREIGN KEY (`expense_transaction_id`) REFERENCES `expense_transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_items_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expense_items_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_item_receipts`
--
ALTER TABLE `expense_item_receipts`
  ADD CONSTRAINT `expense_item_receipts_expense_item_id_foreign` FOREIGN KEY (`expense_item_id`) REFERENCES `expense_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `follow_up_documents`
--
ALTER TABLE `follow_up_documents`
  ADD CONSTRAINT `follow_up_documents_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `gas_expenses`
--
ALTER TABLE `gas_expenses`
  ADD CONSTRAINT `gas_expenses_plate_number_foreign` FOREIGN KEY (`plate_number`) REFERENCES `vehicles` (`plate_number`);

--
-- Constraints for table `insurance_tracker`
--
ALTER TABLE `insurance_tracker`
  ADD CONSTRAINT `insurance_tracker_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `models`
--
ALTER TABLE `models`
  ADD CONSTRAINT `models_make_id_foreign` FOREIGN KEY (`make_id`) REFERENCES `makes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recommendation_trackers`
--
ALTER TABLE `recommendation_trackers`
  ADD CONSTRAINT `recommendation_trackers_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `recommendation_tracker_images`
--
ALTER TABLE `recommendation_tracker_images`
  ADD CONSTRAINT `recommendation_tracker_images_recommendation_tracker_id_foreign` FOREIGN KEY (`recommendation_tracker_id`) REFERENCES `recommendation_trackers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_agent_commissions`
--
ALTER TABLE `sales_agent_commissions`
  ADD CONSTRAINT `sales_agent_commissions_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `spot_cash_details`
--
ALTER TABLE `spot_cash_details`
  ADD CONSTRAINT `spot_cash_details_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transfer_orcr`
--
ALTER TABLE `transfer_orcr`
  ADD CONSTRAINT `transfer_orcr_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_make_id_foreign` FOREIGN KEY (`make_id`) REFERENCES `makes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_model_id_foreign` FOREIGN KEY (`model_id`) REFERENCES `models` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_ads`
--
ALTER TABLE `vehicle_ads`
  ADD CONSTRAINT `vehicle_ads_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_custom_fields`
--
ALTER TABLE `vehicle_custom_fields`
  ADD CONSTRAINT `vehicle_custom_fields_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_documents`
--
ALTER TABLE `vehicle_documents`
  ADD CONSTRAINT `vehicle_documents_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_document_files`
--
ALTER TABLE `vehicle_document_files`
  ADD CONSTRAINT `vehicle_document_files_vehicle_document_id_foreign` FOREIGN KEY (`vehicle_document_id`) REFERENCES `vehicle_documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  ADD CONSTRAINT `vehicle_expenses_plate_number_foreign` FOREIGN KEY (`plate_number`) REFERENCES `vehicles` (`plate_number`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_forfeit_details`
--
ALTER TABLE `vehicle_forfeit_details`
  ADD CONSTRAINT `vehicle_forfeit_details_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_images`
--
ALTER TABLE `vehicle_images`
  ADD CONSTRAINT `vehicle_images_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_incentives`
--
ALTER TABLE `vehicle_incentives`
  ADD CONSTRAINT `vehicle_incentives_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_status_details`
--
ALTER TABLE `vehicle_status_details`
  ADD CONSTRAINT `vehicle_status_details_plate_number_foreign` FOREIGN KEY (`plate_number`) REFERENCES `vehicles` (`plate_number`) ON DELETE CASCADE;

--
-- Constraints for table `video_posting_records`
--
ALTER TABLE `video_posting_records`
  ADD CONSTRAINT `video_posting_records_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
