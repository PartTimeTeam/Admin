<?php
class Site_GroupUserController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-group-user' ) );
    }

    public function indexAction() {
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlGroupUser = new GroupUser();
    	//define columns
    	$columns = array(
    			0=>"group_id",
				1=>"group_name",
    			3=>"user_name_leader",
				5=>"email_leader",
				7=>"phone",
				8=>"created_at"
				
    	);
    	//order function
    	if ( empty( $this->post_data["order"] ) == false ) {
    		$this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
    		$this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
    	} else {
    		$this->post_data["order"]["column"] = "id";
    		$this->post_data["order"]["dir"] = "asc";
    	}
    	//get total data
    	if ( empty( $this->post_data["columns"] ) == false && is_array( $this->post_data["columns"] ) ) {
    		foreach ( $this->post_data["columns"] as $column ) {
    			if ( $column["searchable"] == true && empty( $column["search"] ) == false && $column["search"]["value"] != "" ) {
    				$this->post_data[$column["data"]] = $column["search"]["value"];
    			}
    		}
    	}
    	$this->post_data['count_only'] = 1;
    	$count = $mdlGroupUser->fetchAllGroupUser( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$groupUserList = $mdlGroupUser->fetchAllGroupUser( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $groupUserList;
    	$this->_helper->json( $return );
    	exit;
    }
    public function detailAction(){
		$id = "";
		$groupUserInfo = array();
		$groupUser = new GroupUser();
		if( empty( $this->post_data['id'] ) == false ) {
			$id = $this->post_data['id'];
			// get order info
			if(  intval( $id ) > 0  ) {
				$groupUserInfo = $groupUser->getInfo( $id );
			}
			if( empty( $groupUserInfo ) == true ) {
				// if order info empty
				$this->_redirect( '/'.$this->controller );
			}
		}
		else{
			$this->_redirect( '/'.$this->controller );
		}
		
		$this->view->info = $groupUserInfo;
    }
    
    
}
