-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for autoparts_db
CREATE DATABASE IF NOT EXISTS `autoparts_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `autoparts_db`;

-- Dumping structure for table autoparts_db.address
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `city` varchar(50) DEFAULT NULL,
  `brgy` varchar(50) DEFAULT NULL,
  `shipping` float DEFAULT NULL,
  `city_code` varchar(50) DEFAULT NULL,
  `brgy_code` varchar(50) DEFAULT NULL,
  `estimated` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.address: ~2 rows (approximately)
INSERT INTO `address` (`id`, `city`, `brgy`, `shipping`, `city_code`, `brgy_code`, `estimated`, `created_at`, `updated_at`, `active`) VALUES
	(1, 'Himamaylan', 'Libacao', 200, '064510000', '064510007', 1, '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1),
	(5, 'Cauayan', 'Caliling', 180, '064507000', '064507009', 1, '2026-06-05 10:58:08', '2026-06-16 19:38:53', 1);

-- Dumping structure for table autoparts_db.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `details` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.category: ~7 rows (approximately)
INSERT INTO `category` (`id`, `name`, `details`, `created_at`, `updated_at`, `active`) VALUES
	(1, 'Brakes', NULL, NULL, NULL, 1),
	(2, 'Engine Parts', NULL, NULL, NULL, 1),
	(3, 'Suspension', NULL, NULL, NULL, 1),
	(4, 'Electrical', NULL, NULL, NULL, 1),
	(5, 'Filters', NULL, NULL, NULL, 1),
	(6, 'Exhaust', NULL, NULL, NULL, 1),
	(7, 'Tire', NULL, NULL, NULL, 1);

-- Dumping structure for table autoparts_db.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `string` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.configs: ~2 rows (approximately)
INSERT INTO `configs` (`id`, `title`, `string`, `created_at`, `updated_at`, `active`) VALUES
	(1, 'contact', '09104758395', '2026-06-18 21:06:18', '2026-06-18 21:06:19', 1),
	(2, 'email', 'tyronemalocon@gmail.com', '2026-06-18 21:07:28', '2026-06-18 21:07:53', 1);

-- Dumping structure for table autoparts_db.customer
CREATE TABLE IF NOT EXISTS `customer` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  `fulladdress` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.customer: ~14 rows (approximately)
INSERT INTO `customer` (`id`, `fullname`, `contact`, `address`, `username`, `password`, `email`, `created_at`, `updated_at`, `active`, `fulladdress`) VALUES
	(54, 'Tyrone L Malocon', '923819231723', 'Purok 2 Libacao Himamaylan City', 'tyrone@clearmindai.com', '123', 'tyrone@clearmindai.com', '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1, NULL),
	(55, 'Yro Ez', '923819231723', 'Binalbagan Negros Occidental', 'tyrone+1@clearmindai.com', '123', 'tyrone+1@clearmindai.com', '2026-06-05 10:58:39', '2026-06-05 10:58:39', 1, NULL),
	(56, 'Prince Yro', '923819231723', 'Libacao Himamaylan City', 'tyronemalocon+9@gmail.com', '123', 'tyronemalocon+9@gmail.com', '2026-06-05 10:59:16', '2026-06-05 10:59:16', 1, NULL),
	(57, 'Tyrone L Malocon', '923819231723', 'Libacao Himamaylan City', 'tyrone+5@clearmindai.com', '123', 'tyrone+5@clearmindai.com', '2026-06-05 10:59:42', '2026-06-05 10:59:42', 1, NULL),
	(58, 'Alex Turner', '098312214', 'Himamaylan City', 'customer@gmail.com', '123', 'customer@gmail.com', '2026-06-05 11:24:47', '2026-06-05 11:24:47', 1, NULL),
	(59, 'Bryan Norman Jr.', '923819231723', 'purok something', 'tyrone+34@clearmindai.com', '123', 'tyrone+34@clearmindai.com', '2026-06-06 22:10:20', '2026-06-06 22:10:20', 1, NULL),
	(60, 'Tyr the one', '0981237', 'Purok 1, Libacao, Himamaylan City, Negros Occident', 'admin+1@gmail.com', '123', 'admin+1@gmail.com', '2026-06-08 13:12:12', '2026-06-08 13:12:12', 1, NULL),
	(61, 'Tyr the one', '0981237', 'Zone 3, Payao, Binalbagan, Negros Occidental', 'admin+16@gmail.com', '123', 'admin+16@gmail.com', '2026-06-08 13:20:46', '2026-06-08 13:20:46', 1, NULL),
	(62, 'Tyr the one', '0981237', 'Camangcamang, Isabela, Negros Occidental', 'admin+134@gmail.com', '123', 'admin+134@gmail.com', '2026-06-08 13:45:15', '2026-06-08 13:45:15', 1, NULL),
	(63, 'Tambok', '09977892013', 'Libacao, Himamaylan City, Negros Occidental', 'tyronemalocon+22@gmail.com', '123', 'tyronemalocon+22@gmail.com', '2026-06-09 10:13:13', '2026-06-19 10:40:56', 1, 'purok 2, Libacao, Himamaylan City, Negros Occident'),
	(64, 'Tyr the two', '0981237', 'Camugao, Kabankalan City, Negros Occidental', 'tyronemalocon@gmail.com', '123', 'tyronemalocon@gmail.com', '2026-06-10 10:27:59', '2026-06-10 10:27:59', 1, 'purok 5, Camugao, Kabankalan City, Negros Occident'),
	(65, 'Tyrone Emz', '09231766252', 'Libacao, Himamaylan City, Negros Occidental', 'tyrone+11@clearmindai.com', '93106', 'tyrone+11@clearmindai.com', '2026-06-10 10:39:34', '2026-06-10 10:39:34', 1, 'purok 2, Libacao, Himamaylan City, Negros Occident'),
	(66, 'Tyrone Emz', '0928376634', 'Libacao, Himamaylan City, Negros Occidental', 'tmalocon@iom.int', '5214', 'tmalocon@iom.int', '2026-06-10 10:44:00', '2026-06-10 10:44:00', 1, 'Purok 2, Libacao, Himamaylan City, Negros Occident'),
	(67, 'Tyrone L Malocon', '923819231723', 'Caliling, Cauayan, Negros Occidental', 'tyrone+1005@clearmindai.com', '62110', 'tyrone+1005@clearmindai.com', '2026-06-17 08:57:17', '2026-06-17 08:57:17', 1, 'zone 5, Caliling, Cauayan, Negros Occidental'),
	(79, 'KYG_260619125020', '-', 'kyg@gmail.com', '-', '', '', '2026-06-19 12:50:20', '2026-06-19 12:50:20', 1, '-');

-- Dumping structure for table autoparts_db.inventory
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `stock_id` varchar(50) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.inventory: ~25 rows (approximately)
INSERT INTO `inventory` (`id`, `product_id`, `quantity`, `stock_id`, `supplier_id`, `created_at`, `updated_at`, `active`) VALUES
	(1, 5, 45, NULL, 43, NULL, NULL, 1),
	(2, 5, 43, NULL, 232, '2026-05-29 09:03:21', NULL, 1),
	(3, 5, 11, NULL, 11, '0000-00-00 00:00:00', NULL, 1),
	(4, 5, 22, NULL, 22, '2026-05-29 17:06:06', NULL, 1),
	(5, 5, 4, NULL, 23, '2026-05-29 17:52:43', NULL, 1),
	(6, 6, 1, NULL, 3, '2026-05-29 18:12:32', NULL, 1),
	(7, 6, 1, NULL, 3, '2026-05-29 18:12:50', NULL, 1),
	(8, 6, 3, NULL, 2, '2026-05-29 18:27:17', NULL, 1),
	(9, 6, 3, NULL, 2, '2026-05-29 18:56:10', NULL, 1),
	(10, 7, 10, NULL, 0, '2026-05-29 19:17:04', NULL, 1),
	(11, 8, 32, NULL, 0, '2026-05-29 19:17:09', NULL, 1),
	(12, 9, 2, NULL, 2, '2026-05-29 19:17:19', NULL, 1),
	(13, 9, 40, NULL, 0, '2026-05-29 19:17:26', NULL, 1),
	(14, 9, 3, NULL, 2, '2026-05-30 21:48:00', NULL, 1),
	(15, 9, 1, NULL, 1, '2026-05-30 21:48:24', NULL, 1),
	(16, 8, 3, NULL, 1, '2026-05-30 21:48:45', NULL, 1),
	(17, 8, 1, NULL, 1, '2026-05-30 21:48:57', NULL, 1),
	(18, 8, 2, NULL, 1, '2026-05-30 21:49:41', NULL, 1),
	(19, 8, 2, NULL, 1, '2026-05-30 21:59:31', NULL, 1),
	(20, 5, 2, NULL, 1, '2026-05-30 22:01:39', NULL, 1),
	(21, 5, 3, NULL, 3, '2026-06-01 14:24:03', NULL, 1),
	(22, 6, 2, NULL, 1, '2026-06-03 10:59:23', NULL, 1),
	(23, 6, 0, NULL, 1, '2026-06-03 11:02:09', NULL, 1),
	(24, 5, 0, NULL, 1, '2026-06-03 13:07:01', NULL, 1),
	(25, 5, 3, NULL, 1, '2026-06-03 18:10:47', NULL, 1),
	(26, 10, 10, NULL, 1, '2026-06-04 20:14:15', NULL, 1),
	(27, 10, 3, NULL, 1, '2026-06-07 13:43:14', NULL, 1),
	(28, 11, 5, NULL, 1, '2026-06-13 20:40:47', NULL, 1),
	(29, 13, 7, NULL, 1, '2026-06-15 20:48:56', NULL, 1),
	(30, 12, 2, NULL, 1, '2026-06-15 20:49:36', NULL, 1),
	(31, 12, 23, NULL, 1, '2026-06-18 18:24:39', '2026-06-18 18:24:39', 1);

-- Dumping structure for table autoparts_db.logs
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `message` varchar(800) DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.logs: ~0 rows (approximately)

-- Dumping structure for table autoparts_db.photo
CREATE TABLE IF NOT EXISTS `photo` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `path` text DEFAULT NULL,
  `alt` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  `size` float DEFAULT NULL,
  `realpath` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.photo: ~3 rows (approximately)
INSERT INTO `photo` (`id`, `path`, `alt`, `created_at`, `updated_at`, `active`, `size`, `realpath`) VALUES
	(6, '/ctrstorage/products/SUF3EI260609093756.png', 'Air Filter for car', '2026-06-09 09:37:56', '2026-06-09 09:37:56', 1, 9.56, NULL),
	(7, '/ctrstorage/products/WE1AJZ260615084429.jpg', 'break lever', '2026-06-15 20:44:29', '2026-06-15 20:44:29', 1, 6.84, NULL),
	(8, '/ctrstorage/products/W52TC1260615084750.png', '5 Best Tires for your Car by Brands - XL Race Part', '2026-06-15 20:47:50', '2026-06-15 20:47:50', 1, 37.05, NULL);

-- Dumping structure for table autoparts_db.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `details` varchar(50) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  `category` int(11) DEFAULT NULL,
  `image` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.product: ~9 rows (approximately)
INSERT INTO `product` (`id`, `name`, `details`, `price`, `created_at`, `updated_at`, `active`, `category`, `image`, `added_by`) VALUES
	(5, 'Ceramic Brake Pads', '1', 20, '2026-05-29 12:44:26', '2026-06-03 18:10:47', 1, 1, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHVTw6f3RkZN09baKEFfQbhTU0KF4W_7QCaw&s', NULL),
	(6, 'D1250m D1345 Hot Sale Model Disc Semi-Metallic', 'Details', 48, '2026-05-29 18:07:53', '2026-06-03 11:02:09', 1, 1, 'https://image.made-in-china.com/202f0j00FuDbHRaqhnzs/D1250m-D1345-Hot-Sale-Model-Disc-Semi-Metallic-Brake-Pads-for-Nissan-Versa.webp', NULL),
	(7, 'Motorcycle Exhaust Systems', 'Good with High Performance', 200, '2026-05-29 19:10:47', '2026-05-29 19:10:47', 1, 6, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRdgL3c0CRxhJM1gD16q8whKIkyoPiUOlwSww&s', NULL),
	(8, 'Best Selling Motorcycle Exhaust Muffler Modified', 'Details', 500, '2026-05-30 21:48:57', '2026-06-17 19:47:26', 1, 6, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ91c2x70kwpCSz9KnuF0u3k1AMf7yab_4SXA&s', NULL),
	(9, 'Air Filter', 'air filter', 100, '2026-05-30 21:48:24', '2026-05-29 19:12:28', 1, 5, 'https://cdn.pgfilters.com/uploads/2023/02/What-is-the-Oil-Filters-Primary-Job_-1000x675-1.jpg', NULL),
	(10, 'JE, RevTech, 3.813 in. Bore, 2 Cyl. Kit', 'Shop High Quality RevTech Engine Piston Kit With 9', 800, '2026-06-04 20:04:29', '2026-06-07 13:43:14', 1, 2, 'https://www.jepistons.com/wp-content/uploads/2023/07/JEP-Piston-620-002-LEF_2000x2000-4.jpg', 1),
	(11, 'Car Air filter', 'Bakod', 1400, '2026-06-09 09:40:39', '2026-06-13 20:40:47', 1, 5, '/ctrstorage/products/SUF3EI260609093756.png', 1),
	(12, 'Brake Lever Mtb at Joshua Allingham blog', 'something', 700, '2026-06-15 20:45:01', '2026-06-18 18:24:39', 1, 1, '/ctrstorage/products/WE1AJZ260615084429.jpg', 1),
	(13, '5 Best Tires for your Car by Brands - XL Race Part', 'Large', 4500, '2026-06-15 20:48:41', '2026-06-15 20:48:56', 1, 7, '/ctrstorage/products/W52TC1260615084750.png', 1);

-- Dumping structure for table autoparts_db.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) DEFAULT NULL,
  `details` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.role: ~2 rows (approximately)
INSERT INTO `role` (`id`, `role`, `details`, `created_at`, `updated_at`, `active`) VALUES
	(2, 'Cashier', NULL, '2026-06-06 21:00:03', NULL, 1),
	(3, 'Rider', NULL, '2026-06-06 21:00:14', NULL, 1);

-- Dumping structure for table autoparts_db.supplier
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.supplier: ~0 rows (approximately)
INSERT INTO `supplier` (`id`, `name`, `address`, `contact`, `created_at`, `updated_at`, `active`) VALUES
	(1, 'New Supplier', 'Binalbagan Negros Occidental', '09977846573', NULL, NULL, 1);

-- Dumping structure for table autoparts_db.transaction
CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(50) DEFAULT NULL,
  `subtotal` float DEFAULT NULL,
  `shipping` float DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  `status` int(11) DEFAULT 0,
  `remarks` varchar(50) DEFAULT NULL,
  `rider` int(11) DEFAULT NULL,
  `date_delivered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.transaction: ~15 rows (approximately)
INSERT INTO `transaction` (`id`, `transaction_code`, `subtotal`, `shipping`, `total_price`, `customer_id`, `created_at`, `updated_at`, `active`, `status`, `remarks`, `rider`, `date_delivered`) VALUES
	(24, 'BBD035922231CA0', 1540, 200, 1740, 54, '2026-06-05 10:58:08', '2026-06-08 20:12:08', 1, 3, NULL, 3, NULL),
	(25, '521162460261988', 600, 200, 800, 55, '2026-06-05 10:58:39', '2026-06-06 22:08:39', 1, 3, '', NULL, NULL),
	(26, 'C0E7352111BA706', 100, 200, 300, 56, '2026-06-05 10:59:16', '2026-06-19 10:39:23', 1, 2, '', 3, '2026-06-19 10:39:23'),
	(27, '21005A9E52D5D61', 40, 200, 240, 57, '2026-06-05 10:59:42', '2026-06-06 22:08:30', 1, 2, '', NULL, NULL),
	(28, '3C8A51690C44CD2', 520, 200, 720, 58, '2026-06-05 11:24:47', '2026-06-18 20:16:49', 1, 3, NULL, 2, '2026-06-18 20:16:49'),
	(29, '9A5823E54EA8574', 300, 200, 500, 59, '2026-06-06 22:10:20', '2026-06-06 22:10:20', 1, 0, NULL, NULL, NULL),
	(30, '212D8D70B5773AB', 20, 200, 220, 60, '2026-06-08 13:12:12', '2026-06-18 20:17:02', 1, 3, NULL, 2, '2026-06-18 20:17:02'),
	(31, '199B10155602C11', 20, 200, 220, 61, '2026-06-08 13:20:46', '2026-06-18 20:17:08', 1, 3, NULL, 2, '2026-06-18 20:17:08'),
	(32, 'B6726A11EB23565', 20, 200, 220, 62, '2026-06-08 13:45:15', '2026-06-08 19:58:12', 1, 7, 'because of ssomething', 2, NULL),
	(33, 'A872A23197D2C0C', 500, 200, 700, 63, '2026-06-09 10:13:13', '2026-06-19 10:39:08', 1, 2, 'wla tawo, nag lantaw basket.', 3, '2026-06-19 10:39:08'),
	(34, 'AB23D273248587B', 20, 200, 220, 64, '2026-06-10 10:27:59', '2026-06-15 10:37:03', 1, 1, NULL, NULL, NULL),
	(35, '87C4E4CA437668D', 700, 200, 900, 65, '2026-06-10 10:39:34', '2026-06-10 10:39:34', 1, 0, NULL, NULL, NULL),
	(36, '101B62891CB118A', 748, 200, 948, 66, '2026-06-10 10:44:00', '2026-06-10 10:44:00', 1, 0, NULL, NULL, NULL),
	(37, 'A3B188E648108B9', 5200, 180, 5380, 67, '2026-06-17 08:57:17', '2026-06-18 20:30:19', 1, 3, NULL, 3, '2026-06-18 20:30:19'),
	(49, '115CB9ABB7A8AB4', 500, 0, 500, 79, '2026-06-19 12:50:20', '2026-06-19 12:50:20', 1, 11, NULL, NULL, NULL);

-- Dumping structure for table autoparts_db.transaction_details
CREATE TABLE IF NOT EXISTS `transaction_details` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `shipping` float DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `transaction_code` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.transaction_details: ~24 rows (approximately)
INSERT INTO `transaction_details` (`id`, `product_id`, `customer_id`, `quantity`, `price`, `shipping`, `total_price`, `transaction_code`, `created_at`, `updated_at`, `active`) VALUES
	(31, 5, 54, 2, 20, NULL, 40, 'BBD035922231CA0', '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1),
	(32, 8, 54, 1, 500, NULL, 500, 'BBD035922231CA0', '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1),
	(33, 10, 54, 1, 800, NULL, 800, 'BBD035922231CA0', '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1),
	(34, 7, 54, 1, 200, NULL, 200, 'BBD035922231CA0', '2026-06-05 10:58:08', '2026-06-05 10:58:08', 1),
	(35, 8, 55, 1, 500, NULL, 500, '521162460261988', '2026-06-05 10:58:39', '2026-06-05 10:58:39', 1),
	(36, 9, 55, 1, 100, NULL, 100, '521162460261988', '2026-06-05 10:58:39', '2026-06-05 10:58:39', 1),
	(37, 9, 56, 1, 100, NULL, 100, 'C0E7352111BA706', '2026-06-05 10:59:16', '2026-06-05 10:59:16', 1),
	(38, 5, 57, 2, 20, NULL, 40, '21005A9E52D5D61', '2026-06-05 10:59:42', '2026-06-05 10:59:42', 1),
	(39, 5, 58, 1, 20, NULL, 20, '3C8A51690C44CD2', '2026-06-05 11:24:47', '2026-06-05 11:24:47', 1),
	(40, 8, 58, 1, 500, NULL, 500, '3C8A51690C44CD2', '2026-06-05 11:24:47', '2026-06-05 11:24:47', 1),
	(41, 9, 59, 1, 100, NULL, 100, '9A5823E54EA8574', '2026-06-06 22:10:20', '2026-06-06 22:10:20', 1),
	(42, 7, 59, 1, 200, NULL, 200, '9A5823E54EA8574', '2026-06-06 22:10:20', '2026-06-06 22:10:20', 1),
	(43, 5, 60, 1, 20, NULL, 20, '212D8D70B5773AB', '2026-06-08 13:12:12', '2026-06-08 13:12:12', 1),
	(44, 5, 61, 1, 20, NULL, 20, '199B10155602C11', '2026-06-08 13:20:46', '2026-06-08 13:20:46', 1),
	(45, 5, 62, 1, 20, NULL, 20, 'B6726A11EB23565', '2026-06-08 13:45:15', '2026-06-08 13:45:15', 1),
	(46, 8, 63, 1, 500, NULL, 500, 'A872A23197D2C0C', '2026-06-09 10:13:14', '2026-06-09 10:13:14', 1),
	(47, 5, 64, 1, 20, NULL, 20, 'AB23D273248587B', '2026-06-10 10:27:59', '2026-06-10 10:27:59', 1),
	(48, 8, 65, 1, 500, NULL, 500, '87C4E4CA437668D', '2026-06-10 10:39:34', '2026-06-10 10:39:34', 1),
	(49, 9, 65, 2, 100, NULL, 200, '87C4E4CA437668D', '2026-06-10 10:39:34', '2026-06-10 10:39:34', 1),
	(50, 9, 66, 2, 100, NULL, 200, '101B62891CB118A', '2026-06-10 10:44:00', '2026-06-10 10:44:00', 1),
	(51, 8, 66, 1, 500, NULL, 500, '101B62891CB118A', '2026-06-10 10:44:00', '2026-06-10 10:44:00', 1),
	(52, 6, 66, 1, 48, NULL, 48, '101B62891CB118A', '2026-06-10 10:44:00', '2026-06-10 10:44:00', 1),
	(53, 13, 67, 1, 4500, NULL, 4500, 'A3B188E648108B9', '2026-06-17 08:57:17', '2026-06-17 08:57:17', 1),
	(54, 12, 67, 1, 700, NULL, 700, 'A3B188E648108B9', '2026-06-17 08:57:17', '2026-06-17 08:57:17', 1),
	(71, 8, 79, 1, 500, NULL, 500, '115CB9ABB7A8AB4', '2026-06-19 12:50:20', '2026-06-19 12:50:20', 1);

-- Dumping structure for table autoparts_db.translations
CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` text DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `en` text DEFAULT NULL,
  `str` text DEFAULT NULL,
  `active` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.translations: ~37 rows (approximately)
INSERT INTO `translations` (`id`, `lang`, `name`, `en`, `str`, `active`, `created_at`, `updated_at`) VALUES
	(1, 'hil', 'Hiligaynon', 'ACTIONS', 'Aksyon', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(2, 'hil', 'Hiligaynon', 'Add new Product', 'Dugang pradak', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(3, 'hil', 'Hiligaynon', 'CATEGORY', 'CATEGORYA', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(4, 'hil', 'Hiligaynon', 'Customers', 'Kustomir', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(5, 'hil', 'Hiligaynon', 'Email address', 'emil', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(6, 'hil', 'Hiligaynon', 'Filter', 'Salain', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(7, 'hil', 'Hiligaynon', 'ID', 'Aydi', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(8, 'hil', 'Hiligaynon', 'IMAGE', 'Imahe', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(9, 'hil', 'Hiligaynon', 'Manage customer orders, update status, and track deliveries', 'Dumalahan ang order sang customer, i-update ang status, kag bantayan ang delivery', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(10, 'hil', 'Hiligaynon', 'New Products', 'Bag-o nga pradak', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(11, 'hil', 'Hiligaynon', 'Orders', 'Mga ordir', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(12, 'hil', 'Hiligaynon', 'Password', 'pasword', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(13, 'hil', 'Hiligaynon', 'Price', 'Presyo', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(14, 'hil', 'Hiligaynon', 'Product catalog management', 'Pagdumala sang katalogo sang produkto', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(15, 'hil', 'Hiligaynon', 'PRODUCT NAME', 'Produkto', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(16, 'hil', 'Hiligaynon', 'Products', 'Mga pradak', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(17, 'hil', 'Hiligaynon', 'Search by name, category, ID...', 'Pangitaa paagi sa ngalan, kategorya, ID...', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(18, 'hil', 'Hiligaynon', 'Search product', 'Pangitaa ang produkto', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(19, 'hil', 'Hiligaynon', 'Type product name, SKU, or category...', 'Isulat ang ngalan sang produkto, SKU, ukon kategorya...', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(20, 'hil', 'Hiligaynon', 'View Cart', 'Tan-awa ang Cart', 1, '2026-06-14 22:46:28', '2026-06-14 22:46:28'),
	(21, 'fil', 'Filipino', ' Product catalog management\n', 'Pamamahala ng katalogo ng produkto', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(22, 'fil', 'Filipino', ' Product catalog management', 'Pamamahala ng katalogo ng produkto', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(23, 'fil', 'Filipino', 'ACTIONS', 'Aksyon', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(24, 'fil', 'Filipino', 'Add new Product', 'Dugang pradak', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(25, 'fil', 'Filipino', 'CATEGORY', 'CATEGORYA', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(26, 'fil', 'Filipino', 'Customers', 'Kustomir', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(27, 'fil', 'Filipino', 'Email address', 'emil', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(28, 'fil', 'Filipino', 'ID', 'Aydi', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(29, 'fil', 'Filipino', 'IMAGE', 'Imahe', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(30, 'fil', 'Filipino', 'New Products', 'Bag-o nga pradak', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(31, 'fil', 'Filipino', 'Orders', 'Mga ordir', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(32, 'fil', 'Filipino', 'Password', 'pasword', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(33, 'fil', 'Filipino', 'Price', 'Presyo', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(34, 'fil', 'Filipino', 'Product catalog management', 'Pamamahala ng katalogo ng produkto', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(35, 'fil', 'Filipino', 'PRODUCT NAME', 'Produkto', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(36, 'fil', 'Filipino', 'Products', 'Mga pradak', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47'),
	(37, 'fil', 'Filipino', 'Search by name, category, ID...', 'Maghanap ayon sa pangalan, kategorya, ID...', 1, '2026-06-14 22:46:47', '2026-06-14 22:46:47');

-- Dumping structure for table autoparts_db.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `emp_id` varchar(10) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `fullname` varchar(50) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` int(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table autoparts_db.user: ~4 rows (approximately)
INSERT INTO `user` (`id`, `emp_id`, `username`, `password`, `fullname`, `role`, `created_at`, `updated_at`, `active`) VALUES
	(1, '1222', 'admin@gmail.com', 'admin@gmail.com', 'Tyrone Lee Emz', 2, '0000-00-00 00:00:00', '2026-06-19 12:52:58', 1),
	(2, '1234', 'rider@gmail.com', 'rider@gmail.com', 'James Rider', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
	(3, '34333', 'rider1@gmail.com', 'rider1@gmail.com', 'Junto Nakatani', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
	(4, '1245522', 'admin1@gmail.com', 'admin1@gmail.com', 'Tyrone Lee Emz', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
