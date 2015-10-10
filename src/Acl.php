<?php

namespace Lyra;
use Lyra\Acl\Role;

/**
 * AccessControl
 */
class Acl implements \Lyra\Interfaces\Acl
{
	
	protected $container;
	protected $player;
	protected $roles = array();

	/**
	 * Instantiates the ACL
	 * @param \Lyra\Interfaces\Container $container
	 */
	public function __construct(\Lyra\Interfaces\Container &$container)
	{
		$this->container = $container;
		\Profiler::setTime('ACL::constructor');
		// is the player logged in?
		$sessionModel = \App::getModel('Session');
		\Profiler::setTime('ACL::_construct SessionModel loaded');
		$playerModel = \App::getModel('Player');
		\Profiler::setTime('ACL::_construct playerModel loaded');

		if ($sessionModel->isLoggedIn()) {
			$this->setPlayer($playerModel->find($sessionModel->getPlayerId()));
			\Profiler::setTime('ACL::_construct setPlayer to Loggedin');
		} else {
			$this->setPlayer($playerModel->findGuest());
			\Profiler::setTime('ACL::_construct setPlayer as Guest');
		}

		// lookup in cache for roles
        if ( \Config::get('cache.use') === true && isset( $container['cache']['acl_player_' . $this->player['player_id'] .'_roles'])) {
			$roles = \Cache::get('acl_player_' . $this->player['player_id'] .'_roles');
			\Profiler::setTime('ACL::_construct CacheUse is true, set roles');
		} else {
			$roles = \App::getModel('PlayerRole')->findAllForPlayer($this->player['player_id']);
			\Profiler::setTime('ACL::_construct CacheUse is false, set roles');

			if (\Config::get('cache.use') === true) {
                $container['cache']['acl_player_' . $this->player['player_id'] .'_roles'] = $roles;
			}
		}
		
		$this->addRoles($roles);
		\Profiler::setTime('ACL::_construct addRoles');
		$container['acl'] = $this;
	}
	
	/**
	 * Sets the context of the ACL to a player
	 * @param array $player
	 */
	public function setPlayer($player) 
	{
		$this->player = $player;
	}
	
	/**
	 * Retrieves the context player
	 * @return array
	 */
	public function getPlayer()
	{
		return $this->player;
	}
	
	/**
	 * Retrieves roles player is linked to
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}
	
	/**
	 * Adds a role to the current context
	 * @param Role $role
	 */
	public function addRole($role)
	{
		array_push($this->roles, $role);
	}
	
	/**
	 * Adds multiple roles to player's context
	 * @param array $roles
	 */
	public function addRoles($roles)
	{
		foreach($roles as $metadata) {
			$this->addRole(new Role($this->container, $metadata));
		}
	}
	
	/**
	 * Valdiates whether player has permission
	 * 
	 * This is case-insensitive
	 * 
	 * @param string $permission
	 * @return boolean
	 */
	public function verify($permission)
	{
		foreach($this->roles as $role) {
			if ($role->hasPermission($permission) || $role->isRoot()) {
				return true;
			}
		}
		
		return false;
	}
		
	/**
	 * Validates whether player has role
	 * 
	 * This is case-insensitive
	 * 
	 * @param string $role
	 * @return boolean
	 */
	public function hasRole($role)
	{
		foreach($this->roles as $r) {
            if ($r->getTitle()== $role || $r->isRoot()) {
                return true;
			}
		}
		
		return false;
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
       /* if (array_key_exists($method, $this->helpers)) {
            $class = $this->helpers[$method];
            return call_user_func_array(array($class, $method), $args);
        }
       */
    }
}