<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author thomas
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Alertdata extends MainModel
{

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type varchar(11)
     *
     * @var string     
     */
    protected $_Type;
    
    /**
     * mysql var type varchar(100)
     *
     * @var string     
     */
    protected $_Path;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_AlertlogId;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_PhoneId;
    
    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_CreatedDate;
    
    const TYPE_IMAGE = '0';
    const TYPE_AUDIO = '1';
    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'type'=>'Type',
    'path'=>'Path',
    'alertlog_id'=>'AlertlogId',
    'phone_id'=>'PhoneId',
    'created_date'=>'CreatedDate',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertdata     
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
     * sets column type type varchar(11)     
     *
     * @param string $data
     * @return Sos_Model_Alertdata     
     *
     **/

    public function setType($data)
    {
        $this->_Type=$data;
        return $this;
    }

    /**
     * gets column type type varchar(11)
     * @return string     
     */
     
    public function getType()
    {
        return $this->_Type;
    }
    
    /**
     * sets column path type varchar(100)     
     *
     * @param string $data
     * @return Sos_Model_Alertdata     
     *
     **/

    public function setPath($data)
    {
        $this->_Path=$data;
        return $this;
    }

    /**
     * gets column path type varchar(100)
     * @return string     
     */
     
    public function getPath()
    {
        return $this->_Path;
    }
    
    /**
     * sets column alertlog_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertdata     
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
     * sets column phone_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertdata     
     *
     **/

    public function setPhoneId($data)
    {
        $this->_PhoneId=$data;
        return $this;
    }

    /**
     * gets column phone_id type int(11)
     * @return int     
     */
     
    public function getPhoneId()
    {
        return $this->_PhoneId;
    }
    
    /**
     * sets column created_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Alertdata     
     *
     **/

    public function setCreatedDate($data)
    {
        $this->_CreatedDate=$data;
        return $this;
    }

    /**
     * gets column created_date type datetime
     * @return datetime     
     */
     
    public function getCreatedDate()
    {
        return $this->_CreatedDate;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_AlertdataMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_AlertdataMapper());
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

