<?php
/**
 * Controller Action Helper setup common function for session
 * Class Name:  My_Controller_Action_Helper_Mail
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 8, 2009
 * @Version V001 Jul 8, 2009 (hoangpm) New Create
 */
class My_Controller_Action_Helper_Session extends Zend_Controller_Action_Helper_Abstract
{
    // Session object
    protected $_session;

    // Session namespace
    const SESSION_NAMESPACE = 'MY_SESSION';

    public function __construct( )
    {
        $config = Zend_Registry::get('config');
        $config = $config['session'];

        $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $this->_session->setExpirationSeconds(900);
    }

    /**
     * Set data to session with a key
     * Function Name: SetSession
     * Programmer: boihn (GCS)
     * Create Date: Dec 10, 2008
     * @param   $key
     * @param   $data
     * @return  void
     * @Version V001 Dec 10, 2008 (boihn) New Create
     */
    public function setSession($key, $data)
    {
        $session = $this->_session;
        $session->$key = $data;
    }

    public function unsetSession( $key )
    {
        $session = $this->_session;
        $session->__unset( $key );
    }

    /**
     * Get Session
     * Function Name: SetSession
     * Programmer: boihn (GCS)
     * Create Date: Dec 10, 2008
     * @param  string $key Session Key
     * @return  type  var Session data
     * @Version V001 Dec 10, 2008 (boihn) New Create
     */
    public function getSession($key)
    {
        $session = $this->_session;
        return $session->$key;
    }

    /**
     * Clear all Session
     * Function Name: SetSession
     * Programmer: boihn (GCS)
     * Create Date: Dec 10, 2008
     * @param  string $key Session Key
     * @return  type  var Session data
     * @Version V001 Dec 10, 2008 (boihn) New Create
     */
    public function detroySession()
    {
        $session = $this->_session;
        $session->unsetAll();
    }
}