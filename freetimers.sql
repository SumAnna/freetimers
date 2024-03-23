-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 23, 2024 at 05:03 PM
-- Server version: 11.2.2-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freetimers`
--

-- --------------------------------------------------------

--
-- Table structure for table `baskets`
--

DROP TABLE IF EXISTS `baskets`;
CREATE TABLE IF NOT EXISTS `baskets` (
  `basket_id` int(11) NOT NULL AUTO_INCREMENT,
  `bags_number` int(11) NOT NULL,
  `user_hash` varchar(255) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`basket_id`),
  UNIQUE KEY `user_hash` (`user_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_calculations`
--

DROP TABLE IF EXISTS `saved_calculations`;
CREATE TABLE IF NOT EXISTS `saved_calculations` (
  `calculation_id` int(11) NOT NULL AUTO_INCREMENT,
  `width` float NOT NULL,
  `length` float NOT NULL,
  `depth` float NOT NULL,
  `unit_id` int(11) NOT NULL,
  `depth_unit_id` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  `vat_rate` float NOT NULL,
  PRIMARY KEY (`calculation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
CREATE TABLE IF NOT EXISTS `units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(255) NOT NULL,
  `unit_short_name` varchar(255) NOT NULL,
  `depth_unit` tinyint(1) NOT NULL,
  `measurement_unit` tinyint(1) NOT NULL,
  `conversion_to_m` float NOT NULL,
  `conversion_to_cm` float NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `unit_short_name`, `depth_unit`, `measurement_unit`, `conversion_to_m`, `conversion_to_cm`) VALUES
(1, 'metre', 'm', 0, 1, 1, 100),
(2, 'centimetre', 'cm', 1, 0, 0.01, 1),
(3, 'foot', 'ft', 0, 1, 0.3048, 30.48),
(4, 'yard', 'yd', 0, 1, 0.9144, 91.44),
(5, 'inch', 'in', 1, 0, 0.0254, 2.54);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `saved_calculations`
--
ALTER TABLE `saved_calculations`
  ADD CONSTRAINT `saved_calculations_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `saved_calculations_ibfk_2` FOREIGN KEY (`depth_unit_id`) REFERENCES `units` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
