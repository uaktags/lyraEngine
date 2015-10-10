<?php

namespace Module\Index\Controllers;

use \Lyra\Controller;

/**
 * Index controller
 */
class Index extends Controller
{
    /**
     * Page title
     * @var string
     */
    protected $title = 'Welcome to lyraEngine!';
	
	public function __construct()
	{
		$player = \App::getModel('session');
        if($player->isLoggedIn())
		{
			header('Location: Home');
            exit;
		}
	}

    /**
     * Default action
     * @param $args array
     */
    public function index(array $args = array())
    {
        \View::set('pageTitle', $this->title);
        \View::set('contentMsg', 'I am the Index Module');
    }

    public function testWidget(array $args = array())
    {
        \View::setTemplate('testWidget.twig');
        \View::set('pageTitle', 'Test Widget.');
        \View::set('contentMsg', 'On this page, we\'re testing Widgets');
    }

    public function testConfig(array $args = array())
    {
        \Config::set('site.general.nestedURL', 'www.me.com');
        \View::set('pageTitle', \Config::get('site.general.nestedURL'));
    }
}
