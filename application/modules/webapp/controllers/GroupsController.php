<?php

define('PHONE_INVALID', 'Phone number is not valid.');
define('CONTACT_EMPTY', 'Your contact record is empty or has no phone number or email address.');

class Webapp_GroupsController extends Zend_Controller_Action {
    
    public function indexAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Contact Groups');
        $phoneId = $phone->getId();
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $group = new Sos_Model_Contactgroup();
        $contact = new Sos_Model_Contact();
        $message = '';
        if ($this->getRequest()->isPost()) {
            $editGroupId = trim($this->getRequest()->getParam('editGroupId', ''));
            $deleteGroupId = trim($this->getRequest()->getParam('deleteGroupId', ''));
            $editGroupId = intval($editGroupId);
            $deleteGroupId = intval($deleteGroupId);
            if ($deleteGroupId) {
                $group->find($deleteGroupId);
                if ($group->getId()) {
                    if ($group->getType() > 2) {
                        $group->delete('id=' . $deleteGroupId);
                        $message = 'Group deleted successfully';
                    } else {
                        if ($group->getType() == 2) {
                            $group->getMapper()->deleteNWGroup($group);
                            $message = 'All contacts in this group deleted successfully';
                        } else {
                            $message = 'Cannot delete default group.';
                        }
                    }
                } else {
                    $message = 'Group is not exist.';
                }
            } else {
                $editGroupName = trim($this->getRequest()->getParam('editGroupName', ''));
                $response = $groupMapper->saveGroup($editGroupName, $phoneId, $editGroupId);
                $message = $response['message'];
            }
        }
        $phoneGroups = $groupMapper->getGroups($phoneId);  
        $this->view->message = $message;
        $this->view->groups = $phoneGroups;
    }
    
    public function contactsAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Contact List');
        $message = '';
        $contacts = array();
        $joinContacts = array();
        $phoneId = $phone->getId();
        $allowJoin = false;
        $groupId = $this->getRequest()->getParam('groupId', '');
        $group = new Sos_Model_Contactgroup();
        $contact = new Sos_Model_Contact();
        $group->find($groupId);
        if ($groupId && $group->getId() && ($group->getPhoneId() == $phone->getId())) {
            if ($this->getRequest()->isPost()) {
                $editId = $this->getRequest()->getParam('editId', '');
                $deleteId = $this->getRequest()->getParam('deleteId', '');
                $doUnjoin = $this->getRequest()->getParam('doUnjoin', '');
                $editId = intval($editId);
                $deleteId = intval($deleteId);
                $name = trim($this->getRequest()->getParam('editName', ''));
                $email = trim($this->getRequest()->getParam('editEmail', ''));
                $textPhone = trim($this->getRequest()->getParam('editTextPhone', '0'));
                $textPhone = Sos_Service_Functions::stripPhoneNumber($textPhone);
                $voicePhone = trim($this->getRequest()->getParam('editVoicePhone', ''));
                if ($doUnjoin) {
                    $result = Sos_Service_Functions::joinGroup(2, 0, $groupId, 0, 0, 0);
                    $message .= $result['message'];
                } else if ($deleteId) {
                    $contact->find($deleteId);
                    if ($contact->getId() && $contact->getType() != 1) {
                        if ($group->getType() == 2) {
                            $group->getMapper()->deleteNWJoinGroupName($group, $contact);
                        }
                        $contact->delete('id=' . $deleteId);
                        $message = 'Contact deleted successfully';
                    } else if ($contact->getType() == 1) {
                        $message = 'Cannot delete default contact';
                    }
                } else {
                    if ($editId) $contact = $contact->find($editId);
                    if ($contact->getType() != 1) {
                        $emailValidate = new Zend_Validate_EmailAddress();
                        $isEmailValid = $emailValidate->isValid($email) || !$email;
                        if ($name && ($email || $textPhone) && $isEmailValid) {
                            $contact->setGroupId($groupId);
                            $contact->setName($name);
                            $contact->setEmail($email);
                            $contact->setTextphone($textPhone);
                            $contact->setVoicephone($voicePhone);
                            $contact->save();
                            $message = 'Contact saved successfully';
                        } else {
                            $message = 'Please enter contact informations';
                        }
                    } else {
                        $message = 'Cannot edit default contact';
                    }
                }
            }
            $contactsGroup = $contact->getMapper()->getAllContactByPhoneId($phoneId, null, $groupId, true);
            $contacts = $contactsGroup['contacts'];
            $allowJoinGroup = $contactsGroup['allowJoinGroup'];
            $joinContacts = $contactsGroup['joinContacts'];
            $groupName = $contactsGroup['groupName'];
            Zend_Layout::getMvcInstance()->assign('title', htmlspecialchars($groupName));
        } else {
            $message = 'Group is not exist';
        }
        $this->view->allowJoinGroup = $allowJoinGroup;
        $this->view->groupId = $groupId;
        $this->view->message = $message;
        $this->view->contacts = $contacts;
        $this->view->joinContacts = $joinContacts;
    }
  
    public function joinAction() {
        $phone = Sos_Service_Functions::webappAuth();
        Zend_Layout::getMvcInstance()->assign('title', 'Join Neighborhood Watch');
        $message = '';
        $existJoinedGroup = false;
        $phoneId = $phone->getId();
        $groupId = $this->getRequest()->getParam('groupId', '');
        $btSearch = $this->getRequest()->getParam('btSearch', '');
        $groupName = trim($this->getRequest()->getParam('groupName', ''));
        $btJoin = $this->getRequest()->getParam('btJoin', '');
        $joinGroupJoinId = (int) $this->getRequest()->getParam('joinId', 0);
        $joinGroupId = (int) $this->getRequest()->getParam('joinGroupId', 0);
        $joinContactId = (int) $this->getRequest()->getParam('joinContactId', 0);
        $joinAction = (int) $this->getRequest()->getParam('joinAction', 0);
        $results = array();
        $isAllowJoinGroup = false;
        if ($groupId) {
            $group = new Sos_Model_Contactgroup();
            $group->find($groupId);
            if ($group->getId() && $group->getType() == 2) {
                $isAllowJoinGroup = $group->getMapper()->isAllowJoinGroup($group);
                if ($isAllowJoinGroup) {
                    if ($btSearch) {
                        $searchResults = Sos_Service_Functions::searchGroup($phone, $groupName);
                        $message = $searchResults['message'];
                        $results = $searchResults['results'];
                        $existJoinedGroup = $searchResults['existJoinedGroup'];
                    }
                    if ($joinAction && $btJoin && $joinGroupId) {
                        $result = Sos_Service_Functions::joinGroup($joinAction, $phoneId, $groupId, $joinGroupId, $joinContactId, $joinGroupJoinId);
                        $message .= $result['message'];
                        $this->view->rediectUrl = Sos_Service_Functions::$appUrl . '/webapp/groups/contacts/groupId/' . $groupId;
                    }
                } else {
                    $message = 'Group is not allow to join.';
                }
            } else {
                $message = 'Group not found or is not Neighborhood Watch Group.';
            }
        } else {
            $message = 'Group is not valid.';
        }
        $this->view->existJoinedGroup = $existJoinedGroup;
        $this->view->groupId = $groupId;
        $this->view->isAllowJoinGroup = $isAllowJoinGroup;
        $this->view->results = $results;
        $this->view->message = $message;
    }
    
    public function googleContactsAction() {
        $phone = Sos_Service_Functions::webappAuth();
        $this->_helper->layout()->disableLayout();
        $groupId = $this->_request->getParam('groupId', 0);
        $back = $this->_request->getParam('back', '');
        $backUrl = '';
        $groupModel = new Sos_Model_Contactgroup();
        $group = $groupModel->find($groupId);
        $groupId = $group->getId();
        $groupName = $group->getName();
        $phoneNumber = $phone->getNumber();
        $phoneId = $phone->getId();
        Zend_Layout::getMvcInstance()->assign('title', 'Import Google contacts');
        $message = '';
        $success = false;
        $contacts = array();
        if ($back == 'webapp') {
            $backUrl = '/webapp/groups/contacts/groupId/' . $groupId;
        }
        if ($groupId && ($group->getPhoneId() == $phone->getId())) {
            if ($this->_request->isPost()) {
                if (count($this->_request->getParam('contacts'))) {
                    foreach($this->_request->getParam('contacts') as $contactJsonString) {
                        $this->_saveContact($contactJsonString, $groupId);
                    }
                    $message = 'You have added ' . count($this->_request->getParam('contacts')) . ' to your contact list, group: ' . $groupName;
                    $this->_helper->getHelper('Redirector')->gotoUrl($backUrl . '/msg/' . $message);
                }
            }
            $googleOauthConfig      = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'google');
            $oauthConsumerKey       = $googleOauthConfig->oauth->consumerKey ;
            $oauthConsumerSecret    = $googleOauthConfig->oauth->consumerSecret ;
            $oauthRequestTokenUrl   = $googleOauthConfig->oauth->requestTokenUrl;
            $oauthAccessTokenUrl    = $googleOauthConfig->oauth->accessTokenUrl;
            $oauthAuthorizationUrl = $googleOauthConfig->oauth->authorizationUrl;
            $requestContactUrl      = $googleOauthConfig->requestContactUrl;
            $requestContactUrlParams = $googleOauthConfig->requestContactUrlParams;
            //$rsaKeyPrivateFile = APPLICATION_PATH . '/configs/google-sosbeacon.pem';
            //require_once 'Zend/Crypt/Rsa/Key/Private.php'; 
            //require_once 'Zend/Oauth/Consumer.php';
            try {
                Zend_Session::start();
            } catch(Zend_Session_Exception $e) {
                session_start();
            }
            $CONSUMER_KEY = $oauthConsumerKey;
            $CONSUMER_SECRET = $oauthConsumerSecret;
            $SCOPES = array($requestContactUrl);
            $request = Zend_Controller_Front::getInstance()->getRequest();
            // Callback Url
            $callbackUrl =  $callbackUrl = $request->getScheme() . '://' 
                            . $request->getHttpHost() 
                            . '/webapp/groups/google-contacts/groupId/' . $groupId . '/back/' . $back;

            $oauthOptions = array(
              'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
              'version' => '1.0',
              'consumerKey' => $CONSUMER_KEY,
              'consumerSecret' => $CONSUMER_SECRET,
              'signatureMethod' => 'HMAC-SHA1',
              'callbackUrl' => $callbackUrl,
              'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
              'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
              'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken'
            );
            $consumer = new Zend_Oauth_Consumer($oauthOptions);
            if (!empty($_GET['request'])) {
                if ($_GET['request'] == 'disconnect') {
                    if (isset($_SESSION['ACCESS_TOKEN'])) {
                        unset($_SESSION['ACCESS_TOKEN']);
                    }
                    if (isset($_SESSION['REQUEST_TOKEN'])) {
                        unset($_SESSION['REQUEST_TOKEN']);
                    }
                    if (isset($_SESSION['REQUEST_URL'])) {
                        unset($_SESSION['REQUEST_URL']);
                    }
                    $this->_helper->getHelper('Redirector')->gotoUrl($backUrl);
                }
            }
            // When using HMAC-SHA1, you need to persist the request token in some way.
            // This is because you'll need the request token's token secret when upgrading
            // to an access token later on. The example below saves the token object as a session variable.
            if (!isset($_SESSION['ACCESS_TOKEN'])) {
                if (!isset($_SESSION['REQUEST_TOKEN'])) {
                    $_SESSION['REQUEST_TOKEN'] = serialize($consumer->getRequestToken(array('scope' => implode(' ', $SCOPES))));
                    $approvalUrl = $consumer->getRedirectUrl(array('hd' => 'default'));
                    $_SESSION['REQUEST_URL'] = "<a href=\"$approvalUrl\">Request Google Access</a>";
                }
            }   
            if (!empty($_GET) && isset($_SESSION['REQUEST_TOKEN'])) {
               if (!isset($_SESSION['ACCESS_TOKEN'])) {
                   $_SESSION['ACCESS_TOKEN'] = serialize($consumer->getAccessToken($_GET, unserialize($_SESSION['REQUEST_TOKEN'])));
               }
            }
            if (!isset($_SESSION['ACCESS_TOKEN'])) {
                // If on a Google Apps domain, use your domain for the hd param (e.g. 'example.com').
                $loginUrl = $_SESSION['REQUEST_URL'];
                $this->view->loginUrl = $loginUrl;
            }
            if (isset($_SESSION['ACCESS_TOKEN'])) {
                $accessToken = unserialize($_SESSION['ACCESS_TOKEN']);
                $httpClient = $accessToken->getHttpClient($oauthOptions);
                $client = new Zend_Gdata($httpClient);
                $result = $client->getFeed($requestContactUrl . $requestContactUrlParams);
                $contacts = array();
                $t = 0;
                foreach ($result as $entry) {
                    $t ++;
                    $contactXml = simplexml_load_string($entry->getXML());
                    $contact = array();
                    $contact['name'] = $entry->getTitle()->getText();
                    $contact['email'] = '';
                    $contact['phone'] = '';
                    foreach ($contactXml->email as $email) {
                        $contact['email'] = (string) $email['address'];
                        break;
                    }
                    foreach ($contactXml->phoneNumber as $phone) {
                        $phoneValue = (string) $phone;
                        if ($phoneValue) {
                            $contact['phone'] = (string) $phone;
                            break;
                        }
                    }
                    if ($contact['name'] && ($contact['phone'] || $contact['email'])) {
                        $contacts[] = $contact;
                    }
                }
                if (!count($contacts)) {
                    $message = CONTACT_EMPTY;
                }
                $this->view->loginUrl = '<a href="'. $callbackUrl . '?request=disconnect">Exit Google connect</a>';
                
            }
        } else {
            $message = 'Group is not exists.';
        }
        $this->_helper->viewRenderer->setRender('import-contacts'); 
        $this->view->oauthName = 'Google';
        $this->view->phoneNumber = $phoneNumber;
        $this->view->backUrl = $backUrl;
        $this->view->groupId = $groupId;
        $this->view->groupName = $groupName;
        $this->view->contacts = $contacts;
        $this->view->message = $message;
    }
    
    public function yahooContactsAction() {
        $phone = Sos_Service_Functions::webappAuth();
        $this->_helper->layout()->disableLayout();
        $groupId = $this->_request->getParam('groupId', 0);
        $back = $this->_request->getParam('back', '');
        $backUrl = '';
        $groupModel = new Sos_Model_Contactgroup();
        $group = $groupModel->find($groupId);
        $groupId = $group->getId();
        $groupName = $group->getName();
        $phoneNumber = $phone->getNumber();
        $phoneId = $phone->getId();
        Zend_Layout::getMvcInstance()->assign('title', 'Import Yahoo contacts');
        $message = '';
        $success = false;
        $contacts = array();
        if ($back == 'webapp') {
            $backUrl = '/webapp/groups/contacts/groupId/' . $groupId;
        }
        $hasSession = false;
        $auth_url = '';
        $closeScript = '';
        $profile = false;
        if ($groupId && ($group->getPhoneId() == $phone->getId())) {
            if ($this->_request->isPost()) {
                if (count($this->_request->getParam('contacts'))) {
                    foreach($this->_request->getParam('contacts') as $contactJsonString) {
                        $this->_saveContact($contactJsonString, $groupId);
                    }
                    $message = 'You have added ' . count($this->_request->getParam('contacts')) . ' to your contact list, group: ' . $groupName;
                    $this->_helper->getHelper('Redirector')->gotoUrl($backUrl . '/msg/' . $message);
                }
            }
            error_reporting(E_ALL | E_NOTICE);
            ini_set('display_errors', false);
            try {
                Zend_Session::start();
            } catch(Zend_Session_Exception $e) {
                session_start();
            }
            // Include the YOS library.
            require APPLICATION_PATH . '/../library/Yahoo.inc';
            if(array_key_exists("logout", $_GET)) {
              // if a session exists and the logout flag is detected
              // clear the session tokens and reload the page.
              YahooSession::clearSession();
              header('Location: ' . $backUrl);
            }
            $yahooOauthConfig     = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'yahoo');
            $oauthConsumerKey     = $yahooOauthConfig->oauth->consumerKey ;
            $oauthConsumerSecret  = $yahooOauthConfig->oauth->consumerSecret ;
            $oauthAppId           = $yahooOauthConfig->oauth->consumerAppId ;
            // check for the existance of a session.
            // this will determine if we need to show a pop-up and fetch the auth url,
            // or fetch the user's social data.
            $hasSession = YahooSession::hasSession($oauthConsumerKey, $oauthConsumerSecret, $oauthAppId);
            if($hasSession == FALSE) {
              // create the callback url,
              $callback = YahooUtil::current_url()."?in_popup";
              // pass the credentials to get an auth url.
              // this URL will be used for the pop-up.
              $auth_url = YahooSession::createAuthorizationUrl($oauthConsumerKey, $oauthConsumerSecret, $callback);
            }
            else {
              // pass the credentials to initiate a session
              $session = YahooSession::requireSession($oauthConsumerKey, $oauthConsumerSecret, $oauthAppId);
              // if the in_popup flag is detected,
              // the pop-up has loaded the callback_url and we can close this window.
              if(array_key_exists("in_popup", $_GET)) {
                $closeScript = '<script type="text/javascript">window.close();</script>';
              }
              // if a session is initialized, fetch the user's profile information
              if($session && (!$closeScript)) {
                // Get the currently sessioned user.
                $user = $session->getSessionedUser();
                // Load the profile for the current user.
                $profile = $user->getProfile();
                // get contacts json
                $yahooApp = new YahooApplication($oauthConsumerKey, $oauthConsumerSecret);
                $yahooApp->token = $user->session->accessToken;
                $contactsJson = $yahooApp->query($yahooOauthConfig->queryContact);
                if(is_object($contactsJson) && isset($contactsJson->query->results->contact)) {
                    foreach($contactsJson->query->results->contact as $contactInfo) {
                        $fields = $contactInfo->fields;
                        $contact = array();
                        $contact['name'] = '';
                        $contact['phone'] = '';
                        $contact['email'] = '';
                        $firstEmail = true;
                        $firstPhone = true;
                        foreach ($fields as $f) {
                            // get contact name
                            if ($f->type == 'name') {
                                $contactName = $f->value->givenName;
                                $f->value->middleName ? $contactName .= ' ' . $f->value->middleName : false;
                                $f->value->familyName ? $contactName .= ' ' . $f->value->familyName : false;
                                $contact['name'] = $contactName;
                            }
                            if ($f->type == 'email' && $firstEmail) { // get first email if contact have multi email
                                $contact['email'] = $f->value;
                                $firstEmail = false;
                            }
                            if ($f->type == 'phone' && $firstPhone) { // get first mobile if contact have multi email
                                $contact['phone'] = $f->value;
                                if ($f->flags == 'MOBILE') { // mobile number is priority
                                    $firstPhone = false;
                                }
                            }
                        }
                        if ($contact['name'] && ($contact['phone'] || $contact['email'])) {
                            $contacts[] = $contact;
                        }
                    }
                } else {
                    $message = CONTACT_EMPTY;
                }
              }
            }
        } else {
            $message = 'Group is not exists.';
        }
        $this->_helper->viewRenderer->setRender('import-contacts'); 
        $this->view->oauthName = 'Yahoo';
        $this->view->phoneNumber = $phoneNumber;
        $this->view->backUrl = $backUrl;
        $this->view->groupName = $groupName;
        $this->view->contacts = $contacts;
        $this->view->message = $message;
        $this->view->closeScript = $closeScript;
        $this->view->hasSession = $hasSession;
        $this->view->authUrl = $auth_url;
        $this->view->profile = $profile;
    }

    private function _saveContact($contactJsonString, $groupId) {
        $contactJson = json_decode($contactJsonString);
        $name = trim($contactJson->name);
        $email = trim($contactJson->email);
        $textPhone = trim($contactJson->phone);
        $textPhone = Sos_Service_Functions::stripPhoneNumber($textPhone);
        if ($groupId && $name && ($email || $textphone)) {
            try {
                $mapper = new Sos_Model_ContactMapper();
                $contact = new Sos_Model_Contact();
                $contact->setGroupId($groupId);
                $contact->setName($name);
                $contact->setEmail($email);
                $contact->setVoicephone($voicephone);
                $contact->setTextphone($textPhone);
                $mapper->save($contact);
            } catch (Zend_Exception $e) {}
        }
    }
}