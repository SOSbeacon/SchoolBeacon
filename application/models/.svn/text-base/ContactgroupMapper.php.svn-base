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
        $group = new Sos_Model_Contactgroup();
        $result = $mapper->findByField('phone_id', $phoneId, $group);
        $groups = array();
        foreach ($result as $key => $row) {
            $arr = array();
            $arr['id'] = $row->getId();
            $arr['name'] = $row->getName();
            $arr['type'] = $row->getType();
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
        if (!$name) {
            $isValid = false;
            $message .= ' Group name is required.';
        }
        $mapper = new Sos_Model_ContactgroupMapper();
        $group = new Sos_Model_Contactgroup();
        if ($groupId) { // edit group
            $group->find($groupId);
            if ($group->getId() && (intval($group->getType()) < 3)) {
                $isValid = false;
                $message .= ' Cannot edit default group.';
            }
        }
        if ($group->getName() != $name && !$this->checkGroupNameValid($phoneId, $name)) {
            $isValid = false;
            $message .= ' Duplicate group names not allowed.';
        }
        if ($isValid) {
            $group->setName($name);
            $group->setPhoneId($phoneId);
            $group->setType('3'); // type = 3 : group create by user
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

}
