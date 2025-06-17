-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 03:08 PM
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
-- Database: `kusumba_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approved_birth_certificates`
--

CREATE TABLE `approved_birth_certificates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_aadhaar` varchar(12) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_aadhaar` varchar(12) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_number` varchar(50) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_birth_certificates`
--

INSERT INTO `approved_birth_certificates` (`id`, `name`, `gender`, `dob`, `mother_name`, `mother_aadhaar`, `father_name`, `father_aadhaar`, `address`, `registration_number`, `registration_date`, `created_at`, `updated_at`) VALUES
(1, 'Aarav Patil', 'MALE', '2024-02-10', 'Priya Patil', '123456789012', 'Rahul Patil', '987654321098', 'KUSUMBA Gali No 4', 'BRTH-2025-86621-1', '2025-03-07', '2025-03-07 16:54:17', '2025-03-07 17:02:50'),
(2, 'Aarav Patil', 'MALE', '2343-03-30', 'Priya Patil', '123456789012', 'Rahul Patil', '123456789876', 'r45', 'BRTH-2025-78012-2', '2025-03-08', '2025-03-08 09:19:20', '2025-03-08 09:19:36'),
(3, 'Jayesh ravindra patil', 'MALE', '2000-07-21', 'ushabai ravindra patil', '457889658569', 'ravindra kailas patil', '784545214568', 'Kusumba Gali no. 4', 'BRTH-2025-90576-3', '2025-03-21', '2025-03-21 07:10:05', '2025-03-21 07:11:09'),
(4, 'dkjdjdsd', 'FEMALE', '2025-03-05', 'djskdjks', '457859887546', 'skskdjksjdksd', '784578598658', 'dsdsdklssdjs', '242', '2025-03-23', '2025-03-24 07:25:52', '2025-03-24 07:25:52'),
(5, 'dkjdjdsd', 'FEMALE', '2025-03-05', 'djskdjks', '457859887546', 'skskdjksjdksd', '784578598658', 'dsdsdklssdjs', '186', '2025-03-23', '2025-03-27 09:34:03', '2025-03-27 09:34:03');

-- --------------------------------------------------------

--
-- Table structure for table `approved_death_certificate`
--

CREATE TABLE `approved_death_certificate` (
  `id` int(11) NOT NULL,
  `name_of_deceased` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `age` int(11) NOT NULL,
  `aadhaar_number` varchar(12) NOT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `registration_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_death_certificate`
--

INSERT INTO `approved_death_certificate` (`id`, `name_of_deceased`, `gender`, `age`, `aadhaar_number`, `date_of_death`, `place_of_death`, `address`, `registration_number`, `registration_date`, `created_at`, `remarks`) VALUES
(1, 'vijay ramesh jadhav', 'MALE', 50, '457889568956', '2025-03-12', 'Shirpur', 'Kusumba Gali no.5', 'NOT_SPECIFIED', '2025-03-21', '2025-03-21 09:18:57', 'all data valid'),
(2, 'Kunal narayan Patil', 'MALE', 39, '784589586895', '2025-03-04', 'Shirpur ', 'Kusumba Gali no.7', 'NOT_SPECIFIED', '2025-03-22', '2025-03-22 14:13:56', 'all document is valid\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `approved_marriage`
--

CREATE TABLE `approved_marriage` (
  `id` int(11) NOT NULL,
  `husband_name` varchar(255) NOT NULL,
  `husband_photo` varchar(255) DEFAULT NULL,
  `husband_address` text DEFAULT NULL,
  `husband_dob` date DEFAULT NULL,
  `husband_age` int(11) DEFAULT NULL,
  `husband_cast` varchar(100) DEFAULT NULL,
  `husband_aadhaar` varchar(20) DEFAULT NULL,
  `wife_name` varchar(255) NOT NULL,
  `wife_photo` varchar(255) DEFAULT NULL,
  `wife_address` text DEFAULT NULL,
  `wife_dob` date DEFAULT NULL,
  `wife_age` int(11) DEFAULT NULL,
  `wife_cast` varchar(100) DEFAULT NULL,
  `wife_aadhaar` varchar(20) DEFAULT NULL,
  `marriage_date` date NOT NULL,
  `place_of_marriage` varchar(255) DEFAULT NULL,
  `registration_date` date NOT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `certificate_no` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_marriage`
--

INSERT INTO `approved_marriage` (`id`, `husband_name`, `husband_photo`, `husband_address`, `husband_dob`, `husband_age`, `husband_cast`, `husband_aadhaar`, `wife_name`, `wife_photo`, `wife_address`, `wife_dob`, `wife_age`, `wife_cast`, `wife_aadhaar`, `marriage_date`, `place_of_marriage`, `registration_date`, `registration_number`, `certificate_no`, `remarks`, `created_at`) VALUES
(2, 'Rohit Sukhalal Patil', '1743324158_husband photo.png', 'kusumba Gali no.2', '1999-05-05', 25, 'OBC', NULL, 'Prachi Manoj Patil', '1743324158_Screenshot_2025-03-06_17-38-24.png', 'Shirpur Pratap nagar.', '2003-06-10', 21, 'OBC', NULL, '2025-03-06', NULL, '2025-03-30', 'MRRG-2025-98225-2', '1002', 'kskdlsl', '2025-03-31 10:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `approved_marriage_db`
--

CREATE TABLE `approved_marriage_db` (
  `id` int(11) NOT NULL,
  `certificate_number` varchar(20) NOT NULL,
  `husband_name` varchar(255) NOT NULL,
  `husband_photo` varchar(255) NOT NULL,
  `husband_address` text NOT NULL,
  `wife_name` varchar(255) NOT NULL,
  `wife_photo` varchar(255) NOT NULL,
  `wife_address` text NOT NULL,
  `marriage_date` date NOT NULL,
  `registration_date` date NOT NULL,
  `registration_number` varchar(10) NOT NULL,
  `marriage_place` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_marriage_db`
--

INSERT INTO `approved_marriage_db` (`id`, `certificate_number`, `husband_name`, `husband_photo`, `husband_address`, `wife_name`, `wife_photo`, `wife_address`, `marriage_date`, `registration_date`, `registration_number`, `marriage_place`, `created_at`) VALUES
(1, '839242', 'Ramesh Kisan patil', '1743675477_8360f6e8e6167d545b0c34de7490cc1e.jpg', 'kusumba Gali no.4', 'Prachi Manoj Patil', '1743675477_images (1).jpeg', 'Surat Sanjay Nagar', '2025-03-05', '2025-04-03', '945', NULL, '2025-04-03 10:30:25'),
(2, '914821', 'Ramesh Kisan patil', '1743675477_8360f6e8e6167d545b0c34de7490cc1e.jpg', 'kusumba Gali no.4', 'Prachi Manoj Patil', '1743675477_images (1).jpeg', 'Surat Sanjay Nagar', '2025-03-05', '2025-04-03', '134', 'Kusumba', '2025-04-03 10:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `birth_certificate`
--

CREATE TABLE `birth_certificate` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('MALE','FEMALE') NOT NULL,
  `dob` date NOT NULL,
  `place_of_birth` varchar(255) NOT NULL,
  `proof_of_birth` enum('hospital_certificate','hospital_statement','parental_affidavit') NOT NULL,
  `proof_of_birth_file` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) NOT NULL,
  `mother_aadhaar` varchar(12) DEFAULT NULL,
  `mother_aadhaar_proof` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_aadhaar` varchar(12) DEFAULT NULL,
  `father_aadhaar_proof` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `registration_date` date NOT NULL,
  `marriage_certificate` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registration_number` varchar(10) DEFAULT NULL,
  `villager_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `birth_certificate`
--

INSERT INTO `birth_certificate` (`id`, `name`, `gender`, `dob`, `place_of_birth`, `proof_of_birth`, `proof_of_birth_file`, `mother_name`, `mother_aadhaar`, `mother_aadhaar_proof`, `father_name`, `father_aadhaar`, `father_aadhaar_proof`, `address`, `registration_date`, `marriage_certificate`, `created_at`, `status`, `remarks`, `updated_at`, `registration_number`, `villager_email`) VALUES
(1, 'Aarav Patil', 'MALE', '2024-02-10', 'Dhule', 'hospital_certificate', '1741360014_fake-hospital-bill-template-lovely-fake-hospital-bill-template-of-fake-hospital-bill-template.jpg', 'Priya Patil', '123456789012', '1741360014_OIP.jpg', 'Rahul Patil', '987654321098', '1741360014_OIP (2).jpg', 'KUSUMBA Gali No 4', '2025-03-07', '1741360014_OIP (7).jpg', '2025-03-07 15:06:54', 'APPROVED', 'all uploaded documents is valid and data also valid', '2025-03-07 16:54:17', NULL, NULL),
(2, 'Aarav Patil', 'MALE', '2343-03-30', 'Dhule', 'hospital_statement', '1741425531_aj013834_professional_indian_female_white_background_55478f12-b275-4542-ae41-5f8ec56490db.png', 'Priya Patil', '123456789012', '1741425531_aj013834_professional_indian_female_white_background_55478f12-b275-4542-ae41-5f8ec56490db.png', 'Rahul Patil', '123456789876', '1741425531_OIP (7).jpg', 'r45', '2025-03-08', '1741425531_screencapture-localhost-UDAY-WEB-CPP-WEB-Admin-Certificate-Manage-view-birth-certificates-php-2025-03-07-20_37_32.png', '2025-03-08 09:18:51', 'APPROVED', 'done', '2025-03-08 09:19:20', NULL, NULL),
(3, 'Jayesh ravindra patil', 'MALE', '2000-07-21', 'Dhule', 'hospital_certificate', '1742540334_hospital bill for new baby.png', 'ushabai ravindra patil', '457889658569', '1742540334_aadhaar-card-1.png', 'ravindra kailas patil', '784545214568', '1742540334_Aadhaar-card-sample - Copy.png', 'Kusumba Gali no. 4', '2025-03-21', '1742540334_marriage_cert_67bc94af1b382_BlankMarriageCertificateDownload.jpg', '2025-03-21 06:58:54', 'APPROVED', 'all villager details and documents are valid', '2025-03-21 07:10:05', NULL, NULL),
(4, 'UDAY LALDAS AHIRE', 'MALE', '2006-02-15', 'Surat', 'hospital_certificate', '1742708899_Screenshot 2023-10-23 182401.png', 'Surekha Laldas Ahire', '457414782589', '1742708899_Screenshot 2023-10-21 201048.png', '456878547859', '784578956987', '', 'Ravidas Chauk Songir Tal and Dist Dhule ', '2025-03-23', '1742708899_Screenshot 2023-10-25 095308.png', '2025-03-23 05:48:19', 'PENDING', NULL, NULL, NULL, NULL),
(5, 'Gita Ramesh Patil', 'FEMALE', '2007-10-23', 'Dhule ', 'hospital_certificate', '1742710011_Screenshot 2023-10-23 000716.png', 'vffvfff', '457857848986', '1742710011_Screenshot 2023-10-23 000654.png', 'dshdhshihowh', '789456859652', '1742710011_Screenshot 2023-10-21 200836.png', 'Kusumba Gali no.6', '2025-03-23', '1742710011_Screenshot 2023-10-23 182538.png', '2025-03-23 06:06:51', 'PENDING', NULL, NULL, '047', 'ravi@gmail.com'),
(6, 'Gita Ramesh Patil', 'FEMALE', '2007-10-23', 'Dhule ', 'hospital_certificate', '1742710234_Screenshot 2023-10-23 000716.png', 'vffvfff', '457857848986', '1742710234_Screenshot 2023-10-23 000654.png', 'dshdhshihowh', '789456859652', '1742710234_Screenshot 2023-10-21 200836.png', 'Kusumba Gali no.6', '2025-03-23', '1742710234_Screenshot 2023-10-23 182538.png', '2025-03-23 06:10:34', 'REJECTED', 'dsksdlksd', '2025-03-30 08:31:57', 'B695856', 'ravi@gmail.com'),
(7, 'dkjdjdsd', 'FEMALE', '2025-03-05', 'sdkjdjs', 'hospital_certificate', '1742710306_Screenshot 2023-10-25 095402.png', 'djskdjks', '457859887546', '1742710306_Screenshot 2023-10-25 095308.png', 'skskdjksjdksd', '784578598658', '1742710306_Screenshot 2023-10-21 200836.png', 'dsdsdklssdjs', '2025-03-23', '1742710306_Screenshot 2023-10-23 182538.png', '2025-03-23 06:11:46', 'APPROVED', 'all data is valid', '2025-03-27 09:34:03', 'B237105', 'ravi@gmail.com'),
(8, 'dkjdjdsd', 'FEMALE', '2025-03-05', 'sdkjdjs', 'hospital_certificate', '1742710335_Screenshot 2023-10-25 095402.png', 'djskdjks', '457859887546', '1742710335_Screenshot 2023-10-25 095308.png', 'skskdjksjdksd', '784578598658', '1742710335_Screenshot 2023-10-21 200836.png', 'dsdsdklssdjs', '2025-03-23', '1742710335_Screenshot 2023-10-23 182538.png', '2025-03-23 06:12:15', 'APPROVED', 'all data is valid\r\n', '2025-03-24 07:25:52', 'B202503230', 'ravi@gmail.com'),
(9, 'jatin mahesh gabda', 'MALE', '2006-08-03', 'dhule', 'hospital_certificate', '1744011744_Calculator.jpg', 'hghghg', '457888575744', '1744011744_aadhaar-card-1.png', 'mahesh gabda', '457857458956', '1744011744_uploads1740628575_aadhar card.jpg', 'Kumar nagar sakri road dhule', '2025-04-07', '1744011744_Marriage Certificate.pdf', '2025-04-07 07:42:24', 'REJECTED', 'document is invalid ', '2025-04-07 07:43:30', 'B202504070', 'nitin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `death_certificate`
--

CREATE TABLE `death_certificate` (
  `id` int(11) NOT NULL,
  `certificate_number` varchar(20) NOT NULL,
  `name_of_deceased` varchar(255) NOT NULL,
  `gender` enum('MALE','FEMALE') NOT NULL,
  `birth_date` date NOT NULL,
  `age` int(11) NOT NULL CHECK (`age` >= 0 and `age` <= 150),
  `aadhaar_number` varchar(12) NOT NULL,
  `aadhaar_document` varchar(255) NOT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `registration_date` date NOT NULL,
  `villager_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'PENDING',
  `remarks` text DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `death_certificate`
--

INSERT INTO `death_certificate` (`id`, `certificate_number`, `name_of_deceased`, `gender`, `birth_date`, `age`, `aadhaar_number`, `aadhaar_document`, `date_of_death`, `place_of_death`, `address`, `registration_date`, `villager_email`, `created_at`, `status`, `remarks`, `updated_at`) VALUES
(1, 'DC202503229217', 'Kunal narayan Patil', 'MALE', '1985-03-12', 39, '784589586895', 'aadhaar_784589586895_20250322055618.jpg', '2025-03-04', 'Shirpur ', 'Kusumba Gali no.7', '2025-03-22', NULL, '2025-03-22 04:56:18', 'APPROVED', 'all document is valid\r\n', '2025-03-22 19:43:56'),
(3, 'DC202503228933', 'Komal vinod Patil', 'FEMALE', '1998-06-09', 26, '789858927845', 'aadhaar_789858927845_20250322123053.jpg', '2025-03-19', 'Shindkheda', 'Kusumba gali no.2', '2025-03-22', NULL, '2025-03-22 11:30:53', 'PENDING', NULL, NULL),
(4, 'DC202503228527', 'UDAY LALDAS AHIRE', 'MALE', '1984-06-05', 40, '457889568956', 'aadhaar_457889568956_20250322124551.jpg', '2025-03-06', 'dhule', 'Kusumba Gali no.7', '2025-03-22', 'ravi@gmail.com', '2025-03-22 11:45:51', 'PENDING', NULL, NULL),
(5, 'DC202504271634233176', 'nikhil patil', 'MALE', '2006-06-07', 18, '231212121212', '1745764463_view recipient in villager site.jpg', '2025-04-15', 'sdssdd', 'ssdsd', '2025-04-27', 'ravi@gmail.com', '2025-04-27 14:34:23', 'PENDING', NULL, NULL),
(6, 'DC202504271638308012', 'ssddsd', 'MALE', '2002-10-15', 22, '456789582589', '1745764710_use case (2).png', '2025-04-09', 'songi', 'dksjkjdks', '2025-04-27', 'ravi@gmail.com', '2025-04-27 14:38:30', 'PENDING', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `marriage_certificates`
--

CREATE TABLE `marriage_certificates` (
  `id` int(11) NOT NULL,
  `certificate_no` int(11) NOT NULL,
  `husband_name` varchar(100) NOT NULL,
  `husband_address` text NOT NULL,
  `husband_dob` date NOT NULL,
  `husband_age` int(11) NOT NULL,
  `husband_cast` varchar(50) NOT NULL,
  `husband_photo` varchar(255) NOT NULL,
  `husband_aadhar_no` varchar(12) NOT NULL,
  `husband_aadhar_doc` varchar(255) NOT NULL,
  `wife_name` varchar(100) NOT NULL,
  `wife_address` text NOT NULL,
  `wife_dob` date NOT NULL,
  `wife_age` int(11) NOT NULL,
  `wife_cast` varchar(50) NOT NULL,
  `wife_photo` varchar(255) NOT NULL,
  `wife_aadhar_no` varchar(12) NOT NULL,
  `wife_aadhar_doc` varchar(255) NOT NULL,
  `marriage_date` date NOT NULL,
  `marriage_place` varchar(255) NOT NULL,
  `marriage_card` varchar(255) NOT NULL,
  `witness1_name` varchar(100) NOT NULL,
  `witness1_aadhar_doc` varchar(255) NOT NULL,
  `witness2_name` varchar(100) NOT NULL,
  `witness2_aadhar_doc` varchar(255) NOT NULL,
  `witness3_name` varchar(100) NOT NULL,
  `witness3_aadhar_doc` varchar(255) NOT NULL,
  `registration_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'PENDING',
  `remarks` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marriage_certificates`
--

INSERT INTO `marriage_certificates` (`id`, `certificate_no`, `husband_name`, `husband_address`, `husband_dob`, `husband_age`, `husband_cast`, `husband_photo`, `husband_aadhar_no`, `husband_aadhar_doc`, `wife_name`, `wife_address`, `wife_dob`, `wife_age`, `wife_cast`, `wife_photo`, `wife_aadhar_no`, `wife_aadhar_doc`, `marriage_date`, `marriage_place`, `marriage_card`, `witness1_name`, `witness1_aadhar_doc`, `witness2_name`, `witness2_aadhar_doc`, `witness3_name`, `witness3_aadhar_doc`, `registration_date`, `created_at`, `status`, `remarks`, `updated_at`, `email`) VALUES
(1, 1001, 'ABHIJIT MADHUKAR PARDESHI', 'Kusumba Gali no.7, Dist. and Tal. Dhule', '2000-06-08', 24, 'OBC', '1742383471_1000_F_422823992_ZtyrE96o6wGTJcyZolZ6pLRUGHBRCH9c.jpg', '784578698547', '1742383471_aadhaar-card-1.png', 'MEGHA RATILAL PARDESHI', 'Sakri Road Dhule', '2002-06-12', 22, 'OBC', '1742383471_istockphoto-911901526-612x612.jpg', '784589652541', '1742383471_aadhar card.jpg', '2025-01-22', 'Kusumba ', '1742383471_System Architecture Diagram (Community).png', 'Kunal Patil', '1742383471_Aadhaar-card-sample.png', 'pranav mali', '1742383471_Aadhaar-card-sample.png', 'mahesh jadhav', '1742383471_uploads1740628575_aadhar card.jpg', '2025-03-19', '2025-03-19 11:24:31', 'REJECTED', 'document is invalid', '2025-03-31 11:06:30', NULL),
(2, 1002, 'Rohit Sukhalal Patil', 'kusumba Gali no.2', '1999-05-05', 25, 'OBC', '1743324158_husband photo.png', '234554321234', '1743324158_Screenshot_2025-03-05_11-19-03.png', 'Prachi Manoj Patil', 'Shirpur Pratap nagar.', '2003-06-10', 21, 'OBC', '1743324158_Screenshot_2025-03-06_17-38-24.png', '457857848965', '1743324158_Screenshot_2025-03-07_04-28-06.png', '2025-03-06', 'Kusumba Gali no.2', '', 'Kunal mahesh patil', '1743324158_FigJam basics (1).jpg', 'priya dipak mahale', '1743324158_aadhaar-card-1.png', 'nikita ramesh shinde', '1743324158_Screenshot_2025-03-06_17-38-24.png', '2025-03-30', '2025-03-30 08:42:38', 'APPROVED', 'kskdlsl', '2025-03-31 10:42:23', NULL),
(3, 1003, 'Ramesh Kisan patil', 'kusumba Gali no.4', '1996-01-16', 29, 'OBC', '1743675477_8360f6e8e6167d545b0c34de7490cc1e.jpg', '789456875485', '1743675477_uploads1740628575_aadhar card.jpg', 'Prachi Manoj Patil', 'Surat Sanjay Nagar', '2002-02-05', 23, 'OBC', '1743675477_images (1).jpeg', '457457125698', '1743675477_aadhaar-card-1.png', '2025-03-05', 'Kusumba', '', 'Ritesh vijay Patil', '1743675477_uploads1740628575_aadhar card.jpg', 'Komal Satish Mahale', '1743675477_aadhaar-card-1.png', 'Pranjal Vikas Patil', '1743675477_uploads1740628575_aadhar card.jpg', '2025-04-03', '2025-04-03 10:17:57', NULL, NULL, '2025-04-28 08:27:33', 'nikhil@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `marriage_certificate_db`
--

CREATE TABLE `marriage_certificate_db` (
  `id` int(11) NOT NULL,
  `husband_name` varchar(255) NOT NULL,
  `husband_address` text NOT NULL,
  `husband_dob` date NOT NULL,
  `husband_age` int(11) NOT NULL,
  `husband_cast` varchar(100) NOT NULL,
  `husband_photo` varchar(255) DEFAULT NULL,
  `husband_aadhar` varchar(255) DEFAULT NULL,
  `wife_name` varchar(255) NOT NULL,
  `wife_address` text NOT NULL,
  `wife_dob` date NOT NULL,
  `wife_age` int(11) NOT NULL,
  `wife_cast` varchar(100) NOT NULL,
  `wife_photo` varchar(255) DEFAULT NULL,
  `wife_aadhar` varchar(255) DEFAULT NULL,
  `marriage_date` date NOT NULL,
  `marriage_place` varchar(255) NOT NULL,
  `registration_date` date NOT NULL,
  `witness1_name` varchar(255) NOT NULL,
  `witness1_aadhar` varchar(255) NOT NULL,
  `witness2_name` varchar(255) NOT NULL,
  `witness2_aadhar` varchar(255) NOT NULL,
  `witness3_name` varchar(255) NOT NULL,
  `witness3_aadhar` varchar(255) NOT NULL,
  `marriage_priest_name` varchar(255) NOT NULL,
  `marriage_priest_aadhar` varchar(255) NOT NULL,
  `marriage_card` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings_db`
--

CREATE TABLE `meetings_db` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `title_marathi` varchar(200) DEFAULT NULL,
  `description` text NOT NULL,
  `description_marathi` text DEFAULT NULL,
  `meeting_date` datetime NOT NULL,
  `venue` varchar(200) NOT NULL,
  `status` enum('upcoming','completed','cancelled') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetings_db`
--

INSERT INTO `meetings_db` (`id`, `title`, `title_marathi`, `description`, `description_marathi`, `meeting_date`, `venue`, `status`, `created_at`) VALUES
(1, 'Shiv Jayanti Event Planning🚩', 'शिवजयंती कार्यक्रम नियोजन 🚩', 'Chhatrapati Shivaji Maharaj Jayanti Planning 🚩', 'छत्रपती शिवाजी महाराज जयंती नियोजन 🚩', '2025-02-17 18:00:00', 'Kusumba Gali No2', 'completed', '2025-02-09 09:40:45'),
(2, 'Ganesh Utsav', 'गणेश उत्सव', 'for celebration ', 'उत्सवासाठी', '2025-08-28 19:46:00', 'gali no2', 'upcoming', '2025-06-17 12:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `property_tax`
--

CREATE TABLE `property_tax` (
  `id` int(11) NOT NULL,
  `property_number` varchar(255) NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `transfer_name` varchar(255) DEFAULT NULL,
  `house_type` varchar(255) DEFAULT NULL,
  `height` double DEFAULT NULL,
  `width` double DEFAULT NULL,
  `area_sqft` double DEFAULT NULL,
  `area_sqm` double DEFAULT NULL,
  `tax_rate` double DEFAULT NULL,
  `previous_home_tax` double DEFAULT NULL,
  `current_home_tax` double DEFAULT NULL,
  `total_home_tax` double DEFAULT NULL,
  `water_tax_type` varchar(255) DEFAULT NULL,
  `previous_water_tax` double DEFAULT NULL,
  `water_tax` double DEFAULT NULL,
  `total_water_tax` double DEFAULT NULL,
  `previous_sanitation_tax` double DEFAULT NULL,
  `sanitation_tax` double DEFAULT NULL,
  `total_sanitation_tax` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_tax`
--

INSERT INTO `property_tax` (`id`, `property_number`, `owner_name`, `transfer_name`, `house_type`, `height`, `width`, `area_sqft`, `area_sqm`, `tax_rate`, `previous_home_tax`, `current_home_tax`, `total_home_tax`, `water_tax_type`, `previous_water_tax`, `water_tax`, `total_water_tax`, `previous_sanitation_tax`, `sanitation_tax`, `total_sanitation_tax`) VALUES
(1, '1822', 'Subhash Raghav Jire', 'self', 'Concrete House', 30, 30, 900, 83.61, 1.95, 355, 1755, 2110, 'General', 975, 150, 150, 50, 50, 50),
(2, '1823/1', 'Jayvantabai Pandit Pardhi', 'self', 'Concrete House', 30, 15, 450, 41.81, 1.95, 0, 877.5, 877.5, 'General', 413, 150, 563, 30, 50, 80),
(3, '1823/2', 'Ravindra Pandit Chavhan', 'self', 'Concrete House', 60, 30, 1800, 167.23, 1.95, 500, 3510, 4010, 'General', 413, 150, 563, 30, 50, 80),
(4, '6038', 'Manilal Maganlal Jain', 'self', 'Concrete House', 60, 30, 1800, 167.23, 1.95, 500, 3510, 4010, 'General', 58, 150, 208, 50, 50, 100),
(5, '2701', 'Manisha Rajendra Vani', 'Self', 'Concrete House', 30, 15, 450, 41.81, 1.95, 800, 877.5, 1677.5, 'General', 250, 150, 400, 58, 50, 108),
(6, '3208', 'Manisha Swapnil Shinde', 'Self', 'Concrete House', 60, 30, 1800, 167.23, 1.95, 234, 3510, 3744, 'General', 10469, 150, 10619, 570, 50, 620),
(7, '2683', 'Renukabai Pundlik Perdeshi', 'Self', 'Concrete House', 30, 30, 900, 83.61, 1.95, 0, 1755, 1755, 'General', 575, 150, 725, 50, 50, 100),
(8, '392', 'Rekha Rajendra Shinde', 'Self', 'Concrete House', 60, 15, 900, 83.61, 1.95, 700, 1755, 2455, 'General', 413, 150, 563, 30, 50, 80),
(9, '3935', 'Manisha Madhav Jire', 'self', 'RCC', 60, 30, 1800, 167.23, 2.8, 355, 5040, 5395, 'General', 413, 150, 563, 30, 50, 80);

-- --------------------------------------------------------

--
-- Table structure for table `scheme_db`
--

CREATE TABLE `scheme_db` (
  `id` int(11) NOT NULL,
  `scheme_name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `description_marathi` text DEFAULT NULL,
  `required_documents` text DEFAULT NULL,
  `required_documents_marathi` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheme_db`
--

INSERT INTO `scheme_db` (`id`, `scheme_name`, `description`, `description_marathi`, `required_documents`, `required_documents_marathi`, `start_date`, `status`, `created_at`) VALUES
(1, '1️⃣ Employment Guarantee Scheme (EGS)', 'The National Rural Employment Guarantee Act (NREGA) of 2005 was implemented and officially launched on February 2, 2006. This scheme guarantees a minimum of 100 days of wage employment per financial year to rural households willing to engage in unskilled manual labor. It focuses on works related to water conservation, afforestation, irrigation, land development, and rural connectivity.', '२००५ मधील राष्ट्रीय ग्रामीण रोजगार हमी कायद्यांतर्गत (NREGA) कार्यान्वित आणि अधिकृतपणे २ फेब्रुवारी २००६ रोजी सुरू करण्यात आलेली ही योजना, अशिक्षित श्रम कामासाठी स्वेच्छेने सामील होणाऱ्या ग्रामीण कुटुंबांसाठी आर्थिक वर्षात किमान १०० दिवस हमी रोजगार प्रदान करते. ही योजना जलसंधारण, वृक्षारोपण, सिंचन, जमीन विकास आणि ग्रामीण संपर्क यासारख्या कामांवर केंद्रित आहे', 'Required Documents:\r\n✅ Aadhaar Card\r\n✅ Residence Certificate\r\n✅ Income Certificate\r\n✅ Bank Account Details\r\n✅ Passport Size Photo\r\n✅ Job Card (Must be registered under NREGA)\r\n✅ Certificate from Gram Panchayat or Local Authority', 'आवश्यक कागदपत्रे:\r\n✅ आधार कार्ड\r\n✅ रहिवासी प्रमाणपत्र\r\n✅ उत्पन्न प्रमाणपत्र\r\n✅ बँक खाते तपशील\r\n✅ पासपोर्ट साइज फोटो\r\n✅ जॉब कार्ड (NREGA अंतर्गत नोंदणीकृत असणे आवश्यक)\r\n✅ ग्रामपंचायत किंवा स्थानिक प्राधिकरणाचे प्रमाणपत्र', '2006-06-15', 'active', '2025-02-09 09:05:53'),
(2, '2️⃣ Sanjay Gandhi Niradhar Anudan Yojana', 'This state-sponsored scheme provides financial assistance to needy individuals who are weak, mentally challenged, physically disabled, or suffering from chronic illnesses such as cancer or leprosy, as well as orphaned children who are unable to sustain themselves. Eligible applicants must be residents of Maharashtra for at least 15 years, must not have any permanent source of income, and should be below 65 years of age.', 'ही राज्यपुरस्कृत योजना अशक्त, मानसिकदृष्ट्या अक्षम, शारीरिक अपंग, कर्करोग किंवा कुष्ठरोगासारख्या जुनाट आजारांनी ग्रस्त असलेले व्यक्ती तसेच अनाथ मुले जे उपजीविकेसाठी सक्षम नाहीत अशा गरजूंसाठी आर्थिक मदत प्रदान करते. पात्र अर्जदार हे महाराष्ट्राचे किमान १५ वर्षांचे रहिवासी असणे आवश्यक आहे, त्यांना कोणताही स्थायी उत्पन्न स्रोत नसावा आणि त्यांचे वय ६५ वर्षांपेक्षा कमी असावे.', 'Required Documents:\r\n✅ Aadhaar Card\r\n✅ Residence Certificate\r\n✅ Income Certificate\r\n✅ Bank Account Details\r\n✅ Passport Size Photo\r\n✅ Disability Certificate (if applicable)\r\n✅ Medical Certificate (for severe illnesses)\r\n✅ Orphan Certificate (if applicable)', 'आवश्यक कागदपत्रे:\r\n✅ आधार कार्ड\r\n✅ रहिवासी प्रमाणपत्र\r\n✅ उत्पन्न प्रमाणपत्र\r\n✅ बँक खाते तपशील\r\n✅ पासपोर्ट साइज फोटो\r\n✅ अपंगत्व प्रमाणपत्र (जर लागू असेल तर)\r\n✅ वैद्यकीय प्रमाणपत्र (गंभीर आजारासाठी)\r\n✅ अनाथ प्रमाणपत्र (जर लागू असेल तर)', '2016-07-13', 'inactive', '2025-02-09 09:10:47'),
(15, 'Ladki bahin', 'for women', 'महिलांसाठी', 'aadhar card', 'आधार कार्ड', '2025-06-11', 'active', '2025-06-17 12:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `scheme_recipients`
--

CREATE TABLE `scheme_recipients` (
  `sr_no` int(11) NOT NULL,
  `recipient_id` varchar(50) NOT NULL,
  `scheme_id` int(11) DEFAULT NULL,
  `recipient_name` varchar(200) NOT NULL,
  `recipient_name_marathi` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheme_recipients`
--

INSERT INTO `scheme_recipients` (`sr_no`, `recipient_id`, `scheme_id`, `recipient_name`, `recipient_name_marathi`, `created_at`) VALUES
(1, 'MH136166648', 1, 'Aanil Janan More', 'अनील जानन अधिक', '2025-03-15 13:15:09'),
(3, 'mh131313134533', 1, 'nikhil patil', 'निखिल पाटील', '2025-03-29 15:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `villager`
--

CREATE TABLE `villager` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `villager`
--

INSERT INTO `villager` (`id`, `name`, `email`, `mobile`, `password`, `address`, `created_at`) VALUES
(1, 'Subhash Raghav Jire', 'subhash@gmail.com', '7057256877', '$2y$10$T.BTXwcHGTq7s7Do542LO.aGa1Ja8jH/PkbWdCsJoUoznjgXhi8UW', 'KUSUMBA Gali No 2', '2025-03-07 14:35:25'),
(2, 'Ravindra Pandit Chavhan', 'ravi@gmail.com', '1234567890', '$2y$10$LSiTl60QRzgrbsjbzZmUw.j5lMhi4/sEtZOvkMgZvoHrx8F5Ptu1G', 'KUSUMBA Gali No 2', '2025-03-07 14:38:28'),
(3, 'Kunal narayan Patil', 'kunal@gmail.com', '0123456789', '$2y$10$qoWAHLtD7rIa4P2WDHx0CuPc1oaxugTl9DcfZpeD7U310VeKHcVcu', 'Kusumba Gali no.7', '2025-03-24 07:21:56'),
(4, 'Manisha Swapnil Shinde', 'manisha@gmail.com', '1234567890', '$2y$10$kefDONbThhkz.RuVd5q4FuBIU94DOXefMTOgglsBFcISWlucHjm36', 'kusumba Gali no.2 ', '2025-03-30 08:36:12'),
(5, 'Nikhil Nilesh Patil', 'nikhil@gmail.com', '7458785478', '$2y$10$mrZMSH0gifJl/ureNYlpr.hPm06MXWNKvpBXffVUykkclpkrOe7x6', 'kusumba Gali no3', '2025-04-03 09:51:23'),
(6, 'nitin mahesh patil', 'nitin@gmail.com', '7854785489', '$2y$10$k9R/LRuhP45DRzYHqJsz/.RSV2sZ8RkjFS/jqvQp7mjBmdx/sn.t2', 'kusumba gali no.4', '2025-04-07 07:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `villagers`
--

CREATE TABLE `villagers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `caste` varchar(100) DEFAULT NULL,
  `aadhaar_number` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') DEFAULT 'Single',
  `spouse_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `villagers`
--

INSERT INTO `villagers` (`id`, `name`, `address`, `dob`, `age`, `caste`, `aadhaar_number`, `gender`, `marital_status`, `spouse_name`, `created_at`) VALUES
(1, 'Rohit Sukhalal Patil', 'kusumba Gali no.2', '1999-05-05', 25, 'OBC', NULL, 'Male', 'Married', 'Prachi Manoj Patil', '2025-03-31 10:42:23'),
(2, 'Prachi Manoj Patil', 'Shirpur Pratap nagar.', '2003-06-10', 21, 'OBC', NULL, 'Female', 'Married', 'Rohit Sukhalal Patil', '2025-03-31 10:42:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approved_birth_certificates`
--
ALTER TABLE `approved_birth_certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approved_death_certificate`
--
ALTER TABLE `approved_death_certificate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approved_marriage`
--
ALTER TABLE `approved_marriage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `husband_aadhaar` (`husband_aadhaar`),
  ADD UNIQUE KEY `wife_aadhaar` (`wife_aadhaar`),
  ADD UNIQUE KEY `certificate_no` (`certificate_no`);

--
-- Indexes for table `approved_marriage_db`
--
ALTER TABLE `approved_marriage_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `birth_certificate`
--
ALTER TABLE `birth_certificate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `death_certificate`
--
ALTER TABLE `death_certificate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD UNIQUE KEY `aadhaar_number` (`aadhaar_number`);

--
-- Indexes for table `marriage_certificates`
--
ALTER TABLE `marriage_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_no` (`certificate_no`);

--
-- Indexes for table `marriage_certificate_db`
--
ALTER TABLE `marriage_certificate_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meetings_db`
--
ALTER TABLE `meetings_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_tax`
--
ALTER TABLE `property_tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheme_db`
--
ALTER TABLE `scheme_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheme_recipients`
--
ALTER TABLE `scheme_recipients`
  ADD PRIMARY KEY (`sr_no`),
  ADD UNIQUE KEY `recipient_id` (`recipient_id`),
  ADD KEY `scheme_id` (`scheme_id`);

--
-- Indexes for table `villager`
--
ALTER TABLE `villager`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `villagers`
--
ALTER TABLE `villagers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aadhaar_number` (`aadhaar_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approved_birth_certificates`
--
ALTER TABLE `approved_birth_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `approved_death_certificate`
--
ALTER TABLE `approved_death_certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approved_marriage`
--
ALTER TABLE `approved_marriage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approved_marriage_db`
--
ALTER TABLE `approved_marriage_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `birth_certificate`
--
ALTER TABLE `birth_certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `death_certificate`
--
ALTER TABLE `death_certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `marriage_certificates`
--
ALTER TABLE `marriage_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `marriage_certificate_db`
--
ALTER TABLE `marriage_certificate_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings_db`
--
ALTER TABLE `meetings_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `property_tax`
--
ALTER TABLE `property_tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `scheme_db`
--
ALTER TABLE `scheme_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `scheme_recipients`
--
ALTER TABLE `scheme_recipients`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `villager`
--
ALTER TABLE `villager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `villagers`
--
ALTER TABLE `villagers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scheme_recipients`
--
ALTER TABLE `scheme_recipients`
  ADD CONSTRAINT `scheme_recipients_ibfk_1` FOREIGN KEY (`scheme_id`) REFERENCES `scheme_db` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
