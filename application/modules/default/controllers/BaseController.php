<?php
abstract class BaseController extends Zend_Rest_Controller {

    public function authorizePhone($token, $phoneId) {
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phone = new Sos_Model_Phone();
        $phoneMapper->findOneByField('token', $token, $phone);
        if ($phone->getId() != $phoneId)
            throw new Zend_Acl_Exception("phoneId $phoneId or token $token are incorrect");
        return true;
    }
    
    public function authorizeGroup($token, $groupId) {
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $group = new Sos_Model_Contactgroup();
        $groupMapper->findOneByField('id', $groupId, $group);
        if ($group->getId() != $groupId)
            throw new Zend_Acl_Exception("group is incorrect");
        else {
            $phoneId = $group->getPhoneId();
            $this->authorizePhone($token, $phoneId);
        }
        return true;
    }
    
    public function authorizeContact($token, $contactId) {        
        $contactMapper = new Sos_Model_ContactMapper();
        $contact = new Sos_Model_Contact();
        $contactMapper->findOneByField('id', $contactId, $contact);
        if ($contact->getId() != $contactId)
            throw new Zend_Acl_Exception("contact is incorrect");
        else {
            $groupId = $contact->getGroupId();
            $this->authorizeGroup($token, $groupId);
        }        
        return true;
    }
    
    public function authorizeSetting($token, $settingId) {        
        $settingMapper = new Sos_Model_SettingMapper();
        $setting = new Sos_Model_Setting();
        $settingMapper->findOneByField('id', $settingId, $setting);
        
        if ($setting->getId() != $settingId)
            throw new Zend_Acl_Exception("contact is incorrect");
            
        return true;
    }
}