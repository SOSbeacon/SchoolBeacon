<?php

class Sos_Model_StudentgroupMapper {

    protected $_dbTable;

    private function _setValues(Sos_Model_Studentgroup $obj, $values) {
        $obj
            ->setId($values->id)
            ->setStudentId($values->student_id)
            ->setGroupId($values->group_id);
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

    public function toArray(Sos_Model_Studentgroup $cls) {
        $result = array(
            'id' => $cls->getId(),
            'student_id' => $cls->getStudentId(),
            'group_id' => $cls->getGroupId(),
        );
        return $result;
    }

    public function findByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();
        $result = array();

        $rows = $table->fetchAll($select->where("{$field} = ?", $value));
        foreach ($rows as $row) {
            $cls = new Sos_Model_Studentgroup();
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
            $this->setDbTable('Sos_Model_DbTable_Studentgroup');
        }
        return $this->_dbTable;
    }

    public function save(Sos_Model_Studentgroup $cls, $ignoreEmptyValuesOnUpdate=true) {
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

    public function find($id, Sos_Model_Studentgroup $cls) {
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
            $cls = new Sos_Model_Studentgroup();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }

    public function fetchList($where=null, $order=null, $count=null, $offset=null) {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $cls = new Sos_Model_Studentgroup();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }

}
