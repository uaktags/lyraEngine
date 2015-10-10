<?php

namespace Lyra;

/**
 * Router
 */
class Router implements \Lyra\Interfaces\Router
{
	protected $routes = array();
	
	/**
	 * Create the routes.
	 * @param \Lyra\Interfaces\Container $container
	 * @throws Exception
	 */
	public function __construct(Interfaces\Container $container)
	{
        if(is_null($container['config']['routes']) || empty($container['config']['routes']))
            return;
		foreach($container['config']['routes'] as $route => $options) {

			$type = isset($options['type']) ? strtolower($options['type']) : 'literal';

			switch ($type) {
				case 'literal':
					$route = new Router\Route\Literal($route, $options);
					break;
				case 'regex':
					$route = new Router\Route\Regex($route, $options);
					break;
				default :
					throw new Exception('Invalid route type "' . $options['type'] . '" specified');
			}

			$this->addRoute($route);
		}
	}
	
	/**
	 * Add a route
	 * @param array $route
	 */
	public function addRoute($route) 
	{
		array_push($this->routes, $route);
	}
	
	/**
	 * Resolve a url and find a matching route
	 * @param string $url
	 * @return boolean
	 */
	public function resolve($url) 
	{
		foreach($this->routes as $route) {
			$routeMatch = $route->resolve($url);
			
			if ($routeMatch !== false) {
				return $routeMatch;
			}
		}
		
		return false;
	}
}