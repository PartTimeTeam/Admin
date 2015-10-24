<?php
/**
 * Setup Db profiler common function for MySql
 * Class Name:  My_Controller_Action_Helper_DbProfiler
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 14, 2009
 * @Version V001 Jul 14, 2009 (hoangpm) New Create
 */
class My_DbProfiler
{
    // Db Adapter object
    protected $_defaultProfiler;

    protected static $_instance = null;

    // Log file path
    protected $_logPath;

    // Enable profiler
    protected $_enabled;

    // Logger for profiler
    protected $_logger;

    const END_LINE = '\r\n';

    protected function __construct()
    {
        $dbAdapters = Zend_Registry::get('dbAdapters');
        $adapter = $dbAdapters['default'];
        $this->_defaultProfiler = $adapter->getProfiler();

        $config = Zend_Registry::get('config');
        $this->_logPath = $config['profiler']['log_path'];
        $this->_enabled = $config['profiler']['enabled'];

        $writer = new Zend_Log_Writer_Stream( $this->_logPath );
        $this->_logger = new Zend_Log( $writer );
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

    public function direct(Zend_Db_Profiler $profiler = null)
    {
        return $this->write($profiler);
    }

    public function write(Zend_Db_Profiler $profiler = null)
    {
        if($this->_enabled == true && APPLICATION_ENV != 'production')
        {
            if($profiler == null)
            {
                $profiler = $this->_defaultProfiler;
            }

            $query = $profiler->getLastQueryProfile();

            if(is_object($query) == true)
            {
                $message = 'QUERY STATEMENT: '.$query->getQuery().self::END_LINE;
                $message.= 'DURATION: '.$query->getElapsedSecs().self::END_LINE;
                $this->_logger->log( $message, Zend_Log::INFO );
            }
        }
    }
}