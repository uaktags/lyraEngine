<?php

namespace Lyra\Model;

use Lyra\Model;

/**
 * Role
 * @see Library\Model
 */
class Role extends Model
{
	protected $tableName = 'roles';

	/**
	 * getRoleByName
	 * @param string $name
	 * @return int
	 */
	public function getRoleByName($name) {
		$data = parent::find($name, 'title', 'role_id');
		return $data['role_id'];
	}
	
}