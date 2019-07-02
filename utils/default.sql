-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: vs_biztretto_trillala
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1

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
-- Table structure for table `actionlogger`
--

DROP TABLE IF EXISTS `actionlogger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionlogger` (
  `actionlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_id` int(11) NOT NULL,
  `office_id` int(11) NOT NULL,
  `office_nametag` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int(1) NOT NULL,
  `action_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `request` mediumblob,
  `object` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`actionlog_id`)
) ENGINE=InnoDB AUTO_INCREMENT=778 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actionlogger`
--

LOCK TABLES `actionlogger` WRITE;
/*!40000 ALTER TABLE `actionlogger` DISABLE KEYS */;
INSERT INTO `actionlogger` VALUES (771,4,1,'trillala','178.48.120.170',0,'user.login','valzol@gmail.com','{\"user\":\"valzol@gmail.com\",\"pass\":\"ab\",\"doLogin\":\"Sign in\"}','User.4','2013-10-03 11:12:51'),(772,4,1,'trillala','178.48.120.170',0,'user.logout','valzol@gmail.com','[]','User.4','2013-10-03 11:13:58'),(773,4,1,'trillala','178.48.120.170',0,'user.login','valzol@gmail.com','{\"user\":\"valzol@gmail.com\",\"pass\":\"ab\",\"doLogin\":\"Sign in\"}','User.4','2013-10-03 11:14:06'),(774,4,1,'trillala','178.48.120.170',0,'user.login','valzol@gmail.com','{\"user\":\"valzol@gmail.com\",\"pass\":\"ab\",\"doLogin\":\"Sign in\"}','User.4','2013-10-03 11:19:02'),(775,4,1,'trillala','178.48.120.170',0,'mediafiles.removefilebyids','ids:156','{\"\\/process\\/mymedia\\/handelmediafiles\\/\":\"\",\"action\":\"delete\",\"form\":[{\"name\":\"diskArea_id\",\"value\":\"3\"},{\"name\":\"diskArea_name\",\"value\":\"default\"},{\"name\":\"office_id\",\"value\":\"1\"},{\"name\":\"office_nametag\",\"value\":\"trillala\"},{\"name\":\"owner\",\"value\":\"4\"}],\"files\":[{\"mediaelement\":\"image-07_1.jpg\",\"id\":\"156\",\"textid\":\"imag\",\"mediatype\":\"local\",\"type\":\"image\",\"groupname\":\"\",\"did\":\"3\"}]}','','2013-10-03 11:32:44'),(776,4,1,'trillala','178.48.120.170',0,'mediafiles.removefilebyids','ids:156','{\"\\/process\\/mymedia\\/handelmediafiles\\/\":\"\",\"action\":\"delete\",\"form\":[{\"name\":\"diskArea_id\",\"value\":\"3\"},{\"name\":\"diskArea_name\",\"value\":\"default\"},{\"name\":\"office_id\",\"value\":\"1\"},{\"name\":\"office_nametag\",\"value\":\"trillala\"},{\"name\":\"owner\",\"value\":\"4\"}],\"files\":[{\"mediaelement\":\"image-07_1.jpg\",\"id\":\"156\",\"textid\":\"imag\",\"mediatype\":\"local\",\"type\":\"image\",\"groupname\":\"\",\"did\":\"3\"}]}','','2013-10-03 11:32:46'),(777,4,1,'trillala','178.48.120.170',0,'user.logout','valzol@gmail.com','[]','User.4','2013-10-03 12:10:54');
/*!40000 ALTER TABLE `actionlogger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipcheck`
--

DROP TABLE IF EXISTS `ipcheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipcheck` (
  `id` int(11) NOT NULL,
  `loggedip` tinytext NOT NULL,
  `failedattempts` int(11) NOT NULL,
  `user_email` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipcheck`
--

LOCK TABLES `ipcheck` WRITE;
/*!40000 ALTER TABLE `ipcheck` DISABLE KEYS */;
/*!40000 ALTER TABLE `ipcheck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_diskarea`
--

DROP TABLE IF EXISTS `media_diskarea`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_diskarea` (
  `diskArea_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `name` varchar(15) NOT NULL,
  `sortname` varchar(15) NOT NULL,
  `owner` bigint(20) NOT NULL,
  `createdDate` datetime NOT NULL,
  `size` int(20) NOT NULL,
  PRIMARY KEY (`diskArea_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_diskarea`
--

LOCK TABLES `media_diskarea` WRITE;
/*!40000 ALTER TABLE `media_diskarea` DISABLE KEYS */;
INSERT INTO `media_diskarea` VALUES (3,1,'trillala','default','default',3,'2013-10-03 00:00:00',200),(4,2,'trillala','corporate','corporate',3,'2013-10-03 00:00:00',200);
/*!40000 ALTER TABLE `media_diskarea` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_mediabox`
--

DROP TABLE IF EXISTS `media_mediabox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_mediabox` (
  `mediabox_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `diskArea_id` bigint(20) NOT NULL,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `name` varchar(15) NOT NULL,
  `owner` bigint(20) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`mediabox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_mediabox`
--

LOCK TABLES `media_mediabox` WRITE;
/*!40000 ALTER TABLE `media_mediabox` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_mediabox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_mediaboxfiles`
--

DROP TABLE IF EXISTS `media_mediaboxfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_mediaboxfiles` (
  `mediaboxFiles_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `diskArea_id` bigint(20) NOT NULL,
  `mediabox_id` bigint(20) NOT NULL,
  `mymedia_id` bigint(20) NOT NULL,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `owner` bigint(20) NOT NULL,
  `createdDate` datetime NOT NULL,
  PRIMARY KEY (`mediaboxFiles_id`),
  KEY `fk_mediaboxFiles` (`mediabox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_mediaboxfiles`
--

LOCK TABLES `media_mediaboxfiles` WRITE;
/*!40000 ALTER TABLE `media_mediaboxfiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_mediaboxfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_mymedia`
--

DROP TABLE IF EXISTS `media_mymedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media_mymedia` (
  `mymedia_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `diskArea_id` bigint(20) NOT NULL,
  `mediabox_id` bigint(20) NOT NULL DEFAULT '0',
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  `owner` bigint(20) NOT NULL,
  `createdDate` datetime NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `folder` varchar(15) NOT NULL DEFAULT '',
  `mediatype` varchar(10) NOT NULL DEFAULT '',
  `mediaurl` varchar(250) NOT NULL DEFAULT '',
  `thumbnail_url` varchar(250) NOT NULL DEFAULT '',
  `uploaded` date NOT NULL,
  `uploaded_ts` int(11) NOT NULL,
  `duration` varchar(12) DEFAULT '',
  `filesize` varchar(12) DEFAULT '',
  `size` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mymedia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_mymedia`
--

LOCK TABLES `media_mymedia` WRITE;
/*!40000 ALTER TABLE `media_mymedia` DISABLE KEYS */;
INSERT INTO `media_mymedia` VALUES (157,3,0,1,'trillala',4,'2013-10-03 12:31:52','image-07.jpg','image','','local','default/image-07.jpg','default/thumbnail/image-07.jpg','2013-10-03',1380799908,'','475.55 KB','');
/*!40000 ALTER TABLE `media_mymedia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office`
--

DROP TABLE IF EXISTS `office`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office` (
  `office_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `office_type` varchar(10) NOT NULL DEFAULT 'office',
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  `office_name_hu` varchar(100) NOT NULL DEFAULT '',
  `office_email` varchar(50) NOT NULL DEFAULT '',
  `office_tel` varchar(20) NOT NULL DEFAULT '',
  `office_postcode` varchar(6) NOT NULL DEFAULT '',
  `office_city` varchar(50) NOT NULL DEFAULT '',
  `office_street` varchar(50) NOT NULL DEFAULT '',
  `office_name_en` varchar(100) NOT NULL DEFAULT '',
  `contact_name` varchar(50) NOT NULL DEFAULT '',
  `contact_title` varchar(50) NOT NULL DEFAULT '',
  `updatedDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fileSystemQuota` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`office_id`),
  UNIQUE KEY `office_nametag` (`office_nametag`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office`
--

LOCK TABLES `office` WRITE;
/*!40000 ALTER TABLE `office` DISABLE KEYS */;
INSERT INTO `office` VALUES (2,'office','trillala','Trillala Kft.','','','','','','','','','0000-00-00 00:00:00','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `office` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slide_slides`
--

DROP TABLE IF EXISTS `slide_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slide_slides` (
  `slides_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slideshow_id` bigint(20) NOT NULL,
  `office_id` bigint(20) DEFAULT NULL,
  `office_nametag` varchar(20) DEFAULT NULL,
  `owner` bigint(20) DEFAULT NULL,
  `id` double NOT NULL,
  `type` varchar(10) DEFAULT '',
  `templateType` varchar(10) DEFAULT 'normal',
  `slideItems` longtext,
  `html` longtext,
  `htmlForSlideshow` longtext,
  `tag` varchar(45) DEFAULT '',
  `badge` int(11) DEFAULT NULL,
  `slideLevel` int(11) NOT NULL DEFAULT '1',
  `templateOption` longtext,
  `answare` text,
  `description` longtext,
  `parent_id` bigint(20) DEFAULT NULL,
  `lft` bigint(20) DEFAULT NULL,
  `rgt` bigint(20) DEFAULT NULL,
  `depth` bigint(20) DEFAULT NULL,
  `transform` varchar(50) DEFAULT NULL,
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `missingContent` varchar(200) DEFAULT '',
  `background` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`slides_id`),
  UNIQUE KEY `id` (`id`),
  FULLTEXT KEY `html_index` (`missingContent`)
) ENGINE=MyISAM AUTO_INCREMENT=435 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slide_slides`
--

LOCK TABLES `slide_slides` WRITE;
/*!40000 ALTER TABLE `slide_slides` DISABLE KEYS */;
/*!40000 ALTER TABLE `slide_slides` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `slide_slideshow`
--

DROP TABLE IF EXISTS `slide_slideshow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `slide_slideshow` (
  `slideshow_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` varchar(250) DEFAULT '',
  `diskArea_id` bigint(20) DEFAULT NULL,
  `mediabox_id` int(11) NOT NULL DEFAULT '0',
  `attachment` longtext,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `cover` text,
  `owner` bigint(20) NOT NULL,
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isEnabled` int(1) NOT NULL DEFAULT '1',
  `templateSlideCount` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`slideshow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `slide_slideshow`
--

LOCK TABLES `slide_slideshow` WRITE;
/*!40000 ALTER TABLE `slide_slideshow` DISABLE KEYS */;
/*!40000 ALTER TABLE `slide_slideshow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `starrating`
--

DROP TABLE IF EXISTS `starrating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `starrating` (
  `office_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rate` int(1) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`office_id`,`u_id`,`id`),
  KEY `rate` (`rate`,`ts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `starrating`
--

LOCK TABLES `starrating` WRITE;
/*!40000 ALTER TABLE `starrating` DISABLE KEYS */;
/*!40000 ALTER TABLE `starrating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stat_login`
--

DROP TABLE IF EXISTS `stat_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stat_login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id` bigint(20) NOT NULL,
  `office_id` bigint(20) NOT NULL DEFAULT '0',
  `ctime` varchar(220) NOT NULL,
  `loginDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastActivity` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `logoutDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `u_id` (`office_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stat_login`
--

LOCK TABLES `stat_login` WRITE;
/*!40000 ALTER TABLE `stat_login` DISABLE KEYS */;
INSERT INTO `stat_login` VALUES (54,4,1,'1380798771','2013-10-03 12:12:51','0000-00-00 00:00:00','2013-10-03 12:13:58'),(55,4,1,'1380798846','2013-10-03 12:14:06','0000-00-00 00:00:00','0000-00-00 00:00:00'),(56,4,1,'1380799142','2013-10-03 12:19:02','0000-00-00 00:00:00','2013-10-03 13:10:54');
/*!40000 ALTER TABLE `stat_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_results`
--

DROP TABLE IF EXISTS `training_results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_results` (
  `training_id` bigint(20) NOT NULL DEFAULT '0',
  `slideshow_id` bigint(20) NOT NULL DEFAULT '0',
  `slide_id` bigint(20) NOT NULL DEFAULT '0',
  `u_id` bigint(20) NOT NULL DEFAULT '0',
  `result` bigint(20) NOT NULL DEFAULT '0',
  `testtype` varchar(50) DEFAULT '',
  `token` varchar(50) NOT NULL DEFAULT '',
  `office_id` bigint(20) NOT NULL DEFAULT '0',
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `answer` text,
  UNIQUE KEY `training_id_slideshow_id_u_id_slide_id_token` (`training_id`,`slideshow_id`,`u_id`,`slide_id`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_results`
--

LOCK TABLES `training_results` WRITE;
/*!40000 ALTER TABLE `training_results` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_slideshow`
--

DROP TABLE IF EXISTS `training_slideshow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_slideshow` (
  `ts_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `training_id` bigint(20) NOT NULL DEFAULT '0',
  `slideshow_id` bigint(20) NOT NULL DEFAULT '0',
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `timeout1` time DEFAULT NULL,
  `timeout2` time DEFAULT NULL,
  `wtimeout1` time DEFAULT NULL,
  `wtimeout2` time DEFAULT NULL,
  `testlevel` int(1) DEFAULT '1',
  `type` int(1) DEFAULT '0',
  `testtype` varchar(50) DEFAULT '2 pole',
  `repetable` int(1) DEFAULT '0',
  `credit` int(10) DEFAULT '0',
  `traininggroups` text CHARACTER SET utf8 COLLATE utf8_hungarian_ci,
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `lft` bigint(20) NOT NULL DEFAULT '2',
  `rgt` bigint(20) NOT NULL DEFAULT '3',
  `depth` bigint(20) NOT NULL DEFAULT '1',
  `createdDate` datetime DEFAULT '0000-00-00 00:00:00',
  `updatedDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `office_id` bigint(20) NOT NULL DEFAULT '0',
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ts_id`),
  UNIQUE KEY `training_id_slideshow_id` (`training_id`,`slideshow_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_slideshow`
--

LOCK TABLES `training_slideshow` WRITE;
/*!40000 ALTER TABLE `training_slideshow` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_slideshow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_slideshow_score`
--

DROP TABLE IF EXISTS `training_slideshow_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_slideshow_score` (
  `tss_id` int(11) NOT NULL AUTO_INCREMENT,
  `training_id` int(11) NOT NULL,
  `slideshow_id` int(11) NOT NULL,
  `type` int(1) DEFAULT NULL,
  `testtype` int(1) DEFAULT NULL,
  `office_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `token_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `max_credit` int(11) NOT NULL,
  `credit` int(11) NOT NULL,
  `max_point` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `success` int(1) NOT NULL,
  `rate` int(1) NOT NULL,
  `visited` int(1) NOT NULL DEFAULT '0',
  `archive` int(1) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tss_id`),
  KEY `visited` (`visited`),
  KEY `archive` (`archive`),
  KEY `rate` (`rate`),
  KEY `success` (`success`),
  KEY `created` (`created`),
  KEY `u_id` (`u_id`),
  KEY `token_id` (`token_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_slideshow_score`
--

LOCK TABLES `training_slideshow_score` WRITE;
/*!40000 ALTER TABLE `training_slideshow_score` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_slideshow_score` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_slideshow_visited`
--

DROP TABLE IF EXISTS `training_slideshow_visited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_slideshow_visited` (
  `training_id` bigint(20) NOT NULL DEFAULT '0',
  `slideshow_id` bigint(20) NOT NULL DEFAULT '0',
  `u_id` bigint(20) NOT NULL DEFAULT '0',
  `visited` bigint(20) NOT NULL DEFAULT '0',
  `office_id` bigint(20) NOT NULL DEFAULT '0',
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  UNIQUE KEY `training_id_slideshow_id` (`training_id`,`slideshow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_slideshow_visited`
--

LOCK TABLES `training_slideshow_visited` WRITE;
/*!40000 ALTER TABLE `training_slideshow_visited` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_slideshow_visited` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_training`
--

DROP TABLE IF EXISTS `training_training`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_training` (
  `training_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `cover` mediumblob,
  `authors` text,
  `attachment` text,
  `owner` bigint(20) NOT NULL,
  `diskArea_id` bigint(20) NOT NULL,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL DEFAULT '',
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activeState` varchar(10) NOT NULL DEFAULT 'draft',
  `credit` int(10) DEFAULT '0',
  `startDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `traininggroups` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`training_id`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_training`
--

LOCK TABLES `training_training` WRITE;
/*!40000 ALTER TABLE `training_training` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_training` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_users`
--

DROP TABLE IF EXISTS `training_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_users` (
  `trusers_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `training_id` bigint(20) NOT NULL DEFAULT '0',
  `slideshow_id` bigint(20) NOT NULL DEFAULT '0',
  `u_id` bigint(20) NOT NULL DEFAULT '0',
  `maxCredit` int(20) DEFAULT '0',
  `credit` int(20) DEFAULT '0',
  `finished` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trusers_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_users`
--

LOCK TABLES `training_users` WRITE;
/*!40000 ALTER TABLE `training_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_traininggroup`
--

DROP TABLE IF EXISTS `user_traininggroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_traininggroup` (
  `traininggroup_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `name` varchar(15) NOT NULL DEFAULT '',
  `doname` varchar(15) NOT NULL DEFAULT '',
  `owner` bigint(20) NOT NULL,
  PRIMARY KEY (`traininggroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_traininggroup`
--

LOCK TABLES `user_traininggroup` WRITE;
/*!40000 ALTER TABLE `user_traininggroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_traininggroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_traininggroupusers`
--

DROP TABLE IF EXISTS `user_traininggroupusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_traininggroupusers` (
  `u_id` bigint(20) NOT NULL,
  `traininggroup_id` bigint(20) NOT NULL,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `owner` bigint(20) NOT NULL,
  PRIMARY KEY (`u_id`,`traininggroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_traininggroupusers`
--

LOCK TABLES `user_traininggroupusers` WRITE;
/*!40000 ALTER TABLE `user_traininggroupusers` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_traininggroupusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_u`
--

DROP TABLE IF EXISTS `user_u`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_u` (
  `u_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(7) DEFAULT '',
  `office_id` bigint(20) NOT NULL DEFAULT '0',
  `office_nametag` varchar(100) NOT NULL DEFAULT '',
  `elotag` varchar(5) DEFAULT '',
  `vezeteknev` varchar(50) NOT NULL DEFAULT '',
  `keresztnev` varchar(15) NOT NULL DEFAULT '',
  `full_name` varchar(100) NOT NULL DEFAULT '',
  `user_name` varchar(20) DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `userlevel` tinyint(4) NOT NULL DEFAULT '1',
  `parent_id` bigint(20) DEFAULT '0',
  `pwd` varchar(220) DEFAULT '',
  `office_name` varchar(100) NOT NULL DEFAULT '',
  `user_tel` varchar(20) DEFAULT '',
  `department` int(11) NOT NULL DEFAULT '0',
  `position` varchar(50) DEFAULT '',
  `birthDate` date DEFAULT '1900-01-01',
  `gender` varchar(10) DEFAULT '',
  `language` varchar(100) DEFAULT '',
  `schools` text,
  `skills` text,
  `pemail` varchar(100) DEFAULT NULL COMMENT 'private email',
  `profilePicture` longtext,
  `users_ip` varchar(200) DEFAULT '',
  `approved` int(1) NOT NULL DEFAULT '0',
  `activation_code` varchar(25) DEFAULT '',
  `activation_time` datetime DEFAULT '0000-00-00 00:00:00',
  `activatedBy` bigint(20) DEFAULT '0',
  `activeState` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `banned` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `ckey` varchar(220) DEFAULT '',
  `ctime` varchar(220) DEFAULT '',
  `depth` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lft` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rgt` bigint(20) unsigned NOT NULL DEFAULT '0',
  `loginattempt` int(11) unsigned DEFAULT '0',
  `createdDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cryptedText` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `cv` text,
  `deleted` int(1) unsigned zerofill NOT NULL DEFAULT '0',
  `isvisible` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`u_id`)
) ENGINE=InnoDB AUTO_INCREMENT=434 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_u`
--

LOCK TABLES `user_u` WRITE;
/*!40000 ALTER TABLE `user_u` DISABLE KEYS */;
INSERT INTO `user_u` VALUES (1,'office',1,'trillala','','Anonym','User','Anonym User','','anonym@a.com',1,0,'','','',0,'','1900-01-01','','',NULL,NULL,NULL,NULL,'',1,'','0000-00-00 00:00:00',0,1,0,'','',0,0,0,0,'0000-00-00 00:00:00','2013-10-03 11:07:58','',NULL,NULL,0,0),(2,'office',1,'trillala','','Site','Admin','Site Admin','','trillala@v.hu',9,0,'b142827f4b4085cc85b275268351b83c7cd3e9bfb7bc4b50e10ba6d001b8d1f29d172a62edc52db048150c10e21bb6e3665eb7ad78068cbbe95f310bfabc806e','','',0,'','1900-01-01','','',NULL,NULL,NULL,NULL,'',1,'','0000-00-00 00:00:00',0,1,0,'','',0,0,0,0,'0000-00-00 00:00:00','2013-10-03 11:06:02','',NULL,NULL,0,0),(3,'office',1,'trillala','','Várkonyi','István','Várkonyi István','','pp@varsoft.hu',7,0,'b142827f4b4085cc85b275268351b83c7cd3e9bfb7bc4b50e10ba6d001b8d1f29d172a62edc52db048150c10e21bb6e3665eb7ad78068cbbe95f310bfabc806e','','',0,'','1900-01-01','','',NULL,NULL,NULL,NULL,'',1,'','0000-00-00 00:00:00',0,1,0,'','',0,0,0,0,'0000-00-00 00:00:00','2013-10-03 11:13:41','',NULL,NULL,0,1),(4,'office',1,'trillala','','Valentyik','Zoltán','Valentyik Zoltán','','valzol@gmail.com',7,0,'b142827f4b4085cc85b275268351b83c7cd3e9bfb7bc4b50e10ba6d001b8d1f29d172a62edc52db048150c10e21bb6e3665eb7ad78068cbbe95f310bfabc806e','','',0,'','1900-01-01','','',NULL,NULL,NULL,NULL,'',1,'','0000-00-00 00:00:00',0,1,0,'xpr13k4A6Xb','1380799142',0,0,0,0,'0000-00-00 00:00:00','2013-10-03 11:19:02','',NULL,NULL,0,1);
/*!40000 ALTER TABLE `user_u` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_usergroup`
--

DROP TABLE IF EXISTS `user_usergroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_usergroup` (
  `usergroup_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `office_id` bigint(20) NOT NULL,
  `office_nametag` varchar(20) NOT NULL,
  `name` varchar(15) NOT NULL DEFAULT '',
  `doname` varchar(15) NOT NULL DEFAULT '',
  `owner` bigint(20) NOT NULL,
  PRIMARY KEY (`usergroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_usergroup`
--

LOCK TABLES `user_usergroup` WRITE;
/*!40000 ALTER TABLE `user_usergroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_usergroup` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-24 12:41:23
