-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2024 at 11:38 AM
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
-- Database: `electovote`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_fname` varchar(50) NOT NULL,
  `admin_mname` varchar(50) DEFAULT NULL,
  `admin_lname` varchar(50) NOT NULL,
  `admin_username` varchar(50) NOT NULL,
  `admin_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_fname`, `admin_mname`, `admin_lname`, `admin_username`, `admin_password`) VALUES
('Justin', 'Lacandazo', 'Ramos', 'cedieboi', 'adminpassword');

-- --------------------------------------------------------

--
-- Table structure for table `candidate`
--

CREATE TABLE `candidate` (
  `candidate_id` varchar(20) NOT NULL,
  `candidate_img` longblob DEFAULT NULL,
  `candidate_fname` varchar(50) NOT NULL,
  `candidate_mname` varchar(50) DEFAULT NULL,
  `candidate_lname` varchar(50) NOT NULL,
  `party_list` varchar(50) NOT NULL,
  `position_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate`
--

INSERT INTO `candidate` (`candidate_id`, `candidate_img`, `candidate_fname`, `candidate_mname`, `candidate_lname`, `party_list`, `position_id`) VALUES
('1', NULL, 'Justin', 'Lacandazo', 'Ramos', 'Techno', 1),
('23', 0x433a2f78616d70702f6874646f63732f616465742d766f74696e672f43616e6469646174652f4c6542726f6e2e6a7067, 'LeBron', 'Raymone', 'James', 'Lakers', 1),
('3', NULL, 'test', '', 'test', 'test', 2),
('5', NULL, 'testing', 'sana', 'gumana', 'yehey', 3);

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL,
  `position_name` enum('President','Vice President','Councilor','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`position_id`, `position_name`) VALUES
(1, 'President'),
(2, 'Vice President'),
(3, 'Councilor');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` varchar(20) NOT NULL,
  `reference_id` varchar(20) NOT NULL,
  `student_fname` varchar(50) NOT NULL,
  `student_mname` varchar(50) DEFAULT NULL,
  `student_lname` varchar(50) NOT NULL,
  `student_year` enum('1st','2nd','3rd','4th') NOT NULL,
  `student_section` enum('1','1N','2','2N','3','4','5','Irregular') NOT NULL,
  `student_program` enum('BSIT','BSCS','','') NOT NULL,
  `birth_date` date NOT NULL,
  `student_email` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `reference_id`, `student_fname`, `student_mname`, `student_lname`, `student_year`, `student_section`, `student_program`, `birth_date`, `student_email`) VALUES
('101', 'R123', 'Joji', 'Sta', 'Maria', '1st', '1N', 'BSCS', '2000-05-15', 'john.doe@example.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_username`);

--
-- Indexes for table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`candidate_id`),
  ADD KEY `fk_positionid` (`position_id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `fk_positionid` FOREIGN KEY (`position_id`) REFERENCES `position` (`position_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
