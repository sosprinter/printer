<?php
require_once 'Feedback.php';
require_once 'Users.php';

class FeedbackController extends Zend_Controller_Action
{

    public function init(){
        $this->_logger = Zend_Registry::get("log");
        $this->feedback = new Feedback();
        $this->users = new Users();
    }

    public function indexAction(){
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $list_feedback = $this->feedback->getFeedback($user,Feedback::$RECEIPT);
        $object_list_feedback = $this->createTable($list_feedback);
        $this->view->list_feedback = $object_list_feedback;
    }

    public function addAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $message = $request->getParam('message');
            $destination = $request->getParam('destination');
            $value = $request->getParam('value');

            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'id' => hash('crc32', $user . strtotime($current_data)),
                'user' => $user,
                'message' => $message,
                'value' => $value,
                'destination' => $destination,
                'created' => $current_data,
            );

            $this->feedback->insert($data);
            $this->_redirect('/feedback');
        }else{
            $user = $request->getParam('dest');
            if($user) {
                $object_user = $this->users->getUser($user);
                $this->view->object_user = $object_user;
            }else{
                $this->_redirect('/feedback');
            }
        }

    }

    public function sentAction(){
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $list_feedback = $this->feedback->getFeedback($user,Feedback::$SEND);
        $object_list_feedback = $this->createTable($list_feedback);
        $this->view->list_feedback = $object_list_feedback;
        $this->renderScript('feedback/index.phtml');
    }



    private function createTable($list_feedback){
        $object_list_feedback =  array();
        foreach ($list_feedback as $key => $val){
            $object_list_feedback[] = $this->getObjectFeedback($val);
        }
        return $object_list_feedback;
    }

    private function getObjectFeedback($val){

        $object_feddback =  array();
        $object_user = $this->users->getUser($val['user']);
        $object_destination = $this->users->getUser($val['destination']);
        $object_feddback['user'] = $object_user;
        $object_feddback['destination'] = $object_destination;
        $object_feddback['message'] = $val['message'];
        $object_feddback['value'] = $val['value'];
        $object_feddback['created'] = $val['created'];

        return $object_feddback;
    }

}