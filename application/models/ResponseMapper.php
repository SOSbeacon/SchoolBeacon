<?php

class Sos_Model_ResponseMapper {

    protected $_dbTable;

    private function _setValues(Sos_Model_Response $obj, $values) {
        $obj
            ->setId($values->id)
            ->setAlertGroupId($values->alert_group_id)
            ->setContactId($values->contact_id)
            ->setReplyId($values->reply_id)
            ->setName($values->name)
            ->setEmail($values->email)
            ->setNumber($values->number)
            ->setReceiveEmail($values->receive_email)
            ->setReceiveSms($values->receive_sms)
            ->setOpenLink($values->open_link)
            ->setResponseChat($values->response_chat);
        return $obj;
    }
    
    public function findOneByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();
        $row = $table->fetchRow($select->where("{$field} = ?", $value));
        if (count($row)) {
            return $this->_setValues($cls, $row);
        }
        return false;
    }

    public function toArray(Sos_Model_Response $cls) {
        $result = array(
            'id' => $cls->getId(),
            'alert_group_id' => $cls->getAlertGroupId(),
            'contact_id' => $cls->getContactId(),
            'reply_id' => $cls->getReplyId(),
            'name' => $cls->getName(),
            'email' => $cls->getEmail(),
            'number' => $cls->getNumber(),
            'receive_email' => $cls->getReceiveEmail(),
            'receive_sms' => $cls->getReceiveSms(),
            'open_link' => $cls->getOpenLink(),
            'response_chat'=> $cls->getResponseChat(),
        );
        return $result;
    }

    public function findByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();
        $result = array();

        $rows = $table->fetchAll($select->where("{$field} = ?", $value));
        foreach ($rows as $row) {
            $cls = new Sos_Model_Response();
            $result[] = $this->_setValues($cls, $row);
        }
        return $result;
    }

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

    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Response');
        }
        return $this->_dbTable;
    }

    public function save(Sos_Model_Response $cls, $ignoreEmptyValuesOnUpdate=true) {
        if ($ignoreEmptyValuesOnUpdate) {
            $data = $cls->toArray();
            foreach ($data as $key => $value) {
                if (is_null($value) or $value == '') {
                    unset($data[$key]);
                }
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

    public function find($id, Sos_Model_Response $cls) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $cls = $this->_setValues($cls, $row);
    }

    public function fetchAll() {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $cls = new Sos_Model_Response();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }

    public function fetchList($where=null, $order=null, $count=null, $offset=null) {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $cls = new Sos_Model_Response();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }
    
    public function findByAlertGroup($alertGroupId, $contactId = 0, $number = 0) {
        $cls = new Sos_Model_Response();
        if ($alertGroupId) {
            $where = '';
            if ($contactId) {
                $where = "(alert_group_id = $alertGroupId) AND (contact_id = $contactId)";
            } else {
                if ($number) {
                    $where = "(alert_group_id = $alertGroupId) AND (number = \"$number\")"; 
                }
            }
            if ($where) {
                $result = $this->getDbTable()->fetchAll($where, null, null, null);
                if (count($result)) {
                    $this->_setValues($cls, $result[0]);
                }
            }
        }
        return $cls;
    }
}