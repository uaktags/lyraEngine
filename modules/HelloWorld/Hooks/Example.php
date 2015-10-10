<?php

namespace Module\HelloWorld\Hooks;

/**
 * Example plugin
 */
class Example extends \Lyra\Hook
{
	/**
	 * Implementation of the actionAfter hook
	 */
	public function actionAfter()
	{
		\View::set('helloWorld', \View::get('helloWorld') . ' This does not work.');
	}
}
