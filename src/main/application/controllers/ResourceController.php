<?php
require_once 'Resource.php';
require_once 'Printer.php';
require_once 'Users.php';

class ResourceController extends Zend_Controller_Action
{

    private $resource = null;

    public function init()
    {
        $this->_logger = Zend_Registry::get("log");
        $this->resource = new Resource();
        $this->printer = new Printer();
        $this->users = new Users();
    }

    public function indexAction()
    {
    }

    public function addAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $object_printer = $this->printer->getPrinter($user);
            $printer = $object_printer[0]['id'];
            $sheets = $request->getParam('sheets');
            $ink = $request->getParam('ink');
            $price_color = $request->getParam('price_color');
            $price_bn = $request->getParam('price_bn');


            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'id' => hash('crc32', $printer . strtotime($current_data)),
                'printer' => $printer,
                'sheets' => $sheets,
                'ink' => $ink,
                'price_color' => floatval($price_color),
                'price_bn' => floatval($price_bn),
                'created' => $current_data,
                'update_ts' => $current_data
            );

            $this->resource->insert($data);

            $userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $userInfo->resource = $data['id'];

            $this->_redirect('/resource/edit');
        }
    }

    public function editAction(){

        $request = $this->getRequest();

        if ($request->isPost()) {
            $id =  Zend_Auth::getInstance()->getIdentity()->resource;
            $sheets = $request->getParam('sheets');
            $ink = $request->getParam('ink');
            $price_color = $request->getParam('price_color');
            $price_bn = $request->getParam('price_bn');


            $current_data = date("Y/m/d H:i:s");

            $data = array(
                'sheets' => $sheets,
                'ink' => $ink,
                'price_color' => $price_color,
                'price_bn' => $price_bn,
                'update_ts' => $current_data
            );

            $this->resource->update($data,array('id = ?' => $id));

            $this->_redirect('/resource/edit');
        }else{
            $resource = Zend_Auth::getInstance()->getIdentity()->resource;
            $list_resource = $this->resource->getResourceById($resource)[0];
            $this->view->list_resource = $list_resource;
        }

    }

    public function deleteAction(){
        $id = Zend_Auth::getInstance()->getIdentity()->resource;
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $userInfo->resource = null;
        $where =  $this->resource->getAdapter()->quoteInto('id = ?', $id);
        $this->resource->delete($where);
        $this->_redirect('index');
    }



}