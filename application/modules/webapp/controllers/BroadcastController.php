<?php

class Webapp_BroadcastController extends Zend_Controller_Action {
    
    private $logger;
    private $twilioId;
    private $twilioToken;
    private $broadcastUrl;

    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        header("content-type: text/xml");
    }
    
    public function indexAction() {
        
    }
    
    public function recordAction() {
        $this->_getConfig();
        $this->logger->log('>>>>> TWILIO BROADCAST, start record', Zend_Log::INFO);
        $sid = $this->_request->getParam('AccountSid', '');
        $from = $this->_request->getParam('From', '');
        $to = $this->_request->getParam('To', '');
        $this->logger->log("Params: from=$from, to=$to", Zend_Log::INFO);
        /* Start TwiML */
        $response = new Services_Twilio_Twiml();
        /* Check to make sure that the user has contacts in the database */
        if (($sid == $this->twilioId) && $from) {
        //if ($from) {
            //if (check($from)) {
                $response->record(array(
                    'action' => $this->broadcastUrl . '/broadcast?number=' . urlencode($from)
                ));
                //$response->say('I did not receive a message');
            //} else {
            //    $response->say('You have not registered any contacts');
            //}
        } else {
            $response->say('Request is not valid');
        }
        $this->logger->log('Record response: ' . $response, Zend_Log::INFO);
        print $response;
    }
    
    public function broadcastAction() {
        $this->_getConfig();
        $this->logger->log('>>>>> TWILIO BROADCAST, start broadcast', Zend_Log::INFO);
        // Outgoing Caller ID you have previously validated with Twilio
        $outgoingCaller = '+14157993016';
        $recordingUrl = $this->_request->getParam('RecordingUrl', '');
        $number = $this->_request->getParam('number', '');
        $this->logger->log("Params: recordingUrl=$recordingUrl, number=$number", Zend_Log::INFO);
        // Instantiate a new Twilio Rest Client
        $client = new Services_Twilio($this->twilioId, $this->twilioToken, $this->twilioVersion);
        // @start snippet
        $response = '';
        if (empty($recordingUrl)) {
            // Warn the caller if we didn't find a recording URL
            $response = 'Error: No URL';
        } else if($number) {
            //$contacts = array('15105409446', '15105278898'); // ### for test // 19164755596
            //$contacts = array('5105409446', '84902585979', '84974955353');
            $contacts = array();
            // $contacts = get contact with $number
            // Attempt to retrieve contacts
            if (empty($contacts)) {
                // Warn the caller if we didn't find any contacts
                $response = 'No Contacts could be found';
            } else {
                // Call each contact
                foreach ($contacts as $output) {
                    $this->logger->log('Call contact: ' . $output, Zend_Log::INFO);
                    try {
                        $url = $this->broadcastUrl . '/play?url=' . $recordingUrl;
                        $params = array('IfMachine' => 'Continue');
                        $client->account->calls->create($outgoingCaller, $output, $url, $params);
                    } catch (Zend_Exception $e) {
                        $this->logger->log('Exception: ' . $e->getMessage(), Zend_Log::ERR);
                    }
                }
                $response = 'Your message has been broadcasted';
            }
        }
        // Render TwiML
        $this->logger->log('Broadcast response: ' . $response, Zend_Log::INFO);
        print $response;
    }
    
    public function playAction() {
        $this->_getConfig();
        $this->logger->log('>>>>> TWILIO BROADCAST, start playAction', Zend_Log::INFO);
        $url = $this->_request->getParam('url', '');
        $answeredBy = $this->_request->getParam('AnsweredBy', ''); // human, machine
        if ($url) {
            $response = new Services_Twilio_Twiml();
            if ($answeredBy == 'machine') {
                $response->pause(10);
            }
            $response->say('Hello. You have a broadcast from SOS Beacon');
            $response->play($url); 
            
            $this->logger->log('Play response: ' . $response, Zend_Log::INFO);
            print $response;
        }
    }
    
    public function statusAction() {
        $this->_getConfig();
        $this->logger->log('>>>>> TWILIO BROADCAST, status callback', Zend_Log::INFO);
    }
    
    public function callAction() {
        $this->_getConfig();
        $auth = Sos_Service_Functions::webappAuth(false);
        $this->logger->log('>>>>> TWILIO BROADCAST, start call', Zend_Log::INFO);
        $fromNumber = '14156898484'; // $fromNumber = trim($this->_request->getParam('fromNumber', ''));
        $toNumbers = $this->_request->getParam('toNumbers', array());
        // $toNumbers =  array('9164755596', '5105278898', '84902585979', '84974955353'); // test 
        //$toNumbers =  array('84974955353'); // test
        $alertId = (int) $this->_request->getParam('alertId', '');
        $callbackId = 'twilioCall_' . $alertId . '_' . date('Ymdhis');
        $textMessage = trim($this->_request->getParam('textMessage', ''));
        $audioFile =  trim($this->_request->getParam('audioFile', ''));
        $audioFilePath = $audioFile ? (Sos_Service_Functions::$appUrl . trim($this->_request->getParam('audioFile', ''))) : '';
        $this->logger->log("Params: phoneId: " . $auth->getId() . ",callbackId=$callbackId,alertId=$alertId,fromNumber=$fromNumber,toNumbers=" . implode(',', $toNumbers) .",audioFile=$audioFilePath", Zend_Log::INFO);
        $called = $fromNumber;
        if (($auth->getId()) && $alertId && $textMessage && (count($toNumbers) > 0)) {
            // $called = $this->_request->getParam('called', '');
            $client = new Services_Twilio($this->twilioId, $this->twilioToken, $this->twilioVersion);
            $params = array('IfMachine' => 'Continue');
            foreach ($toNumbers as $toNumber) {
                try {
                    $call = $client->account->calls->create($fromNumber, $toNumber, $this->broadcastUrl . '/callback?callbackId=' . $callbackId . '&number=' . $called . '&toNumber=' . $toNumber . '&textMessage=' . urlencode($textMessage) . '&playUrl=' . urlencode($audioFilePath), $params);
                } catch(Exception $e) {
                    $this->logger->log('Exception: ' . $e->getMessage(), Zend_Log::ERR);
                }
            }
            if ($auth->getEmail()) {
                $this->_sendCallResultEmail($auth->getEmail(), $auth->getName(), $alertId, $toNumbers, $textMessage);
            }
        }
    }
    
    public function callbackAction() {
        $callbackId = trim($this->_request->getParam('callbackId', ''));
        $answeredBy = $this->_request->getParam('AnsweredBy', ''); // human, machine
        if ($callbackId) {
            $cacheTag = 'twilioCall';
            $cacheId = $callbackId . $answeredBy;
            $cache = Sos_Service_Cache::cacheFactory();
            $responseCache = $cache->load($cacheId);
            if ($responseCache === false) {
                $this->_getConfig();
                $this->logger->log('>>>>> TWILIO BROADCAST, start callback', Zend_Log::INFO);
                $playUrl = $this->_request->getParam('playUrl', '');
                $textMessage = trim($this->_request->getParam('textMessage', ''));
                $number = $this->_request->getParam('number', '');
                $toNumber = $this->_request->getParam('toNumber', '');
                $response = new Services_Twilio_Twiml();
                $response->say('Hello, You have a broadcast from SOS Beacon. ');
                if ($answeredBy == 'machine') {
                    $response->pause(2);
                    $response->say('We will read you the email broadcast from SOSbeacon now');
                    $response->pause(1);
                }
                $response->say($textMessage . ' . ');
                if ($playUrl) $response->play($playUrl);
                // $response->dial($toNumber);
                $this->logger->log('Callback response: ' . $response, Zend_Log::INFO);
                $cache->save($response->__toString(), $cacheId, array($cacheTag));
                echo $response;
            } else {
                echo $responseCache;
            }
        }
    }
 
    private function _getConfig() {
        require_once APPLICATION_PATH . '/../library/Twilio.php';
        $this->logger = Sos_Service_Logger::getLogger();
        $this->broadcastUrl = Sos_Service_Functions::$appUrl . '/webapp/broadcast';
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'twilio');
        $this->twilioVersion = $config->api->version;
        $this->twilioId = $config->api->id;
        $this->twilioToken = $config->api->token;
        
    }
    
    private function _sendCallResultEmail($email, $name, $alertId, $numbers, $textMessage) {
        $this->_getConfig();
        $emailContent = 
            'SOSbeacon ROBOCALL service for alert <span style="color:#C00000">' . $alertId . '</span> was requested by you on <br />
            <span style="color:#C00000">' . date('F d, Y \a\t h:i a') . ' (GMT)</span><br /><br />
            The following numbers were called: <br />
            <span style="color:#C00000">' . implode('<br />', $numbers) . '</span><br /><br />
            The following text was delivered <br />
            <span style="color:#C00000">' . $textMessage . '</span>';
        //Send mail
        $mailSend = false;
        $mail = new Sos_Service_ClassMail();
        $subject = 'SOSbeacon ROBOCALL service for alert ' . $alertId . ' was requested by you';
        $mail->setSubject(htmlspecialchars($subject));
        $mail->setAddressTo($email);
        $mail->setAddressName(htmlspecialchars($name));
        $mail->setBody($emailContent);
        try {
            //Save emaillog when send email
            $emaillog = new Sos_Model_Emaillog();
            $emaillogMapper = new Sos_Model_EmaillogMapper();
            $content = 'Email: SOSbeacon ROBOCALL service for alert ' . $alertId . ' ';
            $emaillogMapper->saveEmaillog('SOSbeacon', $email, $content, $emaillog);
            $mail->sendMail('', 'SOSBeacon', true);
            $emaillogMapper->save($emaillog);
            $this->logger->log($content, Zend_Log::INFO);
            $mailSend = true;
        } catch (Exception $ex) {
            $this->logger->log($ex, Zend_Log::ERR);
        }
    }
}