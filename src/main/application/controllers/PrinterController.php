<?php

require_once 'Printer.php';

class PrinterController extends Zend_Controller_Action
{

    public function init(){

        $this->_logger = Zend_Registry::get("log");
        $this->printer = new Printer();
    }

    public function indexAction(){

    }


    public function addAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $brand = $request->getParam('brand');
            $type = $request->getParam('type');
            $color = $request->getParam('color');
            $format = $request->getParam('format');
            $resolution = $request->getParam('resolution');


            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'id' => hash('crc32', $user . strtotime($current_data)),
                'user' => $user,
                'brand' => $brand,
                'type' => $type,
                'color' => $color,
                'format' => json_encode($format),
                'resolution' => $resolution,
                'created' => $current_data,
                'update_ts' => $current_data
            );

            $this->printer->insert($data);

            $userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $userInfo->printer = $data['id'];

            $this->_redirect('/printer/edit');
        }
    }

    public function editAction(){

        $request = $this->getRequest();

        if ($request->isPost()) {
            $id = $request->getParam('id');
            $brand = $request->getParam('brand');
            $type = $request->getParam('type');
            $color = $request->getParam('color');
            $format = $request->getParam('format');
            $resolution = $request->getParam('resolution');

            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'brand' => $brand,
                'type' => $type,
                'color' => $color,
                'format' => json_encode($format),
                'resolution' => $resolution,
                'update_ts' => $current_data
            );

            $this->printer->update($data,array('id = ?' => $id));

            $this->_redirect('/');
        }else{
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $list_printer = $this->printer->getPrinter($user);
            $this->view->list_printer = $list_printer;
        }

    }

    public function deleteAction(){
        $id = Zend_Auth::getInstance()->getIdentity()->printer;
        $where = $this->printer->getAdapter()->quoteInto('id = ?', $id);
        $this->printer->delete($where);
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $userInfo->printer = null;
        $this->_redirect('index');
    }

}