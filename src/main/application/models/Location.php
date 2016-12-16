<?php


 class Location extends Zend_Db_Table_Abstract{
 	protected $_primary = 'id';
	public $id;


    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'location';
        parent::_setupTableName();
    }

     public function getLocation($user) {
         $where = $this->getAdapter()->quoteInto('user = ?', $user);
         $qry = $this->select()->where($where);
         return $this->fetchAll($qry)->toArray();
     }

     public function getRangeLocation($lat_start,$lat_end,$lng_start,$lng_end){

         $where = $this->getAdapter()->quoteInto("(lat BETWEEN ? ",$lat_start).
                  $this->getAdapter()->quoteInto("AND ? ) AND ",$lat_end).
                  $this->getAdapter()->quoteInto(" (lng BETWEEN ? ", $lng_start).
                  $this->getAdapter()->quoteInto(" AND ?)", $lng_end);;

         $qry = $this->select()->where($where);
         return $this->fetchAll($qry)->toArray();

     }
 }
