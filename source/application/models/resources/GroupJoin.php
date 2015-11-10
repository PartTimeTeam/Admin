<?php
/**
 * Process for Join
 * @author Phuong Nguyen
 *
 */
class GroupJoin extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'group_join';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllGroupJoin( $data ) {

        $db = $this->getAdapter();
        $db->setProfiler('other');
        $commonObj = new My_Controller_Action_Helper_Common();
        $select = $this->getAdapter()->select();
        if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
        	$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
        } else {
        	$select = $select->from( $this->_name )->columns( array( 'group_join.create_date' => new Zend_Db_Expr( "DATE_FORMAT(group_join.create_date,'%Y-%m-%d %H:%i:%s')") ) );
        }
		$select = $select->joinLeft( 'users', 'users.user_id = group_join.user_id', 'user_name as name' );
		$select = $select->joinLeft( 'group_users', 'group_users.group_id = group_join.group_id', 'group_name as group_name' );
        // if( empty( $data["create_date"] ) == false ) {
        // 	$data["create_date"] = date('Y-m-d',strtotime($data['create_date']));
        // 	$select = $select->where( "create_date >= ?", $data["create_date"].' 00:00:00' );
        // 	$select = $select->where( "create_date <= ?", $data["create_date"].' 23:59:59' );
        // }
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
    	$where[] = $db->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
    	return $result;
    }
    
}