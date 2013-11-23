<?php

require_once 'BaseController.php';

class UsersController extends BaseController {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
                ->addActionContext('index', array('xml', 'json'))
                ->addActionContext('get', array('xml', 'json'))
                ->addActionContext('post', array('xml', 'json'))
                ->addActionContext('put', array('xml', 'json'))
                ->initContext();
    }

    public function indexAction() {
        
    }

    public function getAction() {
        
    }

    public function postAction() {
        $this->_forward('index', 'phones');
    }

    public function putAction() {
        
    }

    public function deleteAction() {
        
    }

}
