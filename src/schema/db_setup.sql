/*
This should be the basic commands to run to setup the demo site database.
NOTE: It assumes a fresh instance.
*/
-- DROP DATABASE `demo-db`;
--
CREATE DATABASE `demo-db`;
--
USE `demo-db`;
--
-- DROP TABLE `page_index`;
--
CREATE TABLE `page_index` (
   `pageid` INT NOT NULL AUTO_INCREMENT COMMENT 'id used across tables',
   `page_title` VARCHAR(255) NOT NULL COMMENT 'string use in the meta title field',
   `page_header` VARCHAR(255) NOT NULL COMMENT 'string the user sees in the header',
   `main_article` text NOT NULL COMMENT 'parsable text rendered and displayed at the top of the page',
   PRIMARY KEY (`pageid`)
);
--
INSERT INTO `page_index`
(`page_title`, `page_header`, `main_article`)
VALUES
('About Me - Website Demo', 'About Me', "## This is the About Me Article!

I write code and don't have _any_ coding examples. I hope this will serve both as my personal website and an example of how I write code!"),
('Dev - Website Demo', 'The Dev Environment', "## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!");
--
-- DROP TABLE `page_nav`;
--
CREATE TABLE `page_nav` (
   `navid` INT NOT NULL AUTO_INCREMENT,
   `type` ENUM('ARTICLE_PAGE', 'CUSTOM') COMMENT 'Page will load page_index data; Custom will assume a custom link',
   `slug` VARCHAR(255) NOT NULL COMMENT 'Either the string representing the page_index.pageid or a external full url',
   `nav_string` VARCHAR(255) NOT NULL COMMENT 'string show in the sidebar nav',
   `pageid` INT DEFAULT NULL COMMENT 'Should match a value the in page_index table. ',
   `orderby` INT NOT NULL COMMENT 'Order entries will appear on the page',
   PRIMARY KEY(`navid`),
   UNIQUE KEY(`slug`),
   UNIQUE KEY(`nav_string`)
);
--
INSERT INTO `page_nav`
(`type`, `slug`, `nav_string`, `pageid`, `orderby`)
VALUES
('ARTICLE_PAGE', 'about-me', 'About Me', 1, 1),
('CUSTOM', 'https://github.com/cdcline/demo-website', 'Resume', NULL, 3),
('ARTICLE_PAGE', 'dev', 'Dev', 2, 2);
--