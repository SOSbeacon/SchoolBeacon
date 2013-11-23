<?php

class Sos_Plugin_Acl extends Zend_Controller_Plugin_Abstract{

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// set up acl

		$acl = new Zend_Acl();
		// add the roles

		$acl->addRole(new Zend_Acl_Role('guest'));
		$acl->addRole(new Zend_Acl_Role('users'), 'guest');
		$acl->addRole(new Zend_Acl_Role('administrator'));


		// add the resources
		$acl->add(new Zend_Acl_Resource('users'));
		$acl->add(new Zend_Acl_Resource('alert'));		
		$acl->add(new Zend_Acl_Resource('admin'));
		
		// set up the access rules
		$acl->allow(null, array('users'));

		// a guest can only read content and login
		$acl->allow('guest', null, array('login', 'register','forgot'));
		$acl->allow('guest', 'users', array('forgot'));
		$acl->allow('guest', 'admin', array('index','login'));

		// fetch the current user
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			if( isset($identity->mgr_user_id) && $identity->mgr_user_id>0)
			$role = 'administrator';
			else
			{
				$type = $identity->user_type_id;
				if ($type == 1) {
					$role = 'to';
				} else {
					$role = 'hotel';
				}
			}
		}else{
			$role = 'guest';
		}

		$controller = $request->controller;
		$action = $request->action;

		//error_log("role:".$role." controller is :".$controller." action is:".$action." allowed:".$acl->isAllowed($role, $controller, $action));

		if (!$acl->isAllowed($role, $controller, $action)) {
			error_log("not allowed");
			if ($role == 'guest') {
				$request->setControllerName('users');
				$request->setActionName('login');
			} //else {
				//$request->setControllerName('error');
				//$request->setActionName('noauth');
			//}
		}
	}
}

?>