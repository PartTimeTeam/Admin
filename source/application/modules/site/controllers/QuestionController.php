<?php
class Site_QuestionController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-question' ) );
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
    	$mdlQuestion = new Question();
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
    	$count = $mdlQuestion->fetchAllQuestion( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$list = $mdlQuestion->fetchAllQuestion( $this->post_data );
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
    	$mdlQuestion = new Question();
    	$info = array();
    	$error = array();
    	$id = 0;
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $mdlQuestion->fetchQuestionById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
//     		if ( empty( $this->post_data['content'] ) == false ){
//     			$this->post_data['content'] = htmlentities( $this->post_data['content'] );
//     		}
    		$xml = APPLICATION_PATH.'/xml/question.xml';
    		$error = BaseService::checkInputData( $xml, $this->post_data);
    		if( empty( $error ) == true ) {
    			$result = $mdlQuestion->saveQuestion( $this->post_data );
    			if ( empty( $result ) == true ){
    				$info = $this->post_data;
    				$error = $error;
    			} else{
    				$this->_redirect( '/'.$this->controller );
    			}
    		}
    	}
    	$mdlStage = new Stage();
    	$this->view->stageList = $mdlStage->fetchAllStage();
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
    		$mdl = new Question();
    		$mdl->deleteQuestion( $id );
    		$this->ajaxResponse( CODE_SUCCESS );
    	} else {
    		$this->ajaxResponse( CODE_HAS_ERROR );
    	}
    }
}
