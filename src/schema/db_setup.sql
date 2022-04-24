/*
This should be the basic commands to run to setup the demo site database.
NOTE: It assumes a fresh instance.
*/

CREATE DATABASE `demo-db`;
--
USE `demo-db`;
--
-- DROP TABLE `page_index`;
--
CREATE TABLE `page_index` (
  `pageid` INT NOT NULL AUTO_INCREMENT COMMENT 'used as the key across tables',
  `slug` VARCHAR(255) NOT NULL COMMENT 'used in the url',
  `nav_string` VARCHAR(255) NOT NULL COMMENT 'used in the sidebar nav',
  `page_title` VARCHAR(255) NOT NULL COMMENT 'meta field in the head',
  `page_header` VARCHAR(255) NOT NULL COMMENT 'main title seen by the user on the page',
  PRIMARY KEY (`pageid`),
  UNIQUE KEY(`slug`),
  UNIQUE KEY(`nav_string`)
);
--
INSERT INTO `page_index`
(`slug`, `nav_string`, `page_title`, `page_header`)
VALUES
('about-me', 'About Me', 'About Me - Website Demo', 'About Me'),
('dev', 'Dev', 'Dev - Website Demo', 'The Dev Environment');
