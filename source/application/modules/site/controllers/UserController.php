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
    
}
