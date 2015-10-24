<?php
/**
 * Setup singleton class logging for the system
 * Class Name:  My_Helper_Logging
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 16, 2009
 * @Version V001 Jul 16, 2009 (hoangpm) New Create
 */
class My_Logging
{
    protected static $_instance = null;

    // Enable profiler
    protected $_logger;

    // Log file path
    protected $_logPath;

    // Enable profiler
    protected $_enabled;

    protected function __construct()
    {
        $config = Zend_Registry::get('config');
        $this->_logPath = $config['logging']['log_path'];
        $this->_enabled = $config['logging']['enabled'];

        $writer = new Zend_Log_Writer_Stream( $this->_logPath );
        $logger = new Zend_Log( $writer );
        if ('production' == APPLICATION_PATH) {
            $filter = new Zend_Log_Filter_Priority(Zend_Log::ERR);
            $logger->addFilter($filter);
        }
        $this->_logger = $logger;
    }

    protected function __clone()
    {
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function direct($message, Zend_Log $type = Zend_Log::INFO)
    {
        return $this->write($message, $type);
    }

    public function write($message, Zend_Log $type = Zend_Log::INFO)
    {
        if($this->_enabled == true){
            $this->_logger->log( $message, $type);
        }
    }
}