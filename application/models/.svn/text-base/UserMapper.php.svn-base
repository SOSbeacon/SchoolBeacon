<?php
//
///**
// * Add your description here
// *
// * @author thomas
// * @copyright ZF model generator
// * @license http://framework.zend.com/license/new-bsd     New BSD License
// */
//
//class Sos_Model_UserMapper {
//
//    /**
//     * $_dbTable - instance of Sos_Model_DbTable_User
//     *
//     * @var Sos_Model_DbTable_User     
//     */
//    protected $_dbTable;
//
//    /**
//     * finds a row where $field equals $value
//     *
//     * @param string $field
//     * @param mixed $value
//     * @param Sos_Model_User $cls
//     */     
//    public function findOneByField($field, $value, $cls)
//    {
//            $table = $this->getDbTable();
//            $select = $table->select();
//
//            $row = $table->fetchRow($select->where("{$field} = ?", $value));
//            if (0 == count($row)) {
//                    return;
//            }
//
//            $cls->setId($row->id)
//		->setEmail($row->email)
//		->setPassword($row->password)
//		->setToken($row->token)
//		->setName($row->name)
//		->setAddress($row->address)
//		->setCreatedDate($row->created_date)
//		->setModifiedDate($row->modified_date)
//                ->setReceiveEmail($row->receive_email)
//                ->setUnsubscribe($row->unsubscribe);
//	    return $cls;
//    }
//
//
//    /**
//     * returns an array, keys are the field names.
//     *
//     * @param new Sos_Model_User $cls
//     * @return array
//     *
//     */
//    public function toArray($cls) {
//        $result = array(
//            'id' => $cls->getId(),
//            'email' => $cls->getEmail(),
//            'password' => $cls->getPassword(),
//            'token' => $cls->getToken(),
//            'name' => $cls->getName(),
//            'address' => $cls->getAddress(),
//            'created_date' => $cls->getCreatedDate(),
//            'modified_date' => $cls->getModifiedDate(),
//            'receive_email' => $cls->getReceiveEmail(),
//            'unsubscribe'   => $cls->getUnsubscribe(),
//        );
//        return $result;
//    }
//
//    /**
//     * finds rows where $field equals $value
//     *
//     * @param string $field
//     * @param mixed $value
//     * @param Sos_Model_User $cls
//     * @return array
//     */
//    public function findByField($field, $value, $cls)
//    {
//            $table = $this->getDbTable();
//            $select = $table->select();
//            $result = array();
//
//            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
//            foreach ($rows as $row) {
//                $cls=new Sos_Model_User();
//                $result[]=$cls;
//                $cls->setId($row->id)
//                    ->setEmail($row->email)
//                    ->setPassword($row->password)
//                    ->setToken($row->token)
//                    ->setName($row->name)
//                    ->setAddress($row->address)
//                    ->setCreatedDate($row->created_date)
//                    ->setModifiedDate($row->modified_date)
//                    ->setReceiveEmail($row->receive_email)
//                    ->setUnsubscribe($row->unsubscribe);;
//            }
//            return $result;
//    }
//
//    
//    /**
//     * finds rows where $field equals $value
//     *
//     * @param string $field1,$field2
//     * @param mixed $value1,$value2
//     * @param Sos_Model_User $cls
//     * @return array
//     */
//    public function findByTwoField($field1, $field2, $value1, $value2, $cls)
//    {
//        $table = $this->getDbTable();
//        $select = $table->select();
//        $result = array();
//
//        $rows = $table->fetchAll(
//        $select->where("{$field1} = ?", $value1)
//               ->where("{$field2} = ?", $value2)
//        );
//        foreach ($rows as $row) {
//            $cls=new Sos_Model_User();
//            $result[]=$cls;
//            $cls->setId($row->id)
//                ->setEmail($row->email)
//                ->setPassword($row->password)
//                ->setToken($row->token)
//                ->setName($row->name)
//                ->setAddress($row->address)
//                ->setCreatedDate($row->created_date)
//                ->setModifiedDate($row->modified_date)
//                ->setReceiveEmail($row->receive_email)
//                ->setUnsubscribe($row->unsubscribe);
//        }
//        return $result;
//    }
//        
//    
//    /**
//     * sets the dbTable class
//     *
//     * @param Sos_Model_DbTable_User $dbTable
//     * @return Sos_Model_UserMapper
//     * 
//     */
//    public function setDbTable($dbTable)
//    {
//        if (is_string($dbTable)) {
//            $dbTable = new $dbTable();
//        }
//        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
//            throw new Exception('Invalid table data gateway provided');
//        }
//        $this->_dbTable = $dbTable;
//        return $this;
//    }
//
//    /**
//     * returns the dbTable class
//     * 
//     * @return Sos_Model_DbTable_User     
//     */
//    public function getDbTable()
//    {
//        if (null === $this->_dbTable) {
//            $this->setDbTable('Sos_Model_DbTable_User');
//        }
//        return $this->_dbTable;
//    }
//
//    /**
//     * saves current row
//     *
//     * @param Sos_Model_User $cls
//     *
//     */
//     
//    public function save(Sos_Model_User $cls,$ignoreEmptyValuesOnUpdate=true)
//    {
//        if ($ignoreEmptyValuesOnUpdate) {
//            $data = $cls->toArray();
//            foreach ($data as $key=>$value) {
//                if (is_null($value) or $value == '')
//                    unset($data[$key]);
//            }
//        }
//
//        if (null === ($id = $cls->getId())) {
//            unset($data['id']);
//            $id=$this->getDbTable()->insert($data);
//            $cls->setId($id);
//        } else {
//            if ($ignoreEmptyValuesOnUpdate) {
//             $data = $cls->toArray();
//             foreach ($data as $key=>$value) {
//                if (is_null($value) or $value == '')
//                    unset($data[$key]);
//                }
//            }
//            $this->getDbTable()->update($data, array('id = ?' => $id));
//        }
//    }
//
//    /**
//     * finds row by primary key
//     *
//     * @param int $id
//     * @param Sos_Model_User $cls
//     */
//
//    public function find($id, Sos_Model_User $cls)
//    {
//        $result = $this->getDbTable()->find($id);
//        if (0 == count($result)) {
//            return;
//        }
//
//        $row = $result->current();
//
//        $cls->setId($row->id)
//            ->setEmail($row->email)
//            ->setPassword($row->password)
//            ->setToken($row->token)
//            ->setName($row->name)
//            ->setAddress($row->address)
//            ->setCreatedDate($row->created_date)
//            ->setModifiedDate($row->modified_date)
//            ->setReceiveEmail($row->receive_email)
//            ->setUnsubscribe($row->unsubscribe);
//    }
//
//    /**
//     * fetches all rows 
//     *
//     * @return array
//     */
//    public function fetchAll()
//    {
//        $resultSet = $this->getDbTable()->fetchAll();
//        $entries   = array();
//        foreach ($resultSet as $row) {
//            $entry = new Sos_Model_User();
//            $entry->setId($row->id)
//                  ->setEmail($row->email)
//                  ->setPassword($row->password)
//                  ->setToken($row->token)
//                  ->setName($row->name)
//                  ->setAddress($row->address)
//                  ->setCreatedDate($row->created_date)
//                  ->setModifiedDate($row->modified_date)
//                  ->setReceiveEmail($row->receive_email)
//                  ->setUnsubscribe($row->unsubscribe)
//                  ->setMapper($this);
//            $entries[] = $entry;
//        }
//        return $entries;
//    }
//
//    /**
//     * fetches all rows optionally filtered by where,order,count and offset
//     * 
//     * @param string $where
//     * @param string $order
//     * @param int $count
//     * @param int $offset 
//     *
//     */
//    public function fetchList($where=null, $order=null, $count=null, $offset=null)
//    {
//            $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
//            $entries   = array();
//            foreach ($resultSet as $row)
//            {
//                $entry = new Sos_Model_User();
//                $entry->setId($row->id)
//                      ->setEmail($row->email)
//                      ->setPassword($row->password)
//                      ->setToken($row->token)
//                      ->setName($row->name)
//                      ->setAddress($row->address)
//                      ->setCreatedDate($row->created_date)
//                      ->setModifiedDate($row->modified_date)
//                      ->setReceiveEmail($row->receive_email)
//                      ->setUnsubscribe($row->unsubscribe)
//                      ->setMapper($this);
//                $entries[] = $entry;
//            }
//            return $entries;
//    }
//    
//    public function getPhoneList() {
//    	
//    	$db = $this->getDbTable()->getDefaultAdapter();
//    	
//    	$sql = "SELECT u.id AS uid, u.receive_email AS receive_email, u.unsubscribe AS unsubscribe, p.number AS number, p.name AS name, p.id AS pid, p.type AS type, p.status AS status
//                FROM phone p, user u
//                WHERE p.user_id = u.id
//                ORDER BY u.id";
//    	
//    	$resultSet = $db->fetchAll($sql);
//        $names = array();
//        $pids = array();
//        $numbers = array();
//        $phones = array();
//        foreach ($resultSet as $row) {
//            $phones[$row["uid"]][] = array('pid' => $row['pid'],
//                                           'name' => $row['name'],
//                                           'number' => $row['number'],
//                                           'type' => ($row['type'] == '1' ? 'IPhone' : ($row["type"] == '2' ? 'Android' : 'Unknown')),
//                                           'status' => $row['status'],
//                                           'receive_email' => $row['receive_email'],
//                                           'unsubscribe' => $row['unsubscribe']
//                                       );
//        }
//        return $phones;
//    }
//}