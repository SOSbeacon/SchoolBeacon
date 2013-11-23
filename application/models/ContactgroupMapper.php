<?php

class Sos_Model_ContactgroupMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Contactgroup
     *
     * @var Sos_Model_DbTable_Contactgroup     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contactgroup $cls
     */
    public function findOneByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();

        $row = $table->fetchRow($select->where("{$field} = ?", $value));
        if (0 == count($row)) {
            return;
        }

        $cls->setId($row->id)
                ->setPhoneId($row->phone_id)
                ->setName($row->name)
                ->setType($row->type);
        return $cls;
    }

    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Contactgroup $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
            'id' => $cls->getId(),
            'phone_id' => $cls->getPhoneId(),
            'name' => $cls->getName(),
            'type' => $cls->getType(),
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contactgroup $cls
     * @return array
     */
    public function findByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();
        $result = array();

        $rows = $table->fetchAll($select->where("{$field} = ?", $value));
        foreach ($rows as $row) {
            $cls = new Sos_Model_Contactgroup();
            $result[] = $cls;
            $cls->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setName($row->name)
                    ->setType($row->type);
        }
        return $result;
    }

    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Contactgroup $dbTable
     * @return Sos_Model_ContactgroupMapper
     * 
     */
    public function setDbTable($dbTable) {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * returns the dbTable class
     * 
     * @return Sos_Model_DbTable_Contactgroup     
     */
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Contactgroup');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Contactgroup $cls
     *
     */
    public function save(Sos_Model_Contactgroup $cls, $ignoreEmptyValuesOnUpdate=true) {
        if ($ignoreEmptyValuesOnUpdate) {
            $data = $cls->toArray();
            foreach ($data as $key => $value) {
                if (is_null($value) or $value == '')
                    unset($data[$key]);
            }
        }

        if (null === ($id = $cls->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $cls->setId($id);
        } else {
            if ($ignoreEmptyValuesOnUpdate) {
                $data = $cls->toArray();
                foreach ($data as $key => $value) {
                    if (is_null($value) or $value == '')
                        unset($data[$key]);
                }
            }

            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    /**
     * finds row by primary key
     *
     * @param int $id
     * @param Sos_Model_Contactgroup $cls
     */
    public function find($id, Sos_Model_Contactgroup $cls) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
                ->setPhoneId($row->phone_id)
                ->setName($row->name)
                ->setType($row->type);
    }

    /**
     * fetches all rows 
     *
     * @return array
     */
    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Sos_Model_Contactgroup();
            $entry->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setName($row->name)
                    ->setType($row->type)
                    ->setMapper($this);
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     * fetches all rows optionally filtered by where,order,count and offset
     * 
     * @param string $where
     * @param string $order
     * @param int $count
     * @param int $offset 
     *
     */
    public function fetchList($where=null, $order=null, $count=null, $offset=null) {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Sos_Model_Contactgroup();
            $entry->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setName($row->name)
                    ->setType($row->type)
                    ->setMapper($this);
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     * Check group is allow join
     * @param Sos_Model_Contactgroup $group
     */
    public function isAllowJoinGroup($group) {
        if ($group->getType() == 2 && $group->getName() == 'Neighborhood Watch') {
            return true;
        }
        return false;
    }
    
    public function checkGroupNameValid($phoneId, $name) {
        $group = new Sos_Model_Contactgroup();
        $db = Zend_Db_Table::getDefaultAdapter();
        $where = 'LOWER(name) = ' . $db->quote(strtolower($name)) . ' AND phone_id = ' . $db->quote($phoneId);
        $count = $group->countByQuery($where);
        if ($count) {
            return false;
        }
        return true;
    }

    public function getGroups($phoneId) {
        $mapper = new Sos_Model_ContactgroupMapper();
        $contact = new Sos_Model_Contact();
        $group = new Sos_Model_Contactgroup();
        $join = new Sos_Model_Groupjoin();
        $result = $mapper->findByField('phone_id', $phoneId, $group);
        $groups = array();
        foreach ($result as $key => $row) {
            $arr = array();
            $contactGroupCount = $contact->getMapper()->countContactsByPhoneId($phoneId, '', $row->getId());
            $ownerContactGroupCount = $contactGroupCount;
            $contactJoinCount = 0;
            $joinGroup = new Sos_Model_Contactgroup();
            // Get Neighborhood Watch JOIN if have
            if ($row->getType() == 2) {
                $groupJoins = $join->getMapper()->fetchList("phone_id=$phoneId AND group_id=" . $row->getId());
                if (count($groupJoins)) {
                    $joinGroupId = 0;
                    foreach ($groupJoins as $gj) {
                        $joinGroupId = $gj->getJoinGroupId();
                        $joinContacts = $contact->fetchList('group_id=' . $joinGroupId, 'type DESC');
                        if (count($joinContacts)) $contactJoinCount += count($joinContacts);
                    }
                    $joinGroup->find($joinGroupId);
                }
            }
            $contactGroupCount += $contactJoinCount;
            $arr['id'] = $row->getId();
            $arr['name'] = $joinGroup->getId() ? $joinGroup->getName() : $row->getName();
            $arr['type'] = $row->getType();
            $arr['phoneId'] = $row->getPhoneId();
            $arr['contactCount'] = $contactGroupCount;
            $arr['allowEdit'] = ($row->getType() > 2 || ($row->getType() == 2 && ($row->getName() == 'Neighborhood Watch') && $contactJoinCount == 0)) ? true : false;
            $arr['allowDelete'] = ($row->getType() > 2 || ($row->getType() == 2 && $row->getName() != 'Neighborhood Watch')) ? true : false; // && $ownerContactGroupCount > 1
            $groups[$key] = $arr;
        }
        return $groups;
    }

    /**
     * Save new / update group 
     */
    public function saveGroup($name, $phoneId, $groupId = '') {
        $success = 'false';
        $message = '';
        $response = array();
        $isValid = true;
        $mapper = new Sos_Model_ContactgroupMapper();
        $group = new Sos_Model_Contactgroup();
        if ($groupId) { // edit group
            $group->find($groupId);
            if ($group->getId() && (intval($group->getType()) < 2)) { // update: now "Neighborhood Watch" can be edit
                $isValid = false;
                $message .= ' Cannot edit default group.';
            }
            if ($group->getId() && $group->getType() == 2) { // Neighborhood Watch
                $name = 'Neighborhood Watch ' . $name;
            }
        }
        if (!$name && ($group->getType() != 3)) {
            $isValid = false;
            $message .= ' Group name is required.';
        }
        if ($group->getName() != $name && !$this->checkGroupNameValid($phoneId, $name)) {
            $isValid = false;
            $message .= ' Duplicate group names not allowed.';
        }
        // Check Neighborhood Watch JOIN
        if ($group->getType() == 2) {
            $groupJoin = new Sos_Model_Groupjoin();
            $groupJoin->getMapper()->findOneByField('join_group_id', $group->getId(), $groupJoin);
            if ($groupJoin->getId()) { // this group is currently used for join another group
                 $isValid = false;
                 $message .= ' This group is currently used for join another group so you can\'t edit it. 
                     But you can delete all contacts on this group and then create new group name.';
            }
        }
        if ($isValid) {
            $group->setName(trim($name));
            $group->setPhoneId($phoneId);
            $groupType = $group->getType() ? $group->getType() : 3;  // type = 3 : group create by user
            $group->setType($groupType);
            $mapper->save($group);
            // New/update default contact for this group
            $phone = new Sos_Model_Phone();
            $phone->find($phoneId);
            if ($phone->getId()) {
                $contact = new Sos_Model_Contact();
                $contactMapper = new Sos_Model_ContactMapper();
                if ($groupId) { // update default contact
                    $contacts = $contact->fetchList('group_id=' . $groupId . ' AND type=1');
                    if (count($contact)) {
                        $contact = $contacts[0];
                    }
                } else { // new default contact
                    $contact->setGroupId($group->getId());
                }
                $contact->setName(trim($phone->getName() . ' ' . ucfirst($name)));
                $contact->setEmail($phone->getEmail());
                $contact->setTextphone($phone->getNumber());
                $contact->setType(1);
                $contactMapper->save($contact);
                $response['id'] = $group->getId();
                $response['data'] = $this->getGroups($phone->getId());
                $success = 'true';
                $message = 'Group saved successfully';
            }
        }
        $response['success'] = $success;
        $response['message'] = $message;
        return $response;
    }
    
    /**
     * Delete all Neighbor Watch Group contacts and name
     * @param Sos_Model_Contactgroup $group
     */
    public function deleteNWGroup($group) {
        if ($group->getType() == 2) {
            $contact = new Sos_Model_Contact();
            $contacts = $contact->getMapper()->fetchList('group_id=' . $group->getId() . ' AND type=0');
            if (count($contacts)) { // Delete contacts and reset group name
                foreach($contacts as $c) {
                    $this->deleteNWJoinGroupName($group, $c);
                    $c->deleteRowByPrimaryKey();
                }
            } else { // Reset group name
                $this->saveGroup('', $group->getPhoneId(), $group->getId());
            }
        }
    }
    
    /**
     * delete Neighbor Watch Group name
     * @param Sos_Model_Contactgroup $group
     * @param Sos_Model_Contact $deleteContact
     */
    public function deleteNWJoinGroupName($group, $deleteContact) {
        $deleteId = $deleteContact->getId();
        if ($group->getType() == 2) {
            // check group is empty
            $contact = new Sos_Model_Contact();
            $count = $contact->countByQuery("id<>$deleteId AND type=0 AND group_id=" . $group->getId());
            if (!$count) {
                $this->saveGroup('', $group->getPhoneId(), $group->getId());
            }
            // check if contact is joined contact
            $groupJoin = new Sos_Model_Groupjoin();
            $groupJoin->getMapper()->findOneByField('contact_id', $deleteContact->getId(), $groupJoin);
            if ($groupJoin->getId()) { // send email notify to joined contact
                try {
                    $mail = new Sos_Service_ClassMail();
                    $subject = 'Your joined Neighbor Watch Group has been removed';
                    $mail->setSubject($subject);
                    $mail->setAddressTo($deleteContact->getEmail());
                    $mail->setAddressName($deleteContact->getName());
                    $body = 'Your joined Neighbor Watch Group has been removed from group "' . $group->getName() . '".';
                    $mail->setBody($body);
                    $mail->sendMail();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}
