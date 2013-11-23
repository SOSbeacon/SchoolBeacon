<?php

class Web_UsersController extends Zend_Controller_Action {

    public function registerAction() {
        $this->view->error = "We are still beta testing and are not accepting new accounts right now.";
        $this->view->response = '';
        $this->view->form = '';
    }

    private function setNewLocationId($latitude = 0, $longtitude = 0) {
        $loc = new Sos_Model_Location();
        $map = new Sos_Model_LocationMapper();
        $loc->setId(NULL);
        $loc->setLatitude($latitude);
        $loc->setLongtitude($longtitude);
        $loc->setUpdatedDate(date("Y-m-d H:i:s"));
        $map->save($loc);

        return $loc->getId();
    }

    public function loginAction() {
        $auth = Sos_Service_Functions::webappAuth(false);
        if ($auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl('/web/alert/index/');
        }
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $regMsg = new Zend_Session_Namespace('msg');
        $form = new Sos_Form_UserLogin();
        $user = $this->_request->getParam('user', false);
        $password = $this->_request->getParam('password', false);
        $remember = $this->_request->getParam('chkRemember', false);
        $selectPhoneId = $this->_request->getParam('rbSelectPhoneId', 0);
        $selectPhoneId = intval($selectPhoneId);
        $selectPhone = '';
        $resObject = array();        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST) && $user && $password) {
                $loginResult = Sos_Service_Functions::loginPhone($user, $password, $selectPhoneId);
                $resObject['message'] = $loginResult['message'];
                $selectPhone = $loginResult['selectPhone'];
                //Login successfull, App auto redirect to Review Phone History page
                $auth = Sos_Service_Functions::webappAuth(false);
                if ($auth->getId()){
                    $alertloggroupMapper = new Sos_Model_AlertloggroupMapper();
                    $alertloggroup = $alertloggroupMapper->findLastByPhoneId($auth->getId());
                    if ($alertloggroup->getId() != NULL) {
                        $this->_helper->getHelper('Redirector')->gotoUrl('/web/alert/list?id=' . $alertloggroup->getToken());
                    } else {
                        $this->_helper->getHelper('Redirector')->gotoUrl('/web/alert/index');
                    }
                }
            } else {
                $resObject['message'] = 'Phone number or password is not valid';
            }
        }
        $this->view->msg = $regMsg->msg;
        $this->view->response = $resObject;
        $this->view->selectPhone = $selectPhone;
        $this->view->form = $form;
        //Clear Register message
        $regMsg->unsetAll();
    }

    public function loginstatusAction() {
        $this->_helper->layout()->disableLayout();
        $auth = Sos_Service_Functions::webappAuth(false);
        $this->view->auth = $auth;
    }

    public function forgotAction() {
        $number = trim($this->_request->getParam('number', ''));
        // $email = trim($this->_request->getParam('email', '')); Now send password to phone SMS
        $resObject = array();
        if ($this->_request->isPost()) {
            if ($number) { //if ($number && $email) {
                $phone = new Sos_Model_Phone();
                $phoneMapper = new Sos_Model_PhoneMapper();
                $where = 'status=1 AND number="' . $number . '" ';
                $phones = $phone->fetchList($where, 'id DESC');
                $countPhones = count($phones);
                if ($countPhones) {
                    $phone = $phones[0];
                    // Reset password
                    $newPass = $phoneMapper->genPassword();
                    $toName = $phone->getName() ? $phone->getName() : $phone->getNumber();
                    //$mail = new Sos_Service_ClassMail();
                    //$mail->setSubject("SOSbeacon - Your login details here");
                    //$mail->setAddressTo($phone->getEmail());
                    //$mail->setAddressName($toName);
                    $link = "http://" . $_SERVER['HTTP_HOST'] . "/web/users/login";
                    $body = "Dear " . $toName . ", ";
                    //$body = "You have requested to get your password on SOSbeacon because you have forgotten your password.<br/><br/>";
                    //$body .= "Your login is Phone number: " . $phone->getNumber() . ', ';
                    $body .= "Your password is: " . $newPass . "";
                    //$body .= "To login your account, go to this page: $link";
                    //$body .= "<a href=\"$link\">$link</a><br/><br/>";
                    $body .= " . All the best, SOSbeacon";
                    //$mail->setBody($body);
                    try {
                    //    $mail->sendMail(); //Send mail
                        Sos_Service_Functions::sendNewPassword($number, $body);
                        $phone->setPassword(md5($newPass)); // save if send email successfully
                        $phone->save();
                    } catch (Exception $ex) {
                        $resObject['message'] = 'Error: SMS text message was not sent.';
                    }
                    $resObject['message'] = '<h2>Your new password has been sent to you by SMS text message. You will now be returned to where you were before.</h2>';
                    
                } else {
                    $resObject['message'] = 'You have not entered a phone number that we recognize, or your phone number has not been activated or you have not set a password in settings in your SOSbeacon app on your mobile phone. Please try again.';
                }
            } else {
                $resObject['message'] = 'Please enter your phone number.';
            }
        }
        $this->view->resObject = $resObject;
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('webapp'))->clearIdentity();
        $this->_helper->getHelper('Redirector')->gotoUrl('/web/users/login/');
    }

    //Check exist phone number
    private function phoneNumExist($phoneNum) {
        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $phoneMap->findOneByField('number', $phoneNum, $phone);
        if ($phone->getId() != NULL) {
            return 1;
        } else {
            return 0;
        }
    }

    public function unsubscribeAction() {
        $email = trim($this->_request->getParam('m', ''));
        $token = trim($this->_request->getParam('token', ''));
        $message = '';
        $success = false;
        if ($email && $token) {
            $email = mysql_escape_string($email);
            $phone = new Sos_Model_Phone();
            $mapper = new Sos_Model_PhoneMapper();
            $phones = $mapper->findByField('email', $email, $phone);
            if (count($phones)) {
                foreach($phones as $p) {
                    if ($p->getId()) {
                        if (($p->getSubscribe()) && md5('sos' . $p->getEmail()) == $token) {
                            $p->setSubscribe('0')->save();
                            $success = true;
                        }
                    }
                }
            }
        }
        if ($success) {
            $message = 'Unsubscribe success, you have been removed from SOSbeacon newsletter mailing list.';
        } else {
            $message = 'Request is not valid. Or your email has been unsubscribed';
        }
        $this->view->message = $message;
    }
    
    public function schoolAdminsAction() {}
    
    public function samaritanMapAction() {}
    
    public function infoAction() {}
    
}
