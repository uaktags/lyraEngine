CREATE TABLE if not exists `<pre>player_attributes` (
  `attribute_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `value` text,
  PRIMARY KEY (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;