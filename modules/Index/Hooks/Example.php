<?php

namespace Module\Index\Hooks;

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
		//$this->view->helloWorld .= ' This string was altered by ' . __CLASS__ . '.';
	}
}
