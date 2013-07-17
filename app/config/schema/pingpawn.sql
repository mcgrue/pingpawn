-- MySQL dump 10.11
--
-- Host: localhost    Database: sexyman_bot
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `2007_2010_irc-log`
--

DROP TABLE IF EXISTS `2007_2010_irc-log`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `2007_2010_irc-log` (
  `id` bigint(20) NOT NULL auto_increment,
  `channel` varchar(255) NOT NULL default '',
  `speaker_nick` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `speaker_mask` varchar(255) NOT NULL default '',
  `speaker_login` varchar(255) NOT NULL default '',
  `time_said` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_said_long` bigint(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1187132 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `quote_id` int(11) NOT NULL,
  `user_id` int(11) default NULL,
  `is_spam` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `website` varchar(255) default NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `irc_logs`
--

DROP TABLE IF EXISTS `irc_logs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `irc_logs` (
  `id` int(11) NOT NULL auto_increment,
  `time` datetime NOT NULL,
  `room` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `usermask` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1599243 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `jerkcity`
--

DROP TABLE IF EXISTS `jerkcity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `jerkcity` (
  `id` int(11) NOT NULL auto_increment,
  `quote` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=298 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `login_tokens`
--

DROP TABLE IF EXISTS `login_tokens`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `login_tokens` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `token` char(32) NOT NULL,
  `duration` varchar(32) NOT NULL,
  `used` tinyint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7480 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prfs`
--

DROP TABLE IF EXISTS `prfs`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prfs` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) default NULL,
  `url_key` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `url_key` (`url_key`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `prfs_quotes`
--

DROP TABLE IF EXISTS `prfs_quotes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `prfs_quotes` (
  `prf_id` int(11) NOT NULL default '0',
  `quote_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`quote_id`,`prf_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `quotes` (
  `id` bigint(20) NOT NULL auto_increment,
  `quote` text character set latin1,
  `original_quote` text character set latin1,
  `active` tinyint(4) NOT NULL default '1',
  `url_key` varchar(255) character set latin1 default NULL,
  `title` varchar(255) character set latin1 default NULL,
  `time_added` datetime default NULL,
  `user_id` int(11) default NULL,
  `prf_id` int(11) default NULL,
  `tally` int(11) NOT NULL,
  `is_formatted` tinyint(4) default NULL,
  `last_edited_by` int(11) default NULL,
  `is_public` tinyint(11) NOT NULL default '1',
  `total_votes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url_key` (`url_key`),
  KEY `user_id` (`user_id`),
  KEY `prf_id` (`prf_id`),
  KEY `tally` (`tally`)
) ENGINE=MyISAM AUTO_INCREMENT=11073 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `quotes_permalinks`
--

DROP TABLE IF EXISTS `quotes_permalinks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `quotes_permalinks` (
  `quote_id` int(11) NOT NULL,
  `pretty_url` varchar(255) NOT NULL,
  `is_current` tinyint(1) NOT NULL default '0',
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `pretty_url` (`pretty_url`),
  KEY `quote_id` (`quote_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `quotes_tags`
--

DROP TABLE IF EXISTS `quotes_tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `quotes_tags` (
  `tag_id` int(11) NOT NULL default '0',
  `quote_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`tag_id`,`quote_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL auto_increment,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `real_name` varchar(255) default NULL,
  `twitter_name` varchar(255) default NULL,
  `description` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `profile_image_url` varchar(255) default NULL,
  `is_admin` tinyint(4) default NULL,
  `display_name` varchar(16) default NULL,
  `first_joined` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `display_name` (`display_name`)
) ENGINE=MyISAM AUTO_INCREMENT=476147104 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `votes` (
  `quote_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  `time_voted` datetime default NULL,
  `vote` tinyint(4) default NULL,
  PRIMARY KEY  (`quote_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-17 10:07:31
