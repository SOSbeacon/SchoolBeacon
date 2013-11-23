<?php
require_once('MainModel.php');

/**
 * Add your description here
 *
 * @author CNC Group
 * @copyright CNC Group
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
 
class Sos_Model_Adminuser extends MainModel
{

    /**
     * mysql var type int(11) unsigned
     *
     * @var int     
     */
    protected $_Id;
    
    /**
     * mysql var type varchar(255)
     *
     * @var string     
     */
    protected $_Email;
    
    /**
     * mysql var type varchar(40)
     *
     * @var string     
     */
    protected $_Username;
    
    /**
     * mysql var type varchar(40)
     *
     * @var string     
     */
    protected $_Password;
    
    /**
     * mysql var type varchar(255)
     *
     * @var string     
     */
    protected $_Token;
    
    /**
     * mysql var type varchar(255)
     *
     * @var string     
     */
    protected $_Name;
    
    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_CreatedDate;
    
    /**
     * mysql var type datetime
     *
     * @var datetime     
     */
    protected $_ModifiedDate;
    

    

function __construct() {
    $this->setColumnsList(array(
    'id'=>'Id',
    'email'=>'Email',
    'username'=>'Username',
    'password'=>'Password',
    'token'=>'Token',
    'name'=>'Name',
    'created_date'=>'CreatedDate',
    'modified_date'=>'ModifiedDate',
    ));
}

	
    
    /**
     * sets column id type int(11) unsigned     
     *
     * @param int $data
     * @return Sos_Model_Adminuser     
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
     * sets column email type varchar(255)     
     *
     * @param string $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setEmail($data)
    {
        $this->_Email=$data;
        return $this;
    }

    /**
     * gets column email type varchar(255)
     * @return string     
     */
     
    public function getEmail()
    {
        return $this->_Email;
    }
    
    /**
     * sets column username type varchar(40)     
     *
     * @param string $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setUsername($data)
    {
        $this->_Username=$data;
        return $this;
    }

    /**
     * gets column username type varchar(40)
     * @return string     
     */
     
    public function getUsername()
    {
        return $this->_Username;
    }
    
    /**
     * sets column password type varchar(40)     
     *
     * @param string $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setPassword($data)
    {
        $this->_Password=$data;
        return $this;
    }

    /**
     * gets column password type varchar(40)
     * @return string     
     */
     
    public function getPassword()
    {
        return $this->_Password;
    }
    
    /**
     * sets column token type varchar(255)     
     *
     * @param string $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setToken($data)
    {
        $this->_Token=$data;
        return $this;
    }

    /**
     * gets column token type varchar(255)
     * @return string     
     */
     
    public function getToken()
    {
        return $this->_Token;
    }
    
    /**
     * sets column name type varchar(255)     
     *
     * @param string $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setName($data)
    {
        $this->_Name=$data;
        return $this;
    }

    /**
     * gets column name type varchar(255)
     * @return string     
     */
     
    public function getName()
    {
        return $this->_Name;
    }
    
    /**
     * sets column created_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Adminuser     
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
     * sets column modified_date type datetime     
     *
     * @param datetime $data
     * @return Sos_Model_Adminuser     
     *
     **/

    public function setModifiedDate($data)
    {
        $this->_ModifiedDate=$data;
        return $this;
    }

    /**
     * gets column modified_date type datetime
     * @return datetime     
     */
     
    public function getModifiedDate()
    {
        return $this->_ModifiedDate;
    }
    
    /**
     * returns the mapper class
     *
     * @return Sos_Model_AdminuserMapper
     *
     */

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new Sos_Model_AdminuserMapper());
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

