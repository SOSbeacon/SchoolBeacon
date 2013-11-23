<?php

require_once 'BaseController.php';

class ContactsController extends BaseController {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch')
                ->addActionContext('index', array('xml', 'json'))
                ->addActionContext('get', array('xml', 'json'))
                ->addActionContext('post', array('xml', 'json'))
                ->addActionContext('put', array('xml', 'json'))
                ->addActionContext('delete', array('xml', 'json'))
                ->initContext();

        $this->logger = Sos_Service_Logger::getLogger();
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction() {
        $token = $this->_request->getParam('token', false);
        $groupId = $this->_request->getParam('groupId', false);
        $resObj = array();
        try {
            $this->authorizeGroup($token, $groupId);
            $group = new Sos_Model_Contactgroup();
            $group->find($groupId);
            $mapper = new Sos_Model_ContactMapper();
            $contact = new Sos_Model_Contact();
            $contactsGroup = $contact->getMapper()->getAllContactByPhoneId($group->getPhoneId(), null, $groupId, true);
            $contactList = $contactsGroup['contacts'];
            $allowJoinGroup = $contactsGroup['allowJoinGroup'];
            $joinContactList = $contactsGroup['joinContacts'];
            $groupName = $contactsGroup['groupName'];
            $resObj['success'] = "true";
            $contacts = array();
            $index = 0;
            foreach ($contactList as $row) {
                $arr = array();
                $arr['id'] = $row->getId();
                $arr['group_id'] = $row->getGroupId();
                $arr['name'] = $row->getName();
                $arr['email'] = $row->getEmail();
                $arr['voicePhone'] = $row->getVoicephone();
                $arr['textPhone'] = $row->getTextphone();
                $arr['type'] = $row->getType();
                $contacts['contact_' . $index++] = $arr;
            }
            $joinContacts = array();
            $index = 0;
            foreach ($joinContactList as $row) {
                $arr = array();
                $arr['id'] = $row->getId();
                $arr['group_id'] = $row->getGroupId();
                $arr['name'] = $row->getName();
                $arr['email'] = $row->getEmail();
                $arr['voicePhone'] = $row->getVoicephone();
                $arr['textPhone'] = $row->getTextphone();
                $arr['type'] = $row->getType();
                $joinContacts['contact_' . $index++] = $arr;
            }
            $resObj['data'] = $contacts;
            $resObj['groupName'] = $groupName;
            $resObj['allowJoinGroup'] = $allowJoinGroup;
            $resObj['joinContacts'] = count($joinContacts) ? $joinContacts : null;
        } catch (Zend_Exception $ex) {
            $resObj['success'] = 'false';
            $resObj['message'] = $ex->getMessage();
        }
        $this->view->response = $resObj;
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it 
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction() {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->message = 'Not implemented';
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() {
        $token = $this->_request->getParam('token', false);
        $groupId = $this->_request->getParam('groupId', false);

        $name = $this->_request->getParam('name', false);
        $email = $this->_request->getParam('email', false);
        $voicephone = $this->_request->getParam('voicePhone', false);
        $textPhone = $this->_request->getParam('textPhone', false);
        $textPhone = Sos_Service_Functions::stripPhoneNumber($textPhone);
        $resObj = array();
        $resObj['success'] = 'false';
        if ($name && ($email || $textPhone)) {
            try {
                $this->authorizeGroup($token, $groupId);

                $mapper = new Sos_Model_ContactMapper();
                $contact = new Sos_Model_Contact();

                $contact->setGroupId($groupId);
                $contact->setName($name);
                $contact->setEmail($email);
                $contact->setVoicephone($voicephone);
                $contact->setTextphone($textPhone);
                $mapper->save($contact);

                $resObj['success'] = 'true';
                $resObj['id'] = $contact->getId();
            } catch (Zend_Exception $ex) {
                $resObj['message'] = $ex->getMessage();
            }
        } else {
            $resObj['message'] = 'Please enter Name and TextPhone or Name and Email';
        }

        $this->view->response = $resObj;
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it 
     * should update the server resource state of the resource identified by 
     * the 'id' value.
     */
    public function putAction() {
        $contactId = $this->_request->getParam('id', false);
        $token = $this->_request->getParam('token', false);

        $name = $this->_request->getParam('name', '');
        $email = $this->_request->getParam('email', '');
        $voicephone = $this->_request->getParam('voicePhone', '');
        $textPhone = $this->_request->getParam('textPhone', '');
        $textPhone = Sos_Service_Functions::stripPhoneNumber($textPhone);
        $resObj = array();
        $resObj['success'] = 'false';
        if ($name && ($email || $textPhone)) {
            try {
                $this->authorizeContact($token, $contactId);

                $contactMapper = new Sos_Model_ContactMapper();
                $contact = new Sos_Model_Contact();

                $data = array();

                $data['name'] = $name == '' ? NULL : $name;
                $data['email'] = $email == '' ? NULL : $email;
                $data['voicePhone'] = $voicephone == '' ? NULL : $voicephone;
                $data['textPhone'] = $textPhone == '' ? NULL : $textPhone;

                $contactMapper->getDbTable()->update($data, array('id = ?' => $contactId));

                $resObj['success'] = 'true';
            } catch (Zend_Exception $ex) {
                $resObj['message'] = $ex->getMessage();
                $this->logger->log('Error : ' . $ex, Zend_Log::ERR);
            }
        } else {
            $resObj['message'] = 'Please enter Name and TextPhone or Name and Email';
        }
        $this->view->response = $resObj;
    }

    /**
     * The delete action handles DELETE requests and receives an 'id' 
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction() {
        $contactId = $this->_request->getParam('id', false);
        $token = $this->_request->getParam('token');

        $resObj = array();
        try {
            $this->authorizeContact($token, $contactId);

            $contact = new Sos_Model_Contact();
            $contactMapper = new Sos_Model_ContactMapper();
            $contactMapper->findOneByField('id', $contactId, $contact);
            if ($contact->getId() != null) {
                //IF Contact is not default contact
                if ($contact->getType() != 1) {
                    $group = new Sos_Model_Contactgroup();
                    $group->find($contact->getGroupId());
                    if ($group->getType() == 2) {
                        $group->getMapper()->deleteNWJoinGroupName($group, $contact);
                    }
                    $contact->delete('id = ' . $contactId);
                } else {
                    $resObj['success'] = "false";
                    $resObj['message'] = "Cannot delete default contact";
                }
            }

            $resObj['success'] = "true";
        } catch (Zend_Exception $ex) {
            $resObj['success'] = "false";
            $resObj['message'] = $ex->getMessage();
        }

        $this->view->response = $resObj;
    }

}

