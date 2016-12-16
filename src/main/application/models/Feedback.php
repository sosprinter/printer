<?php


class Feedback extends Zend_Db_Table_Abstract{
    protected $_primary = 'id';
    public $id;

    public static $SEND = "user";
    public static $RECEIPT = "destination";

    protected function _setupDatabaseAdapter(){
        $this->_db = Zend_Registry::get("sp_db");
    }

    protected function _setupTableName(){
        $this->_name = 'feedback';
        parent::_setupTableName();
    }

    public function getFeedback($user, $type) {

        $where= $this->getAdapter()->quoteInto($type.' = ?', $user);
        $qry = $this->select()->where($where);

        return $this->fetchAll($qry)->toArray();
    }

    public function getFeedbackById($user, $id) {

        $where= $this->getAdapter()->quoteInto('user = ? AND ', $user).$this->getAdapter()->quoteInto('id = ?', $id);
        $qry = $this->select()->where($where);

        return $this->fetchAll($qry)->current()->toArray();
    }

    public function getSendFeedback($user) {
        $where = $this->getAdapter()->quoteInto('user = ?', $user);
        $qry = $this->select()->where($where);
        return $this->fetchAll($qry)->toArray();
    }

    public function getMediaFeedback($user) {
        $where= $this->getAdapter()->quoteInto('destination = ?', $user);
        $qry = $this->select()->where($where);
        $list = $this->fetchAll($qry)->toArray();
        $count = 0 ;
        foreach($list as $value){
            $count = $count + $value['value'];
        }
        if($count === 0)
            return array('value' => 0.0 , 'comments' =>count($list));
        return array('value' => round($count/count($list),2) , 'comments' =>count($list));
    }

}
