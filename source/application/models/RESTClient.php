<?php

/**
 * REST Client Helper
 * @author TuanAnh
 *
 */
class RESTClient{

    const USER_AGENT = 'RESTClient';

    private $with_curl;
    private $_curl_info;
    private $_response;
    private $_error_code;
    private $_error_message;
    private $_timeout = 60;
    private static $_instance = null;
    private $_httpcode = null;
    private $_login_name = null;
    
    /**
     * init of singleton object
     * @return NULL
     */
    public static function getInstance() {
        if( self::$_instance == null ) {
            self::$_instance = new RESTClient();
        }
        return self::$_instance;
    }

    /**
     * Get name of current logged in user
     * @return string
     */
    private function _getLoginName() {
        if ( empty( $this->_login_name ) == true ) {
            $login_info = UtilAuth::getLoginInfo();
            $this->_login_name = $login_info->full_name;
        }
        return $this->_login_name;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        if( function_exists( "curl_init" ) ) {
            $this->with_curl = TRUE;
        } else {
            $this->with_curl = FALSE;
        }
    }

    /**
       * Get error number of cUrl transfer
       * @return int
        */
    public function getErrorCode() {
        return $this->_error_code;
    }

    /**
     *  Get Curl tranfer info
     *  @return mixed content.
     */
    public function getHttpCode() {
        return $this->_httpcode;
    }

    /**
       * Set timeout for API request
       * @param int $timeout: Number of time in second when the request expires
        */
    public function setTimeOut( $timeout ) {
        $this->_timeout = ( int )$timeout;
    }

    /**
     *  Get error text of cUrl transfer
     *  @return string
     */
    public function getErrorMessage() {
        return $this->_error_message;
    }

    /**
     *  Get Curl tranfer info
     *  @return mixed content.
     */
    public function getCurlInfo() {
        return $this->_curl_info;
    }

    /**
     *  Get response result of cUrl transfer
     *  @return the mixed result on success, FALSE on failure.
     */
    public function getResponse() {
        return $this->_response;
    }

    /**
     * Check response
     * @return boolean
     */
    public static function checkResponse() {
        $httpCode = RESTClient::getInstance()->getHttpCode();
        if( $httpCode > 0 ) {
            return true;
        }
        return false;
    }

    /**
     * Encode input params
     * @param array $params
     * @param string $url
     * @return Ambigous <string, unknown>
     */
    public function paramsEncode( $params, $url = '' ) {
        $params_str = '';
        if( is_array( $params ) == true ) {
            $values = array();
            foreach( $params as $key => $value ) {
                $values[] = urlencode( $key ).'='.urlencode( $value );
            }
            $params_str = implode( '&', $values );
        } else {
            $params_str = urlencode( $params );
        }
        $return_url = $params_str;
        if( strlen( $url ) > 0 ) {
            $return_url = $url;
            if( strlen( $params_str ) > 0 ) {
                $return_url .= '?'.$params_str;
            }
        }
        return $return_url;
    }

    /**
     * Create stream context for url when cUrl is not initiated
     * @param string $url
     * @param array $opts
     * @return number
     */
    private function streamContextExe( $url, $opts = array() ) {
        if( empty( $opts ) == true ) {
            $opts = array(
                'http' => array(
                    'method' => "GET",
                    'header' => "User-Agent: ".RESTClient::USER_AGENT."\r\n"
                )
            );
        }
        $context = stream_context_create( $opts );
        $fp = fopen( $url, 'r', false, $context );
        $result = fpassthru( $fp );
        fclose( $fp );
        return $result;
    }

    /**
     * Implement GET method transfer
     * @param string $url
     * @param array $params
     * @param array $header
     * @return Ambigous <string, mixed, number, number>
     */
    private function _get( $url, $params, $header = array() ) {
//         $header = array_merge( $header, array( 'LOGIN-USER' => $this->_getLoginName() ) );
        $url = $this->paramsEncode( $params, $url );
        $this->_response = "";
        if( $this->with_curl == true ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_URL, $url );
//             curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
            curl_setopt( $curl, CURLOPT_HTTPGET, TRUE );
            curl_setopt( $curl, CURLOPT_USERAGENT, RESTClient::USER_AGENT );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt( $curl, CURLOPT_TIMEOUT, $this->_timeout );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
            try {
                $this->_response = curl_exec( $curl );
                $this->_curl_info = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            } catch( Exception $e ) {
                
            }
            $this->_error_code = curl_errno( $curl );
            $this->_error_message = curl_error( $curl );
            $this->_httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

            curl_close( $curl );
        } else {
            $this->_response = $this->streamContextExe( $url );
        }
        return $this->_response;
    }

    /**
     * Implement POST method transfer
     * @param string $url
     * @param array $data
     * @param array $header
     * @return Ambigous <string, mixed, number, number>
     */
    private function _post( $url, $data, $header = array() ) {
        if( isset( $header['content_type'] ) == true ) {
            $content_type = $header['content_type'];
            unset( $header['content_type'] );
        }
        $this->_response = "";
        if( $this->with_curl ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $header );
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt( $curl, CURLOPT_TIMEOUT, $this->_timeout );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt( $curl, CURLOPT_POST, TRUE );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
            curl_setopt( $curl, CURLOPT_USERAGENT, RESTClient::USER_AGENT );
            try {
                $this->_response = curl_exec( $curl );
                $this->_error_message = curl_error( $curl );
            } catch( Exception $e ) {
                
            }
            $this->_error_code = curl_errno( $curl );
            $this->_error_message = curl_error( $curl );
            $this->_httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

            curl_close( $curl );
        } else {
            $content_type = "text/xml";
            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => "User-Agent: ".RESTClient::USER_AGENT."\r\n".
                    "Content-Type: ".$content_type."\r\n".
                    "Content-length: ".strlen( $data )."\r\n",
//                     "LOGIN-USER:" . $this->_getLoginName()."\r\n",
                    'content' => $data ) );

            $this->_response = $this->streamContextExe( $url, $opts );
        }
        return $this->_response;
    }

    /**
     * Implement GET method transfer with JSON data
     * @param string $url
     * @param array $params
     * @param array $options
     * @return mixed
     */
    public function getJson( $url, $params, $options = false ) {
        $start = time();
        $header = array( 'Accept: application/json', 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] );
        $result = $this->_get( $url, $params, $header );
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $result );
        $result = json_decode( $result, $options );
        return $result;
    }

    /**
     * Implement GET method transfer
     * @param string $url
     * @param array $params
     * @param string $content_type
     * @return Ambigous <mixed, Ambigous, string, number, number>
     */
    public function get( $url, $params, $content_type = "text/xml" ) {
        $start = time();
        $header = array( "Content-Type: ".$content_type, 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] );
        $result = $this->_get( $url, $params, $header );
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $result );
        // Check content type of xml to xml object
        if( strpos( $content_type, 'xml' ) != false ) {
            $result = $this->_parseXML( $result );
        }
        return $result;
    }

    /**
     * Implement POST method transfer with JSON data
     * @param string $url
     * @param array $data
     * @param array $option
     * @return mixed
     */
    public function postJson( $url, $data, $option = false ) {
        $start = time();
        $header = array( 'Accept: application/json', 'Content-Type: application/json', 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] );
        $result = $this->_post( $url, $data, $header );
        $end = time();
        $period = $end - $start;
//         UtilLogs::logApiForPerformance( $start, $end, $period, $url, $result );
        $result = json_decode( $result, $option );
        return $result;
    }

    /**
     * Implement POST method transfer
     * @param string $url
     * @param array $data
     * @param string $content_type
     * @return Ambigous <mixed, Ambigous, string, number, number>
     */
    public function post( $url, $data, $content_type = "text/xml" ) {
        $start = time();
        $header = array( "Content-Type: ".$content_type, 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] );
        $result = $this->_post( $url, $data, $header );
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $result );
        // Check content type of xml to xml object
        if( strpos( $content_type, 'xml' ) != false ) {
            $result = $this->_parseXML( $result );
        }
        return $result;
    }

    /**
     * Implement PUT method transfer with JSON
     * @param string $url
     * @param array $data
     * @param array $option
     * @return mixed
     */
    public function putJson( $url, $data, $option = false ) {
        $start = time();

        if( $this->with_curl ) {
            $tenMB = 10 * 1024 * 1024;
            $fh = fopen( "php://temp/maxmemory:{$tenMB}", 'rw' );
            fwrite( $fh, $data );
            rewind( $fh );
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Accept: application/json', 'Content-Type: application/json', 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR']) );
            curl_setopt( $curl, CURLOPT_USERAGENT, RESTClient::USER_AGENT );
            curl_setopt( $curl, CURLOPT_INFILE, $fh );
            curl_setopt( $curl, CURLOPT_INFILESIZE, strlen( $data ) );
            curl_setopt( $curl, CURLOPT_TIMEOUT, $this->_timeout );
            curl_setopt( $curl, CURLOPT_PUT, 1 );
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            try {
                $this->_response = curl_exec( $curl );
                $this->_curl_info = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            } catch( Exception $e ) {
                
            }
            $this->_error_code = curl_errno( $curl );
            $this->_error_message = curl_error( $curl );
            $this->_httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

            curl_close( $curl );

            fclose( $fh );
        } else {
            $opts = array(
                'http' => array(
                    'method' => "PUT",
                    'header' => "User-Agent: ".RESTClient::USER_AGENT."\r\n".
                    "Content-Type: ".$content_type."\r\n".
                    "Content-length: ".strlen( $data )."\r\n",
//                     "LOGIN-USER:" . $this->_getLoginName()."\r\n",
                    'content' => $data ) );
            $context = stream_context_create( $opts );
            $fp = fopen( $url, 'r', false, $context );
            $this->_response = fpassthru( $fp );
            fclose( $fp );
        }
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $this->_response );
        $this->_response = json_decode( $this->_response, $option );
        return $this->_response;
    }

    /**
     * Implement PUT method transfer
     * @param string $url
     * @param array $data
     * @param string $content_type
     * @return mixed
     */
    public function put( $url, $data, $content_type = "text/xml" ) {
        $start = time();

        if( $this->with_curl ) {
            $tenMB = 10 * 1024 * 1024;
            $fh = fopen( "php://temp/maxmemory:{$tenMB}", 'rw' );
            fwrite( $fh, $data );
            rewind( $fh );
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( "Content-Type: ".$content_type, 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] ) );
            curl_setopt( $curl, CURLOPT_USERAGENT, RESTClient::USER_AGENT );
            curl_setopt( $curl, CURLOPT_INFILE, $fh );
            curl_setopt( $curl, CURLOPT_INFILESIZE, strlen( $data ) );
            curl_setopt( $curl, CURLOPT_TIMEOUT, $this->_timeout );
            curl_setopt( $curl, CURLOPT_PUT, 1 );
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            try {
                $this->_response = curl_exec( $curl );
                $this->_curl_info = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            } catch( Exception $e ) {
                
            }
            $this->_error_code = curl_errno( $curl );
            $this->_error_message = curl_error( $curl );
            $this->_httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

            curl_close( $curl );
            fclose( $fh );
        } else {
            $opts = array(
                'http' => array(
                    'method' => "PUT",
                    'header' => "User-Agent: ".RESTClient::USER_AGENT."\r\n".
                    "Content-Type: ".$content_type."\r\n".
                    "Content-length: ".strlen( $data )."\r\n",
//                     "LOGIN-USER:" . $this->_getLoginName()."\r\n",
                    'content' => $data ) );
            $context = stream_context_create( $opts );
            $fp = fopen( $url, 'r', false, $context );
            $this->_response = fpassthru( $fp );
            fclose( $fp );
        }
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $this->_response );
        // Check content type of xml to xml object
        if( strpos( $content_type, 'xml' ) != false ) {
            $this->_response = $this->_parseXML( $this->_response );
        }
        return $this->_response;
    }

    /**
     * Implement DELETE method transfer
     * @param string $url
     * @param array $params
     * @param string $content_type
     * @return Ambigous <string, mixed>
     */
    public function delete( $url, $params = '', $content_type = "" ) {
        $start = time();
        $params_str = "?";
        if( is_array( $params ) ) {
            foreach( $params as $key => $value ) {
                $params_str .= urlencode( $key )."=".
                        urlencode( $value )."&";
            }
        } else {
            $params_str .= $params;
        }
        $url .= $params_str;
        if( $this->with_curl ) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'REMOTE_ADDR: '.$_SERVER['REMOTE_ADDR'] ) );
            curl_setopt( $curl, CURLOPT_URL, $url );
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "DELETE" );
            curl_setopt( $curl, CURLOPT_USERAGENT, RESTClient::USER_AGENT );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

            try {
                $this->_response = curl_exec( $curl );
                $this->_curl_info = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            } catch( Exception $e ) {
                
            }
            $this->_error_code = curl_errno( $curl );
            $this->_error_message = curl_error( $curl );
            $this->_httpcode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

            curl_close( $curl );
        } else {
            $opts = array(
                'http' => array(
                    'method' => "DELETE",
                    'header' => "User-Agent: ".RESTClient ::USER_AGENT."\r\n",
//                     "LOGIN-USER:" . $this->_getLoginName()."\r\n"
            ) );

            $this->_response = $this->streamContextExe( $url, $opts );
        }
        $end = time();
        $period = $end - $start;
        UtilLogs::logApiForPerformance( $start, $end, $period, $url, $this->_response );
        // Check content type of xml to xml object
        if( strpos( $content_type, 'xml' ) != false ) {
            $this->_response = $this->_parseXML( $this->_response );
        }
        return $this->_response;
    }

    /**
     * Parse XML response
     * @param unknown_type $xmlString
     * @return mixed
     */
    private function _parseXML( $xmlString ) {
        $result = null;
        try {
            //Workaround for XML with XML string has the same attribute name in second level.
            $result = new Zend_Config_Xml( '<?xml version="1.0" encoding="UTF-8"?><Root>'.$xmlString.'</Root>' );
            $result = $result->current();
        } catch( Exception $ex ) {
            
        }
        return $result;
    }

}
