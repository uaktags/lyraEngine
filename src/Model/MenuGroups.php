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
		// Select all entries from the menu table
		//$result=mysql_query("SELECT id, label, link, parent FROM menu ORDER BY parent, sort, label");
		$result = parent::findAll();
		// Create a multidimensional array to conatin a list of items and parents
		$menu = array(
			'items' => array(),
			'parents' => array()
		);
		// Builds the array lists with data from the menu table
		foreach($result as $items)//while ($items = mysql_fetch_assoc($result))
		{
			// Creates entry into items array with current menu item id ie. $menu['items'][1]
			$menu['items'][$items['menu_id']] = $items;
		}
		return $menu;
	}
}