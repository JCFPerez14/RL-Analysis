-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2025 at 06:39 AM
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
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `barangay_code` varchar(15) NOT NULL,
  `barangay_name` varchar(100) NOT NULL,
  `city_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `barangay_code`, `barangay_name`, `city_id`, `created_at`, `updated_at`) VALUES
(1, 'BAGONGPOOK001', 'Bagong Pook', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(2, 'BALINTAWAK002', 'Balintawak', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(3, 'BANAYBANAY003', 'Banaybanay I', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(4, 'BANAYBANAY004', 'Banaybanay II', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(5, 'BOLBOK005', 'Bolbok', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(6, 'BUGTONGNAP006', 'Bugtong na Pulo', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(7, 'BULACNIN007', 'Bulacnin', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(8, 'BUNGAHAN008', 'Bungahan', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(9, 'CALLEJON009', 'Callejon', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(10, 'CALZADA010', 'Calzada', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(11, 'CUTA011', 'Cuta', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(12, 'DELAPAZ012', 'Dela Paz', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(13, 'DELAPAZPUL013', 'Dela Paz Pulot Aplaya', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(14, 'DELAPAZPUL014', 'Dela Paz Pulot Itaas', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(15, 'DUMANTAY015', 'Dumantay', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(16, 'GULODITAAS016', 'Gulod Itaas', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(17, 'GULODLABAC017', 'Gulod Labac', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(18, 'HALIGUEKAN018', 'Haligue Kanluran', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(19, 'HALIGUESIL019', 'Haligue Silangan', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(20, 'ILIJAN020', 'Ilijan', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(21, 'KUMBA021', 'Kumba', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(22, 'KUMINTANGI022', 'Kumintang Ibaba', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(23, 'KUMINTANGI023', 'Kumintang Ilaya', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(24, 'LIBJO024', 'Libjo', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(25, 'LIPACITY025', 'Lipa City', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(26, 'LIPACITYPR026', 'Lipa City Proper', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(27, 'MABACONG027', 'Mabacong', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(28, 'MAHABANGDA028', 'Mahabang Dahilig', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(29, 'MAHABANGPA029', 'Mahabang Parang', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(30, 'MALABANAN030', 'Malabanan', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(31, 'MALITAM031', 'Malitam', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(32, 'MARUCLAP032', 'Maruclap', 2, '2025-10-09 02:58:51', '2025-10-09 02:58:51'),
(33, 'MATAASNALU033', 'Mataas na Lupa', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(34, 'NATUNUAN034', 'Natunuan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(35, 'PAGKILATAN035', 'Pagkilatan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(36, 'PAHARANGKA036', 'Paharang Kanluran', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(37, 'PAHARANGSI037', 'Paharang Silangan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(38, 'PALLOCANKA038', 'Pallocan Kanluran', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(39, 'PALLOCANSI039', 'Pallocan Silangan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(40, 'PINAMUCAN040', 'Pinamucan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(41, 'SANAGUSTIN041', 'San Agustin Kanluran', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(42, 'SANAGUSTIN042', 'San Agustin Silangan', 2, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(43, 'ADYA043', 'Adya', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(44, 'ANILAO044', 'Anilao', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(45, 'ANILAOLABA045', 'Anilao-Labac', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(46, 'BAGONGPOOK046', 'Bagong Pook', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(47, 'BALINTAWAK047', 'Balintawak', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(48, 'BANAYBANAY048', 'Banaybanay I', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(49, 'BANAYBANAY049', 'Banaybanay II', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(50, 'BOLBOK050', 'Bolbok', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(51, 'BUGTONGNAP051', 'Bugtong na Pulo', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(52, 'BULACNIN052', 'Bulacnin', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(53, 'BUNGAHAN053', 'Bungahan', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(54, 'CALLEJON054', 'Callejon', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(55, 'CALZADA055', 'Calzada', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(56, 'CUTA056', 'Cuta', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(57, 'DELAPAZ057', 'Dela Paz', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(58, 'DELAPAZPUL058', 'Dela Paz Pulot Aplaya', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(59, 'DELAPAZPUL059', 'Dela Paz Pulot Itaas', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(60, 'DUMANTAY060', 'Dumantay', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(61, 'GULODITAAS061', 'Gulod Itaas', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(62, 'GULODLABAC062', 'Gulod Labac', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(63, 'HALIGUEKAN063', 'Haligue Kanluran', 1, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(64, 'ALTURAMATA064', 'Altura Matanda', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(65, 'ALTURASOUT065', 'Altura-South', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(66, 'AMBULONG066', 'Ambulong', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(67, 'BANJOEAST067', 'Banjo East', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(68, 'BANJOWEST068', 'Banjo West', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(69, 'BILOGBILOG069', 'Bilog-Bilog', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(70, 'BOOT070', 'Boot', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(71, 'CALE071', 'Cale', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(72, 'DARASA072', 'Darasa', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(73, 'LAUREL073', 'Laurel', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(74, 'LUYOS074', 'Luyos', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(75, 'NATATAS075', 'Natatas', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(76, 'PAGASPAS076', 'Pagaspas', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(77, 'PANTAYBATA077', 'Pantay Bata', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(78, 'PANTAYMATA078', 'Pantay Matanda', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(79, 'POBLACIONB079', 'Poblacion Barangay 1', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(80, 'POBLACIONB080', 'Poblacion Barangay 2', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(81, 'POBLACIONB081', 'Poblacion Barangay 3', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(82, 'POBLACIONB082', 'Poblacion Barangay 4', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(83, 'POBLACIONB083', 'Poblacion Barangay 5', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(84, 'SALA084', 'Sala', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(85, 'SAMBAT085', 'Sambat', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(86, 'SANJOSE086', 'San Jose', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(87, 'SANTOL087', 'Santol', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(88, 'SANTOR088', 'Santor', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(89, 'ULANGO089', 'Ulango', 3, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(90, 'SANAGUSTIN090', 'San Agustin', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(91, 'SANANTONIO091', 'San Antonio', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(92, 'SANBARTOLO092', 'San Bartolome', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(93, 'SANFELIX093', 'San Felix', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(94, 'SANFERNAND094', 'San Fernando', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(95, 'SANFRANCIS095', 'San Francisco', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(96, 'SANISIDRON096', 'San Isidro Norte', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(97, 'SANISIDROS097', 'San Isidro Sur', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(98, 'SANJOAQUIN098', 'San Joaquin', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(99, 'SANJOSE099', 'San Jose', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(100, 'SANJUAN100', 'San Juan', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(101, 'SANLUIS101', 'San Luis', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(102, 'SANMIGUEL102', 'San Miguel', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(103, 'SANPEDRO103', 'San Pedro', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(104, 'SANTAANA104', 'Santa Ana', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(105, 'SANTAANAST105', 'Santa Anastacia', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(106, 'SANTACLARA106', 'Santa Clara', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(107, 'SANTACRUZ107', 'Santa Cruz', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(108, 'SANTAELENA108', 'Santa Elena', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(109, 'SANTAMARIA109', 'Santa Maria', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(110, 'SANTATERES110', 'Santa Teresita', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(111, 'SANTIAGO111', 'Santiago', 5, '2025-10-09 02:58:52', '2025-10-09 02:58:52'),
(117, 'DASMARIAS117', 'Dasmariñas', 14, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(118, 'SALITRAN118', 'Salitran', 14, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(119, 'PALIPARAN119', 'Paliparan', 14, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(120, 'LANGKAAN120', 'Langkaan', 14, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(121, 'SAMPALOC121', 'Sampaloc', 14, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(142, 'MALOLOS142', 'Malolos', 25, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(143, 'POBLACION143', 'Poblacion', 25, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(144, 'BARANGAYI144', 'Barangay I', 25, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(145, 'BARANGAYII145', 'Barangay II', 25, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(146, 'BARANGAYII146', 'Barangay III', 25, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(147, 'ANGELES147', 'Angeles', 28, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(148, 'POBLACION148', 'Poblacion', 28, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(149, 'BALIBAGO149', 'Balibago', 28, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(150, 'CUTCUT150', 'Cutcut', 28, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(151, 'PAMPANG151', 'Pampang', 28, '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(152, 'BACOOR152', 'Bacoor', 6, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(153, 'MOLINO153', 'Molino', 6, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(154, 'ZAPOTE154', 'Zapote', 6, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(155, 'POBLACION155', 'Poblacion', 6, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(156, 'NIOG156', 'Niog', 6, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(157, 'IMUS157', 'Imus', 15, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(158, 'POBLACION158', 'Poblacion', 15, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(159, 'ANABU159', 'Anabu', 15, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(160, 'BUCANDALA160', 'Bucandala', 15, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(161, 'MALAGASANG161', 'Malagasang', 15, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(162, 'TAGAYTAY162', 'Tagaytay', 16, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(163, 'POBLACION163', 'Poblacion', 16, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(164, 'CALAMBA164', 'Calamba', 16, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(165, 'MAHARLIKA165', 'Maharlika', 16, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(166, 'SILANG166', 'Silang', 16, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(167, 'SANPABLO167', 'San Pablo', 9, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(168, 'POBLACION168', 'Poblacion', 9, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(169, 'BARANGAYI169', 'Barangay I', 9, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(170, 'BARANGAYII170', 'Barangay II', 9, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(171, 'BARANGAYII171', 'Barangay III', 9, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(172, 'SANTAROSA172', 'Santa Rosa', 19, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(173, 'POBLACION173', 'Poblacion', 19, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(174, 'BALIBAGO174', 'Balibago', 19, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(175, 'CUTCUT175', 'Cutcut', 19, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(176, 'PAMPANG176', 'Pampang', 19, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(177, 'BIAN177', 'Biñan', 20, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(178, 'POBLACION178', 'Poblacion', 20, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(179, 'CANLALAY179', 'Canlalay', 20, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(180, 'MALABAN180', 'Malaban', 20, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(181, 'SANANTONIO181', 'San Antonio', 20, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(182, 'MAKATI182', 'Makati', 23, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(183, 'POBLACION183', 'Poblacion', 23, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(184, 'BELAIR184', 'Bel-Air', 23, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(185, 'DASMARINAS185', 'Dasmarinas', 23, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(186, 'SANLORENZO186', 'San Lorenzo', 23, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(187, 'TAGUIG187', 'Taguig', 24, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(188, 'POBLACION188', 'Poblacion', 24, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(189, 'BICUTAN189', 'Bicutan', 24, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(190, 'FORTBONIFA190', 'Fort Bonifacio', 24, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(191, 'UPPERBICUT191', 'Upper Bicutan', 24, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(192, 'MEYCAUAYAN192', 'Meycauayan', 26, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(193, 'POBLACION193', 'Poblacion', 26, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(194, 'BANCAL194', 'Bancal', 26, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(195, 'BAGBAGUIN195', 'Bagbaguin', 26, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(196, 'CAMALIG196', 'Camalig', 26, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(197, 'SANJOSEDEL197', 'San Jose del Monte', 27, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(198, 'POBLACION198', 'Poblacion', 27, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(199, 'TUNGKONGMA199', 'Tungkong Mangga', 27, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(200, 'SAPANGPALA200', 'Sapang Palay', 27, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(201, 'GUMAOC201', 'Gumaoc', 27, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(202, 'SANFERNAND202', 'San Fernando', 29, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(203, 'POBLACION203', 'Poblacion', 29, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(204, 'BARANGAYI204', 'Barangay I', 29, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(205, 'BARANGAYII205', 'Barangay II', 29, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(206, 'BARANGAYII206', 'Barangay III', 29, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(207, 'MABALACAT207', 'Mabalacat', 30, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(208, 'POBLACION208', 'Poblacion', 30, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(209, 'DAU209', 'Dau', 30, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(210, 'DOLORES210', 'Dolores', 30, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(211, 'SANFRANCIS211', 'San Francisco', 30, '2025-10-09 03:40:03', '2025-10-09 03:40:03'),
(212, 'MALVARPOBL212', 'Malvar Poblacion', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(213, 'BAGONGPOOK213', 'Bagong Pook', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(214, 'BANGA214', 'Banga', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(215, 'LUTANORTE215', 'Luta Norte', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(216, 'LUTASUR216', 'Luta Sur', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(217, 'SANGREGORI217', 'San Gregorio', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(218, 'SANPEDRO218', 'San Pedro', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(219, 'TINGLOY219', 'Tingloy', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(220, 'TAYSAN220', 'Taysan', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(221, 'POBLACIONI221', 'Poblacion I', 4, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(222, 'CAVITECITY222', 'Cavite City Poblacion', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(223, 'DALAHICAN223', 'Dalahican', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(224, 'SANANTONIO224', 'San Antonio', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(225, 'SANROQUE225', 'San Roque', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(226, 'SANTACRUZ226', 'Santa Cruz', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(227, 'CARIDAD227', 'Caridad', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(228, 'SANLORENZO228', 'San Lorenzo', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(229, 'PORTAVAGA229', 'Porta Vaga', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(230, 'MAGALLANES230', 'Magallanes', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(231, 'SANJOSE231', 'San Jose', 7, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(232, 'CALAMBAPOB232', 'Calamba Poblacion', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(233, 'POBLACIONI233', 'Poblacion I', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(234, 'POBLACIONI234', 'Poblacion II', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(235, 'POBLACIONI235', 'Poblacion III', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(236, 'POBLACIONI236', 'Poblacion IV', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(237, 'POBLACIONV237', 'Poblacion V', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(238, 'POBLACIONV238', 'Poblacion VI', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(239, 'POBLACIONV239', 'Poblacion VII', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(240, 'POBLACIONV240', 'Poblacion VIII', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(241, 'POBLACIONI241', 'Poblacion IX', 8, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(242, 'INTRAMUROS242', 'Intramuros', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(243, 'ERMITA243', 'Ermita', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(244, 'MALATE244', 'Malate', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(245, 'PACO245', 'Paco', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(246, 'PANDACAN246', 'Pandacan', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(247, 'SANANDRESB247', 'San Andres Bukid', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(248, 'SANMIGUEL248', 'San Miguel', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(249, 'SANNICOLAS249', 'San Nicolas', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(250, 'SANTAANA250', 'Santa Ana', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(251, 'SANTAMESA251', 'Santa Mesa', 10, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(252, 'DILIMAN252', 'Diliman', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(253, 'KAMUNING253', 'Kamuning', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(254, 'CUBAO254', 'Cubao', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(255, 'PROJECT4255', 'Project 4', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(256, 'PROJECT6256', 'Project 6', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(257, 'EASTKAMIAS257', 'East Kamias', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(258, 'WESTKAMIAS258', 'West Kamias', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(259, 'PINYAHAN259', 'Pinyahan', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(260, 'KRUSNALIGA260', 'Krus na Ligas', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18'),
(261, 'CENTRAL261', 'Central', 11, '2025-10-09 04:15:18', '2025-10-09 04:15:18');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `city_code` varchar(10) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `province_id` int(11) NOT NULL,
  `city_type` enum('City','Municipality') DEFAULT 'City',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `city_code`, `city_name`, `province_id`, `city_type`, `created_at`, `updated_at`) VALUES
(1, 'BAT001', 'Lipa City', 7, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(2, 'BAT002', 'Batangas City', 7, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(3, 'BAT003', 'Tanauan City', 7, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(4, 'BAT004', 'Malvar', 7, 'Municipality', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(5, 'BAT005', 'Sto. Tomas', 7, 'Municipality', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(6, 'CAV001', 'Bacoor City', 8, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(7, 'CAV002', 'Cavite City', 8, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(8, 'LAG001', 'Calamba City', 9, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(9, 'LAG002', 'San Pablo City', 9, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(10, 'MNL001', 'Manila', 10, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(11, 'MNL002', 'Quezon City', 10, 'City', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(14, 'DASMARIÑAS', 'Dasmariñas City', 8, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(15, 'IMUS', 'Imus City', 8, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(16, 'TAGAYTAY', 'Tagaytay City', 8, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(19, 'STAROSA', 'Santa Rosa City', 9, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(20, 'BINAN', 'Biñan City', 9, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(23, 'MAKATI', 'Makati City', 10, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(24, 'TAGUIG', 'Taguig City', 10, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(25, 'MALOLOS', 'Malolos City', 11, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(26, 'MEYCAUAYAN', 'Meycauayan City', 11, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(27, 'SJDMC', 'San Jose del Monte City', 11, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(28, 'ANGELES', 'Angeles City', 12, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(29, 'SANFERNAND', 'San Fernando City', 12, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09'),
(30, 'MABALACAT', 'Mabalacat City', 12, 'City', '2025-10-09 03:39:09', '2025-10-09 03:39:09');

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
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `province_code` varchar(10) NOT NULL,
  `province_name` varchar(100) NOT NULL,
  `region` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `province_code`, `province_name`, `region`, `created_at`, `updated_at`) VALUES
(7, 'BAT', 'Batangas', 'Region IV-A', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(8, 'CAV', 'Cavite', 'Region IV-A', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(9, 'LAG', 'Laguna', 'Region IV-A', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(10, 'MNL', 'Metro Manila', 'NCR', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(11, 'BUL', 'Bulacan', 'Region III', '2025-10-09 02:01:41', '2025-10-09 02:01:41'),
(12, 'PAM', 'Pampanga', 'Region III', '2025-10-09 02:01:41', '2025-10-09 02:01:41');

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
(4, 'perezjf1@students.nu-lipa.edu.ph', '$2y$10$H9.ijw85VKO7QS2hy6L1fOGdQFcz9BIUl4.TcN5v2/U/.TDvwZPQC', 'admin', 'Enrolled', '2025-09-17 10:13:42'),
(5, 'dimaanosr@students.nu-lipa.edu.ph', '$2y$10$H9.ijw85VKO7QS2hy6L1fOGdQFcz9BIUl4.TcN5v2/U/.TDvwZPQC', 'admin', 'Enrolled', '2025-09-17 10:17:57'),
(15, 'test_ml_1759294739@example.com', '$2y$10$AmCmD6vvGFh3xbffQUjU3uIRkkb2aOJAs5GE6p1NdYIepMro6OF7a', 'student', 'Not Enrolled', '2025-10-01 04:58:59'),
(17, 'salbahe@students.nu-lipa.edu.ph', '$2y$10$CJYySmJkmCtu2CmAG6ABa.qagxPpDnPkQTvlb.CinvTS4l5nx4MiO', 'student', 'Not Enrolled', '2025-10-01 05:24:32'),
(25, 'SAD12@gmail.com', '$2y$10$x.IU2f/ujBEwKeNSBf/oJOoCbx18gWKh/N4IFCo1gWUrMsqD0dSge', 'student', 'Enrolled', '2025-10-07 02:27:56'),
(39, 'ivhoryl@gmail.com', '$2y$10$lfmoKnjMzJgz8r3AElWkyumkvTboYrV3irg8/iRoBx8NS7ilK0Rp6', 'student', 'Enrolled', '2025-10-09 04:36:28'),
(40, '3mmm@gmail.com', '$2y$10$Mgte4JgYUHs1LFclzG60t.VqaYOoapN2Obd4dYHHKMJa7svKFaHDe', 'student', 'Not Enrolled', '2025-10-09 05:19:26'),
(41, '123@gmail.com', '$2y$10$gjbxOcYHOMz/WSF1EI.lo.8fP.2aybwKgGHKQilf9bfveYi0AUze.', 'student', 'Not Enrolled', '2025-10-09 07:02:00'),
(42, 'rz@gmail.com', '$2y$10$u6ybeb.I9P.krlM.MjtJge7llfYul4p8oYjKt2xIGkp3BjLI3KECy', 'student', 'Not Enrolled', '2025-10-09 07:09:12'),
(43, 'mas@gmail.com', '$2y$10$//oXdmpAi3ED8vF.pOdva.K5FZeEgiWC0BZdoT6xjbm.o5eXS2YYS', 'student', 'Not Enrolled', '2025-10-09 07:31:20'),
(44, 'aldo@gmail.com', '$2y$10$sxgzorTIPABUzq7/gWtnSObn4ewAOk0/phstQbFqG1C6q6OEJxs/u', 'student', 'Enrolled', '2025-10-09 08:07:33');

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
  `birth_date` date DEFAULT NULL,
  `academic_year` varchar(50) DEFAULT NULL,
  `academic_term` varchar(50) DEFAULT NULL,
  `applying_for` varchar(50) DEFAULT NULL,
  `strand` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `second_program` varchar(255) DEFAULT NULL,
  `previous_school` varchar(255) DEFAULT NULL,
  `family_income` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `city_code` varchar(10) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `province_code` varchar(10) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `photo` varchar(255) NOT NULL,
  `likelihood` decimal(5,2) DEFAULT NULL,
  `school_type` varchar(50) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `barangay_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `firstname`, `middlename`, `lastname`, `mobile`, `nationality`, `sex`, `birth_date`, `academic_year`, `academic_term`, `applying_for`, `strand`, `program`, `second_program`, `previous_school`, `family_income`, `father_occupation`, `mother_occupation`, `birthplace`, `city`, `city_code`, `province`, `province_code`, `current_address`, `photo`, `likelihood`, `school_type`, `barangay`, `barangay_code`) VALUES
(3, 5, 'Seth', 'm', 'D', '09812345674', 'Filipino', 'Male', NULL, '2025-2026', '1st Semester', 'Freshman', 'ABM', 'COM221', '1234', NULL, '1,627,000', 'Businessman', 'Businesswoman', 'Granja, Lipa City', 'San Antonio', NULL, 'Quezon', NULL, 'Pury, San Antonio, Quezon', '', 87.00, NULL, NULL, NULL),
(7, 15, 'Juan', 'Santos', 'Cruz', '09123456789', 'Filipino', 'Male', NULL, '2025-2026', '1st Semester', 'Freshman', 'STEM', 'BSCS', 'BSIT', NULL, '150000', 'Engineer', 'Teacher', 'Manila', 'Lipa', NULL, 'Batangas', NULL, 'Lipa City, Batangas', 'uploads/default.png', 79.00, NULL, NULL, NULL),
(9, 17, 'Jecel', 'Zab', 'Salvador', '09955783670', 'Filipino', 'Female', NULL, '2025-2026', '1st Semester', 'Freshman', '', 'BSTM', 'BSPYS', NULL, '1', 'none', 'none', 'Dito', 'Saan', NULL, 'Kelan', NULL, 'Shoto', 'uploads/1759296272_e373a72e-56d7-470a-a62f-06087014e875.jpg', 82.00, NULL, NULL, NULL),
(14, 25, 'Seth', 'A', 'Dmaano', '+6312121313131212121', 'Filipino', 'Male', NULL, '2025-2026', '1st Semester', 'Freshman', 'IT', 'BSCS', 'BSIT', NULL, '10', 'Tambay', 'None', 'Batangas City', 'Batangas City', NULL, 'Batangas', NULL, 'Batangas', 'uploads/1759804076_bg.jpg', 67.00, NULL, NULL, NULL),
(19, 39, 'Lhanz Ivhory', 'Mentoy', 'Aguila', '09952014535', 'Filipino', 'Female', '2004-09-06', '2025-2026', '2nd Term', 'Cross Enrollee', 'HUMSS', 'BSA - Marketing', 'BSTM', 'National University Lipa', '0', 'Father/OFW', 'Mother/Housewife', 'Batangas Regional Hospital', 'Lipa City', '1', 'Batangas', '7', '', 'uploads/default.png', 62.14, 'Private', 'Banaybanay I', '48'),
(20, 40, 'Micah', 'Macatangay', 'Mercado', '09999156955', 'Filipino', 'Female', '2003-12-12', '2025-2026', '2nd Term', 'Transferee', 'ABM', 'BSTM', 'BSA', 'Lipa City Senior High School', '25,000-50,000', 'Accountant', 'NA', 'Makati City', '041014000', '41014000', '041000000', '41000000', '', 'uploads/default.png', 69.35, 'Public', '041014031', '41014031'),
(22, 41, 'Isaias', 'Recomono', 'Dimaano', '09182301233', 'Filipino', 'Male', '2024-07-22', '2025-2026', '1st Term', 'Freshman', 'HUMSS', 'BSN', 'BSCS', 'Bixby Preparatory Academy', '0-25,000', 'None', 'Call Center', 'Padre Garcia General Hospital', '045641000', '45641000', '045600000', '45600000', '', 'uploads/default.png', 84.84, 'Private', '045641016', '45641016'),
(23, 42, 'Raphael', 'Zuckerberg', 'Lopez', '09653265959', 'Filipino', 'Male', '2004-03-03', '2025-2026', '2nd Term', 'Transferee', 'STEM', 'BSMT', 'BSN', 'Cuenca Integrated National High School', '25,000-50,000', 'Jeepney Driver', 'School Teacher', 'Manila Medical Hospital', '041009000', '41009000', '041000000', '41000000', '', 'uploads/default.png', 76.20, 'Public', '041009013', '41009013'),
(24, 43, 'Mark', 'Azogue', 'Santos', '09121348419', 'Filipino', 'Male', '2001-12-12', '2025-2026', '2nd Term', 'Transferee', 'ABM', 'BSBA - Fin', 'BSBA - MM', 'Lipa City College', '25,000-50,000', 'Barangay Tanod', 'OFW', 'Bacolod City', '041004000', '41004000', '041000000', '41000000', '', 'uploads/default.png', 58.41, 'Private', '041004009', '41004009'),
(25, 44, 'Aldouse', 'Ricoa', 'Rocako', '09293283283', 'Filipino', 'Female', '2000-03-11', '2025-2026', '1st Term', 'Freshman', 'TVL-HE', 'BSCS', 'BSIT', 'Bixby Knolls', '0-25,000', 'NONE', 'NONe', 'Pury', '060407000', '60407000', '060400000', '60400000', '', 'uploads/default.png', 70.84, 'Private', '060407016', '60407016');

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
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_barangay_code` (`barangay_code`),
  ADD KEY `idx_barangay_name` (`barangay_name`),
  ADD KEY `idx_city_id` (`city_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_city_code` (`city_code`),
  ADD KEY `idx_city_name` (`city_name`),
  ADD KEY `idx_province_id` (`province_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_province_code` (`province_code`),
  ADD KEY `idx_province_name` (`province_name`);

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
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangays`
--
ALTER TABLE `barangays`
  ADD CONSTRAINT `barangays_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
