<?php
require_once 'Users.php';

class IndexController extends Zend_Controller_Action
{

    public function init(){
        $this->_logger = Zend_Registry::get("log");
    }

    public function indexAction(){
       if(Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect('/Map');
    }

}