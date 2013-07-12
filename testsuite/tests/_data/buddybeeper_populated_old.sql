# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.28)
# Database: buddybeeper
# Generation Time: 2013-06-14 19:15:25 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table client_access_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_access_tokens`;

CREATE TABLE `client_access_tokens` (
  `access_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `user` int(11) unsigned NOT NULL,
  `client` int(11) unsigned NOT NULL,
  `expires_at` int(10) unsigned NOT NULL,
  `scope` varchar(3) NOT NULL DEFAULT '*',
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `client_access_tokens` WRITE;
/*!40000 ALTER TABLE `client_access_tokens` DISABLE KEYS */;

INSERT INTO `client_access_tokens` (`access_token`, `user`, `client`, `expires_at`, `scope`)
VALUES
	(X'50386143384E4C66317261436A7969624851336356714748734D2B7837634676656D4A722B7776347143564D626751675836467A647744674B7250624B4E6548',1,1,1571240749,'*');

/*!40000 ALTER TABLE `client_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table client_refresh_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_refresh_tokens`;

CREATE TABLE `client_refresh_tokens` (
  `refresh_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `user` int(11) unsigned NOT NULL,
  `client` int(11) unsigned NOT NULL,
  `scope` varchar(3) NOT NULL DEFAULT '*',
  PRIMARY KEY (`refresh_token`),
  UNIQUE KEY `user` (`user`,`client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `client_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `client_refresh_tokens` DISABLE KEYS */;

INSERT INTO `client_refresh_tokens` (`refresh_token`, `user`, `client`, `scope`)
VALUES
	(X'786E6B2B4A4C4331534F36575032692F674E6266665868576264794B635853734276477A484255786461504358443932517A52703656683358764A663975752F',1,1,'*');

/*!40000 ALTER TABLE `client_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` text,
  `secret` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;

INSERT INTO `clients` (`id`, `name`, `description`, `secret`)
VALUES
	(1,'Web','Web','f4wOVg9nBvLz2vprSa5T00Mh8986eXD7LG95pPBf7ctgR7IOo3qtjOT4Rys99cp1');

/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table event_activities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_activities`;

CREATE TABLE `event_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` int(11) unsigned NOT NULL,
  `user` int(11) unsigned DEFAULT NULL,
  `activity` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table event_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_comments`;

CREATE TABLE `event_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `text` text,
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table event_dates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_dates`;

CREATE TABLE `event_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table event_invites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_invites`;

CREATE TABLE `event_invites` (
  `event` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL,
  `event_token` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `notification_scope` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '*',
  PRIMARY KEY (`user`,`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `event_invites` WRITE;
/*!40000 ALTER TABLE `event_invites` DISABLE KEYS */;

INSERT INTO `event_invites` (`event`, `user`, `event_token`, `notification_scope`)
VALUES
	(1,1,X'6563334E31504E6E74576B6D56674D593861726E59623634574A70633333674379453275473157495935432F6B2B6A2B644C50396678344256686F6877516A65',X'2A');

/*!40000 ALTER TABLE `event_invites` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table event_locations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_locations`;

CREATE TABLE `event_locations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `event` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table event_votes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_votes`;

CREATE TABLE `event_votes` (
  `user` int(11) unsigned NOT NULL,
  `type` enum('date','activity','','') NOT NULL,
  `choice` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL,
  `final_date` int(11) unsigned DEFAULT NULL,
  `final_activity` int(11) unsigned DEFAULT NULL,
  `final_location` int(11) unsigned DEFAULT NULL,
  `description` varchar(160) NOT NULL DEFAULT '',
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;

INSERT INTO `events` (`id`, `user`, `final_date`, `final_activity`, `final_location`, `description`, `deadline`, `created_at`)
VALUES
	(1,0,NULL,NULL,NULL,'Whoopwhoop',NULL,'2013-06-14 21:09:18');

/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_communication_channels
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_communication_channels`;

CREATE TABLE `user_communication_channels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL,
  `type` enum('facebook','email','mobile','') CHARACTER SET utf8 NOT NULL DEFAULT '',
  `value` varchar(256) CHARACTER SET utf8 NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `is_bound` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `user_communication_channels` WRITE;
/*!40000 ALTER TABLE `user_communication_channels` DISABLE KEYS */;

INSERT INTO `user_communication_channels` (`id`, `user`, `type`, `value`, `active`, `is_bound`)
VALUES
	(1,1,'email','maximilian@localhost',1,0),
	(2,2,'email','test@localhost',1,0);

/*!40000 ALTER TABLE `user_communication_channels` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` int(11) unsigned DEFAULT NULL,
  `password` text,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `locale` char(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'en_EN',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `locale`, `created_at`)
VALUES
	(1,1,'ozJnjhM8R1yy.ozaKS5jwH9ChfdOQMxwCnL','Max',NULL,X'656E5F454E','2013-06-14 21:09:18'),
	(2,2,'d+LzJa.bBhHT2d+ZZefWe5izjyFitpUxeOG','Max',NULL,X'656E5F454E','2013-06-14 21:09:18');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table verification_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `verification_tokens`;

CREATE TABLE `verification_tokens` (
  `reference` int(11) unsigned DEFAULT NULL,
  `type` enum('signup','channel','password','') NOT NULL DEFAULT '',
  `token` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `data` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `reference` (`reference`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
