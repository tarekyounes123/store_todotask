-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 05:55 AM
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
-- Database: `task_manager`
--

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_sections`
--

CREATE TABLE `landing_page_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `section_type` varchar(255) NOT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `position` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_page_sections`
--

INSERT INTO `landing_page_sections` (`id`, `name`, `title`, `content`, `section_type`, `settings`, `position`, `is_active`, `metadata`, `created_at`, `updated_at`) VALUES
(6, 'change status hero', 'Discover Products at Unbeatable Prices', 'Shop our extensive collection of premium quality items. Fast shipping, competitive prices, and exceptional customer service.', 'hero', NULL, 0, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 08:08:17'),
(7, 'features', 'Why Choose Us?', 'We provide the best shopping experience with quality products', 'features', NULL, 1, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 07:37:42'),
(8, 'products', 'Featured Products', 'Check out our most popular items', 'products', NULL, 2, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 07:37:42'),
(9, 'cta', 'Ready to Start Shopping?', 'Become a member today and enjoy exclusive benefits, special discounts, and early access to new products.', 'cta', NULL, 3, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 07:37:42'),
(10, 'newsletter', 'Stay Updated', 'Subscribe to our newsletter to receive updates and offers.', 'newsletter', NULL, 4, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 07:42:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `landing_page_sections`
--
ALTER TABLE `landing_page_sections`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `landing_page_sections`
--
ALTER TABLE `landing_page_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
