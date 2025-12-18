-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 10:39 AM
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
-- Database: `gitam_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_log`
--

CREATE TABLE `attendance_log` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_log`
--

INSERT INTO `attendance_log` (`id`, `subject_id`, `date`, `status`) VALUES
(1, 2, '2025-12-17', 'Present'),
(2, 2, '2025-12-18', 'Present'),
(3, 1, '2025-12-19', 'Present'),
(4, 2, '2025-12-19', 'Absent'),
(5, 1, '2025-12-22', 'Present'),
(6, 1, '2025-12-23', 'Absent'),
(7, 2, '2025-12-24', 'Absent'),
(8, 2, '2025-12-25', 'Absent'),
(9, 1, '2025-12-26', 'Absent'),
(10, 2, '2025-12-26', 'Absent'),
(11, 1, '2025-12-29', 'Absent'),
(16, 4, '2025-11-11', 'Present'),
(17, 4, '2025-10-01', 'Present'),
(19, 5, '2025-10-01', 'Present'),
(20, 6, '2025-10-01', 'Present'),
(27, 14, '2025-12-02', 'Absent'),
(28, 11, '2025-11-05', 'Present'),
(29, 12, '2025-11-05', 'Present'),
(30, 13, '2025-11-05', 'Present'),
(31, 14, '2025-11-05', 'Present'),
(32, 11, '2025-11-13', 'Present'),
(33, 12, '2025-11-13', 'Present'),
(34, 13, '2025-11-13', 'Present'),
(35, 14, '2025-11-13', 'Present'),
(38, 12, '2025-11-17', 'Absent'),
(39, 13, '2025-11-17', 'Absent'),
(40, 11, '2025-11-17', 'Absent'),
(41, 14, '2025-11-17', 'Absent'),
(42, 3, '2025-12-01', 'Present'),
(43, 3, '2025-12-03', 'Absent'),
(45, 3, '2025-12-18', 'Present'),
(46, 4, '2025-12-18', 'Absent'),
(47, 5, '2025-12-18', 'Absent'),
(48, 6, '2025-12-18', 'Absent'),
(49, 15, '2025-12-09', 'Present'),
(50, 15, '2025-12-10', 'Present'),
(51, 15, '2025-12-16', 'Absent'),
(52, 15, '2025-12-17', 'Absent'),
(53, 15, '2025-12-23', 'Absent'),
(54, 15, '2025-12-24', 'Absent'),
(55, 17, '2025-08-04', 'Absent'),
(57, 17, '2025-08-06', 'Absent'),
(58, 18, '2025-08-05', 'Present'),
(59, 18, '2025-08-07', 'Present'),
(60, 17, '2025-08-08', 'Absent'),
(61, 18, '2025-08-08', 'Present'),
(62, 17, '2025-08-11', 'Absent'),
(63, 18, '2025-12-18', 'Present'),
(64, 17, '2025-12-19', 'Absent'),
(65, 18, '2025-12-19', 'Absent');

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `semester_id`, `holiday_date`, `name`) VALUES
(1, 3, '2025-11-05', 'Fest');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `semester_id`, `day_name`, `subject_id`) VALUES
(1, 2, 'Monday', 1),
(3, 2, 'Tuesday', 1),
(4, 2, 'Wednesday', 2),
(5, 2, 'Thursday', 2),
(6, 2, 'Friday', 1),
(7, 2, 'Friday', 2),
(8, 3, 'Monday', 3),
(9, 3, 'Monday', 4),
(10, 3, 'Monday', 5),
(11, 3, 'Monday', 6),
(13, 3, 'Tuesday', 4),
(14, 3, 'Wednesday', 3),
(15, 3, 'Wednesday', 4),
(16, 3, 'Wednesday', 5),
(17, 3, 'Wednesday', 6),
(18, 3, 'Thursday', 3),
(19, 3, 'Thursday', 4),
(20, 3, 'Thursday', 5),
(21, 3, 'Thursday', 6),
(22, 3, 'Friday', 3),
(23, 3, 'Friday', 5),
(24, 3, 'Friday', 6),
(25, 5, 'Monday', 11),
(26, 5, 'Monday', 12),
(27, 5, 'Monday', 13),
(28, 5, 'Monday', 14),
(29, 5, 'Tuesday', 14),
(30, 5, 'Wednesday', 11),
(31, 5, 'Wednesday', 12),
(32, 5, 'Wednesday', 13),
(33, 5, 'Wednesday', 14),
(34, 5, 'Thursday', 11),
(35, 5, 'Thursday', 12),
(36, 5, 'Thursday', 13),
(37, 5, 'Thursday', 14),
(42, 5, 'Friday', 11),
(43, 5, 'Friday', 12),
(44, 5, 'Friday', 13),
(45, 6, 'Tuesday', 15),
(46, 6, 'Wednesday', 15),
(47, 7, 'Monday', 17),
(48, 7, 'Tuesday', 18),
(50, 7, 'Thursday', 18),
(51, 7, 'Wednesday', 17),
(52, 7, 'Friday', 17),
(53, 7, 'Friday', 18);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `sessional1_date` date DEFAULT NULL,
  `sessional2_date` date DEFAULT NULL,
  `target_percentage` int(11) DEFAULT 75
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `user_id`, `name`, `start_date`, `end_date`, `sessional1_date`, `sessional2_date`, `target_percentage`) VALUES
(2, 1, 'SEM 6', '2025-12-01', '2025-12-31', '2026-01-09', NULL, 50),
(3, 1, 'SEM test', '2025-08-04', '2025-12-03', '2025-10-06', '2025-12-04', 75),
(5, 2, 'Sem 5', '2025-08-04', '2025-12-09', '2025-10-06', '2025-12-04', 75),
(6, 4, 'sem', '2025-12-01', '2025-12-31', '2025-12-16', '2025-12-30', 90),
(7, 1, 'sem 5', '2025-08-04', '2025-08-22', '2025-08-13', '2025-08-21', 75);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `total_classes` int(11) DEFAULT 0,
  `target_percentage` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `semester_id`, `name`, `total_classes`, `target_percentage`) VALUES
(1, 2, 'DBMS', 30, NULL),
(2, 2, 'Java', 30, NULL),
(3, 3, 'DM', 80, 50),
(4, 3, 'PHP', 80, 70),
(5, 3, 'OOAD', 80, 40),
(6, 3, 'STAT', 80, 60),
(11, 5, 'DM', 80, 60),
(12, 5, 'STAT', 80, 60),
(13, 5, 'OOAD', 80, 75),
(14, 5, 'PHP', 80, 50),
(15, 6, 'dbms', 80, NULL),
(16, 6, 'dm', 80, NULL),
(17, 7, 'dm', 80, NULL),
(18, 7, 'ooad', 80, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_alert_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `student_id`, `full_name`, `password`, `email`, `last_alert_date`) VALUES
(1, '2023005717', 'Juhi Lunia', '$2y$10$VG33FTrrB4QyTVS78nVlxOhTBDBYeajfghwX8NbsqCfXceRISZ5y2', 'jlunia2@gitam.in', NULL),
(2, '2023002040', 'Veronica Roshini', '$2y$10$dSRl3EnVhZpm2IP5AT0HGuEWohLYmdr.W5E.LaAcpph1JLkH7.QyK', '', NULL),
(3, '2023000', '   ', '$2y$10$Jn89MrINzFHykkuQiVVNi.kmidbU1Bq6EBKtE/RBnK.oUfykDsFMO', '', NULL),
(4, '2023001937', 'Akarsh', '$2y$10$wIND27GZLFrb5Lg8ycZ3R.nlNLI6nn/oeWp5YfpEocnNPQQgUiQuC', 'jboppudi2@gitam.in', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_log`
--
ALTER TABLE `attendance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_log`
--
ALTER TABLE `attendance_log`
  ADD CONSTRAINT `attendance_log_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `holidays_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
