<?php

require_once('MainModel.php');

class Sos_Model_Studentgroup extends MainModel {

    protected $_Id;
    protected $_StudentId;
    protected $_GroupId;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'student_id' => 'StudentId',
            'group_id' => 'GroupId',
        ));
    }

    public function setId($data) {
        $this->_Id = $data;
        return $this;
    }

    public function getId() {
        return $this->_Id;
    }

    public function setStudentId($data) {
        $this->_StudentId = $data;
        return $this;
    }

    public function getStudentId() {
        return $this->_StudentId;
    }

    public function setGroupId($data) {
        $this->_GroupId = $data;
        return $this;
    }

    public function getGroupId() {
        return $this->_GroupId;
    }
    
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_StudentgroupMapper());
        }
        return $this->_mapper;
    }

    public function deleteRowByPrimaryKey() {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }

}