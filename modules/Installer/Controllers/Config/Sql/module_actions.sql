CREATE TABLE if not exists `<pre>module_actions` (
  `action_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(25) NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;