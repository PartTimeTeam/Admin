<?php
include 'PHP45.XML.php';

class My_GeoLocation {
    // XML document
    var $doc  = null;
    // string describing the host of the geo location service
    var $host = "http://api.hostip.info/?ip=<IP>";

    // string describing the city
    var $city      = 'unknown';
    // string describing the country
    var $country   = 'unknown';
    // longitude
    var $longitude = '0';
    // latitude
    var $latitude  = '0';

    // ctor
    function My_GeoLocation($ip) {
        $this->doc = new PHP45XML();
        $this->doc->preserveWhiteSpace = false;

        // prepare url of service
        $host  = str_replace( "<IP>", $ip, $this->host);
        $reply = $this->fetch($host);

        // decode the reply and make it available
        $this->decode($reply);
    }

    function fetch($host) {
        $reply = 'error';
        // try curl or fopen
        if( function_exists('curl_init') ) {
            // use curl too fetch site
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL           , $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $reply = curl_exec($ch);
            curl_close ($ch);
        } else {
            // fall back on fopen
            $reply = file_get_contents($host, 'r');
        }
        return $reply;
    }

    function decode($text) {
        // load in php version independent manner
        $this->doc->loadXML($text);
        // use the PHP4/5 XML wrapper to decode the result
        $result = $this->doc->xpath("//gml:name");
        	
        $this->city      = $result['city'];
        $this->country   = $result['country'];
        $this->longitude = $result['lng'];
        $this->latitude  = $result['lat'];
    }
}

?>