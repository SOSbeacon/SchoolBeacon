<?php

class Webapp_IndexController extends Zend_Controller_Action {
    
    private $logger;
    
    public function init() {
        $this->logger = Sos_Service_Logger::getLogger();
    }
    
    public function indexAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Broadcast');
        $checkInGroup = $this->_request->getParam('btCheckInGroup', '');
        $alertGroup = $this->_request->getParam('btAlertGroup', '');
        $message = '';
        if ($checkInGroup) {
            $this->getHelper('Redirector')->gotoUrl('/webapp/checkin');
        }
        if ($alertGroup) {
            $this->getHelper('Redirector')->gotoUrl('/webapp/checkin/index/type/alert');
        }
        $this->view->message = $message;
    }
}