CREATE TABLE if not exists `<pre>modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(25) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the module is active',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `<pre>modules` (`module_id`, `title`) VALUES
(1, 'Index'),
(2, 'Home'),
(3, 'Login'),
(4, 'Register');