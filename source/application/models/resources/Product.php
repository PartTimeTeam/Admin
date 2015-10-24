<?php

class Product extends Zend_Db_Table_Abstract
{
    protected $_name 			= 'product_tbl';
    protected $_rowClass 		= 'DbTableRow';
    protected $_primary			= 'id';
    
    public function insertProduct( $data ){
    	return $this->insert( $data );
    }
    /**
     * get all list product
     * @param unknown $data
     * @return multitype:
     */
    public function fetchAllProducts( $data = array() ) {
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
	/**
	 * Delete User Type
	 * @param int $userTypeId
	 * @return number
	 */
	public function deleteProduct( $id )
	{
		$response = "";
	    $where = $this->getAdapter()->quoteInto( "id = ?", $id, Zend_Db::INT_TYPE );
	    $response = $this->delete( $where );
	    return $response;
	}
}