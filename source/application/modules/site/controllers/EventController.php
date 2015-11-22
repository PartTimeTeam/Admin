<?php
class Site_EventController extends FrontBaseAction {
    public function init() {
        parent::init();
        $this->loadJs( array( 'pages-event' ) );
    }

    public function indexAction() {
    	//TODO
    }
    public function listAction() {
    	$this->isAjax();// controller nhan ajax request tu client
    	//get parameter
    	$draw = $this->post_data['draw']; // bien draw de tinh tong so trang
    	$mdlEvent = new Event();
    	//get total data
    	$this->post_data['count_only'] = 1;
    	$count = $mdlEvent->fetchAllEvent( $this->post_data );
    	//get filtered data
    	unset( $this->post_data['count_only'] );
    
    	$eventList = $mdlEvent->fetchAllEvent( $this->post_data );
    	//return data
    	$return = array();
    	$return['draw'] = $draw;
    	$return['recordsTotal'] = $count;
    	$return['recordsFiltered'] = $count;
    	$return['data']= $eventList;
    	$this->_helper->json( $return );
    	exit;
    }
    /**
     * detail
     */
    public function detailAction(){
    	$event = new Event();
    	$info = array();
    	$error = array();
    	$id = 0;
		
    	// get post card information if there is postcard'id available
    	if( empty( $this->post_data ['id'] ) == false ) {
    		$id = $this->post_data ['id'];
    		$info = $event->fetchEventById( $id );
    		if( empty( $info ) == true ) {
    			$this->_redirect( '/'.$this->controller );
    		}
    	}
    	// check request is POST or GET
    	if( $this->request->isPost() ) {
		print_r($this->post_data);exit;
    		//$xml = APPLICATION_PATH.'/xml/product_group.xml';
    		//$error = BaseService::checkInputData( $xml, $this->post_data);
			
    		//=============upload================== 	
			if($_FILES["logo_url"]["error"] == 0) {
				$target_dir = PUBLIC_PATH.'/upload/';
				$date = getdate();
				$date = $date['mday'].$date['mon'].$date['year'].$date['hours'].$date['minutes'].$date['seconds'];
				$file_name = $date.$_FILES["logo_url"]["name"];
				$target_file = $target_dir . basename($file_name);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
					$error['extension-image'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk != 0) {
					//$error[]= "Sorry, your file was not uploaded.";
					// if everything is ok, try to upload file
				//} else {
					if ( $id > 0 && empty( $info['logo_url'] ) == false ){
						if ( file_exists(PUBLIC_PATH.'/upload/'.$info['logo_url']) ) {
							unlink(PUBLIC_PATH.'/upload/'.$info['logo_url']);
						}
					}
					$this->post_data['logo_url'] = $file_name;
					if (move_uploaded_file($_FILES["logo_url"]["tmp_name"], $target_file)) {
						echo "The file ". basename( $_FILES["logo_url"]["name"]). " has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}
    		}		
			// check image if edit
			if ( empty( $this->post_data['file_name'] ) == false ){
				if ( empty ( $this->post_data['logo_url'] ) == true ){
					$this->post_data['logo_url'] = 	$this->post_data['file_name'];			
				}
				
			}
    		//==============================				
    		if( empty( $error ) == true ) {
    			$result = $event->saveEvent( $this->post_data );
    			if ( empty( $result ) == false ){
    				$this->_redirect( "/".$this->controller );
    			}
    		}else {
    			$info = $this->post_data;
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
    		$event = new Event();
    		$event->deleteEvent( $id );
    		$this->ajaxResponse( CODE_SUCCESS );
    	} else {
    		$this->ajaxResponse( CODE_HAS_ERROR );
    	}
    }
    /**
    * get logo
    */
    public function viewLogoAction(){
        //check ajax request
        $this->isAjax();
        if( empty( $this->post_data['id'] ) == false ){
            $mdlEvent = new Event();
            $infoEvent = $mdlEvent->fetchEventById( $this->post_data['id']);
            if ( empty( $infoEvent['logo_url'] ) == false ){
                $this->view->logo = $infoEvent['logo_url'];
               $this->loadTemplate( "/event/_dialog-event.phtml");
                //$this->ajaxResponse( CODE_SUCCESS );
            }
        }
       // $this->ajaxResponse( CODE_HAS_ERROR );
    }
}
