<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Alertlog extends MainModel
{

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_AlertloggroupId;
    
    /**
     * mysql var type varchar(160)
     *
     * @var string     
     */
    protected $_Message;
    
    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_CreatedDate;
    
    /**
     * mysql var type tinyint(1)
     *
     * @var int     
     */
    protected $_Type;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'alertloggroup_id'=>'AlertloggroupId',
    'message'=>'Message',
    'created_date'=>'CreatedDate',
    'type'=>'Type',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertlog     
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
     * sets column alertloggroup_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertlog     
     *
     **/

    public function setAlertloggroupId($data)
    {
        $this->_AlertloggroupId=$data;
        return $this;
    }

    /**
     * gets column alertloggroup_id type int(11)
     * @return int     
     */
     
    public function getAlertloggroupId()
    {
        return $this->_AlertloggroupId;
    }
    
    /**
     * sets column message type varchar(160)     
     *
     * @param string $data
     * @return Sos_Model_Alertlog     
     *
     **/

    public function setMessage($data)
    {
        $this->_Message=$data;
        return $this;
    }

    /**
     * gets column message type varchar(160)
     * @return string     
     */
     
    public function getMessage()
    {
        return $this->_Message;
    }
    
    /**
     * sets column created_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Alertlog     
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
     * sets column type type tinyint(1)     
     *
     * @param int $data
     * @return Sos_Model_Alertlog     
     *
     **/

    public function setType($data)
    {
        $this->_Type=$data;
        return $this;
    }

    /**
     * gets column type type tinyint(1)
     * @return int     
     */
     
    public function getType()
    {
        return $this->_Type;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_AlertlogMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_AlertlogMapper());
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

