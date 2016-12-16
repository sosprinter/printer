<?php

require_once 'Location.php';
require_once 'GoogleApi.php';

class LocationController extends Zend_Controller_Action
{
    public $googleApi =  NULL;

    public function init()
    {
        $this->googleApi  =  new GoogleApi();
        $this->_logger = Zend_Registry::get("log");
        $this->location = new Location();
    }

    public function indexAction()
    {
      /*
             $address ="Via Salone";
             $number="";
             $city ="ROMA";
             var_dump($this->get_coordinate_by_address($address,$number,$city));
             var_dump($this->get_address_by_coordinate(41.9376122,12.6211786));
      */
    }

    public function testAction(){

    }

    public function addressAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $address = $request->getParam('address');
            $number = $request->getParam('number');
            $city = $request->getParam('city');
            $result_location = $this->get_coordinate_by_address($address, $number, $city);
            $this->add($result_location);
            $this->_redirect('location/editaddress');
        }
    }

    public function editaddressAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $address = $request->getParam('address');
            $number = $request->getParam('number');
            $city = $request->getParam('city');
            $result_location = $this->get_coordinate_by_address($address, $number, $city);
            $this->edit($result_location);
            $this->_redirect('location/editaddress');
        }else{
            $user = Zend_Auth::getInstance()->getIdentity()->id;
            $list_location = $this->location->getLocation($user);
            if($list_location)
                $this->view->list_location = $list_location[0];
        }
    }

    public function coordinateAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $lat = $request->getParam('lat');
            $lng = $request->getParam('lng');
            $result_location = $this->get_address_by_coordinate($lat, $lng);
            $this->add($result_location);
            echo "OK";
        }
    }

    public function editcoordinateAction(){
        $request = $this->getRequest();

        if ($request->isPost()) {
            $lat = $request->getParam('lat');
            $lng = $request->getParam('lng');
            $result_location = $this->get_address_by_coordinate($lat, $lng);
            $this->edit($result_location);
            echo "OK";
        }
    }


    public function add($result_location){
        $request = $this->getRequest();

        if ($request->isPost()) {
            if(!$result_location){
                echo "La via non trovata!";
            }else{
                $user = Zend_Auth::getInstance()->getIdentity()->id;

                $current_data = date("Y/m/d H:i:s");

                $data = array(
                    'id' => hash('crc32', $user . strtotime($current_data)),
                    'user' => $user,
                    'formatted_address' => $result_location['formatted_address'],
                    'lat' => $result_location['lat'],
                    'lng' => $result_location['lng'],
                    'street_number' => $result_location['street_number'],
                    'route' => $result_location['route'],
                    'city' => $result_location['city'],
                    'abb_city' => $result_location['abb_city'],
                    'abb_region' => $result_location['abb_region'],
                    'country' => $result_location['country'],
                    'postal_code' => $result_location['postal_code'],
                    'created' => $current_data,
                    'update_ts' => $current_data
                );

                $userInfo = Zend_Auth::getInstance()->getStorage()->read();
                $userInfo->location = $data['id'];

                $this->location->insert($data);
            }
        }
    }

    public function edit($result_location)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            if(!$result_location){
                echo "La via non trovata!";
            }else{
                echo "Via Registrata!";

                $user = Zend_Auth::getInstance()->getIdentity()->id;

                $current_data = date("Y/m/d H:i:s");

                $data = array(
                    'formatted_address' => $result_location['formatted_address'],
                    'lat' => $result_location['lat'],
                    'lng' => $result_location['lng'],
                    'street_number' => $result_location['street_number'],
                    'route' => $result_location['route'],
                    'city' => $result_location['city'],
                    'abb_city' => $result_location['abb_city'],
                    'abb_region' => $result_location['abb_region'],
                    'country' => $result_location['country'],
                    'postal_code' => $result_location['postal_code'],
                    'update_ts' => $current_data
                );

                $where = $this->location->getAdapter()->quoteInto('user = ?', $user);
                $this->location->update($data,$where);
            }
        }
    }

    public function deleteAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $userInfo->location = null;
        $user = Zend_Auth::getInstance()->getIdentity()->id;
        $where = $this->location->getAdapter()->quoteInto('user = ?', $user);
        $this->location->delete($where);
        $this->_redirect('location');
    }

    public function get_coordinate_by_address($address,$number,$city){
        return $this->googleApi->get_coordinate_by_address($address,$number,$city);
    }

    private function get_address_by_coordinate($lat, $long){
        return $this->googleApi->get_address_by_coordinate($lat, $long);
    }
}