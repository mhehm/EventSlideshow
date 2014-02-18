--
-- Table structure for table `#__eventslideshow_events`
--

CREATE TABLE IF NOT EXISTS `#__eventslideshow_events` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `datetype` varchar(10) NOT NULL,
  `startmonth` int(2) NOT NULL,
  `startday` int(2) NOT NULL,
  `endmonth` int(2) NOT NULL,
  `endday` int(2) NOT NULL,
  `image` text NOT NULL,
  `clickurl` text,
  `clickarticle` int(11) DEFAULT NULL,
  `language` char(7) NOT NULL,
  `description` text NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_catid` (`catid`),
  KEY `idx_checked_out` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_checked_out_time` (`checked_out_time`),
  KEY `idx_language` (`language`),
  KEY `idx_title` (`title`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
