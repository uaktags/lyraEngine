<?php
namespace Lyra;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

try {
    require __DIR__ . '/vendor/autoload.php';
	$timer = new Profiler();

    //Iniitalize PimpleDI Container
    $container = new Container;
	$timer->setTime('Container');
	$container['profiler'] = $timer;
    //Initialize Proxy and set Container
    Proxy\Proxy::setContainer($container);
	$timer->setTime('Proxy');
    //Load all aliases from file
    $aliases = require __DIR__ . '/config/aliases.php';

    //Load Class Aliases
    foreach ($aliases as $alias => $class) {
        class_alias($class, $alias);
    }
	$timer->setTime('Class Aliases');
    
	//Initialize Lyra APP
    $app = new App($container);
	$timer->setTime('App');
    //Add App to Container
    $container['app'] = $app;

    //Initialize Config and add to Container
    $container['config'] = new Config();
	$timer->setTime('Config');
    //Set Error Handler
    set_error_handler(array($app, 'error'), E_ALL | E_STRICT);

    //Load all Configuration files in Config folder
    foreach(glob(__DIR__. '/config/*.php') as $key=>$val)
    {
        require_once $val;
    }
	$timer->setTime('Config Folder');
    $uri = new Uri();
	$timer->setTime('URI');
    if(!file_exists(__DIR__ . '/config/config.php')) {
        if ($uri->segment(0) != "Installer") {
            header("Location: Installer");
            exit;
        } elseif (file_exists(__DIR__ . '/install.config.php')) {
            require_once __DIR__ . '/install.config.php';
			$timer->setTime('installConfig');
        }
    }
	$timer->setTime('configFile Checked');
    if($uri->segment(0) == 'Installer')
        define('INSTALL', true);

    date_default_timezone_set('UTC');
	$timer->setTime('setTimeZone');
    if (!file_exists(__DIR__ . "/config/routes.php") && !defined('INSTALL')) {
		$timer->setTime('Routes doesnt exist');
		$timer->setTime(__DIR__ . "/config/routes.php");
        $routes = $app->getModel('route');
		$timer->setTime('Create Route Model');
        $routes->buildCache();
		$timer->setTime('Router buildCache()');
    }elseif(file_exists(__DIR__ . "/config/routes.php") ){
		require __DIR__ .'/config/routes.php';
		$timer->setTime('Routes');
	}
    $app->run();
	$timer->setTime('App Run');
    ob_start();

    $app->serve();
	$timer->setTime('App Serve');
    ob_end_flush();
	//die(var_dump($timer->getTime()));
} catch (\Exception $e) {
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Temporarily Unavailable');
        header('Status: 503 Service Temporarily Unavailable');
    }

    $errorCode = substr(sha1(uniqid(mt_rand(), true)), 0, 5);

    $errorMessage = $errorCode . date(' r ') . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();

	function getExceptionTraceAsString($exception) {
		$rtn = "";
		$count = 0;
		foreach ($exception->getTrace() as $frame) {
			$args = "";
			if (isset($frame['args'])) {
				$args = array();
				foreach ($frame['args'] as $arg) {
					if (is_string($arg)) {
						$args[] = "'" . $arg . "'";
					} elseif (is_array($arg)) {
						$args[] = "Array";
					} elseif (is_null($arg)) {
						$args[] = 'NULL';
					} elseif (is_bool($arg)) {
						$args[] = ($arg) ? "true" : "false";
					} elseif (is_object($arg)) {
						$args[] = get_class($arg);
					} elseif (is_resource($arg)) {
						$args[] = get_resource_type($arg);
					} else {
						$args[] = $arg;
					}   
				}   
				$args = join(", ", $args);
			}
			$rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
									 $count,
									 isset($frame['file']) ? $frame['file'] : 'unknown file',
									 isset($frame['line']) ? $frame['line'] : 'unknown line',
									 (isset($frame['class']))  ? $frame['class'].$frame['type'].$frame['function'] : $frame['function'],
									 $args );
			$count++;
		}
		return $rtn;
	}
	
    file_put_contents(__DIR__ . '/log/exceptions.log', "\n" . $errorMessage . "\n" . getExceptionTraceAsString($e) . "\n",
        FILE_APPEND);

    exit('Exception: ' . $errorCode . '<br><br><small>The issue has been logged. Please contact the website administrator.</small><br>' . $errorMessage);
}
try {
// Load Last 10 Git Logs
	$git_diff = [];
	$git_logs = [];
	exec("git log -1 --pretty=format:%h", $git_logs);
    exec("git add .");
	exec("git log --shortstat --oneline  HEAD HEAD^", $git_diff);
	$hash = explode(' ', $git_logs[0]);
	$hash = trim(end($hash));
	echo "lyraEngine is up to date with: <a href='https://github.com/uaktags/lyraEngine/commit/$hash'>$hash</a>";
	echo "<pre>";
    print_r($git_diff);
    echo "</pre>";
} catch(\Exception $e) {
	file_put_contents(__DIR__ . '/log/exceptions.log', "\n" . "GIT Version was attempted, but not installed with GIT" . "\n" . getExceptionTraceAsString($e) . "\n",
		FILE_APPEND);
}
