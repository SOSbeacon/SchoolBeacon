<?php

/**
 * Add your description here
 *
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_AlertnoteMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Alertnote
     *
     * @var Sos_Model_DbTable_Alertnote     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertnote $cls
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
		->setAlertlogId($row->alertlog_id)
		->setFrom($row->from)
		->setMessage($row->message)
		->setCreatedDate($row->created_date);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Alertnote $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'alertlog_id' => $cls->getAlertlogId(),
            'from' => $cls->getFrom(),
            'message' => $cls->getMessage(),
            'created_date' => $cls->getCreatedDate(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertnote $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Alertnote();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setAlertlogId($row->alertlog_id)
		->setFrom($row->from)
		->setMessage($row->message)
		->setCreatedDate($row->created_date);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Alertnote $dbTable
     * @return Sos_Model_AlertnoteMapper
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
     * @return Sos_Model_DbTable_Alertnote     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Alertnote');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Alertnote $cls
     *
     */
     
    public function save(Sos_Model_Alertnote $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * @param Sos_Model_Alertnote $cls
     */

    public function find($id, Sos_Model_Alertnote $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setAlertlogId($row->alertlog_id)
		->setFrom($row->from)
		->setMessage($row->message)
		->setCreatedDate($row->created_date);
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
            $entry = new Sos_Model_Alertnote();
            $entry->setId($row->id)
                  ->setAlertlogId($row->alertlog_id)
                  ->setFrom($row->from)
                  ->setMessage($row->message)
                  ->setCreatedDate($row->created_date)
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
                    $entry = new Sos_Model_Alertnote();
                    $entry->setId($row->id)
                          ->setAlertlogId($row->alertlog_id)
                          ->setFrom($row->from)
                          ->setMessage($row->message)
                          ->setCreatedDate($row->created_date)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }

    /**
     * DELETE ROW BY FIELD
     * @param unknown_type $field
     * @param unknown_type $value
     * @param unknown_type $cls
     */
    public function deleteByField($field, $value)
    {
            $table = $this->getDbTable()->getDefaultAdapter();
            $where = "$field = ".$table->quote($value);
            $countDel = $table->delete('alertnote',$where);
            
            return $countDel;
    }
}
