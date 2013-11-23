<?php

class IndexController extends Zend_Controller_Action {

    public function indexAction() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $auth = Sos_Service_Functions::webappAuth(false);
        $this->view->auth = $auth;
    }

}
