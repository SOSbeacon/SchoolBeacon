<?php

class Admin_UserController extends Zend_Controller_Action
{
    
    public function indexAction() {
        if(!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        }
        $message = '';
        // admin auto login to selected user
        if ($this->_request->getParam('do', '') == 'loginuser') {
            $phoneId = $this->_request->getParam('id');
            $phoneModel = new Sos_Model_Phone();
            $phoneModel = $phoneModel->find($phoneId);
            if ($phoneModel->getId()) {
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session());
                $storage = $auth->getStorage();
                $authStorage = new stdClass();
                $authStorage->id = $phoneModel->getId();
                $authStorage->email = $phoneModel->getEmail();
                $authStorage->phoneNumber = $phoneModel->getNumber();
                $authStorage->username = $phoneModel->getName();
                $storage->write($authStorage);
                $alertloggroupMapper = new Sos_Model_AlertloggroupMapper();
                $alertloggroup = $alertloggroupMapper->findLastByFieldByUid($storage->read()->id);
                if ($alertloggroup->getId() != NULL) {
                    $this->_helper->getHelper('Redirector')->gotoUrl("/web/alert/list?id=".$alertloggroup->getToken());
                } else {
                    $this->_helper->getHelper('Redirector')->gotoUrl("/web/alert/index");
                }
            } else {
                $message = 'User not found.';
            }
        } 
        $method = $this->_request->getParam('form_method');
        $id = $this->_request->getParam('form_item_id');
        if ($this->_request->isPost()) {
            if($method == 'update_phone') {
                $phone = new Sos_Model_Phone();
                $name = $this->_request->getParam('phoneName', '');
                $status = $this->_request->getParam('phoneStatus');
                $pass = $this->_request->getParam('password', '');
                $emailEnabled = $this->_request->getParam('emailEnabled', 0);
                if (empty($emailEnabled)) {
                    $emailEnabled = "0";
                }
                if (trim($pass)) {
                    $phone->setPassword(md5($pass));
                }
                $phone->setId($id);
                $phone->setEmailEnabled($emailEnabled);
                $phone->setName($name);
                $phone->setStatus($status);
                $phone->setModifiedDate(date("Y-m-d H:i:s"));
                $phone->save();
                $message = 'Updated phone successfully!';
            }
            if($method == 'delete_phone') {
                $phone = new Sos_Model_Phone();
                $phone->delete('id=' . $id);
                $message = 'Deleted phone successfully!';
            }
        }
        // get phones list
        $phoneModel = new Sos_Model_Phone();
        $paginator = $phoneModel->fetchListToPaginator(null, 'id DESC');
        $paginator->setItemCountPerPage(40);
        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(10);
        $this->view->paginator = $paginator;
        $this->view->message = $message;
    }
    
    public function loginAction() {
    	$this->_helper->layout()->disableLayout();
        if(Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()){
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/index");
        }   
        $username 	= $this->_request->getParam('username', false);
        $password 	= $this->_request->getParam('password', false);
        $regMsg 	 = new Zend_Session_Namespace('msg');
        if ($this->_request->isPost()) {
            //Check null param
            if($username == null || $password == null) {
                $regMsg->msg = "Username or password cannot be null";
            }
            else {
                $db = Zend_Db_Table::getDefaultAdapter();
                //create the auth adapter
                $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'adminuser', 'username', 'password');
                // set the username and password
                $authAdapter->setIdentity($username);
                $authAdapter->setCredential(md5($password));
                //authenticate
                $result = $authAdapter->authenticate();
                if ($result->isValid()) {
                    $auth = Zend_Auth::getInstance('adminy');
                    $auth->setStorage(new Zend_Auth_Storage_Session('admin'));
                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject(array('username' , 'name', 'id', )));
                    $this->_helper->getHelper('Redirector')->gotoUrl("/admin/index");
                } else {
                    $regMsg->msg = "Username or password does not exist";
                }
            }
        }
        $this->view->response 	= $regMsg->msg;
        //Clear Register message
        $regMsg->unsetAll();
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->clearIdentity();
        $regMsg 	 = new Zend_Session_Namespace('msg');
        $regMsg->msg = 'You have logged out.';
        $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login/");
    }
    
    public function loginstatusAction() {
    	//Disable layout 
    	//$this->_helper->layout()->disableLayout(); 
    	//Zend_Auth check login status
        $auth 	  	 = Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->getStorage()->read();
        $this->view->auth 		= $auth;
    }
    
    public function sendEmailAction() {
        if(!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        }
        $message = '';
        $sql = 'SELECT p.id AS id, p.email AS email, p.name AS name FROM phone p WHERE ((p.email IS NOT NULL) AND p.email<>"" AND p.status=1 AND p.email_enabled=1 AND p.subscribe=1) GROUP BY p.email';
        $db = Zend_Db_Table::getDefaultAdapter();
        $numberEmailsInDb = $db->query($sql)->rowCount();
        
        $emailConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/email.ini', 'gmail');
        $from = $emailConfig->resources->email->account;
        $sendAccount = $emailConfig->resources->email->account;
        $sendPass = $emailConfig->resources->email->password;
        
        if ($this->_request->isPost()) {
            $mailSubject = trim($this->_request->getParam('formSubject', ''));
            $mailMessage = trim($this->_request->getParam('formMessage', ''));
            $selectTo = $this->_request->getParam('formSelectTo', '1');
            $selectedEmails = trim($this->_request->getParam('formEmails', ''));
            if ($selectTo == '1') {
                $numberEmails = $numberEmailsInDb;
                $users = $db->query($sql)->fetchAll();
            }
            if ($selectTo == '2') { // if selected emails
                $numberEmails = 0;
                $users = array();
                $selectedEmails = $selectedEmails . ',' ; // in case only one email
                $selectedEmailsArr = explode(',', $selectedEmails);
                if ($selectedEmailsArr) {
                    foreach($selectedEmailsArr as $userInfo) {
                        if (trim($userInfo)) {
                            $userInfoArr = explode('|', $userInfo);
                            $u = array();
                            if (count($userInfoArr) > 1) {
                                $u['name'] = trim($userInfoArr[0]);
                                $u['email']  = trim($userInfoArr[1]);
                            } else {
                                $u['name'] = '';
                                $u['email'] = trim($userInfo);
                            }
                            $users[] = $u;
                        }
                    }
                }
                $numberEmails = count($users);
            }
            if ($numberEmails && $mailSubject && $mailSubject) {
                $emailSent = 0;
                $emailFail = 0;
                $emailFailList = array();
                foreach ($users as $u) {
                    $mail = new Sos_Service_ClassMail();
                    $mail->setSubject($mailSubject);
                    $mail->setAddressTo($u['email']);
                    $mail->setAddressName($u['name']);
                    $body = $mailMessage;
                    
                    $unsubscribe_link = 'http://sosbeacon.org:8085/web/users/unsubscribe/m/' . urlencode($u['email']) . '/token/' . md5('sos' . $u['email']) ;
                    
                    $body .= '<br />----------<br /> The message was sent from SOSbeacon.org. 
                            If you don\'t want to receive these emails from SOSbeacon in the future 
                            you can <a href="' . $unsubscribe_link . '"><strong>unsubscribe</strong></a>.';
                    
                    $mail->setBody($body);
                    try {
                        $mail->sendMail($from, '', true);
                        $emailSent ++;
                    } catch (Exception  $ex) {
                        $emailFail ++;
                        $emailFailList[] = $u['email'];
                        $logger = Sos_Service_Logger::getLogger();
                        $logger->log($ex, Zend_Log::ERR);	
                    } 
                }
                //$message = '<strong><em>' . $emailSent . ' emails</em></strong> have been sent successfully.';
                $message = 'Email(s) have been sent successfully.';
                if ($emailFail) {
                    $message .= '<br /><strong><em>' . $emailFail . ' emails</em></strong> have been sent fail: ' . implode(', ', $emailFailList) . '.';
                }
                
            } else {
                $message = 'Please enter subject and message.';
            }
        }
        
        $this->view->message = $message;
        $this->view->from = $from;
        $this->view->to =  '<strong><em>' . $numberEmailsInDb . ' emails </em></strong> of registered user who has activated at least one phone.';
    }
    
    public function selectUsersAction() {
        if(!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
            $this->_helper->getHelper('Redirector')->gotoUrl('/admin/user/login');
        }
        $phone = new Sos_Model_Phone();
        $paginator  = $phone->fetchListToPaginator('(email IS NOT NULL) AND email<>""', 'id DESC');
        $paginator->setItemCountPerPage(40);
        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        $paginator->setPageRange(10);
        $this->view->paginator = $paginator;
    }
}
