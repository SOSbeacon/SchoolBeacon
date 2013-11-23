<?php
class Webapp_AccountController extends Zend_Controller_Action {
    public function indexAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Account');
        $message = '';
        if ($this->getRequest()->isPost()) {
            $name = trim($this->getRequest()->getParam('name', ''));
            $email = trim($this->getRequest()->getParam('email', ''));
            $currentPassword = $this->getRequest()->getParam('cpassword', '');
            $newPassword = $this->getRequest()->getParam('npassword', '');
            $retypePassword = $this->getRequest()->getParam('rpassword', '');
            $emailValidate = new Zend_Validate_EmailAddress();
            $isValid = true;
            if ($phone->getPassword() != md5($currentPassword)) {
                $isValid = false;
                $message .= '<br /> Current password is wrong.';
            }
            if($email && !$emailValidate->isValid($email)) {
                $isValid = false;
                $message .= '<br /> Email is not valid.';
            }
            if ($newPassword) {
                if (strlen($newPassword) < 6) {
                    $isValid = false;
                    $message .= '<br /> Password can not be shorter than 6 characters.';
                }
                if ($newPassword != $retypePassword) {
                    $isValid = false;
                    $message .= '<br /> Confirm password is not correct .';
                }
            }
            if ($isValid) {
                $account = new Sos_Model_Phone();
                $account->find($phone->getId());
                $account->setName($name);
                $account->setEmail($email);
                if ($newPassword) {
                    $account->setPassword(md5($newPassword));
                }
                $account->save();
                Sos_Service_Functions::updateDefaultContact($account);
                $message .= '<br /> Account updated successfully.';
            }
        }
        $phone = Sos_Service_Functions::webappAuth();
        $this->view->phone = $phone;
        $this->view->message = $message;
    }
    
    public function loginAction() {
        Zend_Layout::getMvcInstance()->assign('title', 'SOSbeacon Login');
        $number = $this->getRequest()->getParam('number', '');
        $password = $this->getRequest()->getParam('password', '');
        $selectPhoneId = $this->_request->getParam('rbSelectPhoneId', 0);
        $selectPhoneId = intval($selectPhoneId);
        $selectPhone = '';
        $message = '';
        if ($this->getRequest()->isPost()) {
            $loginResult = Sos_Service_Functions::loginPhone($number, $password, $selectPhoneId);
            $message = $loginResult['message'];
            $selectPhone = $loginResult['selectPhone'];
        }
        $this->view->message = $message;
        $this->view->selectPhone = $selectPhone;
        $auth = Sos_Service_Functions::webappAuth(false);
        if ($auth->getId()){
            $this->getHelper('Redirector')->gotoUrl('/webapp');
        }
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('webapp'))->clearIdentity();
        $this->getHelper('Redirector')->gotoUrl('/');
    }
}