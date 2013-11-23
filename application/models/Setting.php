<?php
require_once('SettingMapper.php');
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author SOSbeacon
 * @copyright SOSbeacon
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Setting extends MainModel
{

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_RecordingVoiceDuration;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_RecordingLocationReportDuration;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_RecordingImageDuration;
    
    /**
     * mysql var type varchar(50)
     *
     * @var string     
     */
    protected $_PanicAlertPhonenummber;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_GoodSamaritanStatus;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_GoodSamaritanRange;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_AlertSendtoGroup;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_IncomingGovernmentAlert;
    
    /**
     * mysql var type smallint(6)
     *
     * @var int     
     */
    protected $_PanicAlertGoodSamaritanStatus;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_PanicAlertGoodSamaritanRange;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'recording_voice_duration'=>'RecordingVoiceDuration',
    'recording_location_report_duration'=>'RecordingLocationReportDuration',
    'recording_image_duration'=>'RecordingImageDuration',
    'panic_alert_phonenummber'=>'PanicAlertPhonenummber',
    'good_samaritan_status'=>'GoodSamaritanStatus',
    'good_samaritan_range'=>'GoodSamaritanRange',
    'alert_sendto_group'=>'AlertSendtoGroup',
    'incoming_government_alert'=>'IncomingGovernmentAlert',
    'panic_alert_good_samaritan_status'=>'PanicAlertGoodSamaritanStatus',
    'panic_alert_good_samaritan_range'=>'PanicAlertGoodSamaritanRange',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setId($data)
    {
        $this->_Id=$data;
        return $this;
    }

    /**
     * gets column id type int(11)
     * @return int     
     */
     
    public function getId()
    {
        return $this->_Id;
    }
    
    /**
     * sets column recording_voice_duration type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setRecordingVoiceDuration($data)
    {
        $this->_RecordingVoiceDuration=$data;
        return $this;
    }

    /**
     * gets column recording_voice_duration type smallint(6)
     * @return int     
     */
     
    public function getRecordingVoiceDuration()
    {
        return $this->_RecordingVoiceDuration;
    }
    
    /**
     * sets column recording_location_report_duration type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setRecordingLocationReportDuration($data)
    {
        $this->_RecordingLocationReportDuration=$data;
        return $this;
    }

    /**
     * gets column recording_location_report_duration type smallint(6)
     * @return int     
     */
     
    public function getRecordingLocationReportDuration()
    {
        return $this->_RecordingLocationReportDuration;
    }
    
    /**
     * sets column recording_image_duration type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setRecordingImageDuration($data)
    {
        $this->_RecordingImageDuration=$data;
        return $this;
    }

    /**
     * gets column recording_image_duration type smallint(6)
     * @return int     
     */
     
    public function getRecordingImageDuration()
    {
        return $this->_RecordingImageDuration;
    }
    
    /**
     * sets column panic_alert_phonenummber type varchar(50)     
     *
     * @param string $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setPanicAlertPhonenummber($data)
    {
        $this->_PanicAlertPhonenummber=$data;
        return $this;
    }

    /**
     * gets column panic_alert_phonenummber type varchar(50)
     * @return string     
     */
     
    public function getPanicAlertPhonenummber()
    {
        return $this->_PanicAlertPhonenummber;
    }
    
    /**
     * sets column good_samaritan_status type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setGoodSamaritanStatus($data)
    {
        $this->_GoodSamaritanStatus=$data;
        return $this;
    }

    /**
     * gets column good_samaritan_status type smallint(6)
     * @return int     
     */
     
    public function getGoodSamaritanStatus()
    {
        return $this->_GoodSamaritanStatus;
    }
    
    /**
     * sets column good_samaritan_range type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setGoodSamaritanRange($data)
    {
        $this->_GoodSamaritanRange=$data;
        return $this;
    }

    /**
     * gets column good_samaritan_range type int(11)
     * @return int     
     */
     
    public function getGoodSamaritanRange()
    {
        return $this->_GoodSamaritanRange;
    }
    
    /**
     * sets column alert_sendto_group type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setAlertSendtoGroup($data)
    {
        $this->_AlertSendtoGroup=$data;
        return $this;
    }

    /**
     * gets column alert_sendto_group type int(11)
     * @return int     
     */
     
    public function getAlertSendtoGroup()
    {
        return $this->_AlertSendtoGroup;
    }
    
    /**
     * sets column incoming_government_alert type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setIncomingGovernmentAlert($data)
    {
        $this->_IncomingGovernmentAlert=$data;
        return $this;
    }

    /**
     * gets column incoming_government_alert type smallint(6)
     * @return int     
     */
     
    public function getIncomingGovernmentAlert()
    {
        return $this->_IncomingGovernmentAlert;
    }
    
    /**
     * sets column panic_alert_good_samaritan_status type smallint(6)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setPanicAlertGoodSamaritanStatus($data)
    {
        $this->_PanicAlertGoodSamaritanStatus=$data;
        return $this;
    }

    /**
     * gets column panic_alert_good_samaritan_status type smallint(6)
     * @return int     
     */
     
    public function getPanicAlertGoodSamaritanStatus()
    {
        return $this->_PanicAlertGoodSamaritanStatus;
    }
    
    /**
     * sets column panic_alert_good_samaritan_range type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Setting     
     *
     **/

    public function setPanicAlertGoodSamaritanRange($data)
    {
        $this->_PanicAlertGoodSamaritanRange=$data;
        return $this;
    }

    /**
     * gets column panic_alert_good_samaritan_range type int(11)
     * @return int     
     */
     
    public function getPanicAlertGoodSamaritanRange()
    {
        return $this->_PanicAlertGoodSamaritanRange;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_SettingMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_SettingMapper());
        }
        return $this->_mapper;
    }


    /**
     * deletes current row by deleting a row that matches the primary key
     * 
     * @return int
     */

    public function deleteRowByPrimaryKey()
    {
        if (!$this->getId())
            throw new Exception('Primary Key does not contain a value');
        return $this->getMapper()->getDbTable()->delete('id = '.$this->getId());
    }

}

