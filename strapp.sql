-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 21 mars 2019 à 15:50
-- Version du serveur :  5.7.21
-- Version de PHP :  7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `strapp`
--

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `image_size` int(11) NOT NULL,
  `web_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `image_name`, `updated_at`, `image_size`, `web_path`) VALUES
(0, '0.jpg', '2019-03-13 00:00:00', 0, '/uploads/avatars/0.jpg'),
(13, '5c9347acbc13e711849790.jpg', '2019-03-21 08:13:32', 41297, 'uploads/avatars/5c9347a5ed609865204009.jpg'),
(14, '5c8286448022c759563162.jpg', '2019-03-08 15:12:04', 66255, ''),
(15, '5c82885d7121c062639466.jpg', '2019-03-08 15:21:01', 10103, ''),
(16, '5c8288ba9b528049716961.jpg', '2019-03-08 15:22:34', 456905, ''),
(17, '5c8a1944a1f96105895655.jpg', '2019-03-14 09:05:08', 263607, 'uploads/avatars/17256x25617.jpg'),
(18, '5c8a0fe52349c905559835.jpg', '2019-03-14 08:25:09', 389866, 'uploads/avatars/'),
(19, '5c9369577a191030883214.jpg', '2019-03-21 10:37:11', 75682, 'uploads/avatars/'),
(20, '5c936e0ac3012334021623.jpg', '2019-03-21 10:57:14', 33982, 'uploads/avatars/5c936de9b5e78435364022.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190307084542', '2019-03-07 08:45:47'),
('20190307090842', '2019-03-07 09:08:48'),
('20190307102012', '2019-03-07 10:20:24'),
('20190311082712', '2019-03-11 08:27:41'),
('20190313075441', '2019-03-13 07:55:06'),
('20190314093154', '2019-03-14 09:32:09');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_signup` datetime NOT NULL,
  `mood` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating_writer` double NOT NULL,
  `rating_reader` double NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `current_location` longtext COLLATE utf8mb4_unicode_ci COMMENT '(DC2Type:array)',
  `bio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649EA9FDD75` (`media_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `media_id`, `name`, `lastname`, `email`, `password`, `date_signup`, `mood`, `rating_writer`, `rating_reader`, `username`, `roles`, `current_location`, `bio`) VALUES
(1, 13, 'hubert', 'pfersdorff', 'a@a.fr', '$2y$13$rr5FkfxEI4.bkNvo9Z0zTuv3JoOjG6hxlDdZP8NfiHh3o5q92WnzC', '2019-03-07 08:55:31', 'tamereresdsdsdsdsdsdsdsdsrerer', 42, -42, 'tontonsat', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:28:\"7.745857699999999,48.5558015\";s:5:\"state\";s:8:\"Bas-Rhin\";}', 'I have crippling depression!'),
(2, 14, 'Gerard', 'Depardieu', 'g@g.fr', '$2y$13$/3hDP5YPGoEgCv0nWuGj8OWsyEtDVmW6WvAWQXUj41VCezchAvwnW', '2019-03-08 15:00:18', 'hello there', 0, 0, 'Gégé', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:36:\"7.746270399999999,48.555867799999994\";s:5:\"state\";s:8:\"Bas-Rhin\";}', NULL),
(3, 0, 'Emmanuel', 'Macron', 'm@m.fr', '$2y$13$TlX7Y7w9Nsv2Y4KGasiS/OY.Rc3ioY0p50phzQ5CnGGOwQjPC5r7.', '2019-03-08 15:13:35', 'hello there', 0, 0, 'Manu', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:5:\"state\";s:8:\"Bas-Rhin\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7460587,48.5558967\";}', NULL),
(4, 16, 'Cardinal', 'Barbarin', 'b@b.fr', '$2y$13$B3JZ4M7/q14uHavyCnQvWe0pDDJQuF6xsVxnbnuCiBsD9zV35AonG', '2019-03-08 15:15:36', 'hello there', 0, 0, 'Bibi', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:5:\"state\";s:8:\"Bas-Rhin\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7460587,48.5558967\";}', NULL),
(5, 15, 'Joseph', 'Staline', 's@s.fr', '$2y$13$CfiNcqdDSeGqaQpHAGue4u/MwYusrmBkcntwqprtaJgAxcafFsIfi', '2019-03-08 15:20:42', 'hello there', 0, 0, 'PèreDesPeuples', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:5:\"state\";s:8:\"Bas-Rhin\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7460587,48.5558967\";}', NULL),
(6, 17, 'Nicolas', 'Sarkozy', 'n@n.fr', '$2y$13$EEIIl4Icbkj.YM95y1V7KORvEKTvMMmyluOkt4MyS69bN7gf9jT4G', '2019-03-11 10:15:04', 'hello there', 0, 0, 'Sarko', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:5:\"state\";s:8:\"Bas-Rhin\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7460587,48.5558967\";}', NULL),
(11, 18, 'Jacques', 'Chirac', 'j@j.fr', '$2y$13$/2gwRwsmhouuGf5E1os6dOFL6kjL6w1FfEvZ/NIyy3xERm0KZEcvq', '2019-03-14 08:23:06', 'hello there', 0, 0, 'JackyDu93', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:10:\"Strasbourg\";s:5:\"state\";s:8:\"Bas-Rhin\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:28:\"7.746022199999999,48.5558927\";}', NULL),
(12, 19, 'Gandalf', 'The grey', 'gandalf@mordor.fr', '$2y$13$70R6Evd3oi81tzWS3TJfNeoOcl/5bT6ao9BPVESvp1ckoKNkIDvMy', '2019-03-21 10:36:21', 'You shall not pass, bitch', 0, 0, 'Mithrandir', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:11:\"Montpellier\";s:5:\"state\";s:8:\"Hérault\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7461607,48.5558307\";}', 'I\'m gandalf.'),
(13, 20, 'Frodo', 'Baggins', 'frodo@frodo.fr', '$2y$13$QU1z5KCbIjbmfivvyC4egeOn08PvnOGoUeNG8w7ZPGca3e51BRzHy', '2019-03-21 10:53:44', 'Gandoulf touch my pipi', 0, 0, 'FrodoFaggins', 'a:1:{i:0;s:9:\"ROLE_USER\";}', 'a:4:{s:4:\"city\";s:11:\"Montpellier\";s:5:\"state\";s:8:\"Hérault\";s:7:\"country\";s:6:\"France\";s:5:\"coord\";s:20:\"7.7460542,48.5557989\";}', 'Bio - I am a lovely turtle and I like big trains.');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649EA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
