-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 11 Avril 2011 à 13:37
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `iseplive`
--

-- --------------------------------------------------------

--
-- Structure de la table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `ext` varchar(4) NOT NULL COMMENT 'Extension du fichier stocké correspondant',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- RELATIONS POUR LA TABLE `attachments`:
--   `post_id`
--       `posts` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `url_name` varchar(100) NOT NULL,
  `order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order` (`order`),
  KEY `url_name` (`url_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `date_start` (`date_start`),
  KEY `date_end` (`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS POUR LA TABLE `events`:
--   `post_id`
--       `posts` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `url_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `creation_date` date NOT NULL,
  `mail` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_name` (`url_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `groups_users`
--

DROP TABLE IF EXISTS `groups_users`;
CREATE TABLE IF NOT EXISTS `groups_users` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL COMMENT 'Poste',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'si =1, l''utilisateur peut poster des messages officiels et modifier les informations de l''association',
  `order` tinyint(3) unsigned NOT NULL DEFAULT '255',
  UNIQUE KEY `group_id` (`group_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `order` (`order`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS POUR LA TABLE `groups_users`:
--   `user_id`
--       `users` -> `id`
--   `group_id`
--       `groups` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `time` int(11) NOT NULL,
  `category_id` tinyint(3) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  `official` tinyint(1) NOT NULL COMMENT '0 si le post n''est pas officiel, 1 s''il est officiel au sein de l''association',
  `private` tinyint(1) NOT NULL COMMENT '0 si le post est visible pour les non membres connectés, 1 s''il est visible uniquement pour les membres connectés',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `official` (`official`),
  KEY `private` (`private`),
  KEY `time` (`time`),
  KEY `category_id` (`category_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

--
-- RELATIONS POUR LA TABLE `posts`:
--   `user_id`
--       `users` -> `id`
--   `category_id`
--       `categories` -> `id`
--   `group_id`
--       `groups` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `post_comments`
--

DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE IF NOT EXISTS `post_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `attachment_id` int(10) unsigned DEFAULT NULL,
  `message` text NOT NULL,
  `time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`,`time`),
  KEY `attachment_id` (`attachment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- RELATIONS POUR LA TABLE `post_comments`:
--   `post_id`
--       `posts` -> `id`
--   `attachment_id`
--       `attachments` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `post_comment_likes`
--

DROP TABLE IF EXISTS `post_comment_likes`;
CREATE TABLE IF NOT EXISTS `post_comment_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `comment_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `attachement_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_id` (`comment_id`),
  KEY `user_id` (`user_id`),
  KEY `attachement_id` (`attachement_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- RELATIONS POUR LA TABLE `post_comment_likes`:
--   `post_id`
--       `posts` -> `id`
--   `comment_id`
--       `post_comments` -> `id`
--   `user_id`
--       `users` -> `id`
--   `attachement_id`
--       `attachments` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `post_likes`
--

DROP TABLE IF EXISTS `post_likes`;
CREATE TABLE IF NOT EXISTS `post_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `attachment_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `post_id` (`post_id`),
  KEY `attachment_id` (`attachment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=9 ;

--
-- RELATIONS POUR LA TABLE `post_likes`:
--   `post_id`
--       `posts` -> `id`
--   `user_id`
--       `users` -> `id`
--   `attachment_id`
--       `attachments` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `username` varchar(10) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `promo` smallint(5) unsigned NOT NULL,
  `cesure` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`username`),
  KEY `promo` (`promo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `surveys`
--

DROP TABLE IF EXISTS `surveys`;
CREATE TABLE IF NOT EXISTS `surveys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `question` varchar(255) NOT NULL,
  `multiple` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 : 1 seule réponse possible, 1 : plusieurs réponses possibles',
  `date_end` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS POUR LA TABLE `surveys`:
--   `post_id`
--       `posts` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `survey_answers`
--

DROP TABLE IF EXISTS `survey_answers`;
CREATE TABLE IF NOT EXISTS `survey_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL,
  `answer` varchar(255) NOT NULL,
  `nb_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `votes` text NOT NULL COMMENT 'Tableau JSON des votants',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- RELATIONS POUR LA TABLE `survey_answers`:
--   `survey_id`
--       `surveys` -> `id`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mail` varchar(255) NOT NULL,
  `msn` varchar(255) NOT NULL,
  `jabber` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` mediumint(9) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `cellphone` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `groups_users`
--
ALTER TABLE `groups_users`
  ADD CONSTRAINT `groups_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groups_users_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `post_comment_likes`
--
ALTER TABLE `post_comment_likes`
  ADD CONSTRAINT `post_comment_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comment_likes_ibfk_2` FOREIGN KEY (`comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comment_likes_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comment_likes_ibfk_4` FOREIGN KEY (`attachement_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `surveys`
--
ALTER TABLE `surveys`
  ADD CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD CONSTRAINT `survey_answers_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE;
