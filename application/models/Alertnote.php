<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Alertnote extends MainModel
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
    protected $_AlertlogId;
    
    /**
     * mysql var type varchar(30)
     *
     * @var string     
     */
    protected $_From;
    
    /**
     * mysql var type varchar(1000)
     *
     * @var string     
     */
    protected $_Message;
    
    /**
     * mysql var type date
     *
     * @var date     
     */
    protected $_CreatedDate;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'alertlog_id'=>'AlertlogId',
    'from'=>'From',
    'message'=>'Message',
    'created_date'=>'CreatedDate',
    ));
}

	
    
    /**
     * sets column id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertnote     
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
     * sets column alertlog_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Alertnote     
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
     * sets column from type varchar(30)     
     *
     * @param string $data
     * @return Sos_Model_Alertnote     
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
     * sets column message type varchar(1000)     
     *
     * @param string $data
     * @return Sos_Model_Alertnote     
     *
     **/

    public function setMessage($data)
    {
        $this->_Message=$data;
        return $this;
    }

    /**
     * gets column message type varchar(1000)
     * @return string     
     */
     
    public function getMessage()
    {
        return $this->_Message;
    }
    
    /**
     * sets column created_date type date     
     *
     * @param date $data
     * @return Sos_Model_Alertnote     
     *
     **/

    public function setCreatedDate($data)
    {
        $this->_CreatedDate=$data;
        return $this;
    }

    /**
     * gets column created_date type date
     * @return date     
     */
     
    public function getCreatedDate()
    {
        return $this->_CreatedDate;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_AlertnoteMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_AlertnoteMapper());
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

