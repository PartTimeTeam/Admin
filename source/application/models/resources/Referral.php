<?php
/**
 * Process for user
 * @author Phuong Nguyen
 *
 */
class Referral extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'referral_code_tbl';
    protected $_rowClass 		= 'DbTableRow';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function fetchAllReferral( $data = array() ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        
        $select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		$select = $select->from( $this->_name );
    	}
    	$select = $select->joinLeft( 'question_tbl', 'question_tbl.id = Referral_tbl.id_question', 'name as question_name' );
		$select->where('Referral_tbl.status <>?', STATUS_DELETE );
// 		$select->where('question_tbl.status <>?', STATUS_DELETE );
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
    public function fetchReferralById( $id ) {
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
    public function saveReferral( $data ) {
    	$datain = array(
    			'code'       => $data['name'],
    			'max_user'           => $data['max_user']
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
    public function deleteReferral( $id )
    {
    	$datain = array(
    			'status'      => STATUS_DELETE
    	);
    	$where = $this->getAdapter()->quoteInto('id = ?', $id);
    	//         UtilLogs::logHistory( LOG_ACTION_DELETE, Constants::$controllerUtilMapping[Constants::USER_CTRL], $userId, '', array(), $datain );
    	return $this->update( $datain, $where );
    }
}