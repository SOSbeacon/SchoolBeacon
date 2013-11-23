<?php

require_once 'BaseController.php';

define('SUCCESS', 1);
define('ERROR', 2);
define('NEW_ACCOUNT', 3);
define('ACCOUNT_NEW_NUMBER', 4);
define('ACCOUNT_NEW_IMEI', 5);
define('ACCOUNT_NOT_ACTIVATED', 6);

class PhonesController extends BaseController {
    
    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch
            ->addActionContext('index', array('xml', 'json'))
            ->addActionContext('get', array('xml', 'json'))
            ->addActionContext('post', array('xml', 'json'))
            ->addActionContext('put', array('xml', 'json'))
            ->initContext();
    }

    public function indexAction() {
        /*/ import database
        $phoneMapper = new Sos_Model_PhoneMapper();
        $db = $phoneMapper->getDbTable()->getDefaultAdapter();
        $this->_helper->layout()->disableLayout();
        $sql = 'SELECT
            p.name AS name,
            p.number AS number,
            p.type AS type,
            p.imei AS imei,
            u.email AS email, 
            u.created_date AS cd,
            u.modified_date AS md 
            
            FROM user_tmp u INNER JOIN phone_tmp p ON u.id = p.user_id';
        $temp = $db->fetchAll($sql);
        //print_r($temp);
        foreach($temp as $t) {
        
            $phone = new Sos_Model_Phone();
            $phone->setName($t['name']);
            $phone->setNumber($t['number']);
            $phone->setType($t['type']);
            $phone->setImei($t['imei']);
            $phone->setEmail($t['email']);
            $phone->setCreatedDate($t['cd']);
            $phone->setModifiedDate($t['md']);
            Sos_Service_Functions::addNewPhone($phone);
        } */
        
    }
    
    /**
     * Get phone infor by IMEI
     */
    public function getAction() {
        // Get phone infor by imei, number and password (optional)
        $imei = $this->_request->getParam('id', ''); // Get id url
        $phoneNumber = trim($this->_request->getParam('number', ''));
        $phoneInfo = trim($this->_request->getParam('phoneInfo', ''));
        // $password = trim($this->_request->getParam('password', ''));
        $phoneType = $this->_request->getParam('phoneType', 0); // 0: unknown, 1: iphone, 2: android, 3: blackberry, 4: winphone
        
        $responseCode = 0;
        $message = '';
        $restObj = array();
        
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        
        $query = 'imei="' . $imei . '"';
        
        $phones = $phoneMapper->fetchList($query, 'id DESC'); // Get all phones have common IMEI, sort by newest first
        
        if (count($phones) >= 1) {
            if ($phoneNumber) { // search phone have $phoneNumber
                foreach ($phones as $p) {
                    if ($p->getNumber() == $phoneNumber) {
                        $phone = $p;
                        break;
                    }
                }
            }
            if (!$phone->getId()) { // if not found, get newest phone
                $phone = $phones[0];
            }
            
            /* $isLoginValid = false;
            $regNumber = $phone->getNumber();
            $regPassword = $phone->getPassword();
            if ($regNumber && $regPassword) { // if phone has password then check password is valid
                if ($phoneNumber && $password) {
                    if (($regNumber == $phoneNumber)  && ($regPassword == md5($password))) {
                        $isLoginValid = true;
                    } else {
                        $message .= ' Phone number or password are wrong.';
                        $responseCode = ERROR; // Unauthorized 
                    }
                } else {
                    $message .= ' Please enter phone number and password.';
                    $responseCode = ERROR; // Unauthorized 
                }                    
            } else { // phone has not password then return true
                $isLoginValid = true;
            } */
            // Get phone infor if login is valid
            //if ($isLoginValid) {
            if ($phone->getStatus()) {
                // Save new token for phone each time user login
                $phone->setToken(Sos_Helper_Encryption::encode($phone->getId() . time(), 6));
                if ($phoneType) {
                    $phone->setType($phoneType);
                }
                $phoneMapper->save($phone);
                // Count contact
                $contactMapper = new Sos_Model_ContactMapper();
                $phoneContactCount =  $contactMapper->countContactsByPhoneId($phone->getId(), '0');
                $restObj['countContact'] = $phoneContactCount;
                $responseCode = SUCCESS; // OK
                $this->_setRestPhoneInfor($restObj, $phone);
                $message .= ' Login success.';
            } else { 
                $message .= '  Please click on link in SMS text message to activate this phone number.';
                $responseCode = ACCOUNT_NOT_ACTIVATED; // Forbiden : Phone is not activated
                $this->_setRestPhoneInfor($restObj, $phone);
            }
            if ($phone->getId() && $phoneInfo) { // save phone informations
                $phone->setPhoneInfo($phoneInfo);
                $phone->save();
            }
            ///}
        } else {
            $message .= ' New device.';
            $responseCode = NEW_ACCOUNT; // Not found
        }
        $restObj['responseCode'] = $responseCode;
        $restObj['message'] = $message;
        $this->view->response = $restObj;
    }
    
    /**
     * Phone register, update
     */
    public function postAction() {
        // Register new phone with imei and number
        $imei = trim($this->_request->getParam('imei', ''));
        $phoneNumber = trim($this->_request->getParam('number', ''));
        $phoneNumber = Sos_Service_Functions::stripPhoneNumber($phoneNumber);
        $phoneInfo = trim($this->_request->getParam('phoneInfo', ''));
        $phoneType = $this->_request->getParam('phoneType', 0); // 0: unknown, 1: iphone, 2: android, 3: blackberry, 4: winphone
        $action = $this->_request->getParam('do', ''); // request: NEW = new register, UPDATE = update exist account
        //[format=json, imei=000000000000111, number=84111222, phoneType=2, do=UPDATE]
        $restObj = array();
        $responseCode = 0;
        $message = '';
        
        if ($imei && $phoneNumber) {
            $phone = new Sos_Model_Phone();
            $phoneMapper = new Sos_Model_PhoneMapper();
                      
            $query = 'imei="' . $imei . '" AND number="' . $phoneNumber . '"';
            $phones = $phoneMapper->fetchList($query, 'id DESC');
            
            if (count($phones)) { // IMEI and number are exist
                $this->_forward('get', null, null, array('id' => $imei, 'number' => $phoneNumber, 'phoneType' => $phoneType));
            } else { // IMEI is new or number is new
                try {
                    $numberOrImeiExist = false;
                    // Check if number is exist
                    $phonesSameNumber = $phoneMapper->fetchList('number="' . $phoneNumber . '" AND status=1', 'id DESC');
                    if (count($phonesSameNumber)) {
                        $phone = $phonesSameNumber[0];
                        $numberOrImeiExist = true;
                        if (!$action) {
                            $message .= ' This  number is registered to another phone device.  Do you want to set up a new SOSbeacon account for this new device  or assign the new phone to the current SOSbeacon phone account?';
                            $responseCode = ACCOUNT_NEW_IMEI;
                        }
                    } else {
                        // Check if imei is exist
                        $phonesSameImei = $phoneMapper->fetchList('imei="' . $imei . '" AND status=1', 'id DESC');
                        if (count($phonesSameImei)) {
                            $phone = $phonesSameImei[0];
                            $numberOrImeiExist = true;
                            if (!$action) {
                                $message .= ' This phone is registered to another number. Do you want to assign the current SOSbeacon phone account to this new number or set up a new SOSbeacon account for this new phone number?';
                                $responseCode = ACCOUNT_NEW_NUMBER;
                            }
                        }
                    }
                    $db = $phoneMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    if ($action == 'NEW' || !$numberOrImeiExist) {
                        // Create new phone
                        $phone = new Sos_Model_Phone();
                        $phone->setNumber($phoneNumber);
                        $phone->setCreatedDate(date('Y-m-d H:i:s'));
                        $phone->setModifiedDate(date('Y-m-d H:i:s'));
                        $phone->setImei($imei);
                        $phone->setType($phoneType);
                        $phone->setPhoneInfo($phoneInfo);
                        Sos_Service_Functions::addNewPhone($phone);
                        Sos_Service_Functions::sendActiveSMS($phoneNumber, $phone->getToken(), $phone);
                        $message .= ' Your new account has been registered.  Please click on the link in the SMS text message  we are sending you to activate and confirm this phone number.';
                        $this->_setRestPhoneInfor($restObj, $phone);
                        $responseCode = ACCOUNT_NOT_ACTIVATED; // Created OK
                    }
                    if ($action == 'UPDATE') { // update number or imei
                        $phone->setModifiedDate(date('Y-m-d H:i:s'));
                        $phone->setStatus('0');
                        $newToken = Sos_Helper_Encryption::encode($phone->getId() . time(), 6);
                        $phone->setToken($newToken);
                        $phone->setType($phoneType);
                        $phone->setPhoneInfo($phoneInfo);
                        $phone->save();
                        $this->_createActivated($phone, 1, $phoneNumber, $imei);
                        Sos_Service_Functions::sendActiveSMS($phoneNumber, $phone->getToken(), $phone);
                        $this->_setRestPhoneInfor($restObj, $phone);
                        $message .= ' Please click on link in SMS text message to activate this phone number.';
                        $responseCode = ACCOUNT_NOT_ACTIVATED;
                    }
                    $db->commit();
                } catch (Zend_Exception $ex) {
                    $db->rollBack();
                    $message  = ' ' . $ex->getMessage();
                    $responseCode = ERROR; // Internal server error
                }
            }
        } else {
            $responseCode = ERROR; // Bad request : missing parameters
            if (!$imei) {
                $message .= ' Phone device IMEI not recognized.';
            }
            if (!$phoneNumber) {
                $message .= ' Phone number is required.';
            }
        }
        $restObj['responseCode'] = $responseCode;
        $restObj['message'] = $message;
        $this->view->response = $restObj;
    }

    public function putAction() {
        $phoneId = trim($this->_request->getParam('id', ''));
        $imei = trim($this->_request->getParam('imei', ''));
        
        $requestType = $this->_request->getParam('do', ''); // update
        $token = $this->_request->getParam('token', '');
        $phoneNumber = trim($this->_request->getParam('number', ''));
        $password = trim($this->_request->getParam('password', ''));
        $name = trim($this->_request->getParam('name', ''));
        $email = trim($this->_request->getParam('email', ''));

        $restObj = array();
        $responseCode = 0;
        $message = '';
        
        if ($requestType == 'UPDATE') {
            // Validate data, if data has post
            $isRequestValid = true;
            $emailValidate = new Zend_Validate_EmailAddress();
            if ($email && !$emailValidate->isValid($email)) { // if has email, it must valid, if email is empty then skip (email is not required)
                $isRequestValid = false;
                $message .= ' Email address is not valid.';
            }
            if ($password && strlen($password) < 6) { // If have set password, it must be over 6 characters 
                $isRequestValid = false;
                $message .= ' Password must be at least 6 characters long.';
            }

            if ($isRequestValid) {
                $phone = new Sos_Model_Phone();
                $phoneMapper = new Sos_Model_PhoneMapper();
                $phoneMapper->find($phoneId, $phone);
                if ($phone->getId() && ($phone->getImei() == $imei) && ($phone->getToken() == $token)) {
                    try {
                        $db = $phoneMapper->getDbTable()->getDefaultAdapter();
                        $db->beginTransaction();
                        $phone->setName($name);
                        $phone->setEmail($email);
                        if ($password) {
                            $phone->setPassword(md5($password));
                        }
                        $phone->setModifiedDate(date('Y-m-d H:i:s'));
                        Sos_Service_Functions::updateDefaultContact($phone);
                        $phoneMapper->save($phone);
                        $message .= ' Phone has been updated successfully.';
                        $this->_setRestPhoneInfor($restObj, $phone);
                        $responseCode = SUCCESS; // OK
                        $db->commit();
                        
                    } catch (Zend_Exception $ex) {
                        $db->rollBack();
                        $message = ' ' . $ex->getMessage();
                        $responseCode = ERROR; // Internal server error
                    }
                } else {
                    $message .= ' Request is not valid.';
                    $responseCode = ERROR;
                }
            } else {
                $responseCode = ERROR;
            }
        }
        /*/ If request forgot password  ## Not use           
        if ($requestType == 'REQUEST_PASSWORD') {
            $phone = new Sos_Model_Phone();
            $phoneMapper = new Sos_Model_PhoneMapper();
            $phoneMapper->findByField('imei', $imei, $phone);
            if ($phone->getId() && $phone->getPassword()) {
                try {
                    $db = $phoneMapper->getDbTable()->getDefaultAdapter();
                    $db->beginTransaction();
                    $randomPassword = $phoneMapper->genPassword();
                    $passwordContent = 'Your login phone number is: ' . $phone->getNumber();
                    $passwordContent .= ' / Your password is: ' . $randomPassword . ' / ';
                    $passwordContent .= 'All the best, SOSbeacon';
                    Sos_Service_Functions::sendNewPassword($phone->getNumber(), $passwordContent);
                    $phone->setPassword(md5($randomPassword));
                    $phone->save();
                    $message = ' Your new password has been created and sent to number ' . $phone->getNumber() . ' , please check your SMS to get your password.';
                    $db->commit();
                    $responseCode = 200; // OK
                } catch (Zend_Exception $ex) {
                    $db->rollBack();
                    $message = $ex->getMessage();
                    $responseCode = 500; // Server error
                }
            }
        } */   

        $restObj['responseCode'] = $responseCode;
        $restObj['message'] = $message;
        $this->view->response = $restObj;
    }
    
    /**
     * Set phone return values
     * not return phone password
     * @param type $restObj
     * @param type $phone 
     */
    private function _setRestPhoneInfor(&$restObj, $phone) {
        $restObj['id'] = $phone->getId();
        $restObj['imei'] = $phone->getImei();
        $restObj['number'] = $phone->getNumber();
        $restObj['password'] = $phone->getPassword();
        $restObj['email'] = strval($phone->getEmail());
        $restObj['name'] = strval($phone->getName());
        $restObj['token'] = $phone->getToken();
        $restObj['phoneType'] = $phone->getType();
        $restObj['phoneStatus'] = $phone->getStatus() ? '1' : '0';
        $restObj['createdDate'] = $phone->getCreatedDate();
        $restObj['modifiedDate'] = strval($phone->getModifiedDate());
        $restObj['settingId'] = $phone->getSettingId();
        $restObj['locationId'] = $phone->getLocationId();
        
        /*
        $restObj['address'] = '';
        $restObj['username'] = strval($phone->getName()); // old api
        $restObj['phonename'] = strval($phone->getName()); //old api 
        $restObj['phone_id'] = $phone->getId();
        $restObj['phone_status'] = $phone->getStatus() ? '1' : '0'; */
        
        // Get phone settings
        $this->_setRestSettingInfor($restObj, $phone);
    }

    private function _setRestSettingInfor(&$restObj, Sos_Model_Phone $phone) {
        $mapper = new Sos_Model_SettingMapper();
        $setting = new Sos_Model_Setting();
        $settingId = $phone->getSettingId();
        $mapper->findOneByField('id', $settingId, $setting);
        if ($setting->getId()) {
            $toGroupId = $setting->getAlertSendtoGroup();
            if (!$toGroupId) { // Get default group (Family) if it is not set
                $group = new Sos_Model_Contactgroup();
                $groups = $group->fetchList('phone_id=' . $phone->getId() . ' AND type=0', 'type');
                if (count($groups)) {
                    $defaultGroup = $groups[0];
                    $toGroupId = $defaultGroup->getId();
                    $setting->setAlertSendtoGroup($toGroupId);
                    $setting->save();
                }
            }
            $restObj['emergencyNumber'] = $setting->getPanicAlertPhonenummber();
            $restObj['recordDuration'] = $setting->getRecordingVoiceDuration();
            $restObj['alertSendToGroup'] = $toGroupId;
            $restObj['goodSamaritanStatus'] = $setting->getGoodSamaritanStatus();
            $restObj['goodSamaritanRange'] = $setting->getGoodSamaritanRange();
            $restObj['panicStatus'] = $setting->getPanicAlertGoodSamaritanStatus();
            $restObj['panicRange'] = $setting->getPanicAlertGoodSamaritanRange();
            $restObj['incomingGovernmentAlert'] = $setting->getIncomingGovernmentAlert();
        }
        /*
        $restObj['voice_duration'] = $setting->getRecordingVoiceDuration();
        $restObj['location_duration'] = $setting->getRecordingLocationReportDuration();
        $restObj['alert_sendto_group'] = $setting->getAlertSendtoGroup();
        $restObj['good_samaritan_status'] = $setting->getGoodSamaritanStatus();
        $restObj['good_samaritan_range'] = $setting->getGoodSamaritanRange();
        $restObj['incoming_government_alert'] = $setting->getIncomingGovernmentAlert();
        $restObj['panic_alert_good_samaritan_status'] = $setting->getPanicAlertGoodSamaritanStatus();
        $restObj['panic_alert_good_samaritan_range'] = $setting->getPanicAlertGoodSamaritanRange(); */
    }

    private function _createActivated(Sos_Model_Phone $phone, $action, $newNumber, $newImei) {
        $activate = new Sos_Model_Activate();
        $activate->setPhoneId($phone->getId());
        $activate->setNumber($phone->getNumber());
        $activate->setAction($action); // 1: update, 2: new,  0: done
        $activate->setImei($phone->getImei());
        $activate->setToken($phone->getToken());
        if ($phone->getNumber() != $newNumber) {
            $activate->setNewNumber($newNumber);
        }
        if ($phone->getImei() != $newImei) {
            $activate->setNewImei($newImei);
        }
        $activate->save();
    }
    
    private function isPhoneNumberExist($number) {
        if (!$number) {
            return false;
        }
        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $phoneSameNumberList = $phoneMap->findByField('number', $number, $phone, 1);
        if (count($phoneSameNumberList)) {
            return true;
        }
        return false;
    }

    private function isEmailExist($email) {
        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $phoneMap->findOneByField('email', $email, $phone);
        if ($phone->getId() != NULL) {
            return true;
        }
        return false;
    }

    public function deleteAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }
}