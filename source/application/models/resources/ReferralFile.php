<?php

class ReferralFile extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'referral_file_tbl';
    protected $_rowClass 		= 'DbTableRow';
    protected $_primary			= 'id';
    public function init()
    {
    	$dbAdapters = Zend_Registry::get('dbAdapters');
    	$dbAdapter  = $dbAdapters['other'];
    	$this->_setAdapter($dbAdapter);
    }
    public function insertReferralFile( $data ){
    	return $this->insert( $data );
    }
    /**
     * get all list ReferralFile
     * @param unknown $data
     * @return multitype:
     */
    public function fetchAllReferralFiles( $data = array() ) {
    	$db = $this->getAdapter();
        $db->setProfiler('other');
        
    	$select = $this->getAdapter()->select();
    	if( isset( $data['count_only'] ) == true && $data['count_only'] == 1 ) {
    		$select = $select->from( $this->_name, array( "cnt" => new Zend_Db_Expr("COUNT(1)") ) );
    	} else {
    		$select = $select->from( $this->_name );
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
    public function checkUrlParam( $param ){
    	$db = $this->getAdapter();
    	$db->setProfiler('other');
    	$select = $this->getAdapter()->select();
    	$result = array();
    	if( empty($param) == false ){
	    	$where[]  = $db->quoteInto( "url_share_file = ?", $param );
	    	$result = $this->fetchRow( $where );
	    	$result = $result->toArray();
    	}
    	return $result;
    }
    public function getFileByCode( $code ){
    	$db = $this->getAdapter();
    	$db->setProfiler('other');
    	$select = $this->getAdapter()->select();
    	$result = array();
    	if( empty($code) == false ){
    		$where[]  = $db->quoteInto( "code = ?", $code );
    		$result = $this->fetchRow( $where );
    		$result = $result->toArray();
    	}
    	return $result;
    }
    public function fetchReferralFileById( $id ) {
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
	/**
	 * Delete User Type
	 * @param int $userTypeId
	 * @return number
	 */
    public function deleteReferralFile( $id )
    {
    	$datain = array(
    			'status'      => STATUS_DELETE
    	);
    	$where = $this->getAdapter()->quoteInto('id = ?', $id);
    	return $this->update( $datain, $where );
    }
}