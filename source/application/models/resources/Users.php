<?php
/**
 * Process for user
 * @author TuanAnh
 *
 */
class Users extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'user_tbl';
    protected $_rowClass 		= 'DbTableRow';
    
    /**
     * Get user by username
     * @param string $username
     * @param int $userId
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function fetchUserByUserName( $username, $userId = null )
    {
        $db     = $this->getAdapter();
        $where[]  = $db->quoteInto( "user_name = ?", $username );
        $where[]  = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
        if ( empty( $userId ) == false ) {
            $where[]  = $db->quoteInto( "id != ?", $userId, Zend_Db::INT_TYPE );
        }
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }

    /**
     * Get user by email
     * @param string $email
     * @param int $userId
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function fetchUserByEmail( $email, $userId = null )
    {
        $db     = $this->getAdapter();
        $where[]  = $db->quoteInto( "email = ?", $email );
        $where[]  = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
        if ( empty( $userId ) == false ) {
            $where[]  = $db->quoteInto( "id != ?", $userId, Zend_Db::INT_TYPE );
        }
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
    
    /**
     * Update user info by user id
     * @param int $userId
     * @param array $data
     * @return Ambigous <mixed, multitype:>|boolean
     */
    public function updateUserById( $userId, $data ) {
        $db     = $this->getAdapter();
        $where[] = $db->quoteInto( "user_id = ?", $userId, Zend_Db::INT_TYPE );
        $where[] = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
        return $this->update( $data, $where );
    }
    
    /**
     * Get user by user id and password
     * @param int $userId
     * @param string $password
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function fetchUserByIdAndPassword( $userId, $password ) {
        $db = $this->getAdapter();
        $where[] = $db->quoteInto( "id = ?", $userId, Zend_Db::INT_TYPE );
        $where[] = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
        $where[] = $db->quoteInto( "password = ?", $password);
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }

    /**
     * Get all users
     * @param array $data
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
    public function fetchAllUsers( $data = array() ) {
        $select = $this->getAdapter()->select();
        if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
            $select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
        } else {
            $select = $select->from( $this->_name )
                             ->columns( array( 'full_name' => new Zend_Db_Expr( "full_name") ) );
        }

//         $select = $select->joinLeft( 'role_assign_tbl', 'role_assign_tbl.role_id = user_tbl.role_id', 'name as role_name' );
        $commonObj = new My_Controller_Action_Helper_Common();
        //search by name
        if( empty( $data["full_name"] ) == false ) {
            $data["full_name"] = $commonObj->quoteLike( $data["full_name"] );
            $select = $select->where( "full_name like ?", "%" . $data["full_name"] . "%" );
        }
        //search by username
        if( empty( $data["user_name"] ) == false ) {
            $data["user_name"] = $commonObj->quoteLike( $data["user_name"] );
            $select = $select->where( "user_name like ?", "%" . $data["user_name"] . "%" );
        }
        //search by email
        if( empty( $data["email"] ) == false ) {
            $data["email"] = $commonObj->quoteLike( $data["email"] );
            $select = $select->where( "email like ?", "%" . $data["email"] . "%" );
        }
        $select = $select->where( "status <> ?", USER_STATUS_DELETED );
        //check count only purpose
        if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
            if ( empty( $data["order"] ) == false ) {
                $order = $data["order"]["column"] . " " . $data["order"]["dir"];
                $select = $select->order( $order );
            }
            $start = ( empty( $data['start'] ) == false ) ? $data['start'] : 0;
            $length = ( empty( $data['length'] ) == false ) ? $data['length'] : 0;
            $select = $select->limit( $length, $start );
        }
        $result = $this->getAdapter()->fetchAll( $select );
        if( empty( $data['count_only'] ) == false && $data['count_only'] == 1 ) {
            return $result[0]['cnt'];
        }
        return $result;
    }
    /**
     * Get user info by user id
     * @param int $id
     * @return multitype:|unknown
     */
    public function fetchUserById( $id ) {
        $db     = $this->getAdapter();
        $where[] = $db->quoteInto( "user_id = ?", $id, Zend_Db::INT_TYPE );
        $where[] = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
        $result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
    
    /**
     * Update/Add user
     * @param array $data
     * @return boolean
     */
    public function saveUser( $data ) {
        $datain = array(
                'full_name'       => $data['full_name'],
                'user_name'       => $data['user_name'],
                'email'           => $data['email'],
                'role_id'         => $data['role_id'],
                'timeout'         => $data['timeout'],
                'disable_cache'   => $data['disable_cache']
        );
        if ( empty( $data['password'] ) == false ) {
            $datain['password'] = UtilEncryption::encryptPassword( $data['password'] );
        }
        if ( isset( $data['status'] ) == true ) {
            $datain['status'] = $data['status'];
        }
        if ( empty( $data["id"] ) == false )  {
            $datain['date_updated'] = new Zend_Db_Expr("NOW()");
            $where[] = $this->getAdapter()->quoteInto( "id = ?", $data["id"], Zend_Db::INT_TYPE );
            $where[] = $this->getAdapter()->quoteInto( "status <> ?", USER_STATUS_DELETED );
            UtilLogs::logHistory( LOG_ACTION_EDIT, Constants::$controllerUtilMapping[Constants::USER_CTRL], $data["id"], '', array(), $datain );
            return $this->update( $datain, $where );
        } else {
            $datain['date_created']  = new Zend_Db_Expr("NOW()");
            $userId = $this->insert( $datain );
            UtilLogs::logHistory( LOG_ACTION_ADD, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
            return $userId;
        }
    }
    
    /**
     * Delete User
     * @param int $userId
     * @return number
     */
    public function deleteUser( $userId )
    {
        $datain = array(
                'status'      => USER_STATUS_DELETED
        );
        $datain['date_updated'] = new Zend_Db_Expr("NOW()");
        $where = $this->getAdapter()->quoteInto('id = ?', $userId);
        UtilLogs::logHistory( LOG_ACTION_DELETE, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
        return $this->update( $datain, $where );
    }
    public function updateUserByEmail( $data, $userId = null ) {
    	$db     = $this->getAdapter();
    	$where[] = $db->quoteInto( "email = ?", $data['email'] );
    	$where[] = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
//     	UtilLogs::logHistory( LOG_ACTION_EDIT, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $data );
    	return $this->update( $data, $where );
    }
    /**
     * 
     */
    public function fetchUserByPass( $password )
    {
    	$db     = $this->getAdapter();
    	$where[]  = $db->quoteInto( "password = ?", $password );
    	$where[]  = $db->quoteInto( "status <> ?", USER_STATUS_DELETED );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
}