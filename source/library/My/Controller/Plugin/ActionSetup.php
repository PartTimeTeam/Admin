<?php
/**
 * Front Controller plug in to set up the action stack.
 * Class Name:  My_Controller_Plugin_ActionSetup
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 8, 2009
 * @Version V001 Jul 8, 2009 (hoangpm) New Create
 */
class My_Controller_Plugin_ActionSetup extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $have_lang = 0;
        //promo_code/LM965
        $session = new My_Controller_Action_Helper_Session();
        if( $request->getParam('lang') === 'vi' || $request->getActionName() === 'vi') {
            $session->setSession('LANG_CODE', 'vi');
            $have_lang = 1;
        } else if ($request->getParam('lang') === 'en' || $request->getActionName() === 'en') {
            $session->setSession('LANG_CODE', 'en');
            $have_lang = 1;
        }

        if($have_lang == 0) {
            $error_url = $request->getRequestUri();
            $params = explode("/", $error_url);
            for($i=0; $i<count($params); $i+=1) {
                if($params[$i] === 'lang') {
                    if((isset($params[$i+1])) && ($params[$i+1] === 'en' || $params[$i+1] === 'es')) {
                        $session->setSession('LANG_CODE', $params[$i+1]);
                        $have_lang = 1;
                    }
                    else {
                        $session->setSession('LANG_CODE', 'en');
                        $have_lang = 1;
                    }
                    break;
                }
            }
        }

        //end check
        //check timeout
        if ( $request->isXmlHttpRequest()
                && $request->getControllerName() == 'index'
                && $request->getActionName() == 'check-time-out') {
            $time_loging = $session->getSession("TIME_LOGING");
            if(empty($time_loging) == FALSE){
                $time = time();
                $time_out = 1800;
                $time_new = $time - $time_out;
                if($time_new > $time_loging){
                    die('1');
                }
            }
            exit();
        }
         
         
        if ((!$request->isXmlHttpRequest()) &&
                ($request->getParam('format') != 'json') &&
                ($request->getParam('format') != 'text')) {
            $front = Zend_Controller_Front::getInstance();
            if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
                $actionStack = new Zend_Controller_Plugin_ActionStack();
                $front->registerPlugin($actionStack, 97);
            } else {
                $actionStack = $front->getPlugin('Zend_Controller_Plugin_ActionStack');
            }
        }
    }
}