<?php

class Web_GroupsController extends Zend_Controller_Action {

    public function indexAction() {
        $auth = Sos_Service_Functions::webappAuth(false);
        if (!$auth->getId()) {
            $this->_helper->getHelper('Redirector')->gotoUrl("/web/users/login/");
        }
        // get phone list
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $phoneMapper->find($auth->getId(), $phone);
        // get contact groups by phone number
        $groupModel = new Sos_Model_Contactgroup();
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $phoneId = $phone->getId();
        $groups = $groupMapper->findByField('phone_id', $phoneId, $groupModel);
        $contactGroups = array();
        foreach ($groups as $group) {
            $contact = new Sos_Model_Contact();
            $contactNumber = $contact->countByQuery('group_id = ' . $group->getId());
            $contactGroups[] = array(
                'id' => $group->getId(),
                'name' => $group->getName(),
                'number' => $contactNumber
            );
        }

        $this->view->groups = $contactGroups;
        $this->view->phoneId = $phoneId;
    }

}
