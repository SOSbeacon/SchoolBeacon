<?php

/**
 * Add your description here
 *
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_ContactMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Contact
     *
     * @var Sos_Model_DbTable_Contact     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contact $cls
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
		->setGroupId($row->group_id)
		->setName($row->name)
		->setEmail($row->email)
		->setVoicephone($row->voicephone)
		->setTextphone($row->textphone)
		->setType($row->type);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Contact $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'group_id' => $cls->getGroupId(),
            'name' => $cls->getName(),
            'email' => $cls->getEmail(),
            'voicephone' => $cls->getVoicephone(),
            'textphone' => $cls->getTextphone(),
            'type' => $cls->getType(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contact $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Contact();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setGroupId($row->group_id)
		->setName($row->name)
		->setEmail($row->email)
		->setVoicephone($row->voicephone)
		->setTextphone($row->textphone)
		->setType($row->type);
            }
            return $result;
    }


    /**
     * finds rows where $field equals $value and ORDER rows by type, name
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contact $cls
     * @return array
     */
    public function findByFieldOrder($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value), 'type DESC, name');
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Contact();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setGroupId($row->group_id)
		->setName($row->name)
		->setEmail($row->email)
		->setVoicephone($row->voicephone)
		->setTextphone($row->textphone)
		->setType($row->type);
            }
            return $result;
    }
    
  /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Contact $cls
     * @return array
     */
    public function findDistinctByField($phoneId, $field, $value, $cls)
    {
		$table = $this->getDbTable();
		$select = $table->select()->distinct();
		//SQL injection
		$value =  $table->getDefaultAdapter()->quote($value);
		$phoneId =  $table->getDefaultAdapter()->quote($phoneId);
		
		$where = " $field = $value AND group_id IN (SELECT id FROM contactgroup WHERE phone_id = $phoneId)";
		$result = array();
		$rows = $table->fetchAll($select->where($where));
		foreach ($rows as $row) {
			$cls=new Sos_Model_Contact();
			$result[]=$cls;
			$cls->setId($row->id)
				->setGroupId($row->group_id)
				->setName($row->name)
				->setEmail($row->email)
				->setVoicephone($row->voicephone)
				->setTextphone($row->textphone);
		}
		return $result;
    }

    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Contact $dbTable
     * @return Sos_Model_ContactMapper
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
     * @return Sos_Model_DbTable_Contact     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Contact');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Contact $cls
     *
     */
     
    public function save(Sos_Model_Contact $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * @param Sos_Model_Contact $cls
     */

    public function find($id, Sos_Model_Contact $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setGroupId($row->group_id)
		->setName($row->name)
		->setEmail($row->email)
		->setVoicephone($row->voicephone)
		->setTextphone($row->textphone)
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
            $entry = new Sos_Model_Contact();
            $entry->setId($row->id)
                  ->setGroupId($row->group_id)
                  ->setName($row->name)
                  ->setEmail($row->email)
                  ->setVoicephone($row->voicephone)
                  ->setTextphone($row->textphone)
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
                    $entry = new Sos_Model_Contact();
                    $entry->setId($row->id)
                          ->setGroupId($row->group_id)
                          ->setName($row->name)
                          ->setEmail($row->email)
                          ->setVoicephone($row->voicephone)
                          ->setTextphone($row->textphone)
                          ->setType($row->type)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }
    
    public function fetchCompleteContact($phoneId) {
    	$phoneId = $this->getDbTable()->quote($phoneId);
        $query = "SELECT g.name as groupname, g.id as groupid, c.name as name, c.email, c.voicephone, c.textphone, c.id as contactid
                from  contactgroup g 
                inner join contact c on g.id = c.group_id
                where g.phone_id = $phoneId ";
        
        $resultSet = $this->getDbTable()->getAdapter()->query($query)->fetchAll();
       
        $groups = array();
        
        foreach ($resultSet as $row)
        {
            if (!array_key_exists($row['groupid'], $groups)) {
                $groups[$row['groupid']] = array();
                $groups[$row['groupid']]['name'] = $row['groupname'];
                $groups[$row['groupid']]['contacts'] = array();
            }   
            
            $contact = array();
            $contact['id'] = $row['contactid'];
            $contact['name'] = $row['name'];
            $contact['email'] = $row['email'];
            $contact['voicephone'] = $row['voicephone'];
            $contact['textphone'] = $row['textphone'];      
                    
            $groups[$row['groupid']]['contacts'][] = $contact;
            
            
        }
        
        return $groups;
        
    }
    
    /**
     * Return array of contact
     */
    public function getAllContactByPhoneId($phoneId, $groupType = 0, $groupId = 0, $returnGroup = false) {
        $db 	 = $this->getDbTable()->getDefaultAdapter();
        $phoneId = $db->quote($phoneId);
        $andGroup = '';
        $andType = '';
        $groupName = '';
        $allowJoinGroup = false;
        $joinContacts = array();
        if ($groupId) {
            $andGroup = 'group_id=' . $groupId;
            $group = new Sos_Model_Contactgroup();
            $joinGroup = new Sos_Model_Contactgroup();
            $group->find($groupId);
            $groupName = $group->getName();
            if ($group->getType() == 2) { // Get Neighborhood Watch JOIN if have
                $join = new Sos_Model_Groupjoin();
                $joins = $join->fetchList("phone_id=$phoneId AND group_id=$groupId");
                if (count($joins)) {
                    $joinGroupId = 0;
                    $contact = new Sos_Model_Contact();
                    foreach ($joins as $gj) {
                        $joinGroupId = $gj->getJoinGroupId();
                        $_joinContacts = $contact->getMapper()->fetchList('group_id=' . $gj->getJoinGroupId(), 'type DESC');
                        $joinContacts = array_merge($joinContacts, $_joinContacts);
                    }
                    if ($joinGroupId) {
                        $joinGroup->find($joinGroupId);
                        if ($joinGroup->getId()) $groupName = $joinGroup->getName();
                    }
                    $andGroup .= ' AND type = 1'; // get only default contact, remove all normal own contacts
                }
                $allowJoinGroup = $group->getMapper()->isAllowJoinGroup($group);
            }
        } else {
            if($groupType === 0) $andType= " AND type = 0";
            elseif($groupType == 1) $andType= " AND type = 1";
            elseif($groupType == 2) $andType= " AND type = 2";
            elseif($groupType == 3) $andType= " AND type = 3";
            $andGroup = "group_id IN (SELECT id FROM contactgroup WHERE phone_id = $phoneId $andType)";
        }
        $select = "SELECT * FROM contact  WHERE $andGroup ORDER BY type DESC, name";
        $result = $db->query($select)->fetchAll(Zend_Db::FETCH_ASSOC);
        $entries   = array();
        foreach ($result as $row) {
            $entry = new Sos_Model_Contact();
            $entry->setId($row['id'])
                ->setGroupId($row['group_id'])
                ->setName($row['name'])
                ->setEmail($row['email'])
                ->setVoicephone($row['voicephone'])
                ->setTextphone($row['textphone'])
                ->setType($row['type'])
                ->setMapper($this);
            $entries[] = $entry;
        }
        if (count($joinContacts)) $entries = array_merge($entries, $joinContacts);
        if ($returnGroup) {
            return array(
                'groupName' => $groupName,
                'allowJoinGroup' => $allowJoinGroup, 
                'contacts' => $entries,
                'joinContacts' => $joinContacts,
            );
        }
        return $entries;
    }
    
    /*
     * Get contact by group type
     */
    public function getContactByGroupTypes($phoneId, $groupTypes = array()) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $phoneId = (int) $phoneId;
        $types = implode(',', $groupTypes);
        $select = "
            SELECT * FROM contact  
            WHERE group_id IN (SELECT id FROM contactgroup WHERE phone_id = $phoneId AND type IN($types))";
        $result = $db->fetchAll($select);
        $entries   = array();
        foreach ($result as $row) {
            $entry = new Sos_Model_Contact();
            if (is_array($row)) {
                $entry->setId($row['id'])
                  ->setGroupId($row['group_id'])
                  ->setName($row['name'])
                  ->setEmail($row['email'])
                  ->setVoicephone($row['voicephone'])
                  ->setTextphone($row['textphone'])
                  ->setMapper($this);
            }
            if (is_object($row)) {
                $entry->setId($row->id)
                  ->setGroupId($row->group_id)
                  ->setName($row->name)
                  ->setEmail($row->email)
                  ->setVoicephone($row->voicephone)
                  ->setTextphone($row->textphone)
                  ->setMapper($this);
            }
            $entries[] = $entry;
        }
        return $entries;
    }
    
    /**
     * Count phone contact s
     * @param type $phoneId
     * @param type $type = 1 : default, 0 : nomral
     */
    public function countContactsByPhoneId($phoneId, $type = '', $groupId = '') {
        $db = $this->getDbTable()->getDefaultAdapter();
        $phoneId = $db->quote($phoneId);
        $sql = 'SELECT COUNT(id) FROM contact
                   WHERE group_id IN 
                           (SELECT id FROM contactgroup WHERE phone_id=' . $phoneId . ')';
        if ($type !== '') {
            $sql .= ' AND type=' . $type;
        }
        if ($groupId) {
            $sql = 'SELECT COUNT(id) FROM contact WHERE group_id=' . $groupId .'';
        }    
        $count = $db->fetchOne($sql);
        return $count;
    }
    
    
    /**
     * UPDATE Contact default name
     * @param $userId
     * @param $groupName
     * @param $groupType
     */
    public function updateDefaultContact($phone, $groupName, $groupType) {
    	$db = $this->getDbTable()->getDefaultAdapter();
        $phoneId = $phone->getId();
        $email = $phone->getEmail() ? $phone->getEmail() : '';
        // Update contact by group_id and type = 1 (default)
        $sql = 'UPDATE contact SET 
                    name="' . ucfirst($phone->getName()) . ' ' . $groupName . '", 
                    email="' . $email . '",
                    textphone="' . $phone->getNumber() . '"
                WHERE group_id IN(
                    SELECT id FROM contactgroup WHERE phone_id=' . $phoneId . '
                    AND type=' . $groupType . '
                ) AND contact.type = 1';
        $db->query($sql);
    }
}
