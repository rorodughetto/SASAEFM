-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Hôte : db5011766148.hosting-data.io
-- Généré le : mer. 04 oct. 2023 à 16:25
-- Version du serveur : 10.6.15-MariaDB-1:10.6.15+maria~deb11-log
-- Version de PHP : 7.0.33-0+deb9u12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dbs9912373`
--
DROP DATABASE IF EXISTS `dbs9912373`;
CREATE DATABASE IF NOT EXISTS `dbs9912373` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE `dbs9912373`;

-- --------------------------------------------------------

--
-- Structure de la table `room`
--

DROP TABLE IF EXISTS `room`;
CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Déchargement des données de la table `room`
--

INSERT INTO `room` (`id`, `room_name`) VALUES
(9, 'cellule 1'),
(10, 'cellule 2'),
(11, 'cellule 3'),
(12, 'cellule 4'),
(13, 'cellule 5'),
(14, 'Salon Saphir'),
(15, 'Salon Topaze'),
(16, 'Salon Jade'),
(17, 'Salon Emeraude');

-- --------------------------------------------------------

--
-- Structure de la table `schedule_list`
--

DROP TABLE IF EXISTS `schedule_list`;
CREATE TABLE `schedule_list` (
  `id` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `deceased_name` varchar(255) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `toilet_and_dressing` enum('Oui','Non') NOT NULL,
  `care` enum('Oui','Non') NOT NULL,
  `ritual_toilet` enum('Oui','Non') NOT NULL,
  `technical_room_reservation` enum('Oui','Non') NOT NULL,
  `technical_room_reservation_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Déchargement des données de la table `schedule_list`
--

INSERT INTO `schedule_list` (`id`, `author`, `deceased_name`, `room_name`, `start_datetime`, `end_datetime`, `toilet_and_dressing`, `care`, `ritual_toilet`, `technical_room_reservation`, `technical_room_reservation_time`) VALUES
(17, 'EYQUEMXA', 'ALBERT', 'cellule 1', '2023-09-14 19:29:00', '2023-09-15 19:29:00', 'Non', 'Non', 'Non', 'Non', '0000-00-00 00:00:00'),
(18, 'EYQUEMXA', 'BOB', 'Salon Saphir', '2023-09-22 16:16:00', '2023-09-27 09:30:00', 'Non', 'Oui', 'Non', 'Non', '0000-00-00 00:00:00'),
(19, 'EYQUEMXA', 'CHAOUI', 'cellule 1', '2023-09-23 22:00:00', '2023-09-28 14:00:00', 'Non', 'Non', 'Oui', 'Non', '0000-00-00 00:00:00'),
(20, 'EYQUEMXA', 'MICKEY', 'cellule 5', '2023-09-22 16:30:00', '2023-10-08 16:23:00', 'Oui', 'Non', 'Non', 'Non', '0000-00-00 00:00:00'),
(21, 'EYQUEMXA', 'CHARLES', 'Salon Jade', '2023-09-22 16:26:00', '2023-10-06 16:26:00', 'Non', 'Oui', 'Non', 'Oui', '2023-09-23 16:26:00'),
(22, 'EYQUEMXA', 'CAMILIA', 'cellule 3', '2023-09-21 16:29:00', '2023-10-14 16:29:00', 'Non', 'Oui', 'Non', 'Non', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `super` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `super`) VALUES
(7, 'EYQUEMRO', '$2y$10$uozrVqCX.rtCzdz.ZkQzUOezI8OPNMLGjM0btLXcrynwmZt2iisjK', 0),
(10, 'EYQUEMXA', '$2y$10$/U.VPo6ilLBChz79llEDeeCMDla8bqQor/tyoSSED3QWqBsvIFEcK', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `schedule_list`
--
ALTER TABLE `schedule_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
