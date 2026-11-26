-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 26, 2026 at 05:08 PM
-- Server version: 11.8.6-MariaDB-0+deb13u1 from Debian
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `z9`
--

-- --------------------------------------------------------

--
-- Table structure for table `goscieportalu`
--

CREATE TABLE `goscieportalu` (
  `id` int(11) NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `browser` varchar(100) NOT NULL DEFAULT '',
  `screen_res` varchar(20) NOT NULL DEFAULT '',
  `window_res` varchar(20) NOT NULL DEFAULT '',
  `screen_colors` varchar(10) NOT NULL DEFAULT '',
  `cookies_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `java_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `language` varchar(30) NOT NULL DEFAULT '',
  `action` varchar(20) NOT NULL DEFAULT 'visit',
  `username_attempt` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `goscieportalu`
--

INSERT INTO `goscieportalu` (`id`, `ipaddress`, `datetime`, `browser`, `screen_res`, `window_res`, `screen_colors`, `cookies_enabled`, `java_enabled`, `language`, `action`, `username_attempt`) VALUES
(1, '46.239.135.21', '2026-11-25 13:09:55', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(2, '46.239.135.21', '2026-11-25 13:10:00', 'Firefox', '', '', '', 0, 0, '', 'login_fail', 'user1'),
(3, '46.239.135.21', '2026-11-25 13:10:01', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(4, '46.239.135.21', '2026-11-25 13:10:17', 'Firefox', '', '', '', 0, 0, '', 'login_ok', 'test1'),
(5, '46.239.135.21', '2026-11-25 13:10:19', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(6, '46.239.135.21', '2026-11-25 13:10:24', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(7, '46.239.135.21', '2026-11-25 13:14:37', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(8, '46.239.135.21', '2026-11-25 13:15:13', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(9, '46.239.135.21', '2026-11-25 13:15:29', 'Chrome', '', '', '', 0, 0, '', 'login_fail', 'user2'),
(10, '46.239.135.21', '2026-11-25 13:15:30', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(11, '46.239.135.21', '2026-11-25 13:15:34', 'Chrome', '', '', '', 0, 0, '', 'login_fail', 'user2'),
(12, '46.239.135.21', '2026-11-25 13:15:34', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(13, '46.239.135.21', '2026-11-25 13:15:41', 'Chrome', '', '', '', 0, 0, '', 'login_fail', 'user2'),
(14, '46.239.135.21', '2026-11-25 13:15:42', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(15, '46.239.135.21', '2026-11-25 13:15:49', 'Chrome', '', '', '', 0, 0, '', 'login_ok', 'test2'),
(16, '46.239.135.21', '2026-11-25 13:15:50', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(17, '46.239.135.21', '2026-11-25 13:15:54', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(18, '46.239.135.21', '2026-11-25 13:19:30', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(19, '46.239.135.21', '2026-11-25 13:19:37', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(20, '46.239.135.21', '2026-11-25 13:21:14', 'Firefox', '1920x1080', '1904x988', '24', 1, 0, 'pl', 'visit', ''),
(21, '46.239.135.21', '2026-11-25 13:21:26', 'Chrome', '1920x1080', '1226x650', '30', 1, 0, 'pl-PL', 'visit', ''),
(22, '46.239.135.21', '2026-11-25 13:24:25', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(23, '46.239.135.21', '2026-11-25 13:24:27', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(24, '46.239.135.21', '2026-11-25 13:25:08', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(25, '46.239.135.21', '2026-11-25 13:25:25', 'Chrome', '', '', '', 0, 0, '', 'login_ok', 'user1'),
(26, '46.239.135.21', '2026-11-25 13:25:26', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(27, '46.239.135.21', '2026-11-25 13:26:57', 'Firefox', '1920x1080', '944x988', '24', 1, 0, 'pl', 'visit', ''),
(28, '46.239.135.21', '2026-11-25 13:28:44', 'Firefox', '1920x1080', '944x988', '24', 1, 0, 'pl', 'visit', ''),
(29, '46.239.135.21', '2026-11-25 13:30:30', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(30, '46.239.135.21', '2026-11-25 13:31:34', 'Firefox', '1920x1080', '944x988', '24', 1, 0, 'pl', 'visit', ''),
(31, '46.239.135.21', '2026-11-25 13:35:04', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(32, '46.239.135.21', '2026-11-25 13:35:08', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(33, '46.239.135.21', '2026-11-25 13:35:14', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(34, '46.239.135.21', '2026-11-25 13:35:45', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(35, '46.239.135.21', '2026-11-25 13:35:49', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(36, '46.239.135.21', '2026-11-25 13:36:16', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(37, '46.239.135.21', '2026-11-25 13:36:23', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(38, '46.239.135.21', '2026-11-25 13:54:26', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(39, '46.239.135.21', '2026-11-25 13:54:36', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(40, '46.239.135.21', '2026-11-25 13:54:40', 'Chrome', '', '', '', 0, 0, '', 'login_ok', 'test2'),
(41, '46.239.135.21', '2026-11-25 13:54:41', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(42, '46.239.135.21', '2026-11-25 13:54:45', 'Chrome', '1920x1080', '960x947', '30', 1, 0, 'pl-PL', 'visit', ''),
(43, '37.209.144.202', '2026-11-26 12:44:40', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(44, '37.209.144.202', '2026-11-26 12:44:44', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(45, '37.209.144.202', '2026-11-26 16:31:42', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(46, '37.209.144.202', '2026-11-26 17:42:01', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(47, '37.209.144.202', '2026-11-26 17:42:07', 'Firefox', '', '', '', 0, 0, '', 'login_fail', 'grad'),
(48, '37.209.144.202', '2026-11-26 17:42:08', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(49, '37.209.144.202', '2026-11-26 17:42:13', 'Firefox', '', '', '', 0, 0, '', 'login_ok', 'user1'),
(50, '37.209.144.202', '2026-11-26 17:42:14', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(51, '37.209.144.202', '2026-11-26 17:42:19', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(52, '37.209.144.202', '2026-11-26 17:42:27', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(53, '37.209.144.202', '2026-11-26 17:52:57', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(54, '37.209.144.202', '2026-11-26 17:54:42', 'Firefox', '1920x1080', '1904x1034', '24', 1, 0, 'pl', 'visit', ''),
(55, '37.209.144.202', '2026-11-26 17:57:47', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(56, '37.209.144.202', '2026-11-26 17:57:52', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(57, '37.209.144.202', '2026-11-26 17:57:56', 'Firefox', '', '', '', 0, 0, '', 'login_fail', 'test1'),
(58, '37.209.144.202', '2026-11-26 17:57:57', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(59, '37.209.144.202', '2026-11-26 17:58:01', 'Firefox', '', '', '', 0, 0, '', 'login_fail', 'user1'),
(60, '37.209.144.202', '2026-11-26 17:58:02', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', ''),
(61, '37.209.144.202', '2026-11-26 17:58:05', 'Firefox', '', '', '', 0, 0, '', 'login_ok', 'user1'),
(62, '37.209.144.202', '2026-11-26 17:58:05', 'Firefox', '1920x1080', '1587x910', '24', 1, 0, 'pl', 'visit', '');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `idk` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `message` text NOT NULL,
  `user` varchar(50) NOT NULL,
  `recipient` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`idk`, `datetime`, `message`, `user`, `recipient`) VALUES
(1, '2026-11-25 13:16:00', 'test', 'test2', 'test1'),
(2, '2026-11-25 13:16:19', 'test2/1795608979_ggugyuffu.png', 'test2', 'test1'),
(3, '2026-11-25 13:16:48', 'test2/1795609008_robert_spin_again.mp4', 'test2', 'test1'),
(4, '2026-11-25 13:17:46', 'f', 'test1', 'test2'),
(5, '2026-11-25 13:17:46', 'sss', 'test1', 'test2'),
(6, '2026-11-25 13:17:46', 'sss', 'test1', 'test2'),
(7, '2026-11-25 13:17:46', 'sss', 'test1', 'test2'),
(8, '2026-11-25 13:17:46', 'sss', 'test1', 'test2'),
(9, '2026-11-25 13:17:58', 'test', 'test1', 'test2'),
(10, '2026-11-25 13:18:01', 'test', 'test1', 'test2'),
(11, '2026-11-25 13:18:09', 'tst', 'test2', 'test1'),
(12, '2026-11-25 13:20:10', 'tr', 'test1', 'test2'),
(13, '2026-11-25 13:21:30', 'test', 'test2', 'test1'),
(14, '2026-11-25 13:21:37', 'test', 'test1', 'test2'),
(15, '2026-11-25 13:22:03', 'test2/1795609323_Oh_Klahoma.mp3', 'test2', 'test1'),
(16, '2026-11-25 13:22:45', 'test2/1795609365_okladka.png', 'test2', 'test1'),
(17, '2026-11-25 13:31:52', 'test1/1795609912_1794324136888.jpg', 'test1', 'user1'),
(18, '2026-11-25 13:32:09', 'this you', 'test1', 'user1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'media/avatars/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`) VALUES
(1, 'admin', 'admin', 'media/avatars/default.png'),
(2, 'test1', 'test1', 'media/avatars/default.png'),
(3, 'test2', 'test2', 'media/avatars/default.png'),
(4, 'user1', 'pass1', 'media/avatars/6b06d3a4084bf_1794324136888.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `goscieportalu`
--
ALTER TABLE `goscieportalu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`idk`),
  ADD KEY `idx_users` (`user`,`recipient`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `goscieportalu`
--
ALTER TABLE `goscieportalu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `idk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
