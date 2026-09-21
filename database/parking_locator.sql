-- =======================================================
-- Smart Parking Locator (Kathmandu) - Database Dump
-- Compatible with XAMPP (Apache + MySQL / MariaDB) & phpMyAdmin
-- Default XAMPP credentials: User: root, Password: (none)
-- =======================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database: `parking_locator`
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `parking_locator` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `parking_locator`;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dumping data for table `admins`
-- --------------------------------------------------------
INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$FcYWiEB53kt68JdNVVs9S.bO//nIEhqh8VvSj6oHv9rYt6AQPElcS', NOW())
ON DUPLICATE KEY UPDATE `username` = `username`;

-- --------------------------------------------------------
-- Table structure for table `parking_facilities`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parking_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `address` varchar(255) NOT NULL,
  `area` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `payment_type` enum('free','paid') NOT NULL DEFAULT 'paid',
  `parking_fee` varchar(100) DEFAULT NULL,
  `vehicle_type` enum('car','motorcycle','both') NOT NULL DEFAULT 'both',
  `total_capacity` int(11) NOT NULL DEFAULT 50,
  `available_spaces` int(11) NOT NULL DEFAULT 20,
  `capacity_bike` int(11) NOT NULL DEFAULT 30,
  `available_bike` int(11) NOT NULL DEFAULT 12,
  `capacity_car` int(11) NOT NULL DEFAULT 20,
  `available_car` int(11) NOT NULL DEFAULT 8,
  `opening_time` time DEFAULT '07:00:00',
  `closing_time` time DEFAULT '21:00:00',
  `contact` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_verified` date NOT NULL,
  `last_spaces_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_area` (`area`),
  KEY `idx_payment` (`payment_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dumping data for table `parking_facilities`
-- Kathmandu Verified Parking Locations
-- --------------------------------------------------------
INSERT INTO `parking_facilities` (`id`, `name`, `address`, `area`, `latitude`, `longitude`, `payment_type`, `parking_fee`, `vehicle_type`, `total_capacity`, `available_spaces`, `capacity_bike`, `available_bike`, `capacity_car`, `available_car`, `opening_time`, `closing_time`, `contact`, `description`, `image`, `status`, `last_verified`, `created_at`) VALUES
(1, 'Chhaya Center Underground Parking', 'Amrit Marg, Thamel', 'Thamel', 27.71490000, 85.31220000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 120, 38, 80, 26, 40, 12, '06:00:00', '23:00:00', '01-4256789', 'Multi-level basement parking with security personnel, lighting, and CCTV monitoring. Convenient for visitors to central Thamel and Chhaya Center shopping complex.', 'chhaya_center.jpg', 'active', '2026-09-01', NOW()),
(2, 'Thamel Marg Open Parking Area', 'Thamel Marg, Near Kathmandu Guest House', 'Thamel', 27.71610000, 85.31050000, 'paid', 'Rs. 25/hr (Bike)', 'motorcycle', 60, 14, 60, 14, 0, 0, '07:00:00', '22:00:00', '9841234567', 'Designated outdoor two-wheeler parking lot managed by local ward committee for tourist area visitors.', NULL, 'active', '2026-08-28', NOW()),
(3, 'Dharahara Underground Smart Parking', 'Khichapokhari, New Road', 'New Road', 27.70060000, 85.31220000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', 250, 95, 180, 70, 70, 25, '06:00:00', '22:00:00', '01-4221144', 'Spacious modern multi-level underground parking facility constructed near reconstructed Dharahara with dedicated lanes for two-wheelers and four-wheelers.', 'dharahara_parking.jpg', 'active', '2026-09-04', NOW()),
(4, 'Khichapokhari Municipal Bike Stand', 'Khichapokhari Marg', 'New Road', 27.70280000, 85.31170000, 'paid', 'Rs. 20/hr', 'motorcycle', 80, 9, 80, 9, 0, 0, '08:00:00', '20:30:00', NULL, 'Open municipal motorcycle stand located within walking distance of Bishal Bazar and New Road gate.', NULL, 'active', '2026-09-02', NOW()),
(5, 'Baneshwor Complex Customer Parking', 'New Baneshwor Chowk', 'Baneshwor', 27.69180000, 85.34210000, 'free', 'Free for customers', 'both', 50, 18, 35, 14, 15, 4, '08:00:00', '21:00:00', '01-4498877', 'Ground level dedicated customer parking lot situated behind commercial offices and banks at Baneshwor junction.', NULL, 'active', '2026-08-20', NOW()),
(6, 'Eyeplex Mall Basement Parking', 'Main Road, New Baneshwor', 'Baneshwor', 27.69020000, 85.33750000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 90, 27, 60, 19, 30, 8, '08:30:00', '22:30:00', '01-4471234', 'Covered underground parking for moviegoers and commercial shoppers with paved ramps and guard assistance.', 'eyeplex_mall.jpg', 'active', '2026-09-05', NOW()),
(7, 'Putalisadak Commercial Complex Parking', 'Putalisadak Main Road', 'Putalisadak', 27.70580000, 85.32160000, 'paid', 'Rs. 40/hr (Car), Rs. 20/hr (Bike)', 'both', 45, 6, 30, 4, 15, 2, '07:30:00', '20:00:00', '9851098765', 'Off-street parking lot serving educational consultancies, computer hardware stores, and offices along Putalisadak corridor.', NULL, 'active', '2026-08-15', NOW()),
(8, 'City Center Multi-Story Parking', 'Kamalpokhari, Near Putalisadak', 'Putalisadak', 27.70880000, 85.32430000, 'paid', 'Rs. 50/hr (Car), Rs. 25/hr (Bike)', 'both', 150, 52, 100, 36, 50, 16, '09:00:00', '22:00:00', '01-4011555', 'Covered multi-level parking complex inside City Center shopping mall with elevator access and automated barrier gates.', 'city_center.jpg', 'active', '2026-09-03', NOW()),
(9, 'Kalanki Underpass Roadside Parking Bay', 'Kalanki Chowk West', 'Kalanki', 27.69370000, 85.28180000, 'free', 'Free public bay', 'both', 30, 0, 20, 0, 10, 0, '06:00:00', '20:00:00', NULL, 'Designated public roadside parking bay designated by traffic police near Kalanki junction for short halts.', NULL, 'active', '2026-08-10', NOW()),
(10, 'Makalu Complex Parking', 'Ring Road, Kalanki', 'Kalanki', 27.69520000, 85.28340000, 'paid', 'Rs. 30/hr (Car), Rs. 15/hr (Bike)', 'both', 40, 15, 25, 10, 15, 5, '07:00:00', '21:00:00', '9803123456', 'Paved commercial ground parking facility suitable for commuter vehicles and local shoppers.', NULL, 'active', '2026-08-30', NOW()),
(11, 'Labim Mall Underground Parking', 'Pulchowk, Lalitpur', 'Patan', 27.67780000, 85.31680000, 'paid', 'Rs. 60/hr (Car), Rs. 30/hr (Bike)', 'both', 180, 64, 120, 45, 60, 19, '08:00:00', '23:00:00', '01-5536789', 'Modern double-level basement facility with digital ticket validation, well-marked bays, and direct elevator access to mall floors.', 'labim_mall.jpg', 'active', '2026-09-05', NOW()),
(12, 'Mangal Bazar Heritage Parking Lot', 'Patan Durbar Square West', 'Patan', 27.67290000, 85.32450000, 'paid', 'Rs. 25/hr (Bike), Rs. 50/hr (Car)', 'both', 70, 11, 50, 8, 20, 3, '07:00:00', '20:00:00', '01-5522334', 'Designated municipal vehicle parking lot for visitors to Patan Durbar Square and surrounding handicraft markets.', NULL, 'active', '2026-09-02', NOW()),
(13, 'Bhatbhateni Maharajgunj Parking', 'Narayan Gopal Chowk, Maharajgunj', 'Maharajgunj', 27.73620000, 85.33120000, 'free', 'Free with store purchase', 'both', 110, 42, 70, 28, 40, 14, '07:30:00', '21:30:00', '01-4720123', 'Dedicated multi-story and ground customer parking with trolley bays and attendant assistance.', 'bhatbhateni_parking.jpg', 'active', '2026-09-01', NOW()),
(14, 'TUTH Hospital Visitor Parking', 'Teaching Hospital Gate, Maharajgunj', 'Maharajgunj', 27.73480000, 85.33010000, 'paid', 'Rs. 20/hr (Bike), Rs. 40/hr (Car)', 'both', 100, 16, 65, 11, 35, 5, '00:00:00', '23:59:59', '01-4412303', 'Round-the-clock hospital visitor parking lot with paved sections for emergency vehicles, staff, and patient visitors.', NULL, 'active', '2026-08-25', NOW()),
(15, 'Basantapur Public Scooter Stand', 'Ganga Path, Basantapur', 'Kathmandu Durbar Square', 27.70320000, 85.30890000, 'paid', 'Rs. 20/hr', 'motorcycle', 85, 3, 85, 3, 0, 0, '07:00:00', '21:00:00', NULL, 'Open air motorcycle and scooter parking space adjacent to ancient Durbar Square heritage perimeter.', NULL, 'active', '2026-09-04', NOW())
ON DUPLICATE KEY UPDATE `name` = `name`;

-- --------------------------------------------------------
-- Table structure for table `parking_reports`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parking_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parking_id` int(11) NOT NULL,
  `issue_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `reporter_contact` varchar(100) DEFAULT NULL,
  `status` enum('pending','reviewed','resolved') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parking_id` (`parking_id`),
  CONSTRAINT `fk_parking_report_facility` FOREIGN KEY (`parking_id`) REFERENCES `parking_facilities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Dumping data for table `parking_reports`
-- --------------------------------------------------------
INSERT INTO `parking_reports` (`id`, `parking_id`, `issue_type`, `message`, `reporter_contact`, `status`, `created_at`) VALUES
(1, 3, 'Fee Discrepancy', 'The rate for four-wheelers was updated to Rs. 40/hr during weekend peak hours.', 'ramesh@example.com', 'pending', NOW()),
(2, 1, 'Timings Verification', 'Closing time on public holidays extended up to 23:30.', 'sita@example.com', 'reviewed', NOW())
ON DUPLICATE KEY UPDATE `issue_type` = `issue_type`;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
