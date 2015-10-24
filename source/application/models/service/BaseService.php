<?php

/**
 * Base Service
 * @author Phuong Nguyen
 *
 */
class BaseService{

    protected $retData;
    public static function checkResponse( $response ) {
    	if( empty( $response ) == true || (empty( $response ["errorCode"] ) == false && intval( $response ["errorCode"] ) > 0) ) {
    		return false;
    	}
    	return true;
    }
    public function serviceResponse( $data = array(), $response = array(), $errors = '' ) {
    	$this->retData['PostData'] = $data;
    	$this->retData['Response'] = (empty($response['data']) == false) ? $response['data'] : $response;
    	$this->retData['Errors'] = $errors;
    	return $this->retData;
    }
    public function checkInputData( $xml, &$data ,$ignore = false ) {
    	$errors = UtilValidator::check($xml,$data,$option = array('isTranslate'=>true),$ignore);
    	return $errors;
    }
}
