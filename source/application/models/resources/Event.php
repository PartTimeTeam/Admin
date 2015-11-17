<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class Event extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'event';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllEvent( $data ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        
        $select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		$select = $select->from( $this->_name );
    	}
		$select->where('status <>?', USER_STATUS_DELETED);
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
    public function fetchEventById( $id ) {
    	$db     = $this->getAdapter();
    	$db->setProfiler('other');
    	$where[] = $db->quoteInto( "event_id = ?", $id, Zend_Db::INT_TYPE );
    	$result = $this->fetchRow( $where );
    	if ( empty( $result ) == true ) {
    		return array();
    	}
    	$result = $result->toArray();
    	return $result;
    }
    public function saveEvent( $data ) {
    	$datain = array(
    			'event_name'       => $data['event_name'],
    			'logo_url'           => $data['logo_url'],
    			'date'	 => $data['date'],
    			'status'             => 0,
    			'team_number'               => $data['team_number'],
    			'team_member'            => $data['team_member'],
    			'price'           => $data['price'],
    			'round'             => $data['round'],
    			'description'        => $data['description'],
                );
    	if ( empty( $data["id"] ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "event_id = ?", $data["id"], Zend_Db::INT_TYPE );
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
    public function deleteEvent( $id )
    {
    	$datain = array(
    			'status'      => USER_STATUS_DELETED
    	);
    	$where = $this->getAdapter()->quoteInto('event_id = ?', $id);
    	//         UtilLogs::logHistory( LOG_ACTION_DELETE, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
    	return $this->update( $datain, $where );
    }
}