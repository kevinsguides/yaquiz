CREATE TABLE IF NOT EXISTS `#__com_yaquiz_quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `params` text,
  `access` int(11) NOT NULL DEFAULT '0',
  `hits` int(11) NOT NULL DEFAULT '0',
  `submissions` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__com_yaquiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `details` mediumtext, 
  `answers` mediumtext,
  `correct` text,
  `catid` int(11) NOT NULL DEFAULT '0',
  `feedback_right` text NULL,
  `feedback_wrong` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- a table to link questions to quizzes
CREATE TABLE IF NOT EXISTS `#__com_yaquiz_question_quiz_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `numbering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;  


-- the general results table
CREATE TABLE IF NOT EXISTS `#__com_yaquiz_results_general` (
  `quiz_id` int(11) NOT NULL,
  `submissions` int(11) NOT NULL DEFAULT '0',
  `total_average_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `total_times_passed` int(11) NOT NULL DEFAULT '0',
  `misc_stats` text,
  PRIMARY KEY (`quiz_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;  


-- the individual results table
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
  `verifyhash` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- a table to link users with quizzes
-- for now this is really only used to keep track of number of attempts and limit them if set
CREATE TABLE IF NOT EXISTS `#__com_yaquiz_user_quiz_map` (
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempt_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`quiz_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;