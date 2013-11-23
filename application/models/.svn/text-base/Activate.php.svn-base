<?php

require_once('MainModel.php');

class Sos_Model_Activate extends MainModel {

    protected $_Id;
    protected $_PhoneId;
    protected $_Number;
    protected $_Imei;
    protected $_NewNumber;
    protected $_NewImei;
    protected $_Token;
    protected $_Action;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'phone_id' => 'PhoneId',
            'number' => 'Number',
            'imei' => 'Imei',
            'new_number' => 'NewNumber',
            'new_imei' => 'NewImei',
            'token' => 'Token',
            'action' => 'Action'
        ));
    }
    
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_ActivateMapper());
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
    
    public function setPhoneId($data) {
        $this->_PhoneId = $data;
        return $this;
    }

    public function getPhoneId() {
        return $this->_PhoneId;
    }

    public function setNumber($data) {
        $this->_Number = $data;
        return $this;
    }

    public function getNumber() {
        return $this->_Number;
    }

    public function setImei($data) {
        $this->_Imei = $data;
        return $this;
    }

    public function getImei() {
        return $this->_Imei;
    }

    public function setNewNumber($data) {
        $this->_NewNumber = $data;
        return $this;
    }

    public function getNewNumber() {
        return $this->_NewNumber;
    }

    public function setNewImei($data) {
        $this->_NewImei = $data;
        return $this;
    }

    public function getNewImei() {
        return $this->_NewImei;
    }
    
    public function setToken($data) {
        $this->_Token = $data;
        return $this;
    }

    public function getToken() {
        return $this->_Token;
    }
    
    public function setAction($data) {
        $this->_Action = $data;
        return $this;
    }

    public function getAction() {
        return $this->_Action;
    }
}