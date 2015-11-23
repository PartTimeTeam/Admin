<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class Game extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'game_tbl';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllGame( $data = array() ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        
        $select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		$select = $select->from( $this->_name );
    	}
		$select->where('status <>?', STATUS_DELETE );
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
    public function fetchGameById( $id ) {
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
    public function saveGame( $data ) {
    	$datain = array(
    			'name'           => $data['name'],
    			'description'	 			=> $data['description'],
    			'status' 	=> 0,
    			'time_start'           => $data['time_start'],
    			'end_time'           => $data['end_time'],
    			'list_stage'           => $data['list_stage'],
                );
    	if ( empty( $data["id"] ) == false )  {
    		$where[] = $this->getAdapter()->quoteInto( "id = ?", $data["id"], Zend_Db::INT_TYPE );
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
    public function deleteGame( $id )
    {
    	$datain = array(
    			'status'      => STATUS_DELETE
    	);
    	$where = $this->getAdapter()->quoteInto('id = ?', $id);
    	return $this->update( $datain, $where );
    }
}