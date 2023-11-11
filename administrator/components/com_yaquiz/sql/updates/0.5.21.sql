-- alter table #__com_yaquiz_questions and change feedback_right and feedback_wrong to mediumtext

ALTER TABLE `#__com_yaquiz_questions` CHANGE `feedback_right` `feedback_right` MEDIUMTEXT NULL DEFAULT NULL;
ALTER TABLE `#__com_yaquiz_questions` CHANGE `feedback_wrong` `feedback_wrong` MEDIUMTEXT NULL DEFAULT NULL;
