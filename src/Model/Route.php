<?php

namespace Lyra\Model;
use \Lyra\Model;

/**
 * Route
 * @see Library\Model
 */
class Route extends Model
{
	protected $tableName = 'routes';
	
	/**
	 * Retrieve specific route
	 * @param integer $id Route ID
	 */
	public function get($id)
	{
		$id = intval($id);
		$route = $this->query('SELECT * FROM <prefix>routes WHERE `route_id`='.$id);
		if ($route->rowCount() == 1) {
			return $route->fetch();
		}
		return false;
	}

	/**
	 * Builds cache of routes
	 * @return boolean
	 */
	public function buildCache()
	{
		$routes = $this->findAll();
		$cache = "<?php\n\$config['routes'] = array(";
		foreach ($routes as $route) {
			$cache .= "\n	'".$route['path']."' => array(";
			if ($route['base'] != NULL)
				$cache .= "\n		'base' => '".$route['base']."',";
			$cache .= "\n		'module' => '".$route['module']."',";
			if ($route['action'] != NULL)
				$cache .= "\n		'action' => '".$route['action']."',";
			if ($route['params'] != NULL) {
				$params = explode(',',$route['params']);
				if (count($params) > 1) {
					foreach ($params as $param) {
						$paramList .= "'".$param."',";
					}
				} else {
					$paramList = "'".$route['params']."',";
				}
				$cache .= "\n		'params' => array(".$paramList."),";
			}
			if ($route['permission'] != NULL || $route['role'] != NULL) {
				$cache .= "\n		'access' => array(";
				if ($route['permission'] != NULL) {
					$permissions = "";
					$permissionsArray = explode(',', $route['permission']);
					foreach ($permissionsArray as $permission) {
						$permissions .= "'".$permission."',";
					}
					$cache .= "\n			'permission' => array(".$permissions."),";
				}
				if ($route['role'] != NULL) {
					$roles = "";
					$rolesArray = explode(',', $route['role']);
					foreach ($rolesArray as $role) {
						$roles .= "'".$role."',";
					}
					$cache .= "\n			'role' => array(".$roles."),";
				}
				$cache .= "\n		),";
			}
			if ($route['type'] != NULL)
				$cache .= "\n		'type' => '".$route['type']."',";
			$cache .= "\n	),";
		}
		$cache .= "\n);";

		if(is_writable('./config/')){
			$fh = fopen('./config/routes.php','w+');
			fwrite($fh, $cache);
			fclose($fh);
		} else {
			throw new \Exception("Unable to create Routes");
		}
	}
}