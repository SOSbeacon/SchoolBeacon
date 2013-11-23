<?php

require_once('MainModel.php');

class Sos_Model_Student extends MainModel {

    protected $_Id;
    protected $_PhoneId;
    protected $_Contact1Id;
    protected $_Contact2Id;
    protected $_Name;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'phone_id' => 'PhoneId',
            'contact1_id' => 'Contact1Id',
            'contact2_id' => 'Contact2Id',
            'name' => 'Name',
        ));
    }
    
    /**
     * @return Sos_Model_StudentMapper
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_StudentMapper());
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
    
    public function setContact1Id($data) {
        $this->_Contact1Id = $data;
        return $this;
    }

    public function getContact1Id() {
        return $this->_Contact1Id;
    }
    
    public function setContact2Id($data) {
        $this->_Contact2Id = $data;
        return $this;
    }

    public function getContact2Id() {
        return $this->_Contact2Id;
    }
    
    public function setName($data) {
        $this->_Name = $data;
        return $this;
    }

    public function getName() {
        return $this->_Name;
    }
}