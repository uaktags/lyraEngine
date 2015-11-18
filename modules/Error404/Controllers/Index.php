<?php
namespace Module\Admin\Controllers;
use \Lyra\Controller;
/**
 * Index controller
 */
class Index extends Controller
{
    private $players;
    public function __construct()
    {
        \App::useAdminTheme();
        $player = \App::getModel('session');
		$checkPlayer = $player->isLoggedIn() && \Acl::hasRole('administrator');
        \View::set('loggedIn', $checkPlayer);
        if ($checkPlayer === false ){
            header('Location: Home');
            exit;
        } else {
            $playerID = $player->get('player_id');
            \View::set('player', \App::getModel('Player')->find($playerID));
        }
        $this->players = \App::getModel('adminPlayers');
        $this->loadStats();
    }
    /**
     * Default action
     * @param $args array
     */
    public function index(array $args = array())
    {
        \View::setTemplate('Admin.twig');
        \View::set('pageTitle', 'Admin Panel');
    }
    public function appearance()
    {
        \View::setTemplate('Admin.twig');
        \View::set('pageTitle', 'Appearances');
        \View::set('mainContent', 'Select Themes and stuff');
    }
    public function users()
    {
        \View::setTemplate('Users.twig');
		$players = \App::getModel('Player');
		\View::set('playerArray', $players->findAll());
        \View::set('pageTitle', 'User Manager');
        \View::set('mainContent', 'See stats, edit users, etc');
    }
    public function config()
    {
        \View::setTemplate('Admin.twig');
        \View::set('pageTitle', 'Configuration');
        \View::set('mainContent', 'See and edit the Site Config');
    }
    public function modules()
    {
        \View::setTemplate('Admin.twig');
        \View::set('pageTitle', 'Module Manager');
        \View::set('mainContent', 'Install/Activate/Deactive/Uninstall Modules');
    }
    public function loadStats()
    {
        \View::set('numPlayers', $this->players->getTotal());
    }
}