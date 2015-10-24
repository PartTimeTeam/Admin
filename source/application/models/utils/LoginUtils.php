<?php
/**
 * Process for Content to API
 * @author Phuong Nguyen
 *
 */
class LoginUtils extends BaseUtils {
    
    private static $_instance = null;
    
    public static function getInstance() {
        if( self::$_instance == null ) {
            self::$_instance = new LoginUtils();
        }
        self::$_instance->setUri( '/login' );
        self::$_instance->setUtilName( Constains::$ControllerName[Constains::LOGIN] );
        return self::$_instance;
    }
    /**
     * user login
     * @param unknown $inputParam['username,password']
     */
    public function userLogin( $data, $action = '' ){
    	$info = array();
    	$url = API_URL.$this->getUri();
    	if( empty( $action ) == false ) {
    		$url .= $action.'/';
    	}
    	if( is_array( $data ) == true ) {
    		$data = Zend_Json_Encoder::encode( $data );
    	}
    	try {
    		$info = RESTClient::getInstance()->postJson( $url, $data, true );
    		// check info
    		if( RESTClient::checkResponse() == false ) {
    			$info = array();
    		}
    	} catch( Exception $exc ) {
    			//TODO
    	}
    	
    	return $info;
    }
}
