<?php

class AlertWorker // extends Sos_Service_Gearman_Worker
{
    protected $_timeout = 10000; // 10 seconds
 
    protected function initRegisterFunctions()
    {
        $this->_registerFunction = array(
            'sendSms' => '_sendSms',
            'sendEmail' => '_sendEmail'
        );
    }
 
    public function _sendSms($job)
    {
        $logger = Sos_Service_Gearman_Manager::getLogger();
        $logger->log('>>>>> SEND SMS IN GEARMAN TASK', Zend_Log::INFO);
        try {
            //$gearmanModel = new Sos_Model_Gearman();
            $job = trim($job);
            //$gearmanModel->find($job);
            //$logger->log("SMS jobId: $jobId " . print_r($gearmanModel->toArray(),1), Zend_Log::INFO);
            //if ($gearmanModel->getId() && ($gearmanModel->getStatus() == 1)
            //        && ($gearmanModel->getType() == 2) && $gearmanModel->getArgument()) {
            //    $gearmanModel->setStatus(2)->save(); // started
            //    $sms = unserialize($gearmanModel->getArgument());
            $sms = unserialize($job);
            $successCount = 0;
            $sender = $sms['sender'];
            $body = $sms['body'];
            $message = $sms['message'];
            $token = $sms['token'];
            $smsObjects = $sms['smsObjects'];
            $appUrl = Sos_Service_Functions::$appUrl;
            $shortLinks = array();
            foreach($smsObjects as $s) {
                try {
                    $smsTo = $s['to'];
                    $contactId = $s['cid'];
                    $alertLink = $appUrl . '/r/' . $contactId . '/' . $token;
                    // short URL
                    $alertLinkShort = $alertLink;
                    // check if short link is created (when one contact have both email and phone then short link has created before)
                    if (in_array($contactId, array_keys($shortLinks))) {
                        $alertLinkShort = $shortLinks[$contactId];
                    } else {
                        $alertLinkShort = Sos_Service_ShortenUrl::shortenYourls($alertLink); // shorten url
                        $shortLinks[$contactId] = $alertLinkShort;
                    }
                    $smsMessage = $body . ' ' . $alertLinkShort . ' ' . $message;
                    if (strlen($smsMessage) > 140) $smsMessage = substr($smsMessage, 0, 140) . '...';
                    Sos_Service_Twilio::sendSMS('415-689-8484', $smsTo, $smsMessage, $sender);
                    $logger->log("Send SMS to $smsTo, message: $smsMessage", Zend_Log::INFO);
                    $successCount++;
                } catch (Exception $e) {
                    $logger->log($e->getMessage(), Zend_Log::ERR);
                }
            }
            $logger->log(">>>>> SEND SMS RESULT, total:" . count($smsObjects) . ", success: $successCount", Zend_Log::INFO);
            //}
        } catch (Zend_Exception $ex) {
            $logger->log($ex->getMessage(), Zend_Log::ERR);
        }
        exit();
    }
 
    public function _sendEmail($job)
    {
        $logger = Sos_Service_Gearman_Manager::getLogger();
        $logger->log('>>>>> SEND EMAILS IN GEARMAN TASK', Zend_Log::INFO);
        try {
            //$gearman = new Sos_Model_Gearman();
            $job = trim($job);
            //$gearman->find($job);
            //$logger->log("Email jobId: $job " . print_r($gearman->toArray(),1), Zend_Log::INFO);
            //if ($gearman->getId() && ($gearman->getStatus() == 1)
            //        && ($gearman->getType() == 1) && $gearman->getArgument()) {
            //    $gearman->setStatus(2)->save(); // started
                //$emails = unserialize($gearman->getArgument()); 
            $emails = unserialize($job);
            $successCount = 0;
            $fromName = $emails['fromName'];
            $fromEmail = $emails['fromEmail'];
            $subject = $emails['subject'];
            $body = $emails['body'];
            $message = $emails['message'];
            $token = $emails['token'];
            $emailObjects = $emails['emailObjects'];
            $appUrl = Sos_Service_Functions::$appUrl;
            foreach ($emailObjects as $e) {
                try {
                    $toEmail = $e['to'];
                    $alertLink = $appUrl . '/r/' . $e['cid'] . '/' . $token;
                    $link  = 'Detail here: <a href="' . $alertLink . '">' . $alertLink . '</a>';
                    $emailBody = $body . '. '. $link . '<br />' . $message;
                    $response = Sos_Service_SendGrid::sendEmails(array(), array(), $subject, $emailBody, '', $fromName, $fromEmail, $toEmail);
                    $returnMessage = (@$response->message == 'success') ? 1 : 0;
                    if ($returnMessage) $successCount++;
                    $logger->log("Send email from $fromEmail to $toEmail, Subject: $subject, body: $emailBody", Zend_Log::INFO);
                    $emaillog = new Sos_Model_Emaillog();
                    $emaillog->setFrom($fromEmail);
                    $emaillog->setTo($toEmail);
                    $emaillog->setMessage($emailBody);
                    $emaillog->setStatus($returnMessage);
                    $emaillog->setCreatedDate(date('Y-m-d H:i:s'));
                    $emaillog->save();
                    if (!$returnMessage && is_array(@$response->errors)) {
                        $errorMessage = implode(',', $response->errors);
                        $logger->log($errorMessage, Zend_Log::ERR);
                    }
                } catch (Exception $e) {
                    $logger->log($e->getMessage(), Zend_Log::ERR);
                }
            }
            $logger->log(">>>>> SEND EMAILS RESULT, total: " . count($emailObjects) . ", success:$successCount", Zend_Log::INFO);
            //}
        } catch (Zend_Exception $ex) {
            $logger->log($ex->getMessage(), Zend_Log::ERR);
        }
        exit();
    }
}