<?php
/**
 * Authentication
 * Class Name:  My_Controller_Action_Helper_Auth
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 20, 2009
 * @Version V001 Jul 20, 2009 (hoangpm) New Create
 */

class My_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{
    //------------------------------------------------------------------------//
    // Zend_Auth object
    //------------------------------------------------------------------------//
    protected $_auth;

    public function __construct( array $data = array() )
    {
        $this->_auth = Zend_Auth::getInstance();
    }
    /**
     * Check user is login or not
     * Function Name:authUser
     * Programmer: hoatt (PlanV)
     * Create Date: May 12, 2009
     * @param   type  var description
     * @throws  Zend_Cache_Exception
     * @return  type  var description
     * @Version V001 May 12, 2009 (hoatt) New Create
     */
    public function hasIdentity()
    {
        return $this->_auth->hasIdentity();
    }
    /**
     * Clear login user infomation
     * Function Name:
     * Programmer: hoatt (PlanV)
     * Create Date: May 12, 2009
     * @param   void
     * @throws  Zend_Cache_Exception
     * @return  type  var description
     * @Version V001 May 12, 2009 (hoatt) New Create
     */
    public function clearIdentity()
    {
        return $this->_auth->clearIdentity();
    }
    /**
     * Get login user infomation
     * Function Name: getIdentity()
     * Programmer: hoatt (PlanV)
     * Create Date: May 12, 2009
     * @param   void
     * @throws  Zend_Cache_Exception
     * @return  type  var description
     * @Version V001 May 12, 2009 (hoatt) New Create
     */
    public function getIdentity()
    {
        return $this->_auth->getIdentity();
    }
}
