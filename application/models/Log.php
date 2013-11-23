<?php
require_once('MainModel.php');

/**
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Log extends MainModel
{

    protected $_Id;
    
    protected $_Timestamp;
    
    protected $_Priority;
    
    protected $_Message;
    

    function __construct() {
        $this->setColumnsList(array(
        'id'=>'Id',
        'timestamp'=>'Timestamp',
        'priority'=>'Priority',
        'message'=>'Message',
        ));
    }


    public function setId($data)
    {
        $this->_Id=$data;
        return $this;
    }

    public function getId()
    {
        return $this->_Id;
    }

    public function setTimestamp($data)
    {
        $this->_Timestamp=$data;
        return $this;
    }
     
    public function getTimestamp()
    {
        return $this->_Timestamp;
    }

    public function setPriority($data)
    {
        $this->_Priority=$data;
        return $this;
    }
    public function getPriority()
    {
        return $this->_Priority;
    }
    

    public function setMessage($data)
    {
        $this->_Message=$data;
        return $this;
    }
     
    public function getMessage()
    {
        return $this->_Message;
    }
    

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_LogMapper());
        }
        return $this->_mapper;
    }

}

