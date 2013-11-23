<?php

/**
 * Add your description here
 *
 * @author SOSbeacon
 * @copyright SOSbeacon
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_SettingMapper {

    /**
     * $_dbTable - instance of Sos_Model_DbTable_Setting
     *
     * @var Sos_Model_DbTable_Setting     
     */
    protected $_dbTable;

    /**
     * finds a row where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Setting $cls
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
		->setRecordingVoiceDuration($row->recording_voice_duration)
		->setRecordingLocationReportDuration($row->recording_location_report_duration)
		->setRecordingImageDuration($row->recording_image_duration)
		->setPanicAlertPhonenummber($row->panic_alert_phonenummber)
		->setGoodSamaritanStatus($row->good_samaritan_status)
		->setGoodSamaritanRange($row->good_samaritan_range)
		->setAlertSendtoGroup($row->alert_sendto_group)
		->setIncomingGovernmentAlert($row->incoming_government_alert)
		->setPanicAlertGoodSamaritanStatus($row->panic_alert_good_samaritan_status)
		->setPanicAlertGoodSamaritanRange($row->panic_alert_good_samaritan_range);
	    return $cls;
    }


    /**
     * returns an array, keys are the field names.
     *
     * @param new Sos_Model_Setting $cls
     * @return array
     *
     */
    public function toArray($cls) {
        $result = array(
        
            'id' => $cls->getId(),
            'recording_voice_duration' => $cls->getRecordingVoiceDuration(),
            'recording_location_report_duration' => $cls->getRecordingLocationReportDuration(),
            'recording_image_duration' => $cls->getRecordingImageDuration(),
            'panic_alert_phonenummber' => $cls->getPanicAlertPhonenummber(),
            'good_samaritan_status' => $cls->getGoodSamaritanStatus(),
            'good_samaritan_range' => $cls->getGoodSamaritanRange(),
            'alert_sendto_group' => $cls->getAlertSendtoGroup(),
            'incoming_government_alert' => $cls->getIncomingGovernmentAlert(),
            'panic_alert_good_samaritan_status' => $cls->getPanicAlertGoodSamaritanStatus(),
            'panic_alert_good_samaritan_range' => $cls->getPanicAlertGoodSamaritanRange(),
                    
        );
        return $result;
    }

    /**
     * finds rows where $field equals $value
     *
     * @param string $field
     * @param mixed $value
     * @param Sos_Model_Setting $cls
     * @return array
     */
    public function findByField($field, $value, $cls)
    {
            $table = $this->getDbTable();
            $select = $table->select();
            $result = array();

            $rows = $table->fetchAll($select->where("{$field} = ?", $value));
            foreach ($rows as $row) {
                    $cls=new Sos_Model_Setting();
                    $result[]=$cls;
                    $cls->setId($row->id)
		->setRecordingVoiceDuration($row->recording_voice_duration)
		->setRecordingLocationReportDuration($row->recording_location_report_duration)
		->setRecordingImageDuration($row->recording_image_duration)
		->setPanicAlertPhonenummber($row->panic_alert_phonenummber)
		->setGoodSamaritanStatus($row->good_samaritan_status)
		->setGoodSamaritanRange($row->good_samaritan_range)
		->setAlertSendtoGroup($row->alert_sendto_group)
		->setIncomingGovernmentAlert($row->incoming_government_alert)
		->setPanicAlertGoodSamaritanStatus($row->panic_alert_good_samaritan_status)
		->setPanicAlertGoodSamaritanRange($row->panic_alert_good_samaritan_range);
            }
            return $result;
    }
    
    /**
     * sets the dbTable class
     *
     * @param Sos_Model_DbTable_Setting $dbTable
     * @return Sos_Model_SettingMapper
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
     * @return Sos_Model_DbTable_Setting     
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Sos_Model_DbTable_Setting');
        }
        return $this->_dbTable;
    }

    /**
     * saves current row
     *
     * @param Sos_Model_Setting $cls
     *
     */
     
    public function save(Sos_Model_Setting $cls,$ignoreEmptyValuesOnUpdate=true)
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
     * @param Sos_Model_Setting $cls
     */

    public function find($id, Sos_Model_Setting $cls)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }

        $row = $result->current();

        $cls->setId($row->id)
		->setRecordingVoiceDuration($row->recording_voice_duration)
		->setRecordingLocationReportDuration($row->recording_location_report_duration)
		->setRecordingImageDuration($row->recording_image_duration)
		->setPanicAlertPhonenummber($row->panic_alert_phonenummber)
		->setGoodSamaritanStatus($row->good_samaritan_status)
		->setGoodSamaritanRange($row->good_samaritan_range)
		->setAlertSendtoGroup($row->alert_sendto_group)
		->setIncomingGovernmentAlert($row->incoming_government_alert)
		->setPanicAlertGoodSamaritanStatus($row->panic_alert_good_samaritan_status)
		->setPanicAlertGoodSamaritanRange($row->panic_alert_good_samaritan_range);
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
            $entry = new Sos_Model_Setting();
            $entry->setId($row->id)
                  ->setRecordingVoiceDuration($row->recording_voice_duration)
                  ->setRecordingLocationReportDuration($row->recording_location_report_duration)
                  ->setRecordingImageDuration($row->recording_image_duration)
                  ->setPanicAlertPhonenummber($row->panic_alert_phonenummber)
                  ->setGoodSamaritanStatus($row->good_samaritan_status)
                  ->setGoodSamaritanRange($row->good_samaritan_range)
                  ->setAlertSendtoGroup($row->alert_sendto_group)
                  ->setIncomingGovernmentAlert($row->incoming_government_alert)
                  ->setPanicAlertGoodSamaritanStatus($row->panic_alert_good_samaritan_status)
                  ->setPanicAlertGoodSamaritanRange($row->panic_alert_good_samaritan_range)
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
                    $entry = new Sos_Model_Setting();
                    $entry->setId($row->id)
                          ->setRecordingVoiceDuration($row->recording_voice_duration)
                          ->setRecordingLocationReportDuration($row->recording_location_report_duration)
                          ->setRecordingImageDuration($row->recording_image_duration)
                          ->setPanicAlertPhonenummber($row->panic_alert_phonenummber)
                          ->setGoodSamaritanStatus($row->good_samaritan_status)
                          ->setGoodSamaritanRange($row->good_samaritan_range)
                          ->setAlertSendtoGroup($row->alert_sendto_group)
                          ->setIncomingGovernmentAlert($row->incoming_government_alert)
                          ->setPanicAlertGoodSamaritanStatus($row->panic_alert_good_samaritan_status)
                          ->setPanicAlertGoodSamaritanRange($row->panic_alert_good_samaritan_range)
                          ->setMapper($this);
                    $entries[] = $entry;
            }
            return $entries;
    }

}
