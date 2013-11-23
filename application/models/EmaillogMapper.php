<?php

/**
 * Add your description here
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_EmaillogMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Emaillog
     *
     * @var Sos_Model_DbTable_Emaillog     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Emaillog $cls
     */     
    public function findOneByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();

            $row = $table->fetchRow($select->where("{$field} = ?", $value));
            if (0 == count($row)) {
                    return;
            }

            $cls->setId($row->id)
		->setFrom($row->from)
		->setTo($row->to)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setStatus($row->status);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Emaillog $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'from' => $cls->getFrom(),
            'to' => $cls->getTo(),
            'message' => $cls->getMessage(),
            'created_date' => $cls->getCreatedDate(),
            'status' => $cls->getStatus(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Emaillog $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Emaillog();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setFrom($row->from)
		->setTo($row->to)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setStatus($row->status);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Emaillog $dbTable
     * @return Sos_Model_EmaillogMapper
     * 
     */
    public function setDbTable($dbTable)
    {
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
     * @return Sos_Model_DbTable_Emaillog     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Emaillog');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Emaillog $cls
     *
     */
     
    public function save(Sos_Model_Emaillog $cls,$ignoreEmptyValuesOnUpdate=true)
    {
        if ($ignoreEmptyValuesOnUpdate) {
            $data = $cls->toArray();
            foreach ($data as $key=>$value) {
                if (is_null($value) or $value == '')
                    unset($data[$key]);
            }
        }

        if (null === ($id = $cls->getId())) {
            unset($data['id']);
            $id=$this->getDbTable()->insert($data);
            $cls->setId($id);
        } else {
            if ($ignoreEmptyValuesOnUpdate) {
             $data = $cls->toArray();
             foreach ($data as $key=>$value) {
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
     * @param Sos_Model_Emaillog $cls
     */

    public function find($id, Sos_Model_Emaillog $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setFrom($row->from)
		->setTo($row->to)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setStatus($row->status);
    }

    /**
     * fetches all rows 
     *
     * @return array
     */
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Sos_Model_Emaillog();
            $entry->setId($row->id)
                  ->setFrom($row->from)
                  ->setTo($row->to)
                  ->setMessage($row->message)
                  ->setCreatedDate($row->created_date)
                  ->setStatus($row->status)
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
    public function fetchList($where=null, $order=null, $count=null, $offset=null)
    {
            $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
            $entries   = array();
            foreach ($resultSet as $row)
            {
                    $entry = new Sos_Model_Emaillog();
                    $entry->setId($row->id)
                          ->setFrom($row->from)
                          ->setTo($row->to)
                          ->setMessage($row->message)
                          ->setCreatedDate($row->created_date)
                          ->setStatus($row->status)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }


   /**
     * SAVE NEW emaillog
     * @param $from
     * @param $to
     * @param $message
     */
    public function saveEmaillog($from, $to, $message, Sos_Model_Emaillog $smslog) {
    	$smslogMapper = new Sos_Model_EmaillogMapper();
    	$smslog->setFrom($from);
    	$smslog->setTo($to);
    	$smslog->setMessage($message);
    	$smslog->setCreatedDate(date("Y-m-d H:i:s"));
    	
    	$smslog->save();
    }
}
