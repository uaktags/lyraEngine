<?php

namespace Lyra\Model;

use Lyra\Model;
use \Exception;
use	\InvalidArgumentException;

/**
 * Player
 * @see Library\Model
 */
class Menu extends Model
{
	/**
	 * Random state
	 * @var string
	 */
	private $randomState;
	
	protected $useCaching = false;

    protected $tableName = 'menu';
	
	public function getMenu()
	{
		$sql = 'SELECT * FROM <prefix>menu_locations loc LEFT JOIN <prefix>menu_assignments assign ON loc.location_id = assign.location_id LEFT JOIN <prefix>menu mnu ON mnu.menu_id = assign.menu_id ORDER BY assign.menu_sort ASC';
		$query = $this->prepare($sql);
		$query->execute();
		$res = $query->fetchAll();
		$sql2 = 'SELECT DISTINCT menu_parent FROM <prefix>menu_assignments';
		$query2 = $this->prepare($sql2);
		$query2->execute();
		$res2 = $query2->fetchAll();
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array(
			'locations' => array(),
			'parents' => array()
		);
		foreach($res2 as $parents)
		{
			$menu['parents'][]=$parents['menu_parent'];
		}
		// Builds the array lists with data from the menu table
		foreach($res as $locations)
		{
			$locations['isParent'] = (in_array($locations['menu_id'], $menu['parents'])? true : false);
			$menu['locations'][$locations['location_title']][] = $locations;
			
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			//$menu['items'][$items['menu_id']] = $items;
		}
		return $menu;
	}
}