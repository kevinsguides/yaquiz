ALTER TABLE `#__com_yaquiz_questions` ADD `catid` INT(11) NOT NULL DEFAULT '0' AFTER `params`;

ALTER TABLE `#__com_yaquiz_quizzes` ADD `catid` INT(11) NOT NULL DEFAULT '0' AFTER `hits`;



