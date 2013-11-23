<?php
class Sos_Service_Functions {
    
    public static $appUrl = 'http://sosbeacon.org';
    public static $phoneDevices = array(0 => 'Unknown', 1 => 'Iphone', 2 => 'Android', 3 => 'BlackBerry', 4 => 'Winphone');
    public static $imageExtension = array('jpg', 'jpeg', 'gif', 'png');
    public static $audioExtension = array('mp3', 'amr', 'caf');
    public static $maxFileSize = '4096';
    public static $shortenUrlConfig;
    public static $shortenLogin;
    public static $shortenKey;
        
    public static function shortenUrl($longUrl) {
        if (!self::$shortenKey) {
                self::$shortenUrlConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'bitly');
                self::$shortenLogin = self::$shortenUrlConfig->api->login;
                self::$shortenKey = self::$shortenUrlConfig->api->key;
        }
        $shortUrl = '';
        try {
            $longUrlEncode = urlencode($longUrl);
            $shortenRequest = "http://api.bitly.com/v3/shorten?login=" . self::$shortenLogin . "&apiKey=" . self::$shortenKey ."&longUrl=$longUrlEncode&format=json";
            $shortenResult = file_get_contents($shortenRequest);
            $shortenJson = json_decode($shortenResult);
            if ($shortenJson->status_txt == 'OK') {
                $shortUrl = $shortenJson->data->url;
            }
        } catch (Zend_Exception $e) {}
        if (!$shortUrl) $shortUrl = $longUrl; // if shorten url error then use long url
        $logger = Sos_Service_Logger::getLogger();
        $logger->log("SHORTEN URL, longUrl: $longUrl, shortUrl: $shortUrl", Zend_Log::INFO);
        return $shortUrl;
    }
    
    public static function sendActiveMail($toEmail, $toName, $code) {
        $mail = new Sos_Service_ClassMail();
        $mail->setSubject("SOSbeacon - Phone Activation Required");
        $mail->setAddressTo($toEmail);
        $mail->setAddressName($toName);
        $link = self::$appUrl . "/web/phone/doactive/code/$code";
        $body = "Please DO NOT REPLY to this message - it is an automated email and your reply will not be received.<br/>";
        $body .= "-----------------------------------------------------------<br/>";
        $body .= "Thank you for registering with us (www.SOSbeacon.org).<br/>";
        $body .= "To continue using your phone, you will need to activate your phone by simply clicking here: <br/>";
        $body .= "<a href=\"$link\">$link</a><br/>";
        $body .= "Your activation code: $code";
        $mail->setBody($body);
        try {
            $mail->sendMail();
        } catch (Exception $ex) {
            $logger = Sos_Service_Logger::getLogger();
            $logger->log('Active email: ', Zend_Log::ERR);
        }
    }
    
    public static function sendNewPassword($toNumber, $content) {
        try {
            Sos_Service_Twilio::sendSMS("415-689-8484", $toNumber, $content);
        } catch (Exception $ex) {
            $logger = Sos_Service_Logger::getLogger();
            $logger->log('Send request password: ' . $ex->getMessage(), Zend_Log::ERR);
            throw new Zend_Exception($ex->getMessage());
        }
    }

    public static function webappAuth($redirect = true) {
        static $authPhone;
        $authId = 0;
        try {
            $webappAuthStorage = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('webapp'));
            if(!$webappAuthStorage->hasIdentity()) {
                if ($redirect) {
                    $redirector = new Zend_Controller_Action_Helper_Redirector();
                    $redirector->gotoUrlAndExit('/webapp/account/login');
                }
            }
            $authStorage =  $webappAuthStorage->getStorage()->read();
            $authId = $authStorage['id'];
        } catch (Zend_Exception $e) {}
        if (is_object($authPhone)) if ($authPhone->getId()) return $authPhone;
        $authPhone = new Sos_Model_Phone();
        if ($authId) {
            $authPhone->find($authId);
            if ($authPhone->getId()) {
              $setting = new Sos_Model_Setting();
              $setting->find($authPhone->getSettingId());
              $authPhone->setSetting($setting);
            }
        }
        return $authPhone;
    }
    
    public static function sendActiveSMS($toNumber, $code, $phone = null) {
        $link = self::$appUrl . "/a/$code";
        //## not use because ShortURL service will check URL
        //$link = Sos_Service_Functions::shortenUrl($link);
        $body = "SOSbeacon - Phone Activation Required. Check link: $link . Activation code: $code";
        if ($toNumber != NULL && trim($toNumber) != '') {
            try {
                Sos_Service_Twilio::sendSMS("415-689-8484", $toNumber, $body);
            } catch (Exception $ex) {
                $logger = Sos_Service_Logger::getLogger();
                $imei = $phone ? $phone->getImei() : '';
                $logger->log('Active SMS - IMEI: ' . $imei . ' . ' . $ex->getMessage() . '. SMS: "' . $body . '"' , Zend_Log::ERR);
                throw new Zend_Exception($ex->getMessage());
            }
        }
    }

    public static function addNewPhone(Sos_Model_Phone $phone) {
        $phoneMapper = new Sos_Model_PhoneMapper();
        $settingMapper = new Sos_Model_SettingMapper();
        $setting = new Sos_Model_Setting();
        $settingMapper->save($setting);
        $phone->setSettingId($setting->getId());
        $phone->setLocationId(self::setNewLocationId(0, 0));
        $phoneMapper->save($phone);
        $group = new Sos_Model_Contactgroup();
        $group->createDefaultGroup($phone);
        $phone->setToken(Sos_Helper_Encryption::encode($phone->getId() . time(), 6));
        $phoneMapper->save($phone);
    }

    public static function setNewLocationId($latitude = 0, $longtitude = 0) {
        $loc = new Sos_Model_Location();
        $map = new Sos_Model_LocationMapper();
        $loc->setId(NULL);
        $loc->setLatitude($latitude);
        $loc->setLongtitude($longtitude);
        $loc->setUpdatedDate(date("Y-m-d H:i:s"));
        $map->save($loc);
        return $loc->getId();
    }

    public static function updateDefaultContact($phone) {
        $contactMapper = new Sos_Model_ContactMapper();
        $contactGroupMapper = new Sos_Model_ContactgroupMapper();
        $contactGroup = new Sos_Model_Contactgroup();
        $contactGroups = $contactGroupMapper->findByField('phone_id', $phone->getId(), $contactGroup);
        
        if (count($contactGroups)) {
            foreach ($contactGroups as $g) {
                $gid = $g->getId();
                $defaultContacts = $contactMapper->fetchList('group_id=' . $gid . ' AND type=1');
                if (count($defaultContacts)) {
                    $c = $defaultContacts[0]; // One group have only one default contact
                    $c->setName(ucfirst($phone->getName()) . ' ' . $g->getName());
                    $c->setEmail($phone->getEmail());
                    $c->setTextphone($phone->getNumber());
                    $c->save();
                }
            }
        }
    }

    public static function compressFilesAndSendEmail($alertData, $email, $name, $fileName, $mailBody) {
        $logger = Sos_Service_Logger::getLogger();
        $fileFullName = '';
        if ($fileName) {
            $fileName = 'SOSbeacon-data-files-' . $fileName . '-' . gmdate('YmdHi') . '.zip';
            $files = array();
            $publicPath = realpath(APPLICATION_PATH . '/../public');
            $fileFullName = $publicPath . '/sosdata/compress_files/' . $fileName;
            foreach ($alertData as $rows) {
                foreach ($rows as $k => $item) {
                    if ($item->type == "0" || $item->type == "1") {
                        $fileInZip = str_replace('/sosdata/', 'sosbeacon/', $item->path);
                        $fileInZip = str_replace('//', '/', $fileInZip);
                        $filePath = $publicPath . $item->path;
                        $files[] = array('path' => $filePath, 'zip_path' => $fileInZip);
                    }
                }
            }
            if (count($files)) {
                $zip = new ZipArchive();
                $zip->open($fileFullName, ZIPARCHIVE::OVERWRITE);
                foreach ($files as $file) {
                    $zip->addFile($file['path'], $file['zip_path']);
                }
                $logger->log('Zip file status: ' . $zip->getStatusString(), Zend_Log::INFO);
                $zip->close();
            }
        }
        $mailSend = false;
        //Send mail
        $mail = new Sos_Service_ClassMail();
        $subject = 'Download data sent from SOSbeacon Message Center ' . gmdate('Y-m-d-H-i');
        $mail->setSubject(htmlspecialchars($subject));
        $mail->setAddressTo($email);
        $mail->setAddressName(htmlspecialchars($name));
        $body = $mailBody;
        $mail->setBody($body);
        if ($fileFullName) if (file_exists($fileFullName)) $mail->setAttachment($fileFullName);
        try {
            //Save emaillog when send email
            $emaillog = new Sos_Model_Emaillog();
            $emaillogMapper = new Sos_Model_EmaillogMapper();
            $content = 'SOSbeacon send alert details and files via email';
            $emaillogMapper->saveEmaillog('SOSbeacon', $email, $content, $emaillog);
            $mail->sendMail();
            $emaillogMapper->save($emaillog);
            $logger->log('SOSbeacon send files to ' . $name . ' - ' . $email, Zend_Log::INFO);
            $mailSend = true;
        } catch (Exception $ex) {
            $logger->log($ex, Zend_Log::ERR);
        }
        if ($fileFullName) if (file_exists($fileFullName)) unlink($fileFullName);
        return $mailSend;
    }
    
    /**
     * Find contacts in a gorup
     * If $groupId = 0 : select all contacts of $phone
     * If $groupId = -1 : single contact
     */
    public static function getContactList($phoneId, $groupId = 0, $singleContact = '') {
        $contactMapper = new Sos_Model_ContactMapper();
        $contact = new Sos_Model_Contact();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $result = array();
        if ($groupId >= 0) {
            $result = $contactMapper->getAllContactByPhoneId($phoneId, '-1', $groupId);
        }
        if ($groupId == -1) { // checking in type is single contact
            $result = $contactMapper->findDistinctByField($phoneId, 'textphone', $singleContact, $contact);
            if (count($result) == 0) { // if contact not found
                $contact->setTextphone($singleContact);
                $result[0] = $contact; // Add only number of single contact
            }
            $row = $phoneMapper->getAllPhoneDataByPid($phoneId); // also send to own user when send single contact
            $contact = new Sos_Model_Contact();
            if (is_array($row)) {
                $contact->setEmail($row['email']);
                $contact->setTextphone($row['number']);
            }
            if (is_object($row)) {
                $contact->setEmail($row->email);
                $contact->setTextphone($row->number);
            }
            $result[1] = $contact; // assign "user contact" to list contact
            return $result;
        }
        if ($groupId == -2) { // Family & Friend
            $result = $contactMapper->getContactByGroupTypes($phoneId, array(0, 1));
        }
        if (count($result) == 0) {
            $row = $phoneMapper->getAllPhoneDataByPid($phoneId); // Set vitual contact for user in case there is no contact
            $contact = new Sos_Model_Contact();
            if (is_array($row)) {
                $contact->setEmail($row['email']);
                $contact->setTextphone($row['number']);
            }
            if (is_object($row)) {
                $contact->setEmail($row->email);
                $contact->setTextphone($row->number);
            }
            $result[0] = $contact;
        }
        return $result;
    }
    
    public static function loginPhone($number, $password, $selectPhoneId = 0) {
        $message = '';
        $selectPhone = '';
        if ($number && $password) {
            $phone = new Sos_Model_Phone();
            $phoneMapper = new Sos_Model_PhoneMapper();
            $where = 'status=1 AND number="' . $number . '" AND password="' . md5($password) . '"';
            if ($selectPhoneId) {
                $where .= ' AND id=' . $selectPhoneId;
            }
            $phones = $phone->fetchList($where, 'id DESC');
            $countPhones = count($phones);
            if ($countPhones) {
                if ($countPhones == 1) {
                    $p = $phones[0];
                    $webappAuth = Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session('webapp'))->getStorage();
                    $webappAuth->write(array('id' => $p->getId(), 'number' => $p->getNumber(), 'email' => $p->getEmail(), 'name' => $p->getName()));
                }
                if ($countPhones > 1) {
                    $selectPhone = 
                        '<strong>You have multi phone with same number and password, please select a phone to login.</strong>
                         <table><tr><th>Number</th><th>Name</th><th>Email</th><th>Device</th><th>Created date</th></tr>';
                    foreach($phones as $p) {
                        $selectPhone .= 
                            '<tr><td>
                                <input type="radio" name="rbSelectPhoneId" value="' . $p->getId() . '" />
                                ' . htmlspecialchars($p->getNumber()) . '</td>
                              <td>' . htmlspecialchars($p->getName()) . '</td>
                              <td>' . htmlspecialchars($p->getEmail()) . '</td>
                              <td>' . self::$phoneDevices[$p->getType()] . '</td>
                              <td>' . $p->getCreatedDate() . '</td></tr>';
                    }
                    $selectPhone .= '</table>';
                }
            } else {
                $message = 'Login incorrect, please try again.';
            }
        }  else {
            $message = 'Please enter phone number and password';
        }
        return array('message' => $message, 'selectPhone' => $selectPhone);
    }
    
    public static function showHttpLink($str) {
        $return = htmlspecialchars($str);
        while (strpos($str, "http://") != false) {
            $pos1 = strpos($str, "http://");
            $str = substr($str, $pos1, strlen($str));
            $pos2 = (strpos($str, " ") != false) ? strpos($str, " ") : strlen($str);
            $http = substr($str, 0, $pos2);
            $http = trim($http, '.');
            $httpNew = "<a href='$http'>$http</a>";
            $str = substr($str, $pos2, strlen($str));
            $return = str_replace($http, $httpNew, $return);
        }
        return $return;
    }
    
    public static function autoSetSpace($string, $len) {
        $result = $string;
        if (strlen($string) > $len) {
            $arr = explode(' ', $string);
            foreach ($arr as $key => $val) {
                if (strlen($val) > $len) {
                    $val_space = '';
                    while (strlen($val) > $len) {
                        $val_space .= substr($val, 0, $len - 1) . ' ';
                        $val = str_replace(substr($val, 0, $len - 1), '', $val);
                    }
                    $val_space .= $val;
                    $arr[$key] = $val_space;
                }
            }
            $result = implode(' ', $arr);
        }
        return $result;
    }
    
    public static function isMobileAccess() {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (stristr($agent, 'iphone') || stristr($agent, 'android') || stristr($agent, 'BlackBerry')) {
            return true;
        }
        return false;
    }
    
    public static function setTimeZone($requestTimezone = null) {
        $timezone = 'America/Los_Angeles';
        try {
            $timezoneSession = new Zend_Session_Namespace('userTimezone');
            if ($requestTimezone) {
                $timezoneSession->timezone = $requestTimezone;
            }
            if ($timezoneSession->timezone) {
                $timezone = $timezoneSession->timezone;
            } 
        } catch (Zend_Exception $e) {}
        return $timezone;
    }
    
    public static function convertTimezone($date, $timezone) {
        $dt = new DateTime($date);
        $dt->setTimezone(new DateTimeZone($timezone));
        return $dt->format('M d Y, h:i A');
    }
    
    public static function systemTimeZones($blank = NULL) {
        // Limit
        return array(
            'America/Los_Angeles' => 'America/San Francisco',
            'America/Denver' => 'America/Denver',
            'America/Chicago' => 'America/Chicago',
            'America/New_York' => 'America/New York',
            'America/Sao_Paulo' => 'America/Rio De Janeiro',
            'Atlantic/Reykjavik' => 'Atlantic/Reykjavik',
            'Europe/London' => 'Europe/London',
            'Europe/Zurich' => 'Europe/Zurich',
            'Europe/Athens' => 'Europe/Athens',
            'Europe/Moscow' => 'Europe/Moscow',
            'Asia/Calcutta' => 'Asia/New Delhi',
            'Asia/Ho_Chi_Minh' => 'Asia/Ho Chi Minh',
            'Asia/Hong_Kong' => 'Asia/Hong Kong',
            'Asia/Tokyo' => 'Asia/Tokyo',
            'Pacific/Guam' => 'Pacific/Guam',
            'Pacific/Honolulu' => 'Pacific/Honolulu',
            'America/Anchorage' => 'America/Anchorage',
        );
        /*$requestTime = (int) $_SERVER['REQUEST_TIME'];
        $zonelist = timezone_identifiers_list();
        $zones = $blank ? array('' => '- Select -') : array();
        foreach ($zonelist as $zone) {
        // Because many time zones exist in PHP only for backward compatibility
        // reasons and should not be used, the list is filtered by a regular
        // expression.
        if (preg_match('!^((Africa|America|Antarctica|Arctic|Asia|Atlantic|Australia|Europe|Indian|Pacific)/|UTC$)!', $zone)) {
          $zones[$zone] = str_replace('_', ' ', $zone); // ' : ' . format_date($requestTime, 'custom', 'l, F j, Y - H:i  O', $zone);
        }
        }
        // Sort the translated time zones alphabetically.
        asort($zones);print_r($zones);
        return $zones;*/
    }

    public static function stripPhoneNumber($number) {
        $number = preg_replace('/[^0-9]/', '', $number);
        if ($number) if (strpos($number, '1', 0) === 0) $number = substr($number, 1);
        return $number;
    }
}