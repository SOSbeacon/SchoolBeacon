<?php

/**
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
class Sos_Model_AlertloggroupMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Alertloggroup
     *
     * @var Sos_Model_DbTable_Alertloggroup     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertloggroup $cls
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
                ->setGroupId($row->group_id)
                ->setStatus($row->status)
                ->setToken($row->token)
                ->setCreatedDate($row->created_date)
                ->setLastUpdated($row->last_updated);
        return $cls;
    }

    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Alertloggroup $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
            'id' => $cls->getId(),
            'phone_id' => $cls->getPhoneId(),
            'group_id' => $cls->getGroupId(),
            'status' => $cls->getStatus(),
            'token' => $cls->getToken(),
            'created_date' => $cls->getCreatedDate(),
            'last_updated' => $cls->getLastUpdated(),
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertloggroup $cls
     * @return array
     */
    public function findByField($field, $value, $cls) {
        $table = $this->getDbTable();
        $select = $table->select();
        $result = array();
        $rows = $table->fetchAll($select->where("{$field} = ?", $value));
        foreach ($rows as $row) {
            $cls = new Sos_Model_Alertloggroup();
            $result[] = $cls;
            $cls->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setGroupId($row->group_id)
                    ->setStatus($row->status)
                    ->setToken($row->token)
                    ->setCreatedDate($row->created_date)
                    ->setLastUpdated($row->last_updated);
        }
        return $result;
    }

    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Alertloggroup $dbTable
     * @return Sos_Model_AlertloggroupMapper
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
     * @return Sos_Model_DbTable_Alertloggroup     
     */
    public function getDbTable() {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Alertloggroup');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Alertloggroup $cls
     *
     */
    public function save(Sos_Model_Alertloggroup $cls, $ignoreEmptyValuesOnUpdate=true) {
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
     * @param Sos_Model_Alertloggroup $cls
     */
    public function find($id, Sos_Model_Alertloggroup $cls) {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
                ->setPhoneId($row->phone_id)
                ->setGroupId($row->group_id)
                ->setStatus($row->status)
                ->setToken($row->token)
                ->setCreatedDate($row->created_date)
                ->setLastUpdated($row->last_updated);
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
            $entry = new Sos_Model_Alertloggroup();
            $entry->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setGroupId($row->group_id)
                    ->setStatus($row->status)
                    ->setToken($row->token)
                    ->setCreatedDate($row->created_date)
                    ->setLastUpdated($row->last_updated)
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
            $entry = new Sos_Model_Alertloggroup();
            $entry->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setGroupId($row->group_id)
                    ->setStatus($row->status)
                    ->setToken($row->token)
                    ->setCreatedDate($row->created_date)
                    ->setLastUpdated($row->last_updated)
                    ->setMapper($this);
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     * Find last record by field
     * @param $field
     * @param $value
     * @return object Sos_Model_Alertloggroup;
     */
    public function findLastByField($field, $value) {
        $where = "$field = $value";
        $resultSet = $this->getDbTable()->fetchAll($where, 'id DESC', 1, 0);
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Sos_Model_Alertloggroup();
            $entry->setId($row->id)
                    ->setPhoneId($row->phone_id)
                    ->setGroupId($row->group_id)
                    ->setStatus($row->status)
                    ->setToken($row->token)
                    ->setCreatedDate($row->created_date)
                    ->setLastUpdated($row->last_updated)
                    ->setMapper($this);
            $entries[] = $entry;
        }
        return (count($entries) > 0) ? $entries[0] : null;
    }

    /**
     * find the most recent alert that in the same session ( less than 4 hours old)

     * @return object Sos_Model_Alertloggroup
     */
    public function findInSessionAlert($phoneId) {
        $cls = new Sos_Model_Alertloggroup();
        $db = $this->getDbTable()->getDefaultAdapter();
        $preparedPhoneId = $db->quote($phoneId);
        $currentData = date("Y-m-d H:i:s");
        $sql = "select  * from alertloggroup
                where id = (select max(id) from alertloggroup 
                    where phone_id = $preparedPhoneId)
                    and TIMESTAMPDIFF(MINUTE, last_updated, '$currentData')  <= 15";
        $result = $db->fetchAll($sql);
        if ($result) {
            $cls->setId($result[0]['id'])
                    ->setPhoneId($result[0]['phone_id'])
                    ->setGroupId($result[0]['group_id'])
                    ->setStatus($result[0]['status'])
                    ->setToken($result[0]['token'])
                    ->setCreatedDate($result[0]['created_date'])
                    ->setLastUpdated($result[0]['last_updated'])
                    ->setMapper($this);
        }
        return $cls;
    }

    public function findLastByPhoneId($id) {
        $alertloggroupMapper = new Sos_Model_AlertloggroupMapper();
        $where = ($id == null) ? "phone_id = null" : "phone_id = $id";
        $rows = $alertloggroupMapper->fetchList($where, "id DESC");
        $alertloggroup = new Sos_Model_Alertloggroup();
        if (count($rows) > 0) {
            $alertloggroup = $rows[0];
        }
        return $alertloggroup;
    }

    /**
     * find the most recent alert that in the same session ( less than 4 hours old)
     * @todo IF alert in one Session, Not create new alertloggroup, just create new alertloggroup
     * @param $cls
     * @return Sos_Model_Alertloggroup
     */
    public function getOneBySession($phoneId, Sos_Model_Alertloggroup $alertloggroup, $groupId = '') {
        $alertloggroupMap = new Sos_Model_AlertloggroupMapper();
        $db = $this->getDbTable()->getDefaultAdapter();
        $preparedPhoneId = $db->quote($phoneId);
        $groupId = intval($groupId);
        $result = null;
        $currentData = date("Y-m-d H:i:s");
        if ($groupId != -1) { // if not single contact
            $sql = "select  * from alertloggroup
                    where id = (select max(id) from alertloggroup 
                        where phone_id = $preparedPhoneId)
                            and TIMESTAMPDIFF(MINUTE, last_updated, '$currentData')  <= 120";
            $result = $db->fetchAll($sql);
        }
        $isSessionExist = false;
        if ($result) {
            $logData = $result[0];
            if (is_array($logData)) {
               if ($groupId == intval($logData['group_id'])) {
                    $alertloggroup->setId($logData['id'])
                        ->setPhoneId($logData['phone_id'])
                        ->setGroupId($logData['group_id'])
                        ->setStatus($logData['status'])
                        ->setToken($logData['token'])
                        ->setCreatedDate($logData['created_date'])
                        ->setLastUpdated(date("Y-m-d H:i:s"))
                        ->setMapper($this);
                    $alertloggroupMap->save($alertloggroup);
                    $isSessionExist = true;
               }
            }
            if (is_object($logData)) {
                if ($groupId == intval($logData->group_id)) {
                    $alertloggroup->setId($logData->id)
                        ->setPhoneId($logData->phone_id)
                        ->setGroupId($logData->group_id)
                        ->setStatus($logData->status)
                        ->setToken($logData->token)
                        ->setCreatedDate($logData->created_date)
                        ->setLastUpdated(date("Y-m-d H:i:s"))
                        ->setMapper($this);
                    $alertloggroupMap->save($alertloggroup);
                    $isSessionExist = true;
                }
            }
        } 
        if (!$isSessionExist) {
            //CREATE NEW ALERTLOGGROUP    
            $alertloggroup->setPhoneId($phoneId);
            $alertloggroup->setGroupId($groupId);
            $alertloggroup->setStatus('Open');
            $alertloggroup->setToken(Sos_Helper_Encryption::encode($phoneId . time(), 6));
            $alertloggroup->setCreatedDate(date("Y-m-d H:i:s"));
            $alertloggroup->setLastUpdated(date("Y-m-d H:i:s"));
            $alertloggroupMap->save($alertloggroup);
        }
    }
}