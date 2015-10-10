<?php

namespace Lyra\Hook;
use Lyra\Hook;
use Lyra\Container;

/**
 * OnlinePlayers
 * @see \Lyra\Hook
 */
class OnlinePlayers extends Hook
{
	public function actionBefore()
	{
		// When we have db
		$playerModel = \App::getModel('Player');
		\View::set('PLAYERS_ONLINE', number_format($playerModel->getNumOnline()));
	}
}