<?php
class Site_HintController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-hint' ) );
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
    	$mdlHint = new Hint();
    	//define columns
    	$columns = 
    		array( // danh sach cac cot de ordering
//     			0 => "user_id",
//     			1 => "user_name",
//     			2 => "email",
//     			3 => "status",
//     			4 => "full_name",
    	);
    	//order function
//     	if ( empty( $this->post_data["order"] ) == false ) {
//     		$this->post_data["order"]["column"] = $columns[$this->post_data["order"][0]["column"]];
//     		$this->post_data["order"]["dir"] = $this->post_data["order"][0]["dir"];
//     	} else {
//     		$this->post_data["order"]["column"] = "id";
//     		$this->post_data["order"]["dir"] = "asc";
//     	}
    	//search function
//     	if ( empty( $this->post_data["columns"] ) == false && is_array( $this->post_data["columns"] ) ) {
//     		foreach ( $this->post_data["columns"] as $column ) {
//     			if ( $column["searchable"] == true && empty( $column["search"] ) == false && $column["search"]["value"] != "" ) {
//     				$this->post_data[$column["data"]] = $column["search"]["value"];
//     			}
//     		}
//     	}
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlHint->fetchAllHint( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$list = $mdlHint->fetchAllHint( $this->post_data );
    	if ( empty( $list ) == false ){
    		foreach ( $list as $value ){
    			$value['content'] = strip_tags( $value['content'] );
    		}
    	} 
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $list;
    	$this->_helper->json( $return );
    	exit;
    }
    /**
     * Create/ Update
     */
    public function detailAction(){
    	$mdlHint = new Hint();
    	$info = array();
    	$error = array();
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $mdlHint->fetchHintById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
//     		if ( empty( $this->post_data['content'] ) == false ){
//     			$this->post_data['content'] = htmlentities( $this->post_data['content'] );
//     		}
    		$xml = APPLICATION_PATH.'/xml/hint.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		if( empty( $error ) == true ) {
    			$result = $mdlHint->saveHint( $this->post_data );
    			if ( $result > 0 ){
	    			$this->_redirect( '/'.$this->controller );
    			}
    		}else {
    			$info = $this->post_data;
    			$error = $error;
    		}
    	}
    	$mdlQuestion = new Question();
    	$this->view->questionList = $mdlQuestion->fetchAllQuestion();
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
    		$mdl = new Hint();
    		$mdl->deleteHint( $id );
    		$this->ajaxResponse( CODE_SUCCESS );
    	} else {
    		$this->ajaxResponse( CODE_HAS_ERROR );
    	}
    }
}
