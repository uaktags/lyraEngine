CREATE TABLE `<pre>menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_title` varchar(50) NOT NULL DEFAULT '',
  `menu_link` varchar(100) NOT NULL DEFAULT '#',
  `menu_loginCond` int(11) NOT NULL DEFAULT '0' COMMENT '0=ShowAlways, 1=ShowOnlyLoggedIn, 2=ShowOnlyLoggedOut',
  `menu_roleID` int(11) NOT NULL DEFAULT '0' COMMENT 'If set, require that ACL permission',
  PRIMARY KEY (`menu_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `<pre>menu`
--

INSERT INTO `<pre>menu` (`menu_id`, `menu_title`, `menu_link`, `menu_loginCond`, `menu_roleID`) VALUES
(1, 'Home', 'Home', 1, 0),
(2, 'Login', 'Login', 2, 0),
(3, 'Admin', 'Admin', 1, 2),
(4, 'Register', 'Register', 2, 0),
(5, 'Logout', 'Login/logout', 1, 0),
(6, 'Index', 'Index', 2, 0),
(7, 'Administration', '#', 0, 0),
(8, 'Appearance', '#', 0, 0),
(9, 'Configuration', '#', 0, 0),
(10, 'Players', '#', 0, 0),
(11, 'Modules', '#', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `<pre>menu_assignments`
--

CREATE TABLE `<pre>menu_assignments` (
  `assign_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL DEFAULT '0',
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `menu_parent` int(11) NOT NULL DEFAULT '0',
  `menu_sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assign_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `<pre>menu_assignments`
--

INSERT INTO `<pre>menu_assignments` (`assign_id`, `location_id`, `menu_id`, `menu_parent`, `menu_sort`) VALUES
(1, 1, 1, 0, 0),
(2, 1, 2, 0, 2),
(3, 1, 3, 0, 0),
(4, 1, 4, 0, 3),
(5, 1, 5, 0, 100),
(6, 1, 6, 0, 0),
(7, 2, 7, 0, 0),
(8, 2, 8, 7, 0),
(9, 2, 9, 7, 0),
(10, 2, 10, 7, 0),
(11, 2, 11, 7, 0),
(12, 2, 3, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `<pre>menu_locations`
--

CREATE TABLE `<pre>menu_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_title` varchar(50) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `<pre>menu_locations`
--

INSERT INTO `<pre>menu_locations` (`location_id`, `location_title`) VALUES
(1, 'Main_Top'),
(2, 'Admin_Main');