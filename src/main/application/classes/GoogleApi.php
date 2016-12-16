<?php

class GoogleApi
{

    public static $KEY = "AIzaSyDgbg1JE7OtJVRBF7j6iahXG7arUp-mXws";

    public static $URL_COORDINATE = "https://maps.google.com/maps/api/geocode/xml?sensor=false&address=";
    public static $URL_ADDRESS = "https://maps.googleapis.com/maps/api/geocode/xml?&key=";


    public function get_coordinate_by_address($address,$number,$city)
    {
        $encoding_address = $address.",". $number.",".$city;
        $url = GoogleApi::$URL_COORDINATE.urlencode($encoding_address);
        return $this->_callGoogleApi($url);
    }

    private function get_address_by_coordinate($lat, $long){

        $LAT_LOG = $lat . "," . $long;
        $url = GoogleApi::$URL_COORDINATE.GoogleApi::$KEY."&latlng=".urlencode($LAT_LOG);
        return $this->_callGoogleApi($url);
    }


    private function _callGoogleApi($url){

        try{
            $client = new Zend_Http_Client();

            $client->setUri($url);

            $client->setConfig(array('timeout' => 500,'maxredirects' => 0));
            $client->setHeaders(Zend_Http_Client::CONTENT_TYPE, 'application/xml; charset=UTF-8');
            $client->setMethod(Zend_Http_Client::GET);

            $response = $client->request();
            $XMLresult = $response->getBody();

            $XMLobject = new SimpleXMLElement($XMLresult);
            $list_result = array();
            if($XMLobject->status == 'OK' ){

                $type = array("street_number"=>"street_number",
                    "route" => "route",
                    "administrative_area_level_3" => "city",
                    "administrative_area_level_2" => "abb_city",
                    "administrative_area_level_1" => "abb_region",
                    "country" => "country",
                    "postal_code" => "postal_code");

                $list_component = $XMLobject->result->address_component;

                $list_result['formatted_address'] = trim($XMLobject->result->formatted_address);
                $list_result['lat'] = trim($XMLobject->result->geometry->location->lat);
                $list_result['lng'] = trim($XMLobject->result->geometry->location->lng);

                foreach($list_component as $key => $value){
                    if(array_key_exists(trim($value->type),$type)){
                        $list_result[$type[trim($value->type)]] = trim($value->short_name);
                    }
                }

                if(count($list_result) != 10){
                    return false;
                }
            }else {
                return false;
            }

            return $list_result;

        } catch (Zend_Http_Client_Adapter_Exception $e) {
            throw new Exception($e->getMessage());
        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}