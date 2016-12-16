<?php

require_once 'Location.php';
require_once 'Users.php';
require_once 'Feedback.php';
require_once 'Printer.php';
require_once 'Resource.php';
require_once 'GoogleApi.php';

class Map_IndexController extends Zend_Controller_Action
{

    private static $KM_COORDINATE = 0.01;
    private static $KM = 1;
    private $googleApi =  NULL;

    public function init(){
        $this->location = new Location();
        $this->users = new Users();
        $this->feedback = new Feedback();
        $this->printer = new Printer();
        $this->resource = new Resource();
        $this->googleApi  = new GoogleApi();
    }

    public function indexAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $address = $request->getParam('address');
            $number = $request->getParam('number');
            $address =  $this->googleApi->get_coordinate_by_address($address,$number,"Roma");
            var_dump($address);
        }
    }

    public function selectaddressAction(){
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $coordinates = $request->getParam('coordinates');
            $object_array = json_decode($coordinates);
            $address = $object_array->address;
            $number = $object_array->number;

            $address =  $this->googleApi->get_coordinate_by_address($address,$number,"Roma");
            echo json_encode($address);
        }

    }

    public function printerAction(){
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $coordinates = $request->getParam('coordinates');

            //******* TEST **********
            //#######################
             $coordinates_test = $request->getParam('coordinates_TEST');
             if(isset($coordinates_test))
                 $coordinates = json_encode($coordinates_test);
            //#######################
            //***********************

            $object_array = json_decode($coordinates);
            if(isset($object_array->x) && isset($object_array->y)) {
                $lat = $object_array->x;
                $lng = $object_array->y;
                $km = Map_IndexController::$KM;

                $km_rang = (Map_IndexController::$KM_COORDINATE * $km);
                $lat_start = $lat - $km_rang;
                $lat_end = $lat + $km_rang;
                $lng_start = $lng - $km_rang;
                $lng_end = $lng + $km_rang;
                $array_printers = $this->location->getRangeLocation($lat_start, $lat_end, $lng_start, $lng_end);

                $request = array();
                foreach ($array_printers as $key => $value) {
                    $object_user = $this->users->getUser($value['user']);

                    $object_feedback = $this->feedback->getMediaFeedback($value['user']);
                    $object_printer = $this->printer->getPrinter($value['user']);
                    if (array_key_exists('id', $object_printer)) {

                        $object_resource = $this->resource->getResource($object_printer['id']);

                        $name = $object_user['name'];
                        $lat = floatval($value['lat']);
                        $lng = floatval($value['lng']);

                        $num = $object_feedback['value'];
                        if (round($num - (0.01), 0) < ($num))
                            $valutation = (round($num - (0.01), 0) + 0.5);
                        else
                            $valutation = round($num, 0);

                        $token = md5($object_user['id'] . $object_printer['id']);

                        $string = file_get_contents("/../views/scripts/index/description.phtml", true);
                        $string = str_replace("{USER}", $object_user['name'], $string);
                        $string = str_replace("{STAR}", $this->getStarImage($valutation), $string);
                        $string = str_replace("{BRAND}", $object_printer['brand'], $string);
                        $string = str_replace("{INFO_PRINTER}", $object_printer['type'] . "," . $object_printer['color'], $string);
                        $string = str_replace("{VALUTATION}", $valutation, $string);
                        $string = str_replace("{PRICE}", array_key_exists("price_bn", $object_resource) ? $object_resource['price_bn'] : "-", $string);
                        $string = str_replace("{PRICE_COLOR}", array_key_exists("price_color", $object_resource) ? $object_resource['price_color'] : "-", $string);
                        $string = str_replace("{LAT}", $lat, $string);
                        $string = str_replace("{LNG}", $lng, $string);
                        $string = str_replace("{DESTINATION}", $object_user['id'], $string);
                        $string = str_replace("{TOKEN}", $token, $string);
                        if (!Zend_Auth::getInstance()->hasIdentity())
                            $string = str_replace("{HIDDEN}", "hidden", $string);
                        else
                            $string = str_replace("{HIDDEN}", "", $string);

                        $description = $string;


                        $icon = $object_printer['color'];

                        if (Zend_Auth::getInstance()->hasIdentity()) { //se non loggato visualizzo tutto
                            if (Zend_Auth::getInstance()->getIdentity()->id != $object_user['id']) { //escludo PRINTER USER ATTUALE
                                $request[] = array('id' => $name, 'coordinate' => array("lat" => $lat, "lng" => $lng), "description" => $description, "icon" => $icon, "token" => $token);
                            }
                        } else {
                            $request[] = array('id' => $name, 'coordinate' => array("lat" => $lat, "lng" => $lng), "description" => $description, "icon" => $icon, "token" => $token);
                        }
                    }
                }
                echo json_encode($request);
            }else
                echo null;

        }
    }


    private function getStarImage($valutation){

            $valutation_round = round($valutation);
            $star = "";
            for($n = 1 ; $n <= $valutation; $n++ ) {
                $star = $star."<img width=\"15px\" height=\"15px;\" src=\"/image/STAR.png\">";
            }
            if($valutation_round > $valutation){
                $star = $star."<img width=\"15px\" height=\"15px;\" src=\"/image/STAR_M.png\">";
                $n = $n +1;
            }
            for($n; $n < 5; $n++ ) {
                $star = $star."<img  width=\"15px\" height=\"15px;\" src=\"/image/STAR_E.png\">";
            }
            return $star;
    }


    public function testAction(){

    }

    public function descriptionAction(){

    }
}

?>