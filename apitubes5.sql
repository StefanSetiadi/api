-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2024 at 03:44 AM
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
-- Database: `apitubes`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `urlimage` varchar(255) DEFAULT NULL,
  `countlike` int(11) DEFAULT 0,
  `countcomment` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `user_id`, `title`, `subtitle`, `content`, `category`, `urlimage`, `countlike`, `countcomment`, `created_at`, `updated_at`) VALUES
(3, 1, 'Title', NULL, 'Content', NULL, 'https://localhost/api/api/article/image/65d20a99dd9395d68e785173b01ce23d806ef79f7a32e3eec6540e0ba9ea1ceb.png', 1, 0, '2024-05-10 01:36:55', '2024-05-10 01:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `commentarticle`
--

CREATE TABLE `commentarticle` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commentpost`
--

CREATE TABLE `commentpost` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commentpost`
--

INSERT INTO `commentpost` (`id`, `post_id`, `user_id`, `comment`, `created_at`, `updated_at`) VALUES
(9, 14, 2, 'Komen Dua', '2024-05-09 03:50:53', '2024-05-09 03:50:53'),
(10, 14, 3, 'Komen Tiga', '2024-05-09 03:51:01', '2024-05-09 03:51:01'),
(11, 14, 4, 'Komen Empat', '2024-05-09 03:51:13', '2024-05-09 03:51:13'),
(12, 14, 5, 'Komen Lima', '2024-05-09 03:51:20', '2024-05-09 03:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE `follow` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `follower_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likearticle`
--

CREATE TABLE `likearticle` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likearticle`
--

INSERT INTO `likearticle` (`id`, `article_id`, `user_id`, `created_at`) VALUES
(1, 3, 2, '2024-05-10 01:43:51');

-- --------------------------------------------------------

--
-- Table structure for table `likepost`
--

CREATE TABLE `likepost` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likepost`
--

INSERT INTO `likepost` (`id`, `post_id`, `user_id`, `created_at`, `updated_at`) VALUES
(7, 14, 2, '2024-05-09 03:50:18', '2024-05-09 03:50:18'),
(8, 14, 3, '2024-05-09 03:50:22', '2024-05-09 03:50:22'),
(9, 14, 4, '2024-05-09 03:50:27', '2024-05-09 03:50:27'),
(10, 14, 5, '2024-05-09 03:50:31', '2024-05-09 03:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `urlimage` varchar(255) DEFAULT NULL,
  `countlike` int(11) DEFAULT 0,
  `countcomment` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `caption`, `urlimage`, `countlike`, `countcomment`, `created_at`, `updated_at`) VALUES
(14, 1, 'Ini update caption', 'https://localhost/api/api/post/image/9cb7957bf1fd0699c4f1c13b4c37d2c9f5e8a23c0c21af1dffe9060538110d1a.png', 4, 4, '2024-05-09 03:39:58', '2024-05-09 03:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `story`
--

CREATE TABLE `story` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `urlimage` varchar(255) DEFAULT NULL,
  `countview` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `story`
--

INSERT INTO `story` (`id`, `user_id`, `urlimage`, `countview`, `created_at`, `updated_at`) VALUES
(1, 1, 'https://localhost/api/api/story/image/OIP.png', 0, '2024-05-04 01:09:49', '2024-05-04 01:09:49'),
(2, 1, 'https://localhost/api/api/story/image/03f8a887a7891454b5180b9d41660edf838c3a7c4e3058531355c211fe3115e9.png', 0, '2024-05-09 04:01:15', '2024-05-09 04:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `bio` varchar(500) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `countpost` int(11) DEFAULT 0,
  `countarticle` int(11) DEFAULT 0,
  `following` int(11) DEFAULT 0,
  `followers` int(11) DEFAULT 0,
  `token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `bio`, `avatar`, `verified`, `countpost`, `countarticle`, `following`, `followers`, `token`, `created_at`, `updated_at`) VALUES
(1, 'satu', 'satu', 'satu', 'satu@example.com', 'bio satu', NULL, 0, 0, 0, 0, 0, 'satu', '2024-04-27 05:19:07', '2024-05-09 03:38:29'),
(2, 'dua', 'dua', 'dua', 'dua@example.com', 'dua', NULL, 0, 0, 0, 0, 0, 'dua', '2024-04-27 05:21:25', '2024-05-09 03:36:59'),
(3, 'tiga', 'tiga', 'tiga', 'tiga@example.com', 'tiga', NULL, 0, 0, 0, 0, 0, 'tiga', '2024-04-27 05:21:25', '2024-05-09 03:32:39'),
(4, 'empat', 'empat', 'empat', 'empat@example.com', 'empat', NULL, 0, 0, 0, 0, 0, 'empat', '2024-04-27 05:21:25', '2024-04-27 05:21:25'),
(5, 'lima', 'lima', 'lima', 'lima@example.com', 'lima', NULL, 0, 0, 0, 0, 0, 'lima', '2024-04-27 05:21:25', '2024-04-27 05:21:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `commentarticle`
--
ALTER TABLE `commentarticle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_article_id` (`article_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `commentpost`
--
ALTER TABLE `commentpost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `follower_id` (`follower_id`);

--
-- Indexes for table `likearticle`
--
ALTER TABLE `likearticle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_article` (`article_id`);

--
-- Indexes for table `likepost`
--
ALTER TABLE `likepost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `story`
--
ALTER TABLE `story`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `commentarticle`
--
ALTER TABLE `commentarticle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `commentpost`
--
ALTER TABLE `commentpost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `likearticle`
--
ALTER TABLE `likearticle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `likepost`
--
ALTER TABLE `likepost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `story`
--
ALTER TABLE `story`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commentarticle`
--
ALTER TABLE `commentarticle`
  ADD CONSTRAINT `fk_comment_article` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commentpost`
--
ALTER TABLE `commentpost`
  ADD CONSTRAINT `fk_post_article` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `fk_post_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `follow`
--
ALTER TABLE `follow`
  ADD CONSTRAINT `follow_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `follow_ibfk_2` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `likearticle`
--
ALTER TABLE `likearticle`
  ADD CONSTRAINT `fk_article` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `likepost`
--
ALTER TABLE `likepost`
  ADD CONSTRAINT `fk_liekpost_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `fk_likepost_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `story`
--
ALTER TABLE `story`
  ADD CONSTRAINT `story_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
