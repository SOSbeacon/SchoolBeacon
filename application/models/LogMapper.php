<?php

/**
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_LogMapper {

    protected $_dbTable;

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

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Log');
        }
        return $this->_dbTable;
    }

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Sos_Model_Smslog();
            $entry->setId($row->id)
                  ->setTimestamp($row->timestamp)
                  ->setPriority($row->priority)
                  ->setMessage($row->message)
                  ->setMapper($this);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function fetchList($where=null, $order=null, $count=null, $offset=null)
    {
            $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
            $entries   = array();
            foreach ($resultSet as $row)
            {
                    $entry = new Sos_Model_Log();
                    $entry->setId($row->id)
                          ->setTimestamp($row->timestamp)
                          ->setPriority($row->priority)
                          ->setMessage($row->message)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }

    
}
