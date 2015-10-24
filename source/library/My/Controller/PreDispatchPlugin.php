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
 * Created on  : Jan 3, 2011
 * PreDispatchPlugin.php
 *
 * </pre>
 */
class My_Controller_PreDispatchPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $front      = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();
        $class      = $dispatcher->getControllerClass($request);
        if (!$class) {
            $class = $dispatcher->getDefaultControllerClass($request);
        }
        $r = new ReflectionClass($class);
        $action = $dispatcher->getActionMethod($request);
        if (!$r->hasMethod($action)) {
            $defaultAction  = $dispatcher->getDefaultAction();
            $controllerName = $request->getControllerName();
            $response       = $front->getResponse();
            $response->setRedirect('/' . $controllerName
                    . '/' . $defaultAction);
            $response->sendHeaders();
            exit;
        }
    }
}
?>