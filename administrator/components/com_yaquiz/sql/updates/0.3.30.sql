CREATE TABLE IF NOT EXISTS `#__com_yaquiz_results_general` (
  `quiz_id` int(11) NOT NULL,
  `submissions` int(11) NOT NULL DEFAULT '0',
  `total_average_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_times_passed` int(11) NOT NULL DEFAULT '0',
  `misc_stats` text,
  PRIMARY KEY (`quiz_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;  