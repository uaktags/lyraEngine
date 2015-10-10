<?php

namespace Module\DevTools\Controllers;

use \Lyra\Controller;

/**
 * Index controller
 * INCOMPLETE PORT FROM EZ2.0!!!
 */
class Index extends Controller
{
    public function __construct(){
        $player = \App::getModel('session');
        $checkPlayer = $player->isLoggedIn();
        \View::set('loggedIn', $checkPlayer);
        if ($checkPlayer === false ){
            header('Location: ' . \Config::get('site.url') . '/Index');
            exit;
        }
    }
    /**
     * Page title
     * @var string
     */
    protected $title = 'Developer Tools';

    /**
     * Default action
     */
    public function index()
    {
        \View::set('pageTitle', "Dev Tools");
        $this->aclTest();
    }

    public function rebuildSettings()
    {
        $settings = \App::getModel('setting');
        $settings->buildCache();
        $settings->updateConfig();
        $this->view->setMessage( "Rebuilt!", 'success');
        \View::setTemplate('DevTools.twig');
    }

    /**
     * aclTest
     */
    public function aclTest()
    {
        $aclTest = new \Acl;
        $stack = array();
        //$aclTest::setPlayer(3); Explicitly writing a PlayerId to test does work. ACL::Constructor is culprit.

        $stack = array(
            'is_root'	=> $this->cBoolStr($aclTest::hasRole('root')),
            'can_Admin_Administrators' => $this->cBoolStr($aclTest::verify('canAdminAdmins')),
            'can_Admin_Players' => $this->cBoolStr($aclTest::verify('canAdminPlayers'))
        );

        $this->view->set('acl_stack', $stack);
    }

    private function cBoolStr($bool)
    {
        return ($bool ? 'True' : 'False');
    }

    /**
     * setPassword
     */
    public function setpassword()
    {
        if ($_POST['username'] && $_POST['password']) {
            $playerModel = \App::getModel('Player');
            $playerModel->safeMode = false;

            $player = $playerModel->find($_POST['username'], 'username');
            if ($player !== false) {
                $id = $player['player_id'];
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

                $playerModel->save(array('player_id' => $id, 'password' => $password));
                \View::setMessage('Old Password Has: ' . $player['password'], 'info');
                \View::setMessage('New Password Set: ' . $password, 'success');
            } else{
                \View::setMessage('Username Not Found', 'warn');
            }
            \View::setTemplate('DevTools.twig');
        }

        $this->index();
    }
}
