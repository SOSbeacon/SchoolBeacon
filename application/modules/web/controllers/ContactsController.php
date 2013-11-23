<?php

define("CONTACT_EMPTY", "Your contact is empty or no contact has one phone number or email address.");
define("PHONE_INVALID", "Phone number is not valid.");
define("USER_INVALID", "User is not valid.");
define("CONTACT_REQUIRE", "Please enter contact group, contact name, email address or text phone number.");

class Web_ContactsController extends Zend_Controller_Action {

    public function listAction() {
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        }

        if ($this->_request->getParam('msg', false)) {
            $this->view->message = htmlspecialchars($this->_request->getParam('msg'));
        }

        $groupId = $this->_request->getParam('groupId', 0);
        $groupId = $groupId ? (int) $groupId : 0;
        $phoneId = $this->_request->getParam('phoneId', 0);
        $phoneId = $phoneId ? (int) $phoneId : 0;

        $groupModel = new Sos_Model_Contactgroup();
        $group = $groupModel->find($groupId);
        $groupId = $group->getId();
        $groupName = $group->getName();

        $phone = new Sos_Model_Phone();
        $phone->find($phoneId);
        $phoneId = $phone->getId();
        $phoneNumber = $phone->getNumber();

        if ($groupId && $phoneId) {
            if ($this->_request->getParam('_method', false)) {
                $this->saveContact();
            }
            $groupMapper = new Sos_Model_ContactgroupMapper();
            $groups = $groupMapper->findByField('phone_id', $phoneId, $groupModel);
            $contactGroups = array();
            foreach ($groups as $group) {
                $contactGroups[] = array(
                    'id' => $group->getId(),
                    'name' => $group->getName()
                );
            }

            $contactModel = new Sos_Model_Contact();
            $contactList = $contactModel->fetchList('group_id=' . $groupId);
            $contacts = array();
            if (count($contactList)) {
                foreach ($contactList as $contact) {
                    $contacts[] = array(
                        'id' => $contact->getId(),
                        'name' => $contact->getName(),
                        'email' => $contact->getEmail(),
                        'voicePhone' => $contact->getVoicePhone(),
                        'textPhone' => $contact->getTextPhone(),
                        'groupId' => $groupId,
                        'groupName' => $groupName,
                        'typeId' => $contact->getType(),
                        'typeName' => ($contact->getType() == '1' ? '<em>Default</em>' : 'Normal')
                    );
                }
            } else {
                $this->view->message = 'Your contacts in this group is empty';
            }

            $this->view->contacts = $contacts;
            $this->view->phoneId = $phoneId;
            $this->view->phoneNumber = $phoneNumber;
            $this->view->groupId = $groupId;
            $this->view->groupName = $groupName;
            $this->view->groups = $contactGroups;
        } else {
            $this->view->message = 'This contact group does not exist';
        }
    }

    private function saveContact($contactJsonString = '', $toGroupId = '') {
        $method = $this->_request->getParam('_method', false);
        $contactId = "";
        $groupId = $toGroupId;
        $name = "";
        $email = "";
        $textphone = "";
        $voicephone = "";
        if ($contactJsonString) { // import contact from google, yahoo
            $contactJson = json_decode($contactJsonString);
            $name = trim($contactJson->name);
            $email = trim($contactJson->email);
            $textphone = trim($contactJson->phone);
        } else {
            $contactId = $this->_request->getParam('contactId', false);
            $groupId = $this->_request->getParam('contactGroups', false);
            $name = trim($this->_request->getParam('contactName', ''));
            $email = trim($this->_request->getParam('contactEmail', ''));
            $textphone = trim($this->_request->getParam('contactTextPhone', ''));
            $voicephone = trim($this->_request->getParam('contactVoicePhone', ''));
        }
        $contact = new Sos_Model_Contact();
        $textphone = Sos_Service_Functions::stripPhoneNumber($textphone);
        if ($groupId && $name && ($email || $textphone)) {
            try {
                $mapper = new Sos_Model_ContactMapper();
                if ($contactId) {
                    $contact->setId($contactId);
                }
                $contact->setGroupId($groupId);
                $contact->setName($name);
                $contact->setEmail($email);
                $contact->setVoicephone($voicephone);
                $contact->setTextphone($textphone);
                $mapper->save($contact);
                $this->view->message = 'Contact saved.';
            } catch (Zend_Exception $e) {
                
            }
        } else {
            if ($method == 'DELETE') {
                $contact->find($contactId);
                if ($contact->getType() != '1') {
                    $contact->deleteRowByPrimaryKey();
                    $this->view->message = 'Contact deleted.';
                } else {
                    $this->view->message = 'Default contact cannot delete.';
                }
            } else {
                $this->view->message = CONTACT_REQUIRE;
            }
        }
    }

    public function importGoogleContactsAction() {
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        }
        $groupId = $this->_request->getParam('groupId', 0);
        $groupId = $groupId ? (int) $groupId : 0;
        $phoneId = $this->_request->getParam('phoneId', 0);
        $phoneId = $phoneId ? (int) $phoneId : 0;

        $groupModel = new Sos_Model_Contactgroup();
        $group = $groupModel->find($groupId);
        $groupId = $group->getId();
        $groupName = $group->getName();

        $phone = new Sos_Model_Phone();
        $phone->find($phoneId);
        $phoneId = $phone->getId();
        $phoneNumber = $phone->getNumber();

        $message = '';
        $contacts = array();
        $profile = false;
        $backUrl = '/web/contacts/list/phoneId/' . $phoneId . '/groupId/' . $groupId;
        if ($groupId && $phoneId) {
            //import contacts to database if is post
            if ($this->_request->isPost()) {
                if (count($this->_request->getParam('contacts'))) {
                    foreach ($this->_request->getParam('contacts') as $contactJsonString) {
                        $this->saveContact($contactJsonString, $groupId);
                    }
                    $message = 'You have added ' . count($this->_request->getParam('contacts')) . ' to your contact list, group: ' . $groupName;
                    $this->_helper->getHelper('Redirector')->gotoUrl($backUrl . '/msg/' . $message);
                }
            }

            $googleOauthConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'google');
            $oauthConsumerKey = $googleOauthConfig->oauth->consumerKey;
            $oauthConsumerSecret = $googleOauthConfig->oauth->consumerSecret;
            $oauthRequestTokenUrl = $googleOauthConfig->oauth->requestTokenUrl;
            $oauthAccessTokenUrl = $googleOauthConfig->oauth->accessTokenUrl;
            $oauthAuthorizationUrl = $googleOauthConfig->oauth->authorizationUrl;
            $requestContactUrl = $googleOauthConfig->requestContactUrl;
            $requestContactUrlParams = $googleOauthConfig->requestContactUrlParams;
            $rsaKeyPrivateFile = APPLICATION_PATH . '/configs/google-sosbeacon.pem';
            require_once 'Zend/Crypt/Rsa/Key/Private.php';
            require_once 'Zend/Oauth/Consumer.php';

            try {
                Zend_Session::start();
            } catch (Zend_Session_Exception $e) {
                session_start();
            }

            $CONSUMER_KEY = $oauthConsumerKey;
            $CONSUMER_SECRET = $oauthConsumerSecret;
            $SCOPES = array($requestContactUrl);
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $callbackUrl = $callbackUrl = $request->getScheme() . '://' . $request->getHttpHost() . '/web/contacts/import-google-contacts/phoneId/' . $phoneId . '/groupId/' . $groupId . '';

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
                    $t++;
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
                $this->view->loginUrl = '<a href="' . $backUrl . '">&lt;&lt;Back</a> | <a href="' . $callbackUrl . '?request=disconnect">Exit Google connect</a>';
            } else {
                //$message = USER_INVALID;
            }
        }
        $this->view->phoneId = $phoneId;
        $this->view->phoneNumber = $phoneNumber;
        $this->view->groupId = $groupId;
        $this->view->groupName = $groupName;
        $this->view->contacts = $contacts;
        $this->view->message = $message;
    }

    public function importYahooContactsAction() {
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        }

        $groupId = $this->_request->getParam('groupId', 0);
        $groupId = $groupId ? (int) $groupId : 0;
        $phoneId = $this->_request->getParam('phoneId', 0);
        $phoneId = $phoneId ? (int) $phoneId : 0;

        $groupModel = new Sos_Model_Contactgroup();
        $group = $groupModel->find($groupId);
        $groupId = $group->getId();
        $groupName = $group->getName();

        $phone = new Sos_Model_Phone();
        $phone->find($phoneId);
        $phoneId = $phone->getId();
        $phoneNumber = $phone->getNumber();

        $hasSession = false;
        $auth_url = '';
        $closeScript = '';
        $message = '';
        $contacts = array();
        $profile = false;

        if ($groupId && $phoneId) {
            if ($this->_request->isPost()) {
                if (count($this->_request->getParam('contacts'))) {
                    foreach ($this->_request->getParam('contacts') as $contactJsonString) {
                        $this->saveContact($contactJsonString, $groupId);
                    }
                    $message = 'You have added ' . count($this->_request->getParam('contacts')) . ' to your contact list, group: ' . $groupName;
                    $this->_helper->getHelper('Redirector')->gotoUrl('/web/contacts/list/phoneId/' . $phoneId . '/groupId/' . $groupId . '/msg/' . $message);
                }
            }

            error_reporting(E_ALL | E_NOTICE);
            ini_set('display_errors', false);

            try {
                Zend_Session::start();
            } catch (Zend_Session_Exception $e) {
                session_start();
            }

            // Include the YOS library.
            require APPLICATION_PATH . '/../library/Yahoo.inc';

            if (array_key_exists("logout", $_GET)) {
                // if a session exists and the logout flag is detected
                // clear the session tokens and reload the page.
                YahooSession::clearSession();
                header('Location: /web/contacts/list/phoneId/' . $phoneId . '/groupId/' . $groupId . '');
            }

            $yahooOauthConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/oauth.ini', 'yahoo');
            $oauthConsumerKey = $yahooOauthConfig->oauth->consumerKey;
            $oauthConsumerSecret = $yahooOauthConfig->oauth->consumerSecret;
            $oauthAppId = $yahooOauthConfig->oauth->consumerAppId;

            // check for the existance of a session.
            // this will determine if we need to show a pop-up and fetch the auth url,
            // or fetch the user's social data.
            $hasSession = YahooSession::hasSession($oauthConsumerKey, $oauthConsumerSecret, $oauthAppId);
            if ($hasSession == FALSE) {
                // create the callback url,
                $callback = YahooUtil::current_url() . "?in_popup";
                // pass the credentials to get an auth url.
                // this URL will be used for the pop-up.
                $auth_url = YahooSession::createAuthorizationUrl($oauthConsumerKey, $oauthConsumerSecret, $callback);
            } else {
                // pass the credentials to initiate a session
                $session = YahooSession::requireSession($oauthConsumerKey, $oauthConsumerSecret, $oauthAppId);

                // if the in_popup flag is detected,
                // the pop-up has loaded the callback_url and we can close this window.
                if (array_key_exists("in_popup", $_GET)) {
                    $closeScript = '<script type="text/javascript">window.close();</script>';
                }

                // if a session is initialized, fetch the user's profile information
                if ($session && (!$closeScript)) {
                    // Get the currently sessioned user.
                    $user = $session->getSessionedUser();
                    // Load the profile for the current user.
                    $profile = $user->getProfile();
                    // get contacts json
                    $yahooApp = new YahooApplication($oauthConsumerKey, $oauthConsumerSecret);
                    $yahooApp->token = $user->session->accessToken;

                    $contactsJson = $yahooApp->query($yahooOauthConfig->queryContact);

                    if (is_object($contactsJson) && isset($contactsJson->query->results->contact)) {
                        foreach ($contactsJson->query->results->contact as $contactInfo) {
                            $fields = $contactInfo->fields;
                            $contact = array();
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
                            if ($contact) {
                                $contacts[] = $contact;
                            }
                        }
                    } else {
                        $message = CONTACT_EMPTY;
                    }
                }
            }
        } else {
            $message = PHONE_INVALID;
        }
        $this->view->phoneId = $phoneId;
        $this->view->phoneNumber = $phoneNumber;
        $this->view->groupId = $groupId;
        $this->view->groupName = $groupName;
        $this->view->contacts = $contacts;
        $this->view->message = $message;
        $this->view->closeScript = $closeScript;
        $this->view->hasSession = $hasSession;
        $this->view->authUrl = $auth_url;
        $this->view->profile = $profile;
    }

    public function importFacebookContactsAction() {
        
    }

    // call back from google, yahoo import contact api
    public function oauthAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->getHelper('Redirector')->gotoUrl('sosbeacon://oauth?' . $_SERVER['QUERY_STRING']);
    }

}
