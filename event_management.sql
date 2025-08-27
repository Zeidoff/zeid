-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 27 août 2025 à 12:44
-- Version du serveur : 5.7.36
-- Version de PHP : 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `event_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `date` datetime NOT NULL,
  `organizer_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organizer_id` (`organizer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `image`, `date`, `organizer_id`) VALUES
(9, 'Fitness Fusion Expo', 'qsdfo', 'uploads/event_68ab736ae6e15.jpg', '2025-08-24 21:17:00', 1),
(6, 'my eventsdqfqdsf', 'odfsqioifqdQSDFDSFQSDFQ', 'Uploads/event_68ab7018c84ce.jpg', '2025-08-30 00:00:00', 4),
(7, 'my event AAAA', 'qpdsofopqfdso', 'uploads/event_68ab6f06d1d17.jpg', '2025-08-30 00:00:00', 4),
(3, 'Tech Innovators Conference', 'oserk', 'uploads/event_68ab628c4976e.jpg', '2025-08-24 20:05:00', 4),
(4, 'Fitness Fusion Expo', 'A fitness and wellness event with live training sessions, workshops, and product showcases.', 'uploads/event_68ab62abc3ab5.jpg', '2025-08-24 20:06:00', 4),
(10, 'Fitness Fusion Expo', 'sqdfoisdqfo', 'uploads/event_68ab74126f420.jpg', '2025-08-31 21:20:00', 4);

-- --------------------------------------------------------

--
-- Structure de la table `participations`
--

DROP TABLE IF EXISTS `participations`;
CREATE TABLE IF NOT EXISTS `participations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_participation` (`user_id`,`event_id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `participations`
--

INSERT INTO `participations` (`id`, `user_id`, `event_id`) VALUES
(3, 3, 1),
(5, 5, 9),
(6, 5, 6),
(7, 5, 7);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','organizer','participant') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'edsedsssss', '$2y$10$kqJOUVyZA3w1V/Sc2vOYZus0x3.RpDWdoVyBzb.rHmwABKJCK2jGe', 'eds@gmail.com', 'organizer'),
(2, 'zied', '$2y$10$25/5tHantIJd0iZgevzbC.YvDHMoOLmWRf.5zjbpEnPPRnqjpo8Uq', 'zied@gmail.com', 'admin'),
(3, 'participant1', '$2y$10$qTkGgd.ffAAR0jwNWyoV4OYZAyiK3RwXWImrrqJu9zJnaU6.5rewy', 'participant@gmail.com', 'participant'),
(4, 'orginizer1', '$2y$10$x9MWqXdVkJihALMSNk8Cbet6RbwBNjVekvGT0gqBzwlgsP.g/OlwW', 'orginizer@gmail.com', 'organizer'),
(5, 'zeeid', '$2y$10$0pmyYd7/E1CVk9sUjtwHuu1akVhA4GD9G8hkUYNyIthc5RNBngbiq', 'zeeid@gmail.com', 'participant');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
