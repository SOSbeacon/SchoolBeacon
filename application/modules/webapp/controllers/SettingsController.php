<?php
class Webapp_SettingsController extends Zend_Controller_Action {
    public function indexAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Settings');
        $message = '';
        if ($this->getRequest()->isPost()) {
            $recordTime = intval($this->getRequest()->getParam('recordTime', 1));
            $defaultGroup = intval($this->getRequest()->getParam('defaultGroup', 0));
            $emergencyNumber = intval($this->getRequest()->getParam('emergencyNumber', 0));
            $receiveGovernment = intval($this->getRequest()->getParam('receiveGovernment', 0));
            $receiveSamaritanStatus = intval($this->getRequest()->getParam('receiveSamaritanStatus', 0));
            $receiveSamaritanRange = intval($this->getRequest()->getParam('receiveSamaritanRange', 0));
            $alertSamaritanStatus = intval($this->getRequest()->getParam('alertSamaritanStatus', 0));
            $alertSamaritanRange = intval($this->getRequest()->getParam('alertSamaritanRange', 0));
            
            $isValid = true;
            if ($recordTime < 1 || $recordTime > 6) {
                $isValid = false;
            }
            if ($receiveSamaritanRange != 0 && $receiveSamaritanRange != 1 && $receiveSamaritanRange != 3
             && $receiveSamaritanRange != 5 && $receiveSamaritanRange != 10 && $receiveSamaritanRange != 20) {
                $isValid = false;
            }
            if ($alertSamaritanRange != 0 && $alertSamaritanRange != 1 && $alertSamaritanRange != 3
             && $alertSamaritanRange != 5 && $alertSamaritanRange != 10 && $alertSamaritanRange != 20) {
                $isValid = false;
            }
            $group = new Sos_Model_Contactgroup();
            $group->find($defaultGroup);
            if ($group->getPhoneId() != $phone->getId()) {
                $isValid = false;
            }
            if ($isValid) {
                $setting = new Sos_Model_Setting();
                $setting->find($phone->getSettingId());
                $setting->setRecordingVoiceDuration($recordTime);
                $setting->setAlertSendtoGroup($defaultGroup);
                $setting->setPanicAlertPhonenummber($emergencyNumber);
                $setting->setIncomingGovernmentAlert($receiveGovernment);
                $setting->setGoodSamaritanStatus($receiveSamaritanStatus);
                $setting->setGoodSamaritanRange($receiveSamaritanRange);
                $setting->setPanicAlertGoodSamaritanStatus($alertSamaritanStatus);
                $setting->setPanicAlertGoodSamaritanRange($alertSamaritanRange);
                $setting->save();
                $message .= 'Settings updated successfully.';
            }
        }
        $phone = Sos_Service_Functions::webappAuth();
        $this->view->phone = $phone;
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $groups = $groupMapper->fetchList('phone_id=' . $phone->getId(), 'type ASC');
        $this->view->groups = $groups;
        $this->view->message = $message;
    }
}