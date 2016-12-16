<?php
require_once 'Message.php';
require_once 'Users.php';

class MessageController extends Zend_Controller_Action
{

    public function init(){
        $this->_logger = Zend_Registry::get("log");
        $this->message = new Message();
        $this->users = new Users();
    }

    public function indexAction(){
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $list_message = $this->message->getMessage($user,Message::$RECEIPT);
        $object_list_message = $this->createTable($list_message);
        $this->view->list_message = $object_list_message;
        var_dump();
        $this->view->count_r = 23;
        $this->view->count_i = 100;
    }

    public function createAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $title = $request->getParam('title');
            $message = $request->getParam('message');
            $destination = $request->getParam('destination');

            $current_data = date("Y/m/d H:i:s");
            $data = array(
                'id' => hash('crc32', $user . strtotime($current_data)),
                'user' => $user,
                'title' => $title,
                'message' => $message,
                'destination' => $destination,
                'type' => "I",
                'status' => "L",
                'create' => $current_data
            );

            $data2 = array(
                'id' => hash('crc32', $destination . strtotime($current_data)),
                'user' => $destination,
                'title' => $title,
                'message' => $message,
                'destination' => $user,
                'type' => "R",
                'status' => "NL",
                'create' => $current_data
            );

            $this->message->insert($data);
            $this->message->insert($data2);

            $this->_redirect('/message/sent');
        }



        $destination = $request->getParam('destination');
        $object_user = $this->users->getUser($destination);
        if($object_user)
          $this->view->destination = $object_user;
        else
          $this->_redirect('/message');
    }

    public function sentAction(){
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $list_message = $this->message->getMessage($user,Message::$SEND);
        $object_list_message = $this->createTable($list_message);
        $this->view->list_message = $object_list_message;
        $this->renderScript('message/index.phtml');
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $email = $request->getParam('email');
        $id = Zend_Auth::getInstance()->getIdentity()->id;
        $where = array();
        $where[] = $this->message->getAdapter()->quoteInto('id = ?', $email);
        $where[] = $this->message->getAdapter()->quoteInto('user = ?', $id);
        $this->message->delete($where);
        $this->_redirect('/message');
    }

    public function viewAction(){
        $request = $this->getRequest();
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $email = $request->getParam('email');
        $response = $this->message->getMessageById($user,$email);
        $object_message = $this->getObjectMessage($response);
        $where = array();
        $where[] = $this->message->getAdapter()->quoteInto('id = ?', $email);
        $where[] = $this->message->getAdapter()->quoteInto('user = ?', $user);
        $this->message->update(array('status'=>"L"),$where);
        $this->view->object_message = $object_message;
    }


    private function createTable($list_message){
        $object_list_message =  array();
        foreach ($list_message as $key => $val){
            $object_list_message[] = $this->getObjectMessage($val);
        }
        return $object_list_message;
    }

    private function getObjectMessage($val){

        $object_message =  array();
        $object_user = $this->users->getUser($val['user']);
        $object_destination = $this->users->getUser($val['destination']);
        $object_message['email'] = $val['id'];
        $object_message['user'] = $object_user;
        $object_message['destination'] = $object_destination;
        $object_message['message'] = $val['message'];
        $object_message['title'] = $val['title'];
        $object_message['status'] = $val['status'];
        $object_message['create'] = $val['create'];

        return $object_message;
    }


}