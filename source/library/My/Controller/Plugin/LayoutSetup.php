<?php
/**
 * Front Controller plug in to setup layout for modules
 * Class Name:  My_Controller_Plugin_LayoutSetup
 * Programmer:  hoangpm(GCS)
 * Create Date:  Jul 8, 2009
 * @Version V001 Jul 8, 2009 (hoangpm) New Create
 */
class My_Controller_Plugin_LayoutSetup extends Zend_Layout_Controller_Plugin_Layout
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout($module);
    }
}