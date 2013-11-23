<?php
require_once('Zend/Db/Table/Abstract.php');
require_once('MainDbTable.php');
/**
 * Add your description here
 * 
 * @author Thomas Tran
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

class Sos_Model_DbTable_Setting extends MainDbTable
{
        /**
         * $_name - name of database table
         *
         * @var string
         */
	protected $_name='setting';

        /**
         * $_id - this is the primary key name

         *
         * @var string

         */
	protected $_id='id';

}


