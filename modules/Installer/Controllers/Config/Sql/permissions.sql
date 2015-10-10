CREATE TABLE if not exists `<pre>permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `<pre>permissions` (`permission_id`, `title`) VALUES
(1, 'canAdminCP'),
(2, 'canAdminPlayers'),
(3, 'canAdminConfig'),
(4, 'canAdminRoute');