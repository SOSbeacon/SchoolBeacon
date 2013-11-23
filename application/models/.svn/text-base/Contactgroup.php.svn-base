<?php

require_once('MainModel.php');

class Sos_Model_Contactgroup extends MainModel {

    protected $_Id;
    protected $_PhoneId;
    protected $_Name;
    protected $_Type;

    function __construct() {
        $this->setColumnsList(array(
            'id' => 'Id',
            'phone_id' => 'PhoneId',
            'name' => 'Name',
            'type' => 'Type',
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

    public function setName($data) {
        $this->_Name = $data;
        return $this;
    }

    public function getName() {
        return $this->_Name;
    }

    public function setType($data) {
        $this->_Type = $data;
        return $this;
    }

    public function getType() {
        return $this->_Type;
    }

    public function getMapper() {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_ContactgroupMapper());
        }
        return $this->_mapper;
    }

    public function deleteRowByPrimaryKey() {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = ' . $this->getId());
    }

    public function createDefaultGroup(Sos_Model_Phone $phone) {
        $mapper = new Sos_Model_ContactgroupMapper();
        $contactMapper = new Sos_Model_ContactMapper();
        
        $phoneId = $phone->getId();
        $number = $phone->getNumber();
        $name =  $phone->getName() ? ucfirst($phone->getName()) . ' ' : '';
        $email = $phone->getEmail() ? $phone->getEmail() : '';
        //Create contact group
        $group1 = new Sos_Model_Contactgroup();
        $group1->setPhoneId($phoneId);
        $group1->setName('Family');
        $group1->setType('0');
        $mapper->save($group1);
        
        // Save defaut group in settings
        $setting = new Sos_Model_Setting();
        $setting->find($phone->getSettingId());
        if ($setting->getId()) {
            $setting->setAlertSendtoGroup($group1->getId());
            $setting->save();
        }
        
        //New default contact for this group
        $contact1 = new Sos_Model_Contact();
        $contact1->setGroupId($group1->getId());
        $contact1->setName($name . 'Family');
        $contact1->setEmail($email);
        $contact1->setTextphone($number);
        $contact1->setType(1);
        $contactMapper->save($contact1);

        $group2 = new Sos_Model_Contactgroup();
        $group2->setPhoneId($phoneId);
        $group2->setName('Friends');
        $group2->setType(1);
        $mapper->save($group2);
        //New contact for this group
        $contact2 = new Sos_Model_Contact();
        $contact2->setGroupId($group2->getId());
        $contact2->setName($name . 'Friends');
        $contact2->setEmail($email);
        $contact2->setTextphone($number);
        $contact2->setType(1);
        $contactMapper->save($contact2);

        $group3 = new Sos_Model_Contactgroup();
        $group3->setPhoneId($phoneId);
        $group3->setName('Neighborhood Watch');
        $group3->setType(2);
        $mapper->save($group3);
        //New contact for this group
        $contact3 = new Sos_Model_Contact();
        $contact3->setGroupId($group3->getId());
        $contact3->setName($name . 'Neighborhood Watch');
        $contact3->setEmail($email);
        $contact3->setTextphone($number);
        $contact3->setType(1);
        $contactMapper->save($contact3);
    }
}
