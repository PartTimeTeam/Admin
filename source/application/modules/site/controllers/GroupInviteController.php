<?php
class Site_GroupInviteController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-group-invite' ) );
    }

    public function indexAction() {
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlGroupInvite = new GroupInvite();
    	//define columns
    	$columns = array(
    			0=>"id",
				1=>"group_name",
    			2=>"name",
				3=>"status",
				4=>"create_date"
				
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

    	$count = $mdlGroupInvite->fetchAllGroupInvite( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$groupInviteList = $mdlGroupInvite->fetchAllGroupInvite( $this->post_data );
        // print_r($groupInviteList);exit;
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $groupInviteList;
    	$this->_helper->json( $return );
    	exit;
    }
    public function detailAction(){
		$id = "";
		$groupInviteInfo = array();
		$groupInvite = new GroupInvite();
		if( empty( $this->post_data['id'] ) == false ) {
			$id = $this->post_data['id'];
			// get order info
			if(  intval( $id ) > 0  ) {
				$groupInviteInfo = $groupInvite->getInfo( $id );
			}
			if( empty( $groupInviteInfo ) == true ) {
				// if order info empty
				$this->_redirect( '/'.$this->controller );
			}
		}
		else{
			$this->_redirect( '/'.$this->controller );
		}
		$this->view->info = $groupInviteInfo;
    }
    
    
}
