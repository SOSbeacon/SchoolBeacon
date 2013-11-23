<?php

class MailController extends Zend_Controller_Action {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');

        $options = $bootstrap->getOption('resources');

        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))->initContext();
        $contextSwitch->addActionContext('post', array('xml', 'json'))->initContext();
    }

    public function indexAction() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
        $username = $config->resources->email->account;
        $password = $config->resources->email->password;
        $senderName = $config->resources->email->name;
        $deliveryContact = $config->resources->email->deliveryContact;
        $server = $config->resources->email->server;
        $port = $config->resources->email->port;
        $sMTPSecure = $config->resources->email->SMTPSecure;
        $resObj['success'] = "true";
        $resObj['email'] = $username;
        $resObj['password'] = $password;
        $resObj['delivery'] = $deliveryContact;
        $resObj['server'] = $server;
        $resObj['port'] = $port;
        $resObj['SMTPSecure'] = $sMTPSecure;
        $this->view->response = $resObj;
    }

    public function postAction() {
        $this->_helper->layout()->disableLayout();
        
        $phoneId = trim($this->_request->getParam('phoneId', ''));
        $imei = trim($this->_request->getParam('imei', ''));
        $token = $this->_request->getParam('token', '');
        
        $emails = trim($this->_request->getParam('emails', '')); // emails spit by ","
        $subject = trim($this->_request->getParam('subject', ''));
        $mailMessage = trim($this->_request->getParam('message', ''));
        $type = $this->_request->getParam('type', ''); // 1 = contact, 2 = tell friends
        
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $phoneMapper->findOneByField('token', $token, $phone);
        
        $success = 'false';
        $message = '';
        
        if ($type == 1) { // if contact then get contact email
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
            $emails .= ',' . $config->resources->email->deliveryContact;
        }
        
        $errorMessage = '';
        if (($phone->getId() == $phoneId) && ($phone->getImei() == $imei) && $type && $emails && $subject && $mailMessage) {
            $fromName = $phone->getName();
            $fromEmail = $phone->getEmail();
            $fromPhone = $phone->getNumber();
            
            $from = 'Phone number ' . $fromPhone;
            if ($fromName) {
                $fromPhone = $fromName . ' - ' . $from;
                $from = $fromPhone;
            }
            if ($fromEmail) {
                $from = $from . ' - ' . $fromEmail;
            }
            
            $subject = '[From ' . $from. '] ' . $subject . '';
            $mailMessage = 'Sender information: ' . $from. '. Message: ' . $mailMessage . '';
            
            $emailArray = explode(',', $emails);
            if ($emailArray) {
                $successCount = 0;
                foreach ($emailArray as $email) {
                    $email = trim($email);
                    if ($email) {
                        $emailValidate = new Zend_Validate_EmailAddress();
                        if ($emailValidate->isValid($email)) {
                            $mail = new Sos_Service_ClassMail();
                            $mail->setSubject($subject);
                            $mail->setAddressName('');
                            $mail->setAddressTo($email);
                            $mail->setBody($mailMessage);
                            try {
                                $mail->sendMail();
                                $successCount ++;
                            } catch (Exception $ex) {
                                $errorMessage .= ' ' . $ex->getMessage();
                            }
                        } else {
                            $errorMessage .=  ' ' . $email . ' is not valid. ';
                        }
                    }
                }
                if ($successCount >= 1) {
                    $message = 'Your message has been sent.';
                    $success = 'true';
                } else {
                    $message = $errorMessage;
                }
            } else {
                $message = 'Email is required.';
            }
        } else {
            $message = 'Request is not valid.';
        }
        
        $resObj['success'] = $success;
        $resObj['message'] = $message;
        $resObj['error'] = $errorMessage;
        $this->view->response = $resObj;
    }
}
