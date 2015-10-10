<?php

namespace Module\Register\Controllers;

use \Lyra\Controller;

/**
 * Index controller
 */
class Index extends Controller
{
    /**
     * Default action
     * @param $args array
     */
    public function index(array $args = array())
    {
        $hooks = array();
        \View::set('registerLayoutHooks', $hooks);
        \View::set('username', (isset($_POST['username']) ? $_POST['username'] : ''));
        \View::set('email', (isset($_POST['email']) ? $_POST['email'] : ''));
        \View::set('password', (isset($_POST['password']) ? $_POST['password'] : ''));
        \View::set('pageTitle', 'Register');
        $auth = $this->app->getModel('player');
        if (isset($_POST['register'])) {
            $insert = array();
            $insert['username'] = $_POST['username'];
            $insert['email'] =	$_POST['email'];
            $insert['password'] = $_POST['password'];
            $insert['confirm_password'] = $_POST['password2'];
            try {
                /* Attempt to register the account */
                $register = $auth->create($insert);
            } catch(\Exception $e) {
                $message = '<strong>You could not be registered:</strong><ul>';

                foreach(unserialize($e->getMessage()) as $prev) {
                    $message .= '<li>' . $prev->getMessage() . '</li>';
                }

                $message .= '</ul>';

                \View::setMessage($message, 'warn');
            }

            /* If the account is active, redirect the user to the login page */
            if (isset($register['active']) && $register['active'] == '1') {
                \View::setMessage('Your accounts was successfully created. You may now log in.', 'success');

            } elseif (isset($register['active']) && $register['active'] == '0') {
                \View::setMessage('Your account has been created, but requires activation.', 'success');
                \View::setTemplate('Index.twig');
            }
        }
    }
}
