<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Emaillog extends MainModel
{

    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type varchar(30)
     *
     * @var string     
     */
    protected $_From;
    
    /**
     * mysql var type varchar(30)
     *
     * @var string     
     */
    protected $_To;
    
    /**
     * mysql var type varchar(500)
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
     * mysql var type tinyint(4)
     *
     * @var int     
     */
    protected $_Status;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'from'=>'From',
    'to'=>'To',
    'message'=>'Message',
    'created_date'=>'CreatedDate',
    'status'=>'Status',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Emaillog     
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
     * sets column from type varchar(30)     
     *
     * @param string $data
     * @return Sos_Model_Emaillog     
     *
     **/

    public function setFrom($data)
    {
        $this->_From=$data;
        return $this;
    }

    /**
     * gets column from type varchar(30)
     * @return string     
     */
     
    public function getFrom()
    {
        return $this->_From;
    }
    
    /**
     * sets column to type varchar(30)     
     *
     * @param string $data
     * @return Sos_Model_Emaillog     
     *
     **/

    public function setTo($data)
    {
        $this->_To=$data;
        return $this;
    }

    /**
     * gets column to type varchar(30)
     * @return string     
     */
     
    public function getTo()
    {
        return $this->_To;
    }
    
    /**
     * sets column message type varchar(500)     
     *
     * @param string $data
     * @return Sos_Model_Emaillog     
     *
     **/

    public function setMessage($data)
    {
        $this->_Message=$data;
        return $this;
    }

    /**
     * gets column message type varchar(500)
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
     * @return Sos_Model_Emaillog     
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
     * sets column status type tinyint(4)     
     *
     * @param int $data
     * @return Sos_Model_Emaillog     
     *
     **/

    public function setStatus($data)
    {
        $this->_Status=$data;
        return $this;
    }

    /**
     * gets column status type tinyint(4)
     * @return int     
     */
     
    public function getStatus()
    {
        return $this->_Status;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_EmaillogMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_EmaillogMapper());
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

    /**
     * Delete rows by ids
     * @param list $ids
     */
    public function deleteRowByIds($ids)
    {
    	$where = ($ids == null) ? "id = null" : "id IN ($ids)";
		$this->getMapper()->getDbTable()->delete($where);
    }
    
    
}

