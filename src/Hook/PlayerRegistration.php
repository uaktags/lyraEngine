<?php

namespace Lyra\Hook;
use Lyra\Hook;
use Lyra\Container;

/**
 * PlayerRegistration
 * @see Library\Hook
 */
class PlayerRegistration extends Hook
{

	/**
	 * playerRegistration
	 * @param array $data
	 * @todo Isnt implemented yet
	*/
	public function playerRegistration($data) 
	{
		$roles = \App::getModel('PlayerRole');
		$memberRole = \App::getModel('Role');
		$roleID = $memberRole->getRoleByName('member');
		$roles->addRole($data['player_id'], $roleID);
	}
	
	/**
	 * playerLogin
	 * @param array $data
	*/
	public function playerLogin($data) 
	{
		$session = \App::getModel('Session');
        $auth = \App::getModel('Auth');
        $session->clear();
        $auth->setLastActive($data['username']);
		$session->set('player_id', $data['player_id']);
		$session->set('hash', $session->generateSignature());
		$session->set('lastActive', time());
		header('Location: Home');
		exit;
	}
	
}
