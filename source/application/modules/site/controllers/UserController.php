<?php
class Site_UserController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-user' ) );
    }

    public function indexAction() {
    }

    /**
     * List by ajax
     */
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlUser = new Users();
    	//define columns
    	$columns = array( // danh sach cac cot de ordering
    			0 => "user_id",
    			1 => "user_name",
    			2 => "email",
    			3 => "status",
    			4 => "full_name",
    	);
    	//order function
    	if ( empty( $this->post_data["order"] ) == false ) {
    		$this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
    		$this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
    	} else {
    		$this->post_data["order"]["column"] = "user_id";
    		$this->post_data["order"]["dir"] = "asc";
    	}
    	//search function
    	if ( empty( $this->post_data["columns"] ) == false && is_array( $this->post_data["columns"] ) ) {
    		foreach ( $this->post_data["columns"] as $column ) {
    			if ( $column["searchable"] == true && empty( $column["search"] ) == false && $column["search"]["value"] != "" ) {
    				$this->post_data[$column["data"]] = $column["search"]["value"];
    			}
    		}
    	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlUser->fetchAllUsers( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$userList = $mdlUser->fetchAllUsers( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $userList;
    	$this->_helper->json( $return );
    	exit;
    }
    /**
     * Create/ Update User
     */
    public function detailAction(){
    	$mdlUser = new Users();
    	$userInfo = array();
    	$error = array();
    	$userId = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$userId = $this->post_data ['id'];
    		$userInfo = $mdlUser->fetchUserById( $userId );
    		if( empty( $userInfo ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
    		$xml = APPLICATION_PATH.'/xml/user.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		if ( $userId == 0 ){
	    		$email = $mdlUser->fetchUserByEmail( $this->post_data['email']);
	    		$username = $mdlUser->fetchUserByUserName( $this->post_data['user_name']);
	    		if ( empty( $username ) == false ){
	    			$error['user_name'] = 'User name already exist.'; 
	    		} else if( empty( $email ) == false ) {
	    			$error['email'] = 'Email already exist.';
	    		}
    		} else {
    			unset( $error['email']);
    			unset( $error['user_name']);
    		}
    		if( empty( $error ) == true ) {
    				$result = $mdlUser->saveUser( $this->post_data );
    				$this->_redirect( '/'.$this->controller );
    		}else {
    			$userInfo = $this->post_data;
    			$error = $error;
    		}
    	}
    	$this->view->info = $userInfo;
    	$this->view->id = $userId;
    	$this->view->error = $error;
    }
    /**
     * Change password
     */
    public function changePasswordAction(){
    	$error = array();
    	$postData = array();
    	if ( $this->request->isPost() ) {
	    	//get data
	    	$data = $this->post_data;
	    	$xml = APPLICATION_PATH.'/xml/password.xml';
	    	$error = BaseService::checkInputData( $xml, $data);
	    	if ( empty( $error ) ) {
	    		$mdlUser = new Users();
	    		$user = $mdlUser->fetchUserByPass( UtilEncryption::encryptPassword( $data['OldPassword'] ,'md5'));
	    		if ( empty( $user ) == true ){
	    			$error[] = "Current password don't match";
	    			$postData = $data;
	    		} else {
	    			$datain = array();
		    		$datain['password'] = UtilEncryption::encryptPassword( $data['Password'] ,'md5');
		    		$rs = $mdlUser->updateUserById( $this->login_info->user_id, $datain );
		    		if ( $rs ){
		    			Zend_Session::destroy();
				    	// Clear login info
				    	$this->login_info = null;
				    	//go to login page
				    	$this->_redirect( "/" . $this->controller . "/login" );
		    		}
	    		}
	    	} else {
	    		$postData = $data;
	    	}
    	}
    	$this->view->postData = $postData;
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
    		$product = new Users();
    		$product->deleteUser( $id );
    		$this->ajaxResponse( CODE_SUCCESS );
    	} else {
    		$this->ajaxResponse( CODE_HAS_ERROR );
    	}
    }
}
