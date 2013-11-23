<?php

/**
 * UsersController
 * 
 * @author
 * @version 
 */
class Admin_PhoneController extends Zend_Controller_Action {

    public function indexAction() {
        
    }

    public function settingsAction() {
        $phoneId = $this->_request->getParam('id', false);

        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $contact = new Sos_Model_Contact();
        $contactMap = new Sos_Model_ContactMapper();

        $auth = Zend_Auth::getInstance()->getStorage()->read();
        //if authentication false, not show alertlog
        if ($auth == NULL) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $phoneList = $phoneMap->findByField('user_id', $auth->id, $phone);

            //Assign data to view
            $this->view->phoneId = $phoneId;
            $this->view->phoneRows = $phoneList;
        }
    }

    public function settingAction() {
        $this->_helper->layout()->disableLayout();
        $phoneId = $this->_request->getParam('pid', false);

        $phone = new Sos_Model_Phone();
        $phoneMap = new Sos_Model_PhoneMapper();
        $contact = new Sos_Model_Contact();
        $contactMap = new Sos_Model_ContactMapper();

        //Get all data of phone by phoneId
        $phoneDatas = array();
        $phoneDatas = $phoneMap->getAllPhoneDataByPid($phoneId);
        if (count($phoneDatas) > 0) {
            if ($phoneDatas['good_samaritan_status'] == 0) {
                $phoneDatas['good_samaritan_status'] = "OFF";
            } elseif ($phoneDatas['good_samaritan_status'] == 1) {
                $phoneDatas['good_samaritan_status'] = "ON";
            }

            //0: family; 1:friend; 2:neighborhood; 3:groupA; 4:groupb; 5:family & friend
            if ($phoneDatas['alert_sendto_group'] == 0) {
                $phoneDatas['alert_sendto_group'] = "Family";
            } elseif ($phoneDatas['alert_sendto_group'] == 1) {
                $phoneDatas['alert_sendto_group'] = "Friends";
            } elseif ($phoneDatas['alert_sendto_group'] == 2) {
                $phoneDatas['alert_sendto_group'] = "Neighborhood Watch";
            } elseif ($phoneDatas['alert_sendto_group'] == 3) {
                $phoneDatas['alert_sendto_group'] = "Group A";
            } elseif ($phoneDatas['alert_sendto_group'] == 4) {
                $phoneDatas['alert_sendto_group'] = "Group B";
            } elseif ($phoneDatas['alert_sendto_group'] == 5) {
                $phoneDatas['alert_sendto_group'] = "Family & Friends";
            }

            if ($phoneDatas['incoming_government_alert'] == 0) {
                $phoneDatas['incoming_government_alert'] = "OFF";
            } elseif ($phoneDatas['incoming_government_alert'] == 1) {
                $phoneDatas['incoming_government_alert'] = "ON";
            }

            if ($phoneDatas['panic_alert_good_samaritan_status'] == 0) {
                $phoneDatas['panic_alert_good_samaritan_status'] = "OFF";
            } elseif ($phoneDatas['panic_alert_good_samaritan_status'] == 1) {
                $phoneDatas['panic_alert_good_samaritan_status'] = "ON";
            }
        }

        //Get all contact of phone by phoneId
        $familyContacts = $contactMap->getAllContactByPhoneId($phoneId, 0);
        $friendContacts = $contactMap->getAllContactByPhoneId($phoneId, 1);
        $neighborContacts = $contactMap->getAllContactByPhoneId($phoneId, 2);
        $groupaContacts = $contactMap->getAllContactByPhoneId($phoneId, 3);
        $groupbContacts = $contactMap->getAllContactByPhoneId($phoneId, 4);

        //Assign data to view
        $this->view->phoneDatas = $phoneDatas;
        $this->view->familyContacts = $familyContacts;
        $this->view->friendContacts = $friendContacts;
        $this->view->neighborContacts = $neighborContacts;
        $this->view->groupaContacts = $groupaContacts;
        $this->view->groupbContacts = $groupbContacts;
    }

    public function listAction() {
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();

        $auth = Zend_Auth::getInstance()->getStorage()->read();
        //if authentication false, not show alertlog
        if ($auth == NULL) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $phoneList = $phoneMapper->findByField('user_id', $auth->id, $phone);

            //Change type of mobile to text
            if (count($phoneList) > 0) {
                foreach ($phoneList as $row) {
                    if ($row->getType() == 0)
                        $row->setType('Unknown');
                    elseif ($row->getType() == 1)
                        $row->setType('Iphone');
                    elseif ($row->getType() == 2)
                        $row->setType('Android');
                }
            }

            //Assign data to view
            $this->view->phoneRows = $phoneList;
        }
    }

    public function editAction() {
        $id = $this->_request->getParam("id");
        $txtName = $this->_request->getParam("txtName");
        $txtNumber = $this->_request->getParam("txtNumber");
        $txtImei = $this->_request->getParam("txtImei");
        $txtType = $this->_request->getParam("txtType");
        $hdType = $this->_request->getParam("hdType");

        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();

        if ($id == null) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/phone/list/");
        }

        $auth = Zend_Auth::getInstance()->getStorage()->read();
        //if authentication false, not show alertlog
        if ($auth == NULL) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $msg = null;
            $msg1 = null;
            $phoneMapper->findOneByField('id', $id, $phone);
            if ($hdType != NULL) {
                $phone->setName($txtName);
                //Check exist phone number when update
                if ($phone->getNumber() == $txtNumber || $this->phoneNumExist($txtNumber) == 0) {
                    $phone->setNumber($txtNumber);
                } else {
                    $msg1 = "Phone number: $txtNumber is exist.<br/>";
                }
                $phone->setImei($txtImei);
                $phone->setType($txtType);

                //Update phone information
                $phoneMapper->save($phone);
                $msg = $msg1;
                $msg .= "Update successfull !";
            }
            //Assign data to view
            $this->view->phoneRow = $phone;

            if ($msg != null) {
                $this->view->message = $msg;
            }
        }
    }

    public function addAction() {
        $txtName = $this->_request->getParam("txtName");
        $txtNumber = $this->_request->getParam("txtNumber");
        $txtImei = $this->_request->getParam("txtImei");
        $txtType = $this->_request->getParam("txtType");

        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();

        $auth = Zend_Auth::getInstance()->getStorage()->read();
        //if authentication false, not show alertlog
        if ($auth == NULL) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $msg = null;

            if ($txtName == NULL || $txtNumber == NULL) {
                //$msg = "Number field, Name field can not be null.";
            }
            //Check exist phone number when add new
            elseif ($this->phoneNumExist($txtNumber) == 1) {
                $msg = "Phone number: $txtNumber is exist.";
            } else {
                //ADD NEW PHONE AND PHONE REFERENCES
                Sos_Service_Functions::addNewPhone($txtName, $txtImei, $auth->id, $txtNumber, $txtType, $phone);

                //Send phone active code to user mail 
                Sos_Service_Functions::sendActiveMail($auth->email, $auth->name, $phone->getToken());
                //Send phone activation to phone
                Sos_Service_Functions::sendActiveSMS($phone->getNumber(), $phone->getToken());

                $this->_helper->getHelper('Redirector')->gotoUrl("/web/phone/list/");
            }

            //Assign data to view
            if ($msg != null) {
                $this->view->message = $msg;
            }
        }
    }

    public function deleteAction() {
        $id = $this->_request->getParam("id");

        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();

        if ($id == null) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/phone/list/");
        }

        $auth = Zend_Auth::getInstance()->getStorage()->read();
        //if authentication false, not show alertlog
        if ($auth == NULL) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $phoneMapper->deletePhoneById($id);
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/phone/list/");
        }
    }

    public function activeAction() {
        $code = $this->_request->getParam('code');
        if ($code != null) {
            $this->view->code = $code;
        }
    }

    public function doactiveAction() {
        $code = $this->_request->getParam('code');
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phoneMapper->findOneByField('token', $code, $phone);

        //If active code found
        $msg = null;
        if ($phone->getId() != NULL) {
            if ($phone->getStatus() == 1) {
                $msg = "Your phone number " . $phone->getNumber() . " has already actived.";
            } else {
                //Save actived phone
                $phone->setStatus(1);
                $phoneMapper->save($phone);

                $msg = "CONGRATULATION !<br/> Your phone number " . $phone->getNumber();
                $msg .= " has been activated your website is ready for login at www.SOSbeacon.org .";
            }
        }
        //If active code not found 
        else {
            $msg = "Your ACTIVATION CODE is not valid.";
        }

        $this->view->message = $msg;
    }

    public function smslogAction() {
        $smslog = new Sos_Model_Smslog();

        if (!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $sort = 'created_date DESC';
            $sort_date = ''; // default
            if ($this->_request->getParam('sort_date', '')) {
                $sort_date = ($this->_request->getParam('sort_date', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'created_date ' . $sort_date;
            }
            $sort_from = '';
            if ($this->_request->getParam('sort_from', '')) {
                $sort_from = ($this->_request->getParam('sort_from', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'from ' . $sort_from;
            }
            $sort_to = '';
            if ($this->_request->getParam('sort_to', '')) {
                $sort_to = ($this->_request->getParam('sort_to', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'to ' . $sort_to;
            }
            
            $doSearch = $this->_request->getParam('doSearch', '');
            $filter = null;
            if ($doSearch) {
                $searchFrom = str_replace('\'', '', trim($this->_request->getParam('searchFrom', '')));
                $searchTo = str_replace('\'', '', trim($this->_request->getParam('searchTo', '')));
                $filterArr = array();
                if ($searchFrom) $filterArr[] = "`from` LIKE '%$searchFrom%'";
                if ($searchTo) $filterArr[] = "`to` LIKE '%$searchTo%'";
                if (count($filterArr) > 0) $filter = implode(' AND ', $filterArr);
            }
            
            $paginator = $smslog->fetchListToPaginator($filter, $sort);
            $paginator->setItemCountPerPage(40);

            $page = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($page);
            $paginator->setPageRange(10);

            //Show Http Link in smslog message
            foreach ($paginator as $row) {
                $messageNew = Sos_Service_Functions::showHttpLink($row['message']);
                $row['message'] = $messageNew;

                //Re-write sent status of smsog
                if ($row['status'] == 0)
                    $row['status'] = 'Error';
                elseif ($row['status'] == 1)
                    $row['status'] = 'Sent';
            }

            //Assign to view
            $this->view->paginator = $paginator;
            $this->view->sort_date = $sort_date;
            $this->view->sort_from = $sort_from;
            $this->view->sort_to = $sort_to;
            $this->view->params = $this->_request;
        }

        //Save current uri
        $sess = new Zend_Session_Namespace('smslog');
        $sess->smslog = $this->getRequest()->getRequestUri();
    }

    public function smslogdeleteAction() {
        $id = $this->_request->getParam('id');

        $smslog = new Sos_Model_Smslog();
        $smslog->deleteRowByIds($id);

        //Back pre page
        $sess = new Zend_Session_Namespace('smslog');
        if (isset($sess->smslog))
            $this->_helper->getHelper('Redirector')->gotoUrl($sess->smslog);
    }

    public function emaillogAction() {
        $emaillog = new Sos_Model_Emaillog();
        if (!Zend_Auth::getInstance('admin')->setStorage(new Zend_Auth_Storage_Session('admin'))->hasIdentity()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/admin/user/login");
        } else {
            $sort = 'created_date DESC';

            $sort_date = ''; // default
            if ($this->_request->getParam('sort_date', '')) {
                $sort_date = ($this->_request->getParam('sort_date', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'created_date ' . $sort_date;
            }
            $sort_from = '';
            if ($this->_request->getParam('sort_from', '')) {
                $sort_from = ($this->_request->getParam('sort_from', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'from ' . $sort_from;
            }
            $sort_to = '';
            if ($this->_request->getParam('sort_to', '')) {
                $sort_to = ($this->_request->getParam('sort_to', '') == 'asc') ? 'ASC' : 'DESC';
                $sort = 'to ' . $sort_to;
            }
            
            $doSearch = $this->_request->getParam('doSearch', '');
            $filter = null;
            if ($doSearch) {
                $searchFrom = str_replace('\'', '', trim($this->_request->getParam('searchFrom', '')));
                $searchTo = str_replace('\'', '', trim($this->_request->getParam('searchTo', '')));
                $filterArr = array();
                if ($searchFrom) $filterArr[] = "`from` LIKE '%$searchFrom%'";
                if ($searchTo) $filterArr[] = "`to` LIKE '%$searchTo%'";
                $searchFrom = trim($this->_request->getParam('searchFrom', ''));
                $searchTo = trim($this->_request->getParam('searchTo', ''));
                if (count($filterArr) > 0) $filter = implode(' AND ', $filterArr);
            }
            
            $paginator = $emaillog->fetchListToPaginator($filter, $sort);
            $paginator->setItemCountPerPage(40);

            $page = $this->_request->getParam('page', 1);
            $paginator->setCurrentPageNumber($page);
            $paginator->setPageRange(10);

            //Show Http Link in smslog message
            foreach ($paginator as $row) {
                $messageNew = Sos_Service_Functions::showHttpLink(htmlspecialchars($row['message']));
                $row['message'] = $messageNew;

                //Re-write sent status of smsog
                if ($row['status'] == 0)
                    $row['status'] = 'Error';
                elseif ($row['status'] == 1)
                    $row['status'] = 'Sent';
            }

            //Assign to view
            $this->view->sort_date = $sort_date;
            $this->view->sort_from = $sort_from;
            $this->view->sort_to = $sort_to;
            $this->view->paginator = $paginator;
            $this->view->params = $this->_request;
        }

        //Save current uri
        $sess = new Zend_Session_Namespace('emaillog');
        $sess->emaillog = $this->getRequest()->getRequestUri();
    }

    public function emaillogdeleteAction() {
        $id = $this->_request->getParam('id');

        $emaillog = new Sos_Model_Emaillog();
        $emaillog->deleteRowByIds($id);

        //Back pre page
        $sess = new Zend_Session_Namespace('emaillog');
        if (isset($sess->emaillog))
            $this->_helper->getHelper('Redirector')->gotoUrl($sess->emaillog);
    }

}
