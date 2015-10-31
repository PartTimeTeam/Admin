<?php
class Site_OrderDetailController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-order' ) );
    }

    public function indexAction() {
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlOrder = new Order();
    	//define columns
    	$columns = array( // danh sach cac cot de ordering
    	);
    	//get total data
    	if ( empty( $this->post_data["columns"] ) == false && is_array( $this->post_data["columns"] ) ) {
    		foreach ( $this->post_data["columns"] as $column ) {
    			if ( $column["searchable"] == true && empty( $column["search"] ) == false && $column["search"]["value"] != "" ) {
    				$this->post_data[$column["data"]] = $column["search"]["value"];
    			}
    		}
    	}
    	$this->post_data['count_only'] = 1;
    	$count = $mdlOrder->fetchAllOrderGroup( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$orderList = $mdlOrder->fetchAllOrderGroup( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $orderList;
    	$this->_helper->json( $return );
    	exit;
    }
    
    
}
