<?php

namespace Lyra;

use Lyra\View;
/**
 * Application class
 * @abstract
 */
class App extends Common implements Interfaces\App
{
    /**
     * Module
     * @var string
     */
    protected $module = 'Index';

    /**
     * Module path
     * @var string
     */
    protected $modulePath = 'modules/';

    /**
     * The 'friendly' version string
     * @var string
     */
    protected $versionString = '1.0.0 Alpha 1';

    /**
     * The version ID that can be used to compare two version numbers
     * First Point  Second Point    Third Point     Build Type  Build Number
     * A            BB              CC              D           EE
     *
     * For Build Type:
     * 1 = Alpha
     * 2 = Beta
     * 3 = Release Cantidate
     * 4 = Gold Release
     * 10000101 = 1.0.0 Alpha 1
     * 10000400 = 1.0.0
     * 10000401 = 1.0.0 PL 1
     * @var int
     */
    protected $versionId = 10000101;

    /**
     * Twig view instance
     * @var Twig
     */
    protected $view;

    /**
     * Configuration values
     * @var array
     */
    protected $config = array();

    /**
     * Hooks
     * @var array
     */
    protected $hooks = array();

    /**
     * Container
     * @var \Lyra\Interfaces\Container
     */
    protected $container;

    /**
     * Access Control Layer
     * @Todo Must implement \Lyra\Interfaces\ACL
     * @var array
     */
    protected $acl;

    /**
     * PDO
     * @var \Lyra\Models\Pdo
     */
    public $Pdo;

    /**
     * Variables
     * Pulled from old View::variables
     * @var array
     */
    public $variables = array();

    public $template = '';

    public $cache;
	
	protected $models = array();

    public $adminTheme = false;
	
    /**
     * Constructor
     * @param \Lyra\Interfaces\Container $container
     * @return App
     */
    public function __construct(\Lyra\Interfaces\Container $container)
    {
        $container['app'] = $this;

        $this->container = $container;

        if(!defined('INSTALL')) {
            $this->loadHooks();
			\Profiler::setTime('loadHooks');
            $this->loadModels();
			\Profiler::setTime('loadModels');
        }
    }

    /**
     * Distpatch the controller
     * @return App
     */
    public function run()
    {
		\Profiler::setTime('inside App::run');
        // Instantiate ACL, conditionally
        //die(var_dump($this->container['config']));
        if ($this->container['config']['security']['acl']['use']){
            /* @see Library\AccessControl */
            $this->initACL();
			\Profiler::setTime('initACL');
		}

        // Instantiate the Router
        $router = new Router($this->container);
		\Profiler::setTime('router');
		
        $requestUri = isset($_GET['q']) && !empty($_GET['q']) ? ucfirst($_GET['q']) : 'Index';
        $routeMatch = $router->resolve($requestUri);
        if ($routeMatch == false) {
            $args = $requestUri ? explode('/', $requestUri) : array();
            $routeMatch['module'] = $args[0];
            if(isset($args[1]))
                $routeMatch['action'] = $args[1];
            $params = array();
            if(isset($args[2]))
            {
                foreach($args as $key => $val)
                    if($key != 0 && $key != 1)
                    {
                        array_push($params, $val);
                    }
            }
            $routeMatch['params'] = $params;
        }

        // Authorization of routes through the AC
        if ($this->container['config']['security']['acl']['use']) {
            if (isset($routeMatch['access']['permission']) && $routeMatch['access']['permission'] != NULL) {
                foreach ($routeMatch['access']['permission'] as $permission) {
                    if (!$this->acl->verify($permission)) {
                        $routeMatch = $router->resolve('error/access-denied');
                    }
                }
            }
            if (isset($routeMatch['access']['role']) && $routeMatch['access']['role'] != NULL) {
                foreach ($routeMatch['access']['role'] as $role) {
                    if (!$this->acl->hasRole($role)) {
                        $routeMatch = $router->resolve('error/access-denied');
                    }
                }
            }
        }

        return $this->dispatch($routeMatch);
    }

    public function dispatch(array $routeMatch)
    {
        $controllerClass = '\\Module\\' . $this->module . '\Controllers\Index';
        $this->module = (!empty($routeMatch['base']) ? str_replace('/', '\\', ucwords($routeMatch['base'])) . '\\' : '') . ucwords($routeMatch['module']);
        $this->action = (!empty($routeMatch['action'])?$routeMatch['action']:'index');
        $this->params = $routeMatch['params'];

        $args = $routeMatch['params'];
        $this->config = $this->getConfig();
        $this->initTwig();

        //$args = $requestUri ? explode('/', $requestUri) : array();
        $params = $args;
        if ( $args )
        {
            if ( !isset( $args[1] ) ) {
                $controllerClass = $this->module . '\Controllers\\Index';
            } else {
                //$controllerClass = $this->module . '\Controllers\\' . str_replace(' ', '\\', ucwords(str_replace('_', ' ', str_replace('-', '', array_shift($args)))));
                $controllerClass =  $this->module . '\Controllers\\Index';
                array_shift($args);
            }
            if ( $args ) {
                $action = str_replace('-', '', array_shift($args));
            }
            if ( is_file($this->modulePath . str_replace('\\', '/', $controllerClass) . '.php') ) {
                $params[0] = null;
                $controllerClass = '\\Module\\' . $this->module . '\Controllers\Index';

                // Instantiate the controller
                $controller = new $controllerClass();
                // Get the action and named parameters if custom routes have been specified
                $routes = $controller->getRoutes();
                if(!empty($routes)) {
                    foreach ( $routes as $route => $method ) {
                        $segments = explode('/', $route);
                        $regex = '/^' . str_replace('/', '\\/', preg_replace('/\(:[^\/]+\)/', '([^/]+)', preg_replace('/([^\/]+)/', '(\\1)', $route))) . '$/';
                        preg_match($regex, $requestUri, $matches);
                        array_shift($matches);
                        if ( $matches ) {
                            $action       = $method;
                            $params = array();
                            foreach ( $segments as $i => $segment ) {
                                if ( substr($segment, 0, 1) == ':' ) {
                                    $params[ltrim($segment, ':')] = $matches[$i];
                                }
                            }
                            $break;
                        }
                    }
                }
            } else {
                //PageNotFound
                $controllerClass = '\\Module\\Error404\\Controllers\\Index';
                $controller = new $controllerClass();
            }
        } elseif(class_exists('\\Module\\' . $this->module . '\Controllers\Index')) {
            $controllerClass = '\\Module\\' . $this->module . '\Controllers\Index';
            $controller = new $controllerClass();
        } else {
            $controllerClass = '\\Module\\Error404\\Controllers\\Index';
            $controller = new $controllerClass();
            $this->action = 'Index';
        }

        $actionExists = false;
        if (method_exists($controller, $this->action)) {
            $method = new \ReflectionMethod($controller, $this->action);

            if ($method->isPublic() && !$method->isFinal() && !$method->isConstructor()) {
                $actionExists = true;
            }
        }else{
			$controllerClass = '\\Module\\Error404\\Controllers\\Index';
            $controller = new $controllerClass();
			$this->action = 'Index';
		}

        if(!defined('INSTALL'))
            $this->registerHook('actionBefore');

        if ($actionExists) {
            $params[1] = null;
        } else {
            $this->action = 'Index';
        }

        $controller
            ->setApp($this)
            ->setView($this->view)
            ->setContainer($this->container);
        
		// Call the controller action
        $controller->{$this->action}(array_filter($params));

        if(!defined('INSTALL'))
            $this->registerHook('actionAfter');
		
        $moduleTemplates = \Config::get("site.url") .'/'. $this->modulePath . $this->module . '/Templates/';
        \View::set('moduleTemplates', $moduleTemplates );

        return $this;
    }

    public function useAdminTheme()
    {
        $this->adminTheme = true;
    }
	
	public function adminTheme()
	{
		return $this->adminTheme;
	}

	public function moduleDirectory()
	{
		return $this->modulePath . $this->module;
	}
	
    /**
     * Serve the page
     * @return App
     */
    public function serve()
    {
		\View::initTwigEnv();
		\View::set('config', $this->getConfig());
			
		if(!defined('INSTALL'))
		{
			$menus = $this->getModel('Menu');
			\View::set('menu', $menus->getMenu());
			$player = \App::getModel('session');
			\View::set('loggedIn', $player->isLoggedIn());
			\Acl::setPlayer($player);
			$role = \Acl::getRoles();
			if(!empty($role))
				\View::set('playerRole', $role[0]->metadata['role_id']);
		}
		
        if($this->adminTheme)
            \View::setTheme(\Config::get('site.adminTheme'));
        if ($this->view->template == '') {
            \View::setTemplate($this->module . '.twig');
        }
        echo \View::render();
        return $this;
    }

    /**
     * Load hooks
     * @param string $namespace
     * @return App
     */
    public function loadHooks()
    {
        // Load hooks
        
        if(file_exists($this->modulePath . str_replace('\\', '/', $this->module . '/Hooks'))) {
            if ($handle = opendir($this->modulePath . str_replace('\\', '/', $this->module . '/Hooks'))) {
                while (($file = readdir($handle)) !== false) {
                    $hookClass = $this->module . '\Hooks\\' . preg_replace('/\.php$/', '', $file);

                    if (is_file($this->modulePath . str_replace('\\', '/', $hookClass) . '.php')) {
                        $hookClass = 'Module\\' . $hookClass;

                        $reflection = new \ReflectionClass($hookClass);

                        $parentClass = $reflection->getParentClass();
                        foreach (get_class_methods($hookClass) as $methodName) {
                            $method = new \ReflectionMethod($hookClass, $methodName);

                            if ($method->isPublic() && !$method->isFinal() && !$method->isConstructor() && !$parentClass->hasMethod($methodName)) {
                                $this->hooks[$hookClass][] = $methodName;
                            }
                        }
                    }
                }

                ksort($this->hooks);

                closedir($handle);
            }
        }

        $rootDir = dirname(__DIR__);
        /**
         * Scan all Modules for Hooks
         */
        $moddirs =  array_diff(scandir($rootDir.'/modules/'), array('..', '.'));
        $hooks=array();
        foreach($moddirs as $key=>$module)
        {
            $hooksdir = $rootDir . '/modules/'. $module . '/Hooks';
            if(file_exists($hooksdir)) {
                $hooksindir = array_diff(scandir($hooksdir), array('..', '.'));
                foreach($hooksindir as $k=>$hook)
                {
                    $hook = preg_replace('/\.php$/', '', $hook);
                    $hookClass = 'Module\\'. $module . '\\Hooks\\' . $hook;
                    $reflection = new \ReflectionClass($hookClass);

                    $parentClass = $reflection->getParentClass();

                    foreach (get_class_methods($hookClass) as $methodName) {
                        $method = new \ReflectionMethod($hookClass, $methodName);

                        if ($method->isPublic() && !$method->isFinal() && !$method->isConstructor() && !$parentClass->hasMethod($methodName)) {
                            $this->hooks[$hookClass][] = $methodName;
                        }
                    }
                }

            }
        }

        if(file_exists(__DIR__.'/Hook')) {
            $moddirs = array_diff(scandir(__DIR__.'/Hook'), array('..', '.', 'Helper'));
            foreach ($moddirs as $key => $hook) {
                if(file_exists(__DIR__.'/Hook/'.$hook)) {
                    $hook = preg_replace('/\.php$/', '', $hook);
                    $hookClass = 'Lyra\\Hook\\' . $hook;

                    $reflection = new \ReflectionClass($hookClass);

                    $parentClass = $reflection->getParentClass();

                    foreach (get_class_methods($hookClass) as $methodName) {
                        $method = new \ReflectionMethod($hookClass, $methodName);

                        if ($method->isPublic() && !$method->isFinal() && !$method->isConstructor() && !$parentClass->hasMethod($methodName)) {
                            $this->hooks[$hookClass][] = $methodName;
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get a configuration value
     * @param string $variable
     * @return mixed
     */
    public function getConfig()
    {
        return \Config::getAll();
    }




    /**
     * Register a hook for hooks to implement
     * @param string $hookName
     * @param array $params
     * @returns mixed
     */
    public function registerHook($hookName, array $params = array())
    {
		$res='';
		foreach ($this->hooks as $pluginName => $hooks) {

			if (is_array($hooks)) {
				if (in_array($hookName, $hooks)) {
					$hook = new $pluginName();

					$hook
						->setApp($this)
						->setView($this->view);

					$hookres = $hook->{$hookName}($params);
					if(isset($hookres) && !is_null($hookres)){
						if(is_array($res))
							array_push($res, $hookres);
						else
							$res = array($hookres);
					}

				}
			} else {
				if ($hooks == $hookName) {
					$hook = new $pluginName();

					$hook
						->setApp($this)
						->setView($this->view);

					$hookres = $hook->{$hookName}($params);
					if(isset($hookres) && !is_null($hookres))
					{
						if(is_array($res))
							array_push($res, $hookres);
						else
							$res = array($hookres);
					}
				}
			}
		}
		return $res;
    }

    /**
     * Convert errors to \ErrorException instances
     * @param int $number
     * @param string $string
     * @param string $file
     * @param int $line
     * @throws \ErrorException
     */
    public function error($number, $string, $file, $line)
    {
        throw new \ErrorException($string, 0, $number, $file, $line);
    }

    /**
     * Get a model
     * @param string $modelName
     * @return object
     */
    public function getModel($modelName)
    {
	    //Oh god no, this whole ucfirst, idk why/if I started it but it's gotta go.
        if(array_key_exists($modelName = ucfirst($modelName), $this->models))
		{
            return new $this->models[$modelName]($this->container);
		}
    }

    /**
     * Load all Models including Module-specific models
     * @return array
     */
	public function loadModels()
	{
        // Load all Models within Lyra's Model folder
		$dir = __DIR__."/Model/";
		$models = scandir($dir);
		foreach($models as $key => $val) {
			if (!in_array($val, array(".", ".."))) {
				$temp = explode('.', $val);
				$ext  = array_pop($temp);
				$name = implode('.', $temp);
				$this->models[$name] = "\\Lyra\\Model\\" . ucfirst($name);
			}
		}

        // Search for Models within individual Modules
		$dir = dirname(__DIR__)."/modules/";
		$mods = scandir($dir);
		foreach($mods as $key => $mod) {
			if (!in_array($mod, array(".", ".."))) {
				if(is_dir($moddir = $dir . $mod . "/Models/"))
				{
					$models = scandir($moddir);
					foreach($models as $key2 => $val) {
						if (!in_array($val, array(".", ".."))) {
							$temp = explode('.', $val);
							$ext  = array_pop($temp);
							$name = implode('.', $temp);
							
							$this->models[$name] = "\\Module\\$mod\\Models\\" . ucfirst($name);
						}
					}
				}
			}
		}
	}
	
    /**
     * Create View Object
     */
    private function initTwig()
    {
        $this->view = new \Lyra\View($this->container);
    }

    private function initACL()
    {
        $this->acl = new \Lyra\Acl($this->container);
    }

   
    /**
     * Register helpers
     */
    protected function registerViewHelpers()
    {
        if(file_exists($dir = dirname(__FILE__) . '/View/Helper')) {
            foreach(scandir($dir) as $file) {
                if (is_dir($file)) {
                    continue;
                }

                $fileName = basename($file);
                $className = substr($fileName, 0, strrpos($fileName, '.'));
                $className = '\\' . __NAMESPACE__ . '\View\Helper\\' . $className;
                if (class_exists($className)) {
                    $class = new $className($this->container);
                    foreach($class->helpers as $helper) {
                        $this->helpers[$helper] = $class;
                    }
                }
            }
        }
    }


}
