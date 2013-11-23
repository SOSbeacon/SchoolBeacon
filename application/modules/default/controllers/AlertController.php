<?php

require_once 'BaseController.php';

class AlertController extends BaseController {

    private $logger;

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
            ->addActionContext('get', array('xml', 'json'))
            ->addActionContext('post', array('xml', 'json'))
            ->addActionContext('put', array('xml', 'json'))
            ->initContext();
        $this->logger = Sos_Service_Logger::getLogger();
    }

    public function postAction() {
        $token = $this->_request->getParam('token', false);
        $phoneId = $this->_request->getParam('phoneId', false);
        $latitude = $this->_request->getParam('latitude', '');
        $longitude = $this->_request->getParam('longitude', $this->_request->getParam('longtitude', ''));
        $type = $this->_request->getParam('type', false);  // 0 = alert, 1 = call emergency, 2 = checking-in message 		
        $toGroup = $this->_request->getParam('toGroup', ''); // to groupId, 0 = all groups, -1 = single contact
        $singleContact = $this->_request->getParam('singleContact', ''); // singleContact is phone number
        $message = $this->_request->getParam('message', '');
        $messageLong = $this->_request->getParam('messageLong', '');
        $resObj = array();
        $resObj['success'] = 'false';
        try {
            $this->authorizePhone($token, $phoneId);
            $phone = new Sos_Model_Phone();
            $phone->find($phoneId);
            $setting = new Sos_Model_Setting();
            $setting->find($phone->getSettingId());
            $phone->setSetting($setting);
            $alert = new Sos_Service_Alert($type, $phone, $message, $messageLong, $toGroup, $singleContact, $latitude, $longitude);
            $alertSaved = $alert->saveAlert();
            //$alert->sendAlertTask();
            $alert->sendAlert();
            $newAlertId = $alertSaved['alertId'];
            $responseMessage = 'Check-in sent successfully.';
            if ($type == 0 || $type == 1)  $responseMessage = 'Alert sent successfully.';
            $resObj['id'] = $newAlertId;
            $resObj['success'] = "true";
            $resObj['message'] = $responseMessage;
        } catch (Zend_Exception $ex) {
            $this->logger->log('ERROR while sending alert: ' . $ex, Zend_Log::ERR);
            $resObj['message'] = $ex->getMessage();
        }
        $this->view->response = $resObj;
    }

    public function indexAction() {}

    public function getAction() {}
    
    public function putAction() {}

    public function deleteAction() {}

}

