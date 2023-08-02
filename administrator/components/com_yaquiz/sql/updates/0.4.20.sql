--add a column 'verifyhash' to com_yaquiz_results
ALTER TABLE `#__com_yaquiz_results` ADD `verifyhash` varchar(255) DEFAULT NULL;