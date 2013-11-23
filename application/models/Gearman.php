<?php

require_once('MainModel.php');

class Sos_Model_Gearman extends MainModel {

    protected $_Id;
    protected $_Argument;
    protected $_Type;
    protected $_Status;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'argument' => 'Argument',
            'type' => 'Type',
            'status' => 'Status',
        ));
    }

    public function setId($data) {
        $this->_Id = $data;
        return $this;
    }

    public function getId() {
        return $this->_Id;
    }

    public function setArgument($data) {
        $this->_Argument = $data;
        return $this;
    }

    public function getArgument() {
        return $this->_Argument;
    }

    public function setType($data)
    {
        $this->_Type=$data;
        return $this;
    }
    
    public function getType()
    {
        return $this->_Type;
    }
    
    public function setStatus($data)
    {
        $this->_Status=$data;
        return $this;
    }
    
    public function getStatus()
    {
        return $this->_Status;
    }
    
    /**
     * @return Sos_Model_GearmanMapper
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_GearmanMapper());
        }
        return $this->_mapper;
    }

    public function deleteRowByPrimaryKey() {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }

}
