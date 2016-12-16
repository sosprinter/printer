<?php


class Resource extends Zend_Db_Table_Abstract{
    protected $_primary = 'id';
    public $id;


    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'resource';
        parent::_setupTableName();
    }

    public function getResource($printer) {
        $where = $this->getAdapter()->quoteInto('printer = ?', $printer);
        $qry = $this->select()->where($where);
        return $this->fetchAll($qry)->toArray();
    }

    public function getResourceById($id) {
        $where = $this->getAdapter()->quoteInto('id = ?', $id);
        $qry = $this->select()->where($where);
        $result  = $this->fetchAll($qry)->toArray() ? $this->fetchAll($qry)->toArray() :array();
        return $result;
    }
}