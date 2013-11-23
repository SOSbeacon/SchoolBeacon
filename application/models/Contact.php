<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Contact extends MainModel
{

    /**
     * mysql var type int(11) unsigned
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type int(11)
     *
     * @var int     
     */
    protected $_GroupId;
    
    /**
     * mysql var type varchar(50)
     *
     * @var string     
     */
    protected $_Name;
    
    /**
     * mysql var type varchar(50)
     *
     * @var string     
     */
    protected $_Email;
    
    /**
     * mysql var type varchar(50)
     *
     * @var string     
     */
    protected $_Voicephone;
    
    /**
     * mysql var type varchar(50)
     *
     * @var string     
     */
    protected $_Textphone;
    
    /**
     * mysql var type tinyint(1)
     *
     * @var int     
     */
    protected $_Type;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'group_id'=>'GroupId',
    'name'=>'Name',
    'email'=>'Email',
    'voicephone'=>'Voicephone',
    'textphone'=>'Textphone',
    'type'=>'Type',
    ));
}

	
    
    /**
     * sets column id type int(11) unsigned     
     *
     * @param int $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setId($data)
    {
        $this->_Id=$data;
        return $this;
    }

    /**
     * gets column id type int(11) unsigned
     * @return int     
     */
     
    public function getId()
    {
        return $this->_Id;
    }
    
    /**
     * sets column group_id type int(11)     
     *
     * @param int $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setGroupId($data)
    {
        $this->_GroupId=$data;
        return $this;
    }

    /**
     * gets column group_id type int(11)
     * @return int     
     */
     
    public function getGroupId()
    {
        return $this->_GroupId;
    }
    
    /**
     * sets column name type varchar(50)     
     *
     * @param string $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setName($data)
    {
        $this->_Name=$data;
        return $this;
    }

    /**
     * gets column name type varchar(50)
     * @return string     
     */
     
    public function getName()
    {
        return $this->_Name;
    }
    
    /**
     * sets column email type varchar(50)     
     *
     * @param string $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setEmail($data)
    {
        $this->_Email=$data;
        return $this;
    }

    /**
     * gets column email type varchar(50)
     * @return string     
     */
     
    public function getEmail()
    {
        return $this->_Email;
    }
    
    /**
     * sets column voicephone type varchar(50)     
     *
     * @param string $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setVoicephone($data)
    {
        $this->_Voicephone=$data;
        return $this;
    }

    /**
     * gets column voicephone type varchar(50)
     * @return string     
     */
     
    public function getVoicephone()
    {
        return $this->_Voicephone;
    }
    
    /**
     * sets column textphone type varchar(50)     
     *
     * @param string $data
     * @return Sos_Model_Contact     
     *
     **/

    public function setTextphone($data)
    {
        $this->_Textphone=$data;
        return $this;
    }

    /**
     * gets column textphone type varchar(50)
     * @return string     
     */
     
    public function getTextphone()
    {
        return $this->_Textphone;
    }
    
    /**
     * sets column type type tinyint(1)     
     *
     * @param int $data
     * @return Sos_Model_Contact     
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
     * @return Sos_Model_ContactMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_ContactMapper());
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

