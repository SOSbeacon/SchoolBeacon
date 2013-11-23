<?php

require_once('MainModel.php');

class Sos_Model_Groupjoin extends MainModel {

    protected $_Id;
    protected $_PhoneId;
    protected $_GroupId;
    protected $_JoinGroupId;
    protected $_ContactId;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'phone_id' => 'PhoneId',
            'group_id' => 'GroupId',
            'contact_id' => 'ContactId',
            'join_group_id' => 'JoinGroupId',
        ));
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

    public function setGroupId($data)
    {
        $this->_GroupId=$data;
        return $this;
    }
    
    public function getGroupId()
    {
        return $this->_GroupId;
    }
    
    public function setJoinGroupId($data)
    {
        $this->_JoinGroupId=$data;
        return $this;
    }
    
    public function getJoinGroupId()
    {
        return $this->_JoinGroupId;
    }
    
    public function setContactId($data) {
        $this->_ContactId = $data;
        return $this;
    }

    public function getContactId() {
        return $this->_ContactId;
    }

    /**
     * @return Sos_Model_GroupjoinMapper
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_GroupjoinMapper());
        }
        return $this->_mapper;
    }

    public function deleteRowByPrimaryKey() {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }

}
