<?php

class Web_AlertController extends Zend_Controller_Action {

    private $auth;

    private $logger;

    public function init() {
        $this->logger = Sos_Service_Logger::getLogger();
    }
    
    public function indexAction() {
        $go = $this->_request->getParam('go');
        if ($go != null && $go == 'last') {
            $auth = Sos_Service_Functions::webappAuth(false);
            // Get last token alert by userId
            $alertgroupMap = new Sos_Model_AlertloggroupMapper();
            $alertloggroup = $alertgroupMap->findLastByPhoneId($auth->getId());
            if ($alertloggroup->getToken() != NULL) {
                $this->_helper->getHelper('Redirector')->gotoUrl('/web/alert/list?id=' . $alertloggroup->getToken());
            }
        }
        $this->view->alertRows = $this->getAlertloggroupData();
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

    public function listAction() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        // $this->logger->log("HTTP_USER_AGENT: " . print_r($agent, 1), Zend_Log::INFO);
        // if request by shorten url, skip process
        if ($agent == 'bitlybot') {
            echo 'bitlybot';
            return;
        }
        $token = $this->_request->getParam('id', '');
        $contactId = $this->_request->getParam('contactId', '');
        $layout = $this->_request->getParam('layout', '');
        $requestTimezone = $this->_request->getParam('timezone', '');
        $timezone = Sos_Service_Functions::setTimeZone($requestTimezone);
        if (!$token) return;
        
        $loginId = '';
        $isOwner = false;
        $openStatus = 'Open'; // alert page is open or closed
        if ($layout == 'mobile') $this->_helper->layout()->disableLayout();
        $isMobile = ($layout == 'mobile' || Sos_Service_Functions::isMobileAccess());
        $auth = Sos_Service_Functions::webappAuth(false);
        $authNumber = '';
        if ($auth->getId()) {
            $loginId = $auth->getId();
            $authNumber = $auth->getNumber();
        }
        
        $alertloggroup = $this->getAlertloggroupByToken($token);
        $alertGroupId = $alertloggroup->getId();
        $responseMapper = new Sos_Model_ResponseMapper();
        
        // get receiver and update open count by contact id or owner login
        if ($alertGroupId) {
            $responseContact = new Sos_Model_Response();
            if ($contactId) {
                $responseContact = $responseMapper->findByAlertGroup($alertGroupId, $contactId);
            } else {
                if ($loginId) {
                    $responseContact = $responseMapper->findByAlertGroup($alertGroupId, 0, $authNumber);
                }
            }
            if ($responseContact->getId()) {
                $contactId = $responseContact->getContactId();
                $contactName = htmlspecialchars($responseContact->getName());
                $openCount = $responseContact->getOpenLink() + 1;
                $responseContact->setOpenLink($openCount);
                $responseContact->save();
                $contactHtmlField = '<input type="hidden" id="rcvId" value="' . $contactId .'" /><input type="hidden" id="rcvName" value="' . $contactName .'" />';
                echo $contactHtmlField;
            }
        }
        // Create cache
        $alertCache = false;
        $cacheTag = 'alert_' . $token;
        $cacheId = 'alert_' . $token;
        if ($isMobile) $cacheId .= '_mobile';
        $cacheId .= '_' . str_replace('/', '_', $timezone);
        $cache = Sos_Service_Cache::cacheFactory();
        if (!$loginId) {
            $alertCache = $cache->load($cacheId);
            if ($alertCache !== false) {
                $this->_helper->viewRenderer->setNoRender(true);
                echo $alertCache;
            }
        }
        if ($alertCache === false) { // cache for not login user
            $openStatus = $alertloggroup->getStatus();
            if ($alertGroupId) {
                $alertData = new Sos_Model_Alertdata();
                $alertMapper = new Sos_Model_AlertdataMapper();
                $alertDataRows = $alertMapper->findAllByAlertloggroup($alertGroupId);
                $phone = new Sos_Model_Phone();
                $phoneMap = new Sos_Model_PhoneMapper();
                $alertlog = new Sos_Model_Alertlog();
                $alertlogMap = new Sos_Model_AlertlogMapper();
                $alertlogRows = $alertlogMap->getAllAlertlogDataByAlertloggroup($alertGroupId);
                $maps = $this->_getMaps($alertlogRows);
                $phoneMap->getPhoneByalertLogGroupId($phone, $alertGroupId);
                $isOwner = ($loginId && $loginId == $phone->getId()) ? true : false;
                // Send files to email
                /* Check files exist
                $isFileExist = false;
                foreach ($alertDataRows as $rows) {
                    foreach ($rows as $k => $item) {
                        if ($item->type == '0' || $item->type == '1') {
                            $isFileExist = true;
                            break;
                        }
                    }
                    if ($isFileExist) break;
                }
                 */
                // if  user is owner
                if ($this->_request->isPost()) {
                    if ($phone->getEmail()) {
                        if ($isOwner && $this->_request->getParam('send_files_to_email', '')) {
                            $selectEmailDownload = $this->_request->getParam('downloadEmailOptions', '');
                            if (count($selectEmailDownload)) {
                                $zipFileName = '';
                                if (in_array('4', $selectEmailDownload)) $zipFileName = $loginId . '-' . $alertGroupId;
                                $mailBody = $this->getAlertMailBodyHtml($phone->getName(), $phone->getNumber(), $phone->getEmail(), $alertlogRows, $alertGroupId, $selectEmailDownload);
                                $isMailSent = Sos_Service_Functions::compressFilesAndSendEmail($alertDataRows, $auth->getEmail(), $phone->getName(), $zipFileName, $mailBody);
                                if ($isMailSent) $this->view->message = '<strong>Your phone website data files have been sent to your email address (<em>' . $phone->getEmail() . '</em>) successfully.</strong>';
                            }
                        }
                    } else {
                        $this->view->message = '<strong>There is no email address set for your account, please enter it to receive data files.
                                                <br /><a href="/webapp/account">Click here to update your email account.</a>
                                                </strong>';
                    }
                }
                $this->view->alertDataRows = $alertDataRows;
                $this->view->maps = $maps;
                $this->view->phoneRows = $phone;
                $this->view->alertlogRows = $alertlogRows;
                $this->view->alertLogGroupId = $alertGroupId;
                $this->view->isMobile = $isMobile;
                $this->view->timezone = $timezone;
                $this->view->systemTimezones = Sos_Service_Functions::systemTimeZones();
                $this->view->isOwner = $isOwner;
                $this->view->openStatus = $openStatus;
            }
            $this->view->auth = $auth;
            $this->view->token = $token;
            if (!$loginId && $alertGroupId) {
                $this->_helper->viewRenderer->setNoRender(true);
                $this->render();
                $cache->save($this->getResponse(), $cacheId, array($cacheTag));
            }
        }
    }

    public function alertlistAction() {
        $this->_helper->layout()->disableLayout();
        $phoneId = $this->_request->getParam("pid", 0);
        $token = $this->_request->getParam("token", 0);
        $alertlogroupId = $this->getAlertloggroupIdByToken($token);
        if (isset($alertlogroupId)) {
            $phone = new Sos_Model_Phone();
            $phoneMap = new Sos_Model_PhoneMapper();
            $alertloggroup = new Sos_Model_Alertloggroup();
            $alertloggroupMap = new Sos_Model_AlertloggroupMapper();
            $alertlogRows = null;
            if ($phoneId == 0) {
                $alertlogRows = $this->getAlertloggroupData();
            } else {
                $alertlogRows = $alertloggroupMap->fetchList("phone_id = $phoneId", "id DESC");
            }
            $phoneMap->getPhoneByAlertloggroupId($phone, $alertlogroupId);
            $alertloggroupMap->findOneByField('id', $alertlogroupId, $alertloggroup);
            $this->view->phoneRows = $phone;
            $this->view->alertlogRow = $alertloggroup;
            $this->view->alertlogRows = $alertlogRows;
            $timezone = Sos_Service_Functions::setTimeZone();
            $this->view->timezone = $timezone;
        }
    }

    private function getAlertMailBodyHtml($name, $number, $email, $alertlogRows, $alertGroupId, $selectEmailDownload) {
        $responseMessages = array();
        $responders = array();
        $noResponse = array();
        if (in_array('1', $selectEmailDownload)) { // Get responder messages
            $alertnoteMapper = new Sos_Model_AlertnoteMapper();
            $responseMessages = $alertnoteMapper->fetchList("alertlog_id = $alertGroupId", "id");
        }
            
        if (in_array('2', $selectEmailDownload)) { // Get responder list
            $responses = new Sos_Model_Response();
            $responders = $responses->fetchList("alert_group_id = $alertGroupId AND (open_link>0 OR response_chat>0)");
        }
            
        if (in_array('3', $selectEmailDownload)) { // Get non responder list
            $responses = new Sos_Model_Response();
            $noResponse = $responses->fetchList("alert_group_id = $alertGroupId AND (open_link=0 AND response_chat=0)");
        }
       
        $zendView = new Zend_View();
        $zendView->assign('name', $name);
        $zendView->assign('number', $number);
        $zendView->assign('email', $email);
        $zendView->assign('alertlogRows', $alertlogRows);
        $zendView->assign('responseMessages', $responseMessages);
        $zendView->assign('responders', $responders);
        $zendView->assign('noResponse', $noResponse);
        $zendView->setScriptPath(APPLICATION_PATH . '/modules/web/views/scripts/alert/');
        $bodyText = $zendView->render('alertMailBody.phtml');
        return $bodyText;
    }

    public function listmobileAction() {
        $token = $this->_request->getParam("id");
        if ($token != NULL) {
            $alertGroupId = $this->getAlertloggroupIdByToken($token);
        }
        if (isset($alertGroupId)) {
            $alertData = new Sos_Model_Alertdata();
            $alertMapper = new Sos_Model_AlertdataMapper();
            $alertDataRows = $alertMapper->findAllByAlertloggroup($alertGroupId);
            $alertlog = new Sos_Model_Alertlog();
            $alertlogMap = new Sos_Model_AlertlogMapper();
            $alertlogRows = $alertlogMap->getAllAlertlogDataByAlertloggroup($alertGroupId);
            $maps = $this->_getMaps($alertlogRows);
            $phone = new Sos_Model_Phone();
            $phoneMap = new Sos_Model_PhoneMapper();
            $phoneMap->getPhoneByAlertloggroupId($phone, $alertGroupId);
            $this->view->phoneRows = $phone;
            $this->view->alertDataRows = $alertDataRows;
            $this->view->alertlogRows = $alertlogRows;
            $this->view->maps = $maps;
            $this->view->alertloggroupId = $alertGroupId;
        }
        $svhost = 'http://' . $_SERVER['HTTP_HOST'];
        $this->view->token = $token;
        $this->view->svhost = $svhost;
    }

    public function listsamaritanAction() {
        $phoneId = 38; //$this->_request->getParam("phoneId");
        $latitude = 21.0157; //$this->_request->getParam("latitude");
        $longtitude = 105.805; //$this->_request->getParam("longtitude");
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phones = $phoneMapper->findSamaritanByLatLon($phoneId, $latitude, $longtitude);
        $this->view->Mylatitude = $latitude;
        $this->view->Mylongtitude = $longtitude;
        $this->view->phoneId = $phoneId;
        $this->view->phones = $phones;
    }

    public function latestAction() {
        $token = $this->_request->getParam('token', false);
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $phone = $phoneMapper->getPhoneByToken($token, $phone);
        $alertToken = null;
        if ($phone != null) {
            // find the latest alert id
            $query = "select id, token from alertloggroup 
                      where id = (select max(id) as id from alertloggroup 
                      where phone_id = " . $phone->getId() . ")";
            $result = $phoneMapper->getDbTable()->getAdapter()->query($query)->fetch();
            $alertToken = $result['token'];
        }
        if ($alertToken != NULL) {
            $this->_helper->getHelper('Redirector')
                    ->gotoUrl("web/alert/listmobile/id/$alertToken");
        } else {
            $this->_helper->getHelper('Redirector')->gotoUrl("web/alert/empty");
        }
    }

    /*
     * User response alert via SMS message
     */
    public  function smsResponseAction() {
        $twillioAid = 'AC6825c3d1d47edc3cabf866d484a1f356';
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $smsFrom = $this->_request->getParam('From', '');
        $smsFrom = Sos_Service_Functions::stripPhoneNumber($smsFrom);
        $smsBody = $this->_request->getParam('Body', ''); 
        
        $smsStatus = $this->_request->getParam('SmsStatus', '');
        $accountSid = $this->_request->getParam('AccountSid', '');
        $smsSid = $this->_request->getParam('SmsSid', '');
        $apiVersion = $this->_request->getParam('ApiVersion', '');
        $this->logger->log(">>>>> Twilio SMS response", Zend_Log::INFO);
        $logMessage = "-- smsFrom:$smsFrom,smsBody:$smsBody,smsStatus:$smsStatus,accountSid:$accountSid,smsSid:$smsSid";
        $message = '';
        if ($smsStatus == 'received' && $smsFrom && $accountSid == $twillioAid) {
            //$pregString = "/^OF|[1-9]+[0-9]*|(0|1)(0|1|2|3|4)|\d|\d|\w$/"; // 
            // {Prefix}|{PhoneId}|{Alert Type}|{Group}|{Latitude}|{Longitude}|{Message}
            // ==> change: {Prefix}|{IMEI}|{Alert Type}|{Group}|{Latitude}|{Longitude}|{Message}
            //$pregMatch = preg_split($pregString, $smsBody);
            $isOfflineSms = false;
            if (substr($smsBody, 0, 3) == 'OF|') {
                $smsArr = explode('|', $smsBody);
                if (count($smsArr) >= 7) {
                    $imei = $smsArr[1]; // $phoneId = (int) $smsArr[1];
                    $alertType = (int) $smsArr[2];
                    $groupType = (int) $smsArr[3];
                    $latitude = (float) $smsArr[4];
                    $longitude = (float) $smsArr[5];
                    $alertMessage = trim($smsArr[6]);
                    $logMessage .= "\n-- parsing result: imei=$imei,alertType=$alertType,groupType=$groupType,latitude=$latitude,longitude=$longitude,alertMessage=$alertMessage";
                    if ($imei && $alertType >= 0 && $alertType <=2 && $groupType >= 0 && $groupType <= 4) { // sms send offline alert
                        $isOfflineSms = true;
                        $alertResult = $this->_smsAlertOffline($imei, $alertType, $groupType, $alertMessage, $latitude, $longitude);
                        $message .= $alertResult['message'];
                        $logMessage .= "\n-- SMS type: send offline alert";
                    }
                }
            }
            if (!$isOfflineSms) { // sms alert response
                $logMessage .= "\n-- SMS type: sms alert response";
                $message .= "Received SMS alert response from $smsFrom";
                $this->_smsReplyAlert($smsFrom, $smsBody);
            }
        } else {
            $message .= 'Request is no valid';
        }
        $result = array('message' => $message, 'smsInfo' => $logMessage);
        $this->logger->log($logMessage, Zend_Log::INFO);
        $this->logger->log("-- Response message: $message", Zend_Log::INFO);
        $format = $this->_request->getParam('format', '');
        if ($format == 'json') {
            echo json_encode($result);
        }
    }
    
    /**
     * $alertType: 0=alert, 1=call, 2=checkin
     * $groupType: 0=family, 1=friend, 2=neighborhood, 3=family&friend, 4=all
     */
    private function _smsAlertOffline($imei, $alertType, $groupType, $alertMessage, $latitude, $longitude) {
        $message = '';
        $newAlertId = 0;
        $phone = new Sos_Model_Phone();
        $imei = $phone->getMapper()->getDbTable()->getDefaultAdapter()->quote($imei);
        $phones = $phone->fetchList("imei=$imei", 'id desc');
        if (count($phones) > 0) $phone = $phones[0];
        if ($phone->getId()) {
            $toGroup = null;
            if ($groupType >= 0 && $groupType <= 2) { // Family or Friend or neighborhood
                $group = new Sos_Model_Contactgroup();
                $groups = $group->fetchList('phone_id='.$phone->getId(). " AND type=$groupType");
                if (count($groups) > 0) {
                    $group = $groups[0];
                    $toGroup = $group->getId();
                }
            }
            if($groupType == 3) {
                $toGroup = -2;
            }
            if($groupType == 4) { // all group
                $toGroup = 0; 
            }
            if ($toGroup >= 0 || $toGroup == -2) {
                $alert = new Sos_Service_Alert($alertType, $phone, $alertMessage, '', $toGroup, '', $latitude, $longitude);
                $alertSaved = $alert->saveAlert();
                $alert->sendAlertTask();
                $newAlertId = $alertSaved['alertId'];
                $message .= 'Alert sent successfully';
            } else {
                $message .= 'Group is not valid';
            }
        } else {
            $message .= 'Phone is not valid';
        }
        return array('alertId' => $newAlertId, 'message' => $message);
    }
    
    private function _smsReplyAlert($smsFrom, $smsBody) {
        // Find reply numbers from receivers table and set reply id
        $responses = new Sos_Model_Response();
        $responsesList = $responses->fetchList("number=$smsFrom", 'id DESC');
        if (count($responsesList) > 0) {
            foreach($responsesList as $rp) {
                $rpId = $rp->getId();
                $alertGroupId = $rp->getAlertGroupId();
                $fromName = $smsFrom;
                if ($rp->getName()) $fromName = $rp->getName() . ' - ' .$fromName;
                $fromName .= ' <em>(SMS response)</em>';
                // Add reply SMS message to alert note
                $alertNote = new Sos_Model_Alertnote();
                $alertNote->setAlertlogId($alertGroupId);
                $alertNote->setFrom($fromName);
                $alertNote->setMessage($smsBody);
                $alertNote->setCreatedDate(date('Y-m-d H:i:s'));
                $alertNote->save();
                $alertNoteId = $alertNote->getId();
                if ($alertNoteId) $rp->setReplyId($alertNoteId); // save last chat (alertnote) id to reply id
                // Save chat count
                $chatCount = $rp->getResponseChat() + 1;
                $rp->setResponseChat($chatCount);
                $rp->save();
                // clear all relate alertcache
                Sos_Service_Cache::clear('chat_' . $alertGroupId);
                $this->logger->log("Saved SMS to chat messages, smsFrom: $fromName, alertGroupId:$alertGroupId, responseId:$rpId, alertNoteId:$alertNoteId", Zend_Log::INFO);
            }
          }
    }
    
    public function emptyAction() {}

    public function addnoteAction() {
        $this->_helper->layout()->disableLayout();
        $alertlogId = $this->_request->getParam('alertlogId', 0);
        $contactId = $this->_request->getParam('rcvCid', 0);
        $alertGroupId = $this->_request->getParam('agrId', 0);
        $from = $this->_request->getParam('from', '');
        $message = $this->_request->getParam('message', '');
        if ($alertlogId && $from && $message) {
            $alertnote = new Sos_Model_Alertnote();
            $alertnoteMapper = new Sos_Model_AlertnoteMapper(); 
            $alertnote->setAlertlogId($alertlogId);
            $alertnote->setFrom(htmlspecialchars($from));
            $alertnote->setMessage($message);
            $alertnote->setCreatedDate(date("Y-m-d H:i:s"));
            $alertnoteMapper->save($alertnote);
            // update response chat count
            if ($alertGroupId && $contactId) {
                $responseMapper = new Sos_Model_ResponseMapper();
                $responseContact = $responseMapper->findByAlertGroup($alertGroupId, $contactId);
                if ($responseContact->getId()) { 
                    $chatCount = $responseContact->getResponseChat() + 1;
                    $responseContact->setResponseChat($chatCount);
                    $responseContact->save();
                }
            }
            // clear cache
            Sos_Service_Cache::clear('chat_' . $alertlogId);
            echo 'success';
        } else {
            echo 'error';
        }
    }

    public function loadnoteAction() {
        $this->_helper->layout()->disableLayout();
        $alertlogId = intval($this->_request->getParam('alertlogId', 0));
        $alertlogId = intval($alertlogId);
        $userType = $this->_request->getParam('curUser', '');
        $userRole = $this->_request->getParam('userRole', '');
        $number = $this->_request->getParam('number', '');
        if (!$alertlogId) return;
        
        $timezone = Sos_Service_Functions::setTimeZone();

        $cacheTag = 'chat_' . $alertlogId;
        $cacheId = 'chat_' . $alertlogId . '_' . str_replace('/', '_', $timezone);
        
        $notes = array();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->timezone = $timezone;
        $this->view->number = $number;
        $cache = Sos_Service_Cache::cacheFactory();
        $notesCache = $cache->load($cacheId);
        if ($notesCache === false) {
            $alertnoteMapper = new Sos_Model_AlertnoteMapper();
            $noteList = $alertnoteMapper->fetchList("alertlog_id = $alertlogId", "id");
            /*$db = $alertnoteMapper->getDbTable()->getAdapter();
            $noteAndSmsReply = "
                SELECT n.* FROM alertnote n 
                    WHERE (n.alertlog_id=$alertlogId 
                        OR n.id IN (SELECT an.id FROM alertnote an 
                                        INNER JOIN response r ON an.id=r.reply_id 
                                            WHERE r.alert_group_id=$alertlogId))
                    ORDER BY n.id";
            $noteList = $db->fetchAll($noteAndSmsReply); 
            */
            $this->view->notes = $noteList;
            $this->render();
            $cache->save($this->getResponse(), $cacheId, array($cacheTag));
        } else {
            echo $notesCache;
        }
        // Display receiver list for owner
        $receiversList = '';
        if ($userType == 'owner') {
            // Get responders list
            $responses = new Sos_Model_Response();
            $responseMapper = new Sos_Model_ResponseMapper();
            $db = $responseMapper->getDbTable()->getAdapter();
            $sql = "SELECT r.*, c.voicephone AS voicephone, g.name AS group_name, g.id as gid 
                    FROM response r
                        LEFT JOIN contact c ON r.contact_id=c.id
                        LEFT JOIN contactgroup g ON c.group_id=g.id
                    WHERE alert_group_id = $alertlogId";
            // Get response list
            $responders = $db->fetchAll($sql . " AND (open_link>0 OR response_chat>0)");
            // Get non-response list
            $noResponse = $db->fetchAll($sql . " AND (open_link=0 AND response_chat=0)");
            /*/ Get most recent SMS reply
            $alertnoteMapper = new Sos_Model_AlertnoteMapper();
            $db = $alertnoteMapper->getDbTable()->getAdapter();
            $smsSql = "SELECT n.* FROM alertnote n INNER JOIN response r ON n.id=r.reply_id
                       WHERE r.alert_group_id=$alertlogId
                       ORDER BY n.id DESC";
            $smsReply = $db->fetchAll($smsSql);
            $this->view->smsReply = $smsReply; */
            
            $receiversView = new Zend_View();
            $receiversView->assign('timezone', $timezone);
            $receiversView->assign('receivers', $responders);
            $receiversView->assign('noResponse', $noResponse);
            $receiversView->assign('number', $number);
            $receiversView->assign('alertId', $alertlogId);
            $receiversView->assign('userRole', $userRole);
            //$receiversView->assign('smsReply', $smsReply);
            
            $viewPaths = $this->view->getScriptPaths();
            $receiversView->setScriptPath($viewPaths[0] . '/alert/');
            $receiversList .= $receiversView->render('receivers.phtml');
        }
        echo $receiversList;
    }

    public function deletenoteAction() {
        $this->_helper->layout()->disableLayout();
        $alertlogId = $this->_request->getParam('alertlogId');
        $noteId = $this->_request->getParam('noteId');
        $alertnote = new Sos_Model_Alertnote();
        $alertnoteMapper = new Sos_Model_AlertnoteMapper();
        if ($noteId != null && $alertlogId != null) {
            $rowDel = $alertnoteMapper->deleteByField('id', $noteId);
            if ($rowDel > 0) {
                echo 'success';
            } else {
                echo 'error';
            }
        }
        // clear cache
        Sos_Service_Cache::clear('chat_' . $alertlogId);
    }

    public function saveAction() {
        $id = $this->_request->getParam("id");
        $status = $this->_request->getParam("status");
        $alert = new Sos_Model_Alertloggroup();
        $alertMapper = new Sos_Model_AlertloggroupMapper();
        $alertMapper->findOneByField('id', $id, $alert);
        $alert->setStatus($status);
        $alertMapper->save($alert);
        // clear cache
        Sos_Service_Cache::clear('alert_' . $alert->getToken());
    }

    public function locationAction() {
        $this->_helper->layout()->disableLayout();
        $alertId = $this->_request->getParam('alertId');
        $alertlogMapper = new Sos_Model_AlertlogMapper();
        $alertlogs = $alertlogMapper->findByField('alertloggroup_id', $alertId, null);
        $where = null;
        $alertlogIds = array();
        foreach ($alertlogs as $row) {
            $alertlogIds[] = $row->getId();
        }
        if (count($alertlogIds) > 0) {
            $alertlogIds = implode(',', $alertlogIds);
            $where = "alertlog_id IN ($alertlogIds)";
        }
        $location = new Sos_Model_Location();
        $paginator = $location->fetchListToPaginator($where, 'id');
        $paginator->setItemCountPerPage(1);
        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(4);
        $this->view->paginator = $paginator;
    }

    public function errorAction() {
        error_log("SMS Status: " . $_POST['SmsStatus']);
    }

    public function notfoundAction() {
        error_log("SMS Status: " . $_POST['SmsStatus']);
    }

    private function getAlertloggroupData() {
        $rows = Array();
        $alertLoggroup = new Sos_Model_Alertloggroup();
        $alertLoggroupMapper = new Sos_Model_AlertloggroupMapper();
        $phoneMap = new Sos_Model_PhoneMapper();
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) return array();
        $phoneId = $auth->getId();
        $where = ($phoneId == null) ? "phone_id = null" : "phone_id = $phoneId";
        $alertRows = $alertLoggroupMapper->fetchList($where, "id DESC");
        foreach ($alertRows as $row) {
            $log = new Sos_Model_Alertloggroup();
            $log->setId($row->getId());
            $log->setPhoneId($this->getPhoneNumber($row->getPhoneId()));
            $log->setStatus($row->getStatus());
            $log->setToken($row->getToken());
            $log->setCreatedDate($row->getCreatedDate());
            $log->setLastUpdated($row->getLastUpdated());
            $rows[] = $log;
        }
        return $rows;
    }

    private function getPhoneNumber($phoneId) {
        $phoneMap = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $row = $phoneMap->findOneByField('id', $phoneId, $phone);
        return $row->getNumber();
    }

    private function getAlertloggroupIdByToken($token) {
        $log = new Sos_Model_Alertloggroup();
        $mapper = new Sos_Model_AlertloggroupMapper();
        $mapper->findOneByField('token', $token, $log);
        return $log->getId();
    }
    
    private function getAlertloggroupByToken($token) {
        $log = new Sos_Model_Alertloggroup();
        $mapper = new Sos_Model_AlertloggroupMapper();
        $mapper->findOneByField('token', $token, $log);
        return $log;
    }   
}