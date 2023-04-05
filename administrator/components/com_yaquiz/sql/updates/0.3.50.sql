-- make table to store individual user results

CREATE TABLE IF NOT EXISTS `#__com_yaquiz_results` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `points` int(11) NOT NULL DEFAULT '0',
  `total_points` int(11) NOT NULL DEFAULT '0',
  `submitted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `passed` tinyint(1) NOT NULL DEFAULT '0',
  `full_results` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;