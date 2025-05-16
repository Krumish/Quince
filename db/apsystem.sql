-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 09:06 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `firstname`, `lastname`, `photo`, `created_on`) VALUES
(1, 'admin', '$2y$10$U4/qPW2j25anqXV55md94uA07ZZ/lECSQPvaDYalJIX9Oxj7H4INy', 'User', 'Admin', 'admin1.png', '2018-04-30');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time NOT NULL,
  `status` int(1) NOT NULL,
  `time_out` time NOT NULL,
  `num_hr` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `date`, `time_in`, `status`, `time_out`, `num_hr`) VALUES
(87, 1, '2020-05-08', '01:40:51', 1, '00:00:00', 0),
(88, 1, '2025-04-19', '22:33:07', 0, '22:33:15', 4.55),
(89, 5, '2025-04-26', '11:40:30', 0, '23:44:52', 11.066666666667),
(90, 3, '2025-04-26', '11:40:41', 0, '23:44:55', 11.066666666667),
(91, 6, '2025-04-26', '11:40:45', 0, '23:44:59', 11.066666666667),
(92, 7, '2025-04-26', '11:40:49', 0, '23:45:03', 11.066666666667),
(93, 1, '2025-05-16', '10:00:00', 1, '19:00:00', 8),
(94, 1, '2025-05-17', '10:00:00', 1, '19:15:00', 8),
(95, 1, '2025-05-18', '10:00:00', 1, '07:30:00', 2.5),
(96, 1, '2025-05-19', '10:00:00', 1, '19:30:00', 8),
(97, 1, '2025-05-20', '10:00:00', 1, '19:30:00', 8),
(98, 1, '2025-05-21', '10:00:00', 1, '19:00:00', 8),
(99, 1, '2025-05-22', '10:00:00', 1, '19:30:00', 8),
(100, 1, '2025-05-23', '10:00:00', 1, '19:30:00', 8),
(101, 1, '2025-05-24', '10:00:00', 1, '19:30:00', 8),
(102, 1, '2025-05-25', '10:00:00', 1, '07:30:00', 2.5),
(103, 1, '2025-05-26', '10:00:00', 1, '19:00:00', 8),
(104, 1, '2025-05-27', '09:30:00', 1, '19:00:00', 8),
(105, 1, '2025-05-28', '10:00:00', 1, '19:00:00', 8),
(106, 1, '2025-05-29', '10:00:00', 1, '19:00:00', 8),
(107, 1, '2025-05-30', '09:15:00', 1, '19:00:00', 8),
(108, 1, '2025-05-31', '09:45:00', 1, '19:15:00', 8),
(109, 1, '2025-06-01', '10:15:00', 0, '19:30:00', 7.75),
(110, 1, '2025-06-02', '09:30:00', 1, '19:00:00', 8),
(111, 1, '2025-06-03', '10:30:00', 0, '19:30:00', 7.5),
(112, 1, '2025-06-04', '10:00:00', 1, '07:00:00', 3),
(113, 1, '2025-05-05', '09:30:00', 1, '19:30:00', 8),
(114, 1, '2025-06-05', '10:00:00', 1, '19:00:00', 8),
(115, 1, '2025-06-06', '10:00:00', 1, '19:30:00', 8),
(116, 1, '2025-06-07', '09:45:00', 1, '19:00:00', 8),
(117, 1, '2025-06-08', '10:00:00', 1, '19:30:00', 8),
(118, 1, '2025-06-09', '10:00:00', 1, '19:30:00', 8),
(119, 1, '2025-06-10', '09:30:00', 1, '19:30:00', 8),
(120, 1, '2025-06-11', '10:15:00', 0, '20:30:00', 7.75),
(121, 1, '2025-06-12', '10:00:00', 1, '19:45:00', 8),
(122, 1, '2025-06-13', '10:00:00', 1, '19:45:00', 8),
(123, 5, '2025-04-17', '12:00:00', 1, '21:45:00', 8),
(124, 3, '2025-04-18', '12:00:00', 1, '09:15:00', 2.75),
(125, 7, '2025-04-18', '12:00:00', 1, '21:00:00', 8),
(126, 1, '2025-06-14', '10:00:00', 1, '20:00:00', 8),
(127, 1, '2025-06-15', '10:00:00', 1, '19:00:00', 8),
(128, 1, '2025-06-16', '10:00:00', 1, '19:15:00', 8),
(129, 8, '2025-05-16', '12:00:00', 1, '21:00:00', 8),
(130, 8, '2025-05-17', '12:00:00', 1, '21:00:00', 8),
(131, 8, '2025-05-18', '12:00:00', 1, '21:00:00', 8),
(132, 8, '2025-05-19', '12:00:00', 1, '21:00:00', 8),
(133, 8, '2025-05-20', '12:00:00', 1, '21:00:00', 8),
(134, 8, '2025-05-21', '12:00:00', 1, '21:00:00', 8),
(135, 8, '2025-05-22', '12:00:00', 1, '21:00:00', 8),
(136, 8, '2025-05-23', '12:00:00', 1, '21:00:00', 8),
(137, 8, '2025-05-24', '12:00:00', 1, '21:00:00', 8),
(138, 8, '2025-05-25', '12:15:00', 0, '21:00:00', 7.75),
(139, 8, '2025-05-26', '12:00:00', 1, '21:00:00', 8),
(140, 8, '2025-05-27', '12:00:00', 1, '21:00:00', 8),
(141, 8, '2025-05-28', '12:00:00', 1, '21:00:00', 8),
(142, 8, '2025-05-29', '12:00:00', 1, '21:00:00', 8),
(143, 8, '2025-05-30', '12:00:00', 1, '21:00:00', 8),
(144, 8, '2025-05-31', '12:00:00', 1, '21:00:00', 8),
(145, 8, '2025-06-01', '12:00:00', 1, '21:00:00', 8),
(146, 8, '2025-05-02', '12:00:00', 1, '21:00:00', 8),
(147, 8, '2025-06-03', '12:00:00', 1, '21:00:00', 8),
(148, 8, '2025-06-04', '12:00:00', 1, '21:00:00', 8),
(149, 8, '2025-06-05', '12:00:00', 1, '21:00:00', 8),
(150, 8, '2025-06-06', '12:00:00', 1, '21:00:00', 8),
(151, 8, '2025-06-07', '12:00:00', 1, '21:00:00', 8),
(152, 8, '2025-06-08', '12:00:00', 1, '21:00:00', 8),
(153, 8, '2025-06-09', '12:00:00', 1, '21:00:00', 8),
(154, 8, '2025-06-10', '12:00:00', 1, '21:00:00', 8),
(155, 8, '2025-06-11', '12:00:00', 1, '21:00:00', 8),
(156, 8, '2025-06-12', '12:00:00', 1, '21:00:00', 8),
(157, 8, '2025-06-13', '12:00:00', 1, '21:00:00', 8),
(158, 8, '2025-06-14', '12:00:00', 1, '21:00:00', 8),
(159, 8, '2025-06-15', '12:00:00', 1, '21:00:00', 8),
(160, 8, '2025-06-16', '12:00:00', 1, '21:00:00', 8);

-- --------------------------------------------------------

--
-- Table structure for table `cashadvance`
--

CREATE TABLE `cashadvance` (
  `id` int(11) NOT NULL,
  `date_advance` date NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cashadvance`
--

INSERT INTO `cashadvance` (`id`, `date_advance`, `employee_id`, `amount`) VALUES
(11, '2025-05-16', '1', 500);

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `deductions`
--

INSERT INTO `deductions` (`id`, `description`, `amount`) VALUES
(1, 'SSS', 200),
(2, 'Pagibig', 150),
(3, 'PhilHealth', 100);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `birthdate` date NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `position_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `password`, `firstname`, `middlename`, `lastname`, `address`, `birthdate`, `contact_info`, `gender`, `position_id`, `schedule_id`, `photo`, `created_on`) VALUES
(1, 'ABC123456789', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Rudy', '', 'Bermoy', 'Antipolo City', '2004-02-03', '09079373999', 'Male', 5, 4, 'bussiness-man.png', '2018-04-28'),
(3, 'DYE473869250', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Jonah', 'Barrameda', 'Juarez', 'Surigao City', '1992-05-02', '09123456789', 'Female', 2, 6, 'man (1).png', '2018-04-30'),
(5, 'AMY852031749', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Lyndon', 'Ydur', 'Cruz', 'Manila', '1994-06-22', '0912345672', 'Female', 4, 6, 'woman.png', '2025-04-26'),
(6, 'NSD810469735', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Mich', NULL, 'Mendieta', 'Antipolo', '2005-11-16', '0912332145', 'Male', 7, 6, 'man.png', '2025-04-26'),
(7, 'UBC957810432', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Mae', 'Legaspi', 'Marigondon', 'Marikina', '2003-09-16', '09154314123', 'Female', 2, 6, 'human.png', '2025-04-26'),
(8, 'GDP685934721', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Gratina', NULL, 'Mendoza', 'Bulacan', '2004-10-20', '09342342352', 'Female', 1, 6, 'profile.png', '2025-04-26'),
(9, 'NVU895063724', '$2y$10$VGe6mo8zsOzqBkSgKy3VV.lLEAqxEe4ES6RgVuv9.3uE.RSGjnH/a', 'Kish', 'Sumang', 'Leyble', 'antipolo city', '2025-05-01', '09079373999', 'Male', 1, 1, 'guwapo2.jpg', '2025-05-16');

-- --------------------------------------------------------

--
-- Table structure for table `overtime`
--

CREATE TABLE `overtime` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(15) NOT NULL,
  `hours` double NOT NULL,
  `rate` double NOT NULL,
  `date_overtime` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `overtime`
--

INSERT INTO `overtime` (`id`, `employee_id`, `hours`, `rate`, `date_overtime`) VALUES
(9, '1', 10.833333333333, 150, '2025-05-16');

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`id`, `description`, `rate`) VALUES
(1, 'HR Manager', 250),
(2, 'Sales Executive', 165),
(3, 'Account Manager', 125),
(4, 'Project Manager', 185),
(5, 'Installer', 150),
(6, 'Sewer', 150),
(7, 'Electrician', 125),
(8, 'Chief Financial Officer', 150);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `time_in`, `time_out`) VALUES
(1, '07:00:00', '16:00:00'),
(2, '08:00:00', '17:00:00'),
(3, '09:00:00', '18:00:00'),
(4, '10:00:00', '19:00:00'),
(6, '12:00:00', '21:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashadvance`
--
ALTER TABLE `cashadvance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overtime`
--
ALTER TABLE `overtime`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `cashadvance`
--
ALTER TABLE `cashadvance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `overtime`
--
ALTER TABLE `overtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
