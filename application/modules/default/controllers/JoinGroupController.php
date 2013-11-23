<?php

require_once 'BaseController.php';

class JoinGroupController extends BaseController {

    public function init() {
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('resources');
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('xml', 'json'))
            ->addActionContext('index', array('xml', 'json'))
            ->addActionContext('get', array('xml', 'json'))
            ->addActionContext('post', array('xml', 'json'))
            ->addActionContext('put', array('xml', 'json'))
            ->addActionContext('delete', array('xml', 'json'))
            ->initContext();
    }

    public function indexAction() {
        $this->view->response = array('success' => 'false', 'message' => 'Method is not implemented');
    }

    public function getAction() {
        $phoneId = $this->_request->getParam('id', false);
        $token = $this->_request->getParam('token', false);
        $groupName = trim($this->_request->getParam('groupName', ''));
        $message = '';
        $success = 'false';
        try {
            $this->authorizePhone($token, $phoneId);
            $phone = new Sos_Model_Phone();
            $phone->find($phoneId);
            $searchResults = Sos_Service_Functions::searchGroup($phone, $groupName);
            $message .= $searchResults['message'];
            $results = $searchResults['results'];
            $success = 'true';
            $this->view->results = $results;
        } catch (Zend_Exception $ex) {
            $message .= $ex->getMessage();
        }
        $this->view->success = $success;
        $this->view->message = $message;
    }

    public function postAction() {
        $phoneId = (int) $this->_request->getParam('phone_id', false);
        $token = $this->_request->getParam('token', false);
        $groupId = (int) $this->getRequest()->getParam('groupId', '');
        $joinGroupId = (int) $this->getRequest()->getParam('joinGroupId', 0);
        $joinContactId = (int) $this->getRequest()->getParam('joinContactId', 0);
        $message = '';
        $success = 'false';
        $joinId = 0;
        $contacts = array();
        try {
            if (!$phoneId || !$token || !$groupId || !$joinGroupId || !$joinContactId) 
                throw new Zend_Exception('Request params required');
            $this->authorizePhone($token, $phoneId);
            $this->authorizeGroup($token, $groupId);
            $result = Sos_Service_Functions::joinGroup(1, $phoneId, $groupId, $joinGroupId, $joinContactId, 0);
            $message .= $result['message'];
            $joinId = $result['joinId'];
            $contacts = $this->_getJoinContactList($joinGroupId);
            $success = 'true';
        } catch (Zend_Exception $ex) {
            $message .= $ex->getMessage();
        }
        $this->view->response = array('success' => $success, 'joinId' => $joinId, 'message' => $message, 'joinContacts' => $contacts);
    }

    public function putAction() {
        $this->view->response = array('success' => 'false', 'message' => 'Method is not implemented');
    }

    public function deleteAction() {
        $groupId = (int) $this->_request->getParam('id', 0);
        $phoneId = (int) $this->_request->getParam('phone_id', false);
        $token = $this->_request->getParam('token', false);
        $joinGroupId = (int) $this->_request->getParam('joinGroupId', 0);
        $message = '';
        $success = 'false';
        $contacts = array();
        try {
            $this->authorizePhone($token, $phoneId);
            $result = Sos_Service_Functions::joinGroup(2, 0, $groupId, 0, 0, 0);
            $message .= $result['message'];
            if ($joinGroupId) $contacts = $this->_getJoinContactList($joinGroupId);
            $success = 'true';
        } catch (Zend_Exception $ex) {
            $message .= $ex->getMessage();
        }
        $this->view->response = array('success' => $success, 'message' => $message, 'joinContacts' => $contacts);
    }

    private function _getJoinContactList($groupId) {
        $contacts = array();
        $mapper = new Sos_Model_ContactMapper();
        $contact = new Sos_Model_Contact();
        $index = 0;
        $result = $mapper->findByFieldOrder('group_id', $groupId, $contact);
        if (count($result)) {
            foreach ($result as $row) {
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
        }
        return $contacts;
    }
}
