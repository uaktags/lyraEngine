<?php

namespace Module\Home\Controllers;

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
		$this->requireLogin();
	}

    /**
     * Default action
     * @param $args array
     */
    public function index(array $args = array())
    {
		$player = \App::getModel('session');
        $playerID = $player->get('player_id');
        $this->view->set('player', $this->app->getModel('Player')->find($playerID));
    }
	
	public function test()
	{
		\View::setMessage('Testing', 'good');
	}
}
