<?php

class Webapp_CheckinController extends Zend_Controller_Action {
    
    private $logger;
    
    public function init() {
        $this->logger = Sos_Service_Logger::getLogger();
    }
    
    public function indexAction() {
        $phone = Sos_Service_Functions::webappAuth();
        $type = $this->getRequest()->getParam('type', 'checkin');
        $title = $type == 'checkin' ? 'Check-in Group Message' : 'Send Group <br /> Emergency Alert';
        Zend_Layout::getMvcInstance()->assign('title', $title);
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $groups = $groupMapper->fetchList('phone_id=' . $phone->getId(), 'type ASC');
        $this->view->groups = $groups;
        $message = '';
        $success = false;
        $formMessage = '';
        $formMessageLong = '';
        $formGroupId = 'NONE';
        if ($this->getRequest()->isPost()) {
            $isValid = true;
            $mediafiles = $_FILES;
            $toGroup = $this->getRequest()->getParam('toGroup', '');
            $latitude = $this->getRequest()->getParam('latitude', '');
            $longitude = $this->getRequest()->getParam('longitude', '');
            $checkinMessage = trim($this->getRequest()->getParam('checkinMessage', ''));
            $checkinMessageLong = trim($this->getRequest()->getParam('checkinMessageLong', ''));
            $btCheckin = $this->getRequest()->getParam('btCheckin', '');
            $btUpload = $this->getRequest()->getParam('btUpload', '');
            $singleContact = $this->getRequest()->getParam('singleContact', '');
            if ($btCheckin && ($type == 'checkin') && (!$checkinMessage || ($toGroup === '') || ($toGroup == '-1' && !$singleContact))) {
                $message .= 'Please enter your message and group name / single contact.';
                $isValid = false;
            }
            if ($btUpload && !count($mediafiles)) {
                $message .= 'Please select files to upload.';
                $isValid = false;
            }
            if (intval($toGroup) > 0) {
                $group = new Sos_Model_Contactgroup();
                $group->find($toGroup);
                if ($group->getPhoneId() != $phone->getId())  {
                    $isValid = false;
                    $message .= 'To groupId is incorrect';
                }
            }
            $alertType = $type == 'checkin' ? 2 : 0;
            if  ($isValid) {
                try {
                    $alert = new Sos_Service_Alert($alertType, $phone, $checkinMessage, $checkinMessageLong, $toGroup, $singleContact, $latitude, $longitude);
                    $isFileValid = true;
                    $hasFile = false;
                    if ($mediafiles) {
                        if (count($mediafiles['mediafile']['name']) > 0) {
                            $hasFile = true;
                            $validMessage = $alert->validateUploadFiles();
                            if ($validMessage != 'valid') {
                                $isFileValid = false;
                                $message .= '<br />File upload error: 
                                            The attachment file type is not valid or
                                            file size exceeds the maximum attachment size.
                                            <br />Please try again.';
                                $formMessage = $checkinMessage; // Set message text for user try again
                                $formMessageLong = $checkinMessageLong;
                                $formGroupId = $toGroup;
                            }
                        }
                    }
                    if ($isFileValid) {
                        // start send check-in in background 
                        $alertSaved = $alert->saveAlert();
                        $newAlertId = $alertSaved['alertId'];
                        $alertToken = $alertSaved['token'];
                        $uploadSuccessCount = 0;
                        if ($hasFile) {
                            $uploadResponse = $alert->uploadFile($phone->getId(), $newAlertId, 2, '', $alertToken);
                            $uploadSuccessCount = $uploadResponse['successCount'];
                            $message .= '<br />' . $uploadResponse['message'];
                        }
                        //$alert->sendAlertTask();
                        $alert->sendAlert();
                        $message .= 'Broadcast sent successfully.';
                    }
                } catch (Zend_Exception $e) {
                    $message .= '<br />' . $e->getMessage();
                }
            }
        }
        $this->view->type = $type;
        $this->view->message = $message;
        $this->view->formGroupId = $formGroupId;
        $this->view->formMessage = $formMessage;
        $this->view->formMessageLong = $formMessageLong;
    }
    
    public function getLocationAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $location = $this->getRequest()->getParam('inputLocation', '');
        $result = Sos_Service_Functions::searchLocation($location);
        echo json_encode($result);
    }
    
    public function reviewAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Review');
        $message = '';
        $alertloggroupMapper = new Sos_Model_AlertloggroupMapper();
        $alertloggroupMapper = $alertloggroupMapper->findLastByPhoneId($phone->getId());
        if ($alertloggroupMapper->getId()) {
            $alertloggroupId = $alertloggroupMapper->getId();
            $alertData = new Sos_Model_Alertdata();
            $alertMapper = new Sos_Model_AlertdataMapper();
            $alertDataRows = $alertMapper->findAllByAlertloggroup($alertloggroupId);
            $alertlog = new Sos_Model_Alertlog();
            $alertlogMap = new Sos_Model_AlertlogMapper();
            $alertlogRows = $alertlogMap->getAllAlertlogDataByAlertloggroup($alertloggroupId);
            $maps = $this->_getMaps($alertlogRows);
            $this->view->alertDataRows = $alertDataRows;
            $this->view->maps = $maps;
            $this->view->phoneRows = $phone;
            $this->view->alertlogRows = $alertlogRows;
        } else {
            $message = 'No record found.';
        }
        $this->view->message = $message;
    }
    
    public function _getMaps($alertlogRows) {
        $maps = array();
        foreach ($alertlogRows as $row) {
            $alrid = $row->alertlog_id;
            $location = new Sos_Model_Location();
            $map = $location->fetchListToArray("alertlog_id = $alrid", 'id');
            if ($map) {
                $maps[$alrid] = $map[0];
            }
        }
        return $maps;
    }
    
    public function alertTaskAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $token = $this->_request->getParam('token', false);
        $phoneId = $this->_request->getParam('phoneId', false);
        $latitude = $this->_request->getParam('latitude', '');
        $longitude = $this->_request->getParam('longitude', '');
        $type = $this->_request->getParam('type', false);  // 0 = alert, 1 = call emergency, 2 = checking-in message 		
        $toGroup = $this->_request->getParam('toGroup', ''); // to groupId, 0 = all groups, -1 = single contact
        $singleContact = $this->_request->getParam('singleContact', ''); // singleContact is phone number
        $message = $this->_request->getParam('message', '');
        $messageLong = $this->_request->getParam('messageLong', '');
        $alertGroupId = $this->_request->getParam('alertGroupId', '');
        $alertToken = $this->_request->getParam('alertToken', '');
        $newAlertId = $this->_request->getParam('newAlertId', '');
        $this->logger->log("START CALL ALERT TASK, token=$token,phoneId=$phoneId,type=$type,toGroup=$toGroup,singleContact=$singleContact,latitude=$latitude,longitude=$longitude, message=$message,messageLong=$messageLong", Zend_Log::INFO);
        $phone = new Sos_Model_Phone();
        $phone->find($phoneId);
        $setting = new Sos_Model_Setting();
        $setting->find($phone->getSettingId());
        $phone->setSetting($setting);
        $alert = new Sos_Service_Alert($type, $phone, $message, $messageLong, $toGroup, $singleContact, $latitude, $longitude);
        $alert->setAlertGroup($alertGroupId, $alertToken, $newAlertId);
        $response = $alert->sendAlert();
        $this->logger->log("END CALL ALERT TASK, success:" . $response['success']  . ',message:' . $response['message'] . ',alertId:' . $response['alertId'], Zend_Log::INFO);
    }
    
}