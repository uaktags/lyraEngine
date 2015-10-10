<?php

namespace Module\Login\Controllers;

/**
 * Index controller.
 */
class Index extends \Lyra\Controller
{
    /**
     * Default action.
     *
     * @param $args array
     */
    public function index(array $args = array())
    {
       	$error = null;
		$playerModel = \App::getModel('Player');
		$session = \App::getModel('Session');

		if (empty($_POST['username']) || empty($_POST['password'])) {
			$error = 'Please enter your username and password!';
		} else {
			try {
				$player = $playerModel->authenticate($_POST['username'], $_POST['password']);
			} catch (\Exception $e) {
				$error = 'Invalid username and/or password.';

				if (\Config::get('security.login.showInvalidLoginReason')) {
					$error = $e->getMessage();
				}
			}
		}
		if (isset($error)) {
			$session->clear();
			
			$error = '<strong>Sorry, you could not be logged in...</strong><br />' . $error;
			\View::setMessage($error, 'fail');
				
			/* Changed from a header to just grabbing the view itself */
            \View::setTemplate('Index.twig');
			\View::set('credentials', array(
				'username' => (\Config::get('security.login.returnUsernameOnFailure') ? (isset($_POST['username']) ? $_POST['username'] : '') : ''
			)));
		}
    }

	/**
	 * Logout Action
	 * Clears Session information & Redirects to index
	 */
	public function logout(){
		$session = \App::getModel('session');
		$session->clear();
		header('Location: '.\Config::get('site.url') .'/Index');
		exit;
	}
}
