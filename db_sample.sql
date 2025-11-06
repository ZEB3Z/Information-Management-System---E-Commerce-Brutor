-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 04:53 PM
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
-- Database: `db_sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `barcode`
--

CREATE TABLE `barcode` (
  `barcode_ean` char(13) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `barcode`
--

INSERT INTO `barcode` (`barcode_ean`, `item_id`) VALUES
('2239872376872', 11),
('3453458677628', 5),
('4587263646878', 9),
('6241234586487', 8),
('6241527746363', 4),
('6241527836173', 1),
('6241574635234', 2),
('6264537836173', 3),
('6434564564544', 6),
('8476736836876', 7),
('9473625532534', 8),
('9473627464543', 8),
('9879879837489', 11);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `title` char(4) DEFAULT NULL,
  `fname` varchar(32) DEFAULT NULL,
  `lname` varchar(32) NOT NULL,
  `addressline` varchar(64) DEFAULT NULL,
  `town` varchar(32) DEFAULT NULL,
  `zipcode` char(10) NOT NULL,
  `phone` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `title`, `fname`, `lname`, `addressline`, `town`, `zipcode`, `phone`) VALUES
(1, 'Miss', 'jenny', 'stones', '27 Rowan Avenue', 'hightown', 'NT21AQ', '023 9876'),
(2, 'Mr', 'Andrew', 'stones', '52 The willows', 'lowtown', 'LT57RA', '876 3527'),
(3, 'Miss', 'Alex', 'Matthew', '4 The Street', 'Nicetown', 'NT22TX', '010 4567'),
(4, 'Mr', 'Adrian', 'MAtthew', 'The Barn', 'Yuleville', 'YV672WR', '487 3871'),
(5, 'Mr', 'Simon', 'Cozens', '7 Shady Lane', 'Oahenham', 'OA36Qw', '514 5926'),
(6, 'Mr', 'Neil', 'Matther', '5 Pasture Lane', 'Nicetown', 'NT37RT', '267 1232'),
(7, 'Mr', 'Richard', 'stones', '34 Holly Way', 'Bingham', 'BG42WE', '342 5982'),
(8, 'Mrs', 'Ann', 'stones', '34 Holly Way', 'Bingham', 'BG42WE', '342 5982'),
(9, 'Mrs', 'Christine', 'Hickman', '36 Queen Street', 'Histon', 'HT35EM', '342 5432'),
(10, 'Mr', 'Mike', 'Howard', '86 Dysart Street', 'Tibsville', 'TB37FG', '505 5482'),
(11, 'Mr', 'Dave', 'Jones', '54 Vale Rise', 'Bingham', 'BG38GD', '342 8264'),
(12, 'Mr', 'Richard', 'Neil', '42 Thached Way', 'Winersbay', 'WB36GQ', '505 6482'),
(13, 'Mrs', 'Laura', 'Hendy', '73 MArgaritta Way', 'Oxbridge', 'OX23HX', '821 2335'),
(14, 'Mr', 'Bill', 'ONeil', '2 Beamer Street', 'Welltown', 'WT38GM', '435 1234'),
(15, 'Mr', 'David', 'Hudson', '4 The Square', 'Milltown', 'MT26RT', '961 4526'),
(16, '', 'Doe', 'Jane', '', '', '', ''),
(17, 'Mrs.', 'Jane', 'Doe', 'Taguig', 'Western Bicutan', '1630', '09186824721');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` varchar(64) NOT NULL,
  `cost_price` decimal(7,2) DEFAULT NULL,
  `sell_price` decimal(7,2) DEFAULT NULL,
  `image_path` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `title`, `description`, `cost_price`, `sell_price`, `image_path`, `created_at`, `updated_at`, `deleted_at`, `category`, `stock_quantity`) VALUES
(2, 'Engine', 'Rubik Cube', 7.45, 11.49, '../uploads/items/1762442179_w.png', NULL, NULL, NULL, 'Engine', 23232),
(5, '', 'PIcture Frame', 7.54, 9.95, '', NULL, NULL, NULL, NULL, 0),
(6, '', 'Fan Small', 9.23, 15.75, '', NULL, NULL, NULL, NULL, 0),
(7, '', 'Fan Large', 13.36, 19.95, '', NULL, NULL, NULL, NULL, 0),
(8, '', 'ToothBrush', 0.75, 1.45, '', NULL, NULL, NULL, NULL, 0),
(9, '', 'Roman Coin', 2.34, 2.45, '', NULL, NULL, NULL, NULL, 0),
(10, '', 'Carrier Bag', 0.01, 0.00, '', NULL, NULL, NULL, NULL, 0),
(11, '', 'Speakers', 19.73, 25.32, '', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

CREATE TABLE `orderinfo` (
  `orderinfo_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_placed` date NOT NULL,
  `date_shipped` date DEFAULT NULL,
  `shipping` decimal(7,2) DEFAULT NULL,
  `status` enum('Processing','Delivered','Canceled') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`orderinfo_id`, `customer_id`, `date_placed`, `date_shipped`, `shipping`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, '2000-03-13', '2000-03-17', 2.99, 'Processing', NULL, NULL),
(2, 8, '2000-06-23', '2000-06-23', 0.00, 'Processing', NULL, NULL),
(3, 15, '2000-09-02', '2000-09-12', 3.99, 'Processing', NULL, NULL),
(4, 13, '2000-09-03', '2000-09-10', 2.99, 'Processing', NULL, NULL),
(5, 8, '2000-07-21', '2000-07-24', 0.00, 'Processing', NULL, NULL),
(15, 1, '2023-03-09', '2023-03-09', 10.00, 'Processing', NULL, NULL),
(16, 1, '2023-03-09', '2023-03-09', 10.00, 'Processing', NULL, NULL),
(18, 1, '2023-03-10', '2023-03-10', 10.00, 'Processing', '2023-03-09 22:57:10', '2023-03-09 22:57:10'),
(21, 1, '2023-03-10', '2023-03-10', 10.00, 'Processing', '2023-03-09 23:20:35', '2023-03-09 23:20:35'),
(22, 1, '2023-03-10', '2023-03-10', 10.00, 'Processing', '2023-03-09 23:21:13', '2023-03-09 23:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `orderinfo_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`orderinfo_id`, `item_id`, `quantity`) VALUES
(1, 4, 1),
(1, 7, 1),
(1, 9, 1),
(2, 1, 1),
(2, 10, 1),
(2, 7, 2),
(2, 4, 2),
(3, 2, 1),
(3, 1, 1),
(4, 5, 2),
(5, 1, 1),
(5, 3, 1),
(15, 1, 1),
(15, 2, 1),
(15, 4, 1),
(16, 1, 3),
(16, 2, 2),
(18, 1, 2),
(18, 2, 2),
(18, 4, 2),
(21, 4, 1),
(21, 1, 1),
(22, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`item_id`, `quantity`) VALUES
(2, 8),
(5, 3),
(7, 8),
(8, 18),
(10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fcm_token` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `fcm_token`, `avatar`) VALUES
(3, 'Admin User', 'admin@shop.com', NULL, '1234', 'admin', NULL, '2025-10-14 13:51:10', '2025-10-14 13:51:10', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barcode`
--
ALTER TABLE `barcode`
  ADD PRIMARY KEY (`barcode_ean`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD PRIMARY KEY (`orderinfo_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orderinfo`
--
ALTER TABLE `orderinfo`
  MODIFY `orderinfo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
