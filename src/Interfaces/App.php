<?php

namespace Lyra\Interfaces;

/**
 * Application interface
 */
interface App extends Common
{
    /**
     * Constructor
     * @param \Lyra\Interfaces\Container $container
     * @return App
     */
    public function __construct(Container $container);

    /**
     * Initiate the App
     */
    public function run();

    /**
     * Dispatch the controller
     * @return App
     */
    public function dispatch(array $routeMatch);

    /**
     * Load plugins
     * @return App
     */
    public function loadHooks();

    /**
     * Get a configuration value
     * @return mixed|null
     */
    public function getConfig();

    /**
     * Register a hook for plugins to implement
     * @param string $hookName
     * @param \Lyra\Interfaces\Controller $controller
     * @param array $params
     * @return App
     */
    public function registerHook($hookName, /*Controller $controller,*/ array $params);

    /**
     * Convert errors to \ErrorException instances
     * @param int $number
     * @param string $string
     * @param string $file
     * @param int $line
     * @throws Exception
     */
    public function error($number, $string, $file, $line);
}
