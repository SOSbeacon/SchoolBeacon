<?php

class Sos_Service_Alert {
    
    private $_type = 0;
    private $_fromPhone; //Sos_Model_Phone
    private $_fromName = '';
    private $_fromEmail = '';
    private $_fromNumber = '';
    private $_alertToken = '';
    private $_newAlertId = '';
    private $_alertGroupId = '';
    private $_alertMessage = '';
    private $_alertMessageLong = '';
    private $_toGroup = 0;
    private $_singleContact = '';
    private $_latitude = '';
    private $_longitude = '';
    private $_logger;
    
    /**
     * type: 0 = alert, 1 = call emergency, 2 = checking-in message
     * toGroup: >0 = to groupId, 0 = all groups, -1 = single contact, -2 = to Family&Friend
     */
    public function __construct($type, Sos_Model_Phone $phone, $alertMessage = '', $alertMessageLong = '', $toGroup = '', $singleContact = '', $latitude = '', $longitude = '') {
        $this->_type = $type;
        if (!$phone->getSetting()) {
            $setting = new Sos_Model_Setting();
            $setting->find($phone->getSettingId());
            $phone->setSetting($setting);
        }
        $this->_fromPhone = $phone;
        $this->_alertMessage = trim($alertMessage);
        $this->_alertMessageLong = trim($alertMessageLong);
        $singleContact = Sos_Service_Functions::stripPhoneNumber($singleContact);
        $this->_singleContact = trim($singleContact);
        $this->_latitude = $latitude;
        $this->_longitude = $longitude;
        $this->_fromNumber = $phone->getNumber();
        $this->_fromName = trim($phone->getName()) ? trim($phone->getName()) : ('Phone number ' . $phone->getNumber());
        $this->_fromEmail = $phone->getEmail();
        if ($toGroup == '' || is_null($toGroup)) { // || ($type == 0) || ($type == 1) to group not 0, alert and call send to defaul group
          $toGroup = $phone->getSetting()->getAlertSendtoGroup(); // get default group
        }
        $this->_toGroup = $toGroup;
        $this->_logger = Sos_Service_Logger::getLogger();
    }
    
    /*
     * Set saved alert group 
     */
    public function setAlertGroup($alertGroupId, $alertToken, $savedAlertId) {
        $this->_alertGroupId = $alertGroupId;
        $this->_alertToken = $alertToken;
        $this->_newAlertId = $savedAlertId;
    }
    
    private function _sendEmailAlert($contacts, $type) {
        $existEmail = array();
        $toNames = array();
        $toEmails = array();
        $emailObjects = array();
        $subject = 'SOSbeacon Alert from ' . $this->_fromName;
        $body = '';
        $message = '';
        $emegencyNumber = $this->_fromPhone->getSetting()->getPanicAlertPhonenummber();
        if ($type == 0 || $type == 1) {
            $body = 'You received an SOSBEACON alert from ' . $this->_fromNumber;
        }
        if ($type == 1) {
            $message =  'EMERGENCY NUMBER CALLED ' . $emegencyNumber;
        }
        if ($type == 2) {
            $subject = 'Checking-In message from ' . $this->_fromName;
            if (trim($this->_fromPhone->getName())) {
                $body = htmlspecialchars($this->_fromName . ' checked in from ' . $this->_fromNumber);
            } else {
                $body = htmlspecialchars($this->_fromName . ' checked in');
            }
            $message = 'Message: ' . ($this->_alertMessageLong ? nl2br(htmlspecialchars($this->_alertMessageLong)) 
                    : htmlspecialchars($this->_alertMessage));
        }
        if ($type == 3) {
            $subject = 'SOSbeacon alert from SOMEONE WHO NEEDS HELP';
            $body = 'You received an SOSBEACON GOOD SAMARITAN alert from "SOMEONE WHO NEEDS HELP"' .
                    '. Alert information: CALLER ' . $this->_fromNumber . ', ' . $this->_fromName;
        }
        foreach ($contacts as $contact) {
            $contactEmail = $contact->getEmail();
            if (!$contactEmail) continue;
            if (!in_array($contactEmail, $existEmail)) { // No duplicate sent mail 
                $existEmail[] = $contactEmail;
            } else {
                continue;
            }
            $toEmails[] = $contactEmail;
            $toNames[] = $contact->getName();
            $emailObjects[] = array(
                'to' => $contactEmail,
                'cid' => $contact->getId()
            );
        }
        if (count($emailObjects)) {
            $emailDrafts = array(
                'fromName' => $this->_fromName,
                'fromEmail' => $this->_fromEmail ? $this->_fromEmail : $this->_fromNumber,
                'subject' => $subject,
                'body' => $body,
                'message' => $message,
                'token' => $this->_alertToken,
                'emailObjects' => $emailObjects
            );
            // save log arguments
            $gearmanModel = new Sos_Model_Gearman();
            $gearmanModel
                ->setType(1) // send email
                ->setStatus(1) // waiting
                ->setArgument(serialize($emailDrafts))
                ->save();
            $emailClientGearman = Sos_Service_Gearman_Manager::getClient();
            $this->_logger->log('>>>>> GEARMAN emails, ID: ' .  $gearmanModel->getId() . ', DATA: ' . serialize($emailDrafts), Zend_Log::INFO);
            ///$emailClientGearman->doBackground('send_alert_email', $gearmanModel->getId());
            $emailClientGearman->doBackground('send_alert_email', serialize($emailDrafts));
            $gearmanResult = '';
            switch($emailClientGearman->returnCode()) {
            case GEARMAN_WORK_DATA:
                $gearmanResult .= "Data: $result\n";
                break;
            case GEARMAN_WORK_STATUS:
                list($numerator, $denominator)= $emailClientGearman->doStatus();
                $gearmanResult .=  "Status: $numerator/$denominator complete\n";
                break;
            case GEARMAN_SUCCESS:
                $gearmanResult .= 'SUCCESS';
                break;
            default:
                $gearmanResult .=  "RET: " . $emailClientGearman->returnCode() . "\n";
                break;
            }
            $this->_logger->log('>>>>> GEARMAN emailClient RESULT: ' . $gearmanResult, Zend_Log::INFO);
        }
    }
    
    private function _sendSmsAlert($contacts, $type) {
        $existPhone = array();
        $smsObjects = array();
        $body = '';
        $message = '';
        $appUrl = Sos_Service_Functions::$appUrl;
        $emegencyNumber = $this->_fromPhone->getSetting()->getPanicAlertPhonenummber();
        if ($type == 0 || $type == 3) {
            if (trim($this->_fromPhone->getName())) {
                $body = $this->_fromName . ' sent an alert from ' . $this->_fromNumber . '. Link';
            } else {
                $body = $this->_fromName . ' sent an alert. Link';
            }
        }
        if ($type == 1) {
            $body = $this->_fromName . ' called emergency ' . $emegencyNumber . '. Link';
            $message = 'EMERGENCY NUMBER CALLED ' . $emegencyNumber;
        }
        if ($type == 2) {
            $body = 'SOSbeacon msg ' . $this->_fromNumber . ' CHECKIN link';
            $message = 'TXT ' . $this->_alertMessage;
        }
        foreach ($contacts as $contact) {
            $contactPhone = $contact->getTextphone();
            if (!$contactPhone) continue;
            if (!in_array($contactPhone, $existPhone)) { // No duplicate sent phone number
                $existPhone[] = $contactPhone;
            } else {
                continue;
            }
            $smsObjects[] = array(
                'to' => $contactPhone,
                'cid' => $contact->getId()
            );
        }
        if (count($smsObjects)) {
            $smsDrafts = array(
                'sender' => $this->_fromPhone->getNumber(),
                'body' => $body,
                'message' => $message,
                'token' => $this->_alertToken,
                'smsObjects' => $smsObjects
            );
            // save log arguments
            $gearmanModel = new Sos_Model_Gearman();
            $gearmanModel
                ->setType(2) // send sms
                ->setStatus(1) // waiting
                ->setArgument(serialize($smsDrafts))
                ->save();
            $smsClientGearman = Sos_Service_Gearman_Manager::getClient();
            $this->_logger->log('>>>>> GEARMAN sms, ID: ' .  $gearmanModel->getId() . ', DATA: ' . serialize($smsDrafts), Zend_Log::INFO);
            //$smsClientGearman->doBackground('send_alert_sms', $gearmanModel->getId());
            $smsClientGearman->doBackground('send_alert_sms', serialize($smsDrafts));
            $gearmanResult = '';
            switch($smsClientGearman->returnCode()) {
            case GEARMAN_WORK_DATA:
                $gearmanResult .= "Data: $result\n";
                break;
            case GEARMAN_WORK_STATUS:
                list($numerator, $denominator)= $smsClientGearman->doStatus();
                $gearmanResult .=  "Status: $numerator/$denominator complete\n";
                break;
            case GEARMAN_SUCCESS:
                $gearmanResult .= 'SUCCESS';
                break;
            default:
                $gearmanResult .=  "RET: " . $smsClientGearman->returnCode() . "\n";
                break;
            }
            $this->_logger->log('>>>>> GEARMAN smsClient RESULT: ' . $gearmanResult, Zend_Log::INFO);
        }
    }
    
    private function _getGoodSamaritans($phoneId, $latitude, $longitude) {
        $phoneMapper = new Sos_Model_PhoneMapper();
        $samaritanContacts = array();
        if ($latitude != ''  && $longitude != '') {
            try {
                $samaritanPhones = $phoneMapper->fetchByLocation($phoneId, $latitude, $longitude);
                if (count($samaritanPhones)) {
                    foreach ($samaritanPhones as $samaritanPhone) {
                        $samaritanContact = new Sos_Model_Contact();
                        $samaritanContact->setTextphone($samaritanPhone->getNumber());
                        $samaritanContact->setEmail($samaritanPhone->getEmail());
                        $samaritanContact->setName($samaritanPhone->getName());
                        $samaritanContacts[] = $samaritanContact;
                    }
                }
            } catch (Zend_Exception $e) {
                $this->_logger->log("getGoodSamaritans(phoneId $phoneId, latitude $latitude, longitude $longitude):" . $e->getMessage(), Zend_Log::ERR);
            }
        }
        return $samaritanContacts;
    }
    
    public function saveAlert() {
        if ($this->_newAlertId) return 0; // alert saved
        $phoneId = $this->_fromPhone->getId();
        $alertGroup = new Sos_Model_Alertloggroup();
        $alertGroupMap = new Sos_Model_AlertloggroupMapper();
        $alertGroupMap->getOneBySession($phoneId, $alertGroup, $this->_toGroup);
        $this->_alertToken = $alertGroup->getToken();
        $this->_alertGroupId = $alertGroup->getId();
        $newAlertId = '';
        $emegencyNumber = $this->_fromPhone->getSetting()->getPanicAlertPhonenummber();
        $alertMessageLog = htmlspecialchars($this->_alertMessage);
        if ($this->_alertMessageLong) $alertMessageLog .= '<hr />' . nl2br(htmlspecialchars($this->_alertMessageLong));
        if ($this->_type == 0) {
            $alertMessageLog = htmlspecialchars($this->_fromName . ' sent an alert from ' . $this->_fromNumber);
        }
        if ($this->_type == 1) {
            $alertMessageLog = 'Emergency call made to ' . $emegencyNumber;
        }
        $alertlogMapper = new Sos_Model_AlertlogMapper();
        $alertlog = new Sos_Model_Alertlog();
        // Find the most recent alert that in the same session ( less than 4 hours old)
        $alertlog->setAlertloggroupId($this->_alertGroupId);
        $alertlog->setMessage($alertMessageLog);
        $alertlog->setCreatedDate(date('Y-m-d H:i:s'));
        $alertlog->setType($this->_type);
        $alertlogMapper->save($alertlog);
        $newAlertId = $alertlog->getId();
        
        // Save location
        $locationMapper = new Sos_Model_LocationMapper;
        $location = new Sos_Model_Location();
        $location->setLatitude($this->_latitude);
        $location->setLongtitude($this->_longitude);
        $location->setAlertlogId($newAlertId);
        $location->setUpdatedDate(date('Y-m-d H:i:s'));
        $locationMapper->save($location);
        // Update check-in data (images, audio) were not set id
        if ($this->_type == 2) {
            $currentTime = date('Y-m-d H:i:s');
            $alertdataMapper = new Sos_Model_AlertdataMapper();
            $where = "(phone_id = $phoneId) AND (alertlog_id is null) AND (TIMESTAMPDIFF(MINUTE, created_date, '$currentTime') <= 15)";
            $arrAlertData = $alertdataMapper->fetchList($where);
            foreach ($arrAlertData as $row) {
                $row->setAlertlogId($newAlertId);
                $alertdataMapper->save($row);
            }
        }
        $this->_logger->log(">>>>> SAVE ALERT INFO, newAlertId:$newAlertId, alertGroupId:" . $this->_alertGroupId, Zend_Log::INFO);
        $this->_newAlertId = $newAlertId;
        return array('alertId' => $newAlertId, 'token' => $this->_alertToken, 'alertGroupId' => $this->_alertGroupId);
    }

    /**
     * Send alert, checkin, call emergency
     */
    public function sendAlert() {
        $this->_logger->log('>>>>> START SENDING ALERT', Zend_Log::INFO);
        $this->_logger->log("From: $this->_fromName - phoneId:" . $this->_fromPhone->getId() . ", to groupId:$this->_toGroup, singleContact:$this->_singleContact, type:$this->_type, message: $this->_alertMessage, long message: $this->_alertMessageLong, location(latitude:$this->_latitude, longitude:$this->_longitude)", Zend_Log::INFO);
        $message = '';
        $success = 'false';
        $alertGroupId = '';
        $response = array();
        $phoneId = $this->_fromPhone->getId();
        $alertMapper = new Sos_Model_AlertlogMapper();
        $db = $alertMapper->getDbTable()->getDefaultAdapter();
        try {
            $isValid = true;
            $group = new Sos_Model_Contactgroup();
            if (intval($this->_toGroup) > 0) {
                $group->find($this->_toGroup);
                if ($group->getPhoneId() != $phoneId)  {
                    $isValid = false;
                    $message = 'To groupId is incorrect';
                }
            }
            if ($isValid) {
                $db->beginTransaction();
                // change: alert save before send $this->saveAlert();
                // Send messages
                $contacts = Sos_Service_Functions::getContactList($phoneId, $this->_toGroup, $this->_singleContact);
                $this->_sendSmsAlert($contacts, $this->_type);
                $this->_sendEmailAlert($contacts, $this->_type);
                if ($this->_type == 1) { // notice good samaritan
                    try {
                        $samaritanContacts = $this->_getGoodSamaritans($phoneId, $this->_latitude, $this->_longitude);
                        $countSamaritan = count($samaritanContacts);
                        $this->_logger->log(">>> SEND ALERT TO GOOD SAMARITAN, to $countSamaritan samaritan(s)", Zend_Log::INFO);
                        if ($countSamaritan) {
                            $this->_sendSmsAlert($samaritanContacts, 3);
                            $this->_sendEmailAlert($samaritanContacts, 3);
                        }
                    } catch (Zend_Exception $sme) {
                        $this->_logger->log('SEND GoodSamaritans ERROR: ' . $sme->getMessage(), Zend_Log::INFO);
                    }
                }
                // Save receiver list
                $existEmails = array();
                $existPhones = array();
                foreach ($contacts as $contact) {
                    $saveEmail = $contact->getEmail();
                    $savePhone = $contact->getTextphone();
                    if (($saveEmail && in_array($saveEmail, $existEmails)) || ($savePhone && in_array($savePhone, $existPhones))) continue;
                    if ($saveEmail)  $existEmails[] = $saveEmail;
                    if ($savePhone) $existPhones[] = $savePhone;
                    $responseContactMapper =  new Sos_Model_ResponseMapper();
                    $responseContact = $responseContactMapper->findByAlertGroup($this->_alertGroupId, $contact->getId());
                    $responseContact->setContactId($contact->getId());
                    $responseContact->setAlertGroupId($this->_alertGroupId);
                    $responseContact->setName($contact->getName());
                    $responseContact->setEmail($saveEmail);
                    $responseContact->setNumber($savePhone);
                    $responseContact->save();    
                }
                $success = 'true';
                $db->commit();
                if ($this->_type == 0 || $this->_type == 1)  $message = 'Alert sent successfully.';
                if ($this->_type == 2)  $message = 'Check-in sent successfully.';
                // clear cache
                Sos_Service_Cache::clear('alert_' . $this->_alertToken);
                Sos_Service_Cache::clearAlertCache($this->_alertToken);
            }
        } catch (Zend_Exception $ex) {
            if (!empty($db)) $db->rollBack();
            $message = $ex->getMessage();
        }
        $response = array(
            'success' => $success, 
            'message' => $message, 
            'alertId' => $this->_newAlertId,
            'alertGroupId' => $alertGroupId, 
            'alertToken' => $this->_alertToken,
            'id' => $this->_newAlertId
        );
        $this->_logger->log("SEND ALERT Response: 'success' => $success, 'message' => $message, 'alertId' => $this->_newAlertId", Zend_Log::INFO);
        return $response;
        
    }
    
    /*
     * Send alert, checkin in background
     */
    public function sendAlertTask() {
        $params = array();
        $params[] = 'token=' . urlencode($this->_alertToken);
        $params[] = 'phoneId=' . urlencode($this->_fromPhone->getId());
        $params[] = 'latitude=' . urlencode($this->_latitude);
        $params[] = 'longitude=' . urlencode($this->_longitude);
        $params[] = 'type=' . urlencode($this->_type);
        $params[] = 'toGroup=' . urlencode($this->_toGroup);
        $params[] = 'singleContact=' . urlencode($this->_singleContact);
        $params[] = 'message=' . urlencode($this->_alertMessage);
        $params[] = 'messageLong=' . urlencode($this->_alertMessageLong);
        $params[] = 'alertGroupId=' . urlencode($this->_alertGroupId);
        $params[] = 'alertToken=' . urlencode($this->_alertToken);
        $params[] = 'newAlertId=' . urlencode($this->_newAlertId);
        $postString = implode('&', $params);
        $this->_logger->log(">>>>> START ALERT TASK, POST: $postString", Zend_Log::INFO);
        try {
            $parts = parse_url(Sos_Service_Functions::$appUrl . '/webapp/checkin/alert-task');
            $fp = fsockopen($parts['host'], $_SERVER['SERVER_PORT'], $errno, $errstr, 300);
            $out = 'POST ' . $parts['path']. " HTTP/1.1\r\n";
            $out.= 'Host: ' . $parts['host']. "\r\n";
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out.= 'Content-Length: ' . strlen($postString)."\r\n";
            $out.= "Connection: Close\r\n\r\n";
            if (isset($postString)) $out.= $postString;
            $fwriteResult = fwrite($fp, $out);
            fclose($fp);
            $this->_logger->log("END ALERT TASK, fwriteResult:$fwriteResult", Zend_Log::INFO);
        } catch (Zend_Exception $e) {
            $this->_logger->log('Exception: ' . $e->getMessage(), Zend_Log::ERR);
        }
    }
    
    /*
     * Validate files upload
     * return "valid" if ok or message if false
     */
    public function validateUploadFiles() {
        $message = 'valid';
        $upload = new Zend_File_Transfer_Adapter_Http();
        $allowExt = implode(',', array_merge(Sos_Service_Functions::$imageExtension, Sos_Service_Functions::$audioExtension));
        $upload->addValidator('Count', false, array('min' => 1, 'max' => 20))
               ->addValidator('Size', false, array('max' => Sos_Service_Functions::$maxFileSize . 'KB'))
               ->addValidator('Extension', false, $allowExt);
        if (!$upload->isValid()) {
            $message = implode(', ', $upload->getMessages());
        }
        return $message;
    }


    public function uploadFile($phoneId, $alertId = '', $alertlogType = 2, $type = '', $alertToken = '') {
        $message = '';
        $newUploadId = 0;
        $resObj = array();
        $successCount = 0;
        $fileCount = 0;
        $totalFileRequest = 0;
        try {
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination(APPLICATION_PATH . '/../tmp');
            $validateMessage = $this->validateUploadFiles();
            $files = $upload->getFileInfo();
            $totalFileRequest = count($files);
            $this->_logger->log("== START UPLOAD FILE, total:" . $totalFileRequest . ", validateMessage:$validateMessage, phoneId:$phoneId, alertId:$alertId, alertlogType:$alertlogType", Zend_Log::INFO);
            if ($validateMessage != 'valid') {
                throw new Zend_Exception('Upload file error: ' . $validateMessage);
            }
            foreach ($files as $file => $info) {
                $fileCount++;
                $ext = pathinfo($info['name']);
                $fileExtension = strtolower($ext['extension']);
                //$message .= $fileCount . '==' . print_r($ext, 1);
                $upload->clearFilters();
                $upload->addFilter('Rename', array('target' => APPLICATION_PATH . '/../tmp/' . $phoneId . '_' . date('Ymdhis') . '_' . $fileCount . '.' . $fileExtension, 'overwrite' => true));
                if (!$info['name']) { // not upload invalid file
                    continue;
                }
                if ($ext['basename']) {
                    $upload->receive($file);
                    $fileName = $upload->getFileName($file, false); //$info['name']; //
                    $tmpPath = $upload->getFileName($file, true); // $tmpPath = $upload->getFileName(null, true);
                    $newPath = '';
                    $url = '';
                    if ($type === '') {
                        if (in_array($fileExtension, Sos_Service_Functions::$imageExtension)) {
                            $type = '0';
                        }
                        if (in_array($fileExtension, Sos_Service_Functions::$audioExtension)) {
                            $type = '1';
                        }
                    }
                    $generatedName = '';
                    $this->_logger->log("File info: basename: " . $ext['basename'] . ", file name: $fileName", Zend_Log::INFO);
                    if ($type === '0') {
                        $generatedName = Sos_Helper_File::generatePath(Sos_Helper_File::getAlertImagePath() . $fileName, false);
                        $newPath = Sos_Helper_File::getAlertImagePath() .  $generatedName;
                        $url = Sos_Helper_File::getAlertImageURL() . $generatedName;
                    }
                    if ($type === '1') {
                        $fullPath = $upload->getFileName($file, true);
                        if ($fileExtension == 'caf') {
                            $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . ".mp3";
                            // convert to mp3: 1. use sndfile-convert to convert CAF to AIF, 2. use lame to conver AIF to MP3
                            $aifFile = substr($fullPath, 0, strlen($fullPath) - 4) . '.aif';
                            $output = array();
                            exec("sndfile-convert $fullPath $aifFile", $output, $returnVal1);
                            exec("lame $aifFile $mp3File", $output, $returnVal2);
                            if ($returnVal1 || $returnVal2) {
                                if (file_exists($fullPath)) unlink($fullPath);
                                if (file_exists($aifFile)) unlink($aifFile);
                                if (file_exists($mp3File)) unlink($mp3File);
                                throw new Zend_Exception('Error while converting audio file');
                            }
                            if (file_exists($aifFile)) unlink($aifFile);
                            if (file_exists($fullPath)) unlink($fullPath);
                        } else if ($fileExtension == 'amr') {
                            $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . ".mp3";
                            // convert to mp3 : 1. use ffmpeg to convert 3gp to AIF, 2. use lame to conver AIF to MP3
                            $rawFile = substr($fullPath, 0, strlen($fullPath) - 4) . ".raw";
                            $aifFile = substr($fullPath, 0, strlen($fullPath) - 4) . ".aif";
                            $output = array();
                            $this->_logger->log("decoder $fullPath $rawFile", Zend_Log::INFO);
                            exec("lame -m m -r -s 8000 --signed --bitwidth 16 $rawFile $mp3File", $output, $returnVal3); // http://packages.medibuntu.org/maverick/libavcodec-extra-52.html
                            exec("/usr/local/sbin/ffmpeg -i $fullPath -ab 8k $mp3File", $output, $returnVal3);
                            if (!file_exists($mp3File)) {
                                if (file_exists($rawFile)) unlink($rawFile);
                                if (file_exists($aifFile)) unlink($aifFile);
                                if (file_exists($mp3File)) unlink($mp3File);
                                throw new Zend_Exception('Error while converting audio file');
                            }
                            if (file_exists($rawFile)) unlink($rawFile);
                            if (file_exists($aifFile)) unlink($aifFile);
                            if (file_exists($fullPath)) unlink($fullPath);
                        }
                        else {
                            $mp3File = substr($fullPath, 0, strlen($fullPath) - 4) . '.' . $fileExtension;
                        }
                        $ext = pathinfo($mp3File);
                        $generatedName = Sos_Helper_File::generatePath(Sos_Helper_File::getAlertAudioPath() . $ext['basename'], false);
                        $newPath = Sos_Helper_File::getAlertAudioPath() . $generatedName;
                        $url = Sos_Helper_File::getAlertAudioURL() . $generatedName;
                        $tmpPath = $mp3File;
                    }
                    $this->_logger->log("Generated file name: $generatedName, type:$type", Zend_Log::INFO);
                    $uploadSuccess = false;
                    if ($newPath && $tmpPath) {
                        try {
                            rename($tmpPath, $newPath);
                            $successCount++;
                            $uploadSuccess = true;
                        } catch (Zend_Exception $uploadException) {
                            $message .= $uploadException->getMessage();
                        }
                    }
                    if ($uploadSuccess && $alertlogType == 2) { // Only for Check-in
                        if (!$alertId) {
                            $alertlogMapper = new Sos_Model_AlertlogMapper();
                            $alertlog = $alertlogMapper->getLastInCheckIn($phoneId);
                            if ($alertlog != null) { // If Check-in Witness data in Session
                                $alertId = $alertlog->id;
                            } else {// IF Check-in Witness data NOT in Session
                                $alertId = null;
                            }
                        }
                    }
                    // End of Only for Check-in
                    if ($uploadSuccess) {
                        $mapper = new Sos_Model_AlertdataMapper();
                        $alertData = new Sos_Model_Alertdata();
                        $alertData->setType($type);
                        $alertData->setPath($url);
                        $alertData->setPhoneId($phoneId);
                        $alertData->setAlertlogId($alertId);
                        $alertData->setCreatedDate(date("Y-m-d H:i:s"));
                        $mapper->save($alertData);
                        $newUploadId = $alertData->getId();
                    }
                    $type = ''; // reset var in loop
                }
            }
        } catch (Zend_Exception $e) {
            $message .= $e->getMessage();
        }
        // Clear cache
        if ($successCount && $alertToken) Sos_Service_Cache::clear('alert_' . $alertToken);
        
        $response = array('message' => $message, 'successCount' => $successCount, 'uploadId' => $newUploadId);
        $this->_logger->log("== UPLOAD RESULT: total:" . $totalFileRequest . ", successCount:$successCount, message:$message, uploadId:$newUploadId", Zend_Log::INFO);
        return $response;
    }
}