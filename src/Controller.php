<?php

namespace Lyra;

/**
 * Controller class
 * @abstract
 */
abstract class Controller extends Common implements \Lyra\Interfaces\Controller
{
    /**
     * Application instance
     * @var \Lyra\Interfaces\App
     */
    protected $app;

    /**
     * View instance
     * @var \Lyra\Interfaces\View
     */
    protected $view;

    /**
     * Page title
     * @var string
     */
    protected $title;

    /**
     * Routes
     * @var array
     */
    protected $routes = array();

    /**
     * @var
     */
    protected $container;

    /**
     * Set application instance
     * @param \Lyra\Interfaces\App $app
     * @return \Lyra\Interfaces\Controller
     */
    public function setApp(\Lyra\Interfaces\App $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Set view instance
     * @param \Lyra\Interfaces\App $app
     * @return \Lyra\Interfaces\Controller
     */
    public function setView($view)
    {
        $this->view = $view;

        $reflection = new \ReflectionClass($this);

        $this->view->name = strtolower($reflection->getShortName());

        $this->view->pageTitle = $this->title;

        return $this;
    }

    public function setContainer(\Lyra\Container $container)
    {
        $this->container = $container;
    }

    /**
     * Set page title
     * @param string $app
     * @return \Lyra\Interfaces\Controller
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->view->pageTitle = $title;

        return $this;
    }

    /**
     * Get routes
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Default action
     */
    public function index()
    {
    }
	
	public function requireLogin($redirect = 'Index')
	{
		$player = \App::getModel('session');
        $checkPlayer = $player->isLoggedIn();
        \View::set('loggedIn', $checkPlayer);

        if ($checkPlayer === false ){
            header('Location: '.$redirect);
            exit;
		}
	}
}
