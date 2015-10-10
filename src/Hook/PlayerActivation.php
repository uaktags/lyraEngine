<?php

namespace Lyra\Hook;
use Lyra\Hook;
use Lyra\Container;

/**
 * PlayerActivation
 * @see Library\Hook
 */
class PlayerActivation extends Hook
{
	/**
	 * playerActivation
	 * @param array $data
	 * @return int
	 */
	public function playerActivation($data) 
	{
		/* Does the application have an activation system enabled */
		$configRequireActivation = \Config::get('accounts.requireActivation');
		if ($configRequireActivation) {
			$data['active'] = 0;
				
			if (\Config::get('accounts.emailActivation')) {
				$this->sendActivationLink($data);
			}
		} else {
			return $data['active'] = 1;
		}
		return $data['active'] = 0;
	}
	
	/**
	 * sendActivationLink
	 * Not yet implemented
	 * @param array $data
	 * @todo Finish implementing
	 */
	protected function sendActivationLink($data)
	{
		return;
	}
}
