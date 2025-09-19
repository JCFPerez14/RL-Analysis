-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2025 at 03:42 PM
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
-- Database: `nulip_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `backup`
--

CREATE TABLE `backup` (
  `id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `academic_term` varchar(50) NOT NULL,
  `applying_for` varchar(50) NOT NULL,
  `strand` varchar(100) DEFAULT NULL,
  `program` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Enrolled','Not Enrolled') NOT NULL DEFAULT 'Not Enrolled',
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `password` varchar(255) NOT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `family_income` varchar(50) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `second_program` varchar(100) DEFAULT NULL,
  `birthplace` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `current_address` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `backup`
--

INSERT INTO `backup` (`id`, `firstname`, `middlename`, `lastname`, `email`, `mobile`, `nationality`, `academic_year`, `academic_term`, `applying_for`, `strand`, `program`, `created_at`, `status`, `role`, `password`, `sex`, `family_income`, `father_occupation`, `mother_occupation`, `second_program`, `birthplace`, `city`, `province`, `current_address`) VALUES
(1, 'seth', 'recomono', 'dimaano', 'dimaanosr@students.nu-lipa.edu.ph', '09182153066', 'Filipino', '2025-2026', '1st Semester', 'Freshman', 'STEM', 'BSIT', '2025-09-16 10:38:08', 'Not Enrolled', 'student', '', 'Female', NULL, NULL, NULL, 'BSCS', NULL, NULL, NULL, NULL),
(3, 'Adbul', 'Dexter', 'Chupapo', 'er@gmail.com', '353535325', 'Filipino', '2025-2026', '2nd Semester', 'Cross Enrollee', 'STEM', 'BSIT', '2025-09-16 11:47:21', 'Not Enrolled', 'student', '$2y$10$nFzBiX0w3hB9qjd8NpoDOemdOZS3LQTN3osem/PURAyE5Ik5gL6Za', 'Male', NULL, NULL, NULL, 'BSA - Financial Management', NULL, NULL, NULL, NULL),
(5, 'Merry', 'M.', 'Chico', 'admin12@nulipa.com', '+63922776597', 'Filipino', '2025-2026', '1st Semester', 'Freshman', 'Stem', 'BSCE', '2025-09-17 07:28:57', 'Not Enrolled', 'admin', '$2y$10$BEupt5ypZPNCHxn.2hk2UuvgBVcHJH8eA5o6fjJB6MlNVG8htEcZO', 'Male', NULL, NULL, NULL, 'BSPYS', NULL, NULL, NULL, NULL),
(6, 'se', 'er', 'ad', 'er2@gmail.com', '12321414', 'Filipino', '2025-2026', '2nd Semester', 'Transferee', 'STEM', 'BSCS', '2025-09-17 08:12:02', 'Enrolled', 'student', '$2y$10$C0JCyWOkfS9GiMQhJWgAWumBYWf2Llm6tgKCSeC5ca5.sNq7a4kde', 'Male', NULL, NULL, NULL, 'BSA - Marketing', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `units` int(11) NOT NULL DEFAULT 3,
  `program` varchar(100) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `prerequisites` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `units`, `program`, `semester`, `prerequisites`, `created_at`, `updated_at`) VALUES
(1, 'BSCS', 'Introduction to Computer Science', 'Basic concepts of computer science and programming.', 3, 'BSCS', 'First Semester', NULL, '2025-09-17 08:42:43', '2025-09-17 08:42:43'),
(2, 'BSIT', 'Networking Fundamentals', 'Introduction to network architecture, protocols, and devices.', 3, 'BSIT', 'Second Semester', 'CS101', '2025-09-17 08:42:43', '2025-09-17 08:42:43'),
(3, 'CE301', 'Structural Analysis', 'Study of forces and their effects on structures.', 4, 'BSCE', 'First Semester', 'CE201', '2025-09-17 08:42:43', '2025-09-17 08:42:43'),
(4, 'ARCH105', 'Architectural Design Basics', 'Fundamental principles of architectural design.', 3, 'BSArch', 'First Semester', NULL, '2025-09-17 08:42:43', '2025-09-17 08:42:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `status` varchar(50) DEFAULT 'Not Enroll',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(3, 'gagoerer@gmail.com', '$2y$10$rGG56y26Sd4pFfdv0R8OmuDAfry67nH428P8sWFn0uGu0sa9PcPkm', 'admin', 'Enrolled', '2025-09-17 09:22:28'),
(4, 'perezjf1@students.nu-lipa.edu.ph', '$2y$10$y.6LCwj8RptGjk/hg50zP.uGSxy5oVCjNZ85taGWF8qSAH5jzwU16', 'student', 'Enrolled', '2025-09-17 10:13:42'),
(5, 'dimaanosr@students.nu-lipa.edu.ph', '$2y$10$H9.ijw85VKO7QS2hy6L1fOGdQFcz9BIUl4.TcN5v2/U/.TDvwZPQC', 'admin', 'Enrolled', '2025-09-17 10:17:57'),
(12, 'ad@gmail.com', '$2y$10$pEJk3E54JcLPOtsWLGSa1ugPmxFpwjFQ1qjIa.GSOvVM6RDqhGnt6', 'admin', 'Not Enrolled', '2025-09-19 03:35:32'),
(13, '12@gmail.com', '$2y$10$s5K11mojTKluLu7Lrd4rLOfY5oHdfujW9z0rUd9CGZYyYP8kVglya', 'student', 'Not Enrolled', '2025-09-19 10:12:03'),
(14, 'req@gmail.com', '$2y$10$Tjslh8pXJhJ2uJDEOPtRkO5O6oYQhCgFEfsgoHaQdNNAm0DBn6FDO', 'student', 'Not Enrolled', '2025-09-19 10:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `academic_year` varchar(50) DEFAULT NULL,
  `academic_term` varchar(50) DEFAULT NULL,
  `applying_for` varchar(50) DEFAULT NULL,
  `strand` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `second_program` varchar(255) DEFAULT NULL,
  `family_income` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `likelihood` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `firstname`, `middlename`, `lastname`, `mobile`, `nationality`, `sex`, `academic_year`, `academic_term`, `applying_for`, `strand`, `program`, `second_program`, `family_income`, `father_occupation`, `mother_occupation`, `birthplace`, `city`, `province`, `current_address`, `photo`, `likelihood`) VALUES
(1, 3, 'ago', 'afaef', 'aefd', '0495043954', 'Filipino', 'Female', '2025-2026', '1st Semester', 'Freshman', 'STEM', 'BSIT', 'BSCS', '', '', '', '', '', '', 'Tambo, Lipa City', '', 100),
(2, 4, '', '', '', '', 'Filipino', 'Female', '2025-2026', '1st Semester', 'Freshman', 'STEM', '', '', '', '', '', '', '', '', 'Batangas City, Batangas', '', 80),
(3, 5, 'Seth', 'm', 'D', '09812345674', 'Filipino', 'Male', '2025-2026', '1st Semester', 'Freshman', 'ABM', 'COM221', '1234', '1,627,000', 'Businessman', 'Businesswoman', 'Granja, Lipa City', 'San Antonio', 'Quezon', 'Pury, San Antonio, Quezon', '', 87),
(4, 12, 'qwer', '', '', '09783463455', 'Filipino', 'Prefer not to say', '2025-2026', '1st Semester', 'Freshman', 'HUMSS', 'BSA - Marketing', 'BSCE', '', '', '', '', '', '', 'Lipa', 'uploads/1758252932_default.jpg', 78),
(6, 14, 'Qwerty', 'Reqwe', 'Reda', '09783444556', 'Filipino', 'Male', '2025-2026', '1st Semester', 'Freshman', 'HUMSS', 'BSA - Marketing', 'BSMT', '1,5899', 'None', 'None', 'Lipa', 'Lipa', 'Batangas', 'Lipa', 'uploads/1758277801_default.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backup`
--
ALTER TABLE `backup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backup`
--
ALTER TABLE `backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
