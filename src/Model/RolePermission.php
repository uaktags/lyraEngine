<?php
namespace Lyra\Model;
use \Lyra\Model;

/**
 * RolePermission
 * @see Library\Model
 */
class RolePermission extends Model
{
	protected $tableName = 'role_permissions';
	
	/**
	 * getPermission
	 * @param int $role_id
	 * @return object
	 */
	public function getPermissions($role_id = '') {
		$where='';
		if($role_id != '')
			$where = 'WHERE rp.role_id = :role_id';
		$sql = 'SELECT * FROM <prefix>role_permissions rp LEFT JOIN <prefix>permissions p ON p.permission_id = rp.permission_id '.$where;
		$query = $this->prepare($sql);
		
		if($role_id != '')
			$query->bindParam('role_id', $role_id);
		$query->execute();
		
		return $query->fetchAll();
	}
	
}