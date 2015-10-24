<?php
/**
 * Acl resources
 * @author Phuong Nguyen
 *
 */
class Model_Acl_Resources extends Model_Abstract {
    protected $_name = 'acl_resources';
    protected $_primary = 'resource_id';

    /**
     * Filter and convert resource name
     * @param string $string
     * @param string $removeString
     * @return mixed
     */
    public function filterResourceName( $string, $removeString ) {
        //remove module name in controller string
        if ( strpos( $string, "_" ) ) {
            $arrString = explode( "_", $string );
            $string = $arrString[1];
        }
        //make first character to lower if this is controller
        if ($removeString == 'Controller') {
            $string = strtolower( substr( $string, 0, 1 ) ) . substr( $string, 1 );
        }
        //remove string
        $string = str_replace( $removeString, '', $string );
        preg_match_all( "/[A-Z]/", $string, $matches );
        foreach ( $matches[0] as $letter ) {
            $string = str_replace( $letter, '-' . strtolower( $letter ), $string );
        }
        return $string;
    }

    /**
     * Delete resources was deleted
     * @param int $resources
     */
    public function deleteResourcesIsNotExist( $resources ) {
        if ( empty( $resources ) == false && is_array( $resources ) ) {
            $where = array();
            foreach ( $resources as $controllerName => $actions ) {
                if ( empty( $actions ) == false && is_array( $actions ) ) {
                    foreach ( $actions as $action ) {
                        $where[] = "'" . $controllerName . ':' . $action . "'";
                    }
                }
            }
            $where = implode(", ", $where);
            $this->delete("CONCAT(controller_name, ':', action_name) NOT IN ({$where})");
        }
    }

    /**
     * Get list of resources
     * @return multitype:
     */
    public function getListOfResources() {
        $result = $this->fetchAll();
        $resources = array();
        if ( empty( $result ) == false ) {
            foreach ( $result as $item ) {
                $resource = $item->module_name . ':' . $item->controller_name;
                $resources[$resource][] = $item;
            }
        }
        return $resources;
    }
    
    /**
     * Get a resource by controller name and action name
     * @param string $controllerName
     * @param string $actionName
     * @return multitype:|unknown
     */
    public function fetchResourceByControllerAndAction( $controllerName, $actionName ) {
        $db     = $this->getAdapter();
        $where[]  = $db->quoteInto( "controller_name = ?", $controllerName );
        $where[]  = $db->quoteInto( "action_name = ?", $actionName );
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
    
    /**
     * Get a resource by controller name
     * @param string $controllerName
     * @return multitype:|unknown
     */
    public function fetchResourceByController( $controllerName ) {
        $db     = $this->getAdapter();
        $where[]  = $db->quoteInto( "controller_name = ?", $controllerName );
        $result = $this->fetchAll( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
}

