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
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $mdl->fetchReferralFileById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	
    	if( $this->request->isPost() ) {
    		if ( empty( $this->post_data['code'] ) == true  ){
    			$this->post_data['code'] = $this->generateCode();
    		}
    		// check data
    		$xml = APPLICATION_PATH.'/xml/referral_file.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		if( $id == 0 ){
    			if( empty($_FILES['fileName']['name']) == true ){
    				$error['fileName'] = 'Please input file!';
    			}
    		}
	    	$uploaddir = PUBLIC_PATH.'/upload/';
	    	$data = $this->post_data;
	    	if( empty( $error ) == true ){
	    		if ( empty( $_FILES['fileName']['name'] ) == false ){
	    			$f_type =  $_FILES['fileName']['type'];
	    			if ($f_type== "image/gif" || $f_type== "image/png" || $f_type== "image/jpeg" || $f_type== "image/JPEG"
	    					|| $f_type== "image/PNG" || $f_type== "image/GIF"){
	    				$dataIn['file_type'] = TYPE_IS_IMAGE;
	    			} else {
	    				$dataIn['file_type'] = TYPE_IS_FILE;
	    			}
	    			$dataIn['physical_name'] = $this->_createFileName( $_FILES['fileName']['name'] );
	    			$dataIn['file_name'] = $_FILES['fileName']['name'];
	    		}
	    		$dataIn['code'] = $data['code'];
	    		$uploadfile = '';
				// check Move
				$checkMove = false;
	    		if( empty($_FILES['fileName']['name']) == false ){
	    			$uploadfile = $uploaddir . basename( $dataIn['physical_name'] );
	    			if ( move_uploaded_file($_FILES['fileName']['tmp_name'], $uploadfile) ) {
	    				$checkMove = true;
	    			} else {
	    				$error['upload'] = 'Upload File Fail. Please Try again';
	    			}
	    		}
	    		if( empty($error) == true ){
		    		if( intval($id) > 0 ){
		    			$result =$mdl->insertUpdateReferralFile( $dataIn, $id );
		    			if( $result >= 0 ){
		    				// unlink if exist file
		    				if( empty($_FILES['fileName']['name']) == false ){
		    					// unlink 
		    					if ( file_exists(PUBLIC_PATH.'/upload/'.$info['physical_name']) ) {
		    						unlink(PUBLIC_PATH.'/upload/'.$info['physical_name']);
		    					}
		    					
		    				}
		    				$this->_redirect( '/'.$this->controller );
		    			} else {
		    				$error['update'] = 'Update Fail';
		    			}
		    			
		    		} else {
		    			// insert
		    			$info = $dataIn;
		    			$dataIn['status'] = 0;
		    			$dataIn['url_share_file'] = substr( hash( 'sha1', $data['code'] ), 0, 10 );
		    			$result = $mdl->insertUpdateReferralFile($dataIn);
		    			if( $result <= 0 ){
		    				
		    				$error['insert'] = 'Insert Fail';
		    			} else {
		    				$this->_redirect( '/'.$this->controller );
		    			}
		    		}
	    		
	    		}
	    	} else {
	    		$info = $this->post_data;
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
