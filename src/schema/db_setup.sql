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
  `pageid` INT NOT NULL AUTO_INCREMENT COMMENT 'id used across tables',
  `slug` VARCHAR(255) NOT NULL COMMENT 'string we use in the url',
  `nav_string` VARCHAR(255) NOT NULL COMMENT 'string show in the sidebar nav',
  `page_title` VARCHAR(255) NOT NULL COMMENT 'string use in the meta title field',
  `page_header` VARCHAR(255) NOT NULL COMMENT 'string the user sees in the header',
  `main_article` text NOT NULL COMMENT 'parsable text rendered and displayed at the top of the page',
  PRIMARY KEY (`pageid`),
  UNIQUE KEY(`slug`),
  UNIQUE KEY(`nav_string`)
);
--
INSERT INTO `page_index`
(`slug`, `nav_string`, `page_title`, `page_header`, `main_article`)
VALUES
('about-me', 'About Me', 'About Me - Website Demo', 'About Me', "## This is the About Me Article!

I write code and don't have _any_ coding examples. I hope this will serve both as my personal website and an example of how I write code!"),
('dev', 'Dev', 'Dev - Website Demo', 'The Dev Environment', "## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!");