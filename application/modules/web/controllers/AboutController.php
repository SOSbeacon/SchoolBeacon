<?php

class Web_AboutController extends Zend_Controller_Action {

    public function indexAction() {
        
    }

    public function featuresAction() {
        
    }

    public function privacyAction() {
        
    }

    public function termsAction() {
        
    }

    public function testimonialsAction() {
        
    }

    public function contactAction() {
        
    }

    public function sendmailAction() {
        //Disable layout 
        $this->_helper->layout()->disableLayout();

        $name = $this->_request->getParam('name');
        $emailFrm = $this->_request->getParam('email');
        $subject = $this->_request->getParam('subject');
        $message = $this->_request->getParam('message');

        //If name, email, subject, message null return false
        if ($name == null || $emailFrm == null || $subject == null || $message == null) {
            echo 'false';
        } else {
            $this->sendMail($name, $emailFrm, $subject, $message);
            //If send successfull
            echo 'true';
        }
    }

    private function sendMail($name, $emailFrm, $subject, $message) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
        $mail = new Sos_Service_ClassMail();
        $mail->setSubject($subject);
        $mail->setAddressTo($config->resources->email->deliveryContact);
        $mail->setAddressName($name);
        $body = "Email is sent from SOSbeacon Contact page.<br/>";
        $body .= "Sender information: $name, $emailFrm.<br/><br/>";
        $body .= "Message: $message";
        $mail->setBody($body);
        //Send mail
        try {
            $mail->sendMail();
        } catch (Exception $ex) {
            $this->logger->log($ex, Zend_Log::ERR);
        }
    }

    public function takeTheTourAction() {
        $this->_helper->layout()->disableLayout();
    }
    
    public function schoolAccountAction() {
        $message = '';
        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            foreach($params as $k => $v) {
                $$k = htmlspecialchars(trim($v));
            }
            $messageContent = "Request form details:
            <h2>School Account Information Form</h2>
            <strong>Name of School:</strong> $schoolName<br />
            <strong>Name of Principal:</strong> $principalName<br />
            <strong>Name of Contact Admin:</strong> $adminName<br />
            <strong>Address:</strong> $address<br />
            <strong>School phone #:</strong> $schoolPhone<br />	
            <strong>Contact phone #:</strong> $contactPhone<br />
            <strong>Contact email address #:</strong> $contactEmail<br />
            <strong>Number of Students:</strong> $studentNo, <strong>Number of classes:</strong> $classNo<br />
            <strong>Student Age range:</strong>   $ageRange, <strong>Grade range:</strong> $gradeRange<br />
            <strong>Is your school (For-profit/Non-profit)?</strong> $profitType <br />
            <strong>Date of School Founding:</strong>  $foundingDate<br />
            <strong>School Website:</strong> $website<br />
            <strong>Have you downloaded the free SOSbeacon app yet?</strong>  $downloadApp <br />
            <strong>What mobile phone # will you use for SOSbeacon service?</strong> $sosPhone<br />
            <strong>Type of mobile phone and carrier:</strong> $sosPhoneCarrier<br />
            <strong>How did you hear about SOSbeacon?:</strong> $howHear<br />
            <strong>Do you have an emergency communications system now?</strong> $haveEmergency<br />
            <strong>If so, what is it?:</strong> $whatEmergency<br />
            <strong>When would you like to get the SOSbeacon emergency communications system started at your school?</strong> $whenGetSos<br />
            <strong>Do you have approval to start this service?</strong>  $approval <br />
            <strong>If NO, what is required to get approval?</strong> $whatApproval<br />
            ";
            $name = 'School Account Information Form';
            $subject = "Email is sent from SOSbeacon School Account Form";
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
            $mail = new Sos_Service_ClassMail();
            $mail->setSubject($subject);
            $mail->setAddressTo($config->resources->email->deliveryContact);
            //$mail->setAddressTo('nguyenngothien@gmail.com'); //test
            $mail->setAddressName($name);
            $mail->setBody($messageContent);
            try {
                $mail->sendMail('', $name, true);
                $message .= 'Your form was submitted to SOSbeacon';
            } catch (Exception $ex) {
                $this->logger->log($ex, Zend_Log::ERR);
            }
        }
        $this->view->message = $message;
    }

}

