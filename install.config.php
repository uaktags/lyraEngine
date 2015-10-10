<?php
$url = 'http://'.$_SERVER['HTTP_HOST'].dirname(str_ireplace('installer/','', $_SERVER['REQUEST_URI']));
\Config::set(
    array(
        'site' => array(
            'name' => 'lyraEngine',
            'url' => $url,
            'theme' => 'EzRPG2',
        ),
	));