ALTER TABLE `#__simplequiz_questions` ADD `catid` INT(11) NOT NULL DEFAULT '0' AFTER `params`;

ALTER TABLE `#__simplequiz_quizzes` ADD `catid` INT(11) NOT NULL DEFAULT '0' AFTER `hits`;



