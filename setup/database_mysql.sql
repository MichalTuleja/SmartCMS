-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2015 at 01:11 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mycms`
--

-- --------------------------------------------------------

--
-- Table structure for table `test_articles`
--

CREATE TABLE IF NOT EXISTS `{{db_articles}}` (
  `article_id` int(10) NOT NULL AUTO_INCREMENT,
  `article_public` tinyint(1) NOT NULL,
  `article_removed` tinyint(1) NOT NULL,
  `article_user` varchar(32) NOT NULL,
  `article_ctime` datetime NOT NULL,
  `article_mtime` datetime NOT NULL,
  `article_title` varchar(128) NOT NULL,
  `article_descr` text,
  `article_body` text NOT NULL,
  `article_bg` int(10) DEFAULT NULL,
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `test_articles`
--

INSERT INTO `{{db_articles}}` (`article_id`, `article_public`, `article_removed`, `article_user`, `article_ctime`, `article_mtime`, `article_title`, `article_descr`, `article_body`, `article_bg`) VALUES
(1, 1, 0, 'User', '2015-05-26 03:47:01', '2015-05-26 03:47:01', 'About us', NULL, 'Article about us', NULL),
(2, 1, 0, 'User', '2015-05-26 03:47:01', '2015-05-26 03:47:01', 'Contact', NULL, 'Contact data', NULL),
(3, 1, 0, 'Administrator', '2015-05-26 03:47:01', '2015-05-26 04:09:47', 'Sample article', 'Description', '<p>Aliquam nec nisi nisl. Nulla porttitor purus in diam porttitor, at molestie nunc cursus. Ut felis libero, egestas sed malesuada non, posuere non elit. Nullam dignissim lacinia placerat. Etiam malesuada purus non diam bibendum, nec bibendum mauris elementum. In sed ante tincidunt sapien luctus sodales ut sed lorem. Sed eu blandit nisi. Praesent posuere maximus congue. Nam at justo efficitur, mattis diam quis, elementum leo. Maecenas ex mauris, malesuada sit amet rutrum quis, ullamcorper ac nulla. Integer rhoncus risus vitae arcu semper, at ultricies eros imperdiet. Nam lobortis nunc quis rutrum vestibulum.<br/><br/>\n\nDonec at nisl ac erat scelerisque commodo vel eget mi. Praesent vestibulum ex at erat viverra molestie. Aenean efficitur maximus aliquet. Praesent felis velit, faucibus ac elit quis, euismod mattis augue. Mauris vulputate diam dolor, eu tincidunt purus tempus in. In consequat faucibus mollis. Phasellus egestas maximus erat, porttitor suscipit ex pharetra ut. Ut ornare dapibus nulla, sit amet vehicula neque efficitur nec. Integer magna ex, ultrices suscipit ex sit amet, scelerisque placerat lorem. Aenean quis mauris nec ligula lobortis pharetra. Aenean ullamcorper sit amet quam eget maximus. Nullam id quam ligula. Quisque ex lectus, efficitur quis volutpat id, elementum id nulla. Donec eu ipsum eros. Mauris interdum diam nec augue vehicula, quis consequat libero venenatis.\n<br/><br/>\nVivamus quis dui rhoncus augue tristique ultricies vitae non ante. Aliquam aliquam nibh eu ipsum porta, a fringilla magna ultrices. Duis enim tortor, pharetra a vehicula sit amet, dictum non metus. Cras nisi diam, ullamcorper vitae elit et, varius iaculis magna. Vestibulum non viverra neque. Ut feugiat sagittis accumsan. Nulla auctor auctor volutpat. Aenean justo turpis, dignissim nec elementum a, iaculis eget diam. Suspendisse in magna vitae est pharetra interdum eget et mauris. Phasellus quis rutrum erat. Nulla quis fringilla quam. In imperdiet lacus sapien, auctor pulvinar mauris ornare sit amet. In porta sit amet lacus et volutpat. Vivamus varius, lacus vel congue dictum, lectus felis placerat ipsum, in tincidunt massa lacus quis est.</p>', NULL),
(4, 0, 0, 'User', '2015-05-26 03:47:01', '2015-05-26 03:47:01', 'Sample deleted artcile', 'Description', '<p>In orci orci, rutrum porta ipsum fermentum, malesuada facilisis orci. In lacus urna, suscipit ac egestas quis, rhoncus id metus. Maecenas eros purus, congue in congue id, varius sit amet massa. Ut egestas auctor luctus. Maecenas fermentum semper mauris et euismod. Morbi tempor pharetra fermentum. Vestibulum quis maximus nisl, vestibulum luctus purus. Proin sed nibh libero. Duis id felis id neque imperdiet malesuada. Vestibulum cursus arcu non pellentesque congue.<br/><br/>\n\nAliquam erat volutpat. Nam eros nisi, tempor quis mi a, tincidunt lobortis ipsum. Vivamus mollis nisi vitae ultricies dictum. Cras eu fermentum turpis. Sed ac pulvinar quam. In a metus dignissim, consectetur erat id, pellentesque risus. Aliquam lacinia sollicitudin magna, vitae accumsan massa semper vel. Nunc sodales orci et pharetra placerat. Aliquam non sapien nibh. Aenean imperdiet consectetur dolor eu vulputate. Nulla facilisi.<br/><br/>\n\nProin elementum elit ut lorem iaculis sollicitudin. Pellentesque eget fringilla risus, non feugiat quam. Pellentesque egestas turpis et faucibus posuere. Sed dignissim sagittis ante sit amet convallis. Aliquam sagittis viverra enim, vel malesuada sem venenatis non. Nam ac sodales purus. Integer dolor turpis, cursus lacinia viverra ac, fringilla sit amet diam. Maecenas scelerisque, lectus sit amet consequat consequat, odio nibh varius tortor, et ornare dui massa eget lorem. Fusce elementum a magna nec iaculis. Duis vitae faucibus erat.<br/><br/></p>', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `test_logs`
--

CREATE TABLE IF NOT EXISTS `{{db_logs}}` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_time` int(11) NOT NULL,
  `log_message` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `test_news`
--

CREATE TABLE IF NOT EXISTS `{{db_news}}` (
  `news_id` int(10) NOT NULL AUTO_INCREMENT,
  `news_removed` tinyint(1) NOT NULL,
  `news_user` varchar(32) NOT NULL,
  `news_ctime` int(11) NOT NULL,
  `news_mtime` int(11) NOT NULL,
  `news_meettime` int(11) DEFAULT NULL,
  `news_title` varchar(128) NOT NULL,
  `news_body` text NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `test_news`
--

INSERT INTO `{{db_news}}` (`news_id`, `news_removed`, `news_user`, `news_ctime`, `news_mtime`, `news_meettime`, `news_title`, `news_body`) VALUES
(1, 0, 'User', 1432604821, 1432897537, NULL, 'Title 1', 'In orci orci, rutrum porta ipsum fermentum, malesuada facilisis orci. In lacus urna, suscipit ac egestas quis, rhoncus id metus. Maecenas eros purus, congue in congue id, varius sit amet massa. Ut egestas auctor luctus. Maecenas fermentum semper mauris et euismod. Morbi tempor pharetra fermentum. Vestibulum quis maximus nisl, vestibulum luctus purus. Proin sed nibh libero. Duis id felis id neque imperdiet malesuada. Vestibulum cursus arcu non pellentesque congue.'),
(2, 0, 'User', 1432604821, 1432604821, NULL, 'Title 2', 'Aliquam erat volutpat. Nam eros nisi, tempor quis mi a, tincidunt lobortis ipsum. Vivamus mollis nisi vitae ultricies dictum. Cras eu fermentum turpis. Sed ac pulvinar quam. In a metus dignissim, consectetur erat id, pellentesque risus. Aliquam lacinia sollicitudin magna, vitae accumsan massa semper vel. Nunc sodales orci et pharetra placerat. Aliquam non sapien nibh. Aenean imperdiet consectetur dolor eu vulputate. Nulla facilisi.'),
(3, 1, 'User', 1432604821, 1432604821, NULL, 'Title 3', 'Aliquam nec nisi nisl. Nulla porttitor purus in diam porttitor, at molestie nunc cursus. Ut felis libero, egestas sed malesuada non, posuere non elit. Nullam dignissim lacinia placerat. Etiam malesuada purus non diam bibendum, nec bibendum mauris elementum. In sed ante tincidunt sapien luctus sodales ut sed lorem. Sed eu blandit nisi. Praesent posuere maximus congue. Nam at justo efficitur, mattis diam quis, elementum leo. Maecenas ex mauris, malesuada sit amet rutrum quis, ullamcorper ac nulla. Integer rhoncus risus vitae arcu semper, at ultricies eros imperdiet. Nam lobortis nunc quis rutrum vestibulum.');

-- --------------------------------------------------------

--
-- Table structure for table `test_pictures`
--

CREATE TABLE IF NOT EXISTS `{{db_pictures}}` (
  `picture_id` int(10) NOT NULL AUTO_INCREMENT,
  `picture_category` int(11) NOT NULL,
  `picture_user` varchar(32) NOT NULL,
  `picture_ctime` datetime NOT NULL,
  `picture_mtime` datetime NOT NULL,
  `picture_caption` text,
  `picture_filename` varchar(255) NOT NULL,
  PRIMARY KEY (`picture_id`),
  KEY `picture_category` (`picture_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `test_pictures`
--

INSERT INTO `{{db_pictures}}` (`picture_id`, `picture_category`, `picture_user`, `picture_ctime`, `picture_mtime`, `picture_caption`, `picture_filename`) VALUES
(1, 2, 'Administrator', '2015-05-26 04:08:00', '2015-05-26 04:08:53', 'Description 1', '1432606080_b5f6c50df710db44dcd3ac3a037f30c4ba6527a1.jpg'),
(2, 2, 'Administrator', '2015-05-26 04:08:08', '2015-05-26 04:08:08', '', '1432606088_66e45b6baadec413f46199bd5a9d6f73ba831307.jpg'),
(3, 2, 'Administrator', '2015-05-26 04:08:22', '2015-05-26 04:09:02', 'Description 2', '1432606102_2677a0ff43c81708b953fc234debad90b2e68dc5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `test_picture_categories`
--

CREATE TABLE IF NOT EXISTS `{{db_albums}}` (
  `picture_categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `picture_categories_public` tinyint(1) NOT NULL,
  `picture_categories_user` varchar(32) NOT NULL,
  `picture_categories_ctime` datetime NOT NULL,
  `picture_categories_mtime` datetime NOT NULL,
  `picture_categories_title` varchar(128) NOT NULL,
  `picture_categories_descr` text,
  `picture_categories_picture_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`picture_categories_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `test_picture_categories`
--

INSERT INTO `{{db_albums}}` (`picture_categories_id`, `picture_categories_public`, `picture_categories_user`, `picture_categories_ctime`, `picture_categories_mtime`, `picture_categories_title`, `picture_categories_descr`, `picture_categories_picture_id`) VALUES
(1, 0, 'User', '2015-05-26 03:47:01', '2015-05-26 03:47:01', 'Recycle bin', 'Contains deleted pictures', NULL),
(2, 1, 'Administrator', '2015-05-26 04:07:17', '2015-05-26 00:00:00', 'Sample gallery', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `test_sessions`
--

CREATE TABLE IF NOT EXISTS `{{db_sessions}}` (
  `session_id` varchar(40) NOT NULL,
  `session_user` int(8) NOT NULL DEFAULT '0',
  `session_ip` varchar(15) NOT NULL DEFAULT '',
  `session_browser` varchar(128) NOT NULL DEFAULT '',
  `session_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `test_users`
--

CREATE TABLE IF NOT EXISTS `{{db_users}}` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_login` varchar(32) NOT NULL,
  `user_name` varchar(40) NOT NULL,
  `user_password` varchar(40) NOT NULL,
  `user_lastvisit` int(8) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_login` (`user_login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `test_users`
--

INSERT INTO `{{db_users}}` (`user_id`, `user_login`, `user_name`, `user_password`, `user_lastvisit`) VALUES
(1, 'admin', 'Administrator', '{{default_pass}}', 1432897873),
(2, 'user', 'User', '{{default_pass}}', 1138562170);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
