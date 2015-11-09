<?php
namespace Lyra;

use Lyra\Widget\WidgetTwigExtension,
    Twig_Extension_Debug,
    Twig_Extension_Optimizer,
    Twig_Loader_Filesystem,
    Twig_NodeVisitor_Optimizer;

class View extends Common{
    protected $loader;
    public $twig;
    public $template;
    protected $templatePath;
    protected $filename;
    protected $data;
    public $name;
    public $theme;
	public $themePath;
	public $adminTheme;
    public $widgetRegistry;
    public $widgets;


    public function __construct(\Lyra\Interfaces\Container $container)
    {
        $this->container = $container;
        $container['view'] = $this;
        $this->data = array();
        $this->registerHelpers();
        $this->loadData();
        $this->modPath = 'modules/';
		$this->themePath = 'themes/'; //Always set by default
        $this->theme = \Config::get('site.theme'); //Load from Config
        $this->adminTheme = \Config::get('site.adminTheme');
    }

	/**
	 * Init Twig Environment
	 */
	protected function initTwigEnv()
	{
		if(\App::adminTheme())
			$this->theme = $this->adminTheme;
		$this->set('theme', $this->theme);
		$this->loader = new \Twig_Loader_Filesystem();
        $this->loader->addPath($this->themePath . $this->theme . '/');
        //$this->loader->addPath($this->themePath . $this->adminTheme . '/');
        $this->loadThemeTemplates();
        if(file_exists(\App::moduleDirectory() . '/templates/'))
            $this->loader->addPath(\App::moduleDirectory(). '/templates/');
        $this->loadModuleTemplates();
		//Create widget registry instance, who contains your widget
        $this->widgetRegistry = new WidgetRegistry();
		$this->twig = new \Twig_Environment($this->loader, array('debug'=>true));
		// set the optimizer-level
        $this->optimizer();
		$this->twig->addExtension(new \Twig_Extension_Debug());
		$this->getWidgets();
        // clear twig-cache
        $this->clearTwigCache();
	}
	
	private function loadThemeTemplates()
    {
		if(file_exists($dir = $this->themePath.$this->theme . '/modules')){
			$mods = scandir($dir);
			foreach($mods as $key => $val) {
				if (!in_array($val, array(".", ".."))) {
					if(is_dir($moddir = $dir . DIRECTORY_SEPARATOR . $val . '/Templates/'))
					{
						$this->loader->addPath($moddir);
					}
				}
			}
		}
       
    }

    /**
     * @param $loader
     */
    private function loadModuleTemplates()
    {
        if(file_exists($dir = $this->modPath)) {
            $mods = scandir($dir);
            foreach ($mods as $key => $val) {
                if (!in_array($val, array(".", ".."))) {
                    //if (is_dir($moddir = dirname(__DIR__) . '/' . $dir .'/Templates/')) {
                    if(is_dir($moddir = $dir . $val . '/Templates/')) {
                        $this->loader->addPath($moddir);
                    }
                }
            } 
        }
    }
    /**
     * Register helpers
     */
    protected function registerHelpers()
    {
        foreach(scandir(dirname(__FILE__) . '/View/Helper') as $file) {
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

    /**
     * set the template-path or use the path from config
     *
     * @param array|string $templatePath
     *
     * @return bool
     */
    private function setTemplatePath($templatePath)
    {
        $return = false;
        if (is_string($templatePath)) {
            if ($this->checkTemplatePath($templatePath) === true) {
                $this->templatePath[] = $templatePath;
                $return = true;
            }
        }
        else if (is_array($templatePath)) {
            foreach ($templatePath as $path) {
                if ($this->checkTemplatePath($path) === true) {
                    $this->templatePath[] = $path;
                    $return = true;
                }
            }
        }
        return $return;
    }
    /**
     * check if the the template-directory exists
     *
     * @param string $templatePath
     * @param bool   $exitOnError
     *
     * @return bool
     */
    private function checkTemplatePath($templatePath, $exitOnError = true)
    {
        if (!is_dir($templatePath)) {
            if ($exitOnError === true) {
                exit();
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * optimize twig-output
     *
     * OPTIMIZE_ALL (-1) | OPTIMIZE_NONE (0) | OPTIMIZE_FOR (2) | OPTIMIZE_RAW_FILTER (4) | OPTIMIZE_VAR_ACCESS (8)
     *
     */
    private function optimizer()
    {
        $optimizeOption = -1;

        switch ($optimizeOption) {
            case -1:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL;
                break;
            case 0:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_NONE;
                break;
            case 2:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_FOR;
                break;
            case 4:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_RAW_FILTER;
                break;
            case 8:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_VAR_ACCESS;
                break;
            default:
                $nodeVisitorOptimizer = Twig_NodeVisitor_Optimizer::OPTIMIZE_ALL;
                break;
        }
        $optimizer = new Twig_Extension_Optimizer($nodeVisitorOptimizer);
        $this->twig->addExtension($optimizer);
    }
    /**
     * clear TwigWrapper-Cache && exit()
     */
    public function clearTwigCache()
    {
        if (isset($_GET['clearTwigCache']) && $_GET['clearTwigCache'] == 1)
        {
            $this->twig->clearCacheFiles();
            echo "twig-cache cleared!";
            exit();
        }
    }
    /**
     * loads default-data into TwigWrapper
     */
    public function loadData()
    {
        // TODO: load extra data e.g. from DB
    }

    /**
     * render the template
     *
     * @param bool $withHeader
     *
     * @return string
     */
    public function render( $withHeader = true)
    {
        // DEBUG
        if (isset($_GET['twigDebug']) && $_GET['twigDebug'] == 1) {
            $this->debug();
        }
        $this->template = $this->twig->loadTemplate($this->template);

        if ($withHeader === true) {
            header('X-UA-Compatible: IE=edge,chrome=1');
            header('Content-Type: text/html; charset=utf-8');
        }
        return $this->template->render($this->data);
    }
    /**
     * debug
     */
    public function debug()
    {
        var_dump($this);
        exit();
    }
    /**
     * show all variables
     */
    public function debug_data()
    {
        var_dump($this->data);
        exit();
    }

    public function get($variable)
    {
        $value = null;

        if (isset($this->data[$variable])) {
            $value = $this->data[$variable];
        }

        return $value;
    }

    public function set($variable, $value = null)
    {
        $this->data[$variable] = $value;

        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
        return $this;
    }
    /**
     * Proxy call requests
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args) {
        if (in_array($method, get_class_methods($this))) {
            return call_user_func_array(array($this, $method), $args);
        }

        if (array_key_exists($method, $this->helpers)) {
            $class = $this->helpers[$method];
            return call_user_func_array(array($class, $method), $args);
        }
    }
    /**
     * Add All Registered Widgets To Twig
     */
    private function getWidgets()
    {
        $this->registerWidget();
        $this->twig->addExtension(new WidgetTwigExtension($this->widgetRegistry, $this->twig));
    }

    /**
     * Register All Widgets Found
     * @return $this
     */
    private function registerWidget()
    {
        $rootDir = dirname(__DIR__);
        /**
         * Scan all Modules for Widgets
         */
        $moddirs =  array_diff(scandir($rootDir.'/modules/'), array('..', '.'));
        $widgets=array();
        foreach($moddirs as $key=>$module)
        {
            $widgetsdir = $rootDir . '/modules/'. $module . '/Widgets';
            if(file_exists($widgetsdir)) {
                $widgetsindir = array_diff(scandir($widgetsdir), array('..', '.'));
                foreach($widgetsindir as $k=>$widget)
                {
                    $widget = preg_replace('/\.php$/', '', $widget);
                    $widgetClass = 'Module\\'. $module . '\\Widgets\\' . $widget;
                    $widget = new $widgetClass();
                    $this->widgetRegistry->addWidget($widget);
                }

            }
        }

        /**
         * Scan the Widget Folder
         */
        if(file_exists($rootDir.'/widgets')) {
            $moddirs = array_diff(scandir($rootDir . '/widgets/'), array('..', '.'));
            $widgets = array();
            foreach ($moddirs as $key => $widget) {
                $widget = preg_replace('/\.php$/', '', $widget);
                $widgetClass = 'Widgets\\' . $widget;
                $widget = new $widgetClass();
                $this->widgetRegistry->addWidget($widget);
            }
        }
        return $this;
    }
}