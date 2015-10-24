<?php
class Site_LoginController extends FrontBaseAction {
    public function init() {
    	if ( $this->getRequest()->getControllerName() == Constains::LOGIN && ($this->getRequest()->getActionName() == "index" || $this->getRequest()->getActionName() == "forgot")) {
    		$this->setNoLoginRequired();
    	}
        parent::init();
//         $this->hasviewPermission();
        $this->loadLanguage( "language_user" );
        $this->loadJs( array( 'pages-login' ) );
    }

    public function indexAction() {
    	if( empty($this->login_info) == false ){
    		$this->_redirect('/');
    	}
    	$info = array();
    	$error = array();
    	$logininfo = array();
    	if($this->request->isPost()){
    		$data = $this->post_data;
    		$xml = APPLICATION_PATH.'/xml/login.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		if( empty( $error ) == true ) {
    			$username = $this->post_data["username"];
    			$password = $this->post_data["password"];
    			if( isset( $this->post_data["remember_me"] ) ) {
    				$isRemember = $this->post_data['remember_me'];
    			}
    			// Encrypt passwod
    			$encryptPassword = UtilEncryption::encryptPassword( $password ,'md5');
    			// Authenticating user login information result
    			// $password must be md5 encrypt
    			$authResult = UtilAuth::authUser( $username, $encryptPassword );
    			if ( $authResult['result']->isValid() ) {
//     				if ( $isRemember == 1 ) {
//     					$seconds = LOGIN_REMEMBER_TIME; // 7 days
//     					setcookie( UtilEncryption::encryptPassword( DOMAIN . "Username" ), UtilEncryption::encrypt( base64_encode( $username ) . '|' . base64_encode( $password ) ), time() + $seconds, '/' );
//     				} else {
//     					setcookie( UtilEncryption::encryptPassword( DOMAIN . "Username" ), '', time() - 3600, '/' );
//     				}
    				$this->_redirect( "/" );
    			} else {
    				$error[] = $this->translate('incorrect-password');
    			}
    		} else {
    			$logininfo = $data;
    		}
    	}
    	$this->view->loginInfo = $logininfo;
    	$this->view->error = $error;
    }
    public function detailAction(){
    }
    /**
     * Logout
     */
    public function logoutAction() {
    	$login_info = $this->login_info;
    	// Clear all session of browser
    	Zend_Session::destroy();
    	// Clear login info
    	$this->login_info = null;
    	//go to login page
    	$this->_redirect( "/" . $this->controller . "/login" );
    }
    /**
     * forgot password
     */
    public function forgotAction(){
    	$newPass = "";
    	$error = array();
    	if ( $this->_request->isPost()){
	    	$email = $this->post_data['email'];
	    	if ( empty( $email ) == false ){
	    		$mdlUser = new Users();
	    		$user = $mdlUser->fetchUserByEmail( $email );
	    		if ( empty( $user ) == true ){
	    			$error[] =  "Your email not exist";
	    		} else {
	    			$newPass = $this->_generateRandomPassword();
	    			$encryptPassword = UtilEncryption::encryptPassword( $newPass ,'md5');
	    			// send mail
	    				//TODO
	    			$data = array();
	    			$data['password'] = $encryptPassword;
	    			$data['email'] = $email;
	    			$result = $mdlUser->updateUserByEmail( $data );
	    			// check result
	    			if ( $result ){
	    				$this->_redirect('/'. $this->controller . '/login');
	    			}
	    		}
	    	}
    	}
    	$this->view->error = $error;
    }
    private function _generateRandomPassword(){
		$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle( $string ), 0, 6);
	}
}
