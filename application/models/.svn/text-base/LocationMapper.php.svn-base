<?php

/**
 * Add your description here
 *
 * @author thomas
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_LocationMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Location
     *
     * @var Sos_Model_DbTable_Location     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Location $cls
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
		->setLatitude($row->latitude)
		->setLongtitude($row->longtitude)
		->setAlertlogId($row->alertlog_id)
		->setUpdatedDate($row->updated_date);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Location $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'latitude' => $cls->getLatitude(),
            'longtitude' => $cls->getLongtitude(),
            'alertlog_id' => $cls->getAlertlogId(),
            'updated_date' => $cls->getUpdatedDate(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Location $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Location();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setLatitude($row->latitude)
		->setLongtitude($row->longtitude)
		->setAlertlogId($row->alertlog_id)
		->setUpdatedDate($row->updated_date);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Location $dbTable
     * @return Sos_Model_LocationMapper
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
     * @return Sos_Model_DbTable_Location     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Location');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Location $cls
     *
     */
     
    public function save(Sos_Model_Location $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * @param Sos_Model_Location $cls
     */

    public function find($id, Sos_Model_Location $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setLatitude($row->latitude)
		->setLongtitude($row->longtitude)
		->setAlertlogId($row->alertlog_id)
		->setUpdatedDate($row->updated_date);
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
            $entry = new Sos_Model_Location();
            $entry->setId($row->id)
                  ->setLatitude($row->latitude)
                  ->setLongtitude($row->longtitude)
                  ->setAlertlogId($row->alertlog_id)
                  ->setUpdatedDate($row->updated_date)
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
                    $entry = new Sos_Model_Location();
                    $entry->setId($row->id)
                          ->setLatitude($row->latitude)
                          ->setLongtitude($row->longtitude)
                          ->setAlertlogId($row->alertlog_id)
                          ->setUpdatedDate($row->updated_date)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }


	//find last location by phoneId
	public function findLastByPid($phoneId) { 
		$db 	 = $this->getDbTable()->getDefaultAdapter();		
		$phoneId = intval($phoneId);
		$sql	 = "SELECT * FROM location ";
		$sql	.=		"WHERE alertlog_id = (SELECT id FROM alertlog "; 
		$sql	.=			"WHERE alertloggroup_id = (SELECT id FROM alertloggroup "; 
		$sql	.=				"WHERE phone_id = $phoneId "; 
		$sql	.=				"ORDER BY id DESC LIMIT 1";
		$sql	.=			")";
		$sql	.=			"ORDER BY id DESC LIMIT 1";
		$sql	.=		")";
                $db->setFetchMode(Zend_Db::FETCH_OBJ);       
		$result  = $db->fetchAll($sql);
		return (count($result) > 0) ? $result[0] : null;
	}
	
	/**
	 * Find location by alertloggroup
	 * @param unknown_type $alerId
	 * @return array Sos_Model_Alertloggroup
	 */
	public function findByAlertloggroup($alerId) { 
		$db 	 = $this->getDbTable()->getDefaultAdapter();		
		$alerId = $db->quote($alerId);
		$sql	 = "SELECT * FROM location ";
		$sql	.=		"WHERE alertlog_id IN (SELECT id FROM alertlog "; 
		$sql	.=			"WHERE alertloggroup_id = $alerId ";
		$sql	.=			"ORDER BY id";
		$sql	.=		")";
		
		$db->setFetchMode(Zend_Db::FETCH_OBJ);        
		$resultSet = $db->fetchAll($sql);
	
		$entries   = array();
		foreach ($resultSet as $row)
		{
			$entry = new Sos_Model_Location();
			$entry->setId($row->id)
						  ->setLatitude($row->latitude)
						  ->setLongtitude($row->longtitude)
						  ->setAlertlogId($row->alertlog_id)
						  ->setUpdatedDate($row->updated_date)
						  ->setMapper($this);
			$entries[] = $entry;
		}
		
		return $entries;
	}
		
}
