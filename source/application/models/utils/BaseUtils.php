<?php

/**
 * Base Utils
 * @author Phuong Nguyen
 *
 */
class BaseUtils{

    private static $_list = array();
    private static $_info = array();
    protected static $_uri = '';
    protected static $_force = false;
    protected static $_util_name = '';

    /**
     * Get URI
     * @return string
     */
    public function getUri() {
        return self::$_uri;
    }

    /**
     * Set URI
     * @param string $uri
     */
    public function setUri( $uri ) {
        self::$_uri = $uri;
    }

    /**
     * Get Util Name
     * @return string
     */
    public function getUtilName() {
        return self::$_util_name;
    }

    /**
     * Set Util Name
     * @param string $name
     */
    public function setUtilName( $name ) {
        self::$_util_name = $name;
    }
	/**
	 * get real url
	 */
    public function getRealUri( $url ){
    	if( substr( $url , -1, 1) != "/") {
    		return $url.'/';
    	} 
    	return $url;
    }

    /**
     * Implement Get Tag Info from API (Method API: GET)
     * @param number $id
     * @return array
     */
    public function getInfo( $id, $action = '' ) {
        if( empty( $list ) == true ) {
            if( empty( $id ) == true  ) { //|| is_numeric( $id ) == false
                return self::$_info[$this->getUri()];
            }
            $url = API_URL.$this->getUri().'/';
            if( empty( $action ) == false ) {
                $url .= $action.'/';
            }
            $key = md5( $url );
            if( empty( self::$_info[$key] ) == true ) {
                try {
                    self::$_info[$key] = RESTClient::getInstance()->getJson( $url.$id, array(), true );
                    if( RESTClient::checkResponse() == false ) {
                        self::$_info[$key] = array();
                    } elseif( empty( $keyC ) == false ) {
                        UtilCache::saveUserCache( $keyC, self::$_info[$key], 0, $tags );
                    }
                } catch( Exception $exc ) {
                    //TODO
                }
            }

            $list = self::$_info[$key];
        }

        return $list;
    }
    public function get_ip_address() {
    	// check for shared internet/ISP IP
    	if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
    		return $_SERVER['HTTP_CLIENT_IP'];
    	}
    
    	// check for IPs passing through proxies
    	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    		// check if multiple ips exist in var
    		if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
    			$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    			foreach ($iplist as $ip) {
    				if ($this->validate_ip($ip))
    					return $ip;
    			}
    		} else {
    			if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
    				return $_SERVER['HTTP_X_FORWARDED_FOR'];
    		}
    	}
    	if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
    		return $_SERVER['HTTP_X_FORWARDED'];
    	if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
    		return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    	if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
    		return $_SERVER['HTTP_FORWARDED_FOR'];
    	if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validate_ip($_SERVER['HTTP_FORWARDED']))
    		return $_SERVER['HTTP_FORWARDED'];
    
    	// return unreliable ip since all else failed
    	return $_SERVER['REMOTE_ADDR'];
    }
    private function validate_ip($ip) {
    	if (strtolower($ip) === 'unknown')
    		return false;
    
    	// generate ipv4 network address
    	$ip = ip2long($ip);
    
    	// if the ip is set and not equivalent to 255.255.255.255
    	if ($ip !== false && $ip !== -1) {
    		// make sure to get unsigned long representation of ip
    		// due to discrepancies between 32 and 64 bit OSes and
    		// signed numbers (ints default to signed in PHP)
    		$ip = sprintf('%u', $ip);
    		// do private network range checking
    		if ($ip >= 0 && $ip <= 50331647) return false;
    		if ($ip >= 167772160 && $ip <= 184549375) return false;
    		if ($ip >= 2130706432 && $ip <= 2147483647) return false;
    		if ($ip >= 2851995648 && $ip <= 2852061183) return false;
    		if ($ip >= 2886729728 && $ip <= 2887778303) return false;
    		if ($ip >= 3221225984 && $ip <= 3221226239) return false;
    		if ($ip >= 3232235520 && $ip <= 3232301055) return false;
    		if ($ip >= 4294967040) return false;
    	}
    	return true;
    }

}
