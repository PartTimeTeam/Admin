<?php
class Site_ReferralFileController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-referral-file' ) );
    }

    public function indexAction() {
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlReferralFile = new ReferralFile();
    	//define columns
    	$columns = array();
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlReferralFile->fetchAllReferralFiles( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$list = $mdlReferralFile->fetchAllReferralFiles( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $list;
    	$this->_helper->json( $return );
    	exit;
    }
    public function detailAction(){
    	$mdl = new ReferralFile();
    	$info = array();
    	$error = array();
    	$dataIn = array();
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $mdl->fetchReferralFileById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	
    	if( $this->request->isPost() ) {
    		// check image if edit
    		$this->post_data['fileName'] = @$_FILES['fileName']['name'];
    		
	    	if ( empty( $this->post_data['file_name_hid'] ) == false ){
	    		if ( empty ( $this->post_data['fileName'] ) == true ){
	    			$this->post_data['fileName'] = 	$this->post_data['file_name_hid'];
	    		}
	    	}
    		if ( empty( $this->post_data['code'] ) == true  ){
    			$this->post_data['code'] = $this->generateCode();
    		}
    		// check data
    		$xml = APPLICATION_PATH.'/xml/referral_file.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		// check file type
    		if ( empty( $_FILES['fileName'] ) == false ){
    			$f_type =  $_FILES['fileName']['type'];
    			if ($f_type== "image/gif" || $f_type== "image/png" || $f_type== "image/jpeg" || $f_type== "image/JPEG" 
    					|| $f_type== "image/PNG" || $f_type== "image/GIF"){
	    			$dataIn['file_type'] = TYPE_IS_IMAGE; 
    			} else {
    				$dataIn['file_type'] = TYPE_IS_FILE;
    			}
    		}else {
    			$dataIn['file_type'] = $this->post_data['file_type'];
    		}
	    	$uploaddir = PUBLIC_PATH.'/upload/';
	    	$data = $this->post_data;
	    	if( empty( $data ) == false && empty( $error ) == true ){
	    		$data['file'] = @$_FILES['fileName'];
	    		if( empty( $data['file'] ) == false && empty($this->post_data['fileName']) == false && empty( $data['code'] ) == false ){
	    			$fileName = $this->_createFileName( $this->post_data['fileName'] );
	    			$uploadfile = $uploaddir . basename( $fileName );
	    			
	    			if( empty( $fileName ) == false ) {
	    				if ( $id > 0 && empty( $info['physical_name'] ) == false && $info['file_name'] != $this->post_data['fileName'] ){
	    					if ( file_exists(PUBLIC_PATH.'/upload/'.$info['physical_name']) ) {
	    						unlink(PUBLIC_PATH.'/upload/'.$info['physical_name']);
	    					}
	    				}
	    				if ( empty( $data['file']['name'] ) == true ){
	    					$data['file']['name'] = $this->post_data['file_name_hid'];
	    				}
    					
    					$dataIn['id'] = $id;
    					$dataIn['physical_name'] = $fileName;
    					$dataIn['file_name'] = $data['file']['name'];
    					$dataIn['status'] = 0;
    					$dataIn['code'] = $data['code'];
    					$dataIn['url_share_file'] = substr( hash( 'sha1', $data['code'] ), 0, 10 );
	    				if ( move_uploaded_file($data['file']['tmp_name'], $uploadfile) ) {
	    					$result = $mdl->insertReferralFile( $dataIn );
	    				} else {
	    					$result = $mdl->insertReferralFile( $dataIn );
	    				}
	    				
    					if ( $result >= 0 ){
    						$this->_redirect( '/'.$this->controller );
    					} else {
    						$info = $this->post_data;
    						$error = $error;
    					}
	    			}
	    		}
	    	}
    	}
    	$this->view->error = $error;
    	$this->view->info = $info;
    }
    private function _clean($string) {
    	$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    	return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
    private function _createFileName($fileName){
    	$date = getdate();
    	$now = $date['mday'].$date['mon'].$date['year'].$date['hours'].$date['minutes'].$date['seconds'];
    	$ranString = uniqid(rand(0,100000), true);
    	$uniqString = $this->_clean($ranString);
    	$imageName = $now.'_'.$ranString.'_'.$fileName;
    	return $imageName;
    }
    /**
     * Delete
     */
    public function deleteAction() {
        //check ajax request
        $this->isAjax();
        //get request parameter
        $id = intval( $this->post_data["id"] );
        //check parameter
        if ( $id > 0 ) {
            $ReferralFile = new ReferralFile();
            $ReferralFile->deleteReferralFile( $id );
            $this->ajaxResponse( CODE_SUCCESS );
        } else {
            $this->ajaxResponse( CODE_HAS_ERROR );
        }
    }
    public static function generateCode(){
//     	$string = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$string = uniqid();
    	return substr(str_shuffle( $string ), 1, 7);
    }
}
