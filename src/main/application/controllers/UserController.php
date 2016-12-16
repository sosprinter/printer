<?php

require_once 'Users.php';

class UserController extends Zend_Controller_Action
{
    const ER_DUP_KEY = 23000;

    public function init()
    {
        Zend_Auth::getInstance()->hasIdentity();
        $this->_logger = Zend_Registry::get("log");
        $this->users =  new Users();
    }

    public function indexAction(){

    }

    public function addAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {

            $name = $request->getParam('name');
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $phone = $request->getParam('phone');

            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'id' => hash('crc32', $email . strtotime($current_data)),
                'name' => $name,
                'email' => $email,
                'password' => md5($password),
                'phone' => $phone,
                'create' => $current_data,
                'last_access' => $current_data
            );
            try {
                $this->users->insert($data);
                $this->_redirect('/login/');
            }catch(Exception $ex){
                if($ex->getCode()  === self::ER_DUP_KEY){
                    echo "User o Email gia' esitenti!";
                }
            }
        }
    }

    public function editAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $id = $request->getParam('id');
            $name = $request->getParam('name');
            $email = $request->getParam('email');
            $password = $request->getParam('password');
            $phone = $request->getParam('phone');

            $data = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'last_access' => date("Y/m/d H:i:s")
            );

            if(!empty($password))
                $data['password'] = md5($password);

            $where = $this->users->getAdapter()->quoteInto('id = ?', $id);
            $this->users->update($data, $where);
            $this->_redirect('/login/logout');
        }else{
            $object_user = $this->users->getUser(Zend_Auth::getInstance()->getIdentity()->id);
            $this->view->object_user = $object_user;
        }
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $where = $this->users->getAdapter()->quoteInto('id = ?', $id);
        $this->users->delete($where);
    }

    public function getuserAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $user_info = $this->users->getUser($id);
    }

}