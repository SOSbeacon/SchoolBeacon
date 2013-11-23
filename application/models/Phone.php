<?php

require_once('MainModel.php');

class Sos_Model_Phone extends MainModel {

    protected $_Id;
    protected $_Name;
    protected $_Number;
    protected $_Email;
    protected $_Password;
    protected $_CreatedDate;
    protected $_ModifiedDate;
    protected $_EmailEnabled;
    protected $_Subscribe;
    protected $_Type;
    protected $_Imei;
    protected $_LocationId;
    protected $_SettingId;
    protected $_Token;
    protected $_Status;
    protected $_Role;
    protected $_Setting;
    protected $_PhoneInfo;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'name' => 'Name',
            'number' => 'Number',
            'email' => 'Email',
            'password' => 'Password',
            'created_date' => 'CreatedDate',
            'modified_date' => 'ModifiedDate',
            'subscribe' => 'Subscribe',
            'email_enabled' => 'EmailEnabled',
            'type' => 'Type',
            'imei' => 'Imei',
            'location_id' => 'LocationId',
            'setting_id' => 'SettingId',
            'token' => 'Token',
            'status' => 'Status',
            'role' => 'Role',
            'phone_info' => 'PhoneInfo',
        ));
    }

    public function setId($data) {
        $this->_Id = $data;
        return $this;
    }

    public function getId() {
        return $this->_Id;
    }

    public function setName($data) {
        $this->_Name = $data;
        return $this;
    }

    public function getName() {
        return $this->_Name;
    }

    public function setNumber($data) {
        $this->_Number = $data;
        return $this;
    }

    public function getNumber() {
        return $this->_Number;
    }
    
    public function setEmail($data) {
        $this->_Email = $data;
        return $this;
    }

    public function getEmail() {
        return $this->_Email;
    }
    
    public function setPassword($data) {
        $this->_Password = $data;
        return $this;
    }

    public function getPassword() {
        return $this->_Password;
    }
    
    public function setCreatedDate($data) {
        $this->_CreatedDate = $data;
        return $this;
    }

    public function getCreatedDate() {
        return $this->_CreatedDate;
    }
    
    public function setModifiedDate($data) {
        $this->_ModifiedDate = $data;
        return $this;
    }

    public function getModifiedDate() {
        return $this->_ModifiedDate;
    }
    
    public function setSubscribe($data) {
        $this->_Subscribe = $data;
        return $this;
    }

    public function getSubscribe() {
        return $this->_Subscribe;
    }
    
    public function setEmailEnabled($data) {
        $this->_EmailEnabled = $data;
        return $this;
    }

    public function getEmailEnabled() {
        return $this->_EmailEnabled;
    }

    public function setType($data) {
        $this->_Type = $data;
        return $this;
    }

    public function getType() {
        return $this->_Type;
    }

    public function setImei($data) {
        $this->_Imei = $data;
        return $this;
    }

    public function getImei() {
        return $this->_Imei;
    }

    public function setLocationId($data) {
        $this->_LocationId = $data;
        return $this;
    }

    public function getLocationId() {
        return $this->_LocationId;
    }

    public function setSettingId($data) {
        $this->_SettingId = $data;
        return $this;
    }

    public function getSettingId() {
        return $this->_SettingId;
    }

    public function getPhoneInfo() {
        return $this->_PhoneInfo;
    }

    public function setPhoneInfo($data) {
        $this->_PhoneInfo = $data;
        return $this;
    }
    
    public function setSetting($data) {
        $this->_Setting = $data;
        return $this;
    }
    
    
    /**
     *
     * @return Sos_Model_Setting
     */
    public function getSetting() {
        return $this->_Setting;
    }
    
    public function setToken($data) {
        $this->_Token = $data;
        return $this;
    }

    public function getToken() {
        return $this->_Token;
    }

    public function setStatus($data) {
        $this->_Status = $data;
        return $this;
    }

    public function getStatus() {
        return $this->_Status;
    }

    public function setRole($data) {
        $this->_Role = $data;
        return $this;
    }

    public function getRole() {
        return $this->_Role;
    }
    
    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_PhoneMapper());
        }
        return $this->_mapper;
    }

    public function findGoodSamaritan($phoneId=NULL, $latitude, $longtitude) {
        $phoneMapper = new Sos_Model_PhoneMapper();
        $result = $phoneMapper->fetchByLatLon($phoneId, $latitude, $longtitude);
        $phones = Array();
        foreach ($result as $row) {
            $cls = new Sos_Model_Phone();
            $cls->setId($row->id)
                ->setName($row->name)
                ->setNumber($row->number)
                ->setEmail($row->email)
                ->setPassword($row->password)
                ->setCreatedDate($row->created_date)
                ->setModifiedDate($row->modified_date)
                ->setSubscribe($row->subscribe)
                ->setEmailEnabled($row->email_enabled)
                ->setType($row->type)
                ->setImei($row->imei)
                ->setLocationId($row->location_id)
                ->setSettingId($row->setting_id)
                ->setToken($row->token)
                ->setStatus($row->status)
                ->setRole($row->role)
                ;
            $phones[] = $cls;
        }
        return $phones;
    }

    public function deleteRowByPrimaryKey() {
        if (!$this->getId()) {
            throw new Exception('Primary Key does not contain a value');
        }
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }
}