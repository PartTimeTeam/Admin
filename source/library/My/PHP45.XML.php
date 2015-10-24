<?php
class PHP45XML {
    var $doc;

    function PHP45XML() {
        # boil down the version information
        if (version_compare(PHP_VERSION,'5','>='))
            define( 'PHP', 5);
        else
            define( 'PHP', 4);
    }

    function loadXML($xml) {
        // switch on the version of the PHP interpreter
        switch( PHP ) {
            case 4:
                // create and load the document in the case of php 4 using DOM XML
                if (!$this->doc = domxml_open_mem($xml))
                    die("Error while parsing the XML document\n");
                break;
            case 5:
                // make DOMDocument and load the data
                $this->doc = new DOMDocument();
                $this->doc->loadXML($xml);
                break;
        }
    }

    // This function uses XPath to parse the result XML file of the hostip.info site
    function xpath($query) {
        $result = array();
        // switch on the version of the PHP interpreter
        switch( PHP ) {
            case 4:
                $xpath = $this->doc->xpath_new_context();
                // doesn't work, $namespace = $xpath->xpath_eval('namespace-uri(//*)'); // returns the namespace uri
                // we need to use the gml namespace.
                // TODO: get this out of the document proper and remove magic constant
                xpath_register_ns($xpath, "gml", 'http://www.opengis.net/gml' );
                $obj = xpath_eval_expression($xpath, "//gml:name"); // finds all gml:name tags
                // start of frustrating XML experience
                $firstnode = $obj->nodeset[1];
                $children = $firstnode->children();
                $value = $children[0]->content;
                $result['city'] = $value;
                	
                $ch = $firstnode->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->children();
                $result['country'] = $ch[0]->content;

                $ch = $firstnode->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->children();
                $result['code'] = $ch[0]->content;

                $ch = $firstnode->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->next_sibling();
                $ch = $ch->children();

                $ch = $ch[1]->children();
                $ch = $ch[1]->children();
                $ch = $ch[1]->children();
                $lnglat        = split( ',', $ch[0]->content);
                $result['lng'] = $lnglat[0];
                $result['lat'] = $lnglat[1];
                // end of frustrating XML experience
                break;
            case 5:
                // use XPath to break apart the result into the interesting elements
                $xpath = new DOMXPath($this->doc);
                $entries = $xpath->query($query); # query = "every gml:name element anywhere in the document"

                $i = 1;
                foreach ($entries as $entry) {
                    // first two gml:name entries are bogus
                    if( $i++ < 2)
                        continue;

                    // get the values and save them in the instance
                    $result['city']    = $entry->nodeValue;
                    $result['country'] = $entry->nextSibling->nextSibling->nodeValue;
                    $result['code']    = $entry->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;

                    $entries = $xpath->query('//gml:coordinates'); # query = "every gml:name element anywhere in the document"
                    foreach ($entries as $entry) {
                        $lnglat        = split( ',', $entry->nodeValue);
                        $result['lng'] = $lnglat[0];
                        $result['lat'] = $lnglat[1];
                        break;
                    }
                }
                break;
        }
        return $result;
    }
}

?>