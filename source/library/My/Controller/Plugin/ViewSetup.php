<?php
/**
 * Front Controller plug in to set up the view with the Coco view helper
 * path and some useful request variables.
 *
 * Class Name:  My_Controller_Plugin_ViewSetup
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 8, 2009
 * @Version V001 Jul 8, 2009 (hoangpm) New Create
 */
class My_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract {
    /**
     *
     * @var Zend_View
     */
    protected $_view;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'viewRenderer' );
        $viewRenderer->init ();

        $view = $viewRenderer->view;
        $this->_view = $view;

        // set up common variables for the view
        $view->module = strtolower ( $request->getModuleName () );
        $view->controller = strtolower ( $request->getControllerName () );
        $view->action = strtolower ( $request->getActionName () );

        // set up doctype for any view helpers that use it
        $view->doctype ( 'XHTML1_STRICT' );

        // setup initial head place holders
        $view->headMeta()->appendHttpEquiv ( 'Content-Type', 'text/html;charset=utf-8' );
        $view->headMeta()->appendHttpEquiv ( 'Content-Language', 'en-US' );

        $view->headLink()->headLink ( array ('rel' => 'icon', 'href' => '/resources/images/favicon.ico', 'type' => 'image/x-icon' ), 'PREPEND' );
        $view->headLink()->headLink ( array ('rel' => 'apple-touch-icon', 'href' => '/resources/images/favicon.ico' ), 'PREPEND' );
        // Add helper path to View/Helper directory within this library
        $prefix = 'My_View_Helper';
        $dir = BASE_PATH . '/library/My/View/Helper';
        $view->addHelperPath ( $dir, $prefix );
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'viewRenderer' );
        $viewRenderer->init ();

        $view = $viewRenderer->view;
        $this->_view = $view;

        // set up common variables for the view
        $view->module = strtolower ( $request->getModuleName () );
        $view->controller = strtolower ( $request->getControllerName () );
        $view->action = strtolower ( $request->getActionName () );

        // Load Multi Language
        $session = new My_Controller_Action_Helper_Session ();
        $langCode = $session->getSession ( "LANG_CODE" );
        if (empty ( $langCode ) == FALSE) {
            switch ($langCode) {
                case 'vi' :
                    if (defined ( 'LANG_CODE' ) == FALSE) {
                        define ( 'LANG_CODE', $langCode );
                    }
                    break;
                default :
                    if (defined ( 'LANG_CODE' ) == FALSE) {
                        define ( 'LANG_CODE', 'en' );
                    }
            }
        } else {
            if (defined ( 'LANG_CODE' ) == FALSE) {
                define ( 'LANG_CODE', 'en' );
            }
        }
		if($view->controller != 'download') {
	        $translate = UtilTranslator::loadTranslator( 'language' );
	        Zend_Registry::set ( 'language', $translate );
	        $view->headTitle()->setSeparator( ' - ' );
	        $view->headTitle( UtilTranslator::translate( "Xrace" ) );
	        //Auto refresh
	        $auto = new My_View_Helper_AutoRefreshRewriter();
	        
	        $view->headLink()->appendStylesheet( "/resources/css/bootstrap.min.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/bootstrap-formhelpers.min.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/font-awesome.min.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/pnotify.custom.min.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/animate.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/datepicker.css" );
	        $view->headLink()->appendStylesheet( "/resources/css/bootstrap-datetimepicker.min.css" );
	//         if ( DEFAULT_MENU == HORIZONTAL ) {
	//             $view->headLink()->appendStylesheet( "/resources/css/left_menu.css" );
	//         }
	        //$view->headLink()->appendStylesheet( "/libs/ckeditor/samples.css" );
	
	        $view->headLink()->appendStylesheet( "/resources/css/site_pages.css" );
	        $view->headScript()->appendFile( '/libs/joop-1.1.js', 'text/javascript' );
	        $view->headScript()->appendFile( '/libs/jquery.min.js', 'text/javascript' );
	        $view->headScript()->appendFile( '/libs/bootstrap.min.js', 'text/javascript' );
	        $view->headScript()->appendFile( '/libs/pnotify.custom.min.js', 'text/javascript' );
			$view->headScript()->appendFile( '/libs/ckeditor/ckeditor.js', 'text/javascript' );
			$view->headScript()->appendFile( '/libs/select2/select2.js', 'text/javascript' );
		}
//         if ( DEFAULT_MENU == HORIZONTAL ) {
//             $view->headScript()->appendFile( '/libs/menu_custom.js', 'text/javascript' );
//         }
    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

    }

}