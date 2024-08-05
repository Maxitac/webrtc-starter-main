-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2024 at 09:11 PM
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
-- Database: `authentication`
--

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'W.C II', 'World Civilizations II', '2024-07-13 09:26:37'),
(2, 'NetSec ', 'Nework Security', '2024-07-13 13:26:17'),
(3, 'EH II', 'Ethical Hacking II', '2024-08-05 18:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `sessiondata`
--

CREATE TABLE `sessiondata` (
  `tmp_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessiondata`
--

INSERT INTO `sessiondata` (`tmp_id`, `user_id`, `username`, `room_id`) VALUES
(84, 8, 'Mark', 1),
(134, 5, 'sheila', 1),
(138, 3, 'cnsadmin', 1),
(139, 6, 'Bob', 1),
(140, 7, 'Anne', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` bigint(20) UNSIGNED NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp GENERATED ALWAYS AS (`created_at` + interval 3 hour) VIRTUAL,
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `session_token`, `created_at`, `user_id`) VALUES
(1, '', '2024-07-01 19:19:04', NULL),
(5, '76f7d704b4abec182dfbcbb2217f2aef', '2024-07-04 15:23:11', 3),
(6, 'ca5536ce8e335fdd833b55f1382b19e7', '2024-07-04 15:26:40', 3),
(7, '2a81e77fb3717da56a3194a5096e0d32', '2024-07-04 15:27:04', 3),
(8, 'c0fa89d2aa26781d1cbaea32e3fe58a3', '2024-07-04 15:27:42', 3),
(9, '9b6dd96b51038c034d098ab85f0a8922', '2024-07-04 15:30:09', 3),
(10, '6d6178018021f448357b5c002ef3c51f', '2024-07-04 15:30:10', 3),
(11, 'ad837a3149a620df049be366de896164', '2024-07-04 15:30:11', 3),
(12, '46f1662572766241b037531b30720a0a', '2024-07-04 15:30:55', 3),
(13, '90c1c284845720fb60f297a97afa158c', '2024-07-04 15:32:03', 3),
(14, '32d84ce86746cc02c74d630a94826d51', '2024-07-10 07:55:49', 3),
(15, 'd77c3460b164d59f76543bd8df1675c2', '2024-07-10 07:56:39', 3),
(16, '2defc832dd31036961911cfa64354598', '2024-07-10 07:59:54', 3),
(17, 'a7ebdeffbabdf5be295f79765404d1ef', '2024-07-10 12:20:48', 3),
(18, '184cc5b0cdff7390e29cb552c5b4769d', '2024-07-10 12:20:58', 3),
(19, '15eb7f2eb77ef9b5606a0628d67dc08f', '2024-07-10 12:21:50', 3),
(20, '555fd34911f079f051273c55f622f687', '2024-07-10 12:31:11', 3),
(21, '8d6d8abd70d150b8e722838d168c5f5d', '2024-07-10 12:34:58', 3),
(22, '561b408df0fe12323b6e708b4b707eea', '2024-07-11 05:00:00', 3),
(23, 'e652758dd9ea9e0d7f181b5a8b173a0d', '2024-07-13 07:48:33', 3),
(24, 'c2f47e81a503d346c9200a79a06a2af6', '2024-07-13 08:16:09', 3),
(25, 'db792e73c86ab1db47a2bffa23f8c473', '2024-07-13 08:17:05', 3),
(26, '2a54c02941027b25752720a0f8d976d1', '2024-07-13 08:19:10', 3),
(27, '9c33076549255939147b72a975806c34', '2024-07-13 08:22:10', 3),
(28, '1913c8986d450702e414809c4ef6f1e5', '2024-07-13 12:06:27', 3),
(29, 'd572531281fb5208ffd12f461ea0e042', '2024-07-13 12:07:01', 3),
(30, '221ff29d4958f2b408e54d9722122774', '2024-07-16 14:22:09', 3),
(31, 'c81eed004603a54b6a00c2c56c6434b5', '2024-07-17 07:08:17', 3),
(32, '60034ab9415afe3dad4b5feb9f74f9c6', '2024-07-17 13:20:07', 3),
(33, 'abe6b2e5339ccc2d410291fa1ce3d931', '2024-07-17 14:36:26', 3),
(34, 'fd875a1157a90b1a1326b9a92bc95dd6', '2024-07-17 14:37:38', 3),
(35, 'cae5dc1579269fe1246371e5ec0a27ac', '2024-07-17 14:51:54', 5),
(36, '35c78aabef01d76c333afef2446267d2', '2024-07-17 14:52:46', 5),
(37, '4f8820f765a1380548cf4bf297fcd89d', '2024-07-17 14:53:35', 3),
(38, '2946fef2c2f53a4bf3ff37dd933419b8', '2024-07-17 14:54:23', 5),
(39, '80c73053fede3a90861ec5abe6a9fbe4', '2024-07-19 07:01:17', 3),
(40, '96d692480d522e9bde3e0a7889b81801', '2024-07-19 07:04:51', 5),
(41, '72829932e95a151d77e8910cc5bf33f6', '2024-07-19 07:07:20', 3),
(42, '29ef7e9694a07e132e677017d6b216f5', '2024-07-19 08:29:00', 3),
(43, '2fb3728b6a272ba82ee0b7bc94b55843', '2024-07-19 08:29:27', 5),
(44, '70eacc64616aad6ccc94bc6a16fda783', '2024-07-19 08:35:41', 3),
(45, '928b9fd25040715c5e3e60a9bdd8fb1f', '2024-07-19 08:35:52', 5),
(46, '0dccbfe9d7a468f7894fb58238dd6d28', '2024-07-19 08:39:00', 3),
(47, '4033813f98d7b3e936c0e1ab6841b58a', '2024-07-19 08:39:43', 5),
(48, '31b002e58ae76713b3a6bf53d85ceda8', '2024-07-19 08:45:40', 5),
(49, 'f5d9d60e1e7325726b3097c46b458397', '2024-07-19 08:45:54', 5),
(50, 'ca2e25a23c2d5202902d288acf51087a', '2024-07-19 08:46:12', 3),
(51, 'a7a6c11ab29f3b606880781ec11f24a5', '2024-07-19 11:38:22', 3),
(52, 'e9c50d9d9a8f6df9c302aa91ee16d8d8', '2024-07-19 11:38:23', 5),
(53, '7c66fdbfc23d07e2c0f136afeb9c60c1', '2024-07-25 12:39:26', 3),
(54, 'c1f374239a51d6a06a8887eabb6940bf', '2024-07-27 17:56:55', 3),
(55, 'a3d040c7bf30203b330ec058e7fb200e', '2024-07-30 12:01:12', 3),
(56, '3104dfbaa6c2322eebb5e7f3ec856c82', '2024-07-30 13:19:13', 5),
(57, 'acfb89b7098354000561a58e6cdffe4e', '2024-07-30 13:29:38', 3),
(58, '7a3391966b8fb085d7dddb9d0d87b6d1', '2024-07-30 13:29:53', 5),
(59, 'f72b7f4339da99166889be9a7795f160', '2024-08-01 14:10:53', 5),
(60, 'b9708aa268052b3677044fd7d139a37e', '2024-08-01 14:10:59', 3),
(61, '4c508f2011320d3d8a2606ba489e4a45', '2024-08-01 14:32:21', 5),
(62, '67de01d522c09935f9ada8a3ca8ef3dd', '2024-08-01 14:43:24', 3),
(63, '2382b5f6d2b1e76f01a93cb8735dba2d', '2024-08-02 09:50:06', 3),
(64, 'a722e9b0916cf6550321cc66a7232062', '2024-08-02 09:50:32', 5),
(65, '8162a7e04fcfe526e6cd5e725b0600ce', '2024-08-02 09:51:02', 6),
(66, 'd838bfdc3dc0cf850b025b245171421c', '2024-08-02 09:51:43', 7),
(67, 'b17dd6118a38eab79cd130ea9a23f762', '2024-08-02 09:51:45', 8),
(68, '6a8ba445295f32c60f71bdd831c9aaf1', '2024-08-03 14:21:45', 3),
(69, '75b97a2edba19a669b3f2d989a6c55e7', '2024-08-03 14:21:59', 5),
(70, 'd2bf12ee15a701299f86152de5a45eba', '2024-08-03 14:43:21', 3),
(71, '26a173bba98bb41dffe1c06b3df6bedc', '2024-08-03 14:44:26', 5),
(72, 'a7cfee6522c004dfe3559ad91ca88eb1', '2024-08-03 15:22:50', 6),
(73, '7c27db3e91cce5bbcea4fd462c7f0b0a', '2024-08-03 16:43:23', 7),
(74, '15957df21ac8ccd3529f92d9238d28f6', '2024-08-03 17:31:51', 3),
(75, '0f86cf14cc84208fe0d5c3fdb1b62bd1', '2024-08-03 17:32:41', 5),
(76, 'e185bb0eabc60499909c453d2e797b42', '2024-08-03 19:11:04', 5),
(77, 'f32347b89644c3654cc88ce0492c022f', '2024-08-03 19:13:27', 3),
(78, 'd09ff6156d22f5ef3d22650d69b25b3d', '2024-08-03 19:16:56', 5),
(79, '01e96724f2fd09ab67b2b297b69ff3d8', '2024-08-05 17:29:15', 3),
(80, 'b64d48fd6651810b232bf24040978581', '2024-08-05 17:29:57', 5),
(81, '8b9efd621c4a90ff1de5edb463f8f46b', '2024-08-05 17:30:41', 6),
(82, '287fe922f60dd683ddf98c524c935fc1', '2024-08-05 18:06:33', 6),
(83, '3997e106086ec72acbac76b7a4dde5c8', '2024-08-05 18:08:18', 7);

-- --------------------------------------------------------

--
-- Table structure for table `useractivity`
--

CREATE TABLE `useractivity` (
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `activity_type` varchar(255) NOT NULL,
  `activity_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useractivity`
--

INSERT INTO `useractivity` (`activity_id`, `user_id`, `activity_type`, `activity_timestamp`) VALUES
(1, 3, 'Created a room', '2024-08-05 18:57:30'),
(2, 3, 'Joined room with ID 1', '2024-08-05 18:57:51'),
(3, 3, 'Joined room with ID 2', '2024-08-05 18:58:10'),
(4, 3, 'Elevated user sheila to host', '2024-08-05 18:58:56'),
(5, 3, 'Elevated a participant to host', '2024-08-05 18:58:56'),
(6, 3, 'Joined room with ID 1', '2024-08-05 19:01:54'),
(7, 6, 'Joined room with ID 1', '2024-08-05 19:06:35'),
(8, 7, 'Joined room with ID 1', '2024-08-05 19:08:23');

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE `userroles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`role_id`, `role_name`) VALUES
(1, 'host'),
(2, 'participant');

-- --------------------------------------------------------

--
-- Table structure for table `userrolesmapping`
--

CREATE TABLE `userrolesmapping` (
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userrolesmapping`
--

INSERT INTO `userrolesmapping` (`user_id`, `role_id`) VALUES
(3, 1),
(4, 2),
(5, 1),
(6, 2),
(7, 2),
(8, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `created_at`, `updated_at`) VALUES
(3, 'cnsadmin', '$2y$10$ony0fj8W4aym5aAzfeJVxeRao4DQEwEYC6QPhDZk27NL/lx.pcnp.', 'ian.mathu@strathmore.edu', '2024-07-01 18:12:02', '2024-07-01 18:12:02'),
(4, 'amadou', '$2y$10$1Esqa.7CpfP10GVK4O4lqu9pJ5D430SyCidnBEwYsjb4FOj4OTmMe', 'amadou@strathmore.edu', '2024-07-03 13:37:29', '2024-07-03 13:37:29'),
(5, 'sheila', '$2y$10$ffzPw68YzQDDKAN9xTsKRufzcmi4ylmfPGJEOaDytpSvnZj5qTuLy', 'sheila@gmail.com', '2024-07-17 15:51:35', '2024-07-17 15:51:35'),
(6, 'Bob', '$2y$10$Fr7j.jJdlgUyLolETPQOpecuhou7oZ6GKaWEiltaGDJaM6c7TrvpC', 'bob@gmail.com', '2024-08-02 10:47:57', '2024-08-02 10:47:57'),
(7, 'Anne', '$2y$10$6vennv6M2f7OhBTi5VvOzu3/a6c7p..3HGn6G1qnp0ELmaUD6WP7i', 'anne@gmail.com', '2024-08-02 10:48:50', '2024-08-02 10:48:50'),
(8, 'Mark', '$2y$10$YSPSQj0/ZscbKVYdAoN7QO9Py7jhaMNewpwhZ4DrrmiieXTQtfW06', 'mark@gmailcom', '2024-08-02 10:49:41', '2024-08-02 10:49:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessiondata`
--
ALTER TABLE `sessiondata`
  ADD PRIMARY KEY (`tmp_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD UNIQUE KEY `unique_session_token` (`session_token`);

--
-- Indexes for table `useractivity`
--
ALTER TABLE `useractivity`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `userroles`
--
ALTER TABLE `userroles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `userrolesmapping`
--
ALTER TABLE `userrolesmapping`
  ADD PRIMARY KEY (`user_id`,`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessiondata`
--
ALTER TABLE `sessiondata`
  MODIFY `tmp_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `useractivity`
--
ALTER TABLE `useractivity`
  MODIFY `activity_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `userroles`
--
ALTER TABLE `userroles`
  MODIFY `role_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
