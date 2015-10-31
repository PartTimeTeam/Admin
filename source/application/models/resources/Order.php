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
        	$select = $select->from( $this->_name );
        }
        $select = $select->joinLeft( 'product_group', 'product_group.product_group_id = order_detail.product_group_id' );
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
        	$data["created_at"] = date('yyyy-mm-dd',strtotime($data['created_at']));
        	$select = $select->where( "order_detail.created_at >= ?", $data["created_at"] );
        	$select = $select->where( "order_detail.created_at <= ?", $data["created_at"] );
        }
    	//check count only purpose
    	if( empty( $data['count_only'] ) == true || $data['count_only'] != 1 ) {
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
//     public function fetchProductGroupById( $id ) {
//     	$db     = $this->getAdapter();
//     	$db->setProfiler('other');
//     	$where[] = $db->quoteInto( "product_group_id = ?", $id, Zend_Db::INT_TYPE );
//     	$result = $this->fetchRow( $where );
//     	if ( empty( $result ) == true ) {
//     		return array();
//     	}
//     	$result = $result->toArray();
//     	return $result;
//     }
//     public function saveProductGroup( $data ) {
//     	$datain = array(
//     			'product_name'       => $data['product_name'],
//     			'logo_url'           => $data['logo_url'],
//     			'product_rating'	 => $data['product_rating'],
//     			'status'             => $data['status'],
//     			'city'               => $data['city'],
//     			'address'            => $data['address'],
//     			'from_day'           => $data['from_day'],
//     			'to_day'             => $data['to_day'],
//     			'from_time'          => $data['from_time'],
//     			'to_time'            => $data['to_time'],
//     			'duration'           => $data['duration'],
//     			'difficulty'         => $data['difficulty'],
//     			'local_knowledge'    => $data['local_knowledge'],
//     			'created_at'         => $data['created_at'],
//     			'product_min'        => $data['product_min'],
//     			'product_max'        => $data['product_max'],
//     			'member_min'         => $data['member_min'],
//     			'member_max'         => $data['member_max'],
//     			'gold'               => $data['gold'],
//     			'video_url'          => $data['video_url'],
//     			'rule_id'            => $data['rule_id'],
//     			'product_group_type_id'    => $data['product_group_type_id'],
//     			'description'        => $data['description'],
//     			'long_description'   => $data['long_description'],
//     	);
//     	if ( empty( $data['created_at'] ) == true ){
//     		$datain['created_at'] = date('Y-m-d H:i:s');
//     	} else {
    		
//     	}
//     	if ( empty( $data["id"] ) == false )  {
//     		$where[] = $this->getAdapter()->quoteInto( "product_group_id = ?", $data["id"], Zend_Db::INT_TYPE );
// //     		$where[] = $this->getAdapter()->quoteInto( "status <> ?", USER_STATUS_DELETED );
//     		return $this->update( $datain, $where );
//     	} else {
//     		$id = $this->insert( $datain );
//     		return $id;
//     	}
//     }
    
//     /**
//      * Delete User
//      * @param int $userId
//      * @return number
//      */
//     public function deleteProductGroup( $id )
//     {
//     	$datain = array(
//     			'status'      => USER_STATUS_DELETED
//     	);
//     	$where = $this->getAdapter()->quoteInto('product_group_id = ?', $id);
//     	//         UtilLogs::logHistory( LOG_ACTION_DELETE, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
//     	return $this->update( $datain, $where );
//     }
}