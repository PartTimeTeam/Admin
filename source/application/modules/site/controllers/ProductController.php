<?php
class Site_ProductController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-product' ) );
    }

    public function indexAction() {
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlProduct = new Product();
    	//define columns
    	$columns = array( // danh sach cac cot de ordering
    			0 => "user_id",
    			1 => "user_name",
    	);
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlProduct->fetchAllProducts( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$userList = $mdlProduct->fetchAllProducts( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $userList;
    	$this->_helper->json( $return );
    	exit;
    }
    public function uploadAction(){
    	$count = 0;
    	$data = array();
    	$date = array();
    	if ( $this->_request->isPost() ) {
    		$uploaddir = BASE_PATH.'/data/upload/';
    		foreach ( $_FILES as $nameInput => $value ){
    			if($_FILES["product"]["error"] != 0) {
    				echo '<div style="margin: -15px 5px;" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Please choose file upload</div>';
    				return;
    			}
    			$date = getdate();
    			$date = $date['mday'].$date['mon'].$date['year'].$date['hours'].$date['minutes'].$date['seconds'];
    			$fileName = $_FILES[$nameInput]['name'];
    			$fileName = $date.$fileName;
    			$uploadfile = $uploaddir . basename( $fileName );
    			
    			$allowed =  array('gif','png' ,'jpg');
    			$ext = pathinfo($fileName, PATHINFO_EXTENSION);
    			if(!in_array($ext,$allowed) ) {
    				echo '<div style="margin: -15px 5px;" class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Extension file invalid</div>';
    				return;
    			}
    			
    			if ( empty( $fileName ) == false ){
    				if (move_uploaded_file($_FILES[$nameInput]['tmp_name'], $uploadfile) ) {
    					$product = new Product();
    					$data['name'] = $fileName;
    					$product->insertProduct( $data );
    					$count++;
    				}
    			}
    		}
    		if ( count($_FILES) == $count ){
    			echo "File is valid, and was successfully uploaded.\n";
    			echo $this->_redirect('/'.$this->controller);
    		}
    	}
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
            $product = new Product();
            $product->deleteProduct( $id );
            $this->ajaxResponse( CODE_SUCCESS );
        } else {
            $this->ajaxResponse( CODE_HAS_ERROR );
        }
    }
    
}
