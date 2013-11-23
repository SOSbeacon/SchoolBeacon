<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author thomas
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Location extends MainModel
{

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type float
     *
     * @var float     
     */
    protected $_Latitude;
    
    /**
     * mysql var type float
     *
     * @var float     
     */
    protected $_Longtitude;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_AlertlogId;
    
    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_UpdatedDate;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'latitude'=>'Latitude',
    'longtitude'=>'Longtitude',
    'alertlog_id'=>'AlertlogId',
    'updated_date'=>'UpdatedDate',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Location     
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
     * sets column latitude type float     
     *
     * @param float $data
     * @return Sos_Model_Location     
     *
     **/

    public function setLatitude($data)
    {
        $this->_Latitude=$data;
        return $this;
    }

    /**
     * gets column latitude type float
     * @return float     
     */
     
    public function getLatitude()
    {
        return $this->_Latitude;
    }
    
    /**
     * sets column longtitude type float     
     *
     * @param float $data
     * @return Sos_Model_Location     
     *
     **/

    public function setLongtitude($data)
    {
        $this->_Longtitude=$data;
        return $this;
    }

    /**
     * gets column longtitude type float
     * @return float     
     */
     
    public function getLongtitude()
    {
        return $this->_Longtitude;
    }
    
    /**
     * sets column alertlog_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Location     
     *
     **/

    public function setAlertlogId($data)
    {
        $this->_AlertlogId=$data;
        return $this;
    }

    /**
     * gets column alertlog_id type int(11)
     * @return int     
     */
     
    public function getAlertlogId()
    {
        return $this->_AlertlogId;
    }
    
    /**
     * sets column updated_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Location     
     *
     **/

    public function setUpdatedDate($data)
    {
        $this->_UpdatedDate=$data;
        return $this;
    }

    /**
     * gets column updated_date type datetime
     * @return datetime     
     */
     
    public function getUpdatedDate()
    {
        return $this->_UpdatedDate;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_LocationMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_LocationMapper());
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

