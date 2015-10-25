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
    public function fetchAllProductGroup(  ) {
        $db = $this->getAdapter();
        $db->setProfiler('other');
        $result = $this->fetchAll();
        if ( empty( $result ) == true ) {
            return array();
        }
        $result = $result->toArray();
        return $result;
    }
}