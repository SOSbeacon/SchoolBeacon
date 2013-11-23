<?php

/**
 * Add your description here
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_AlertlogMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Alertlog
     *
     * @var Sos_Model_DbTable_Alertlog     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertlog $cls
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
		->setAlertloggroupId($row->alertloggroup_id)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setType($row->type);
	    return $cls;
    }

    /**
     * FIND LAST ALERTLOG BY FIELD
     * @param $field
     * @param $value
     * @param $cls
     */
	public function findLastByField($field, $value, $cls)
    { 
		$db 	 = $this->getDbTable()->getDefaultAdapter();		
		$value 	 = $db->quote($value);
		$sql	 = "SELECT * FROM alertlog WHERE $field = $value ORDER BY id DESC LIMIT 1";
		$result  = $db->fetchAll($sql);
		$cls->setId($result[0]['id'])
			->setAlertloggroupId($result[0]['alertloggroup_id'])
			->setMessage($result[0]['message'])
			->setCreatedDate($result[0]['created_date']);
			
	    return $cls;
    }
    
    
    /**
     * Find last alertlog by phone_id
     * @param unknown_type $field
     * @param unknown_type $value
     * @param unknown_type $cls
     */
	public function findLastAlert($phoneId, $cls)
    { 
		$db 	 = $this->getDbTable()->getDefaultAdapter();		
		$value 	 = $db->quote($phoneId);
		$sql	 = "SELECT * FROM alertlog 
					WHERE phone_id = $value
						  and type = 0 
				    ORDER BY id DESC LIMIT 1";
		$result  = $db->fetchAll($sql);
		$cls->setId($result[0]['id'])
			->setPhoneId($result[0]['phone_id'])
			->setStatus($result[0]['status'])
			->setCreatedDate($result[0]['created_date'])
			->setLastUpdated($result[0]['last_updated'])
			->setToken($result[0]['token']);
	    return $cls;
    }

    /**
     * find the most recent alert that in the same session ( less than 4 hours old)
     * @param unknown_type $field
     * @param unknown_type $value
     * @param unknown_type $cls
     */
	public function findInSessionAlert($phoneId, $type, $cls)
    { 
    	$db 	 = $this->getDbTable()->getDefaultAdapter();		
		$preparedType 	 = $db->quote($type);
		$preparedPhoneId = $db->quote($phoneId);
		$currentData = date("Y-m-d H:i:s");
		$sql	 = "select  * from alertlog ";
		$sql	.=		"where id = (select max(id) from alertlog "; 
		$sql	.=		            "where type=$preparedType and "; 
		$sql	.=		                  "phone_id = $preparedPhoneId) ";
		$sql	.=		"and TIMESTAMPDIFF(MINUTE, created_date, '$currentData')  <= 1";		        
		
		$result  = $db->fetchAll($sql);
		if ($result) {
		
			$cls->setId($result[0]['id'])
				->setPhoneId($result[0]['phone_id'])
				->setStatus($result[0]['status'])
				->setCreatedDate($result[0]['created_date'])
				->setLastUpdated($result[0]['last_updated'])
				->setToken($result[0]['token']);
		    return $cls;
		} else 
		   return null;
    } 
    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Alertlog $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'alertloggroup_id' => $cls->getAlertloggroupId(),
            'message' => $cls->getMessage(),
            'created_date' => $cls->getCreatedDate(),
            'type' => $cls->getType(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Alertlog $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Alertlog();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setAlertloggroupId($row->alertloggroup_id)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setType($row->type);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Alertlog $dbTable
     * @return Sos_Model_AlertlogMapper
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
     * @return Sos_Model_DbTable_Alertlog     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Alertlog');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Alertlog $cls
     *
     */
     
    public function save(Sos_Model_Alertlog $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * saveOneField current row
     *
     * @param $field, $value, $id
     *
     */
     
    public function saveOneField($field, $value, $id)
    {
		$data = array(
						$field => $value
					 );	
		$this->getDbTable()->update($data, array('id = ?' => $id));
    }
    
    /**
     * finds row by primary key
     *
     * @param int $id
     * @param Sos_Model_Alertlog $cls
     */

    public function find($id, Sos_Model_Alertlog $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setAlertloggroupId($row->alertloggroup_id)
		->setMessage($row->message)
		->setCreatedDate($row->created_date)
		->setType($row->type);
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
            $entry = new Sos_Model_Alertlog();
            $entry->setId($row->id)
                  ->setAlertloggroupId($row->alertloggroup_id)
                  ->setMessage($row->message)
                  ->setCreatedDate($row->created_date)
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
    public function fetchList($where=null, $order=null, $count=null, $offset=null)
    {
            $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
            $entries   = array();
            foreach ($resultSet as $row)
            {
                    $entry = new Sos_Model_Alertlog();
                    $entry->setId($row->id)
                          ->setAlertloggroupId($row->alertloggroup_id)
                          ->setMessage($row->message)
                          ->setCreatedDate($row->created_date)
                          ->setType($row->type)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }
    
    //Get last alertlog token by UserId
    public function getLastAlertlogTokenByUid($userId) {
    	$alertlogMap = new Sos_Model_AlertlogMapper();
    	$alertlog = new Sos_Model_Alertlog();
    	$phoneMap = new Sos_Model_PhoneMapper();
    	 
		$listPhoneId = $phoneMap->getPhoneByUid($userId, 1);
        $where = " phone_id IN ($listPhoneId)";
        if($listPhoneId == null || $listPhoneId == '')
        	$where = "phone_id = null";
        $alertRows = $alertlogMap->fetchList($where, " id DESC");
        
        if(count($alertRows) > 0) {
        	$alertlog = $alertRows[0];
        }
        
        return $alertlog->getToken();
    }
    
    /**
     * Get last alertlog by PhoneId and Type, and In session
     * @param $phoneId
     * @param $type
     * @return object Alertlog
     */
    public function getLastInCheckIn($phoneId) {
    	$db   = $this->getDbTable()->getDefaultAdapter();
    	$phoneId = $db->quote($phoneId);
    	$currentData = date("Y-m-d H:i:s");
    	
    	$query  = "SELECT * FROM alertlog ";
    	$query .=	"WHERE alertloggroup_id = "; 
    	$query .=		"(SELECT id FROM alertloggroup WHERE phone_id = $phoneId ";
    	$query .=			"ORDER BY id DESC LIMIT 1) ";
    	$query .=	"AND type = 2 ";
    	$query .=	"AND TIMESTAMPDIFF(MINUTE, created_date, '$currentData')  <= 15 ";
    	$query .=	"ORDER BY id DESC LIMIT 1";

		$db->setFetchMode(Zend_Db::FETCH_OBJ);   
		$result = $db->fetchAll($query);
		
		if(count($result) > 0)
			return $result[0];
		else
			return ;
    }
    
    /**
     * Get all alert data by alertloggroup
     * @param $alertloggroupId
     * @return array field of alertlog, location
     */
    public function getAllAlertlogDataByAlertloggroup($alertloggroupId) {
    	$db   = $this->getDbTable()->getDefaultAdapter();
    	
    	$query  = "SELECT * FROM alertlog a ";
    	$query .= 	"INNER JOIN location l ";
    	$query .= 		"ON a.id = l.alertlog_id ";
    	$query .= 	"WHERE alertloggroup_id = $alertloggroupId";

		$db->setFetchMode(Zend_Db::FETCH_OBJ);        
		$resultSet = $db->fetchAll($query);
		
		return $resultSet;
    }
    
}
