<?php
class Site_SignUpController extends FrontBaseAction {
    public function init() {
    	if ( $this->getRequest()->getControllerName() == Constains::SIGN_UP ) {
    		$this->setNoLoginRequired();
    	}
        parent::init();
        $this->hasviewPermission();
        $this->loadLanguage( "language_user" );
        $this->loadJs( array( 'pages-login' ) );
    }

    public function indexAction() {
    }
    public function detailAction(){
    }
    public function captChaAction(){
    	$this->isAjax();
   		$session = new My_Controller_Action_Helper_Session();
    	$captchaSession = $session->getSession('CAPTCHA');
    	if(empty($captchaSession) == false ){
    		$session->unsetSession('CAPTCHA');
    		unlink(BASE_PATH.'/public/captcha/'.$captchaSession.'.png');
    	}
    	$captcha = new Zend_Captcha_Image();
    	$captcha->setTimeout('300')
    		->setWordLen('4')
    		->setHeight('80')
    		->setFont(BASE_PATH.'/public/resources/fonts/ARIBLK.TTF')
    		->setImgDir(BASE_PATH.'/public/captcha/');
    	
    	$captcha->generate();    
    	$captchaId =  $captcha->getId();   
    	$session->setSession('CAPTCHA',$captchaId );
    	$this->_helper->json( $captchaId );
    	exit;
    }
}
