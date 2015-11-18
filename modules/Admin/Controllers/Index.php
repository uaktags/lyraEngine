<?php
	namespaceModule\Admin\Controllers;
	use\Lyra\Controller;
	/** * Index controller */classIndexextendsController{
		private$players;
		publicfunction __construct(){
			\App::useAdminTheme();
			$player=\App::getModel('session');
			$checkPlayer=$player->isLoggedIn() && \Acl::hasRole('administrator');
			\View::set('loggedIn',$checkPlayer);
			if($checkPlayer===false){
				header('Location: Home');
				exit ;
			}
			else{
				$playerID=$player->get('player_id');
				\View::set('player', \App::getModel('Player')->find($playerID));
			}
			$this->players=\App::getModel('adminPlayers');
			$this->loadStats();
		}
		/**     * Default action     * @param $args array     */publicfunction index(array$args=array()){
			\View::setTemplate('Admin.twig');
			\View::set('pageTitle','Admin Panel');
		}
		publicfunction appearance(){
			\View::setTemplate('Admin.twig');
			\View::set('pageTitle','Appearances');
			\View::set('mainContent','Select Themes and stuff');
		}
		publicfunction users(array$args=array()){
			\View::setTemplate('Users.twig');
			if(isset($args[1]) && $args[1]!=null){
				switch($args[1]){
					case 'New':
						\View::setTemplate('NewUser.twig');
						\View::set('pageTitle','New Player');
					break;
					case 'Edit':
						\View::setTemplate('EditUser.twig');
						\View::set('pageTitle','Edit Player');
					break;
				}
			}
			else{
				\View::setTemplate('Users.twig');
				$players=\App::getModel('Player');
				\View::set('playerArray',$players->findAll());
				\View::set('pageTitle','User Manager');
				\View::set('mainContent','See stats, edit users, etc');
			}
		}
		publicfunction config(){
			\View::setTemplate('Admin.twig');
			\View::set('pageTitle','Configuration');
			\View::set('mainContent','See and edit the Site Config');
		}
		publicfunction modules(){
			\View::setTemplate('Admin.twig');
			\View::set('pageTitle','Module Manager');
			\View::set('mainContent','Install/Activate/Deactive/Uninstall Modules');
		}
		publicfunction loadStats(){
			\View::set('numPlayers',$this->players->getTotal());
		}
	}