-- Adminer 4.0.2 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `assignment`;
CREATE TABLE `assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(10) unsigned NOT NULL,
  `student_id` int(10) unsigned NOT NULL,
  `generated_at` datetime NOT NULL,
  `preface` text COLLATE utf8_czech_ci NOT NULL,
  `questions` text COLLATE utf8_czech_ci NOT NULL,
  `rubrics` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `unit_id` (`unit_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`),
  CONSTRAINT `assignment_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `goals` text COLLATE utf8_czech_ci NOT NULL,
  `contact_email` text COLLATE utf8_czech_ci NOT NULL,
  `review_count` tinyint(4) NOT NULL DEFAULT '5',
  `upload_max_filesize_kb` smallint(6) NOT NULL,
  `ga_code` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `course` (`id`, `name`, `goals`, `contact_email`, `review_count`, `upload_max_filesize_kb`, `ga_code`) VALUES
(1, 'Test course',  'The main goal of the test course is to provide a functionality check of the app. One could even say it\'s a *demo*!',  '', 5,  500,  '0'),
(2, 'Transformace dat pomocí počítače', 'Cílem předmětu je porozumění možnostem práce s informacemi v počítači.\r\n \r\nPočítač často vnímáme jako nástroj pro snadné vkládání, ukládání a čtení informací – ať už jde o texty, hudbu, videa či multimediální a interaktivní obsahy. \r\n\r\nPředmět Práce s informacemi prostřednictvím počítače se soustředí na v předchozím výčtu chybějící aktivitu: transformaci informací. Studenti se naučí strukturovat vkládané informace, aby s nimi bylo možné provádět další operace, a jak provádět tyto operace – pomocí jednoduchého textového editoru, tabulkového procesoru, databáze nebo v jednoduchých skriptech. Kurz je vyučován hybridní formou – zadané texty čtou a úkoly plní studenti před společným seminářem, na němž je povinná účast.\r\n',  '', 5,  500,  '0');

DROP TABLE IF EXISTS `enrollment`;
CREATE TABLE `enrollment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL,
  `role` enum('student','assistant','admin') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`user_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `enrollment` (`id`, `user_id`, `course_id`, `role`) VALUES
(1, 1,  1,  'admin'),
(2, 2,  1,  'assistant'),
(3, 3,  1,  'student');

DROP TABLE IF EXISTS `favorite`;
CREATE TABLE `favorite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `entity` enum('Assignment','Course','Review','Unit','User') COLLATE utf8_czech_ci NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `saved_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `favorite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `entity_name` enum('Objection','Review','Solution','Unit','User') COLLATE utf8_czech_ci NOT NULL,
  `entity_identifier` int(11) NOT NULL,
  `action` enum('create','submit','open','edit','delete','login','logout') COLLATE utf8_czech_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `objection`;
CREATE TABLE `objection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `submitted_at` datetime NOT NULL,
  `objection` text COLLATE utf8_czech_ci NOT NULL,
  `evaluated` tinyint(1) NOT NULL,
  `arbiter_id` int(10) unsigned NOT NULL,
  `legitimate` tinyint(1) NOT NULL,
  `comment` text COLLATE utf8_czech_ci NOT NULL,
  `evaluated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  KEY `participant_id` (`user_id`),
  KEY `arbiter_id` (`arbiter_id`),
  CONSTRAINT `objection_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  CONSTRAINT `objection_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `objection_ibfk_3` FOREIGN KEY (`arbiter_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `objection_open`;
CREATE TABLE `objection_open` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `objection_id` int(10) unsigned NOT NULL,
  `arbiter_id` int(10) unsigned NOT NULL,
  `opened_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `objection_id` (`objection_id`),
  KEY `arbiter_id` (`arbiter_id`),
  CONSTRAINT `objection_open_ibfk_1` FOREIGN KEY (`objection_id`) REFERENCES `objection` (`id`),
  CONSTRAINT `objection_open_ibfk_2` FOREIGN KEY (`arbiter_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solution_id` int(10) unsigned NOT NULL,
  `reviewed_by_id` int(10) unsigned NOT NULL,
  `opened_at` datetime NOT NULL,
  `assessment` text COLLATE utf8_czech_ci NOT NULL,
  `score` tinyint(4) DEFAULT NULL,
  `comments` text COLLATE utf8_czech_ci,
  `submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solution_id` (`solution_id`),
  KEY `reviewed_by_id` (`reviewed_by_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`solution_id`) REFERENCES `solution` (`id`),
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`reviewed_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `solution`;
CREATE TABLE `solution` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(10) unsigned NOT NULL,
  `assignment_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `submitted_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `answer` text COLLATE utf8_czech_ci NOT NULL,
  `attachment` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`),
  KEY `unit_id` (`unit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `solution_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`),
  CONSTRAINT `solution_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`id`),
  CONSTRAINT `solution_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `unit`;
CREATE TABLE `unit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(10) unsigned NOT NULL,
  `published_since` datetime NOT NULL,
  `reviews_since` datetime NOT NULL,
  `objections_since` datetime NOT NULL,
  `finalized_since` datetime NOT NULL,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `goals` text COLLATE utf8_czech_ci NOT NULL,
  `reading` text COLLATE utf8_czech_ci NOT NULL,
  `generator` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `unit_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `unit` (`id`, `course_id`, `published_since`, `reviews_since`, `objections_since`, `finalized_since`, `name`, `goals`, `reading`, `generator`) VALUES
(1, 1,  '2015-08-01 00:00:00',  '2016-06-01 00:00:00',  '2016-08-01 00:00:00',  '2016-08-01 00:00:00',  'Test Unit',  'The purpose of the *Test Unit* is to be one of the best parts of the *Test Course*. And happily so.',  'With this test stuff, you\'re lucky: you don\'t need to read anything. You can even do [something useless](http://www.theuselessweb.com).',  'TestGenerator');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password_reset_token` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `password_reset_valid_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `name`, `email`, `password`, `password_reset_token`, `password_reset_valid_until`) VALUES
(1, 'Test Admin', 'admin@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', '', '0000-00-00 00:00:00'),
(2, 'Test Assistant', 'assistant@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', 'b39b0361a2', '2015-09-30 02:38:31'),
(3, 'Test Student', 'student@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', '', '0000-00-00 00:00:00');

-- 2015-10-01 13:05:08
