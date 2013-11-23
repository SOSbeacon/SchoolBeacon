<?php

require_once('MainModel.php');

class Sos_Model_Response extends MainModel {

    protected $_Id;
    protected $_AlertGroupId;
    protected $_ContactId;
    protected $_ReplyId;
    protected $_Name;
    protected $_Email;
    protected $_Number;
    protected $_ReceiveEmail;
    protected $_ReceiveSms;
    protected $_OpenLink;
    protected $_ResponseChat;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'alert_group_id' => 'AlertGroupId',
            'contact_id' => 'ContactId',
            'reply_id' => 'ReplyId',
            'name' => 'Name',
            'email' => 'Email',
            'number' => 'Number',
            'receive_email' => 'ReceiveEmail',
            'receive_sms' => 'ReceiveSms',
            'open_link' => 'OpenLink',
            'response_chat' => 'ResponseChat',
        ));
    }
    
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_ResponseMapper());
        }
        return $this->_mapper;
    }

    public function setId($data) {
        $this->_Id = $data;
        return $this;
    }

    public function getId() {
        return $this->_Id;
    }
    
    public function setAlertGroupId($data) {
        $this->_AlertGroupId = $data;
        return $this;
    }

    public function getAlertGroupId() {
        return $this->_AlertGroupId;
    }

    public function setContactId($data) {
        $this->_ContactId = $data;
        return $this;
    }

    public function getContactId() {
        return $this->_ContactId;
    }

    public function setReplyId($data) {
        $this->_ReplyId = $data;
        return $this;
    }

    public function getReplyId() {
        return $this->_ReplyId;
    }
    
    public function setReceiveEmail($data) {
        $this->_ReceiveEmail = $data;
        return $this;
    }

    public function getReceiveEmail() {
        return $this->_ReceiveEmail;
    }

    public function setReceiveSms($data) {
        $this->_ReceiveSms = $data;
        return $this;
    }

    public function getReceiveSms() {
        return $this->_ReceiveSms;
    }

    public function setOpenLink($data) {
        $this->_OpenLink = $data;
        return $this;
    }

    public function getOpenLink() {
        return $this->_OpenLink;
    }
    
    public function setResponseChat($data) {
        $this->_ResponseChat = $data;
        return $this;
    }

    public function getResponseChat() {
        return $this->_ResponseChat;
    }
    
    public function setName($data) {
        $this->_Name = $data;
        return $this;
    }

    public function getName() {
        return $this->_Name;
    }
    
    public function setEmail($data) {
        $this->_Email = $data;
        return $this;
    }

    public function getEmail() {
        return $this->_Email;
    }
    
    public function setNumber($data) {
        $this->_Number = $data;
        return $this;
    }

    public function getNumber() {
        return $this->_Number;
    }
}