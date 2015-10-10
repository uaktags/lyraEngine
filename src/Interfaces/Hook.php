<?php

namespace Lyra\Interfaces;

/**
 * Hook interface
 */
interface Hook extends Common
{
    /**
     * Set application instance
     * @param App $app
     * @return Hook
     */
    public function setApp(App $app);

    /**
     * Set controller instance
     * @param Controller $controller
     * @return Hook
     */
    public function setController(Controller $controller);

    /**
     * Set view instance
     * @param View $view
     * @return Hook
     */
    public function setView($view);
}
