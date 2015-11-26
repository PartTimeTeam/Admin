<?php
class Site_DownloadController extends FrontBaseAction {
    public function init() {
    	$this->setNoLoginRequired();
        parent::init();
        $this->_helper->layout()->disableLayout();
        $this->view->headLink()->appendStylesheet( "/resources/css/download_site.css" );
        $this->view->headScript()->appendFile( '/scripts/client/head.js', 'text/javascript' );
        $this->view->headScript()->appendFile( '/libs/jquery.min.js', 'text/javascript' );
        $this->view->headScript()->appendFile( '/scripts/site/pages-download.js', 'text/javascript' );
    }
    public function indexAction() {
    	$data = $this->post_data;
    	if( empty($data['fileshare']) == false ){
    		$referrlFile = new ReferralFile();
    		$check = $referrlFile->checkUrlParam($data['fileshare']);
    		if( empty($check) == true ){
    			$this->_redirect('/'.$this->controller.'/page-not-found');
    		}
    		$this->view->fileShare = $data['fileshare'];
    	} else {
    		$this->_redirect('/'.$this->controller.'/page-not-found');
    	}
    }
    public function checkCodeInPutAction(){
    	$this->isAjax();
    	
    	if( empty($data['Code']) == false ){
    		
    	} else {
    		$this->ajaxResponse(CODE_HAS_ERROR,'Please Iput Code');
    	}
    }
    public function processDownloadAction(){
    	$data = $this->post_data;
    	$codeInfo = array();
    	if( empty($data['Code']) == false ){
    		$code = $data['Code'];
    		if( strlen($code) > 0 ){
    			$referrlFile = new ReferralFile();
    			$codeInfo = $referrlFile->getFileByCode( $code );
    			if( empty( $codeInfo) == false ){
    				$this->view->codeInfo = $codeInfo;
    				$this->view->host = 'http://'.$_SERVER['HTTP_HOST'].'/upload/';
    			} else {
    				$this->_redirect('/'.$this->controller.'/page-not-found');
    			}
    		} else {
    			$this->_redirect('/'.$this->controller.'/page-not-found');
    		}
    	} else {
    		$this->_redirect('/'.$this->controller.'/page-not-found');
    	}
    }
    public function pageNotFoundAction(){
    	
    }
}
