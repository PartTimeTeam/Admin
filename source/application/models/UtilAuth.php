<?php
/**
 * Priviledge utilities
 * @author Phuong Nguyen
 *
 */
class UtilAuth {
    
    protected static $login_info = null;
    protected static $instance = null;
    protected static $all_permission = array();
    protected static $controller_permission = array();
    
    /**
     * Authenticate user
     * @param string $username
     * @param sha1_string $password
     * @return multitype:Ambigous <stdClass, boolean, unknown> Zend_Auth_Result
     */
    public static function authUser( $username, $password ) {
        $retData = array();
        $authAdapter = new Zend_Auth_Adapter_DbTable( Zend_Db_Table::getDefaultAdapter() );
        $authAdapter->setTableName( 'user_tbl' );
        $authAdapter->setIdentityColumn( 'user_name' );
        $authAdapter->setCredentialColumn( 'password' );
        $authAdapter->setCredentialTreatment( '?' );
        $authAdapter->setIdentity( $username );
        $authAdapter->setCredential( ( $password ) );
        $auth = self::getAuthInstance();
        $select = $authAdapter->getDbSelect();
        $select->where( "status <> " . USER_STATUS_DELETED );
        $retData['result'] = $auth->authenticate( $authAdapter );
        $retData['data_auth'] = $authAdapter->getResultRowObject( null, 'password' );
        $auth->getStorage()->write( $retData['data_auth'] );
        return $retData;
    }
    
    /**
     * Check user type has privilege on action of a controller
     * @param string $controllerName
     * @param string $actionName
     * @return boolean
     */
    public static function hasPrivilege( $controllerName, $actionName ) {
        $result = false;
        $loginInfo = self::getLoginInfo();
        if ( empty( $loginInfo ) == false && $loginInfo->is_root == USER_GROUP_ROOT ) {
            $result = true;
        } else {
            if ( empty( $loginInfo ) == false ) {
                $roleId = $loginInfo->role_id;
                $role = new Role();
                $roleInfo = $role->fetchRoleById( $roleId );
                if ( empty( $roleInfo ) == false && $roleInfo["is_root"] == USER_GROUP_ROOT ) {
                    $result = true;
                } else {
                    $arrayResources = self::getAllPermission();
                    if ( empty( $arrayResources ) == false ) {
                        $controllerPermission = self::getPermissionByController( $controllerName );
                        if ( empty( $controllerPermission ) == false && empty( $controllerPermission[$actionName] ) == false ) {
                            $resourceId = $controllerPermission[$actionName];
                            if ( in_array( $resourceId, $arrayResources ) ) {
                                $result = true;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Get login info
     */
    public static function getPermissionByController( $controllerName ) {
        if ( empty( self::$controller_permission[$controllerName] ) == true ) {
            $permission = array();
            $aclResource = new Model_Acl_Resources();
            $resourcesList = $aclResource->fetchResourceByController( $controllerName );
            if ( empty( $resourcesList ) == false && is_array( $resourcesList ) ) {
                foreach ( $resourcesList as $key => $resource ) {
                    $permission[$resource["action_name"]] = $resource["resource_id"];
                }
            }
            self::$controller_permission[$controllerName] = $permission;
        }
        return self::$controller_permission[$controllerName];
    }
    
    /**
     * Get all permission of this login info
     */
    public static function getAllPermission() {
        if ( self::$all_permission == null ) {
            $permission = array();
            $loginInfo = self::getLoginInfo();
            if ( empty( $loginInfo ) == false ) {
                $roleId = $loginInfo->role_id;
                $role = new Role();
                $roleInfo = $role->fetchRoleById( $roleId );
                if ( empty( $roleInfo ) == false ) {
                    $resources = $roleInfo["resources"];
                    $permission = explode( ",", $resources );
                }
            }
            self::$all_permission = $permission;
        }
        return self::$all_permission;
    }
    
    /**
     * Get login info
     */
    public static function getLoginInfo() {
        if ( self::$login_info == null ) {
            $auth = self::getAuthInstance();
            self::$login_info = $auth->getIdentity();
        }
        return self::$login_info;
    }
    
    /**
     * Get namespace
     */
    public static function getAuthInstance() {
        if ( self::$instance == null ) {
            $auth = Zend_Auth::getInstance();
            $namespace = Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
            $auth->setStorage( new Zend_Auth_Storage_Session( $namespace, SITE_FRONT ) );
            self::$instance = $auth;
        }
        return self::$instance;
    }
}
