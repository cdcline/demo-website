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
   `type` VARCHAR(255) COMMENT 'determines what page logic will run.', -- should probably be an enum but we'd like to keep the db modifications low
   `page_title` VARCHAR(255) NOT NULL COMMENT 'string use in the meta title field',
   `page_header` VARCHAR(255) NOT NULL COMMENT 'string the user sees in the header',
   `main_article` TEXT NOT NULL COMMENT 'parsable text rendered and displayed at the top of the page',
   PRIMARY KEY (`pageid`)
);
--
INSERT INTO `page_index`
(`page_title`, `type`, `page_header`, `main_article`)
VALUES
('About Me - Website Demo', 'about-me', 'About Me', "## This is the About Me Article!

I write code and don't have _any_ coding examples. I hope this will serve both as my personal website and an example of how I write code!"),
('Dev - Website Demo', 'dev', 'The Dev Environment', "## This is the Dev Article!

I need a space that's pretty constant and one that's _kinda_ scratch paper. This one's the scratch paper!"),
('Test 3 - Website Demo', 'default', 'Test Page 3', '## This is **Test Page 3**

Egestas sed tempus urna et pharetra pharetra massa massa ultricies. Neque sodales ut etiam sit amet nisl. Dictum sit amet justo donec enim diam vulputate. Morbi tincidunt augue interdum velit euismod in pellentesque massa placerat. Vulputate enim nulla aliquet porttitor. Aenean et tortor at risus viverra adipiscing. Pharetra sit amet aliquam id diam. Platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim. Adipiscing at in tellus integer feugiat. Nulla facilisi cras fermentum odio eu feugiat pretium nibh. Pharetra massa massa ultricies mi quis hendrerit dolor. Purus ut faucibus pulvinar elementum integer enim neque volutpat ac.'),
('Test 4 - Website Demo', 'default', 'Test Page 4', '## This is _Test Page 4_

Vulputate dignissim suspendisse in est. Amet risus nullam eget felis eget nunc lobortis. Pellentesque diam volutpat commodo sed egestas. Id leo in vitae turpis massa sed elementum tempus egestas. Nam libero justo laoreet sit amet cursus sit. Consectetur purus ut faucibus pulvinar. Laoreet suspendisse interdum consectetur libero id faucibus nisl. Laoreet non curabitur gravida arcu ac tortor dignissim convallis aenean. Viverra mauris in aliquam sem fringilla. Nibh nisl condimentum id venenatis a condimentum vitae sapien pellentesque. Ut placerat orci nulla pellentesque. Bibendum at varius vel pharetra vel turpis nunc eget lorem. Euismod quis viverra nibh cras pulvinar mattis nunc sed.');
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
-- DROP TABLE `mini_article`;
--
CREATE TABLE `mini_article` (
   `mini_articleid` INT NOT NULL AUTO_INCREMENT,
   `pageid` INT NOT NULL COMMENT 'The page the mini articles will display on', -- This & the PK really belongs in it's own table: https://github.com/cdcline/demo-website/issues/46
   `title` VARCHAR(255) NOT NULL COMMENT 'Text shown at the top of the mini article',
   `text` TEXT NOT NULL COMMENT 'Parsable text that will be displayed in the mini article',
   `start_date` INT DEFAULT NULL COMMENT 'Optional: Start date associated with the article',
   `end_date` INT DEFAULT NULL COMMENT 'Optional: End date associated with article',
   PRIMARY KEY(`mini_articleid`),
   FOREIGN KEY (`pageid`) REFERENCES `page_index`(`pageid`) ON DELETE CASCADE, -- Mini Articles must be attached to a Page. If that Page is deleted, so are the Mini Articles
   UNIQUE KEY(`title`),
   KEY(`start_date`)
);
--
INSERT INTO `mini_article`
(`pageid`, `title`, `start_date`, `end_date`, `text`)
VALUES
(2, 'Mini Article 1', 1391555735, 1645658135, "##### This is a _mini_ article!

It's a small about of text with some tag and date data asscociated with it!

Sit amet nulla facilisi morbi tempus iaculis urna. Ullamcorper a lacus vestibulum sed arcu. Ligula ullamcorper malesuada proin libero nunc consequat. Mattis aliquam faucibus purus in massa tempor nec feugiat. Maecenas ultricies mi eget mauris pharetra et ultrices. Pharetra diam sit amet nisl suscipit adipiscing bibendum. Et ligula ullamcorper malesuada proin libero. Tellus elementum sagittis vitae et leo. Sagittis nisl rhoncus mattis rhoncus urna neque. At urna condimentum mattis pellentesque id nibh tortor.
"),
(2, 'Mini Article 2', 1325546135, NULL, "##### This is another _mini_ article!

Felis donec et odio pellentesque diam volutpat commodo sed. Habitasse platea dictumst quisque sagittis purus sit amet volutpat. Urna nunc id cursus metus aliquam eleifend mi. Morbi tempus iaculis urna id volutpat lacus laoreet. Sollicitudin tempor id eu nisl nunc mi ipsum faucibus. Tortor vitae purus faucibus ornare suspendisse sed nisi lacus. Enim nunc faucibus a pellentesque. Feugiat vivamus at augue eget. Eget felis eget nunc lobortis mattis aliquam faucibus. Tortor id aliquet lectus proin nibh nisl condimentum. Egestas maecenas pharetra convallis posuere morbi leo urna molestie. Id aliquet lectus proin nibh nisl condimentum id venenatis. Libero justo laoreet sit amet. Dignissim sodales ut eu sem integer vitae justo eget. Mi bibendum neque egestas congue quisque egestas diam. Tempus urna et pharetra pharetra massa massa ultricies mi quis. Id diam maecenas ultricies mi eget mauris pharetra et ultrices. Eget nunc lobortis mattis aliquam faucibus purus in massa.
"),
(2, 'Mini Article 5', 1650774278, 1713932680, "##### This is **the** mini _article_!

Nisi est sit amet facilisis. Rutrum quisque non tellus orci ac. Mauris in aliquam sem fringilla ut. Venenatis lectus magna fringilla urna. Dictumst vestibulum rhoncus est pellentesque elit. Rhoncus est pellentesque elit ullamcorper dignissim cras. Eros in cursus turpis massa tincidunt dui. At augue eget arcu dictum varius duis at consectetur lorem. Dui ut ornare lectus sit amet est placerat. Semper viverra nam libero justo laoreet. At ultrices mi tempus imperdiet nulla malesuada pellentesque. Nullam non nisi est sit amet. Metus vulputate eu scelerisque felis imperdiet proin. Porttitor eget dolor morbi non arcu risus quis. Quis ipsum suspendisse ultrices gravida dictum. Tellus molestie nunc ma yafort ayber die partea rox.
"),
(2, 'Mini Article 3', 409674412, 447179274, "##### **This** is a _mini_ article!

Metus aliquam eleifend mi in. Fermentum iaculis eu non diam phasellus vestibulum lorem sed. Non pulvinar neque laoreet suspendisse interdum consectetur. Condimentum vitae sapien pellentesque habitant morbi tristique senectus et netus. Posuere ac ut consequat semper viverra. Tortor consequat id porta nibh. Maecenas volutpat blandit aliquam etiam erat. Ut faucibus pulvinar elementum integer enim. Nisl rhoncus mattis rhoncus urna. Id nibh tortor id aliquet lectus proin nibh nisl condimentum. Vulputate ut pharetra sit amet aliquam id. Volutpat ac tincidunt vitae semper. Sagittis id consectetur purus ut faucibus pulvinar elementum. Donec adipiscing tristique risus nec feugiat in. In ante metus dictum at tempor commodo ullamcorper a. Netus et malesuada fames ac turpis egestas maecenas pharetra.
"),
(2, 'Mini Article 4', 930049271, NULL, "##### This is ~~not~~ a _mini_ article!
`Tellus mauris a diam maecenas sed enim ut. Dui vivamus arcu felis bibendum ut tristique et egestas. Ante in nibh mauris cursus mattis. Euismod elementum nisi quis eleifend quam. Sollicitudin aliquam ultrices sagittis orci a scelerisque purus. Eget sit amet tellus cras adipiscing enim eu turpis egestas. Bibendum ut tristique et egestas. Facilisi morbi tempus iaculis urna id volutpat lacus laoreet. Nullam non nisi est sit amet facilisis magna etiam tempor. Nisi vitae suscipit tellus mauris a diam maecenas. Dignissim sodales ut eu sem integer. Vitae congue eu consequat ac felis donec et odio pellentesque. Vitae congue eu consequat ac felis donec et odio. Potenti nullam ac tortor vitae purus faucibus.`
");
--
-- DROP TABLE `tag`;
--
CREATE TABLE `tag` (
   `tagid` INT NOT NULL AUTO_INCREMENT,
   `text` VARCHAR(255) NOT NULL,
   PRIMARY KEY(`tagid`),
   UNIQUE KEY(`text`) -- Don't think we ever want two tags with the same text
);
--
INSERT INTO `tag`
(`text`)  -- tagid
VALUES
('Foo'),  -- 1
('Bar'),  -- 2
('Fizz'), -- 3
('Buzz'), -- 4
('🎂'),   -- 5
('☃️');    -- 6
--
-- DROP TABLE `mini_article_tag`;
--
CREATE TABLE `mini_article_tag` (
   `mini_articleid` INT NOT NULL,
   `tagid` INT NOT NULL,
   FOREIGN KEY (`mini_articleid`) REFERENCES `mini_article`(`mini_articleid`) ON DELETE CASCADE, -- Mini Article Tags must be attached to a Mini Article. If that Mini Article is deleted, so are the Tag associations
   FOREIGN KEY (`tagid`) REFERENCES `tag`(`tagid`) ON DELETE CASCADE -- Mini Article Tags must be attached to a Tag. If that Tag is deleted, so are the Tags
);
--
-- TRUNCATE TABLE `mini_article_tag`;
--
INSERT INTO `mini_article_tag`
(`mini_articleid`, `tagid`)
VALUES
(1, 1),
(1, 4),
(1, 6),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(4, 3),
(4, 4),
(5, 2),
(5, 4),
(5, 6);
--
-- List all tags by mini article title
-- SELECT `title`, GROUP_CONCAT(`tag`.`text`) FROM `mini_article_tag` JOIN (`tag`) USING (`tagid`) JOIN `mini_article` USING (`mini_articleid`) GROUP BY `mini_articleid`;
--
-- Get all data required to display the mini articles
-- SELECT `title`, `mini_article`.`text` as `mini_article_text`, `start_date`, `end_date`, GROUP_CONCAT(`tag`.`text`) as `tags` FROM `mini_article_tag` JOIN (`tag`) USING (`tagid`) JOIN `mini_article` USING (`mini_articleid`) GROUP BY `mini_articleid`;
--
