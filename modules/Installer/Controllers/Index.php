<?php

namespace Module\Installer\Controllers;

use Lyra\Controller,
    \Module\Installer\Models\Installer;
/**
 * Installer Index
 * @see Library\Module
 */
class Index extends Controller
{
    /**
     * Page title
     * @var string
     */
    protected $title = 'Welcome to lyraEngine | Installer!';

	/**
	 * Default Action
	 */
	public function index()
	{
		$checks = array(
			'PHP Version' => array(
				'passed' => false,
				'required' => true,
				'message' => 'ezRPG uses some of the newer libraries only available in versions of PHP 5.3.2. and later, while we detected that you are currently running on PHP ' . PHP_VERSION . '.<br />' .
							 'If you are using your own infrastructure, please upgrade PHP to the latest version.<br />' .
							 'If you are on a shared hosting provider this may be some troublesome news, but ask them if they might be able to help you with this problem.'
			),

		/*	'APC Support' => array(
				'passed' => false,
				'required' => false,
				'message' => 'ezRPG uses APC for caching expensive computations to improve performance.<br />' .
							 'Although this is not a scrict requirement, we highly suggest that you(or your hosting provider) enable this functionality.'
			),
*/
			'Root directory writable' => array(
				'passed' => false,
				'required' => true,
				'message' => 'ezRPG writes configuration files within the root directory of the application.<br />' .
							 'You can resolve this problem by changing the permission(even temporarily) for the directory <code>' . getcwd() . '</code>.'
			)
		);

		// A series of checks to confirm compatibility
		if (version_compare(PHP_VERSION, '5.3.2', '>=')) {
			$checks['PHP Version']['passed'] = true;
		}

		/*if (function_exists('apc_fetch')) {
			$checks['APC Support']['passed'] = true;
		}*/

		if (is_writable(getcwd())) {
			$checks['Root directory writable']['passed'] = true;
		}
        \View::set('checks', $checks);
        \View::setTemplate('requirements.twig');
	}

    /**
     * @param array $args
     */
    public function license (array $args = array())
    {
        \View::setTemplate('license.twig');
        $license = nl2br(file_get_contents(__DIR__.'/License/license.txt'));
        \View::set('license', $license);
    }

    /**
     * @param array $args
     */
    public function config (array $args = array())
    {
        $data['guessUrl'] = 'http://'.$_SERVER['HTTP_HOST'].dirname(str_ireplace('installer/','', $_SERVER['REQUEST_URI']));
        if ( isset($_POST['submit']) ) {
            $dbconfig = array(
                'db' => array(
                    'driver'   => $_POST['dbtype'],
                    'host'	 => $_POST['dbhost'],
                    'database' => $_POST['dbname'],
                    'username' => $_POST['dbuser'],
                    'password' => $_POST['dbpass'],
                    'port' => '',
                    'prefix' => $_POST['dbpref']
                ),
            );
            $gameTheme = \Config::get('site.theme');
            \Config::destroyAll();
            \Config::set($dbconfig);
            try {
                $installer = new Installer($this->container);
                foreach ( glob(__DIR__.'/Config/Sql/*.sql') as $query ) {
                    $installer->runSqlFile($query, $_POST['dbpref']);
                }
            } catch(\Exception  $e) {
                die($e->getMessage());
                \View::set("fail", 'Please check your database configurations');
                $error = 1;
            }
            if ( !isset($error) ) {
                $dbtype = $_POST['dbtype'];
                $dbhost = $_POST['dbhost'];
                $dbname = $_POST['dbname'];
                $dbuser = $_POST['dbuser'];
                $dbpass = $_POST['dbpass'];
                $dbpref = $_POST['dbpref'];
                $gamename = $_POST['gamename'];
                $gameurl = $_POST['gameurl'];
                /* Generate configuration file */
                $config = <<<CONFIG
<?php

/**
 * Database Configuration
 */

\Config::set('db', array(
	'driver'   => '$dbtype',
	'host'	 => '$dbhost',
	'database' => '$dbname',
	'username' => '$dbuser',
	'password' => '$dbpass',
	'port' => '',
	'prefix' => '$dbpref'
));
CONFIG;
                $fh = fopen('config/config.php', 'w+');
                fwrite($fh, $config);
                fclose($fh);
                \App::loadHooks();
                \App::loadModels();
                /* Generate Settings */
                $settings = \App::getModel('setting');
                $settings->update(2, $gamename);
                $settings->update(3, $gameurl);
                $settings->update(4, $gameTheme);
                $settings->buildCache();
                $settings->updateConfig();
                session_unset();
                $routes = $this->app->getModel('route');
                try {
                    $routes->buildCache();
                } catch(\Exception $e) {
                    printf('<div><strong>ezRPG Exception</strong></div>%s<pre>', $e->getMessage());
                    var_dump($e);
                    die();
                }
                header("location: {$gameurl}/Installer/admin");
            }

        }
        \View::setTemplate('config.twig');
        \View::set('dbhost', (!isset($_POST['dbhost'])) ? '127.0.0.1' : $_POST['dbhost']);
        \View::set('dbuser', (!isset($_POST['dbuser'])) ? '' : $_POST['dbuser']);
        \View::set('dbname', (!isset($_POST['dbname'])) ? '' : $_POST['dbname']);
        \View::set('dbpref', (!isset($_POST['dbpref'])) ? '' : $_POST['dbpref']);
        \View::set('dbtype', (!isset($_POST['dbtype'])) ? '' : $_POST['dbtype']);
        \View::set('gamename', (!isset($_POST['gamename'])) ? 'lyraEngine' : $_POST['gamename']);
        \View::set('gameurl', (!isset($_POST['gameurl'])) ? $data['guessUrl'] : $_POST['gameurl']);

    }

    /**
     * @param array $args
     */
    public function admin (array $args = array())
    {
        if (isset($_POST['submit'])) {
            $insert = array();
            $insert['username'] = $_POST['username'];
            $insert['email'] =	$_POST['email'];
            $insert['password'] = $_POST['password'];
            $insert['confirm_password'] = $_POST['password'];

            $auth = \App::getModel('player');
            try {
                /* Attempt to register the account */
                $register = $auth->create($insert);
            } catch(\Exception $e) {
                $message = '<strong>You could not be registered:</strong><ul>';
                //foreach(unserialize($e->getMessage()) as $prev) {
                    $message .= '<li>' . $e->getMessage() . ' ' . $e->getTraceAsString() .'</li>';
                //}
                $message .= '</ul>';
                die($message);
                \View::set("warn", $message);
            }
            /* If the account is active, redirect the user to the login page */
            if (isset($register['active'])) {
                $playerRole = \App::getModel('playerRole');
                $playerRole->addRole($register['player_id'], 1);

                if ( is_writable(dirname(__DIR__)) ) {
                    $fh = fopen(dirname(__DIR__)."/locked", 'w+');

                    if ( !$fh ) {
                        die('Your admin account was created, but we were unable to lock the installer. Please remove the Module/Installer directory to use your game.');
                    } else {
                        die('Your admin account was created, and the installer was locked! Continue to your game');
                    }

                } else {
                    die('Your admin account was created, but we were unable to lock the installer. Please remove the Module/Installer directory to use your game.');
                }

            }else{

            }

        }
        \View::setTemplate('admin.twig');
    }

    /**
     * @param $file
     * @param $prefix
     * @return string
     */
    private function runSqlFile($file, $prefix)
    {
        $sql = file_get_contents($file);
        $sql = str_ireplace("<pre>", $prefix, $sql);
        $query = '';//$this->query($sql);
        return $query;
    }
}