<?php
$config['routes'] = array(
	'admin' => array(
		'module' => 'admin',
		'access' => array(
			'permission' => array('canAdminCP',),
		),
		'type' => 'literal',
	),
	'admin/player' => array(
		'base' => 'admin',
		'module' => 'player',
		'access' => array(
			'permission' => array('canAdminCP','canAdminPlayers',),
		),
		'type' => 'literal',
	),
	'admin/player/listing' => array(
		'base' => 'admin',
		'module' => 'player',
		'action' => 'listing',
		'access' => array(
			'permission' => array('canAdminCP','canAdminPlayers',),
		),
		'type' => 'literal',
	),
	'index(.*)' => array(
		'module' => 'index',
		'params' => array('act',),
		'type' => 'regex',
	),
	'error(/+.*)' => array(
		'module' => 'error',
		'action' => 'index',
		'params' => array('type',),
		'type' => 'regex',
	),
	'player/([a-z]+)' => array(
		'module' => 'player',
		'action' => 'view',
		'params' => array('username',),
		'type' => 'regex',
	),
	'login' => array(
		'module' => 'login',
		'type' => 'literal',
	),
	'register' => array(
		'module' => 'register',
		'type' => 'literal',
	),
	'home' => array(
		'module' => 'home',
		'type' => 'literal',
	),
	'logout' => array(
		'module' => 'login',
		'action' => 'logoutt',
		'type' => 'literal',
	),
	'admin/config' => array(
		'base' => 'admin',
		'module' => 'config',
		'access' => array(
			'permission' => array('canAdminCP','canAdminConfig',),
		),
		'type' => 'literal',
	),
	'admin/config/route' => array(
		'base' => 'admin',
		'module' => 'config',
		'action' => 'route',
		'access' => array(
			'permission' => array('canAdminCP','canAdminConfig','canAdminRoute',),
		),
		'type' => 'literal',
	),
	'admin/config/route(/+.*)' => array(
		'base' => 'admin',
		'module' => 'config',
		'action' => 'editroute',
		'params' => array('type',),
		'access' => array(
			'permission' => array('canAdminCP','canAdminConfig','canAdminRoute',),
		),
		'type' => 'regex',
	),
	'admin/config/route/rebuild' => array(
		'base' => 'admin',
		'module' => 'config',
		'action' => 'rebuildroutes',
		'access' => array(
			'permission' => array('canAdminCP','canAdminConfig','canAdminRoute',),
		),
		'type' => 'literal',
	),
);