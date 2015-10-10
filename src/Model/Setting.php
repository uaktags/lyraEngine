<?php

namespace Lyra\Model;

use Lyra\Model,
    Lyra\Interfaces\App;

/**
 * Setting
 * @see Library\Model
 */
class Setting extends Model
{
	protected $tableName = 'settings';
	
	public function update($id, $value)
	{
		$id = intval($id);
		$setting = $this->query("SELECT setting_id FROM <prefix>settings WHERE setting_id='{$id}'");
		if ($setting->rowCount() == 1) {
			$this->query("UPDATE <prefix>settings SET value='{$value}' WHERE setting_id='{$id}'");
			return true;
		}
		return false;
	}

    public function create($args)
    {
        return parent::add($args);
    }
	
	/**
	 * Retrieve all settings as an associative array
	 * @return multitype:string
	 */
	public function getAll()
	{
		$parents = $this->query('select title FROM <prefix>settings WHERE parent_id IS NULL AND active = 1');
		$parents = $parents->fetchAll();
		
		$cache = array();
		foreach($parents as $v) {
			$cache[$v['title']] = '';
		}
		
		while(1) {
			$oc = count($cache, \COUNT_RECURSIVE);
			$this->recurse($cache);
			
			$nc = count($cache, \COUNT_RECURSIVE);
			
			if($nc == $oc) {
				break;
			}
		}
		return $cache;
	}
	
	/**
	 * Utility function that recurses through an array and fills
	 * up parent settings.
	 * @param array $cache
	 */
	private function recurse(&$cache)
	{
		array_walk_recursive(
			$cache,
			function(&$item, $key, $obj)
			{
				$parent = $obj->query('SELECT setting_id FROM <prefix>settings WHERE title = \'' . $key . '\' AND value IS NULL');
				$parent = current($parent->fetchAll());
					
				if ($parent == false) {
					return;
				}
			
				$lookup = $obj->query('SELECT title, value FROM <prefix>settings WHERE parent_id = ' . $parent['setting_id']);
				$lookup = $lookup->fetchAll();
					
				foreach($lookup as $v) {
					$val = $v['value'];
			
					if ($val === '0' || $val === 'false') {
						$val = false;
					} elseif ($val === '1' || $val === 'true') {
						$val = true;
					} elseif (ctype_digit($val)) {
						$val = intval($val);
					}
			
					$item[$v['title']] = $val;
				}
			},
			$this
		);
	}

	private function arrayToString($array) {
		$string = "";
		if (is_array($array)) {
			foreach($array as $key=>$value){
				if(is_array($value)) {
					$string .= "'".$key."' => array(\n";
					$string .= $this->arrayToString($value);
					$string .= "),\n";
				}else{
					if (is_string($value) && $value != "array()") {
						$value = "'".$value."'";
					} elseif (is_bool($value)) {
						$value = ($value)?"true":"false";
					} elseif (is_null($value)) {
						$value = "NULL";
					}
					$string .= "'".$key."' => ".$value.",\n";
				}
			}
			return $string;
		}
		return false;
	}

	public function buildCache()
	{
		$set = $this->getAll();
		$cache = "<?php\n\\Config::set(array(\n";
		$cache .= $this->arrayToString($set);
		$cache .= "));";

		if(is_writable('./')){
			$fh = fopen('config/settings.php','w+');
			fwrite($fh, $cache);
			fclose($fh);
		} else {
			throw new \Exception("Unable to create Settings");
		}
		
	}
    public function updateConfig()
    {
        $set = $this->getAll();
        $db = \Config::get('db');
        \Config::destroyAll();
        \Config::set($set);
        \Config::set('db', $db);
    }
}