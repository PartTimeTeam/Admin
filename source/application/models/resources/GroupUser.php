<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class GroupUser extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'group_users';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllGroupUser( $data ) {
	//print_r($data);exit;
        $db = $this->getAdapter();
        $db->setProfiler('other');
        $commonObj = new My_Controller_Action_Helper_Common();
        $select = $this->getAdapter()->select();
        if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
        	$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
        } else {
        	$select = $select->from( $this->_name )->columns( array( 'created_at' => new Zend_Db_Expr( "DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')") ) );
        }
        if( empty( $data["group_name"] ) == false ) {
        	$data["group_name"] = $commonObj->quoteLike( $data["group_name"] );
        	$select = $select->where( "group_name like ?", "%" . $data["group_name"] . "%" );
        }
        if( empty( $data["email_leader"] ) == false ) {
        	$data["email_leader"] = $commonObj->quoteLike( $data["email_leader"] );
        	$select = $select->where( "email_leader like ?", "%" . $data["email_leader"] . "%" );
        }
		if( empty( $data["user_name_leader"] ) == false ) {
        	$data["user_name_leader"] = $commonObj->quoteLike( $data["user_name_leader"] );
        	$select = $select->where( "user_name_leader like ?", "%" . $data["user_name_leader"] . "%" );
        }
        if( empty( $data["phone"] ) == false ) {
        	$select = $select->where( "phone = ?",$data["phone"]);
        }
        if( empty( $data["created_at"] ) == false ) {
        	$data["created_at"] = date('Y-m-d',strtotime($data['created_at']));
        	$select = $select->where( "created_at >= ?", $data["created_at"].' 00:00:00' );
        	$select = $select->where( "created_at <= ?", $data["created_at"].' 23:59:59' );
        }
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
    public function getInfo($id ){
    	$db     = $this->getAdapter();
    	$db->setProfiler('other');
    	$where[] = $db->quoteInto( "group_id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    
}