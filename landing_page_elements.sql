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
-- Table structure for table `landing_page_elements`
--

CREATE TABLE `landing_page_elements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `element_type` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `position` int(11) NOT NULL DEFAULT 0,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landing_page_elements`
--

INSERT INTO `landing_page_elements` (`id`, `name`, `element_type`, `content`, `attributes`, `position`, `section_id`, `is_active`, `settings`, `created_at`, `updated_at`) VALUES
(8, 'Free Shipping', 'icon', 'bi bi-truck', '{\"title\":\"On orders over $50\"}', 0, 6, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 07:45:31'),
(9, 'Secure Payment', 'icon', 'bi bi-lock', '{\"title\":\"Safe and encrypted\"}', 1, 6, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34'),
(10, '24/7 Support', 'icon', 'bi bi-headset', '{\"title\":\"Dedicated assistance\"}', 2, 6, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34'),
(11, 'Easy Returns', 'icon', 'bi bi-arrow-return-right', '{\"title\":\"30-day guarantee\"}', 3, 6, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34'),
(12, 'Quality Guaranteed', 'heading', 'fas fa-shield-alt', '{\"description\":\"All our products are carefully selected and guaranteed for quality. We stand behind every purchase with our quality promise.\"}', 0, 7, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34'),
(13, 'Best Prices', 'heading', 'fas fa-tag', '{\"description\":\"We offer competitive pricing on all products with regular promotions and discounts for our valued customers.\"}', 1, 7, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34'),
(14, 'Support Team', 'heading', 'fas fa-headset', '{\"description\":\"Our dedicated support team is available to assist you with any questions or concerns you may have.\"}', 2, 7, 1, NULL, '2025-12-04 05:34:34', '2025-12-04 05:34:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `landing_page_elements`
--
ALTER TABLE `landing_page_elements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `landing_page_elements_section_id_foreign` (`section_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `landing_page_elements`
--
ALTER TABLE `landing_page_elements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `landing_page_elements`
--
ALTER TABLE `landing_page_elements`
  ADD CONSTRAINT `landing_page_elements_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `landing_page_sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
