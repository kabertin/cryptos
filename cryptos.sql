-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 21, 2025 at 01:29 PM
-- Server version: 10.11.10-MariaDB-cll-lve
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uqbrlykt_cryptos`
--

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crypto_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `crypto_id`) VALUES
(20, 1, 'tron'),
(22, 1, 'sui'),
(23, 1, 'bitcoin'),
(24, 1, 'binancecoin'),
(25, 1, 'ripple'),
(26, 1, 'injective-protocol'),
(27, 1, 'ethereum'),
(28, 1, 'staked-ether'),
(29, 1, 'usd1'),
(30, 1, 'solana');

-- --------------------------------------------------------

--
-- Table structure for table `price_alerts`
--

CREATE TABLE `price_alerts` (
  `alert_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crypto_id` varchar(100) NOT NULL,
  `crypto_symbol` varchar(20) NOT NULL,
  `price_level` decimal(18,5) NOT NULL,
  `alert_type` enum('above','below') NOT NULL DEFAULT 'above',
  `alert_status` enum('active','triggered','expired') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_sent` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_alerts`
--

INSERT INTO `price_alerts` (`alert_id`, `user_id`, `crypto_id`, `crypto_symbol`, `price_level`, `alert_type`, `alert_status`, `created_at`, `updated_at`, `email_sent`) VALUES
(32, 1, 'ethereum', 'eth', 3353.29000, 'below', 'triggered', '2025-01-16 06:22:33', '2025-01-21 12:27:30', 'yes'),
(33, 1, 'usds', 'usds', 1.00300, 'above', 'triggered', '2025-01-16 16:23:42', '2025-01-21 12:22:11', 'yes'),
(34, 1, 'binancecoin', 'bnb', 700.21000, 'below', 'triggered', '2025-01-16 16:23:52', '2025-01-21 12:23:16', 'yes'),
(36, 1, 'bitcoin', 'btc', 101177.00000, 'above', 'triggered', '2025-01-20 15:49:00', '2025-01-21 12:20:15', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'karindabertin35@gmail.com', '$2y$10$8D6YQla4/o0KMz4xxE1U1OKcgURzm87BtA9QRl1Aa8nmnQum9dyCK', '2024-12-28 04:52:26'),
(2, 'geo@mercato.rw', '$argon2id$v=19$m=65536,t=4,p=1$SnRweWJvb0lLLjNNNTl1dw$qcZxRjY9BvqnrALHkto0daT5dwFCWrPFGLZ5qKrAIHY', '2025-01-10 10:59:13'),
(3, 'hi@hu.com', '$argon2id$v=19$m=65536,t=4,p=1$MFZydE1OUHV6WUhKVTNPQg$/f+Nyi8EuF9Jhd6SDwiPr6ijsRZ5ZPcNw25ouuAd6/w', '2025-01-10 17:08:28'),
(4, 'ffixjulius@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$M2toVEFydFZqOUFhQ01lQg$VllHHS5KhWwJKa1XNZZ1LRIYNaCERR72pEhvCl8WyOE', '2025-01-16 12:52:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `price_alerts`
--
ALTER TABLE `price_alerts`
  ADD PRIMARY KEY (`alert_id`),
  ADD UNIQUE KEY `unique_alert` (`user_id`,`crypto_symbol`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `price_alerts`
--
ALTER TABLE `price_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `price_alerts`
--
ALTER TABLE `price_alerts`
  ADD CONSTRAINT `price_alerts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
