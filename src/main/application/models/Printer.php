<?php


 class Printer extends Zend_Db_Table_Abstract{
 	protected $_primary = 'id';
	public $id;


    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'printers';
        parent::_setupTableName();
    }

     public function getPrinter($user) {
         $where = $this->getAdapter()->quoteInto('user = ?', $user);
         $qry = $this->select()->where($where);
         $result  = $this->fetchAll($qry)->toArray() ? $this->fetchAll($qry)->toArray()[0] :array();
         return $result;
     }
 }
