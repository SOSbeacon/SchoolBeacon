<?php

class RestController extends Zend_Controller_Action {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
                ->initContext();
    }

    public function indexAction() {
        $this->_forward('index', 'phones');
    }

}
