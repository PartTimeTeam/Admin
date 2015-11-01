<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class Order extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'order_detail';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllOrderGroup( $data ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        $commonObj = new My_Controller_Action_Helper_Common();
        $select = $this->getAdapter()->select();
        if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
        	$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
        } else {
        	$select = $select->from( $this->_name )->columns( array( 'created_at' => new Zend_Db_Expr( "DATE_FORMAT(created_at,'%Y-%m-%d %H:%i:%s')") ) );
        }
        if( empty( $data["order_name"] ) == false ) {
        	$data["order_name"] = $commonObj->quoteLike( $data["order_name"] );
        	$select = $select->where( "order_name like ?", "%" . $data["order_name"] . "%" );
        }
        if( empty( $data["email"] ) == false ) {
        	$data["email"] = $commonObj->quoteLike( $data["email"] );
        	$select = $select->where( "email like ?", "%" . $data["email"] . "%" );
        }
        if( empty( $data["currency"] ) == false ) {
        	$select = $select->where( "currency = ?",$data["currency"]);
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
    	$where[] = $db->quoteInto( "order_id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    
}