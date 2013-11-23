<?php

class Sos_Model_PhoneMapper {

    protected $_dbTable;

    private function _setValues($phone, $values) {
        $phone
            ->setId($values->id)
            ->setName($values->name)
            ->setNumber($values->number)
            ->setEmail($values->email)
            ->setPassword($values->password)
            ->setCreatedDate($values->created_date)
            ->setModifiedDate($values->modified_date)
            ->setSubscribe($values->subscribe)
            ->setEmailEnabled($values->email_enabled)
            ->setType($values->type)
            ->setImei($values->imei)
            ->setLocationId($values->location_id)
            ->setSettingId($values->setting_id)
            ->setToken($values->token)
            ->setStatus($values->status)
            ->setRole($values->role);
        return $phone;
    }
    
    public function toArray($cls) {
        $result = array(
            'id' => $cls->getId(),
            'name' => $cls->getName(),
            'number' => $cls->getNumber(),
            'email' => $cls->getEmail(),
            'password' => $cls->getPassword(),
            'created_date' => $cls->getCreatedDate(),
            'modified_date' => $cls->getModifiedDate(),
            'subscribe' => $cls->getSubscribe(),
            'email_enabled' => $cls->getEmailEnabled(),
            'type' => $cls->getType(),
            'imei' => $cls->getImei(),
            'location_id' => $cls->getLocationId(),
            'setting_id' => $cls->getSettingId(),
            'token' => $cls->getToken(),
            'status' => $cls->getStatus(),
            'role' => $cls->getRole(),
            'phone_info' => $cls->getPhoneInfo(),
            
        );
        return $result;
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

    public function findByField($field, $value, $cls, $status = null) {
        $table = $this->getDbTable();
        $select = $table->select();
        $result = array();

        $rows = $table->fetchAll($select->where("{$field} = ?", $value));
        foreach ($rows as $row) {
            $cls = new Sos_Model_Phone();
            $cls = $this->_setValues($cls, $row);
            if ($status !== null) {
                if ($cls->getStatus() == $status) {
                    $result[] = $cls;
                }
            } else {
                $result[] = $cls;
            }
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
            $this->setDbTable('Sos_Model_DbTable_Phone');
        }
        return $this->_dbTable;
    }

    public function save(Sos_Model_Phone $cls, $ignoreEmptyValuesOnUpdate=true) {
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

    public function find($id, Sos_Model_Phone $cls) {
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
            $cls = new Sos_Model_Phone();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }

    public function fetchList($where=null, $order=null, $count=null, $offset=null) {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries = array();
        foreach ($resultSet as $row) {
            $cls = new Sos_Model_Phone();
            $entries[] = $this->_setValues($cls, $row);
        }
        return $entries;
    }

    public function fetchByLocation($phoneId, $latitude, $longtitude) {
        $logger = Sos_Service_Logger::getLogger();
        $logger->log('=== START fetchByLocation', Zend_Log::INFO);
        $db = $this->getDbTable()->getDefaultAdapter();
        //get radius range of alerter
        $select = $db->select()
                ->from(array('p' => 'phone'), array('p.*', 's.*'))
                ->join(array('s' => 'setting'), 'p.setting_id = s.id')
                ->where('p.id = ?', $phoneId);
        $results = $db->fetchAll($select);
        $result = $results[0];
        $panicRange = is_array($result) ? $result['panic_alert_good_samaritan_range'] : $result->panic_alert_good_samaritan_range;
        //check if panic_alert_good_samaritan_status settings on phone is OFF
        $samaritanStatus = is_array($result) ? $result['panic_alert_good_samaritan_status'] : $result->panic_alert_good_samaritan_status;
        if ($samaritanStatus == 0) {
            return array();
        }
        $logger->log('=== find locations in range', Zend_Log::INFO);
        $sqlDistance = "			
		  SELECT p.*
			FROM `phone` p, location l, setting s
			where p.location_id = l.id
			and p.setting_id = s.id
			and s.good_samaritan_status = 1
			and s.good_samaritan_range >= 6371 * acos(sin(RADIANS(l.latitude)) * sin(RADIANS($latitude)) + 
			                                          cos(RADIANS(l.latitude)) * cos(RADIANS($latitude)) * cos(RADIANS(l.longtitude - $longtitude)) 
			                                         ) 
                        and $panicRange >= 6371 * acos(sin(RADIANS(l.latitude)) * sin(RADIANS($latitude)) + 
			                                          cos(RADIANS(l.latitude)) * cos(RADIANS($latitude)) * cos(RADIANS(l.longtitude - $longtitude)) 
			                                         )  			                                        
			";
        $logger->log("=== SQL distance: + $sqlDistance", Zend_Log::INFO);
        $result = $db->fetchAll($sqlDistance);
        $entries = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                $cls = new Sos_Model_Phone();
                $entries[] = $this->_setValues($cls, $row);;
            }
        }
        return $entries;
    }

    public function findSamaritanByLocation($phoneId, $latitude, $longtitude) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $phones = $this->fetchByLocation($phoneId, $latitude, $longtitude);
        $arrId = Array();
        foreach ($phones as $row) {
            $arrId[] = $row->id;
        }
        if (count($arrId) == 0)
            $arrId = 'null';
        $select = $db->select()
                ->from(array('p' => 'phone'), array('p.id', 'p.number', 'p.email', 'l.latitude', 'l.longtitude'))
                ->join(array('l' => 'location'), 'p.location_id = l.id')
                ->where('p.id IN (?)', $arrId)
        ;
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getPhoneByToken($token, Sos_Model_Phone $cls) {
        $result = $this->findByField('token', $token, $cls);
        if (0 == count($result)) {
            return;
        }
        $cls = $result[0];
        return $cls;
    }

    /**
     * Gen new random password
     */
    public function genPassword() {
        $passLength = 6;
        $allowableCharacters = 'abcdefghjkmnpqrstuvwxyz123456789';
        $allowableLength = strlen($allowableCharacters) - 1;
        $newPass = '';
        for ($i = 0; $i < $passLength; $i++) {
            $newPass .= $allowableCharacters[mt_rand(0, $allowableLength)];
        }
        return $newPass;
    }
    
    /**
     * get all phone data by phoneId	
     * return 1 row data
     */
    public function getAllPhoneDataByPid($phoneId) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $phoneId = $db->quote($phoneId);
        $select = "SELECT p.*, s.* , p.id AS id";
        $select.= " FROM phone p";
        $select.= " INNER JOIN location l	ON p.location_id 	= l.id";
        $select.= " INNER JOIN setting s 	ON p.setting_id 	= s.id";
        $select.= " WHERE p.id = $phoneId";
        $result = $db->fetchAll($select);
        if (count($result) == 0) {
            return null;
        }
        return $result[0];
    }
    

    /**
     * Get phone information by alertlog_id
     */
    public function getPhoneByAlertloggroupId(Sos_Model_Phone $phone, $alertloggroupId) {
        $result = $this->getDbTable()->find($alertloggroupId);
        $alert = new Sos_Model_Alertloggroup();
        $alertMapper = new Sos_Model_AlertloggroupMapper();
        $alertMapper->findOneByField('id', $alertloggroupId, $alert);
        if ($alert->getId() != null) {
            $phoneMap = new Sos_Model_PhoneMapper();
            $phoneMap->findOneByField('id', $alert->getPhoneId(), $phone);
            $emei = $phone->getImei();
            //Check if emei string too large
            if (strlen($emei) > 25) {
                $nemei = substr($emei, 0, 24) . " ";
                $nemei .= substr($emei, 25, strlen($emei));
            } else {
                $nemei = $emei;
            }
            $phone->setImei($nemei);
        }
    }

    public function deletePhoneById($phoneId) {
        $db = $this->getDbTable()->getDefaultAdapter();
        $phone = new Sos_Model_Phone();
        $phoneMapper = new Sos_Model_PhoneMapper();
        $location = new Sos_Model_Location();
        $locationMapper = new Sos_Model_LocationMapper();
        $group = new Sos_Model_Contactgroup();
        $groupMapper = new Sos_Model_ContactgroupMapper();
        $alertlog = new Sos_Model_Alertlog();
        $alertlogMapper = new Sos_Model_AlertlogMapper();
        $alertloggroup = new Sos_Model_Alertloggroup();
        $alertloggroupMapper = new Sos_Model_AlertloggroupMapper();
        if ($phoneId != NULL) {
            $phoneMapper->findOneByField('id', $phoneId, $phone);
            if ($phone->getId() != null) {
                $del = $db->delete('phone', "id = " . $phone->getId());
                //TODO DELETE ALL DATA OF alertnote, alertdata, alertlog, contact, contactgroup, location, phone, setting by PhoneId
                if ($del > 0) {
                    //DELETE settings of phone
                    $db->delete('setting', "id = " . $phone->getSettingId());
                    //DELETE location of phone
                    $db->delete('location', "id = " . $phone->getLocationId());
                    //Get contactgroup_id for delete contact
                    $arrGroup = $groupMapper->findByField('phone_id', $phone->getId(), $group);
                    $groupIds = Array();
                    if (count($arrGroup) > 0) {
                        foreach ($arrGroup as $row) {
                            $groupIds[] = $row->getId();
                        }
                    }
                    $groupIds = implode(',', $groupIds);
                    //DELETE contactgroup of phone
                    $db->delete('contactgroup', "phone_id = " . $phone->getId());
                    //DELETE contact of phone
                    if ($groupIds != '')
                        $db->delete('contact', "group_id in ($groupIds)");
                    //Get all alertloggroup_id of phone
                    $alertloggroups = $alertloggroupMapper->findByField('phone_id', $phone->getId(), $alertloggroup);
                    $alertloggroupIds = array();
                    if (count($alertloggroups) > 0) {
                        foreach ($alertloggroups as $row) {
                            $alertloggroupIds[] = $row->getId();
                        }
                    }
                    //DELETE alertloggroup of phone
                    $db->delete('alertloggroup', "phone_id = " . $phone->getId());
                    //Get Where clause of alertloggroup_id
                    $alertlogWhere = ($alertloggroupIds != NULL) ? "alertloggroup_id in (" . implode(',', $alertloggroupIds) . ")" : "alertloggroup_id = NULL";
                    //DELETE alertlof of phone
                    $db->delete('alertlog', $alertlogWhere);

                    //Get alertlog_id for delete alertnote
                    $arrAlertlog = $alertlogMapper->fetchList($alertlogWhere);
                    $alertlogIds = Array();
                    if (count($arrAlertlog) > 0) {
                        foreach ($arrAlertlog as $row) {
                            $alertlogIds[] = $row->getId();
                        }
                    }
                    $alertlogIds = implode(',', $alertlogIds);
                    //DELETE alertdata of phone
                    $db->delete('alertdata', "phone_id = " . $phone->getId());
                    //DELETE alertnote of phone
                    $alertnoteWhere = ($alertloggroupIds != NULL) ? "alertlog_id in (" . implode(',', $alertloggroupIds) . ")" : "alertlog_id = NULL";
                    $db->delete('alertnote', $alertnoteWhere);
                    //DELETE location of alertlog
                    if ($alertlogIds != '' || $alertlogIds != ',')
                        $db->delete('location', $alertnoteWhere);
                    //DELETE alertnote of phone
                    $db->delete('smslog', "`from` = '" . $phone->getNumber() . "'");
                }
            }
        }
    }
}