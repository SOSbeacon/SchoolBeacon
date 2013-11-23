<?php

require_once 'BaseController.php';

class SettingController extends BaseController {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
                ->addActionContext('get', array('xml', 'json'))
                ->addActionContext('post', array('xml', 'json'))
                ->addActionContext('put', array('xml', 'json'))
                ->initContext();
    }

    public function indexAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it 
     * should update the server resource state of the resource identified by 
     * the 'id' value.
     */
    public function putAction() {        
        $settingId = $this->_request->getParam('id', false);
        $phoneId = $this->_request->getParam('phoneId', false);
        $token = $this->_request->getParam('token');
        $recordDuration = $this->_request->getParam('recordDuration');
        
        $panicNumber = $this->_request->getParam('emergencyNumber');
        $alertSendtoGroup = $this->_request->getParam('alertSendToGroup');
        $goodSamaritanStatus = $this->_request->getParam('goodSamaritanStatus');
        $goodSamaritanRange = $this->_request->getParam('goodSamaritanRange');
        $incomingGovernmentAlert = $this->_request->getParam('incomingGovernmentAlert');
        $panicStatus = $this->_request->getParam('panicStatus');
        $panicRange = $this->_request->getParam('panicRange');
        
        // $locationDuration = $this->_request->getParam('locationDuration'); // Not use

        $resObj = array();

        if (intval($recordDuration) <= 0) {
            $recordDuration = 1;
        }
        try {
            $this->authorizePhone($token, $phoneId);
            $this->authorizeSetting($token, $settingId);

            $settingMapper = new Sos_Model_SettingMapper();
            $setting = new Sos_Model_Setting();
            $settingMapper->findOneByField('id', $settingId, $setting);

            if ($setting->getId() == $settingId) {
                
                // check $toGroup is valid
                if ($alertSendtoGroup > 0) {
                    $group = new Sos_Model_Contactgroup();
                    $group->find($alertSendtoGroup);
                    if ($group->getPhoneId() != $phoneId) {
                        throw new Zend_Validate_Exception("groupId is incorrect");
                    }
                }
                
                if ($recordDuration != NULL)
                    $setting->setRecordingVoiceDuration($recordDuration);
                //if ($locationDuration != NULL)
                //    $setting->setRecordingLocationReportDuration($locationDuration);
                if ($panicNumber != NULL)
                    $setting->setPanicAlertPhonenummber($panicNumber);

                if ($alertSendtoGroup != NULL)
                    $setting->setAlertSendtoGroup($alertSendtoGroup);

                if ($goodSamaritanStatus != NULL)
                    $setting->setGoodSamaritanStatus($goodSamaritanStatus);

                if ($goodSamaritanRange != NULL)
                    $setting->setGoodSamaritanRange($goodSamaritanRange);

                if ($incomingGovernmentAlert != NULL)
                    $setting->setIncomingGovernmentAlert($incomingGovernmentAlert);

                if ($panicStatus != NULL)
                    $setting->setPanicAlertGoodSamaritanStatus($panicStatus);

                if ($panicRange != NULL)
                    $setting->setPanicAlertGoodSamaritanRange($panicRange);

                $setting->save();

                $resObj['success'] = "true";
                $resObj['data'] = array(
                    'recordDuration' => $setting->getRecordingVoiceDuration(), 
                    'emergencyNumber' => $setting->getPanicAlertPhonenummber(), 
                    'alertSendToGroup' => $setting->getAlertSendtoGroup(), 
                    'goodSamaritanStatus' => $setting->getGoodSamaritanStatus(), 
                    'goodSamaritanRange' => $setting->getGoodSamaritanRange(), 
                    'panicStatus' => $setting->getPanicAlertGoodSamaritanStatus(), 
                    'panicRange' => $setting->getPanicAlertGoodSamaritanRange(), 
                    'incomingGovernmentAlert' => $setting->getIncomingGovernmentAlert()
                );
                $resObj['message'] = 'Settings save successfully';
            } else {  // email already existed
                $resObj['success'] = "false";
                $resObj['message'] = "Can not find setting";
            }
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = "error";
            $resObj['error'] = $ex->getMessage();
        }

        $this->view->response = $resObj;
    }
    
    /**
     * Not use in new API, phone setting get with phone info in phone controller
     * @see PhonesController
     */
    public function getAction() {
       
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
        
        /*
        $settingId = $this->_request->getParam('id', false);
        $token = $this->_request->getParam('token', false);
        $phoneId = $this->_request->getParam('phoneId', false);

        $resObj = array();

        try {
            $this->authorizePhone($token, $phoneId);
            $this->authorizeSetting($token, $settingId);

            $settingMapper = new Sos_Model_SettingMapper();
            $setting = new Sos_Model_Setting();
            $settingMapper->findOneByField('id', $settingId, $setting);

            if ($setting->getId() != NULL) {
                $resObj['success'] = "true";
                $resObj['voice_duration'] = $setting->getRecordingVoiceDuration();
                $resObj['location_duration'] = $setting->getRecordingLocationReportDuration();
                $resObj['panic_number'] = $setting->getPanicAlertPhonenummber();
                $resObj['alert_sendto_group'] = $setting->getAlertSendtoGroup();
                $resObj['good_samaritan_status'] = $setting->getGoodSamaritanStatus();
                $resObj['good_samaritan_range'] = $setting->getGoodSamaritanRange();
                $resObj['incoming_government_alert'] = $setting->getIncomingGovernmentAlert();
                $resObj['panic_alert_good_samaritan_status'] = $setting->getPanicAlertGoodSamaritanStatus();
                $resObj['panic_alert_good_samaritan_range'] = $setting->getPanicAlertGoodSamaritanRange();
            } else {
                $resObj['success'] = "false";
                $resObj['message'] = "can not find setting";
            }
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = "error";
            $resObj['error'] = $ex->getMessage();
        }

        $this->view->response = $resObj; */
    }

    public function postAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }
    
    public function deleteAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

}

