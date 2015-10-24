// <?php

// class My_Acl extends Zend_Acl
// {
//     private $_noAuth;
//     private $_noAcl;
//     protected static $_instance = null;

//     public function __construct()
//     {
//         $config = Zend_Registry::get('config');
//         $config = $config['acl'];
//         $roles  = $config['roles'];
//         $this->_addResource();
//         $this->_addRoles($roles);
//         $this->_privilegeRoles();
//         $this->_loadRedirectionActions($config);
//     }

//     protected function __clone()
//     {
//     }

//     public static function getInstance()
//     {
//         if (null === self::$_instance) {
//             self::$_instance = new self();
//         }

//         return self::$_instance;
//     }

//     public function setNoAuthAction($noAuth)
//     {
//         $this->_noAuth = $noAuth;
//     }

//     public function setNoAclAction($noAcl)
//     {
//         $this->_noAcl = $noAcl;
//     }

//     public function getNoAuthAction()
//     {
//         return $this->_noAuth;
//     }

//     public function getNoAclAction()
//     {
//         return $this->_noAcl;
//     }

//     protected function _addRoles($roles)
//     {
//         if(is_array($roles) == true){
//             foreach( $roles as $name => $parents) {
//                 if (!$this->hasRole($name)) {
//                     if (empty($parents)) {
//                         $parents = null;
//                     } else {
//                         $parents = explode(',', $parents);
//                     }
//                     $this->addRole(new Zend_Acl_Role($name), $parents);
//                 }
//             }
//         }
//     }

//     protected function _privilegeRoles()
//     {
//         // Guest may only view content
//         //        $this->allow('guest', 'user', 'list');
//         //        $this->allow('member', 'user');
//         //        $this->allow('admin');
//         //        $this->deny('member', 'user', 'add'); // Remove specific privilege
//         //$this->allow('admin');
//         //$this->deny('admin', 'user', 'list'); // Remove specific privilege
//         //        Zend_Debug::dump($this);
//         //        exit;
//         //$this->allow('admin'); // unrestricted access
//         //$this->deny('guest'); // Remove specific privilege

//         // Add authoring ACL check
//         //$this->allow('member', 'forum', 'update', new MyAcl_Forum_Assertion($auth));
//         // NOTE: Dependency on auth object to allow getIdentity() for authenticated user object
         
//     }

//     protected function _addResource()
//     {
//         $this->add(new Zend_Acl_Resource('index'));
// //         $this->add(new Zend_Acl_Resource('auth'));
// //         $this->add(new Zend_Acl_Resource('index'));
//     }

//     protected function _loadRedirectionActions($aclConfig)
//     {
//         $this->_noAuth = $aclConfig['noAuth'];
//         $this->_noAcl = $aclConfig['noAcl'];
//     }
// }