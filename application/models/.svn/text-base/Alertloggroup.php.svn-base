<?php

require_once('MainModel.php');

/**
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
class Sos_Model_Alertloggroup extends MainModel {

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_PhoneId;

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_GroupId;
    
    /**
     * mysql var type varchar(10)
     *
     * @var string     
     */
    protected $_Status;

    /**
     * mysql var type varchar(255)
     *
     * @var string     
     */
    protected $_Token;

    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_CreatedDate;

    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_LastUpdated;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'phone_id' => 'PhoneId',
            'group_id' => 'GroupId',
            'status' => 'Status',
            'token' => 'Token',
            'created_date' => 'CreatedDate',
            'last_updated' => 'LastUpdated',
        ));
    }

    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setId($data) {
        $this->_Id = $data;
        return $this;
    }

    /**
     * gets column id type int(11)
     * @return int     
     */
    public function getId() {
        return $this->_Id;
    }

    /**
     * sets column phone_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setPhoneId($data) {
        $this->_PhoneId = $data;
        return $this;
    }

    /**
     * gets column phone_id type int(11)
     * @return int     
     */
    public function getPhoneId() {
        return $this->_PhoneId;
    }

    public function setGroupId($data) {
        $this->_GroupId = $data;
        return $this;
    }

    public function getGroupId() {
        return $this->_GroupId;
    }
    
    /**
     * sets column status type varchar(10)     
     *
     * @param string $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setStatus($data) {
        $this->_Status = $data;
        return $this;
    }

    /**
     * gets column status type varchar(10)
     * @return string     
     */
    public function getStatus() {
        return $this->_Status;
    }

    /**
     * sets column token type varchar(255)     
     *
     * @param string $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setToken($data) {
        $this->_Token = $data;
        return $this;
    }

    /**
     * gets column token type varchar(255)
     * @return string     
     */
    public function getToken() {
        return $this->_Token;
    }

    /**
     * sets column created_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setCreatedDate($data) {
        $this->_CreatedDate = $data;
        return $this;
    }

    /**
     * gets column created_date type datetime
     * @return datetime     
     */
    public function getCreatedDate() {
        return $this->_CreatedDate;
    }

    /**
     * sets column last_updated type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Alertloggroup     
     *
     * */
    public function setLastUpdated($data) {
        $this->_LastUpdated = $data;
        return $this;
    }

    /**
     * gets column last_updated type datetime
     * @return datetime     
     */
    public function getLastUpdated() {
        return $this->_LastUpdated;
    }

    /**
     * returns the mapper class
     *
     * @return Sos_Model_AlertloggroupMapper
     *
     */
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_AlertloggroupMapper());
        }
        return $this->_mapper;
    }

    /**
     * deletes current row by deleting a row that matches the primary key
     * 
     * @return int
     */
    public function deleteRowByPrimaryKey() {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }

}