<?php
class Site_DownloadController extends FrontBaseAction {
    public function init() {
    	$this->setNoLoginRequired();
        parent::init();
        $this->_helper->layout()->disableLayout();
        $this->view->headLink()->appendStylesheet( "/resources/css/download_site.css" );
        $this->view->headLink()->appendStylesheet( "/resources/css/_dev_download_site.css" );
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
    		$this->view->fileShare = $check['file_name'];//$data['fileshare'];
    		$this->view->urlShare = $check['url_share_file'];
    	} else {
    		$this->_redirect('/'.$this->controller.'/page-not-found');
    	}
    	
    }
    public function checkCodeInputAction(){
    	$this->isAjax();
    	if( $this->request->isPost() ){
    		$data = $this->post_data;
	    	if( empty($data['Code']) == false ){
	    		//
	    		$referrlFile = new ReferralFile();
	    		$codeInfo = $referrlFile->getFileByCode( $data['Code'], $data['UrlShare'] );
	    		if( empty( $codeInfo ) == false ){
	    			$this->ajaxResponse(CODE_SUCCESS,'');
	    		}
	    		//
	    	} 
    	}
    	$this->ajaxResponse(CODE_HAS_ERROR);
    }
    public function processDownloadAction(){
    	$data = $this->post_data;
    	$codeInfo = array();
    	if( empty($data['Code']) == false && empty($data['UrlShare']) == false ){
    		$code = $data['Code'];
    		$urlShare = $data['UrlShare'];
    		if( strlen($code) > 0 ){
    			$referrlFile = new ReferralFile();
    			$codeInfo = $referrlFile->getFileByCode( $code, $urlShare);
    			if( empty( $codeInfo) == false ){
    				$host = 'http://'.$_SERVER['HTTP_HOST'].'/upload/';
    				if( $codeInfo['file_type'] == IS_IMG ){
    					header("Location:".$host.$codeInfo['physical_name']);
    				} else {
    					$this->view->codeInfo = $codeInfo;
    					$this->view->host = $host;
    				}
    				
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
