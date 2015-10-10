<?php

namespace Lyra;

/**
 * Hooks class
 * Formerly called Hook
 * @abstract
 */
abstract class Hook extends Common implements \Lyra\Interfaces\Hook
{
    /**
     * Application instance
     * @var \Lyra\Interfaces\App
     */
    protected $app;

    /**
     * Controller instance
     * @var \Lyra\Interfaces\Controller
     */
    protected $controller;

    /**
     * View instance
     * @var \Lyra\Interfaces\View
     */
    protected $view;

    /**
     * Set application instance
     * @param App $app
     * @return View
     */
    public function setApp(\Lyra\Interfaces\App $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Set controller instance
     * @param Controller $controller
     * @return Hook
     */
    public function setController(\Lyra\Interfaces\Controller $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Set view instance
     * @param View $view
     * @return Hook
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }
}
