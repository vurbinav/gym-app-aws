-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 03:02 AM
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
-- Database: `gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `gym`
--

CREATE TABLE `gym` (
  `id_gym` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym`
--

INSERT INTO `gym` (`id_gym`, `location`, `name`) VALUES
(1, 'Atlanta', 'ATL Fitness');

-- --------------------------------------------------------

--
-- Table structure for table `gym_classes`
--

CREATE TABLE `gym_classes` (
  `id_class` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_classes`
--

INSERT INTO `gym_classes` (`id_class`, `name`, `max_capacity`, `description`, `img`, `id_user`) VALUES
(5, 'Jose Alejandro Espinoza Sánchez', 23, 'asddsadsa', '1732263081_imagen_2024-11-22_031028205.png', 1),
(6, 'Cardio', 40, 'bnnbnnbbv', '1732264658_imagen_2024-11-22_033737643.png', 1),
(7, 'Prueba', 10, 'lmnsdflkmnhfdnlj,vdsljksldjklsdlkdsljksdjlkdslkjdsjlkdsjlk', '1732297893_imagen_2024-11-22_125123473.png', 1),
(8, 'Cardio', 15, 'Cardio training can improve your heart health, help with weight loss, and boost your mood. Some of these exercises include: Squat jumps, Burpees, Jumping rope, and Dancing! Join Today!', '1733441361_cardio.webp', 17),
(9, 'Weightlifting', 5, 'Want to get Stronger? Join Now!', '1733441735_1732264658_imagen_2024-11-22_033737643.png', 17),
(10, 'Dancing', 10, 'Join Today!', NULL, 17);

-- --------------------------------------------------------

--
-- Table structure for table `gym_members`
--

CREATE TABLE `gym_members` (
  `id_member` int(11) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `membership_plan` int(11) NOT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `ntarjeta` int(16) NOT NULL,
  `fvencimiento` varchar(6) NOT NULL,
  `cvv` int(3) NOT NULL,
  `cobroautomatico` tinyint(1) NOT NULL,
  `assistance` int(11) DEFAULT 0,
  `absences` int(11) DEFAULT 0,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_members`
--

INSERT INTO `gym_members` (`id_member`, `direccion`, `membership_plan`, `phone`, `ntarjeta`, `fvencimiento`, `cvv`, `cobroautomatico`, `assistance`, `absences`, `id_user`) VALUES
(4, '1', 2, '1', 1, '1', 111, 1, 0, 0, 15),
(5, '2', 2, '2', 2, '2', 222, 1, 0, 0, 16),
(6, '1', 2, '1', 1, '2', 333, 1, 0, 0, 18);

-- --------------------------------------------------------

--
-- Table structure for table `gym_member_classes`
--

CREATE TABLE `gym_member_classes` (
  `id_members` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_class` int(11) NOT NULL,
  `enrolled` tinyint(1) DEFAULT 0,
  `assisted` tinyint(1) DEFAULT 0,
  `absent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_member_classes`
--

INSERT INTO `gym_member_classes` (`id_members`, `id_user`, `id_class`, `enrolled`, `assisted`, `absent`) VALUES
(18, 12, 7, 1, 1, 0),
(19, 15, 8, 1, 1, 0),
(21, 18, 8, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gym_plan`
--

CREATE TABLE `gym_plan` (
  `id_plan` int(11) NOT NULL,
  `plan_type` varchar(20) NOT NULL,
  `amount` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_plan`
--

INSERT INTO `gym_plan` (`id_plan`, `plan_type`, `amount`, `description`, `price`) VALUES
(1, 'Basic', 5, 'The Basic plan includes access to the machines during limited hours and up to 5 group classes per month.', 10),
(2, 'Premium', 30, 'The Premium plan includes unlimited access to the machines, 30 group classes per month, personalized advice with an instructor and full schedule (24/7).', 30),
(3, 'Standard', 10, 'The Standard plan includes limited access to the machines, 10 group classes per month and limited hours (6 am to 6 pm).', 25);

-- --------------------------------------------------------

--
-- Table structure for table `gym_roles`
--

CREATE TABLE `gym_roles` (
  `id_role` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_roles`
--

INSERT INTO `gym_roles` (`id_role`, `role_name`) VALUES
(3, 'Administrator'),
(2, 'Instructor'),
(1, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `gym_schedule`
--

CREATE TABLE `gym_schedule` (
  `id` int(11) NOT NULL,
  `id_instructor` int(11) NOT NULL,
  `id_class` int(11) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_schedule`
--

INSERT INTO `gym_schedule` (`id`, `id_instructor`, `id_class`, `start_date`, `end_date`) VALUES
(8, 17, 8, '2024-12-09 10:00:00', '2024-12-09 12:00:00'),
(9, 17, 9, '2024-12-09 13:00:00', '2024-12-09 14:00:00'),
(10, 17, 10, '2024-12-12 13:00:00', '2024-12-12 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `gym_users`
--

CREATE TABLE `gym_users` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_gym` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_users`
--

INSERT INTO `gym_users` (`id_user`, `email`, `password`, `nombres`, `apellidos`, `creation_date`, `id_gym`, `id_role`) VALUES
(12, 'murbina@gmail.com', '$2y$10$KbS6i9PdhG3lrkYO4Owx3uSG0OyRETgX7l8RE7o85WvBbU8pE8JkC', 'Martin', 'Urbina', '2024-11-22 17:47:14', 1, 1),
(14, 'test@example.us', '$2y$10$9xvkzD3OMUwPKh/SfMrUGOCm2/V9FOOg30I60HZh/jLmXFZ/0HKWK', 'Jon', 'Doe', '2024-11-22 20:52:47', 1, 1),
(15, 'user@gmail.com', '$2y$10$0hQYkps4Qrts6KBqElbEmub3mtcL2ldRKdFOS/mCaE4Ws1XDd0jgC', 'User', 'User', '2024-12-05 18:44:08', 1, 1),
(16, 'admin@gmail.com', '$2y$10$COEiIc9.QhXCOYnPk1A4/.zERsYbgzf1Zs74EWONWle7wKKH9rJC6', 'Admin', 'Admin', '2024-12-05 19:44:23', 1, 3),
(17, 'instructor@gmail.com', '$2y$10$PNo1LJvZg54XsVxwit6r7.VY/lr6IEll.G4HdO3Z1rd0ABvMAJwJy', 'Instructor', 'Instructor', '2024-12-05 20:12:43', 1, 2),
(18, 'john@gmail.com', '$2y$10$PmRLlN5EQIEWIZcl7Q/3le2s0gm.LKjRzJz6z3DQeIsKxAbkg.CRK', 'john', 'john', '2024-12-06 19:07:58', 1, 1),
(19, 'jack@gmail.com', '$2y$10$CIL6tFO3mNVyoBbL8n.EJ.90AZKpWSV6L1lXw3kY52mqS6WozxasW', 'jack', 'jack', '2024-12-06 19:17:41', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gym`
--
ALTER TABLE `gym`
  ADD PRIMARY KEY (`id_gym`);

--
-- Indexes for table `gym_classes`
--
ALTER TABLE `gym_classes`
  ADD PRIMARY KEY (`id_class`);

--
-- Indexes for table `gym_members`
--
ALTER TABLE `gym_members`
  ADD PRIMARY KEY (`id_member`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `gym_member_classes`
--
ALTER TABLE `gym_member_classes`
  ADD PRIMARY KEY (`id_members`),
  ADD KEY `id_class` (`id_class`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `gym_plan`
--
ALTER TABLE `gym_plan`
  ADD PRIMARY KEY (`id_plan`);

--
-- Indexes for table `gym_roles`
--
ALTER TABLE `gym_roles`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `gym_schedule`
--
ALTER TABLE `gym_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_class` (`id_class`),
  ADD KEY `fk_instructor_user` (`id_instructor`);

--
-- Indexes for table `gym_users`
--
ALTER TABLE `gym_users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_gym` (`id_gym`),
  ADD KEY `fk_gym_users_roles` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gym`
--
ALTER TABLE `gym`
  MODIFY `id_gym` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gym_classes`
--
ALTER TABLE `gym_classes`
  MODIFY `id_class` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gym_members`
--
ALTER TABLE `gym_members`
  MODIFY `id_member` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gym_member_classes`
--
ALTER TABLE `gym_member_classes`
  MODIFY `id_members` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `gym_plan`
--
ALTER TABLE `gym_plan`
  MODIFY `id_plan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `gym_roles`
--
ALTER TABLE `gym_roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gym_schedule`
--
ALTER TABLE `gym_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gym_users`
--
ALTER TABLE `gym_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gym_members`
--
ALTER TABLE `gym_members`
  ADD CONSTRAINT `gym_members_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `gym_users` (`id_user`);

--
-- Constraints for table `gym_member_classes`
--
ALTER TABLE `gym_member_classes`
  ADD CONSTRAINT `gym_member_classes_ibfk_1` FOREIGN KEY (`id_class`) REFERENCES `gym_classes` (`id_class`),
  ADD CONSTRAINT `gym_member_classes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `gym_users` (`id_user`);

--
-- Constraints for table `gym_schedule`
--
ALTER TABLE `gym_schedule`
  ADD CONSTRAINT `fk_instructor_user` FOREIGN KEY (`id_instructor`) REFERENCES `gym_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gym_schedule_ibfk_2` FOREIGN KEY (`id_class`) REFERENCES `gym_classes` (`id_class`);

--
-- Constraints for table `gym_users`
--
ALTER TABLE `gym_users`
  ADD CONSTRAINT `fk_gym_users_roles` FOREIGN KEY (`id_role`) REFERENCES `gym_roles` (`id_role`),
  ADD CONSTRAINT `gym_users_ibfk_1` FOREIGN KEY (`id_gym`) REFERENCES `gym` (`id_gym`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
