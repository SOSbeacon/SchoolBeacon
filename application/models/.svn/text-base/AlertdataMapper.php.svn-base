<?php

/**
 * Add your description here
 *
 * @author thomas
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_AlertdataMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Alertdata
     *
     * @var Sos_Model_DbTable_Alertdata     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertdata $cls
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
		->setType($row->type)
		->setPath($row->path)
		->setAlertlogId($row->alertlog_id)
		->setPhoneId($row->phone_id)
		->setCreatedDate($row->created_date);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Alertdata $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'type' => $cls->getType(),
            'path' => $cls->getPath(),
            'alertlog_id' => $cls->getAlertlogId(),
            'phone_id' => $cls->getPhoneId(),
            'created_date' => $cls->getCreatedDate(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertdata $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Alertdata();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setType($row->type)
		->setPath($row->path)
		->setAlertlogId($row->alertlog_id)
		->setPhoneId($row->phone_id)
		->setCreatedDate($row->created_date);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Alertdata $dbTable
     * @return Sos_Model_AlertdataMapper
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
     * @return Sos_Model_DbTable_Alertdata     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Alertdata');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Alertdata $cls
     *
     */
     
    public function save(Sos_Model_Alertdata $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * @param Sos_Model_Alertdata $cls
     */

    public function find($id, Sos_Model_Alertdata $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setType($row->type)
		->setPath($row->path)
		->setAlertlogId($row->alertlog_id)
		->setPhoneId($row->phone_id)
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
            $entry = new Sos_Model_Alertdata();
            $entry->setId($row->id)
                  ->setType($row->type)
                  ->setPath($row->path)
                  ->setAlertlogId($row->alertlog_id)
                  ->setPhoneId($row->phone_id)
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
                    $entry = new Sos_Model_Alertdata();
                    $entry->setId($row->id)
                          ->setType($row->type)
                          ->setPath($row->path)
                          ->setAlertlogId($row->alertlog_id)
                          ->setPhoneId($row->phone_id)
                          ->setCreatedDate($row->created_date)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }
    
    /**
     * Find all alertdata by alertloggroup
     * @param $alertloggroupId
     * @return array[alertlogId][alertdata]
     */
    public function findAllByAlertloggroup($alertloggroupId) {
    	$db   = $this->getDbTable()->getDefaultAdapter();
    	$quoteId = $db->quote($alertloggroupId);
    	
    	$query  = "SELECT al.id as alertlogId, al.type as alertlogType, ad.* FROM alertlog al ";
    	$query .= 	"LEFT JOIN alertdata ad ";
    	$query .= 		"ON al.id = ad.alertlog_id ";
    	$query .= 	"WHERE al.id IN (";
    	$query .= 		"SELECT id FROM alertlog ";
    	$query .= 			"WHERE alertloggroup_id = $quoteId";
    	$query .= 		")";

		$db->setFetchMode(Zend_Db::FETCH_OBJ);        
		$resultSet = $db->fetchAll($query);
		
		//Get array of alertlog_id unique value
		$arrAlertlog = array();
		foreach ($resultSet as $row) {
			$arrAlertlog[] = $row->alertlogId;
		}
		$arrAlertlog = array_unique($arrAlertlog);
		
		//Get two dimension array of alertdata
		$re = array();
		foreach($arrAlertlog as $val) {
			$arr = array();
			foreach ($resultSet as $row) {
				if($row->alertlogId == $val) {
					$arr[] = $row;
				}
			}
			$re[$val] = $arr;
		}
		
		return $re;
    }

}
