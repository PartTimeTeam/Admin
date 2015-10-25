<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class ProductGroup extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'product_group';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllProductGroup( $data ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        
        $select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		$select = $select->from( $this->_name );
    	}
    	//check count only purpose
    	$select = $select->where( "status <> ?", USER_STATUS_DELETED );
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
    public function fetchProductGroupById( $id ) {
    	$db     = $this->getAdapter();
    	$db->setProfiler('other');
    	$where[] = $db->quoteInto( "product_group_id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveProductGroup( $data ) {
    	$datain = array(
    			'product_name'       => $data['product_name'],
    			'logo_url'           => $data['logo_url'],
    			'product_rating'	 => $data['product_rating'],
    			'status'             => $data['status'],
    			'city'               => $data['city'],
    			'address'            => $data['address'],
    			'from_day'           => $data['from_day'],
    			'to_day'             => $data['to_day'],
    			'from_time'          => $data['from_time'],
    			'to_time'            => $data['to_time'],
    			'duration'           => $data['duration'],
    			'difficulty'         => $data['difficulty'],
    			'local_knowledge'    => $data['local_knowledge'],
    			'created_at'         => $data['created_at'],
    			'product_min'        => $data['product_min'],
    			'product_max'        => $data['product_max'],
    			'member_min'         => $data['member_min'],
    			'member_max'         => $data['member_max'],
    			'gold'               => $data['gold'],
    			'video_url'          => $data['video_url'],
    			'rule_id'            => $data['rule_id'],
    			'product_group_type_id'    => $data['product_group_type_id'],
    			'description'        => $data['description'],
    			'long_description'   => $data['long_description'],
    	);
    	if ( empty( $data['created_at'] ) == true ){
    		$datain['created_at'] = date('Y-m-d H:i:s');
    	} else {
    		
    	}
    	if ( empty( $data["id"] ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "product_group_id = ?", $data["id"], Zend_Db::INT_TYPE );
//     		$where[] = $this->getAdapter()->quoteInto( "status <> ?", USER_STATUS_DELETED );
    		return $this->update( $datain, $where );
    	} else {
    		$id = $this->insert( $datain );
    		return $id;
    	}
    }
    
    /**
     * Delete User
     * @param int $userId
     * @return number
     */
    public function deleteProductGroup( $id )
    {
    	$datain = array(
    			'status'      => USER_STATUS_DELETED
    	);
    	$where = $this->getAdapter()->quoteInto('product_group_id = ?', $id);
    	//         UtilLogs::logHistory( LOG_ACTION_DELETE, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
    	return $this->update( $datain, $where );
    }
}