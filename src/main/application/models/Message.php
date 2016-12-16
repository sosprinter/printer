<?php


class Message extends Zend_Db_Table_Abstract{
    protected $_primary = 'id';
    public $id;

    public static $SEND = "I";
    public static $RECEIPT = "R";

    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'message';
        parent::_setupTableName();
    }

    public function getMessage($user, $type) {

        $where= $this->getAdapter()->quoteInto('user = ? AND ', $user).$this->getAdapter()->quoteInto('type = ?', $type);
        $qry = $this->select()->where($where);

        return $this->fetchAll($qry)->toArray();
    }

    public function getMessageById($user, $id) {

        $where= $this->getAdapter()->quoteInto('user = ? AND ', $user).$this->getAdapter()->quoteInto('id = ?', $id);
        $qry = $this->select()->where($where);
        return $this->fetchAll($qry)->current()->toArray();
    }

    public function getSendMessage($user) {
        $where = $this->getAdapter()->quoteInto('user = ?', $user);
        $qry = $this->select()->where($where);
        return $this->fetchAll($qry)->toArray();
    }
}
