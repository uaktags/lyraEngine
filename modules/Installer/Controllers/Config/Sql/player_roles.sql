CREATE TABLE if not exists `<pre>player_roles` (
  `player_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`player_id`, `role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;