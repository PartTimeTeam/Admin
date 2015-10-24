<?php
class My_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
         
        if(empty($module) == false && $module == "admin"){
             
            // set up acl
            $acl = new Zend_Acl();
             
            $mdlRole = new Role();
             
            // add the resources
            $acl->addResource(new Zend_Acl_Resource('index'));
            $acl->addResource(new Zend_Acl_Resource('error'));
            $acl->addResource(new Zend_Acl_Resource('auth'));
             
            //set up the access rules
            $acl->allow(null, array('index', 'error', 'auth'));
             
            $resources = $mdlRole->getAllResourcesCtrl();
            foreach($resources as $resource){
                $acl->addResource(new Zend_Acl_Resource($resource));
            }
             
            // add the roles
            $roles = $mdlRole->fetchRoles();
            foreach($roles as $role){
                $role_name = strtolower($role['name']);
                $acl->addRole(new Zend_Acl_Role($role_name));

                //setup privilege
                $role_resources = explode(",",$role['resources']);
                foreach($role_resources as $res_num)
                {
                    $ctrl_names = $mdlRole->listCtrlNameFromResourc($res_num);
                    foreach ($ctrl_names as $ctrl_name)
                    {
                        $acl->allow($role_name, $ctrl_name);
                    }
                }
            }
             
            // administrators can do anything
            $acl->allow('administrator', null);
             
            $namespace = Zend_Auth_Storage_Session::NAMESPACE_DEFAULT;
            $auth  = Zend_Auth::getInstance();
            $auth->setStorage(new Zend_Auth_Storage_Session($namespace, 'admin'));
            if( $auth->hasIdentity())
            {
                $identity = $auth->getIdentity();
                $role_id = strtolower($identity->user_type);
                $role = strtolower($mdlRole->getRoleName($role_id));
                if(empty($role))
                {
                    $request->setControllerName('index');
                    $request->setActionName('index');
                }
                else{
                    $controller = $request->controller;

                    //		            if($acl->has($controller) == false){
                    //		            	$request->setControllerName('auth');
                    //						$request->setActionName('logout');
                    //		            }

                    if (!$acl->isAllowed($role, $controller)) {
                        $request->setControllerName('auth');
                        $request->setActionName('logout');
                    }
            }
        }
        	
    }
     
}
}
