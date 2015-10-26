<?php
class Site_ProductGroupController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-product-group' ) );
    }

    public function indexAction() {
    	//TODO
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlProductGroup = new ProductGroup();
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlProductGroup->fetchAllProductGroup( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$productGroupList = $mdlProductGroup->fetchAllProductGroup( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $productGroupList;
    	$this->_helper->json( $return );
    	exit;
    }
    /**
     * detail
     */
    public function detailAction(){
    	$productGroup = new ProductGroup();
    	$info = array();
    	$error = array();
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $productGroup->fetchProductGroupById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    		$xml = APPLICATION_PATH.'/xml/product_group.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		//=============upload================== 		
    		$target_dir = BASE_PATH.'/data/upload/';
    		$target_file = $target_dir . basename($_FILES["logo_url"]["name"]);
    		$uploadOk = 1;
    		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    		// Allow certain file formats
    		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
    			$error['extension-image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    			$uploadOk = 0;
    		}
    		// Check if $uploadOk is set to 0 by an error
    		if ($uploadOk == 0) {
    			echo "Sorry, your file was not uploaded.";
    			// if everything is ok, try to upload file
    		} else {
    			if ( $id > 0 && empty( $info['logo_url'] ) == false ){
    				if (file_exists($info['logo_url'])) {
    					unlink($info['logo_url']);
    				}
    			}
    			$this->post_data['logo_url'] = $target_file;
    			if (move_uploaded_file($_FILES["logo_url"]["tmp_name"], $target_file)) {
    				echo "The file ". basename( $_FILES["logo_url"]["name"]). " has been uploaded.";
    			} else {
    				echo "Sorry, there was an error uploading your file.";
    			}
    		}
    				
    		//==============================				
    		if( empty( $error ) == true ) {
    			$result = $productGroup->saveProductGroup( $this->post_data );
    			if ( empty( $result ) == false ){
    				$this->_redirect( "/".$this->controller );
    			}
    		}else {
    			$userInfo = $this->post_data;
    			$error = $error;
    		}
    	}
    	$this->view->info = $info;
    	$this->view->id = $id;
    	$this->view->error = $error;
		
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
    		$productGroup = new ProductGroup();
    		$productGroup->deleteProductGroup( $id );
    		$this->ajaxResponse( CODE_SUCCESS );
    	} else {
    		$this->ajaxResponse( CODE_HAS_ERROR );
    	}
    }
}
