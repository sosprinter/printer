<?php


 class Users extends Zend_Db_Table_Abstract{
 	protected $_primary = 'id';
	public $id;


    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'users';
        parent::_setupTableName();
    }

    public function getPass($username){

    	$where = $this->getAdapter()->quoteInto('email = ?', $username);
    	$qry = $this->select()->where($where);
    	return $this->fetchAll($qry);
    }

    public function getAll() {
    	$qry = $this->select()->order("email asc");
    	return $this->fetchAll($qry);
    }

    public function getUser($id) {
        $where = $this->find($id);
        if($where->current())
            return $where->current()->toArray();
        else
            return null;
    }

 }
