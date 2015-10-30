-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solution_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `solution_id` (`solution_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `answer_ibfk_3` FOREIGN KEY (`solution_id`) REFERENCES `solution` (`id`),
  CONSTRAINT `answer_ibfk_4` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


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
  `methods` text COLLATE utf8_czech_ci NOT NULL,
  `support` text COLLATE utf8_czech_ci NOT NULL,
  `footer` text COLLATE utf8_czech_ci NOT NULL,
  `contact_email` text COLLATE utf8_czech_ci NOT NULL,
  `review_count` tinyint(4) NOT NULL DEFAULT '5',
  `upload_max_filesize_kb` smallint(6) NOT NULL,
  `ga_code` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `course` (`id`, `name`, `goals`, `methods`, `support`, `footer`, `contact_email`, `review_count`, `upload_max_filesize_kb`, `ga_code`) VALUES
(1, 'Test course',  'The main goal of the test course is to provide a functionality check of the app. One could even say it\'s a *demo*!',  '', '', '', '', 5,  500,  'BB');


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
  `entity` enum('Assignment','Course','Review','Solution','Unit','User') COLLATE utf8_czech_ci NOT NULL,
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
  `entity_name` enum('Objection','Review','ReviewComment','Solution','Unit','User') COLLATE utf8_czech_ci NOT NULL,
  `entity_identifier` int(11) NOT NULL,
  `action` enum('create','submit','open','edit','delete','login','logout') COLLATE utf8_czech_ci NOT NULL,
  `logged_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `course_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `unit_id` (`unit_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`),
  CONSTRAINT `message_ibfk_3` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) unsigned NOT NULL,
  `order` smallint(6) NOT NULL,
  `type` enum('plaintext','markdown') COLLATE utf8_czech_ci NOT NULL,
  `text` text COLLATE utf8_czech_ci NOT NULL,
  `prefill` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_id` (`assignment_id`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `solution_id` int(10) unsigned NOT NULL,
  `reviewed_by_id` int(10) unsigned NOT NULL,
  `opened_at` datetime NOT NULL,
  `status` enum('prep','ok','problem','objection','fixed') COLLATE utf8_czech_ci NOT NULL,
  `assessment` text COLLATE utf8_czech_ci NOT NULL,
  `score` tinyint(4) DEFAULT NULL,
  `notes` text COLLATE utf8_czech_ci,
  `submitted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solution_id` (`solution_id`),
  KEY `reviewed_by_id` (`reviewed_by_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`solution_id`) REFERENCES `solution` (`id`),
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`reviewed_by_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `reviewcomment`;
CREATE TABLE `reviewcomment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `submitted_at` datetime NOT NULL,
  `comment` text COLLATE utf8_czech_ci NOT NULL,
  `review_status` enum('prep','ok','problem','objection','fixed') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `review_id` (`review_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reviewcomment_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  CONSTRAINT `reviewcomment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
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
  `rubrics` text COLLATE utf8_czech_ci NOT NULL,
  `generator` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `unit_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `unit` (`id`, `course_id`, `published_since`, `reviews_since`, `objections_since`, `finalized_since`, `name`, `goals`, `reading`, `rubrics`, `generator`) VALUES
(1, 1,  '2015-08-01 00:00:00',  '2015-06-01 00:00:00',  '2016-08-01 00:00:00',  '2016-08-01 00:00:00',  'Test Unit',  'The purpose of the *Test Unit* is to be one of the best parts of the *Test Course*. And happily so.',  'With this test stuff, you\'re lucky: you don\'t need to read anything. You can even do [something useless](http://www.theuselessweb.com).',  '', 'TestGenerator');


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password_reset_token` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `password_reset_valid_until` datetime DEFAULT NULL,
  `role` enum('admin','registered') COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `name`, `email`, `password`, `password_reset_token`, `password_reset_valid_until`, `role`) VALUES
(1, 'Test Admin', 'admin@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', '', '0000-00-00 00:00:00',  NULL),
(2, 'Test Assistant', 'assistant@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', 'b39b0361a2', '2015-09-30 02:38:31',  NULL),
(3, 'Test Student', 'student@test.dev', '$2y$10$ClCAL6zNDmsdo77MC6y3lukuiQ8lEOHAIfHuRG4TfdPxFIlkxolEG', '', '0000-00-00 00:00:00',  NULL);

-- 2015-10-30 18:43:07
