-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2025 at 07:56 PM
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
-- Database: `raw_backend`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `user_id` int(9) NOT NULL,
  `recent_punch_type` enum('check_in','check_out','break_in','break_out','ot_in','ot_out') DEFAULT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `break_in` time DEFAULT NULL,
  `break_out` time DEFAULT NULL,
  `ot_in` time DEFAULT NULL,
  `ot_out` time DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `date`, `user_id`, `recent_punch_type`, `check_in`, `check_out`, `break_in`, `break_out`, `ot_in`, `ot_out`, `updated_at`) VALUES
(1, '2025-04-14', 1001, 'ot_out', '08:55:00', '17:05:00', '12:00:00', '12:55:00', '17:30:00', '19:00:00', '2025-04-13 16:20:30'),
(2, '2025-04-14', 1002, 'check_out', '09:02:00', '17:00:00', '12:05:00', '13:00:00', NULL, NULL, '2025-04-13 16:28:30'),
(3, '2025-04-14', 1003, 'check_out', '09:45:00', '17:10:00', NULL, NULL, NULL, NULL, '2025-04-13 16:27:30'),
(4, '2025-04-13', 1004, 'check_out', '08:50:00', '15:30:00', '12:00:00', '12:30:00', NULL, NULL, '2025-04-13 16:24:30'),
(5, '2025-04-13', 1005, 'ot_out', '09:00:00', '17:00:00', '12:00:00', '13:00:00', '18:00:00', '20:30:00', '2025-04-13 16:19:30');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `name`) VALUES
(1, 'rcgi'),
(2, 'terraco');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `port` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `sn` varchar(50) NOT NULL,
  `model` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_attendance_logs`
--

CREATE TABLE `device_attendance_logs` (
  `att_logs_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `device_sn` int(11) DEFAULT NULL,
  `state` enum('fingerprint','rfid','password') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `start_time`, `end_time`) VALUES
(1, '08:00:00', '17:00:00'),
(2, '08:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `company_id` int(11) DEFAULT 1,
  `join_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `shift_id`, `last_name`, `first_name`, `username`, `password`, `role`, `company_id`, `join_date`, `created_at`) VALUES
(1001, 1, 'Lopez', 'Norsh Daniel Francois', 'norsh', '$2y$10$V8f4p38BUOIt.2RP6LzDf.AQW414dYac8aujMJo0sJC3nY0QQVBGC', 'admin', 1, NULL, '2025-04-13 13:07:34'),
(1002, 2, 'Lopez', 'Gabriel', 'gab', '$2y$10$V8f4p38BUOIt.2RP6LzDf.AQW414dYac8aujMJo0sJC3nY0QQVBGC', 'user', 2, NULL, '2025-04-13 13:17:05'),
(1003, 1, 'Salazar', 'Nora', 'nora', '$2y$10$V8f4p38BUOIt.2RP6LzDf.AQW414dYac8aujMJo0sJC3nY0QQVBGC', 'user', 1, NULL, '2025-04-13 14:50:13'),
(1004, 2, 'Paez', 'Kyla', 'andeng', '$2y$10$V8f4p38BUOIt.2RP6LzDf.AQW414dYac8aujMJo0sJC3nY0QQVBGC', 'admin', 1, NULL, '2025-04-13 14:50:13'),
(1005, 2, 'Miranda', 'Cyrus', 'cyrus', '$2y$10$V8f4p38BUOIt.2RP6LzDf.AQW414dYac8aujMJo0sJC3nY0QQVBGC', 'user', 1, '2025-04-02 16:00:00', '2025-04-13 15:08:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_id`);

--
-- Indexes for table `device_attendance_logs`
--
ALTER TABLE `device_attendance_logs`
  ADD PRIMARY KEY (`att_logs_id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
