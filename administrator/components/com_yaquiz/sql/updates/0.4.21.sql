-- add an alias col to com_yaquiz_quizzes

ALTER TABLE `#__com_yaquiz_quizzes` ADD `alias` varchar(255) AFTER `title`;
