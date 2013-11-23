<?php

abstract class MainModel {

    protected $_mapper;
    protected $_columnsList;
    
    /**
     *
     * @param array $data
     * @return Default_Model_
     */

    public function setColumnsList($data) {
        $this->_columnsList=$data;
        return $this;
    }

    /**
     * returns columns list array
     *
     * @return array
     */
    public function getColumnsList() {
        return $this->_columnsList;
    }

    /**
     * converts database column name to php setter/getter function name
     * @param string $column
     */
    public function columnName2Var($column) {
        if (!isset($this->_columnsList[$column]))
            throw new Exception("column '$column' not found!");
        return $this->_columnsList[$column];
    }

    /**
     * converts database column name to php setter/getter function name
     * @param string $column
     */
    public function varName2Column($thevar) {

        foreach ($this->_columnsList as $column=>$var)
            if ($var == $thevar)
                    return $column;
        return null;
    }

    /**
     * Recognize methods for Belongs-To cases:
     * findBy<field>()
     * findOneBy<field>()
     * Use the non-greedy pattern repeat modifier e.g. \w+?
     *
     * @param string $method
     * @param array  $args
     */

    public function __call($method, array $args)
    {
        $matches = array();
        $result=null;
        if (preg_match('/^find(One)?By(\w+)?$/', $method, $matches)) {
                $methods = get_class_methods($this);
                $check = 'set'.$matches[2];

                $fieldName=$this->varName2Column($matches[2]);

                if (!in_array($check, $methods)) {
                        throw new Exception("Invalid field {$matches[2]} requested for table");
                }
                if ($matches[1] != '') {
                    $result=$this->getMapper()->findOneByField($fieldName, $args[0], $this);
                }
                    else $result=$this->getMapper()->findByField($fieldName, $args[0], $this);
                return $result;
        }

        throw new Exception("Unrecognized method '$method()'");
    }


    /**
     * created a Zend_Paginator class by a given select
     *
     * @param Zend_Db_Select $query
     * @return Zend_Paginator
     */

    public function select2Paginator(Zend_Db_Select $select) {

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($adapter);

        return $paginator;
    }

    /**
     * fetch all rows into a Zend_Paginator
     *
     * @return Zend_Paginator
     */

    public function fetchAll2Paginator() {

        return $this->select2Paginator($this->getMapper()->getDbTable()->select()->from($this->getMapper()->getDbTable()->getTableName()));

    }

    /**
     * fetch all rows in a 3d array
     *
     * @return array
     */

    
    public function fetchAllToArray()
    {
        $resultSet = $this->getMapper()->getDbTable()->fetchAll()->toArray();
        return $resultSet;
    }

    /**
     *  __set() is run when writing data to inaccessible properties
     *  overloading it to support setting columns.
     *  example: class->column_name='foo' or class->ColumnName='foo'
     *           will execute the function class->setColumnName('foo')
     *
     * @param string $name
     * @param mixed $value
     *
     */

    public function __set($name, $value)
    {
       $name=$this->columnName2var($name);

                $method = 'set'.$name;
                if (('mapper' == $name) || !method_exists($this, $method)) {
                        throw new Exception("name:$name value:$value - Invalid property");
                }
                $this->$method($value);
    }

    /**
     *  __get() is utilized for reading data from inaccessible properties
     *  overloading it to support getting columns value.
     *  example: $foo=class->column_name or $foo=class->ColumnName
     *           will execute the function $foo=class->getColumnName()
     *
     * @param string $name
     * @param mixed $value
     *
     */

    public function __get($name)
    {
        $name=$this->columnName2Var($name);

        $method = 'get'.$name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
                throw new Exception("name:$name  - Invalid property");
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
                $key = preg_replace_callback('/_(.)/', create_function('$matches','return ucfirst($matches[1]);'), $key);
                $method = 'set' . ucfirst($key);
                if (in_array($method, $methods)) {
                        $this->$method($value);
                }
        }
        return $this;
    }

    /**
     * deletes current row by a where statement
     *
     * @param string $where
     * @return int
     */
    public function delete($where)
    {
        return $this->getMapper()->getDbTable()->delete($where);
    }

    /**
     * returns the number of rows in the table
     * @var int
     */
    public function countAllRows() {
        return $this->getMapper()->getDbTable()->countAllRows();
    }
        /**
         * returns the primary key column name
         *
         * @var string
         */
        public function getPrimaryKeyName() {
            return $this->getMapper()->getDbTable()->countAllRows();
        }


     public function countByQuery($where='') {
        return $this->getMapper()->getDbTable()->countByQuery($where);
     }

    /**
     * fetchs all rows
     * optionally filtered by where, order, count and offset
     * returns an array of the model class

     *
     * @return array
     *
     */
    public function fetchList($where=null, $order=null, $count=null, $offset=null)
    {
        return $this->getMapper()->fetchList($where, $order, $count, $offset);
    }

    /**
     * fetchs all rows
     * optionally filtered by where, order, count and offset
     * returns Zend_Paginator
     *
     * @return Zend_Paginator
     *
     */
    public function fetchListToPaginator($where=null, $order=null, $count=null, $offset=null)
    {
        return $this->select2Paginator($this->getMapper()->getDbTable()->fetchList($where,$order,$count,$offset));

    }
    /**
     * fetchs all rows
     * optionally filtered by where, order, count and offset
     * returns a 3d-array of the result
     *
     * @return array
     *
     */
    public function fetchListToArray($where=null, $order=null, $count=null, $offset=null)
    {
        return $this->getMapper()->getDbTable()->fetchAll($where, $order, $count, $offset)->toArray();
    }
    /**
     * finds row by id
     *
     * @param <?=$this->_primaryKey['phptype']?> $id
     * @return MainModel

     *
     */
    public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }

    /**
     * fetchs all row
     * returns an array of model class

     *
     * @return array
     *
     */
    public function fetchAll()
    {
        return $this->getMapper()->fetchAll();
    }

    /**
     * returns an array, keys are the field names.
     *
     * @return array
     */
    public function toArray() {
        return $this->getMapper()->toArray($this);
    }

    /**
     * sets the mapper class
     *
     * @param <?=$this->_namespace?>_Model_<?=$this->_className?>Mapper $mapper
     * @return <?=$this->_namespace?>_Model_<?=$this->_className?>

     */

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }
    /**
     * saves current loaded row
     *
     *  $ignoreEmptyValuesOnUpdate by default is true.
     *      this option will not update columns with empty values.
     *
     * @param boolean $ignoreEmptyValuesOnUpdate
     *
     */

    public function save($ignoreEmptyValuesOnUpdate=true)
    {
        $this->getMapper()->save($this,$ignoreEmptyValuesOnUpdate);
    }

    /**
     *  return the Zend_Db_Table_Select class
     * 
     * @param bool $withFromPart
     * @return Zend_Db_Table_Select
     */
    public function getSelect($withFromPart=true,$resetColumns=true,$resetOrder=true,$resetLimitOffset=true) {
        $select=$this->getMapper()->getDbTable()->select($withFromPart);
        if ($resetColumns)
    $select->reset(Zend_Db_Select::COLUMNS);
        if ($resetOrder)
                     $select->reset(Zend_Db_Select::ORDER);
        if ($resetLimitOffset)
                     $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        return $select;

    }

    /**
     * returns a Zend_Paginator class from a query string
     *
     * @param string $sql
     * @return Zend_Paginator 
     */
    public function query2Paginator($sql) {
        $result=$this->getMapper()->getDbTable()->getAdapter()->fetchAll($sql);
        $paginator = Zend_Paginator::factory($result);
        return $paginator;
    }

    public function getTableName() {
        return $this->getMapper()->getDbTable()->getTableName();
    }

}