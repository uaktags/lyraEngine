CREATE TABLE if not exists `<pre>settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `value` text,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `<pre>settings` (`setting_id`, `parent_id`, `title`, `value`, `active`) VALUES
(1, NULL, 'site', NULL, 1),
(2, 1, 'name', '', 1),
(3, 1, 'url', '', 1),
(4, 1, 'theme', 'EzRPG2', 1),
(5, 1, 'adminTheme', 'Admin', 1),
(6, NULL, 'router', NULL, 1),
(7, 6, 'partialRoutes', 'true', 1),
(8, 6, 'routes', 'array()', 1),
(9, NULL, 'cache', NULL, 1),
(10, 9, 'use', 'false', 1),
(11, 9, 'prefix', 'ezRPG', 1),
(12, 9, 'ttl', '60', 1),
(13, NULL, 'security', NULL, 1),
(14, 13, 'login', NULL, 1),
(15, 14, 'showInvalidLoginReason', 'true', 1),
(16, 14, 'returnUsernameOnFailure', 'true', 1),
(17, 13, 'showExceptions', 'true', 1),
(18, 13, 'acl', NULL, 1),
(19, 18, 'use', 'true', 1),
(20, 18, 'rootRole', 'root', 1),
(21, NULL, 'accounts', NULL, 1),
(22, 21, 'requireActivation', 'false', 1),
(23, 21, 'emailActivation', 'false', 1);