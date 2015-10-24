<?php
/**
 * <pre>
 * <b>LunexTelecom Group</b>
 *
 * @PROJECT   	 : LunexTelecom Portal
 * @Author         : MinhVo@LunexTelecom.com
 * @version         : 1.0
 * @COPYRIGHT    : 2011
 * ------------------------------------------------------
 *
 * Created on  : Jan 26, 2011
 * MnvService.php
 *
 * </pre>
 */

require_once 'OpenInviter/openinviter.php';

/**
 * This class is the connection between OpenInviter service and Zend Framework.
 * The class is implemented only for extracting contacts.
 *
 * @tutorial
 *
 *         $inviter = new OpenInviter_Service();
 *
 *       p($inviter->getPlugins());// get all services
 *      p($inviter->getPlugins('email'));// get all services
 *       p($inviter->getPlugins('email', 'gmail'));// get gmail plugin properties
 *       p($inviter->getPlugins('email', 'gmail', 'version'));// get gmail plugin version
 *
 *       // get contacts
 *       p($inviter->getContacts('me@example.com', 'mypass', 'example'));
 *
 *
 * @author stoil
 * @link http://openinviter.com/
 * @uses OpenInviter 1.7.6
 *
 */

class My_MnvService {

    const PATH_PLUGINS = '\\library\\My\\OpenInviter\\plugins\\';
    protected $_messages = array();
    protected $_plugins;
    protected $_openInviter;

    /*~~~~~~~~~~ private methods ~~~~~~~~~~*/
    private function _loadPlugins()
    {
        if($this->_plugins === null) {
            $this->_plugins = $this->getOpenInviter()->getPlugins(false);
        }
    }

    /*~~~~~~~~~~ protected methods ~~~~~~~~~~*/

    protected function _addMessage($code, $message, $type = 'error')
    {
        $this->_messages[$type][$code] = $message;
    }

    protected function _initAutoload()
    {
        set_include_path(
        dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . self::PATH_PLUGINS.
        PATH_SEPARATOR.
        get_include_path()
        );
    }

    /*~~~~~~~~~~ constructor ~~~~~~~~~~*/

    public function __construct()
    {
        $this->_initAutoload();
        $this->_openInviter = new openinviter();
        $this->_loadPlugins();
    }

    /*~~~~~~~~~~ public methods ~~~~~~~~~~*/

    /**
     * Update plugins
     */
    public function updatePlugins()
    {
        $this->_plugins = $this->getOpenInviter()->getPlugins(true);
    }

    /**
     * Get plugin(s), provider(s) or provider details
     * @param $type
     * @param $provider
     * @param $detail
     * @return unknown_type
     */
    public function getPlugins($type = null, $provider = null, $detail = null)
    {
        if ($type !== null) {
            if ($provider !== null) {
                if ($detail !== null) {
                    return $this->_plugins[$type][$provider][$detail];
                } else {
                    return $this->_plugins[$type][$provider];
                }
            } else {
                return $this->_plugins[$type];
            }
        } else {
            return $this->_plugins;
        }
    }

    /**
     * @return openinviter
     */
    protected function getOpenInviter()
    {
        return $this->_openInviter;
    }

    /**
     * Get system messages
     * @param string $type
     * @return array
     */
    public function getMessages($type = null)
    {
        if($type !== null) {
            return $this->_messages[$type];
        } else {
            return $this->_messages;
        }
    }

    /**
     * Get email clients
     * @param string $email
     * @param string $password
     * @param string $provider
     * @return array
     */
    public function getContacts($email, $password, $provider) {
        $contacts = array();
        $this->getOpenInviter()->startPlugin($provider);
        $internalError = $this->getOpenInviter()->getInternalError();
        if ($internalError) {
            $this->_addMessage('inviter', $internalError);
        } elseif (! $this->getOpenInviter()->login($email, $password)) {
            $internalError = $this->getOpenInviter()->getInternalError();
            $this->_addMessage('login',($internalError ? $internalError : 'Login failed. Please check the email and password you have provided and try again later !'));
        } elseif (false === $contacts = $this->getOpenInviter()->getMyContacts()) {
            $this->_addMessage('contacts', 'Unable to get contacts');
        }
        $this->getOpenInviter()->logout();
        return $contacts;
    }
}