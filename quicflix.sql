-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2024 at 03:54 PM
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
-- Database: `quicflix`
--

-- --------------------------------------------------------

--
-- Table structure for table `billingdetails`
--

CREATE TABLE `billingdetails` (
  `id` int(11) NOT NULL,
  `agreementId` varchar(200) NOT NULL,
  `nextBillingDate` varchar(50) NOT NULL,
  `token` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Action & adventure'),
(2, 'Classic'),
(3, 'Comedies'),
(4, 'Dramas'),
(5, 'Horror'),
(6, 'Romantic'),
(7, 'Sci - Fi & Fantasy'),
(8, 'Sports'),
(9, 'Thrillers'),
(10, 'Documentaries'),
(12, 'Teen'),
(13, 'Children & family'),
(14, 'Anime'),
(15, 'Independent'),
(16, 'Foreign'),
(17, 'Music'),
(18, 'Christmas'),
(19, 'Others'),
(20, 'Cartoon');

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE `entities` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `thumbnail` varchar(250) NOT NULL,
  `preview` varchar(250) NOT NULL,
  `categoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `entities`
--

INSERT INTO `entities` (`id`, `name`, `thumbnail`, `preview`, `categoryId`) VALUES
(103, 'Family Guy', '/quicflix/entities/thumbnails/Family Guy.jpeg', '/quicflix/entities/previews/FamilyGuyS3.mp4', 20),
(104, 'Family Guy', '/quicflix/entities/thumbnails/_06de5b2c-d229-41b9-a63e-62ea90855dbf.jpeg', '/quicflix/entities/previews/FamilyGuyS3.mp4', 20),
(105, 'Family Guy', '/quicflix/entities/thumbnails/Family Guy.jpeg', '/quicflix/entities/previews/FamilyGuyS3.mp4', 20),
(106, 'Mortal Combat', '/quicflix/entities/thumbnails/Mortal Kombat.jpeg', '/quicflix/entities/previews/Mortal Kombat - Official Trailer IMDb.mp4', 1),
(107, 'Agent Game 2', '/quicflix/entities/thumbnails/66dd8a78575ee-Agent Game.jpeg', '/quicflix/entities/previews/66dd8a78575e1-Agent Game Trailer.mp4', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstName` text NOT NULL,
  `lastName` text NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `regDate` datetime NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user','','') DEFAULT '',
  `subscribed` enum('1','0','','') NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstName`, `lastName`, `username`, `email`, `password`, `regDate`, `role`, `subscribed`) VALUES
(1, 'Siphiwe', 'Sikhakhane', 'Paul2024', 'siphiwemathonsi67@gmail.com', 'affefb4096a23002643b2461c5c09a63574785c85f75700d66b05b396d399964e548c73426d001bcf6984d2b2d4f5f815a74da30779774f73b1cb5a94e62d694', '2024-09-05 18:58:50', 'admin', '1'),
(2, 'Siphiwe', 'Sikhakhane', 'user3000', 'siphiwemathonsi67@gmail.com', '33f8d9f41606d8a4467c6a405be387b494ba5f94a5e2cc604d15639dd6617f67e7afded63e35b15e041120e7ae2909375f77ea568baa343ab76b6a23ee1cbbc0', '2024-09-06 00:42:35', 'user', '1');

-- --------------------------------------------------------

--
-- Table structure for table `videoprogress`
--

CREATE TABLE `videoprogress` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `videoId` int(11) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `finished` tinyint(4) NOT NULL DEFAULT 0,
  `dateModified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videoprogress`
--

INSERT INTO `videoprogress` (`id`, `username`, `videoId`, `progress`, `finished`, `dateModified`) VALUES
(1, 'Paul2024', 902, 0, 0, '2024-09-05 22:23:28'),
(2, 'Paul2024', 702, 4, 0, '2024-09-05 23:30:44'),
(3, 'Paul2024', 675, 0, 0, '2024-09-05 22:24:16'),
(4, 'Paul2024', 954, 0, 0, '2024-09-05 22:27:52'),
(5, 'Paul2024', 585, 0, 0, '2024-09-05 23:10:43'),
(6, 'Paul2024', 412, 0, 0, '2024-09-05 23:13:57'),
(7, 'user3000', 1935, 0, 0, '2024-09-06 00:49:41'),
(8, 'Paul2024', 1936, 0, 1, '2024-09-08 12:57:12'),
(9, 'Paul2024', 1937, 0, 1, '2024-09-08 13:06:05'),
(10, 'Paul2024', 1938, 0, 1, '2024-09-08 13:39:27'),
(11, 'Paul2024', 1939, 0, 1, '2024-09-08 13:39:40');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(70) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `filePath` varchar(250) NOT NULL,
  `uploaded_by` varchar(11) NOT NULL,
  `isMovie` tinyint(1) NOT NULL,
  `uploadDate` datetime NOT NULL DEFAULT current_timestamp(),
  `releaseDate` date NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `duration` varchar(10) NOT NULL,
  `season` int(11) DEFAULT 0,
  `episode` int(11) DEFAULT 0,
  `entityId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `description`, `filePath`, `uploaded_by`, `isMovie`, `uploadDate`, `releaseDate`, `views`, `duration`, `season`, `episode`, `entityId`) VALUES
(1936, 'Family Guy', 'This is Family Guy', '/quicflix/entities/videos/FamilyGuyS3.mp4', 'Paul2024', 0, '2024-09-08 12:26:09', '1997-12-12', 16, '22', 1, 1, 103),
(1937, 'Family Guy', 'This is the Family Guy, you know talking baby', '/quicflix/entities/videos/FamilyGuyS3.mp4', 'Paul2024', 0, '2024-09-08 12:56:41', '1997-12-12', 2, '22', 1, 2, 104),
(1938, 'Family Guy', 'SAhjksdjkslkjdlksd', '/quicflix/entities/videos/FamilyGuyS3.mp4', 'Paul2024', 0, '2024-09-08 13:08:56', '1997-12-12', 2, '22', 1, 3, 105),
(1939, 'Mortal Combat', 'This is Mortal Combat Movie', '/quicflix/entities/videos/Mortal Kombat - Official Trailer IMDb.mp4', 'Paul2024', 1, '2024-09-08 13:22:29', '1997-12-12', 1, '120', 0, 0, 106),
(1940, 'Agent Game 3', 'JKhsaedjkasjkldsajdhuiuhiedwqeqwwed', '/quicflix/entities/videos/66dd8a78575e1-Agent Game Trailer.mp4', 'Paul2024', 1, '2024-09-08 13:38:16', '2024-12-12', 0, '145', 0, 0, 107);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `entities`
--
ALTER TABLE `entities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryId` (`categoryId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `videoprogress`
--
ALTER TABLE `videoprogress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entityId` (`entityId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `entities`
--
ALTER TABLE `entities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `videoprogress`
--
ALTER TABLE `videoprogress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1941;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `entities_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
