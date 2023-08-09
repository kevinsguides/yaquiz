-- table to keep track of when users start a quiz and the time limit if any
CREATE TABLE IF NOT EXISTS `#__com_yaquiz_user_quiz_times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL DEFAULT 0,
  `start_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `limit_time` datetime DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;